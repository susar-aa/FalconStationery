<?php
require_once __DIR__ . '/AuditLog.php';

class BankReconciliation {
    private $db;
    private $auditLog;

    public function __construct() {
        $this->db = new Database();
        $this->auditLog = new AuditLog();
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
                INDEX idx_status (status),
                INDEX idx_acc_status_date (account_id, status, statement_date_end)
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
                                OR account_code LIKE '16%'
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
                          ORDER BY r.statement_date_end DESC, r.id DESC");
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

    /**
     * Determine next opening balance for a specific bank account.
     * Uses previous month's confirmed (Finalized) closing balance, or initial setup balance.
     */
    public function getNextOpeningBalance($accountId, $targetStartDate = null) {
        $accountId = intval($accountId);

        // 1. Look for the latest Finalized reconciliation for this bank account
        $sql = "SELECT id, reconciliation_number, statement_date_start, statement_date_end, 
                       statement_closing_balance, status 
                FROM bank_reconciliations 
                WHERE account_id = :account_id AND status = 'Finalized'";
        if (!empty($targetStartDate)) {
            $sql .= " AND statement_date_end < :target_start";
        }
        $sql .= " ORDER BY statement_date_end DESC, id DESC LIMIT 1";

        $this->db->query($sql);
        $this->db->bind(':account_id', $accountId);
        if (!empty($targetStartDate)) {
            $this->db->bind(':target_start', $targetStartDate);
        }
        $lastFinalized = $this->db->single();

        if ($lastFinalized) {
            $nextStart = date('Y-m-d', strtotime($lastFinalized->statement_date_end . ' +1 day'));
            $nextEnd = date('Y-m-t', strtotime($nextStart));
            return [
                'has_finalized' => true,
                'opening_balance' => floatval($lastFinalized->statement_closing_balance),
                'suggested_start' => $nextStart,
                'suggested_end' => $nextEnd,
                'prev_rec_id' => $lastFinalized->id,
                'prev_rec_number' => $lastFinalized->reconciliation_number,
                'prev_end_date' => $lastFinalized->statement_date_end,
                'source' => 'previous_finalized',
                'message' => "Opening balance automatically carried forward from confirmed closing balance of " . $lastFinalized->reconciliation_number . " (LKR " . number_format($lastFinalized->statement_closing_balance, 2) . ")."
            ];
        }

        // 2. Check if an unfinalized Draft exists
        $this->db->query("SELECT id, reconciliation_number, statement_date_start, statement_date_end 
                          FROM bank_reconciliations 
                          WHERE account_id = :account_id AND status = 'Draft' 
                          ORDER BY statement_date_end DESC LIMIT 1");
        $this->db->bind(':account_id', $accountId);
        $draft = $this->db->single();

        // 3. Fallback to registered bank account initial opening balance
        $this->db->query("SELECT opening_balance, opening_balance_date FROM bank_accounts WHERE chart_of_account_id = :account_id LIMIT 1");
        $this->db->bind(':account_id', $accountId);
        $ba = $this->db->single();

        $initOb = $ba ? floatval($ba->opening_balance ?? 0) : 0.00;
        $startDate = ($ba && !empty($ba->opening_balance_date)) ? date('Y-m-d', strtotime($ba->opening_balance_date . ' +1 day')) : date('Y-m-01');
        $endDate = date('Y-m-t', strtotime($startDate));

        $draftWarning = null;
        if ($draft) {
            $draftWarning = "Previous reconciliation draft ({$draft->reconciliation_number} for period {$draft->statement_date_start} to {$draft->statement_date_end}) is not yet finalized. Finalize it to lock confirmed balances.";
        }

        return [
            'has_finalized' => false,
            'has_draft' => !empty($draft),
            'draft_warning' => $draftWarning,
            'opening_balance' => $initOb,
            'suggested_start' => $startDate,
            'suggested_end' => $endDate,
            'source' => 'initial_setup',
            'is_first' => true,
            'message' => "Initial opening balance from registered bank account setup (LKR " . number_format($initOb, 2) . ")."
        ];
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
        $accountId = intval($data['account_id']);
        $startDate = $data['statement_date_start'];
        $endDate = $data['statement_date_end'];

        // Automatically verify/retrieve opening balance from previous finalized month
        $info = $this->getNextOpeningBalance($accountId, $startDate);
        $stmtOpen = isset($data['statement_opening_balance']) ? floatval($data['statement_opening_balance']) : $info['opening_balance'];

        // Generate clean unique reconciliation reference
        $reconciliationNumber = 'BREC-' . date('Ymd', strtotime($endDate)) . '-' . rand(1000, 9999);

        $this->db->query("INSERT INTO bank_reconciliations 
            (reconciliation_number, account_id, statement_date_start, statement_date_end, 
             statement_opening_balance, statement_closing_balance, book_opening_balance, 
             book_closing_balance, adjusted_bank_balance, adjusted_book_balance, difference, status) 
            VALUES (:rec_num, :account_id, :start_date, :end_date, 
                    :stmt_open, :stmt_close, :book_open, 
                    :book_close, :adj_bank, :adj_book, :diff, 'Draft')");
        
        $this->db->bind(':rec_num', $reconciliationNumber);
        $this->db->bind(':account_id', $accountId);
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);
        $this->db->bind(':stmt_open', $stmtOpen);
        $this->db->bind(':stmt_close', floatval($data['statement_closing_balance'] ?? 0));
        $this->db->bind(':book_open', $stmtOpen);
        $this->db->bind(':book_close', $stmtOpen);
        $this->db->bind(':adj_bank', floatval($data['statement_closing_balance'] ?? 0));
        $this->db->bind(':adj_book', $stmtOpen);
        $this->db->bind(':diff', round(floatval($data['statement_closing_balance'] ?? 0) - $stmtOpen, 2));

        if ($this->db->execute()) {
            $recId = $this->db->lastInsertId();
            $this->populateReconciliationItems($recId, $accountId, $endDate);
            $this->calculateBalances($recId);
            return $recId;
        }
        return false;
    }

    /**
     * Populate items into workspace.
     * Brings in all unreconciled transactions up to $endDate, automatically carrying forward
     * un-cleared transactions from prior periods without altering or duplicating them.
     */
    public function populateReconciliationItems($recId, $accountId, $endDate) {
        $rec = $this->getReconciliationById($recId);
        if (!$rec || $rec->status === 'Finalized') {
            return; // Locked - do not alter items of a finalized reconciliation
        }

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

    /**
     * Get all items for reconciliation workspace.
     * Flags carried-forward items originating before the current statement period start.
     */
    public function getItems($recId) {
        $rec = $this->getReconciliationById($recId);
        $startDate = $rec ? $rec->statement_date_start : '1970-01-01';

        $this->db->query("SELECT bri.*, 
                                 IF(bri.transaction_date < :start_date, 1, 0) as is_carried_forward 
                          FROM bank_reconciliation_items bri 
                          WHERE bri.reconciliation_id = :rec_id 
                          ORDER BY bri.transaction_date ASC, bri.id ASC");
        $this->db->bind(':start_date', $startDate);
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
                return $this->calculateBalances($item->reconciliation_id);
            }
        }
        return false;
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

    /**
     * Exact Bank Reconciliation Mathematical Calculation.
     * - Opening balance represents previous month's confirmed closing balance.
     * - Ticking or unticking transactions NEVER alters the opening balance.
     * - Cleared Balance = Opening Balance + Matched Inflows (Dr) - Matched Outflows (Cr).
     * - Difference = Statement Closing Balance - Cleared Balance.
     * - Adjusted Bank = Statement Closing + Deposits in Transit (Unmatched Dr) - Outstanding Cheques (Unmatched Cr).
     * - Adjusted Book = Opening Balance + Total Book Debits - Total Book Credits.
     * When all transactions on the bank statement are matched, Difference is 0.00.
     */
    public function calculateBalances($recId) {
        $rec = $this->getReconciliationById($recId);
        if (!$rec) return false;

        $items = $this->getItems($recId);

        $matchedDeposits = 0.0;
        $matchedPayments = 0.0;
        $depositsInTransit = 0.0;
        $outstandingCheques = 0.0;

        foreach ($items as $item) {
            $dr = floatval($item->debit ?? 0);
            $cr = floatval($item->credit ?? 0);

            if ($item->is_matched) {
                if ($dr > 0) $matchedDeposits += $dr;
                if ($cr > 0) $matchedPayments += $cr;
            } else {
                if ($dr > 0) $depositsInTransit += $dr;
                if ($cr > 0) $outstandingCheques += $cr;
            }
        }

        // Statement Opening Balance is fixed and confirmed from prior month
        $stmtOpening = floatval($rec->statement_opening_balance);
        $stmtClosing = floatval($rec->statement_closing_balance);

        // Cleared Bank Balance represents the transactions confirmed to have cleared
        $clearedBalance = round($stmtOpening + $matchedDeposits - $matchedPayments, 2);

        // Difference: Difference between target Statement Ending Balance and current Cleared Balance
        $difference = round($stmtClosing - $clearedBalance, 2);

        // Adjusted Bank Balance: Bank statement closing balance adjusted for items in transit/outstanding
        $adjustedBank = round($stmtClosing + $depositsInTransit - $outstandingCheques, 2);

        // Adjusted Book Balance: Opening balance + total book debits - total book credits
        $totalBookDebits = $matchedDeposits + $depositsInTransit;
        $totalBookCredits = $matchedPayments + $outstandingCheques;
        $adjustedBook = round($stmtOpening + $totalBookDebits - $totalBookCredits, 2);

        $this->db->query("UPDATE bank_reconciliations 
                          SET book_opening_balance = :book_open, 
                              book_closing_balance = :book_close, 
                              adjusted_bank_balance = :adj_bank, 
                              adjusted_book_balance = :adj_book, 
                              difference = :diff 
                          WHERE id = :id");
        $this->db->bind(':book_open', $stmtOpening);
        $this->db->bind(':book_close', $adjustedBook);
        $this->db->bind(':adj_bank', $adjustedBank);
        $this->db->bind(':adj_book', $adjustedBook);
        $this->db->bind(':diff', $difference);
        $this->db->bind(':id', $recId);
        $this->db->execute();

        return [
            'statement_opening' => $stmtOpening,
            'statement_closing' => $stmtClosing,
            'matched_deposits' => $matchedDeposits,
            'matched_payments' => $matchedPayments,
            'cleared_balance' => $clearedBalance,
            'deposits_in_transit' => $depositsInTransit,
            'outstanding_cheques' => $outstandingCheques,
            'adjusted_bank' => $adjustedBank,
            'adjusted_book' => $adjustedBook,
            'difference' => $difference
        ];
    }

    /**
     * Finalize reconciliation:
     * - Locks the reconciliation.
     * - Sets matched transactions as is_reconciled = 1.
     * - Leaves unmatched transactions as is_reconciled = 0 (carry forward to next month).
     * - Automatically propagates closing balance to any existing future draft reconciliation.
     * - Logs Audit Log entry.
     */
    public function finalizeReconciliation($recId, $userId) {
        $rec = $this->getReconciliationById($recId);
        if (!$rec || $rec->status === 'Finalized') {
            return ['status' => false, 'message' => 'Reconciliation already finalized or invalid.'];
        }

        $calc = $this->calculateBalances($recId);
        $rec = $this->getReconciliationById($recId);

        if (abs(floatval($rec->difference)) > 0.009) {
            return ['status' => false, 'message' => 'Cannot finalize. Unreconciled difference must be exactly zero (Current difference: LKR ' . number_format($rec->difference, 2) . ').'];
        }

        try {
            $this->db->beginTransaction();

            // 1. Lock reconciliation header
            $this->db->query("UPDATE bank_reconciliations 
                              SET status = 'Finalized', reconciled_by = :user, reconciled_at = NOW() 
                              WHERE id = :id");
            $this->db->bind(':user', $userId);
            $this->db->bind(':id', $recId);
            $this->db->execute();

            // 2. Lock matched transactions in transactions table
            $items = $this->getItems($recId);
            $matchedTxIds = [];
            foreach ($items as $item) {
                if ($item->is_matched && $item->source_table === 'transactions' && $item->source_id) {
                    $matchedTxIds[] = intval($item->source_id);
                }
            }

            if (!empty($matchedTxIds)) {
                // Batch update matched transactions
                $inList = implode(',', $matchedTxIds);
                $this->db->query("UPDATE transactions 
                                  SET is_reconciled = 1, reconciliation_id = :rec_id 
                                  WHERE id IN ($inList)");
                $this->db->bind(':rec_id', $recId);
                $this->db->execute();
            }

            // 3. Automatically propagate closing balance to any existing subsequent DRAFT reconciliation for this bank
            $this->db->query("SELECT id FROM bank_reconciliations 
                              WHERE account_id = :account_id 
                                AND status = 'Draft' 
                                AND statement_date_start >= :stmt_end 
                              ORDER BY statement_date_start ASC LIMIT 1");
            $this->db->bind(':account_id', $rec->account_id);
            $this->db->bind(':stmt_end', $rec->statement_date_end);
            $nextDraft = $this->db->single();

            if ($nextDraft) {
                $this->db->query("UPDATE bank_reconciliations 
                                  SET statement_opening_balance = :new_open 
                                  WHERE id = :next_id");
                $this->db->bind(':new_open', $rec->statement_closing_balance);
                $this->db->bind(':next_id', $nextDraft->id);
                $this->db->execute();

                // Recalculate the next draft's balances
                $this->calculateBalances($nextDraft->id);
            }

            // 4. Log audit trail
            $desc = "Finalized Bank Reconciliation #{$rec->reconciliation_number} for {$rec->account_code} - {$rec->account_name} (Period: {$rec->statement_date_start} to {$rec->statement_date_end}). Closing Balance: LKR " . number_format($rec->statement_closing_balance, 2);
            $this->auditLog->logAction($userId, 'Finalize Bank Reconciliation', 'BankReconciliation', $desc, $recId);

            $this->db->commit();
            return ['status' => true, 'message' => 'Bank reconciliation finalized and locked successfully!'];

        } catch (Throwable $e) {
            $this->db->rollBack();
            error_log("finalizeReconciliation error: " . $e->getMessage());
            return ['status' => false, 'message' => 'Database error during finalization: ' . $e->getMessage()];
        }
    }

    /**
     * Reopen a finalized reconciliation safely.
     * Prevents reopening if a subsequent month has already been finalized.
     * Unlocks transactions and recalculates balances.
     */
    public function reopenReconciliation($recId, $userId) {
        $rec = $this->getReconciliationById($recId);
        if (!$rec) {
            return ['status' => false, 'message' => 'Reconciliation not found.'];
        }
        if ($rec->status !== 'Finalized') {
            return ['status' => false, 'message' => 'Reconciliation is not finalized.'];
        }

        // Safety Check: Verify that no subsequent month for this bank account is finalized
        $this->db->query("SELECT id, reconciliation_number, statement_date_start, statement_date_end 
                          FROM bank_reconciliations 
                          WHERE account_id = :account_id 
                            AND status = 'Finalized' 
                            AND statement_date_start > :stmt_start 
                          ORDER BY statement_date_start ASC LIMIT 1");
        $this->db->bind(':account_id', $rec->account_id);
        $this->db->bind(':stmt_start', $rec->statement_date_start);
        $subsequent = $this->db->single();

        if ($subsequent) {
            return [
                'status' => false, 
                'message' => "Cannot reopen this reconciliation. A subsequent reconciliation ({$subsequent->reconciliation_number} for period {$subsequent->statement_date_start} to {$subsequent->statement_date_end}) is already finalized. Please reopen subsequent periods first in reverse chronological order."
            ];
        }

        try {
            $this->db->beginTransaction();

            // 1. Unlock transactions linked to this reconciliation
            $this->db->query("UPDATE transactions 
                              SET is_reconciled = 0, reconciliation_id = NULL 
                              WHERE reconciliation_id = :rec_id");
            $this->db->bind(':rec_id', $recId);
            $this->db->execute();

            // 2. Change status to Draft
            $this->db->query("UPDATE bank_reconciliations 
                              SET status = 'Draft', reconciled_by = NULL, reconciled_at = NULL 
                              WHERE id = :id");
            $this->db->bind(':id', $recId);
            $this->db->execute();

            // 3. Recalculate balances
            $this->calculateBalances($recId);

            // 4. Audit Log
            $desc = "Reopened finalized Bank Reconciliation #{$rec->reconciliation_number} for {$rec->account_code} - {$rec->account_name} back to Draft mode.";
            $this->auditLog->logAction($userId, 'Reopen Bank Reconciliation', 'BankReconciliation', $desc, $recId);

            $this->db->commit();
            return ['status' => true, 'message' => 'Reconciliation successfully reopened for editing.'];

        } catch (Throwable $e) {
            $this->db->rollBack();
            error_log("reopenReconciliation error: " . $e->getMessage());
            return ['status' => false, 'message' => 'Database error during reopen: ' . $e->getMessage()];
        }
    }

    public function deleteReconciliation($id, $userId = null) {
        $rec = $this->getReconciliationById($id);
        if (!$rec) {
            return ['status' => false, 'message' => 'Reconciliation record not found.'];
        }

        if ($rec->status === 'Finalized') {
            return ['status' => false, 'message' => 'Finalized reconciliations cannot be deleted directly. Reopen first if required.'];
        }

        try {
            $this->db->beginTransaction();

            // 1. Delete transactions created by BANK_ONLY adjustment entries
            $this->db->query("DELETE t FROM transactions t 
                              INNER JOIN bank_reconciliation_items bri ON t.journal_entry_id = bri.journal_entry_id 
                              WHERE bri.reconciliation_id = :rec_id AND bri.transaction_type = 'BANK_ONLY'");
            $this->db->bind(':rec_id', $id);
            $this->db->execute();

            // 2. Delete journal entries created for BANK_ONLY items
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

            // 6. Audit Log
            $desc = "Deleted Bank Reconciliation draft #{$rec->reconciliation_number} for {$rec->account_code} - {$rec->account_name}";
            $this->auditLog->logAction($userId ?: ($_SESSION['user_id'] ?? 1), 'Delete Bank Reconciliation Draft', 'BankReconciliation', $desc, $id);

            $this->db->commit();
            return ['status' => true, 'message' => 'Bank reconciliation draft deleted successfully.'];

        } catch (Throwable $e) {
            $this->db->rollBack();
            error_log("deleteReconciliation error: " . $e->getMessage());
            return ['status' => false, 'message' => 'Database error while deleting reconciliation: ' . $e->getMessage()];
        }
    }
}
