-- Align existing Route 496 invoice payment statuses with actual route collections
UPDATE invoices SET status = 'Paid' WHERE id = 278677;   -- Invoice 20260913170015 (WISDOM BOOK SHOP)
UPDATE invoices SET status = 'Unpaid' WHERE id = 281167; -- Invoice 20260921170026 (OLIVIA SUPER MART)

-- Sync grand_total column on invoices to match true net grand total after discounts and tax
UPDATE invoices SET grand_total = (total_amount - COALESCE(CASE WHEN global_discount_type = '%' THEN (total_amount * global_discount_val / 100) ELSE global_discount_val END, 0) + COALESCE(tax_amount, 0)) WHERE rep_route_id = 496;
