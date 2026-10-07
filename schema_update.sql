-- 17. Bank Reconciliation Schema & Performance Index
ALTER TABLE transactions ADD COLUMN is_reconciled TINYINT(1) DEFAULT 0;
ALTER TABLE transactions ADD COLUMN reconciliation_id INT NULL;
ALTER TABLE transactions ADD INDEX idx_reconciliation_id (reconciliation_id);

