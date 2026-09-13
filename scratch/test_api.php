<?php
require 'c:/xampp/htdocs/CURTISS/FalconStationery/ERP/config/config.php';
require 'c:/xampp/htdocs/CURTISS/FalconStationery/ERP/core/Database.php';
$db = new \Core\Database();
$db->query('SELECT * FROM app_releases WHERE is_latest = 1 ORDER BY id DESC LIMIT 1');
$latest = $db->single();
print_r($latest);
