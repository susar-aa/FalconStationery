-- Add clearance tracking columns and index to cheques table
ALTER TABLE `cheques` 
ADD COLUMN `cleared_date` DATE NULL AFTER `banking_date`,
ADD COLUMN `cleared_by` INT(11) NULL AFTER `cleared_date`,
ADD COLUMN `clearance_journal_id` INT(11) NULL AFTER `cleared_by`,
ADD COLUMN `clearance_notes` VARCHAR(255) NULL AFTER `clearance_journal_id`;

ALTER TABLE `cheques` ADD INDEX `idx_chk_clearance` (`status`, `bank_account_id`, `cleared_date`);
