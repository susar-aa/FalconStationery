<?php
declare(strict_types=1);

require_once __DIR__ . '/JournalEntry.php';
require_once __DIR__ . '/AuditLog.php';
require_once __DIR__ . '/PettyCashTransaction.php';

class Expense {
    private Database $db;
    private AuditLog $auditLog;

    public function __construct() {
        $this->db = new Database();
        $this->auditLog = new AuditLog();
    }

    public function getAllExpenses(array $filters = []): array {
        $sql = "SELECT e.*, v.name as vendor_name, coa.account_name as expense_account_name, coa.account_code as expense_account_code
                FROM expenses e 
                LEFT JOIN vendors v ON e.vendor_id = v.id 
                LEFT JOIN chart_of_accounts coa ON e.expense_account_id = coa.id
                WHERE 1=1";
        
        $binds = [];
        if (!empty($filters['search'])) {
            $sql .= " AND (e.reference LIKE :search OR e.description LIKE :search OR v.name LIKE :search)";
            $binds[':search'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['payment_method']) && $filters['payment_method'] !== 'All') {
            $sql .= " AND e.payment_method = :pmethod";
            $binds[':pmethod'] = $filters['payment_method'];
        }
        if (!empty($filters['date_from'])) {
            $sql .= " AND e.expense_date >= :date_from";
            $binds[':date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $sql .= " AND e.expense_date <= :date_to";
            $binds[':date_to'] = $filters['date_to'];
        }

        $sql .= " ORDER BY e.expense_date DESC, e.id DESC";

        $this->db->query($sql);
        foreach ($binds as $k => $v) {
            $this->db->bind($k, $v);
        }
        return $this->db->resultSet() ?: [];
    }

    public function getExpenseStats(): array {
        $this->db->query("
            SELECT 
                COUNT(*) as total_count,
                COALESCE(SUM(amount), 0) as total_amount,
                COALESCE(SUM(CASE WHEN payment_method = 'Petty Cash' THEN amount ELSE 0 END), 0) as petty_cash_total,
                COALESCE(SUM(CASE WHEN payment_method = 'Bank Transfer' THEN amount ELSE 0 END), 0) as bank_transfer_total,
                COALESCE(SUM(CASE WHEN payment_method = 'Cheque' THEN amount ELSE 0 END), 0) as cheque_total,
                COALESCE(SUM(CASE WHEN payment_method = 'Cash' THEN amount ELSE 0 END), 0) as cash_total,
                COALESCE(SUM(CASE WHEN payment_method = 'Credit' THEN amount ELSE 0 END), 0) as ap_credit_total
            FROM expenses
        ");
        $row = $this->db->single();
        return [
            'total_count' => intval($row->total_count ?? 0),
            'total_amount' => floatval($row->total_amount ?? 0),
            'petty_cash_total' => floatval($row->petty_cash_total ?? 0),
            'bank_transfer_total' => floatval($row->bank_transfer_total ?? 0),
            'cheque_total' => floatval($row->cheque_total ?? 0),
            'cash_total' => floatval($row->cash_total ?? 0),
            'ap_credit_total' => floatval($row->ap_credit_total ?? 0)
        ];
    }

    public function createExpenseWithAccounting(array $data, int $expenseAccountId, int $paymentAccountId, int $userId): bool|string {
        try {
            $amount = floatval($data['amount'] ?? 0);
            if ($amount <= 0) {
                return 'Amount must be greater than zero.';
            }

            $ref = trim($data['reference'] ?? '');
            if (empty($ref)) {
                $this->db->query("SELECT id FROM expenses ORDER BY id DESC LIMIT 1");
                $lastRow = $this->db->single();
                $nextId = $lastRow ? ($lastRow->id + 1) : 1;
                $ref = 'EXP-' . str_pad((string)$nextId, 5, '0', STR_PAD_LEFT);
            }

            $paymentMethod = $data['payment_method'] ?? 'Cash';
            $vendorId = !empty($data['vendor_id']) ? intval($data['vendor_id']) : null;
            $vendorName = 'Miscellaneous / N/A';

            if ($vendorId) {
                $this->db->query("SELECT name FROM vendors WHERE id = :vid");
                $this->db->bind(':vid', $vendorId);
                $vRow = $this->db->single();
                if ($vRow) $vendorName = $vRow->name;
            }

            $desc = "Expense [{$paymentMethod}]: " . $data['description'] . ($vendorId ? " (Vendor: {$vendorName})" : "");

            // Build Double Entry Lines
            $lines = [
                [
                    'account_id' => $expenseAccountId,
                    'debit' => $amount,
                    'credit' => 0,
                    'description' => $data['description'],
                    'entity_type' => $vendorId ? 'Vendor' : null,
                    'entity_id' => $vendorId
                ],
                [
                    'account_id' => $paymentAccountId,
                    'debit' => 0,
                    'credit' => $amount,
                    'description' => $data['description'],
                    'entity_type' => $vendorId ? 'Vendor' : null,
                    'entity_id' => $vendorId
                ]
            ];

            $journalModel = new JournalEntry();
            $postResult = $journalModel->postEntry($data['expense_date'], $ref, $desc, $lines, $userId);
            if ($postResult !== true) {
                return is_string($postResult) ? $postResult : 'Accounting journal posting failed.';
            }

            // Fetch the generated journal entry ID
            $this->db->query("SELECT id FROM journal_entries WHERE reference = :ref ORDER BY id DESC LIMIT 1");
            $this->db->bind(':ref', $ref);
            $jeRow = $this->db->single();
            $journalId = $jeRow ? intval($jeRow->id) : null;

            if (!$journalId) {
                return 'Failed to resolve posted journal entry.';
            }

            $pettyCashTxId = null;

            // Handle Petty Cash Integration if paid via Petty Cash
            if ($paymentMethod === 'Petty Cash') {
                $pettyModel = new PettyCashTransaction();
                $voucherNo = $pettyModel->generateNextVoucherNumber();

                $this->db->query("
                    INSERT INTO petty_cash_transactions 
                    (voucher_number, transaction_date, amount, type, description, payee_name, account_id, created_by, approved_by, approved_at, status)
                    VALUES 
                    (:vnum, :tdate, :amt, 'expense', :desc, :payee, :acc_id, :uid, :app_by, NOW(), 'Approved')
                ");
                $this->db->bind(':vnum', $voucherNo);
                $this->db->bind(':tdate', $data['expense_date']);
                $this->db->bind(':amt', $amount);
                $this->db->bind(':desc', 'Petty Cash Expense (' . $ref . '): ' . $data['description']);
                $this->db->bind(':payee', $vendorName);
                $this->db->bind(':acc_id', $expenseAccountId);
                $this->db->bind(':uid', $userId);
                $this->db->bind(':app_by', $userId);
                $this->db->execute();
                $pettyCashTxId = intval($this->db->lastInsertId());

                // Audit petty cash entry
                $this->auditLog->logAction($userId, 'CREATE_PETTY_CASH_EXPENSE', 'PettyCash', "Expense {$ref} logged to Petty Cash Voucher {$voucherNo}", $pettyCashTxId);
            }

            $this->db->beginTransaction();

            // Create Expense Header
            $this->db->query("INSERT INTO expenses (reference, vendor_id, expense_date, amount, description, payment_method, expense_account_id, payment_account_id, journal_entry_id, petty_cash_transaction_id, created_by) 
                              VALUES (:ref, :vid, :edate, :amt, :desc, :pmethod, :exp_acc, :pay_acc, :jid, :pctx, :uid)");
            $this->db->bind(':ref', $ref);
            $this->db->bind(':vid', $vendorId);
            $this->db->bind(':edate', $data['expense_date']);
            $this->db->bind(':amt', $amount);
            $this->db->bind(':desc', $data['description']);
            $this->db->bind(':pmethod', $paymentMethod);
            $this->db->bind(':exp_acc', $expenseAccountId);
            $this->db->bind(':pay_acc', $paymentAccountId);
            $this->db->bind(':jid', $journalId);
            $this->db->bind(':pctx', $pettyCashTxId);
            $this->db->bind(':uid', $userId);
            $this->db->execute();
            $expenseId = intval($this->db->lastInsertId());

            $this->db->commit();

            // Audit Trail Log
            $this->auditLog->logAction(
                $userId,
                'RECORD_EXPENSE',
                'Expenses',
                "Recorded expense {$ref} of Rs. " . number_format($amount, 2) . " via {$paymentMethod}" . ($vendorId ? " for Vendor: {$vendorName}" : ""),
                $expenseId
            );

            return true;

        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return 'Database Exception: ' . $e->getMessage();
        }
    }
}