<?php
require_once __DIR__ . '/../config/database.php';

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'),
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

echo "<pre>";
echo "URL Requested: " . $_SERVER['REQUEST_URI'] . "\n";
echo "HTTPS: " . ($_SERVER['HTTPS'] ?? 'off') . "\n";
echo "HTTP_X_FORWARDED_PROTO: " . ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'N/A') . "\n";
echo "APP_URL: " . APP_URL . "\n";
echo "Session ID: " . session_id() . "\n";
echo "Cookies received: \n";
print_r($_COOKIE);
echo "Session data: \n";
print_r($_SESSION);
echo "</pre>";
