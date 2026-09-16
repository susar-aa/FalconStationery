-- ==========================================================================
-- SQL SCRIPT TO RESTORE FINALIZED ROUTE STATUSES & DELIVERY STATES
-- Restores 'Completed' status for rep routes, deliveries & invoices
-- ==========================================================================

START TRANSACTION;

-- --------------------------------------------------------------------------
-- 1. UPDATING REP DAILY ROUTES TO FINALIZED / COMPLETED STATUS
-- --------------------------------------------------------------------------
UPDATE `rep_daily_routes` SET `status` = 'Completed', `start_meter` = '98370.0', `end_meter` = '98531.0', `start_time` = '2026-08-22 08:54:46', `end_time` = '2026-08-22 18:49:37' WHERE `id` = 374;
UPDATE `rep_daily_routes` SET `status` = 'Completed', `start_meter` = '98659.0', `end_meter` = '98786.0', `start_time` = '2026-08-24 08:19:27', `end_time` = '2026-08-24 20:05:06' WHERE `id` = 384;
UPDATE `rep_daily_routes` SET `status` = 'Completed', `start_meter` = '98937.0', `end_meter` = '99023.0', `start_time` = '2026-08-26 07:53:52', `end_time` = '2026-08-26 19:08:05' WHERE `id` = 392;
UPDATE `rep_daily_routes` SET `status` = 'Completed', `start_meter` = '99183.0', `end_meter` = '99329.0', `start_time` = '2026-08-29 09:22:00', `end_time` = '2026-08-29 18:32:55' WHERE `id` = 400;
UPDATE `rep_daily_routes` SET `status` = 'Completed', `start_meter` = '99329.0', `end_meter` = '99429.0', `start_time` = '2026-08-30 07:40:03', `end_time` = '2026-08-30 18:40:26' WHERE `id` = 402;
UPDATE `rep_daily_routes` SET `status` = 'Completed', `start_meter` = '99430.0', `end_meter` = '99538.0', `start_time` = '2026-08-31 07:41:25', `end_time` = '2026-08-31 17:36:48' WHERE `id` = 407;
UPDATE `rep_daily_routes` SET `status` = 'Completed', `start_meter` = '17170.0', `end_meter` = '17291.0', `start_time` = '2026-09-01 09:19:25', `end_time` = '2026-09-01 21:58:16' WHERE `id` = 408;
UPDATE `rep_daily_routes` SET `status` = 'Completed', `start_meter` = '17330.0', `end_meter` = '17452.0', `start_time` = '2026-09-02 09:42:02', `end_time` = '2026-09-02 22:40:12' WHERE `id` = 411;
UPDATE `rep_daily_routes` SET `status` = 'Completed', `start_meter` = '3384.0', `end_meter` = '3517.0', `start_time` = '2026-09-02 08:49:38', `end_time` = '2026-09-02 17:41:53' WHERE `id` = 412;
UPDATE `rep_daily_routes` SET `status` = 'Completed', `start_meter` = '3902.0', `end_meter` = '4059.0', `start_time` = '2026-09-07 08:50:24', `end_time` = '2026-09-08 08:08:32' WHERE `id` = 427;
-- Updated 10 rep daily routes.

-- --------------------------------------------------------------------------
-- 2. UPDATING DELIVERIES TO FINALIZED / COMPLETED STATUS
-- --------------------------------------------------------------------------
UPDATE `deliveries` SET `status` = 'Completed', `completed_at` = NULL WHERE `id` = 212;
UPDATE `deliveries` SET `status` = 'Completed', `completed_at` = NULL WHERE `id` = 215;
UPDATE `deliveries` SET `status` = 'Completed', `completed_at` = NULL WHERE `id` = 220;
UPDATE `deliveries` SET `status` = 'Completed', `completed_at` = NULL WHERE `id` = 221;
UPDATE `deliveries` SET `status` = 'Completed', `completed_at` = NULL WHERE `id` = 222;
-- Updated 5 deliveries.

-- --------------------------------------------------------------------------
-- 3. UPDATING INVOICES DELIVERY & PAYMENT STATUSES LINKED TO ROUTES
-- --------------------------------------------------------------------------
UPDATE `invoices` SET `status` = 'Paid' WHERE `id` = 277538;
UPDATE `invoices` SET `status` = 'Paid' WHERE `id` = 277647;
UPDATE `invoices` SET `status` = 'Paid' WHERE `id` = 277875;
UPDATE `invoices` SET `status` = 'Paid' WHERE `id` = 277990;
UPDATE `invoices` SET `delivery_status` = 'Delivered' WHERE `id` = 278246;
UPDATE `invoices` SET `delivery_status` = 'Delivered', `status` = 'Paid' WHERE `id` = 278247;
UPDATE `invoices` SET `delivery_status` = 'Cancelled' WHERE `id` = 278248;
UPDATE `invoices` SET `delivery_status` = 'Cancelled' WHERE `id` = 278249;
UPDATE `invoices` SET `delivery_status` = 'Delivered', `status` = 'Paid' WHERE `id` = 278250;
UPDATE `invoices` SET `delivery_status` = 'Delivered', `status` = 'Paid' WHERE `id` = 278251;
UPDATE `invoices` SET `delivery_status` = 'Delivered', `status` = 'Paid' WHERE `id` = 278285;
UPDATE `invoices` SET `delivery_status` = 'Delivered' WHERE `id` = 278286;
UPDATE `invoices` SET `delivery_status` = 'Delivered', `status` = 'Paid' WHERE `id` = 278334;
UPDATE `invoices` SET `delivery_status` = 'Delivered' WHERE `id` = 278362;
UPDATE `invoices` SET `delivery_status` = 'Delivered' WHERE `id` = 278363;
UPDATE `invoices` SET `delivery_status` = 'Delivered', `status` = 'Paid' WHERE `id` = 278391;
UPDATE `invoices` SET `delivery_status` = 'Delivered' WHERE `id` = 278399;
UPDATE `invoices` SET `delivery_status` = 'Delivered', `status` = 'Paid' WHERE `id` = 278400;
UPDATE `invoices` SET `delivery_status` = 'Delivered', `status` = 'Paid' WHERE `id` = 278419;
UPDATE `invoices` SET `delivery_status` = 'Delivered' WHERE `id` = 278420;
UPDATE `invoices` SET `delivery_status` = 'Delivered' WHERE `id` = 278426;
UPDATE `invoices` SET `delivery_status` = 'Delivered' WHERE `id` = 278427;
UPDATE `invoices` SET `delivery_status` = 'Delivered' WHERE `id` = 278428;
UPDATE `invoices` SET `delivery_status` = 'Delivered', `status` = 'Paid' WHERE `id` = 278429;
UPDATE `invoices` SET `delivery_status` = 'Delivered', `status` = 'Paid' WHERE `id` = 278430;
UPDATE `invoices` SET `delivery_status` = 'Delivered', `status` = 'Paid' WHERE `id` = 278431;
UPDATE `invoices` SET `delivery_status` = 'Delivered', `status` = 'Paid' WHERE `id` = 278435;
UPDATE `invoices` SET `delivery_status` = 'Delivered' WHERE `id` = 278436;
UPDATE `invoices` SET `delivery_status` = 'Delivered', `status` = 'Paid' WHERE `id` = 278437;
UPDATE `invoices` SET `delivery_status` = 'Delivered' WHERE `id` = 278444;
UPDATE `invoices` SET `delivery_status` = 'Cancelled' WHERE `id` = 278445;
UPDATE `invoices` SET `delivery_status` = 'Cancelled' WHERE `id` = 278448;
UPDATE `invoices` SET `delivery_status` = 'Cancelled' WHERE `id` = 278449;
UPDATE `invoices` SET `delivery_status` = 'Delivered', `status` = 'Paid' WHERE `id` = 278451;
UPDATE `invoices` SET `delivery_status` = 'Delivered' WHERE `id` = 278452;
UPDATE `invoices` SET `delivery_status` = 'Cancelled' WHERE `id` = 278460;
UPDATE `invoices` SET `delivery_status` = 'Cancelled' WHERE `id` = 278483;
UPDATE `invoices` SET `delivery_status` = 'Cancelled' WHERE `id` = 278484;
UPDATE `invoices` SET `delivery_status` = 'Delivered' WHERE `id` = 278487;
UPDATE `invoices` SET `delivery_status` = 'Delivered' WHERE `id` = 278488;
UPDATE `invoices` SET `delivery_status` = 'Delivered', `status` = 'Paid' WHERE `id` = 278546;
UPDATE `invoices` SET `delivery_status` = 'Delivered', `status` = 'Paid' WHERE `id` = 278547;
UPDATE `invoices` SET `delivery_status` = 'Delivered' WHERE `id` = 278548;
UPDATE `invoices` SET `delivery_status` = 'Delivered' WHERE `id` = 278549;
UPDATE `invoices` SET `delivery_status` = 'Cancelled' WHERE `id` = 278577;
UPDATE `invoices` SET `delivery_status` = 'Delivered', `status` = 'Paid' WHERE `id` = 278578;
UPDATE `invoices` SET `delivery_status` = 'Cancelled' WHERE `id` = 278590;
-- Updated 47 invoices.

COMMIT;
