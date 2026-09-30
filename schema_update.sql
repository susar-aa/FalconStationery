-- Align existing Route 496 invoice payment statuses with actual route collections
UPDATE invoices SET status = 'Paid' WHERE id = 278677;   -- Invoice 20260913170015 (WISDOM BOOK SHOP)
UPDATE invoices SET status = 'Unpaid' WHERE id = 281167; -- Invoice 20260921170026 (OLIVIA SUPER MART)


-- Populate missing account_category for legacy parent accounts in chart_of_accounts
UPDATE chart_of_accounts SET account_category = 'Current Asset' WHERE account_type = 'Asset' AND (account_category IS NULL OR account_category = '');
UPDATE chart_of_accounts SET account_category = 'Current Liability' WHERE account_type = 'Liability' AND (account_category IS NULL OR account_category = '');
UPDATE chart_of_accounts SET account_category = 'Equity' WHERE account_type = 'Equity' AND (account_category IS NULL OR account_category = '');
UPDATE chart_of_accounts SET account_category = 'Revenue' WHERE account_type = 'Revenue' AND (account_category IS NULL OR account_category = '');
UPDATE chart_of_accounts SET account_category = 'Operating Expense' WHERE account_type = 'Expense' AND (account_category IS NULL OR account_category = '');
