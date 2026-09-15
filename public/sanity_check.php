<?php
chdir('c:\xampp\htdocs\CURTISS\FalconStationery\ERP\public');
ob_start();
require_once '../core/Database.php';
require_once '../app/Controllers/PettyCashController.php';
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['role_id'] = 1;
$_SESSION['username'] = 'Admin';
$_SESSION['csrf_token'] = 'fake_token';
define('APP_URL', 'http://localhost');
function hasPermission($module, $action = 'view') { return true; }
$controller = new PettyCashController();
$controller->index();
$html = ob_get_clean();
echo strpos($html, 'id="allocateModal"') !== false ? "SUCCESS\n" : "FAIL\n";
file_put_contents('test_out.html', $html);
echo "Length: " . strlen($html) . "\n";
