-- Step 1: Clean up zeroed/missing item entries for target invoices
DELETE FROM `invoice_items` WHERE `invoice_id` IN (278630, 278631, 278632, 278633, 278634, 278635, 278639, 278642, 278644);

-- Step 2: Insert original item records for Invoice 20260911170001 (ID: 278642)
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `loaded_quantity`, `unit_price`, `discount_value`, `discount_type`, `total`, `cost_at_sale`) VALUES
(32838, 278642, 5613, NULL, 'Birthday Baloon 9 10Pcs', 1.000, 1.000, 125.03, 0.00, 'fixed', 125.03, 75.00),
(33076, 278642, 5125, NULL, 'Pencil Purse 4465', 1.000, 1.000, 520.00, 0.00, 'fixed', 520.00, 360.00);

-- Step 3: Re-sync invoice totals
UPDATE invoices i 
SET total_amount = (SELECT SUM(total) FROM invoice_items ii WHERE ii.invoice_id = i.id) 
WHERE i.id IN (278630, 278631, 278632, 278633, 278634, 278635, 278639, 278642, 278644);

-- Step 4: Re-sync delivery picking items
DELETE FROM delivery_picking_items WHERE delivery_id IN (SELECT DISTINCT id FROM deliveries WHERE rep_route_id IN (SELECT rep_route_id FROM invoices WHERE id IN (278630, 278631, 278632, 278633, 278634, 278635, 278639, 278642, 278644)));

INSERT INTO delivery_picking_items (delivery_id, item_name, item_id, variation_option_id, required_qty, loaded_qty)
SELECT d.id, ii.description, ii.item_id, ii.variation_option_id, SUM(ii.quantity) as required_qty, SUM(ii.loaded_quantity) as loaded_qty
FROM deliveries d
JOIN invoices i ON i.rep_route_id = d.rep_route_id
JOIN invoice_items ii ON ii.invoice_id = i.id
WHERE i.id IN (278630, 278631, 278632, 278633, 278634, 278635, 278639, 278642, 278644) AND i.status != 'Voided'
GROUP BY d.id, ii.description, ii.item_id, ii.variation_option_id;
