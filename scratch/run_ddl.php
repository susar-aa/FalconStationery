<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Database.php';

$db = new Database();
try {
    $db->query("SHOW COLUMNS FROM invoices LIKE 'original_grand_total'");
    if (!$db->single()) {
        echo "Adding column original_grand_total... ";
        $db->query("ALTER TABLE invoices ADD COLUMN original_grand_total DECIMAL(15,2) NULL AFTER grand_total");
        $db->execute();
        $db->query("UPDATE invoices SET original_grand_total = COALESCE(grand_total, ROUND(total_amount - COALESCE(CASE WHEN global_discount_type = '%' THEN (total_amount * global_discount_val / 100) ELSE global_discount_val END, 0) + COALESCE(tax_amount, 0), 2)) WHERE original_grand_total IS NULL");
        $db->execute();
        echo "Done!\n";
    } else {
        echo "Column already exists.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
