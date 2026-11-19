<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config.php';

use Kreait\Firebase\Factory;

$serviceAccountJson = getenv('FIREBASE_CREDENTIALS');
if (!$serviceAccountJson) {
    die(json_encode(["error" => "Firebase credentials missing."]));
}

$serviceAccount = json_decode($serviceAccountJson, true);
if (!$serviceAccount) {
    // Fallback: newlines stripped (private_key \n stay escaped inside JSON)
    $serviceAccount = json_decode(str_replace(["\r", "\n"], '', $serviceAccountJson), true);
}
if (!$serviceAccount) {
    die(json_encode(["error" => "Invalid Firebase JSON format."]));
}

try {
    $factory = (new Factory)
        ->withServiceAccount($serviceAccount)
        ->withDatabaseUri(FIREBASE_DB_URL);
    $database = $factory->createDatabase();
} catch (\Throwable $e) {
    die(json_encode(["error" => "Firebase Connection Failed: " . $e->getMessage()]));
}

function encrypt_pass($data) {
    $cipher = "aes-256-cbc";
    $key = hash('sha256', ENCRYPTION_KEY, true);
    $iv = random_bytes(openssl_cipher_iv_length($cipher));
    $enc = openssl_encrypt($data, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv) . ':' . base64_encode($enc);
}

function decrypt_pass($data) {
    $cipher = "aes-256-cbc";
    $key = hash('sha256', ENCRYPTION_KEY, true);
    $parts = explode(':', $data, 2);
    if (count($parts) !== 2) return false;
    return openssl_decrypt(base64_decode($parts[1]), $cipher, $key, OPENSSL_RAW_DATA, base64_decode($parts[0]));
}
