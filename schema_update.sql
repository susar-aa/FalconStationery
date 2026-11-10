-- Indexes for Bank Reconciliation performance and query isolation
CREATE INDEX IF NOT EXISTS idx_acc_status_date ON bank_reconciliations (account_id, status, statement_date_end);
CREATE INDEX IF NOT EXISTS idx_tx_account_reconciled ON transactions (account_id, is_reconciled, journal_entry_id);