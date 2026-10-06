-- ==============================================================================
-- SAFE BANK DEPOSIT BACKFILL FOR BANK REGISTER
-- Fixes missing Bank Register entries for "Sent to Bank" (In Transit) deposits
-- Idempotent & Duplicate-Safe (using NOT EXISTS check)
-- ==============================================================================

-- 1. Insert missing transaction entries for destination bank accounts
INSERT INTO transactions (journal_entry_id, account_id, debit, credit, description, is_cleared)
SELECT 
    d.journal_entry_id, 
    d.destination_bank_account_id, 
    d.total_deposit, 
    0.00, 
    CONCAT('Bank Deposit #', d.deposit_number, ' (Sent to Bank)'), 
    0
FROM deposits d
WHERE d.status = 'Sent to Bank'
  AND d.journal_entry_id IS NOT NULL
  AND d.destination_bank_account_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM transactions t 
      WHERE t.journal_entry_id = d.journal_entry_id 
        AND t.account_id = d.destination_bank_account_id
  );

-- 2. Recalculate Chart of Accounts balances for affected bank accounts
UPDATE chart_of_accounts coa
SET coa.balance = (
    SELECT COALESCE(SUM(t.debit - t.credit), 0)
    FROM transactions t
    JOIN journal_entries je ON t.journal_entry_id = je.id
    WHERE t.account_id = coa.id
      AND (je.status IS NULL OR je.status != 'Voided')
)
WHERE coa.id IN (
    SELECT DISTINCT destination_bank_account_id 
    FROM deposits 
    WHERE destination_bank_account_id IS NOT NULL
);
