-- --------------------------------------------------------
-- Full EPF & ETF Management System Migration Script
-- --------------------------------------------------------

-- 1. Ensure Chart of Accounts exist
INSERT IGNORE INTO chart_of_accounts (account_code, account_name, account_type, account_category, balance, is_active) VALUES
('2001', 'EPF Payable', 'Liability', 'Current Liabilities', 0.00, 1),
('2002', 'ETF Payable', 'Liability', 'Current Liabilities', 0.00, 1),
('2003', 'Salary Payable', 'Liability', 'Current Liabilities', 0.00, 1),
('6000', 'Salaries & Wages Expense', 'Expense', 'Operating Expenses', 0.00, 1),
('6010', 'EPF Employer Expense', 'Expense', 'Operating Expenses', 0.00, 1),
('6020', 'ETF Employer Expense', 'Expense', 'Operating Expenses', 0.00, 1);

-- 2. Add EPF/ETF Configuration Columns to Employees Table
SET @dbname = DATABASE();
SET @tablename = "employees";
SET @columnname = "epf_member_no";

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname
  ) > 0,
  "SELECT 1",
  "ALTER TABLE employees ADD COLUMN epf_member_no VARCHAR(50) NULL AFTER status, ADD COLUMN is_epf_eligible TINYINT(1) NOT NULL DEFAULT 1 AFTER epf_member_no, ADD COLUMN is_etf_eligible TINYINT(1) NOT NULL DEFAULT 1 AFTER is_epf_eligible, ADD COLUMN epf_employee_rate DECIMAL(5,2) NOT NULL DEFAULT 8.00 AFTER is_etf_eligible, ADD COLUMN epf_employer_rate DECIMAL(5,2) NOT NULL DEFAULT 12.00 AFTER epf_employee_rate, ADD COLUMN etf_employer_rate DECIMAL(5,2) NOT NULL DEFAULT 3.00 AFTER epf_employer_rate, ADD COLUMN epf_etf_effective_date DATE NULL AFTER etf_employer_rate;"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 3. Add EPF/ETF Breakdown Columns to Payroll Slips Table
SET @tablename = "payroll_slips";
SET @columnname = "epf_employee";

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname
  ) > 0,
  "SELECT 1",
  "ALTER TABLE payroll_slips ADD COLUMN epf_applicable_earnings DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER net_salary, ADD COLUMN epf_employee DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER epf_applicable_earnings, ADD COLUMN epf_employer DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER epf_employee, ADD COLUMN etf_employer DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER epf_employer;"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 4. Add EPF/ETF Summary Columns to Payroll Runs Table
SET @tablename = "payroll_runs";
SET @columnname = "total_epf_employee";

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname
  ) > 0,
  "SELECT 1",
  "ALTER TABLE payroll_runs ADD COLUMN total_epf_employee DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER status, ADD COLUMN total_epf_employer DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER total_epf_employee, ADD COLUMN total_etf_employer DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER total_epf_employer, ADD COLUMN epf_payment_status VARCHAR(20) NOT NULL DEFAULT 'Unpaid' AFTER total_etf_employer, ADD COLUMN etf_payment_status VARCHAR(20) NOT NULL DEFAULT 'Unpaid' AFTER epf_payment_status, ADD COLUMN epf_paid_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER etf_payment_status, ADD COLUMN etf_paid_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER epf_paid_amount;"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 5. Create EPF/ETF Settlement Payments Table
CREATE TABLE IF NOT EXISTS epf_etf_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payroll_run_id INT NOT NULL,
    payment_type ENUM('EPF', 'ETF') NOT NULL DEFAULT 'EPF',
    payment_date DATE NOT NULL,
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,
    total_liability DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    amount_paid DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    bank_account_id INT NOT NULL,
    payment_reference VARCHAR(100) NULL,
    journal_entry_id INT NULL,
    notes TEXT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY idx_run_type (payroll_run_id, payment_type),
    KEY idx_date (payment_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
