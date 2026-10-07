-- --------------------------------------------------------
-- Stock Reservation & Fulfillment Migration Script
-- --------------------------------------------------------

-- 1. Create Stock Reservations Table
CREATE TABLE IF NOT EXISTS `stock_reservations` (
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
    KEY `idx_status` (`status`),
    CONSTRAINT `fk_stock_res_so` FOREIGN KEY (`sales_order_id`) REFERENCES `invoices`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Add doc_type Column to Invoices Table safely
SET @dbname = DATABASE();
SET @tablename = "invoices";
SET @columnname = "doc_type";

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname
  ) > 0,
  "SELECT 1",
  "ALTER TABLE invoices ADD COLUMN doc_type ENUM('sales_order', 'tax_invoice') NOT NULL DEFAULT 'tax_invoice' AFTER invoice_number;"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 3. Add fulfillment_status Column to Invoices Table safely
SET @columnname = "fulfillment_status";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname
  ) > 0,
  "SELECT 1",
  "ALTER TABLE invoices ADD COLUMN fulfillment_status ENUM('pending', 'picked', 'dispatched', 'delivered', 'cancelled') NOT NULL DEFAULT 'delivered' AFTER status;"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;
