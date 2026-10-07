-- 17. Bank Reconciliation Schema & Performance Index
ALTER TABLE transactions ADD COLUMN is_reconciled TINYINT(1) DEFAULT 0;
ALTER TABLE transactions ADD COLUMN reconciliation_id INT NULL;
ALTER TABLE transactions ADD INDEX idx_reconciliation_id (reconciliation_id);

-- 18. EPF & ETF System Support
INSERT IGNORE INTO chart_of_accounts (account_code, account_name, account_type, account_category, balance, is_active) VALUES
('2001', 'EPF Payable', 'Liability', 'Current Liabilities', 0.00, 1),
('2002', 'ETF Payable', 'Liability', 'Current Liabilities', 0.00, 1),
('2003', 'Salary Payable', 'Liability', 'Current Liabilities', 0.00, 1),
('6000', 'Salaries & Wages Expense', 'Expense', 'Operating Expenses', 0.00, 1),
('6010', 'EPF Employer Expense', 'Expense', 'Operating Expenses', 0.00, 1),
('6020', 'ETF Employer Expense', 'Expense', 'Operating Expenses', 0.00, 1);

ALTER TABLE employees ADD COLUMN epf_member_no VARCHAR(50) NULL;
ALTER TABLE employees ADD COLUMN is_epf_eligible TINYINT(1) DEFAULT 1;
ALTER TABLE employees ADD COLUMN is_etf_eligible TINYINT(1) DEFAULT 1;
ALTER TABLE employees ADD COLUMN epf_employee_rate DECIMAL(5,2) DEFAULT 8.00;
ALTER TABLE employees ADD COLUMN epf_employer_rate DECIMAL(5,2) DEFAULT 12.00;
ALTER TABLE employees ADD COLUMN etf_employer_rate DECIMAL(5,2) DEFAULT 3.00;
ALTER TABLE employees ADD COLUMN epf_etf_effective_date DATE NULL;

ALTER TABLE payroll_slips ADD COLUMN epf_applicable_earnings DECIMAL(15,2) DEFAULT 0.00;
ALTER TABLE payroll_slips ADD COLUMN epf_employee_amount DECIMAL(15,2) DEFAULT 0.00;
ALTER TABLE payroll_slips ADD COLUMN epf_employer_amount DECIMAL(15,2) DEFAULT 0.00;
ALTER TABLE payroll_slips ADD COLUMN etf_employer_amount DECIMAL(15,2) DEFAULT 0.00;

ALTER TABLE payroll_runs ADD COLUMN total_epf_employee DECIMAL(15,2) DEFAULT 0.00;
ALTER TABLE payroll_runs ADD COLUMN total_epf_employer DECIMAL(15,2) DEFAULT 0.00;
ALTER TABLE payroll_runs ADD COLUMN total_etf_employer DECIMAL(15,2) DEFAULT 0.00;
ALTER TABLE payroll_runs ADD COLUMN epf_payment_status VARCHAR(20) DEFAULT 'Unpaid';
ALTER TABLE payroll_runs ADD COLUMN etf_payment_status VARCHAR(20) DEFAULT 'Unpaid';
ALTER TABLE payroll_runs ADD COLUMN epf_paid_amount DECIMAL(15,2) DEFAULT 0.00;
ALTER TABLE payroll_runs ADD COLUMN etf_paid_amount DECIMAL(15,2) DEFAULT 0.00;

ALTER TABLE company_settings ADD COLUMN default_epf_employee_rate DECIMAL(5,2) DEFAULT 8.00;
ALTER TABLE company_settings ADD COLUMN default_epf_employer_rate DECIMAL(5,2) DEFAULT 12.00;
ALTER TABLE company_settings ADD COLUMN default_etf_employer_rate DECIMAL(5,2) DEFAULT 3.00;

CREATE TABLE IF NOT EXISTS epf_etf_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payroll_run_id INT NOT NULL,
    payment_type ENUM('EPF', 'ETF') NOT NULL,
    payment_date DATE NOT NULL,
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,
    total_liability DECIMAL(15,2) NOT NULL,
    amount_paid DECIMAL(15,2) NOT NULL,
    bank_account_id INT NOT NULL,
    payment_reference VARCHAR(100) NULL,
    journal_entry_id INT NULL,
    notes TEXT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_run_type (payroll_run_id, payment_type),
    INDEX idx_date (payment_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


