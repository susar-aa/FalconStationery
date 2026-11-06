<?php
declare(strict_types=1);

class SalaryAdvance {
    private Database $db;

    public function __construct() {
        $this->db = new Database();
        $this->ensureSchema();
    }

    private function ensureSchema(): void {
        static $checked = false;
        if ($checked) return;
        $checked = true;

        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS salary_advances (
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
            $this->db->execute();

            $this->db->query("CREATE TABLE IF NOT EXISTS salary_advance_recoveries (
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
            $this->db->execute();

            // Self-heal accounts 1659 and 1660 if missing
            $this->db->query("SELECT id FROM chart_of_accounts WHERE account_code = '1659'");
            if (!$this->db->single()) {
                $this->db->query("SELECT id FROM chart_of_accounts WHERE account_code = '1650'");
                $parent = $this->db->single();
                $parentId = $parent ? intval($parent->id) : null;
                $this->db->query("INSERT INTO chart_of_accounts (account_code, account_name, account_type, account_category, parent_id, balance, is_active) 
                                  VALUES ('1659', 'Salary Advance - Damith', 'Asset', 'Current Asset', :parent_id, 0.00, 1)");
                $this->db->bind(':parent_id', $parentId);
                $this->db->execute();
            }

            $this->db->query("SELECT id FROM chart_of_accounts WHERE account_code = '1660'");
            if (!$this->db->single()) {
                $this->db->query("SELECT id FROM chart_of_accounts WHERE account_code = '1650'");
                $parent = $this->db->single();
                $parentId = $parent ? intval($parent->id) : null;
                $this->db->query("INSERT INTO chart_of_accounts (account_code, account_name, account_type, account_category, parent_id, balance, is_active) 
                                  VALUES ('1660', 'Salary Advance - Kaveen', 'Asset', 'Current Asset', :parent_id, 0.00, 1)");
                $this->db->bind(':parent_id', $parentId);
                $this->db->execute();
            }

            // Self-heal payroll_slips advance_deduction column
            $this->db->query("SHOW COLUMNS FROM payroll_slips LIKE 'advance_deduction'");
            if (!$this->db->single()) {
                $this->db->query("ALTER TABLE payroll_slips ADD COLUMN advance_deduction DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER loan_deduction");
                $this->db->execute();
            }

            // Self-heal payroll_runs total_advance_deductions column
            $this->db->query("SHOW COLUMNS FROM payroll_runs LIKE 'total_advance_deductions'");
            if (!$this->db->single()) {
                $this->db->query("ALTER TABLE payroll_runs ADD COLUMN total_advance_deductions DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER total_gross");
                $this->db->execute();
            }
        } catch (Throwable $t) {
            error_log("SalaryAdvance ensureSchema error: " . $t->getMessage());
        }
    }

    public function generateNextAdvanceNumber(): string {
        $prefix = 'ADV-' . date('Ym') . '-';
        $this->db->query("SELECT advance_number FROM salary_advances WHERE advance_number LIKE :pfx ORDER BY id DESC LIMIT 1");
        $this->db->bind(':pfx', $prefix . '%');
        $row = $this->db->single();
        if ($row && preg_match('/-(\d+)$/', $row->advance_number, $matches)) {
            $nextSeq = intval($matches[1]) + 1;
        } else {
            $nextSeq = 1;
        }
        return $prefix . str_pad((string)$nextSeq, 4, '0', STR_PAD_LEFT);
    }

    public function getEmployeeAdvanceAccount(int $employeeId): ?object {
        // 1. Fetch employee record
        $this->db->query("SELECT id, first_name, last_name FROM employees WHERE id = :id");
        $this->db->bind(':id', $employeeId);
        $emp = $this->db->single();
        if (!$emp) {
            return null;
        }

        $firstName = trim($emp->first_name);
        $lastName = trim($emp->last_name);

        // Explicit account code map per requirements
        $nameToCode = [
            'ruwinda' => '1651',
            'chandana' => '1652',
            'madusanka' => '1653',
            'sarath' => '1654',
            'abiman' => '1655',
            'pasindu' => '1656',
            'chethiya' => '1657',
            'madushan' => '1658',
            'damith' => '1659',
            'kaveen' => '1660'
        ];

        $matchedCode = null;
        $fnLower = strtolower($firstName);
        $lnLower = strtolower($lastName);

        foreach ($nameToCode as $keyword => $code) {
            if (strpos($fnLower, $keyword) !== false || strpos($lnLower, $keyword) !== false) {
                $matchedCode = $code;
                break;
            }
        }

        if ($matchedCode) {
            $this->db->query("SELECT * FROM chart_of_accounts WHERE account_code = :code LIMIT 1");
            $this->db->bind(':code', $matchedCode);
            $acc = $this->db->single();
            if ($acc) return $acc;
        }

        // Try exact/partial matching on account_name in 165% accounts
        $this->db->query("SELECT * FROM chart_of_accounts WHERE account_code LIKE '165%' AND (account_name LIKE :fn OR account_name LIKE :ln) AND account_code != '1650' LIMIT 1");
        $this->db->bind(':fn', '%' . $firstName . '%');
        $this->db->bind(':ln', '%' . $lastName . '%');
        $acc = $this->db->single();
        if ($acc) return $acc;

        // Fallback to parent account 1650 - Advance
        $this->db->query("SELECT * FROM chart_of_accounts WHERE account_code = '1650' LIMIT 1");
        return $this->db->single();
    }

    public function getActiveEmployeesWithAccounts(): array {
        $this->db->query("SELECT id, first_name, last_name, department, job_title, base_salary 
                          FROM employees 
                          WHERE status = 'Active' 
                          ORDER BY first_name ASC, last_name ASC");
        $employees = $this->db->resultSet() ?: [];

        foreach ($employees as $emp) {
            $acc = $this->getEmployeeAdvanceAccount(intval($emp->id));
            $emp->advance_account_id = $acc ? intval($acc->id) : null;
            $emp->advance_account_code = $acc ? $acc->account_code : '1650';
            $emp->advance_account_name = $acc ? $acc->account_name : 'Advance';
            $emp->outstanding_advance = $this->getEmployeeOutstandingAdvance(intval($emp->id));
        }

        return $employees;
    }

    public function getEmployeeOutstandingAdvance(int $employeeId): float {
        $this->db->query("SELECT COALESCE(SUM(outstanding_amount), 0) AS total_outstanding 
                          FROM salary_advances 
                          WHERE employee_id = :emp_id AND status IN ('Paid', 'Partially Recovered')");
        $this->db->bind(':emp_id', $employeeId);
        $row = $this->db->single();
        return floatval($row->total_outstanding ?? 0.00);
    }

    public function getEmployeeActiveAdvances(int $employeeId): array {
        $this->db->query("SELECT sa.*, coa.account_name AS advance_account_name, coa.account_code AS advance_account_code
                          FROM salary_advances sa
                          JOIN chart_of_accounts coa ON sa.advance_account_id = coa.id
                          WHERE sa.employee_id = :emp_id AND sa.status IN ('Paid', 'Partially Recovered')
                          ORDER BY sa.payment_date ASC, sa.id ASC");
        $this->db->bind(':emp_id', $employeeId);
        return $this->db->resultSet() ?: [];
    }

    public function issueAdvance(array $data, int $userId): array {
        $employeeId = intval($data['employee_id'] ?? 0);
        $amount = floatval($data['advance_amount'] ?? 0);
        $paymentDate = trim($data['payment_date'] ?? date('Y-m-d'));
        $paymentMethod = trim($data['payment_method'] ?? 'Petty Cash');
        $referenceNo = trim($data['reference_no'] ?? '');
        $notes = trim($data['notes'] ?? '');

        if ($employeeId <= 0) {
            return ['success' => false, 'message' => 'Please select an employee.'];
        }
        if ($amount <= 0) {
            return ['success' => false, 'message' => 'Advance amount must be greater than zero.'];
        }

        // Fetch employee
        $this->db->query("SELECT * FROM employees WHERE id = :id");
        $this->db->bind(':id', $employeeId);
        $emp = $this->db->single();
        if (!$emp) {
            return ['success' => false, 'message' => 'Selected employee not found.'];
        }

        // Advance receivable account (Dr)
        $advanceAcc = $this->getEmployeeAdvanceAccount($employeeId);
        if (!$advanceAcc) {
            return ['success' => false, 'message' => 'Could not map employee to a Salary Advance account in Chart of Accounts.'];
        }
        $advanceAccountId = intval($advanceAcc->id);

        // Resolve source account (Cr)
        $sourceAccountId = 0;
        $bankAccountId = null;

        if ($paymentMethod === 'Petty Cash') {
            require_once __DIR__ . '/PettyCashTransaction.php';
            $pcModel = new PettyCashTransaction();
            $sourceAccountId = $pcModel->getPettyCashAccountId();
        } elseif ($paymentMethod === 'Cash in Hand') {
            $this->db->query("SELECT id FROM chart_of_accounts WHERE account_code = '1000' LIMIT 1");
            $cashRow = $this->db->single();
            $sourceAccountId = $cashRow ? intval($cashRow->id) : 8;
        } elseif ($paymentMethod === 'Bank Transfer' || $paymentMethod === 'Cheque') {
            $bankAccountId = intval($data['bank_account_id'] ?? 0);
            if ($bankAccountId <= 0) {
                return ['success' => false, 'message' => 'Please select a registered bank account for ' . $paymentMethod . '.'];
            }
            $this->db->query("SELECT id, chart_of_account_id, bank_name, account_number FROM bank_accounts WHERE id = :bid");
            $this->db->bind(':bid', $bankAccountId);
            $bRow = $this->db->single();
            if (!$bRow || empty($bRow->chart_of_account_id)) {
                return ['success' => false, 'message' => 'Selected bank account is not linked to Chart of Accounts.'];
            }
            $sourceAccountId = intval($bRow->chart_of_account_id);
        } else {
            return ['success' => false, 'message' => 'Unsupported payment method: ' . $paymentMethod];
        }

        if ($sourceAccountId <= 0) {
            return ['success' => false, 'message' => 'Failed to resolve the source account for payment method ' . $paymentMethod];
        }

        // Unique advance reference
        $advanceNo = $this->generateNextAdvanceNumber();
        $journalRef = 'JE-' . $advanceNo;
        $empFullName = trim($emp->first_name . ' ' . $emp->last_name);
        $journalDesc = "Salary Advance to {$empFullName} via {$paymentMethod} (Ref: {$advanceNo})" . ($notes ? " - {$notes}" : "");

        try {
            $this->db->beginTransaction();

            $pettyCashTxId = null;

            // If Petty Cash, create Petty Cash transaction record and ensure synchronization
            if ($paymentMethod === 'Petty Cash') {
                require_once __DIR__ . '/PettyCashTransaction.php';
                $pcModel = new PettyCashTransaction();

                // Post balanced journal entry first through JournalEntry model
                require_once __DIR__ . '/JournalEntry.php';
                $journalModel = new JournalEntry();

                $lines = [
                    ['account_id' => $advanceAccountId, 'debit' => $amount, 'credit' => 0.00, 'description' => "Salary Advance - {$empFullName}"],
                    ['account_id' => $sourceAccountId, 'debit' => 0.00, 'credit' => $amount, 'description' => "Petty Cash Disbursement for Salary Advance - {$advanceNo}"]
                ];

                $postResult = $journalModel->postEntry($paymentDate, $journalRef, $journalDesc, $lines, $userId);
                if ($postResult !== true) {
                    throw new Exception("Journal Posting Failed: " . (is_string($postResult) ? $postResult : 'Unknown Error'));
                }

                $this->db->query("SELECT id FROM journal_entries WHERE reference = :ref ORDER BY id DESC LIMIT 1");
                $this->db->bind(':ref', $journalRef);
                $jeRow = $this->db->single();
                $journalEntryId = $jeRow ? intval($jeRow->id) : null;

                // Create approved petty cash transaction linked to this journal entry
                $voucherNum = $pcModel->generateNextVoucherNumber();
                $this->db->query("INSERT INTO petty_cash_transactions 
                    (transaction_date, type, amount, reference, description, paid_to, account_id, status, created_by, approved_by, approved_at, journal_entry_id, voucher_number)
                    VALUES 
                    (:tdate, 'expense', :amt, :ref, :desc, :paid_to, :acc_id, 'Approved', :uid, :approver_id, NOW(), :jid, :vnum)");
                $this->db->bind(':tdate', $paymentDate);
                $this->db->bind(':amt', $amount);
                $this->db->bind(':ref', $advanceNo);
                $this->db->bind(':desc', "Salary Advance: " . $journalDesc);
                $this->db->bind(':paid_to', $empFullName);
                $this->db->bind(':acc_id', $advanceAccountId);
                $this->db->bind(':uid', $userId);
                $this->db->bind(':approver_id', $userId);
                $this->db->bind(':jid', $journalEntryId);
                $this->db->bind(':vnum', $voucherNum);
                $this->db->execute();

                $pettyCashTxId = intval($this->db->lastInsertId());

            } else {
                // Cash in Hand, Bank Transfer, or Cheque
                require_once __DIR__ . '/JournalEntry.php';
                $journalModel = new JournalEntry();

                $lines = [
                    ['account_id' => $advanceAccountId, 'debit' => $amount, 'credit' => 0.00, 'description' => "Salary Advance - {$empFullName}"],
                    ['account_id' => $sourceAccountId, 'debit' => 0.00, 'credit' => $amount, 'description' => "Disbursement for Salary Advance - {$advanceNo}"]
                ];

                $postResult = $journalModel->postEntry($paymentDate, $journalRef, $journalDesc, $lines, $userId);
                if ($postResult !== true) {
                    throw new Exception("Journal Posting Failed: " . (is_string($postResult) ? $postResult : 'Unknown Error'));
                }

                $this->db->query("SELECT id FROM journal_entries WHERE reference = :ref ORDER BY id DESC LIMIT 1");
                $this->db->bind(':ref', $journalRef);
                $jeRow = $this->db->single();
                $journalEntryId = $jeRow ? intval($jeRow->id) : null;
            }

            // Insert salary advance record
            $this->db->query("INSERT INTO salary_advances 
                (advance_number, employee_id, advance_amount, recovered_amount, outstanding_amount, 
                 payment_date, payment_method, source_account_id, bank_account_id, advance_account_id, 
                 reference_no, notes, status, journal_entry_id, petty_cash_transaction_id, 
                 created_by, approved_by, approved_at)
                VALUES 
                (:adv_no, :emp_id, :amt, 0.00, :amt, 
                 :pdate, :pmethod, :src_acc, :bank_acc, :adv_acc, 
                 :ref_no, :notes, 'Paid', :jid, :pct_id, 
                 :uid, :approver_id, NOW())");
            $this->db->bind(':adv_no', $advanceNo);
            $this->db->bind(':emp_id', $employeeId);
            $this->db->bind(':amt', $amount);
            $this->db->bind(':pdate', $paymentDate);
            $this->db->bind(':pmethod', $paymentMethod);
            $this->db->bind(':src_acc', $sourceAccountId);
            $this->db->bind(':bank_acc', $bankAccountId);
            $this->db->bind(':adv_acc', $advanceAccountId);
            $this->db->bind(':ref_no', $referenceNo ?: null);
            $this->db->bind(':notes', $notes ?: null);
            $this->db->bind(':jid', $journalEntryId);
            $this->db->bind(':pct_id', $pettyCashTxId);
            $this->db->bind(':uid', $userId);
            $this->db->bind(':approver_id', $userId);
            $this->db->execute();

            $advanceId = intval($this->db->lastInsertId());

            $this->db->commit();

            // Log Audit trail
            try {
                require_once __DIR__ . '/AuditLog.php';
                $audit = new AuditLog();
                $auditDesc = "Issued Salary Advance {$advanceNo} of Rs. " . number_format($amount, 2) . " to {$empFullName} via {$paymentMethod}.";
                $audit->logAction($userId, 'CREATE', 'salary_advance', $auditDesc, $advanceId, null, [
                    'advance_number' => $advanceNo,
                    'employee_id' => $employeeId,
                    'amount' => $amount,
                    'payment_method' => $paymentMethod,
                    'journal_entry_id' => $journalEntryId,
                    'petty_cash_transaction_id' => $pettyCashTxId
                ]);
            } catch (Throwable $at) {}

            return [
                'success' => true,
                'advance_id' => $advanceId,
                'advance_number' => $advanceNo,
                'message' => 'Salary Advance successfully issued and accounting entries posted.'
            ];

        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("issueAdvance error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error issuing salary advance: ' . $e->getMessage()];
        }
    }

    public function cancelAdvance(int $advanceId, int $userId): array {
        $advance = $this->getAdvanceById($advanceId);
        if (!$advance) {
            return ['success' => false, 'message' => 'Salary advance record not found.'];
        }
        if ($advance->status === 'Cancelled') {
            return ['success' => false, 'message' => 'Salary advance is already cancelled.'];
        }
        if ($advance->recovered_amount > 0) {
            return ['success' => false, 'message' => 'Cannot cancel an advance that has already been partially or fully recovered through payroll.'];
        }

        try {
            $this->db->beginTransaction();

            // 1. Void Journal Entry if exists
            if (!empty($advance->journal_entry_id)) {
                require_once __DIR__ . '/JournalEntry.php';
                $jeModel = new JournalEntry();
                $jeModel->voidEntry(intval($advance->journal_entry_id));
            }

            // 2. Reject/Delete Petty Cash transaction if exists
            if (!empty($advance->petty_cash_transaction_id)) {
                $this->db->query("UPDATE petty_cash_transactions SET status = 'Rejected' WHERE id = :pcid");
                $this->db->bind(':pcid', $advance->petty_cash_transaction_id);
                $this->db->execute();
            }

            // 3. Mark salary advance status as Cancelled
            $this->db->query("UPDATE salary_advances SET status = 'Cancelled', outstanding_amount = 0.00 WHERE id = :id");
            $this->db->bind(':id', $advanceId);
            $this->db->execute();

            $this->db->commit();

            try {
                require_once __DIR__ . '/AuditLog.php';
                $audit = new AuditLog();
                $auditDesc = "Cancelled Salary Advance {$advance->advance_number} of Rs. " . number_format(floatval($advance->advance_amount), 2) . ".";
                $audit->logAction($userId, 'VOID', 'salary_advance', $auditDesc, $advanceId, (array)$advance, ['status' => 'Cancelled']);
            } catch (Throwable $at) {}

            return ['success' => true, 'message' => 'Salary Advance successfully cancelled and reversed.'];

        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("cancelAdvance error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error cancelling advance: ' . $e->getMessage()];
        }
    }

    public function recordRecovery(int $advanceId, float $amount, ?int $payrollSlipId, string $date, int $userId, string $notes = ''): bool {
        if ($amount <= 0) return false;

        $advance = $this->getAdvanceById($advanceId);
        if (!$advance || in_array($advance->status, ['Cancelled', 'Fully Recovered'])) {
            return false;
        }

        $amountToRecover = min($amount, floatval($advance->outstanding_amount));
        if ($amountToRecover <= 0) return false;

        $newRecovered = floatval($advance->recovered_amount) + $amountToRecover;
        $newOutstanding = floatval($advance->outstanding_amount) - $amountToRecover;
        $newStatus = ($newOutstanding <= 0.001) ? 'Fully Recovered' : 'Partially Recovered';

        // Insert recovery record
        $this->db->query("INSERT INTO salary_advance_recoveries 
            (salary_advance_id, payroll_slip_id, recovery_date, amount, notes, created_by) 
            VALUES (:aid, :psid, :rdate, :amt, :notes, :uid)");
        $this->db->bind(':aid', $advanceId);
        $this->db->bind(':psid', $payrollSlipId);
        $this->db->bind(':rdate', $date);
        $this->db->bind(':amt', $amountToRecover);
        $this->db->bind(':notes', $notes ?: 'Payroll Deduction');
        $this->db->bind(':uid', $userId);
        $this->db->execute();

        // Update salary advance record
        $this->db->query("UPDATE salary_advances 
                          SET recovered_amount = :rec, outstanding_amount = :out, status = :st 
                          WHERE id = :id");
        $this->db->bind(':rec', $newRecovered);
        $this->db->bind(':out', max(0.0, $newOutstanding));
        $this->db->bind(':st', $newStatus);
        $this->db->bind(':id', $advanceId);
        $this->db->execute();

        return true;
    }

    public function getAdvanceById(int $id): ?object {
        $this->db->query("SELECT sa.*, 
                                 e.first_name, e.last_name, e.employee_code, e.department, e.job_title,
                                 adv_coa.account_code AS advance_account_code, adv_coa.account_name AS advance_account_name,
                                 src_coa.account_code AS source_account_code, src_coa.account_name AS source_account_name,
                                 ba.bank_name, ba.account_number,
                                 u_cr.username AS created_by_name,
                                 u_ap.username AS approved_by_name,
                                 pct.voucher_number AS petty_cash_voucher_number
                          FROM salary_advances sa
                          JOIN employees e ON sa.employee_id = e.id
                          JOIN chart_of_accounts adv_coa ON sa.advance_account_id = adv_coa.id
                          JOIN chart_of_accounts src_coa ON sa.source_account_id = src_coa.id
                          LEFT JOIN bank_accounts ba ON sa.bank_account_id = ba.id
                          LEFT JOIN users u_cr ON sa.created_by = u_cr.id
                          LEFT JOIN users u_ap ON sa.approved_by = u_ap.id
                          LEFT JOIN petty_cash_transactions pct ON sa.petty_cash_transaction_id = pct.id
                          WHERE sa.id = :id");
        $this->db->bind(':id', $id);
        $res = $this->db->single();
        return $res ?: null;
    }

    public function getAdvanceRecoveries(int $advanceId): array {
        $this->db->query("SELECT sar.*, pr.period_start, pr.period_end, u.username AS created_by_name
                          FROM salary_advance_recoveries sar
                          LEFT JOIN payroll_slips ps ON sar.payroll_slip_id = ps.id
                          LEFT JOIN payroll_runs pr ON ps.payroll_run_id = pr.id
                          LEFT JOIN users u ON sar.created_by = u.id
                          WHERE sar.salary_advance_id = :aid
                          ORDER BY sar.recovery_date DESC, sar.id DESC");
        $this->db->bind(':aid', $advanceId);
        return $this->db->resultSet() ?: [];
    }

    public function getAdvances(array $filters = [], ?int $limit = 50, ?int $offset = 0): array {
        $sql = "SELECT sa.*, 
                       e.first_name, e.last_name, e.employee_code, e.department, e.job_title,
                       adv_coa.account_code AS advance_account_code, adv_coa.account_name AS advance_account_name,
                       src_coa.account_code AS source_account_code, src_coa.account_name AS source_account_name,
                       u_cr.username AS created_by_name
                FROM salary_advances sa
                JOIN employees e ON sa.employee_id = e.id
                JOIN chart_of_accounts adv_coa ON sa.advance_account_id = adv_coa.id
                JOIN chart_of_accounts src_coa ON sa.source_account_id = src_coa.id
                LEFT JOIN users u_cr ON sa.created_by = u_cr.id
                WHERE 1=1";

        $params = [];

        if (!empty($filters['employee_id'])) {
            $sql .= " AND sa.employee_id = :emp_id";
            $params[':emp_id'] = intval($filters['employee_id']);
        }

        if (!empty($filters['status']) && $filters['status'] !== 'All') {
            $sql .= " AND sa.status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['start_date'])) {
            $sql .= " AND sa.payment_date >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $sql .= " AND sa.payment_date <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (sa.advance_number LIKE :search 
                      OR e.first_name LIKE :search 
                      OR e.last_name LIKE :search 
                      OR sa.reference_no LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY sa.payment_date DESC, sa.id DESC";

        if ($limit !== null && $offset !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $this->db->query($sql);

        foreach ($params as $k => $v) {
            $this->db->bind($k, $v);
        }

        if ($limit !== null && $offset !== null) {
            $this->db->bind(':limit', $limit, PDO::PARAM_INT);
            $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        }

        return $this->db->resultSet() ?: [];
    }

    public function getAdvancesCount(array $filters = []): int {
        $sql = "SELECT COUNT(*) as cnt 
                FROM salary_advances sa
                JOIN employees e ON sa.employee_id = e.id
                WHERE 1=1";

        $params = [];

        if (!empty($filters['employee_id'])) {
            $sql .= " AND sa.employee_id = :emp_id";
            $params[':emp_id'] = intval($filters['employee_id']);
        }

        if (!empty($filters['status']) && $filters['status'] !== 'All') {
            $sql .= " AND sa.status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['start_date'])) {
            $sql .= " AND sa.payment_date >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $sql .= " AND sa.payment_date <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (sa.advance_number LIKE :search 
                      OR e.first_name LIKE :search 
                      OR e.last_name LIKE :search 
                      OR sa.reference_no LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $this->db->query($sql);
        foreach ($params as $k => $v) {
            $this->db->bind($k, $v);
        }
        $row = $this->db->single();
        return intval($row->cnt ?? 0);
    }

    public function getMetrics(): array {
        // Total advances issued
        $this->db->query("SELECT COALESCE(SUM(advance_amount), 0) AS total_issued, 
                                 COALESCE(SUM(recovered_amount), 0) AS total_recovered,
                                 COALESCE(SUM(CASE WHEN status != 'Cancelled' THEN outstanding_amount ELSE 0 END), 0) AS total_outstanding
                          FROM salary_advances 
                          WHERE status != 'Cancelled'");
        $totals = $this->db->single();

        // Advances issued this month
        $curMonthStart = date('Y-m-01');
        $curMonthEnd = date('Y-m-t');
        $this->db->query("SELECT COALESCE(SUM(advance_amount), 0) AS month_issued 
                          FROM salary_advances 
                          WHERE status != 'Cancelled' AND payment_date BETWEEN :start AND :end");
        $this->db->bind(':start', $curMonthStart);
        $this->db->bind(':end', $curMonthEnd);
        $mRow = $this->db->single();

        // Employee-wise outstanding balances
        $this->db->query("SELECT e.id AS employee_id, e.first_name, e.last_name, e.department, e.job_title,
                                 COALESCE(SUM(sa.outstanding_amount), 0) AS outstanding_advance,
                                 COALESCE(SUM(sa.advance_amount), 0) AS total_advance,
                                 COALESCE(SUM(sa.recovered_amount), 0) AS total_recovered,
                                 COUNT(sa.id) AS advance_count
                          FROM employees e
                          LEFT JOIN salary_advances sa ON e.id = sa.employee_id AND sa.status IN ('Paid', 'Partially Recovered')
                          WHERE e.status = 'Active'
                          GROUP BY e.id
                          HAVING outstanding_advance > 0
                          ORDER BY outstanding_advance DESC");
        $empBalances = $this->db->resultSet() ?: [];

        return [
            'total_issued' => floatval($totals->total_issued ?? 0.0),
            'total_recovered' => floatval($totals->total_recovered ?? 0.0),
            'total_outstanding' => floatval($totals->total_outstanding ?? 0.0),
            'month_issued' => floatval($mRow->month_issued ?? 0.0),
            'employee_balances' => $empBalances
        ];
    }
}

