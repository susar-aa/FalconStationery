<?php
declare(strict_types=1);

require_once __DIR__ . '/JournalEntry.php';
require_once __DIR__ . '/AuditLog.php';
require_once __DIR__ . '/PettyCashTransaction.php';

class PettyCashReimbursement {
    private Database $db;
    private AuditLog $audit;
    private JournalEntry $journal;
    private PettyCashTransaction $pcTx;

    public function __construct() {
        $this->db = new Database();
        $this->audit = new AuditLog();
        $this->journal = new JournalEntry();
        $this->pcTx = new PettyCashTransaction();
        $this->ensureTableSchema();
    }

    public function ensureTableSchema(): void {
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS petty_cash_reimbursements (
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $this->db->execute();

            $cols = [
                'payment_method' => "ALTER TABLE petty_cash_reimbursements ADD COLUMN payment_method VARCHAR(50) DEFAULT 'Bank Transfer' AFTER bank_account_id",
                'cheque_number' => "ALTER TABLE petty_cash_reimbursements ADD COLUMN cheque_number VARCHAR(100) NULL AFTER payment_method",
                'cheque_date' => "ALTER TABLE petty_cash_reimbursements ADD COLUMN cheque_date DATE NULL AFTER cheque_number",
                'payee_name' => "ALTER TABLE petty_cash_reimbursements ADD COLUMN payee_name VARCHAR(255) NULL AFTER cheque_date",
                'cheque_id' => "ALTER TABLE petty_cash_reimbursements ADD COLUMN cheque_id INT NULL AFTER journal_entry_id"
            ];
            foreach ($cols as $col => $sql) {
                $this->db->query("SHOW COLUMNS FROM petty_cash_reimbursements LIKE '{$col}'");
                if (!$this->db->single()) {
                    $this->db->query($sql);
                    $this->db->execute();
                }
            }

            $this->db->query("SHOW COLUMNS FROM petty_cash_transactions LIKE 'reimbursement_id'");
            if (!$this->db->single()) {
                $this->db->query("ALTER TABLE petty_cash_transactions ADD COLUMN reimbursement_id INT NULL AFTER voucher_number");
                $this->db->execute();
            }
        } catch (Throwable $e) {
            // Ignore if schema checks fail or already present
        }
    }

    /**
     * Get pending expenses (approved/unreimbursed expenses)
     */
    public function getPendingExpenses(): array {
        $this->db->query("SELECT t.*, u.username as creator_name, a.account_name as offset_account_name, a.account_code as offset_account_code
                          FROM petty_cash_transactions t
                          LEFT JOIN users u ON t.created_by = u.id
                          LEFT JOIN chart_of_accounts a ON t.account_id = a.id
                          WHERE t.type = 'expense' AND (t.status = 'Approved' OR t.status = 'Pending' OR t.status IS NULL OR t.status = '') AND t.reimbursement_id IS NULL
                          ORDER BY t.transaction_date ASC, t.id ASC");
        return $this->db->resultSet() ?: [];
    }

    /**
     * Get all reimbursement requests
     */
    public function getAllReimbursements(): array {
        $this->db->query("SELECT r.*, u.username as creator_name, app.username as approver_name, a.account_name as bank_account_name, a.account_code as bank_account_code
                          FROM petty_cash_reimbursements r
                          LEFT JOIN users u ON r.created_by = u.id
                          LEFT JOIN users app ON r.approved_by = app.id
                          LEFT JOIN chart_of_accounts a ON r.bank_account_id = a.id
                          ORDER BY r.reimbursement_date DESC, r.id DESC");
        return $this->db->resultSet() ?: [];
    }

    public function getReimbursementById(int $id): ?stdClass {
        $this->db->query("SELECT r.*, u.username as creator_name, app.username as approver_name, a.account_name as bank_account_name, a.account_code as bank_account_code
                          FROM petty_cash_reimbursements r
                          LEFT JOIN users u ON r.created_by = u.id
                          LEFT JOIN users app ON r.approved_by = app.id
                          LEFT JOIN chart_of_accounts a ON r.bank_account_id = a.id
                          WHERE r.id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single() ?: null;
    }

    /**
     * Get all expenses linked to a specific reimbursement
     */
    public function getLinkedExpenses(int $reimbursementId): array {
        $this->db->query("SELECT t.*, u.username as creator_name, a.account_name as offset_account_name, a.account_code as offset_account_code
                          FROM petty_cash_transactions t
                          LEFT JOIN users u ON t.created_by = u.id
                          LEFT JOIN chart_of_accounts a ON t.account_id = a.id
                          WHERE t.reimbursement_id = :rid AND t.type = 'expense'
                          ORDER BY t.transaction_date ASC");
        $this->db->bind(':rid', $reimbursementId);
        return $this->db->resultSet() ?: [];
    }

    /**
     * Create a new reimbursement request
     */
    public function getCashInHandAccountId(): int {
        $this->db->query("SELECT id FROM chart_of_accounts WHERE account_code = '1100' OR account_name LIKE '%Cash in Hand%' OR account_name LIKE '%Cash In Hand%' LIMIT 1");
        $row = $this->db->single();
        if ($row) {
            return (int)$row->id;
        }
        $this->db->query("INSERT INTO chart_of_accounts (account_code, account_name, account_type, account_category, balance, is_active) 
                          VALUES ('1100', 'Cash in Hand', 'Asset', 'Current Asset', 0.00, 1)");
        $this->db->execute();
        return (int)$this->db->lastInsertId();
    }

    public function getOutstandingChequesAccountId(): int {
        $this->db->query("SELECT id FROM chart_of_accounts WHERE account_code = '2050' LIMIT 1");
        $row = $this->db->single();
        if ($row) {
            return (int)$row->id;
        }
        $this->db->query("INSERT INTO chart_of_accounts (account_code, account_name, account_type, account_category, balance, is_active) 
                          VALUES ('2050', 'Outstanding Cheques (Issued)', 'Liability', 'Current Liability', 0.00, 1)");
        $this->db->execute();
        return (int)$this->db->lastInsertId();
    }

    /**
     * Create a new reimbursement request
     */
    public function createRequest(array $data, int $userId): string|bool {
        try {
            // Get all approved unreimbursed expenses
            $pendingExpenses = $this->getPendingExpenses();
            if (empty($pendingExpenses)) {
                return "There are no approved, unreimbursed expenses to group into a reimbursement request.";
            }

            $totalAmount = 0.0;
            $expenseIds = [];
            foreach ($pendingExpenses as $exp) {
                $totalAmount += floatval($exp->amount);
                $expenseIds[] = (int)$exp->id;
            }

            $paymentMethod = !empty($data['payment_method']) ? trim($data['payment_method']) : 'Bank Transfer';
            $date = $data['reimbursement_date'];
            $description = $data['description'] ?? 'Petty Cash Reimbursement Request';
            $chequeNumber = !empty($data['cheque_number']) ? trim($data['cheque_number']) : null;
            $chequeDate = !empty($data['cheque_date']) ? trim($data['cheque_date']) : null;
            $payeeName = !empty($data['payee_name']) ? trim($data['payee_name']) : null;

            if ($paymentMethod === 'Cash') {
                $bankAccountId = $this->getCashInHandAccountId();
            } else {
                $bankAccountId = intval($data['bank_account_id'] ?? 0);
                if ($bankAccountId <= 0) {
                    return "Please select a source bank account.";
                }
            }

            if ($paymentMethod === 'Cheque' && empty($chequeNumber)) {
                return "Please enter a valid cheque number for cheque reimbursement.";
            }

            $this->db->beginTransaction();

            // Insert reimbursement header
            $this->db->query("INSERT INTO petty_cash_reimbursements (reimbursement_date, amount, bank_account_id, payment_method, cheque_number, cheque_date, payee_name, status, description, created_by) 
                              VALUES (:date, :amount, :bank_id, :pmethod, :cnum, :cdate, :payee, 'Pending', :desc, :uid)");
            $this->db->bind(':date', $date);
            $this->db->bind(':amount', $totalAmount);
            $this->db->bind(':bank_id', $bankAccountId);
            $this->db->bind(':pmethod', $paymentMethod);
            $this->db->bind(':cnum', $chequeNumber);
            $this->db->bind(':cdate', $chequeDate);
            $this->db->bind(':payee', $payeeName);
            $this->db->bind(':desc', $description);
            $this->db->bind(':uid', $userId);
            $this->db->execute();

            $reimbursementId = intval($this->db->lastInsertId());

            // Link pending expenses to this reimbursement
            $this->db->query("UPDATE petty_cash_transactions 
                              SET reimbursement_id = :rid 
                              WHERE id IN (" . implode(',', $expenseIds) . ")");
            $this->db->bind(':rid', $reimbursementId);
            $this->db->execute();

            $this->db->commit();

            // Log action in audit trail
            $auditDesc = "Created Petty Cash reimbursement request ID {$reimbursementId} via {$paymentMethod} for amount Rs: " . number_format($totalAmount, 2) . ". Linked " . count($expenseIds) . " expenses.";
            $this->audit->logAction($userId, 'CREATE', 'accounting', $auditDesc, $reimbursementId, null, [
                'reimbursement_id' => $reimbursementId,
                'amount' => $totalAmount,
                'payment_method' => $paymentMethod,
                'bank_account_id' => $bankAccountId,
                'cheque_number' => $chequeNumber,
                'expenses' => $expenseIds
            ]);

            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return "Error: " . $e->getMessage();
        }
    }

    /**
     * Approve and disburse a reimbursement request
     */
    public function approveRequest(int $id, int $approverId): string|bool {
        try {
            $reim = $this->getReimbursementById($id);
            if (!$reim) {
                return "Reimbursement request not found.";
            }
            if ($reim->status !== 'Pending') {
                return "Only pending reimbursement requests can be approved.";
            }

            $date = $reim->reimbursement_date;
            $amount = floatval($reim->amount);
            $bankAccId = (int)$reim->bank_account_id;
            $paymentMethod = !empty($reim->payment_method) ? $reim->payment_method : 'Bank Transfer';
            $pettyAccId = $this->pcTx->getPettyCashAccountId();
            $baseRef = 'PC-REIM-' . str_pad((string)$reim->id, 5, '0', STR_PAD_LEFT);
            $ref = $baseRef;
            $suffix = 1;
            while (true) {
                $this->db->query("SELECT COUNT(*) as cnt FROM journal_entries WHERE reference = :ref");
                $this->db->bind(':ref', $ref);
                $chk = $this->db->single();
                if (!$chk || (int)$chk->cnt === 0) {
                    break;
                }
                $ref = $baseRef . '-' . $suffix;
                $suffix++;
            }

            // Check if period is closed/locked
            $this->db->query("SELECT COUNT(*) as cnt FROM financial_years WHERE :entry_date BETWEEN start_date AND end_date");
            $this->db->bind(':entry_date', $date);
            $res = $this->db->single();
            if ($res && $res->cnt > 0) {
                return "The accounting period containing date {$date} is closed.";
            }

            $creditAccountId = $bankAccId;
            $chequeId = null;

            if ($paymentMethod === 'Cash') {
                $desc = "Reimbursement Replenishment via Cash: " . $reim->description;
                $creditAccountId = $this->getCashInHandAccountId();
                $lines = [
                    ['account_id' => $pettyAccId, 'debit' => $amount, 'credit' => 0.0, 'description' => $desc],
                    ['account_id' => $creditAccountId, 'debit' => 0.0, 'credit' => $amount, 'description' => $desc]
                ];
            } elseif ($paymentMethod === 'Cheque') {
                $desc = "Reimbursement Replenishment via Cheque #" . ($reim->cheque_number ?: '') . ": " . $reim->description;
                $creditAccountId = $this->getOutstandingChequesAccountId();
                $lines = [
                    ['account_id' => $pettyAccId, 'debit' => $amount, 'credit' => 0.0, 'description' => $desc],
                    ['account_id' => $creditAccountId, 'debit' => 0.0, 'credit' => $amount, 'description' => $desc]
                ];
            } else { // Bank Transfer
                $desc = "Reimbursement Replenishment via Bank Transfer: " . $reim->description;
                $lines = [
                    ['account_id' => $pettyAccId, 'debit' => $amount, 'credit' => 0.0, 'description' => $desc],
                    ['account_id' => $creditAccountId, 'debit' => 0.0, 'credit' => $amount, 'description' => $desc]
                ];
            }

            $postResult = $this->journal->postEntry($date, $ref, $desc, $lines, $approverId);
            if ($postResult !== true) {
                return $postResult ?: "Failed to post journal entry.";
            }

            $this->db->query("SELECT id FROM journal_entries WHERE reference = :ref ORDER BY id DESC LIMIT 1");
            $this->db->bind(':ref', $ref);
            $jeRow = $this->db->single();
            $journalId = $jeRow ? intval($jeRow->id) : null;

            $this->db->beginTransaction();

            // If Cheque, record in cheques table so it appears in Cheque Management
            if ($paymentMethod === 'Cheque') {
                $payee = !empty($reim->payee_name) ? $reim->payee_name : 'Petty Cash Custodian';
                $bankingDate = !empty($reim->cheque_date) ? $reim->cheque_date : $date;
                $bankName = !empty($reim->bank_account_name) ? $reim->bank_account_name : 'Bank Account';
                $cNum = !empty($reim->cheque_number) ? $reim->cheque_number : $ref;

                $this->db->query("INSERT INTO cheques (payee_name, bank_name, cheque_number, amount, banking_date, status, bank_account_id, created_by) 
                                  VALUES (:payee, :bank, :cnum, :amt, :bdate, 'Pending', :bank_id, :uid)");
                $this->db->bind(':payee', $payee);
                $this->db->bind(':bank', $bankName);
                $this->db->bind(':cnum', $cNum);
                $this->db->bind(':amt', $amount);
                $this->db->bind(':bdate', $bankingDate);
                $this->db->bind(':bank_id', $bankAccId);
                $this->db->bind(':uid', $approverId);
                $this->db->execute();
                $chequeId = intval($this->db->lastInsertId());
            }

            // Update reimbursement status
            $this->db->query("UPDATE petty_cash_reimbursements 
                              SET status = 'Approved', approved_by = :approver_id, approved_at = NOW(), journal_entry_id = :jid, cheque_id = :cid 
                              WHERE id = :id");
            $this->db->bind(':approver_id', $approverId);
            $this->db->bind(':jid', $journalId);
            $this->db->bind(':cid', $chequeId);
            $this->db->bind(':id', $id);
            $this->db->execute();

            // Create a matching transaction record in petty_cash_transactions
            $this->db->query("INSERT INTO petty_cash_transactions (transaction_date, type, amount, reference, description, account_id, status, created_by, approved_by, approved_at, journal_entry_id, reimbursement_id) 
                              VALUES (:date, 'reimbursement', :amount, :ref, :desc, :acc_id, 'Approved', :approver_id, :approver_id, NOW(), :jid, :rid)");
            $this->db->bind(':date', $date);
            $this->db->bind(':amount', $amount);
            $this->db->bind(':ref', $ref);
            $this->db->bind(':desc', $desc);
            $this->db->bind(':acc_id', $creditAccountId);
            $this->db->bind(':approver_id', $approverId);
            $this->db->bind(':jid', $journalId);
            $this->db->bind(':rid', $id);
            $this->db->execute();

            $this->db->commit();

            // Log action in audit trail
            $auditDesc = "Approved and disbursed Petty Cash reimbursement request ID {$id} of Rs: " . number_format($amount, 2) . " via {$paymentMethod}. Ref: {$ref}";
            $this->audit->logAction($approverId, 'UPDATE', 'accounting', $auditDesc, $id, ['status' => 'Pending'], [
                'status' => 'Approved',
                'payment_method' => $paymentMethod,
                'journal_entry_id' => $journalId,
                'cheque_id' => $chequeId
            ]);

            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return "Error: " . $e->getMessage();
        }
    }

    /**
     * Reject a reimbursement request
     */
    public function rejectRequest(int $id, int $approverId): string|bool {
        try {
            $reim = $this->getReimbursementById($id);
            if (!$reim) {
                return "Reimbursement request not found.";
            }
            if ($reim->status !== 'Pending') {
                return "Only pending reimbursement requests can be rejected.";
            }

            $this->db->beginTransaction();

            // Update reimbursement status
            $this->db->query("UPDATE petty_cash_reimbursements 
                              SET status = 'Rejected', approved_by = :approver_id, approved_at = NOW() 
                              WHERE id = :id");
            $this->db->bind(':approver_id', $approverId);
            $this->db->bind(':id', $id);
            $this->db->execute();

            // Reset the reimbursement_id of all linked expenses so they can be grouped again later
            $this->db->query("UPDATE petty_cash_transactions 
                              SET reimbursement_id = NULL 
                              WHERE reimbursement_id = :rid AND type = 'expense'");
            $this->db->bind(':rid', $id);
            $this->db->execute();

            $this->db->commit();

            // Log action in audit trail
            $auditDesc = "Rejected Petty Cash reimbursement request ID {$id} and unlocked all linked expenses.";
            $this->audit->logAction($approverId, 'UPDATE', 'accounting', $auditDesc, $id, ['status' => 'Pending'], ['status' => 'Rejected']);

            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return "Error: " . $e->getMessage();
        }
    }
}
