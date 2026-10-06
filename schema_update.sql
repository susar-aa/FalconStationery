-- Enable 3rd Party Cheque payment method in supplier_payments
ALTER TABLE `supplier_payments` MODIFY COLUMN `payment_method` VARCHAR(50) NOT NULL;

-- Live Database Performance Indexes (aaPanel MariaDB)
ALTER TABLE `cheques` ADD INDEX `idx_chk_status_date` (`status`, `banking_date`);
ALTER TABLE `cheques` ADD INDEX `idx_chk_customer` (`customer_id`);
ALTER TABLE `cheques` ADD INDEX `idx_chk_vendor` (`vendor_id`);
ALTER TABLE `cheques` ADD INDEX `idx_chk_number` (`cheque_number`);

ALTER TABLE `journal_entry_items` ADD INDEX `idx_jei_account` (`account_id`);
ALTER TABLE `journal_entry_items` ADD INDEX `idx_jei_entry` (`journal_entry_id`);
ALTER TABLE `journal_entries` ADD INDEX `idx_je_date` (`date`);

ALTER TABLE `bank_registers` ADD INDEX `idx_br_acc_date` (`bank_account_id`, `date`);
ALTER TABLE `bank_registers` ADD INDEX `idx_br_status` (`status`);
ALTER TABLE `supplier_payments` ADD INDEX `idx_sp_vendor_date` (`vendor_id`, `payment_date`);
ALTER TABLE `supplier_payments` ADD INDEX `idx_sp_method` (`payment_method`);

ALTER TABLE `invoices` ADD INDEX `idx_inv_cust_date` (`customer_id`, `invoice_date`);
ALTER TABLE `invoices` ADD INDEX `idx_inv_status` (`status`, `delivery_status`, `customer_id`);
ALTER TABLE `customer_payments` ADD INDEX `idx_pmt_status` (`status`, `customer_id`);
ALTER TABLE `credit_notes` ADD INDEX `idx_cn_cust` (`customer_id`);
ALTER TABLE `inventory_transactions` ADD INDEX `idx_it_item_date` (`item_id`, `created_at`);
