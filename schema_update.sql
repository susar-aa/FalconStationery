-- Performance Indexes for Custom Date Range Filtering
ALTER TABLE invoices ADD INDEX idx_invoices_invoice_date (invoice_date);
ALTER TABLE customer_payments ADD INDEX idx_cust_pmt_payment_date (payment_date);
ALTER TABLE rep_daily_routes ADD INDEX idx_rep_routes_start_time (start_time);