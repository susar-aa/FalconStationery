-- Align existing Route 496 invoice payment statuses with actual route collections
UPDATE invoices SET status = 'Paid' WHERE id = 278677;   -- Invoice 20260913170015 (WISDOM BOOK SHOP)
UPDATE invoices SET status = 'Unpaid' WHERE id = 281167; -- Invoice 20260921170026 (OLIVIA SUPER MART)
