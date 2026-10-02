<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$rootDir = dirname(__DIR__);
define('APPROOT', $rootDir . '/app');
require_once $rootDir . '/app/config/config.php';
require_once $rootDir . '/core/Database.php';
require_once $rootDir . '/core/Controller.php';
require_once $rootDir . '/app/Models/RepTracking.php';
require_once $rootDir . '/app/Controllers/RepTracking.php';

try {
    session_start();
    $_SESSION['user_id'] = 1;
    $_SESSION['role'] = 'admin';
    $controller = new RepTracking();
    echo "Controller instantiated successfully.\n";
    $controller->index();
    echo "\nRender completed successfully!\n";
} catch (Throwable $e) {
    echo "FATAL EXCEPTION: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
