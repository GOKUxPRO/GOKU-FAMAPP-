<?php
require_once __DIR__ . '/config.php';

// Composer autoload (run: composer install)
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

use Kreait\Firebase\Factory;

// Firebase Realtime Database connection
$factory = (new Factory)->withDatabaseUri(FIREBASE_DB_URL);
$database = $factory->createDatabase();

/**
 * Simple AES-256-CBC encryption for App Password
 */
function encrypt_pass(string $plain): string {
    $key = hash('sha256', ENCRYPTION_KEY, true);
    $iv  = openssl_random_pseudo_bytes(16);
    $encrypted = openssl_encrypt($plain, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $encrypted);
}

/**
 * Decrypt App Password
 */
function decrypt_pass(string $encrypted): string {
    $data = base64_decode($encrypted);
    if ($data === false || strlen($data) < 17) {
        return '';
    }
    $key = hash('sha256', ENCRYPTION_KEY, true);
    $iv  = substr($data, 0, 16);
    $cipher = substr($data, 16);
    $plain = openssl_decrypt($cipher, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return $plain === false ? '' : $plain;
}
