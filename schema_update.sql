-- Ensure supplier payments have index on vendor_id, status, and payment_date
CREATE INDEX IF NOT EXISTS idx_sp_vendor_status_date ON supplier_payments (vendor_id, status, payment_date);

-- Ensure cheques table has composite index for matching payment links
CREATE INDEX IF NOT EXISTS idx_cheques_supplier_pmt ON cheques (supplier_payment_id);
CREATE INDEX IF NOT EXISTS idx_cheques_vendor_amt ON cheques (vendor_id, amount);
CREATE INDEX IF NOT EXISTS idx_cheques_customer_amt ON cheques (customer_id, amount);

-- Ensure goods receipt notes have index on vendor_id and approval
CREATE INDEX IF NOT EXISTS idx_grn_vendor_approved ON goods_receipt_notes (vendor_id, is_approved, grn_date);