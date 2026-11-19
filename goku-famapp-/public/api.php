<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, X-API-Key");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }
require __DIR__ . '/../db.php';

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true) ?: [];

// Rate limiting
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rateLimitRef = 'rate_limits/' . md5($ip) . '/' . floor(time() / 60);
$requestCount = (int)($database->getReference($rateLimitRef)->getValue() ?: 0);
if ($requestCount >= API_RATE_LIMIT_PER_MINUTE) {
    http_response_code(429);
    echo json_encode(["error" => "Too many requests."]); exit;
}
$database->getReference($rateLimitRef)->set($requestCount + 1);

if ($action === 'register') {
    $email = strtolower(trim($input['email'] ?? ''));
    $appPassword = str_replace(' ', '', $input['app_password'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($appPassword) !== 16) {
        echo json_encode(["error" => "Valid email and 16-char App Password required."]); exit;
    }

    $users = $database->getReference('users')->orderByChild('email')->equalTo($email)->getValue();
    if ($users) { echo json_encode(["error" => "Email already registered."]); exit; }

    $inbox = @imap_open("{imap.gmail.com:993/imap/ssl}INBOX", $email, $appPassword);
    if (!$inbox) {
        echo json_encode(["error" => "Gmail connection failed: " . imap_last_error()]); exit;
    }
    imap_close($inbox);

    $rawApiKey = 'pay_' . bin2hex(random_bytes(24));
    $hashedApiKey = hash('sha256', $rawApiKey);

    $newUserRef = $database->getReference('users')->push([
        'email' => $email,
        'app_password' => encrypt_pass($appPassword),
        'status' => 'active',
        'created_at' => time()
    ]);
    $database->getReference('api_keys/' . $hashedApiKey)->set($newUserRef->getKey());

    echo json_encode(["success" => true, "api_key" => $rawApiKey]);
    exit;
}

if ($action === 'get_payments') {
    $headers = array_change_key_case(getallheaders(), CASE_LOWER);
    $apiKey = $headers['x-api-key'] ?? $_GET['api_key'] ?? '';
    if (!$apiKey) { echo json_encode(["error" => "API Key missing."]); exit; }

    $userId = $database->getReference('api_keys/' . hash('sha256', $apiKey))->getValue();
    if (!$userId) { echo json_encode(["error" => "Invalid API Key."]); exit; }

    $payments = $database->getReference('payments/' . $userId)->getValue() ?: [];
    usort($payments, fn($a, $b) => ($b['created_at'] ?? 0) <=> ($a['created_at'] ?? 0));

    $result = [];
    foreach (array_slice($payments, 0, 50) as $p) {
        $result[] = [
            "amount" => $p['amount'],
            "utr" => $p['utr'] ?? '-',
            "transaction_id" => $p['transaction_id'] ?? '-',
            "sender_name" => $p['sender_name'] ?? '-',
            "received_at" => date('Y-m-d H:i:s', $p['created_at'])
        ];
    }
    echo json_encode(["success" => true, "data" => $result]);
    exit;
}

echo json_encode(["error" => "Unknown action."]);
