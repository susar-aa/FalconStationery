<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('APPROOT', 'c:/xampp/htdocs/CURTISS/FalconStationery/ERP/app');
define('APP_URL', 'https://falcon.trycurtiss.com');

require_once 'c:/xampp/htdocs/CURTISS/FalconStationery/ERP/config/database.php';
require_once 'c:/xampp/htdocs/CURTISS/FalconStationery/ERP/core/Database.php';
require_once 'c:/xampp/htdocs/CURTISS/FalconStationery/ERP/core/Controller.php';
require_once 'c:/xampp/htdocs/CURTISS/FalconStationery/ERP/app/Models/RepTracking.php';
require_once 'c:/xampp/htdocs/CURTISS/FalconStationery/ERP/app/Models/Vehicle.php';
require_once 'c:/xampp/htdocs/CURTISS/FalconStationery/ERP/app/Models/Employee.php';
require_once 'c:/xampp/htdocs/CURTISS/FalconStationery/ERP/app/Models/Delivery.php';
require_once 'c:/xampp/htdocs/CURTISS/FalconStationery/ERP/app/Controllers/RepTrackingController.php';

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
    echo "Rendered HTML length: " . strlen($html) . "\n";
    if (strlen($html) < 1000) {
        echo "HTML OUTPUT:\n" . $html . "\n";
    } else {
        echo "HTML starts with: " . substr($html, 0, 300) . "\n";
        echo "HTML ends with: " . substr($html, -300) . "\n";
    }
} catch (Throwable $e) {
    echo "FATAL EXCEPTION: " . $e->getMessage() . "\n";
    echo $e->getFile() . ":" . $e->getLine() . "\n";
    echo $e->getTraceAsString() . "\n";
}
