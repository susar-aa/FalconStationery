-- RESTORE DELETED ROUTE AND INVOICES
START TRANSACTION;

DELETE FROM `invoice_items` WHERE `invoice_id` IN (SELECT `id` FROM `invoices` WHERE `invoice_number` IN ('20260911240001','20260911240002','20260911240003','20260911240004','20260911240005','20260911240006','20260911240007','20260911240008', '20260912170003', '20260912170005'));
DELETE FROM `invoices` WHERE `invoice_number` IN ('20260911240001','20260911240002','20260911240003','20260911240004','20260911240005','20260911240006','20260911240007','20260911240008', '20260912170003', '20260912170005');
DELETE FROM `rep_daily_routes` WHERE `id` IN (439, 99999);

-- Create a restored route
INSERT INTO `rep_daily_routes` (`id`, `user_id`, `route_name`, `start_meter`, `start_time`, `status`, `created_at`) VALUES (439, 24, 'Nikaweratiya To Pittalam', 0.0, '2026-09-11 08:00:00', 'Adjustments', NOW());

-- Invoice 20260911240001
INSERT INTO `invoices` (`id`, `invoice_number`, `customer_id`, `global_discount_type`, `global_discount_val`, `total_amount`, `status`, `invoice_date`, `due_date`, `created_by`, `rep_route_id`, `stock_status`) VALUES (90000, '20260911240001', 361006, 'Rs', 0, 14179.52, 'Unpaid', '2026-09-11', '2026-09-11', 2, 439, 'deducted');
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900000, 90000, 4543, 225, 'Pocket File - 10', 6.00, 225.00, 1350, 170.00);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900001, 90000, 5231, NULL, 'Math Set 4Pcs 302', 6.00, 140.00, 840, 91.00);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900002, 90000, 5279, NULL, 'Steel Ruler 15cm', 12.00, 50.00, 600, 24.80);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900003, 90000, 5345, NULL, 'Sticker Card Smile 3 Pcs', 6.00, 105.04, 630.24, 80.00);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900004, 90000, 5390, NULL, 'Form Tape 1\' White', 12.00, 76.70, 920.4, 59.00);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900005, 90000, 5441, NULL, 'B Day Party Candle 3622', 6.00, 125.00, 750, 72.25);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900006, 90000, 5469, NULL, 'Pen Holder 9924', 1.00, 478.00, 478, 342.00);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900007, 90000, 5571, NULL, 'Compass Single 8290', 10.00, 110.00, 1100, 85.00);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900008, 90000, 5596, NULL, 'Desighn Clear Bag', 10.00, 50.00, 500, 35.00);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900009, 90000, 5643, NULL, 'Solid Set 9Pcs', 18.00, 240.00, 4320, 185.00);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900010, 90000, 4662, NULL, 'Letter Card Sinhala', 3.00, 200.00, 600, 150.00);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900011, 90000, 4641, NULL, 'Glue Gun Large', 2.00, 505.44, 1010.88, 390.00);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900012, 90000, 4287, 105, 'Number Candles - No 9', 6.00, 35.00, 210, 27.50);

-- Invoice 20260911240002
INSERT INTO `invoices` (`id`, `invoice_number`, `customer_id`, `global_discount_type`, `global_discount_val`, `total_amount`, `status`, `invoice_date`, `due_date`, `created_by`, `rep_route_id`, `stock_status`) VALUES (90001, '20260911240002', 362071, 'Rs', 0, 18403.20, 'Unpaid', '2026-09-11', '2026-09-11', 2, 439, 'deducted');
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900013, 90001, 5372, 130, 'Cello Tape - 48 x 50', 36.00, 162.99, 5867.64, 95.26);

-- Invoice 20260911240003
INSERT INTO `invoices` (`id`, `invoice_number`, `customer_id`, `global_discount_type`, `global_discount_val`, `total_amount`, `status`, `invoice_date`, `due_date`, `created_by`, `rep_route_id`, `stock_status`) VALUES (90002, '20260911240003', 361275, 'Rs', 522.5, 22800.00, 'Unpaid', '2026-09-11', '2026-09-11', 2, 439, 'deducted');
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900014, 90002, 4550, 136, 'Paper File Cover 100Pcs - Blue', 4.00, 1227.50, 4910, 990.00);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900015, 90002, 4550, 136, 'Paper File Cover 100Pcs - Blue', 15.00, 1227.50, 18412.5, 990.00);

-- Invoice 20260911240004
INSERT INTO `invoices` (`id`, `invoice_number`, `customer_id`, `global_discount_type`, `global_discount_val`, `total_amount`, `status`, `invoice_date`, `due_date`, `created_by`, `rep_route_id`, `stock_status`) VALUES (90003, '20260911240004', 361206, 'Rs', 55, 3420.00, 'Unpaid', '2026-09-11', '2026-09-11', 2, 439, 'deducted');
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900016, 90003, 4550, 136, 'Paper File Cover 100Pcs - Blue', 2.00, 1227.50, 2455, 990.00);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900017, 90003, 4624, NULL, 'Redy Binder Gum 500ml', 6.00, 170.00, 1020, 140.00);

-- Invoice 20260911240005
INSERT INTO `invoices` (`id`, `invoice_number`, `customer_id`, `global_discount_type`, `global_discount_val`, `total_amount`, `status`, `invoice_date`, `due_date`, `created_by`, `rep_route_id`, `stock_status`) VALUES (90004, '20260911240005', 361437, 'Rs', 6125, 4320.00, 'Unpaid', '2026-09-11', '2026-09-11', 2, 439, 'deducted');
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900018, 90004, 5024, 250, 'Fabric Paint 15ml 6Pcs - Baby Pink 38', 2.00, 580.00, 1160, 489.60);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900019, 90004, 4346, 191, 'My Clear Bag FC - Blue', 12.00, 42.50, 510, 34.00);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900020, 90004, 4346, 191, 'My Clear Bag FC - Blue', 12.00, 42.50, 510, 34.00);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900021, 90004, 4818, 46, 'Envelop Manila - 16 x 12', 500.00, 13.75, 6875, 11.00);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900022, 90004, 4912, NULL, 'Scissors Small 12Pcs', 1.00, 730.00, 730, 412.80);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900023, 90004, 5571, NULL, 'Compass Single 8290', 6.00, 110.00, 660, 85.00);

-- Invoice 20260911240006
INSERT INTO `invoices` (`id`, `invoice_number`, `customer_id`, `global_discount_type`, `global_discount_val`, `total_amount`, `status`, `invoice_date`, `due_date`, `created_by`, `rep_route_id`, `stock_status`) VALUES (90005, '20260911240006', 361235, 'Rs', 330, 55140.00, 'Unpaid', '2026-09-11', '2026-09-11', 2, 439, 'deducted');
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900024, 90005, 4850, NULL, 'Chalk Color 100Pcs', 6.00, 265.00, 1590, 170.00);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900025, 90005, 4851, NULL, 'Chalk White 100Pcs', 6.00, 225.00, 1350, 160.00);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900026, 90005, 5235, NULL, '3 IN One Sharpener 8103', 12.00, 90.00, 1080, 57.50);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900027, 90005, 4550, 136, 'Paper File Cover 100Pcs - Blue', 6.00, 1227.50, 7365, 990.00);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900028, 90005, 4550, 136, 'Paper File Cover 100Pcs - Blue', 6.00, 1227.50, 7365, 990.00);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900029, 90005, 4624, NULL, 'Redy Binder Gum 500ml', 216.00, 170.00, 36720, 140.00);

-- Invoice 20260911240007
INSERT INTO `invoices` (`id`, `invoice_number`, `customer_id`, `global_discount_type`, `global_discount_val`, `total_amount`, `status`, `invoice_date`, `due_date`, `created_by`, `rep_route_id`, `stock_status`) VALUES (90006, '20260911240007', 361580, 'Rs', 330, 22560.00, 'Unpaid', '2026-09-11', '2026-09-11', 2, 439, 'deducted');
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900030, 90006, 4550, 136, 'Paper File Cover 100Pcs - Blue', 3.00, 1227.50, 3682.5, 990.00);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900031, 90006, 4550, 136, 'Paper File Cover 100Pcs - Blue', 3.00, 1227.50, 3682.5, 990.00);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900032, 90006, 4550, 136, 'Paper File Cover 100Pcs - Blue', 3.00, 1227.50, 3682.5, 990.00);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900033, 90006, 4550, 136, 'Paper File Cover 100Pcs - Blue', 3.00, 1227.50, 3682.5, 990.00);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900034, 90006, 4624, NULL, 'Redy Binder Gum 500ml', 48.00, 170.00, 8160, 140.00);

-- Invoice 20260911240008
INSERT INTO `invoices` (`id`, `invoice_number`, `customer_id`, `global_discount_type`, `global_discount_val`, `total_amount`, `status`, `invoice_date`, `due_date`, `created_by`, `rep_route_id`, `stock_status`) VALUES (90007, '20260911240008', 361100, 'Rs', 0, 6530.09, 'Unpaid', '2026-09-11', '2026-09-11', 2, 439, 'deducted');
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900035, 90007, 4341, NULL, 'Envelop Clear Bag 2316 A6', 12.00, 130.00, 1560, 88.00);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900036, 90007, 4342, NULL, 'Envelop Clear Bag 2318 A4', 12.00, 190.00, 2280, 128.00);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900037, 90007, 4580, NULL, 'Stylish Pocket File Separate B812', 2.00, 900.00, 1800, 600.00);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900038, 90007, 4885, NULL, 'Sticky Notes Multy Color 76*76', 6.00, 78.00, 468, 60.00);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900039, 90007, 5324, NULL, 'Star Sticker Small 10Pcs', 1.00, 122.09, 122.09, 83.00);
INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_id`, `variation_option_id`, `description`, `quantity`, `unit_price`, `total`, `cost_at_sale`) VALUES (900040, 90007, 4666, NULL, 'Slime Mini', 6.00, 50.00, 300, 38.00);

COMMIT;