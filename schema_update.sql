
-- 16. Performance Optimization Indexes for Voided Status Syncing
ALTER TABLE petty_cash_transactions ADD INDEX idx_status_journal (status, journal_entry_id);
ALTER TABLE customer_payments ADD INDEX idx_status_journal (status, journal_entry_id);
ALTER TABLE supplier_payments ADD INDEX idx_status_journal (status, journal_entry_id);
ALTER TABLE journal_entries ADD INDEX idx_status_id (status, id);


