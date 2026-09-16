CREATE TABLE IF NOT EXISTS petty_cash_reimbursements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reimbursement_date DATE NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    bank_account_id INT NULL,
    payment_method VARCHAR(50) DEFAULT 'Bank Transfer',
    cheque_number VARCHAR(100) NULL,
    cheque_date DATE NULL,
    payee_name VARCHAR(255) NULL,
    status ENUM('Pending', 'Approved', 'Disbursed', 'Rejected') DEFAULT 'Pending',
    description TEXT NULL,
    created_by INT NOT NULL,
    approved_by INT NULL,
    approved_at DATETIME NULL,
    disbursed_at DATETIME NULL,
    journal_entry_id INT NULL,
    cheque_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE petty_cash_reimbursements ADD COLUMN IF NOT EXISTS payment_method VARCHAR(50) DEFAULT 'Bank Transfer' AFTER bank_account_id;
ALTER TABLE petty_cash_reimbursements ADD COLUMN IF NOT EXISTS cheque_number VARCHAR(100) NULL AFTER payment_method;
ALTER TABLE petty_cash_reimbursements ADD COLUMN IF NOT EXISTS cheque_date DATE NULL AFTER cheque_number;
ALTER TABLE petty_cash_reimbursements ADD COLUMN IF NOT EXISTS payee_name VARCHAR(255) NULL AFTER cheque_date;
ALTER TABLE petty_cash_reimbursements ADD COLUMN IF NOT EXISTS cheque_id INT NULL AFTER journal_entry_id;
ALTER TABLE petty_cash_transactions ADD COLUMN IF NOT EXISTS reimbursement_id INT NULL AFTER voucher_number;
