<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, X-API-Key");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }
require __DIR__ . '/../db.php';

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true) ?: [];

// Rate Limiting
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rateLimitRef = 'rate_limits/' . md5($ip) . '/' . floor(time() / 60);
$requestCount = (int)($database->getReference($rateLimitRef)->getValue() ?: 0);
if ($requestCount >= API_RATE_LIMIT_PER_MINUTE) { http_response_code(429); echo json_encode(["error" => "Too many requests."]); exit; }
$database->getReference($rateLimitRef)->set($requestCount + 1);

// 1. REGISTER (Ahiya UPI ID add karyu)
if ($action === 'register') {
    $email = strtolower(trim($input['email'] ?? ''));
    $appPassword = str_replace(' ', '', $input['app_password'] ?? '');
    $upiId = trim($input['upi_id'] ?? ''); // Nava user ni UPI ID

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($appPassword) !== 16 || empty($upiId)) {
        echo json_encode(["error" => "Valid email, UPI ID, and 16-char App Password required."]); exit;
    }

    $inbox = @imap_open("{imap.gmail.com:993/imap/ssl}INBOX", $email, $appPassword);
    if (!$inbox) { echo json_encode(["error" => "Gmail connection failed."]); exit; }
    imap_close($inbox);

    $users = $database->getReference('users')->orderByChild('email')->equalTo($email)->getValue();
    if ($users) {
        $userId = array_key_first($users); $user = $users[$userId];
        $rawApiKey = $user['api_key'] ?? ('pay_' . bin2hex(random_bytes(24)));
        $database->getReference('users/' . $userId)->update([
            'app_password' => encrypt_pass($appPassword), 
            'api_key' => $rawApiKey, 
            'upi_id' => $upiId, // Update UPI ID
            'status' => 'active'
        ]);
        $database->getReference('api_keys/' . hash('sha256', $rawApiKey))->set($userId);
        echo json_encode(["success" => true, "api_key" => $rawApiKey, "message" => "Already registered."]); exit;
    }

    $rawApiKey = 'pay_' . bin2hex(random_bytes(24));
    $newUserRef = $database->getReference('users')->push([
        'email' => $email, 
        'app_password' => encrypt_pass($appPassword), 
        'upi_id' => $upiId, // Save UPI ID
        'api_key' => $rawApiKey, 
        'status' => 'active', 
        'created_at' => time()
    ]);
    $database->getReference('api_keys/' . hash('sha256', $rawApiKey))->set($newUserRef->getKey());
    echo json_encode(["success" => true, "api_key" => $rawApiKey]); exit;
}

// 2. GET PAYMENTS (Same as before)
if ($action === 'get_payments') {
    $headers = array_change_key_case(getallheaders(), CASE_LOWER);
    $apiKey = $headers['x-api-key'] ?? $_GET['api_key'] ?? ''; 
    if (!$apiKey) { echo json_encode(["error" => "API Key missing."]); exit; }
    $userId = $database->getReference('api_keys/' . hash('sha256', $apiKey))->getValue();
    if (!$userId) { echo json_encode(["error" => "Invalid API Key."]); exit; }

    $payments = $database->getReference('payments/' . $userId)->getValue() ?: [];
    $payments = array_values($payments);
    usort($payments, fn($a, $b) => ($b['created_at'] ?? 0) <=> ($a['created_at'] ?? 0));

    $result = [];
    foreach (array_slice($payments, 0, 50) as $p) {
        $result[] = ["amount" => $p['amount'] ?? 0, "utr" => $p['utr'] ?? '-', "transaction_id" => $p['transaction_id'] ?? '-', "sender_name" => $p['sender_name'] ?? '-', "received_at" => date('d M, H:i', $p['created_at'] ?? time())];
    }
    echo json_encode(["success" => true, "data" => $result]); exit;
}

// 3. CREATE ORDER (Ahiya User ni UPI ID fetch thashe)
if ($action === 'create_order') {
    $amount = floatval($input['amount'] ?? 0);
    $apiKey = $input['api_key'] ?? '';
    if ($amount <= 0 || !$apiKey) { echo json_encode(["error" => "Amount and API Key required."]); exit; }

    $userId = $database->getReference('api_keys/' . hash('sha256', $apiKey))->getValue();
    if (!$userId) { echo json_encode(["error" => "Invalid API Key."]); exit; }

    // User na data ma thi UPI ID kadhvi
    $userData = $database->getReference('users/' . $userId)->getValue();
    $userUpiId = $userData['upi_id'] ?? '';
    
    if (empty($userUpiId)) {
        echo json_encode(["error" => "User UPI ID not found. Please update in dashboard."]); exit;
    }

    $orderId = 'ORD' . strtoupper(substr(uniqid(), -6));
    $database->getReference('orders/' . $orderId)->set(['user_id' => $userId, 'amount' => $amount, 'status' => 'pending', 'created_at' => time()]);

    echo json_encode([
        "success" => true, 
        "order_id" => $orderId, 
        "upi_id" => $userUpiId, // Dynamic UPI ID
        "payee_name" => "GOKUxFAM"
    ]); 
    exit;
}

// 4. VERIFY ORDER (Same as before)
if ($action === 'verify_order') {
    $orderId = $input['order_id'] ?? '';
    $utr = trim($input['utr'] ?? '');
    if (!$orderId || !$utr) { echo json_encode(["error" => "Order ID and UTR required."]); exit; }

    $order = $database->getReference('orders/' . $orderId)->getValue();
    if (!$order) { echo json_encode(["error" => "Invalid Order ID."]); exit; }
    if ($order['status'] === 'completed') { echo json_encode(["success" => true, "message" => "Payment already verified."]); exit; }

    $userId = $order['user_id'];
    $payments = $database->getReference('payments/' . $userId)->getValue() ?: [];
    $found = false; $matchedAmount = 0;

    foreach ($payments as $p) {
        if (($p['utr'] ?? '') === $utr || ($p['transaction_id'] ?? '') === $utr) { $found = true; $matchedAmount = $p['amount']; break; }
    }

    if (!$found) { echo json_encode(["success" => false, "error" => "UTR not found. Please wait 1-2 minutes and try again."]); exit; }
    if (abs($matchedAmount - $order['amount']) > 0.1) { echo json_encode(["success" => false, "error" => "Amount mismatch."]); exit; }

    $database->getReference('orders/' . $orderId)->update(['status' => 'completed', 'utr' => $utr, 'completed_at' => time()]);
    echo json_encode(["success" => true, "message" => "Payment verified successfully!"]); exit;
}

// 5. DELETE ACCOUNT
if ($action === 'delete_account') {
    $headers = array_change_key_case(getallheaders(), CASE_LOWER);
    $apiKey = $headers['x-api-key'] ?? $_GET['api_key'] ?? ''; 
    if (!$apiKey) { echo json_encode(["error" => "API Key missing."]); exit; }

    $hashedApiKey = hash('sha256', $apiKey);
    $userId = $database->getReference('api_keys/' . $hashedApiKey)->getValue();
    if (!$userId) { echo json_encode(["error" => "Invalid API Key."]); exit; }

    $database->getReference('api_keys/' . $hashedApiKey)->remove();
    $database->getReference('users/' . $userId)->remove();
    $database->getReference('payments/' . $userId)->remove();
    echo json_encode(["success" => true, "message" => "Account deleted successfully."]); exit;
}
echo json_encode(["error" => "Unknown action."]);
