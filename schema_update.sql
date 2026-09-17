-- Section 15: Fix Invoice Status Column Truncation for 'Partially Paid'
ALTER TABLE invoices MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'Unpaid';
