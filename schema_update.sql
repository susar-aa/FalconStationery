-- 1. Create salary_advances Table
CREATE TABLE IF NOT EXISTS salary_advances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    advance_number VARCHAR(50) NOT NULL UNIQUE,
    employee_id INT NOT NULL,
    advance_amount DECIMAL(15,2) NOT NULL,
    recovered_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    outstanding_amount DECIMAL(15,2) NOT NULL,
    payment_date DATE NOT NULL,
    payment_method ENUM('Petty Cash', 'Cash in Hand', 'Bank Transfer', 'Cheque') NOT NULL,
    source_account_id INT NOT NULL,
    bank_account_id INT NULL,
    advance_account_id INT NOT NULL,
    reference_no VARCHAR(100) NULL,
    notes TEXT NULL,
    status ENUM('Pending', 'Approved', 'Paid', 'Partially Recovered', 'Fully Recovered', 'Cancelled') NOT NULL DEFAULT 'Paid',
    journal_entry_id INT NULL,
    petty_cash_transaction_id INT NULL,
    created_by INT NOT NULL,
    approved_by INT NULL,
    approved_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_sa_employee (employee_id),
    INDEX idx_sa_status (status),
    INDEX idx_sa_payment_date (payment_date),
    INDEX idx_sa_journal (journal_entry_id),
    INDEX idx_sa_petty_cash (petty_cash_transaction_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Create salary_advance_recoveries Table
CREATE TABLE IF NOT EXISTS salary_advance_recoveries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    salary_advance_id INT NOT NULL,
    payroll_slip_id INT NULL,
    recovery_date DATE NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    notes VARCHAR(255) NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_sar_advance (salary_advance_id),
    INDEX idx_sar_slip (payroll_slip_id),
    INDEX idx_sar_date (recovery_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Add advance_deduction to payroll_slips if not exists
ALTER TABLE payroll_slips ADD COLUMN advance_deduction DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER loan_deduction;

-- 4. Add total_advance_deductions to payroll_runs if not exists
ALTER TABLE payroll_runs ADD COLUMN total_advance_deductions DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER total_gross;

-- 5. Add Damith (1659) and Kaveen (1660) Accounts under 1650 if missing
INSERT INTO chart_of_accounts (account_code, account_name, account_type, account_category, parent_id, balance, is_active)
SELECT '1659', 'Salary Advance - Damith', 'Asset', 'Current Asset', id, 0.00, 1
FROM chart_of_accounts 
WHERE account_code = '1650'
  AND NOT EXISTS (SELECT 1 FROM chart_of_accounts WHERE account_code = '1659')
LIMIT 1;

INSERT INTO chart_of_accounts (account_code, account_name, account_type, account_category, parent_id, balance, is_active)
SELECT '1660', 'Salary Advance - Kaveen', 'Asset', 'Current Asset', id, 0.00, 1
FROM chart_of_accounts 
WHERE account_code = '1650'
  AND NOT EXISTS (SELECT 1 FROM chart_of_accounts WHERE account_code = '1660')
LIMIT 1;