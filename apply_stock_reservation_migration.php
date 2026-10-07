<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Database.php';

try {
    $db = new Database();
    
    // 1. Create stock_reservations table
    $db->query("CREATE TABLE IF NOT EXISTS `stock_reservations` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `sales_order_id` INT NOT NULL,
        `item_id` INT NOT NULL,
        `variation_option_id` INT NULL,
        `reserved_qty` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        `status` ENUM('active', 'fulfilled', 'expired', 'released') NOT NULL DEFAULT 'active',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        KEY `idx_item_var_status` (`item_id`, `variation_option_id`, `status`),
        KEY `idx_so_status` (`sales_order_id`, `status`),
        KEY `idx_status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    $db->execute();
    echo "1. stock_reservations table created or verified.\n";

    // 2. Add doc_type column to invoices if not present
    $db->query("SHOW COLUMNS FROM invoices LIKE 'doc_type'");
    if (!$db->single()) {
        $db->query("ALTER TABLE invoices ADD COLUMN doc_type ENUM('sales_order', 'tax_invoice') NOT NULL DEFAULT 'tax_invoice' AFTER invoice_number");
        $db->execute();
        echo "2. Added doc_type column to invoices.\n";
    } else {
        echo "2. doc_type column already exists in invoices.\n";
    }

    // 3. Add fulfillment_status column to invoices if not present
    $db->query("SHOW COLUMNS FROM invoices LIKE 'fulfillment_status'");
    if (!$db->single()) {
        $db->query("ALTER TABLE invoices ADD COLUMN fulfillment_status ENUM('pending', 'picked', 'dispatched', 'delivered', 'cancelled') NOT NULL DEFAULT 'delivered' AFTER status");
        $db->execute();
        echo "3. Added fulfillment_status column to invoices.\n";
    } else {
        echo "3. fulfillment_status column already exists in invoices.\n";
    }

    echo "Local DB migration completed successfully!\n";

} catch (Exception $e) {
    echo "Migration Error: " . $e->getMessage() . "\n";
}
