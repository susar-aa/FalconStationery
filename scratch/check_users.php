<?php
require __DIR__ . '/../core/Database.php';
require __DIR__ . '/../config/config.php';
$db = new \Core\Database();
$db->query("SELECT id, username, role FROM users WHERE role = 'rep'");
$users = $db->resultSet();
foreach ($users as $u) {
    echo "ID: {$u->id}, Username: {$u->username}\n";
}
