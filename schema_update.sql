ALTER TABLE expenses 
  ADD COLUMN IF NOT EXISTS payment_method VARCHAR(50) DEFAULT 'Cash' AFTER amount,
  ADD COLUMN IF NOT EXISTS expense_account_id INT NULL AFTER payment_method,
  ADD COLUMN IF NOT EXISTS payment_account_id INT NULL AFTER expense_account_id,
  ADD COLUMN IF NOT EXISTS petty_cash_transaction_id INT NULL AFTER journal_entry_id;
