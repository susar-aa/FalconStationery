-- Make funding_account_id nullable if existing table had NOT NULL constraint:
ALTER TABLE petty_cash_reimbursements MODIFY COLUMN funding_account_id INT NULL DEFAULT NULL;
ALTER TABLE petty_cash_reimbursements ADD COLUMN IF NOT EXISTS bank_account_id INT NULL AFTER amount;
ALTER TABLE petty_cash_reimbursements ADD COLUMN IF NOT EXISTS payment_method VARCHAR(50) DEFAULT 'Bank Transfer' AFTER bank_account_id;
ALTER TABLE petty_cash_reimbursements ADD COLUMN IF NOT EXISTS cheque_number VARCHAR(100) NULL AFTER payment_method;
ALTER TABLE petty_cash_reimbursements ADD COLUMN IF NOT EXISTS cheque_date DATE NULL AFTER cheque_number;
ALTER TABLE petty_cash_reimbursements ADD COLUMN IF NOT EXISTS payee_name VARCHAR(255) NULL AFTER cheque_date;
ALTER TABLE petty_cash_reimbursements ADD COLUMN IF NOT EXISTS cheque_id INT NULL AFTER journal_entry_id;
ALTER TABLE petty_cash_transactions ADD COLUMN IF NOT EXISTS reimbursement_id INT NULL AFTER voucher_number;
