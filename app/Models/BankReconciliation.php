<?php
class BankReconciliation {
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->ensureTablesExist();
    }

    private function ensureTablesExist() {
        try {
            // Main reconciliation header table
            $sql1 = "CREATE TABLE IF NOT EXISTS bank_reconciliations (
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $this->db->query($sql1);
            $this->db->execute();

            // Reconciliation items / matching table
            $sql2 = "CREATE TABLE IF NOT EXISTS bank_reconciliation_items (
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $this->db->query($sql2);
            $this->db->execute();

            // Add is_reconciled & reconciliation_id columns to transactions table if missing
            $this->db->query("SHOW COLUMNS FROM transactions LIKE 'is_reconciled'");
            if (!$this->db->single()) {
                try {
                    $this->db->query("ALTER TABLE transactions ADD COLUMN is_reconciled TINYINT(1) DEFAULT 0");
                    $this->db->execute();
                } catch (Throwable $ex) {}
            }

            $this->db->query("SHOW COLUMNS FROM transactions LIKE 'reconciliation_id'");
            if (!$this->db->single()) {
                try {
                    $this->db->query("ALTER TABLE transactions ADD COLUMN reconciliation_id INT NULL");
                    $this->db->execute();
                } catch (Throwable $ex) {}
            }

        } catch (Throwable $e) {
            error_log("BankReconciliation ensureTablesExist error: " . $e->getMessage());
        }
    }

    public function getBankAccounts() {
        $this->db->query("SELECT id, account_code, account_name, account_type, account_category 
                          FROM chart_of_accounts 
                          WHERE is_active = 1 
                            AND (
                                account_code LIKE '10%' 
                                OR LOWER(account_name) LIKE '%bank%' 
                                OR LOWER(account_category) LIKE '%bank%'
                            )
                          ORDER BY account_code ASC");
        return $this->db->resultSet();
    }

    public function getAllReconciliations() {
        $this->db->query("SELECT r.*, c.account_code, c.account_name, u.username as reconciler_name 
                          FROM bank_reconciliations r 
                          JOIN chart_of_accounts c ON r.account_id = c.id 
                          LEFT JOIN users u ON r.reconciled_by = u.id 
                          ORDER BY r.created_at DESC");
        return $this->db->resultSet();
    }

    public function getReconciliationById($id) {
        $this->db->query("SELECT r.*, c.account_code, c.account_name 
                          FROM bank_reconciliations r 
                          JOIN chart_of_accounts c ON r.account_id = c.id 
                          WHERE r.id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getBookBalance($accountId, $endDate) {
        $this->db->query("SELECT COALESCE(SUM(t.debit - t.credit), 0) as balance 
                          FROM transactions t 
                          JOIN journal_entries je ON t.journal_entry_id = je.id 
                          WHERE t.account_id = :account_id 
                            AND je.entry_date <= :end_date 
                            AND (je.status IS NULL OR je.status != 'Voided')");
        $this->db->bind(':account_id', $accountId);
        $this->db->bind(':end_date', $endDate);
        $row = $this->db->single();
        return floatval($row->balance ?? 0);
    }

    public function createDraft($data) {
        $reconciliationNumber = 'BREC-' . date('Ymd') . '-' . rand(1000, 9999);
        
        $bookOpening = $this->getBookBalance($data['account_id'], date('Y-m-d', strtotime($data['statement_date_start'] . ' -1 day')));
        $bookClosing = $this->getBookBalance($data['account_id'], $data['statement_date_end']);

        $this->db->query("INSERT INTO bank_reconciliations 
            (reconciliation_number, account_id, statement_date_start, statement_date_end, statement_opening_balance, statement_closing_balance, book_opening_balance, book_closing_balance, status) 
            VALUES (:rec_num, :account_id, :start_date, :end_date, :stmt_open, :stmt_close, :book_open, :book_close, 'Draft')");
        
        $this->db->bind(':rec_num', $reconciliationNumber);
        $this->db->bind(':account_id', $data['account_id']);
        $this->db->bind(':start_date', $data['statement_date_start']);
        $this->db->bind(':end_date', $data['statement_date_end']);
        $this->db->bind(':stmt_open', $data['statement_opening_balance']);
        $this->db->bind(':stmt_close', $data['statement_closing_balance']);
        $this->db->bind(':book_open', $bookOpening);
        $this->db->bind(':book_close', $bookClosing);

        if ($this->db->execute()) {
            $recId = $this->db->lastInsertId();
            $this->populateReconciliationItems($recId, $data['account_id'], $data['statement_date_end']);
            $this->calculateBalances($recId);
            return $recId;
        }
        return false;
    }

    public function populateReconciliationItems($recId, $accountId, $endDate) {
        $sql = "INSERT INTO bank_reconciliation_items 
                    (reconciliation_id, transaction_type, source_table, source_id, journal_entry_id, transaction_date, reference, description, debit, credit, is_matched)
                SELECT 
                    :rec_id,
                    'ERP_TRANSACTION',
                    'transactions',
                    t.id,
                    t.journal_entry_id,
                    je.entry_date,
                    COALESCE(je.reference, ''),
                    COALESCE(t.description, je.description, ''),
                    t.debit,
                    t.credit,
                    0
                FROM transactions t
                JOIN journal_entries je ON t.journal_entry_id = je.id
                LEFT JOIN bank_reconciliation_items bri 
                    ON bri.reconciliation_id = :rec_id_dup 
                   AND bri.source_table = 'transactions' 
                   AND bri.source_id = t.id
                WHERE t.account_id = :account_id
                  AND je.entry_date <= :end_date
                  AND (je.status IS NULL OR je.status != 'Voided')
                  AND (t.is_reconciled = 0 OR t.reconciliation_id = :rec_id_dup2)
                  AND bri.id IS NULL";

        $this->db->query($sql);
        $this->db->bind(':rec_id', $recId);
        $this->db->bind(':rec_id_dup', $recId);
        $this->db->bind(':account_id', $accountId);
        $this->db->bind(':end_date', $endDate);
        $this->db->bind(':rec_id_dup2', $recId);
        $this->db->execute();
    }


    public function getItems($recId) {
        $this->db->query("SELECT * FROM bank_reconciliation_items WHERE reconciliation_id = :rec_id ORDER BY transaction_date ASC, id ASC");
        $this->db->bind(':rec_id', $recId);
        return $this->db->resultSet();
    }

    public function toggleMatchItem($itemId, $isMatched) {
        $matchedAt = $isMatched ? date('Y-m-d H:i:s') : null;
        $this->db->query("UPDATE bank_reconciliation_items SET is_matched = :is_matched, matched_at = :matched_at WHERE id = :id");
        $this->db->bind(':is_matched', $isMatched ? 1 : 0);
        $this->db->bind(':matched_at', $matchedAt);
        $this->db->bind(':id', $itemId);
        $res = $this->db->execute();

        if ($res) {
            $this->db->query("SELECT reconciliation_id FROM bank_reconciliation_items WHERE id = :id");
            $this->db->bind(':id', $itemId);
            $item = $this->db->single();
            if ($item) {
                $this->calculateBalances($item->reconciliation_id);
            }
        }
        return $res;
    }

    public function addBankOnlyTransaction($recId, $data) {
        $rec = $this->getReconciliationById($recId);
        if (!$rec || $rec->status === 'Finalized') {
            return false;
        }

        try {
            $this->db->beginTransaction();

            $date = $data['transaction_date'] ?? date('Y-m-d');
            $type = $data['type'];
            $amount = floatval($data['amount']);
            $oppAccountId = intval($data['opposite_account_id']);
            $ref = $data['reference'] ?? 'BANK-REC-ADJ';
            $desc = $data['description'] ?? 'Bank Reconciliation Adjustment';

            $this->db->query("INSERT INTO journal_entries (entry_date, reference, description, created_by, status) VALUES (:edate, :ref, :desc, :user, 'Posted')");
            $this->db->bind(':edate', $date);
            $this->db->bind(':ref', $ref);
            $this->db->bind(':desc', $desc);
            $this->db->bind(':user', $_SESSION['user_id'] ?? 1);
            $this->db->execute();
            $jeId = $this->db->lastInsertId();

            $bankDebit = 0; $bankCredit = 0;
            $oppDebit = 0; $oppCredit = 0;

            if ($type === 'INTEREST' || $type === 'DIRECT_DEPOSIT') {
                $bankDebit = $amount;
                $oppCredit = $amount;
            } else {
                $bankCredit = $amount;
                $oppDebit = $amount;
            }

            $this->db->query("INSERT INTO transactions (journal_entry_id, account_id, debit, credit, description, is_reconciled, reconciliation_id) VALUES (:je, :acc, :dr, :cr, :desc, 1, :rec_id)");
            $this->db->bind(':je', $jeId);
            $this->db->bind(':acc', $rec->account_id);
            $this->db->bind(':dr', $bankDebit);
            $this->db->bind(':cr', $bankCredit);
            $this->db->bind(':desc', $desc);
            $this->db->bind(':rec_id', $recId);
            $this->db->execute();
            $bankTxId = $this->db->lastInsertId();

            $this->db->query("INSERT INTO transactions (journal_entry_id, account_id, debit, credit, description) VALUES (:je, :acc, :dr, :cr, :desc)");
            $this->db->bind(':je', $jeId);
            $this->db->bind(':acc', $oppAccountId);
            $this->db->bind(':dr', $oppDebit);
            $this->db->bind(':cr', $oppCredit);
            $this->db->bind(':desc', $desc);
            $this->db->execute();

            $this->db->query("INSERT INTO bank_reconciliation_items 
                (reconciliation_id, transaction_type, source_table, source_id, journal_entry_id, transaction_date, reference, description, debit, credit, is_matched, matched_at, notes) 
                VALUES (:rec_id, 'BANK_ONLY', 'transactions', :src_id, :je_id, :tx_date, :ref, :desc, :dr, :cr, 1, NOW(), :notes)");
            $this->db->bind(':rec_id', $recId);
            $this->db->bind(':src_id', $bankTxId);
            $this->db->bind(':je_id', $jeId);
            $this->db->bind(':tx_date', $date);
            $this->db->bind(':ref', $ref);
            $this->db->bind(':desc', $desc);
            $this->db->bind(':dr', $bankDebit);
            $this->db->bind(':cr', $bankCredit);
            $this->db->bind(':notes', 'Created during reconciliation');
            $this->db->execute();

            $this->db->commit();
            $this->calculateBalances($recId);
            return true;

        } catch (Throwable $e) {
            $this->db->rollBack();
            error_log("addBankOnlyTransaction error: " . $e->getMessage());
            return false;
        }
    }

    public function calculateBalances($recId) {
        $rec = $this->getReconciliationById($recId);
        if (!$rec) return false;

        $items = $this->getItems($recId);

        $depositsInTransit = 0;
        $outstandingCheques = 0;

        foreach ($items as $item) {
            if (!$item->is_matched) {
                if ($item->debit > 0) {
                    $depositsInTransit += floatval($item->debit);
                }
                if ($item->credit > 0) {
                    $outstandingCheques += floatval($item->credit);
                }
            }
        }

        $stmtClosing = floatval($rec->statement_closing_balance);
        $adjustedBank = $stmtClosing + $depositsInTransit - $outstandingCheques;
        
        $bookClosing = $this->getBookBalance($rec->account_id, $rec->statement_date_end);
        $adjustedBook = $bookClosing;
        $difference = round($adjustedBank - $adjustedBook, 2);

        $this->db->query("UPDATE bank_reconciliations 
                          SET book_closing_balance = :book_close, 
                              adjusted_bank_balance = :adj_bank, 
                              adjusted_book_balance = :adj_book, 
                              difference = :diff 
                          WHERE id = :id");
        $this->db->bind(':book_close', $bookClosing);
        $this->db->bind(':adj_bank', $adjustedBank);
        $this->db->bind(':adj_book', $adjustedBook);
        $this->db->bind(':diff', $difference);
        $this->db->bind(':id', $recId);
        return $this->db->execute();
    }

    public function finalizeReconciliation($recId, $userId) {
        $rec = $this->getReconciliationById($recId);
        if (!$rec || $rec->status === 'Finalized') {
            return ['status' => false, 'message' => 'Reconciliation already finalized or invalid.'];
        }

        $this->calculateBalances($recId);
        $rec = $this->getReconciliationById($recId);

        if (abs(floatval($rec->difference)) > 0.009) {
            return ['status' => false, 'message' => 'Cannot finalize. Difference must be exactly zero. Current difference: ' . number_format($rec->difference, 2)];
        }

        try {
            $this->db->beginTransaction();

            $this->db->query("UPDATE bank_reconciliations SET status = 'Finalized', reconciled_by = :user, reconciled_at = NOW() WHERE id = :id");
            $this->db->bind(':user', $userId);
            $this->db->bind(':id', $recId);
            $this->db->execute();

            $items = $this->getItems($recId);
            foreach ($items as $item) {
                if ($item->is_matched && $item->source_table === 'transactions' && $item->source_id) {
                    $this->db->query("UPDATE transactions SET is_reconciled = 1, reconciliation_id = :rec_id WHERE id = :tx_id");
                    $this->db->bind(':rec_id', $recId);
                    $this->db->bind(':tx_id', $item->source_id);
                    $this->db->execute();
                }
            }

            $this->db->commit();
            return ['status' => true, 'message' => 'Bank reconciliation finalized and locked successfully!'];

        } catch (Throwable $e) {
            $this->db->rollBack();
            error_log("finalizeReconciliation error: " . $e->getMessage());
            return ['status' => false, 'message' => 'Database error during finalization: ' . $e->getMessage()];
        }
    }

    public function deleteReconciliation($id) {
        $rec = $this->getReconciliationById($id);
        if (!$rec) {
            return ['status' => false, 'message' => 'Reconciliation record not found.'];
        }

        if ($rec->status === 'Finalized') {
            return ['status' => false, 'message' => 'Completed reconciliations cannot be deleted.'];
        }

        try {
            $this->db->beginTransaction();

            // 1. Delete transactions created by BANK_ONLY adjustment entries (explicit JOIN)
            $this->db->query("DELETE t FROM transactions t 
                              INNER JOIN bank_reconciliation_items bri ON t.journal_entry_id = bri.journal_entry_id 
                              WHERE bri.reconciliation_id = :rec_id AND bri.transaction_type = 'BANK_ONLY'");
            $this->db->bind(':rec_id', $id);
            $this->db->execute();

            // 2. Delete journal entries created for BANK_ONLY items (explicit JOIN)
            $this->db->query("DELETE je FROM journal_entries je 
                              INNER JOIN bank_reconciliation_items bri ON je.id = bri.journal_entry_id 
                              WHERE bri.reconciliation_id = :rec_id AND bri.transaction_type = 'BANK_ONLY'");
            $this->db->bind(':rec_id', $id);
            $this->db->execute();

            // 3. Unlink any transactions linked to this reconciliation
            $this->db->query("UPDATE transactions SET is_reconciled = 0, reconciliation_id = NULL WHERE reconciliation_id = :rec_id");
            $this->db->bind(':rec_id', $id);
            $this->db->execute();

            // 4. Delete reconciliation items
            $this->db->query("DELETE FROM bank_reconciliation_items WHERE reconciliation_id = :rec_id");
            $this->db->bind(':rec_id', $id);
            $this->db->execute();

            // 5. Delete reconciliation header
            $this->db->query("DELETE FROM bank_reconciliations WHERE id = :rec_id AND status = 'Draft'");
            $this->db->bind(':rec_id', $id);
            $this->db->execute();

            $this->db->commit();
            return ['status' => true, 'message' => 'Bank reconciliation draft deleted successfully.'];

        } catch (Throwable $e) {
            $this->db->rollBack();
            error_log("deleteReconciliation error: " . $e->getMessage());
            return ['status' => false, 'message' => 'Database error while deleting reconciliation: ' . $e->getMessage()];
        }
    }
}

