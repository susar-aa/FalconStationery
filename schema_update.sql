-- 1. Add payment method, cheque tracking, and charges columns to loan_repayments
ALTER TABLE `loan_repayments` 
  ADD COLUMN IF NOT EXISTS `journal_entry_id` int(11) DEFAULT NULL AFTER `loan_id`,
  ADD COLUMN IF NOT EXISTS `penalty_charges` decimal(15,2) NOT NULL DEFAULT 0.00 AFTER `bank_charges`,
  ADD COLUMN IF NOT EXISTS `early_settlement_rebate` decimal(15,2) NOT NULL DEFAULT 0.00 AFTER `penalty_charges`,
  ADD COLUMN IF NOT EXISTS `payment_method` enum('Bank Transfer','Standing Order','Direct Debit','Cheque','Cash') DEFAULT 'Bank Transfer' AFTER `total_payment`,
  ADD COLUMN IF NOT EXISTS `cheque_number` varchar(50) DEFAULT NULL AFTER `payment_method`,
  ADD COLUMN IF NOT EXISTS `cheque_date` date DEFAULT NULL AFTER `cheque_number`,
  ADD COLUMN IF NOT EXISTS `banking_date` date DEFAULT NULL AFTER `cheque_date`;

-- 2. Add indexes on loan_repayments for query performance
ALTER TABLE `loan_repayments`
  ADD INDEX IF NOT EXISTS `idx_loan_rep_method` (`payment_method`),
  ADD INDEX IF NOT EXISTS `idx_loan_rep_bank_acc` (`bank_account_id`);

-- 3. Add loan repayment tracking to cheques table for issued cheques
ALTER TABLE `cheques` 
  ADD COLUMN IF NOT EXISTS `loan_repayment_id` int(11) DEFAULT NULL AFTER `supplier_payment_id`,
  ADD COLUMN IF NOT EXISTS `loan_id` int(11) DEFAULT NULL AFTER `loan_repayment_id`,
  ADD INDEX IF NOT EXISTS `idx_cheques_loan_rep` (`loan_repayment_id`),
  ADD INDEX IF NOT EXISTS `idx_cheques_loan` (`loan_id`);

-- 4. Add missing loan accounts and tracking columns to loans table
ALTER TABLE `loans` 
  ADD COLUMN IF NOT EXISTS `interest_expense_account_id` int(11) DEFAULT NULL AFTER `liability_account_id`,
  ADD COLUMN IF NOT EXISTS `bank_charges_account_id` int(11) DEFAULT NULL AFTER `interest_expense_account_id`,
  ADD COLUMN IF NOT EXISTS `loan_type` varchar(50) DEFAULT 'Term Loan' AFTER `loan_number`,
  ADD COLUMN IF NOT EXISTS `monthly_installment` decimal(15,2) DEFAULT 0.00 AFTER `principal_amount`,
  ADD COLUMN IF NOT EXISTS `interest_type` varchar(50) DEFAULT 'Flat Rate' AFTER `interest_rate`,
  ADD COLUMN IF NOT EXISTS `is_historical_opening` tinyint(1) DEFAULT 0 AFTER `status`,
  ADD COLUMN IF NOT EXISTS `opening_balance` decimal(15,2) DEFAULT 0.00 AFTER `is_historical_opening`;
