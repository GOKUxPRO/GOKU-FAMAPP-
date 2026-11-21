<?php
// Composer autoload load karo
require __DIR__ . '/vendor/autoload.php';

// Jo tamari pase config.php hoy to e load thashe
if (file_exists(__DIR__ . '/config.php')) {
    require __DIR__ . '/config.php';
}

use Kreait\Firebase\Factory;

// 1. Environment Variable ma thi Firebase JSON kadhvo
$serviceAccountJson = getenv('FIREBASE_CREDENTIALS');

if (!$serviceAccountJson) {
    // Jo env variable na hoy to local file check karo (Local testing mate)
    $localFile = __DIR__ . '/firebase-credentials.json';
    if (file_exists($localFile)) {
        $serviceAccountJson = file_get_contents($localFile);
    } else {
        die(json_encode(["error" => "Firebase credentials missing. Please set FIREBASE_CREDENTIALS in Railway."]));
    }
}

// 2. JSON ne Decode karo
$serviceAccount = json_decode($serviceAccountJson, true);

if (!$serviceAccount) {
    // Jo JSON ma newlines na karan error aave to tene fix karo
    $fixedJson = str_replace(["\r", "\n"], '', $serviceAccountJson);
    $serviceAccount = json_decode($fixedJson, true);
}

// Jo fari pan JSON invalid hoy to error aapo
if (!$serviceAccount) {
    die(json_encode(["error" => "Invalid Firebase JSON format. Check Environment Variables."]));
}

// 3. Firebase Connect karo
try {
    // Jo FIREBASE_DB_URL config.php ma define na hoy, to ahiya direct URL nakhi do
    $dbUrl = defined('FIREBASE_DB_URL') ? FIREBASE_DB_URL : 'https://tamari-project-id.firebaseio.com'; 
    
    $factory = (new Factory)
        ->withServiceAccount($serviceAccount)
        ->withDatabaseUri($dbUrl);
    $database = $factory->createDatabase();
} catch (\Throwable $e) {
    die(json_encode(["error" => "Firebase Connection Failed: " . $e->getMessage()]));
}

// 4. Password Encryption/Decryption functions
function encrypt_pass($data) {
    $cipher = "aes-256-cbc";
    // ENCRYPTION_KEY config.php ma hovu joiye, nahi to default use thashe
    $keyString = defined('ENCRYPTION_KEY') ? ENCRYPTION_KEY : 'gokuxfam_super_secret_key_1234567890';
    
    $key = hash('sha256', $keyString, true);
    $iv = random_bytes(openssl_cipher_iv_length($cipher));
    $enc = openssl_encrypt($data, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv) . ':' . base64_encode($enc);
}

function decrypt_pass($data) {
    $cipher = "aes-256-cbc";
    $keyString = defined('ENCRYPTION_KEY') ? ENCRYPTION_KEY : 'gokuxfam_super_secret_key_1234567890';
    
    $key = hash('sha256', $keyString, true);
    $parts = explode(':', $data, 2);
    if (count($parts) !== 2) return false;
    return openssl_decrypt(base64_decode($parts[1]), $cipher, $key, OPENSSL_RAW_DATA, base64_decode($parts[0]));
}
?>
