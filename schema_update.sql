-- =========================================================================================
-- SQL RESTORATION / RESET SCRIPT FOR ROUTE #RT-00466 (KURUNEGALA)
-- Purpose: Remove all test payment records, reconciliation drafts, cash denominations,
--          and pending collection drafts created during testing on Sept 15, 2026.
-- Invoices on this route (20260915260002, 20260915260003, 20260915260004, 20260915260005) are preserved.
-- =========================================================================================

START TRANSACTION;

-- 1. Remove pending collections (draft payments recorded during route execution)
DELETE FROM pending_collections 
WHERE route_id = 466;

-- 2. Clear reconciliation drafts, cash denominations, and accounting entries JSON on deliveries
UPDATE deliveries 
SET cash_denominations = NULL,
    reconciliation_json = NULL,
    accounting_entries_json = NULL,
    selected_credit_invoices = NULL,
    return_stock_json = NULL,
    return_stock_verified_by = NULL,
    return_stock_verified_at = NULL,
    status = 'Pending',
    completed_at = NULL,
    end_meter = NULL
WHERE rep_route_id = 466;

-- 3. Delete payment allocations linked to invoices on route 466 or customer payments of route 466
DELETE FROM customer_payment_allocations 
WHERE invoice_id IN (SELECT id FROM invoices WHERE rep_route_id = 466)
   OR customer_payment_id IN (SELECT id FROM customer_payments WHERE rep_route_id = 466);

-- 4. Delete customer payments created for route 466 or 5000.00 cash payment for RASA WIMANA-WEHERA from test date
DELETE FROM customer_payments 
WHERE rep_route_id = 466 
   OR (amount = 5000.00 
       AND payment_date >= '2026-09-15' 
       AND customer_id IN (SELECT id FROM customers WHERE name LIKE '%RASA WIMANA%'));

-- 5. Delete any test cheques or route expenses recorded for route 466
DELETE FROM cheques 
WHERE rep_route_id = 466;

DELETE FROM route_expenses 
WHERE rep_route_id = 466;

-- 6. Reset invoice status back to 'Unpaid' and delivery_status back to 'Pending' (Invoices & line items remain intact)
UPDATE invoices 
SET status = 'Unpaid',
    delivery_status = 'Pending'
WHERE rep_route_id = 466;

-- 7. Reset daily route status back to 'Delivery Arranged'
UPDATE rep_daily_routes 
SET status = 'Delivery Arranged',
    end_meter = NULL,
    end_time = NULL
WHERE id = 466;

COMMIT;
