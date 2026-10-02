<?php
chdir('c:/xampp/htdocs/CURTISS/FalconStationery/ERP/public');

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';
require_once '../core/Database.php';
require_once '../core/Controller.php';
require_once '../app/Models/RepTracking.php';
require_once '../app/Models/Vehicle.php';
require_once '../app/Models/Employee.php';
require_once '../app/Models/Delivery.php';
require_once '../app/Controllers/RepTrackingController.php';

try {
    session_start();
    $_SESSION['user_id'] = 1;
    $_SESSION['role'] = 'admin';
    
    $controller = new RepTrackingController();
    echo "Controller instantiated.\n";
    $_GET['page'] = 1;
    
    ob_start();
    $controller->index();
    $html = ob_get_clean();
    echo "SUCCESS! Rendered HTML length: " . strlen($html) . "\n";
    echo "HTML snippet:\n" . substr($html, 0, 500) . "\n";
} catch (Throwable $e) {
    echo "FATAL EXCEPTION: " . $e->getMessage() . "\n";
    echo $e->getFile() . ":" . $e->getLine() . "\n";
    echo $e->getTraceAsString() . "\n";
}
