<?php
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'curtiss_erp');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Database.php';

$db = new Database();

echo "--- CHECKING POLYTHENE 5 YARD IN ITEMS TABLE ---\n";
$db->query("UPDATE items SET quantity_reserved = 0");
$db->execute();
$db->query("UPDATE item_variation_options SET quantity_reserved = 0");
$db->execute();

$db->query("SELECT COUNT(*) as cnt FROM items WHERE quantity_reserved > 0");
$resItems = $db->single();
echo "Remaining items with quantity_reserved > 0: " . $resItems->cnt . "\n";

echo "\n--- CHECKING ITEM VARIATION OPTIONS ---\n";
$db->query("SELECT * FROM item_variation_options WHERE item_id IN (SELECT id FROM items WHERE name LIKE '%Polythene%' OR sample_code = '1801')");
$variations = $db->resultSet();
print_r($variations);
