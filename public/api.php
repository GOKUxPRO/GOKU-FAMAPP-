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
if ($requestCount >= (defined('API_RATE_LIMIT_PER_MINUTE') ? API_RATE_LIMIT_PER_MINUTE : 60)) {
    http_response_code(429);
    echo json_encode(["error" => "Too many requests."]); exit;
}
$database->getReference($rateLimitRef)->set($requestCount + 1);

// 1. REGISTER (Direct Gmail + App Password)
if ($action === 'register') {
    $email = strtolower(trim($input['email'] ?? ''));
    $appPassword = str_replace(' ', '', $input['app_password'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($appPassword) !== 16) {
        echo json_encode(["error" => "Valid email and 16-char App Password required."]); exit;
    }

    // Gmail IMAP Verify
    $inbox = @imap_open("{imap.gmail.com:993/imap/ssl}INBOX", $email, $appPassword);
    if (!$inbox) {
        echo json_encode(["error" => "Gmail connection failed. Please check your App Password."]); exit;
    }
    imap_close($inbox);

    // Check if user already exists
    $users = $database->getReference('users')->orderByChild('email')->equalTo($email)->getValue();
    
    if ($users) {
        // User exists, update password and return existing key
        $userId = array_key_first($users);
        $user = $users[$userId];
        $rawApiKey = $user['api_key'] ?? ('pay_' . bin2hex(random_bytes(24)));
        
        $database->getReference('users/' . $userId)->update([
            'app_password' => encrypt_pass($appPassword),
            'api_key' => $rawApiKey,
            'status' => 'active'
        ]);
        $database->getReference('api_keys/' . hash('sha256', $rawApiKey))->set($userId);
        
        echo json_encode(["success" => true, "api_key" => $rawApiKey, "message" => "Already registered. Key restored."]);
        exit;
    }

    // Create new user
    $rawApiKey = 'pay_' . bin2hex(random_bytes(24));
    $hashedApiKey = hash('sha256', $rawApiKey);

    $newUserRef = $database->getReference('users')->push([
        'email' => $email,
        'app_password' => encrypt_pass($appPassword),
        'api_key' => $rawApiKey,
        'status' => 'active',
        'created_at' => time()
    ]);
    $database->getReference('api_keys/' . $hashedApiKey)->set($newUserRef->getKey());

    echo json_encode(["success" => true, "api_key" => $rawApiKey]);
    exit;
}

// 2. GET PAYMENTS
if ($action === 'get_payments') {
    $headers = array_change_key_case(getallheaders(), CASE_LOWER);
    $apiKey = $headers['x-api-key'] ?? '';
    if (!$apiKey) { echo json_encode(["error" => "API Key missing."]); exit; }

    $userId = $database->getReference('api_keys/' . hash('sha256', $apiKey))->getValue();
    if (!$userId) { echo json_encode(["error" => "Invalid API Key."]); exit; }

    $payments = $database->getReference('payments/' . $userId)->getValue() ?: [];
    $payments = array_values($payments);
    usort($payments, fn($a, $b) => ($b['created_at'] ?? 0) <=> ($a['created_at'] ?? 0));

    $result = [];
    foreach (array_slice($payments, 0, 50) as $p) {
        $result[] = [
            "amount" => $p['amount'] ?? 0,
            "utr" => $p['utr'] ?? '-',
            "transaction_id" => $p['transaction_id'] ?? '-',
            "sender_name" => $p['sender_name'] ?? '-',
            "received_at" => date('d M, H:i', $p['created_at'] ?? time())
        ];
    }
    echo json_encode(["success" => true, "data" => $result]);
    exit;
}

// 3. DELETE ACCOUNT
if ($action === 'delete_account') {
    $headers = array_change_key_case(getallheaders(), CASE_LOWER);
    $apiKey = $headers['x-api-key'] ?? '';
    if (!$apiKey) { echo json_encode(["error" => "API Key missing."]); exit; }

    $hashedApiKey = hash('sha256', $apiKey);
    $userId = $database->getReference('api_keys/' . $hashedApiKey)->getValue();
    if (!$userId) { echo json_encode(["error" => "Invalid API Key."]); exit; }

    $database->getReference('api_keys/' . $hashedApiKey)->remove();
    $database->getReference('users/' . $userId)->remove();
    $database->getReference('payments/' . $userId)->remove();

    echo json_encode(["success" => true, "message" => "Account deleted successfully."]);
    exit;
}

echo json_encode(["error" => "Unknown action."]);
