-- 1. Create main bank reconciliations header table
CREATE TABLE IF NOT EXISTS bank_reconciliations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reconciliation_number VARCHAR(50) UNIQUE NOT NULL,
    account_id INT NOT NULL,
    statement_date_start DATE NOT NULL,
    statement_date_end DATE NOT NULL,
    statement_opening_balance DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    statement_closing_balance DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    book_opening_balance DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    book_closing_balance DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    adjusted_bank_balance DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    adjusted_book_balance DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    difference DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    status ENUM('Draft', 'Finalized') DEFAULT 'Draft',
    reconciled_by INT NULL,
    reconciled_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_account (account_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Create bank reconciliation items table
CREATE TABLE IF NOT EXISTS bank_reconciliation_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reconciliation_id INT NOT NULL,
    transaction_type ENUM('ERP_TRANSACTION', 'BANK_ONLY', 'ADJUSTMENT') DEFAULT 'ERP_TRANSACTION',
    source_table VARCHAR(50) NULL,
    source_id INT NULL,
    journal_entry_id INT NULL,
    transaction_date DATE NOT NULL,
    reference VARCHAR(100) NULL,
    description TEXT NULL,
    debit DECIMAL(15,2) DEFAULT 0.00,
    credit DECIMAL(15,2) DEFAULT 0.00,
    is_matched TINYINT(1) DEFAULT 0,
    matched_at DATETIME NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reconciliation_id) REFERENCES bank_reconciliations(id) ON DELETE CASCADE,
    INDEX idx_rec (reconciliation_id),
    INDEX idx_source (source_table, source_id),
    INDEX idx_matched (is_matched)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Add reconciliation columns to transactions table
ALTER TABLE transactions 
ADD COLUMN IF NOT EXISTS is_reconciled TINYINT(1) DEFAULT 0, 
ADD COLUMN IF NOT EXISTS reconciliation_id INT NULL;
