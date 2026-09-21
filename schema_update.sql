-- 1. Safely remove any empty/partial entries for the affected invoices
DELETE FROM `invoice_items` WHERE `invoice_id` IN (278636, 278637, 278638);

-- Invoice 20260910240098 (ID 278636) - Customer: IMAN BOOK SHOP-POLGAHAWELA - Total: Rs 19,900.00
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `loaded_quantity`, `unit_price`, `discount_value`, `discount_type`, `total`, `cost_at_sale`) VALUES
(NULL, 278636, 4351, 220, 'Rainbow Mesh Bag - A 6', 12.00, 12.00, 52.00, 0.00, 'Rs', 624.00, 52.00),
(NULL, 278636, 4351, 222, 'Rainbow Mesh Bag - A 3', 12.00, 12.00, 190.00, 0.00, 'Rs', 2280.00, 52.00),
(NULL, 278636, 4351, 223, 'Rainbow Mesh Bag - B 4', 12.00, 12.00, 156.00, 0.00, 'Rs', 1872.00, 52.00),
(NULL, 278636, 4351, 224, 'Rainbow Mesh Bag - A 5', 12.00, 12.00, 52.00, 0.00, 'Rs', 624.00, 52.00),
(NULL, 278636, 4386, NULL, 'Copy Paper A3 80 Gsm', 5.00, 5.00, 1562.00, 0.00, 'Rs', 7810.00, 2000.00),
(NULL, 278636, 4665, NULL, 'Slime 150', 6.00, 6.00, 185.00, 0.00, 'Rs', 1110.00, 185.00),
(NULL, 278636, 4791, 239, 'Cello CD Marker - Black', 72.00, 72.00, 65.00, 0.00, 'Rs', 4680.00, 65.00),
(NULL, 278636, 5477, NULL, 'Slime 148', 6.00, 6.00, 150.00, 0.00, 'Rs', 900.00, 150.00);

-- Invoice 20260910240099 (ID 278637) - Customer: CITY BOOK SHOP-PUTTALAM - Total: Rs 62,665.60
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `loaded_quantity`, `unit_price`, `discount_value`, `discount_type`, `total`, `cost_at_sale`) VALUES
(NULL, 278637, 4577, NULL, 'File Separate A4 1 To 10', 10.00, 10.00, 138.00, 0.00, 'Rs', 1380.00, 138.00),
(NULL, 278637, 4685, NULL, 'Kids Learning Clock Large', 12.00, 12.00, 96.00, 0.00, 'Rs', 1152.00, 96.00),
(NULL, 278637, 5104, NULL, 'Magnet Box 1230', 40.00, 40.00, 135.00, 0.00, 'Rs', 5400.00, 135.00),
(NULL, 278637, 5174, NULL, 'Pencil Purse 97A', 24.00, 24.00, 410.00, 0.00, 'Rs', 9840.00, 410.00),
(NULL, 278637, 5235, NULL, '3 IN One Sharpener 8103', 48.00, 48.00, 57.50, 0.00, 'Rs', 2760.00, 57.50),
(NULL, 278637, 5654, NULL, 'Lunch Box ET118', 12.00, 12.00, 238.52, 0.00, 'Rs', 2862.24, 238.52),
(NULL, 278637, 4550, 248, 'Paper File Cover 100Pcs - Mix', 15.00, 15.00, 1200.00, 0.00, 'Rs', 18000.00, 912.00),
(NULL, 278637, 4581, 109, 'Magazine Holder 3 Inch - Black', 32.00, 32.00, 265.00, 0.00, 'Rs', 8480.00, 265.00),
(NULL, 278637, 4594, 120, 'Magazine Holder 6 Inch - Black', 8.00, 8.00, 338.92, 0.00, 'Rs', 2711.36, 305.00),
(NULL, 278637, 4857, NULL, 'Fancy Note Book 2518', 18.00, 18.00, 560.00, 0.00, 'Rs', 10080.00, 560.00);

-- Invoice 20260910240100 (ID 278638) - Customer: A TO Z BOOK SHOP-PUTTALAM - Total: Rs 66,204.00
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `loaded_quantity`, `unit_price`, `discount_value`, `discount_type`, `total`, `cost_at_sale`) VALUES
(NULL, 278638, 4653, NULL, 'Scratch Book Small', 24.00, 24.00, 84.00, 0.00, 'Rs', 2016.00, 84.00),
(NULL, 278638, 4863, NULL, 'Fancy Note Book Unicorn', 30.00, 30.00, 590.00, 0.00, 'Rs', 17700.00, 590.00),
(NULL, 278638, 4866, NULL, 'Fancy Note Book Wool', 12.00, 12.00, 590.00, 0.00, 'Rs', 7080.00, 590.00),
(NULL, 278638, 4857, NULL, 'Fancy Note Book 2518', 12.00, 12.00, 560.00, 0.00, 'Rs', 6720.00, 560.00),
(NULL, 278638, 5296, NULL, 'Stencil 1Pcs', 12.00, 12.00, 60.00, 0.00, 'Rs', 720.00, 60.00),
(NULL, 278638, 5297, NULL, 'Stencil 2Pcs', 12.00, 12.00, 120.00, 0.00, 'Rs', 1440.00, 120.00),
(NULL, 278638, 5526, NULL, 'Magnet Box PX500', 24.00, 24.00, 622.00, 0.00, 'Rs', 14928.00, 960.00),
(NULL, 278638, 5600, NULL, 'Panda Bendy Pen 48 Pcs', 2.00, 2.00, 3120.00, 0.00, 'Rs', 6240.00, 3120.00),
(NULL, 278638, 5569, NULL, 'Vacum Cup 169', 12.00, 12.00, 780.00, 0.00, 'Rs', 9360.00, 780.00);

-- 2. Sync invoice totals to match item line sums for bound routes
UPDATE invoices i 
SET total_amount = (SELECT SUM(total) FROM invoice_items ii WHERE ii.invoice_id = i.id) 
WHERE i.id IN (278491, 278492, 278503, 278504, 278505, 278515, 278516, 278636, 278637, 278638);
