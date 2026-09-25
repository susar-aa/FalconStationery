-- 1. Add journal_entry_id column to pending_collections
ALTER TABLE pending_collections ADD COLUMN journal_entry_id INT NULL;

-- 2. Backfill existing finalized collections with their corresponding journal entry IDs
UPDATE pending_collections pc
JOIN customer_payments cp ON cp.rep_route_id = pc.route_id 
                         AND cp.customer_id = pc.customer_id 
                         AND ABS(cp.amount - pc.amount) < 0.01 
                         AND cp.payment_method = pc.payment_method
SET pc.journal_entry_id = cp.journal_entry_id
WHERE pc.status = 'Finalized' AND pc.journal_entry_id IS NULL AND cp.journal_entry_id IS NOT NULL;


-- Enforce unique collection constraints on pending_collections
ALTER TABLE pending_collections ADD UNIQUE KEY uq_rep_local (mobile_rep_id, mobile_local_id);
ALTER TABLE pending_collections ADD UNIQUE KEY uq_pending_uuid (uuid);

-- Migration: Add route closure gate controls & variance approval fields
ALTER TABLE deliveries 
  ADD COLUMN IF NOT EXISTS variance_approval_reason TEXT NULL,
  ADD COLUMN IF NOT EXISTS variance_approved_by INT NULL,
  ADD COLUMN IF NOT EXISTS variance_approved_at DATETIME NULL;

ALTER TABLE rep_daily_routes 
  ADD COLUMN IF NOT EXISTS variance_approval_reason TEXT NULL,
  ADD COLUMN IF NOT EXISTS variance_approved_by INT NULL,
  ADD COLUMN IF NOT EXISTS variance_approved_at DATETIME NULL;

INSERT IGNORE INTO migrations (migration) VALUES ('add_route_closure_gate_columns');


-- Migration: Create sync_exceptions table for Exceptions Inbox
CREATE TABLE IF NOT EXISTS `sync_exceptions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `category` ENUM('unmapped_product', 'unknown_customer', 'route_not_found', 'duplicate_event', 'coa_not_configured', 'stock_conflict', 'other') NOT NULL DEFAULT 'other',
    `entity_type` VARCHAR(50) NOT NULL,
    `local_id` VARCHAR(100) NULL,
    `uuid` VARCHAR(255) NULL,
    `route_id` INT NULL,
    `error_message` TEXT NOT NULL,
    `payload` LONGTEXT NOT NULL,
    `status` ENUM('pending', 'resolved', 'ignored') NOT NULL DEFAULT 'pending',
    `resolution_notes` TEXT NULL,
    `resolved_by` INT NULL,
    `resolved_at` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_user_id` (`user_id`),
    KEY `idx_category` (`category`),
    KEY `idx_status` (`status`),
    KEY `idx_entity_type` (`entity_type`),
    KEY `idx_uuid` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Migration: Add grand_total column to invoices and backfill existing rows
ALTER TABLE `invoices` ADD COLUMN `grand_total` DECIMAL(15,2) NULL AFTER `tax_amount`;

UPDATE `invoices` 
SET `grand_total` = ROUND(
    `total_amount` - COALESCE(CASE WHEN `global_discount_type` = '%' THEN (`total_amount` * `global_discount_val` / 100) ELSE `global_discount_val` END, 0) + COALESCE(`tax_amount`, 0),
    2
) 
WHERE `grand_total` IS NULL;


-- Migration: Seed System Chart of Accounts (COA)
INSERT INTO `chart_of_accounts` (`account_code`, `account_name`, `account_type`, `account_category`, `balance`, `is_active`)
SELECT '1000', 'Cash in Hand', 'Asset', 'Current Assets', 0.00, 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `chart_of_accounts` WHERE `account_code` = '1000') UNION ALL
SELECT '1010', 'Bank Account', 'Asset', 'Current Assets', 0.00, 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `chart_of_accounts` WHERE `account_code` = '1010') UNION ALL
SELECT '1090', 'Driver Transit Collections (Temp)', 'Asset', 'Current Assets', 0.00, 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `chart_of_accounts` WHERE `account_code` = '1090') UNION ALL
SELECT '1100', 'Undeposited Cash', 'Asset', 'Current Assets', 0.00, 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `chart_of_accounts` WHERE `account_code` = '1100') UNION ALL
SELECT '1200', 'Accounts Receivable', 'Asset', 'Current Assets', 0.00, 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `chart_of_accounts` WHERE `account_code` = '1200') UNION ALL
SELECT '1300', 'Inventory Asset', 'Asset', 'Current Assets', 0.00, 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `chart_of_accounts` WHERE `account_code` = '1300') UNION ALL
SELECT '1600', 'Customer Advance Payments', 'Liability', 'Current Liabilities', 0.00, 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `chart_of_accounts` WHERE `account_code` = '1600') UNION ALL
SELECT '1605', 'Undeposited Cheques Clearing', 'Asset', 'Current Assets', 0.00, 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `chart_of_accounts` WHERE `account_code` = '1605') UNION ALL
SELECT '2110', 'VAT / Sales Tax Payable', 'Liability', 'Current Liabilities', 0.00, 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `chart_of_accounts` WHERE `account_code` = '2110') UNION ALL
SELECT '4000', 'Sales Revenue', 'Revenue', 'Sales', 0.00, 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `chart_of_accounts` WHERE `account_code` = '4000') UNION ALL
SELECT '4050', 'Discounts Allowed', 'Expense', 'Discounts', 0.00, 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `chart_of_accounts` WHERE `account_code` = '4050') UNION ALL
SELECT '5000', 'Cost of Goods Sold (COGS)', 'Expense', 'Cost of Goods Sold', 0.00, 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `chart_of_accounts` WHERE `account_code` = '5000') UNION ALL
SELECT '6100', 'Staff Meals & Entertainment', 'Expense', 'Operating Expenses', 0.00, 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `chart_of_accounts` WHERE `account_code` = '6100') UNION ALL
SELECT '6200', 'Accommodation', 'Expense', 'Operating Expenses', 0.00, 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `chart_of_accounts` WHERE `account_code` = '6200') UNION ALL
SELECT '6300', 'Parking & Tolls', 'Expense', 'Operating Expenses', 0.00, 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `chart_of_accounts` WHERE `account_code` = '6300') UNION ALL
SELECT '6700', 'Travel Transport (Fuel)', 'Expense', 'Operating Expenses', 0.00, 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `chart_of_accounts` WHERE `account_code` = '6700') UNION ALL
SELECT '6800', 'Vehicle Maintenance', 'Expense', 'Operating Expenses', 0.00, 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `chart_of_accounts` WHERE `account_code` = '6800') UNION ALL
SELECT '6900', 'Miscellaneous Expenses', 'Expense', 'Operating Expenses', 0.00, 1 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `chart_of_accounts` WHERE `account_code` = '6900');
