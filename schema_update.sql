-- Ensure fast lookups when joining journal entry debit lines for bank transfer payments
CREATE INDEX IF NOT EXISTS idx_tx_je_debit ON transactions (journal_entry_id, debit);
CREATE INDEX IF NOT EXISTS idx_tx_je_credit ON transactions (journal_entry_id, credit);