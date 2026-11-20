<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, X-API-Key, X-Session-Token");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }
require __DIR__ . '/../db.php';

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true) ?: [];

// Rate Limiting
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rateLimitRef = 'rate_limits/' . md5($ip) . '/' . floor(time() / 60);
$requestCount = (int)($database->getReference($rateLimitRef)->getValue() ?: 0);
if ($requestCount >= (defined('API_RATE_LIMIT_PER_MINUTE') ? API_RATE_LIMIT_PER_MINUTE : 100)) {
    http_response_code(429);
    echo json_encode(["error" => "Too many requests."]); exit;
}
$database->getReference($rateLimitRef)->set($requestCount + 1);

// Helper: Get User by Session Token
function getUserByToken($database, $token) {
    if (!$token) return null;
    $users = $database->getReference('users')->orderByChild('session_token')->equalTo($token)->getValue();
    if (!$users) return null;
    return ["id" => array_key_first($users), "data" => reset($users)];
}

// 1. REGISTER (Normal Account)
if ($action === 'register') {
    $email = strtolower(trim($input['email'] ?? ''));
    $password = $input['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
        echo json_encode(["error" => "Valid email and password (min 6 chars) required."]); exit;
    }

    $users = $database->getReference('users')->orderByChild('email')->equalTo($email)->getValue();
    if ($users) { echo json_encode(["error" => "Email already registered. Please login."]); exit; }

    $sessionToken = bin2hex(random_bytes(32));
    
    $newUserRef = $database->getReference('users')->push([
        'email' => $email, // Default login email
        'login_password' => password_hash($password, PASSWORD_DEFAULT),
        'session_token' => $sessionToken,
        'status' => 'inactive', // Inactive until Gmail is linked
        'created_at' => time()
    ]);

    echo json_encode(["success" => true, "session_token" => $sessionToken, "api_key" => null]);
    exit;
}

// 2. LOGIN
if ($action === 'login') {
    $email = strtolower(trim($input['email'] ?? ''));
    $password = $input['password'] ?? '';

    $users = $database->getReference('users')->orderByChild('email')->equalTo($email)->getValue();
    if (!$users) { echo json_encode(["error" => "Account not found."]); exit; }

    $userId = array_key_first($users);
    $user = $users[$userId];

    if (!password_verify($password, $user['login_password'] ?? '')) {
        echo json_encode(["error" => "Invalid Password."]); exit;
    }

    $sessionToken = bin2hex(random_bytes(32));
    $database->getReference('users/' . $userId . '/session_token')->set($sessionToken);

    echo json_encode([
        "success" => true, 
        "session_token" => $sessionToken,
        "api_key" => $user['api_key'] ?? null
    ]);
    exit;
}

// 3. LINK GMAIL (FamApp wali ID ane 16-char Password)
if ($action === 'link_gmail') {
    $headers = array_change_key_case(getallheaders(), CASE_LOWER);
    $token = $headers['x-session-token'] ?? '';
    $user = getUserByToken($database, $token);

    if (!$user) { echo json_encode(["error" => "Unauthorized. Please login again."]); exit; }

    // Have ahiya User potanu FamApp walu email nakhe che
    $paymentEmail = strtolower(trim($input['gmail_address'] ?? ''));
    $appPassword = str_replace(' ', '', $input['app_password'] ?? '');

    if (!filter_var($paymentEmail, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["error" => "Please enter a valid Gmail address."]); exit;
    }

    if (strlen($appPassword) !== 16) {
        echo json_encode(["error" => "App Password must be exactly 16 characters."]); exit;
    }

    // Verify IMAP using the new email and app password
    $inbox = @imap_open("{imap.gmail.com:993/imap/ssl}INBOX", $paymentEmail, $appPassword);
    if (!$inbox) {
        echo json_encode(["error" => "Gmail connection failed. Check your App Password."]); exit;
    }
    imap_close($inbox);

    // API Key Generation
    $rawApiKey = 'pay_' . bin2hex(random_bytes(24));
    $hashedApiKey = hash('sha256', $rawApiKey);

    // Update Database: Ahiya aapde 'email' field ne paymentEmail thi update kari didho che jethi worker e j check kare
    $database->getReference('users/' . $user['id'])->update([
        'email' => $paymentEmail, // FamApp wali ID save thai gai
        'app_password' => encrypt_pass($appPassword),
        'api_key' => $rawApiKey,
        'status' => 'active'
    ]);
    $database->getReference('api_keys/' . $hashedApiKey)->set($user['id']);

    echo json_encode(["success" => true, "api_key" => $rawApiKey]);
    exit;
}

// 4. GET PAYMENTS
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

// 5. DELETE ACCOUNT
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
