<?php
declare(strict_types=1);

class Payroll {
    private Database $db;

    public function __construct() {
        $this->db = new Database();
        $this->ensureEpfEtfSchema();
    }

    private function ensureEpfEtfSchema(): void {
        static $checked = false;
        if ($checked) return;
        $checked = true;

        try {
            // Self-heal payroll_runs columns
            $this->db->query("SHOW COLUMNS FROM payroll_runs LIKE 'epf_payment_status'");
            if (!$this->db->single()) {
                $cols = [
                    "total_epf_employee DECIMAL(12,2) NOT NULL DEFAULT 0.00",
                    "total_epf_employer DECIMAL(12,2) NOT NULL DEFAULT 0.00",
                    "total_etf_employer DECIMAL(12,2) NOT NULL DEFAULT 0.00",
                    "epf_payment_status VARCHAR(20) NOT NULL DEFAULT 'Unpaid'",
                    "etf_payment_status VARCHAR(20) NOT NULL DEFAULT 'Unpaid'",
                    "epf_paid_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00",
                    "etf_paid_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00"
                ];
                foreach ($cols as $c) {
                    try {
                        $this->db->query("ALTER TABLE payroll_runs ADD COLUMN " . $c);
                        $this->db->execute();
                    } catch (Throwable $t) {}
                }
            }

            // Self-heal payroll_slips columns
            $this->db->query("SHOW COLUMNS FROM payroll_slips LIKE 'epf_employee'");
            if (!$this->db->single()) {
                $cols = [
                    "epf_applicable_earnings DECIMAL(12,2) NOT NULL DEFAULT 0.00",
                    "epf_employee DECIMAL(12,2) NOT NULL DEFAULT 0.00",
                    "epf_employer DECIMAL(12,2) NOT NULL DEFAULT 0.00",
                    "etf_employer DECIMAL(12,2) NOT NULL DEFAULT 0.00"
                ];
                foreach ($cols as $c) {
                    try {
                        $this->db->query("ALTER TABLE payroll_slips ADD COLUMN " . $c);
                        $this->db->execute();
                    } catch (Throwable $t) {}
                }
            }

            // Self-heal employees columns
            $this->db->query("SHOW COLUMNS FROM employees LIKE 'employee_code'");
            if (!$this->db->single()) {
                try {
                    $this->db->query("ALTER TABLE employees ADD COLUMN employee_code VARCHAR(50) NULL AFTER id");
                    $this->db->execute();
                } catch (Throwable $t) {}
            }

            $this->db->query("SHOW COLUMNS FROM employees LIKE 'epf_member_no'");
            if (!$this->db->single()) {
                $cols = [
                    "epf_member_no VARCHAR(50) NULL",
                    "is_epf_eligible TINYINT(1) NOT NULL DEFAULT 1",
                    "is_etf_eligible TINYINT(1) NOT NULL DEFAULT 1",
                    "epf_employee_rate DECIMAL(5,2) NOT NULL DEFAULT 8.00",
                    "epf_employer_rate DECIMAL(5,2) NOT NULL DEFAULT 12.00",
                    "etf_employer_rate DECIMAL(5,2) NOT NULL DEFAULT 3.00",
                    "epf_etf_effective_date DATE NULL"
                ];
                foreach ($cols as $c) {
                    try {
                        $this->db->query("ALTER TABLE employees ADD COLUMN " . $c);
                        $this->db->execute();
                    } catch (Throwable $t) {}
                }
            }

            // Self-heal epf_etf_payments table
            $this->db->query("SHOW TABLES LIKE 'epf_etf_payments'");
            if (!$this->db->single()) {
                $this->db->query("CREATE TABLE IF NOT EXISTS epf_etf_payments (
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
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
                $this->db->execute();
            } else {
                // Ensure all expected columns exist on epf_etf_payments
                $payCols = [
                    "payroll_run_id INT NOT NULL DEFAULT 0",
                    "payment_type ENUM('EPF', 'ETF') NOT NULL DEFAULT 'EPF'",
                    "payment_date DATE NOT NULL",
                    "period_start DATE NULL",
                    "period_end DATE NULL",
                    "total_liability DECIMAL(15,2) NOT NULL DEFAULT 0.00",
                    "amount_paid DECIMAL(15,2) NOT NULL DEFAULT 0.00",
                    "bank_account_id INT NOT NULL DEFAULT 0",
                    "payment_reference VARCHAR(100) NULL",
                    "journal_entry_id INT NULL",
                    "notes TEXT NULL",
                    "created_by INT NULL"
                ];
                foreach ($payCols as $pc) {
                    $colName = explode(' ', trim($pc))[0];
                    try {
                        $this->db->query("SHOW COLUMNS FROM epf_etf_payments LIKE '$colName'");
                        if (!$this->db->single()) {
                            $this->db->query("ALTER TABLE epf_etf_payments ADD COLUMN " . $pc);
                            $this->db->execute();
                        }
                    } catch (Throwable $t) {}
                }
            }
        } catch (Throwable $e) {
            // Ignore errors if columns/tables exist
        }
    }

    public function getAllPayrollRuns(): array {
        $this->db->query("SELECT p.*, u.username 
                          FROM payroll_runs p 
                          LEFT JOIN users u ON p.created_by = u.id 
                          ORDER BY p.run_date DESC");
        return $this->db->resultSet() ?: [];
    }

    public function getPayrollRunById($id) {
        $this->db->query("SELECT p.*, u.username 
                          FROM payroll_runs p 
                          LEFT JOIN users u ON p.created_by = u.id 
                          WHERE p.id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getSlipsByRunId($runId) {
        $this->db->query("SELECT ps.*, e.first_name, e.last_name, e.employee_code, e.department, e.job_title, e.epf_member_no
                          FROM payroll_slips ps
                          JOIN employees e ON ps.employee_id = e.id
                          WHERE ps.payroll_run_id = :run_id");
        $this->db->bind(':run_id', $runId);
        return $this->db->resultSet() ?: [];
    }

    public function previewPayroll($periodStart, $periodEnd) {
        // Fetch all active employees combined with system user mapping
        $this->db->query("SELECT e.*, u.id as user_id 
                          FROM employees e 
                          LEFT JOIN users u ON u.employee_id = e.id 
                          WHERE e.status = 'Active'");
        $employees = $this->db->resultSet() ?: [];

        require_once __DIR__ . '/RepPerformance.php';
        $repPerfModel = new RepPerformance();

        $slips = [];
        $totalGross = 0;
        $totalNet = 0;
        $totalDeductions = 0;
        $totalEpfEmployee = 0;
        $totalEpfEmployer = 0;
        $totalEtfEmployer = 0;

        foreach ($employees as $emp) {
            $base = floatval($emp->base_salary);
            $allowances = 0;
            $commissions = 0;
            $overtime = 0;
            $other_deductions = 0;

            $commissionBreakdown = [
                'sales_commission' => 0.00,
                'sales_incentive' => 0.00,
                'productive_visits_bonus' => 0.00,
                'working_days_bonus' => 0.00,
                'collection_bonus' => 0.00
            ];

            // If employee is linked to a sales representative user account, compute commissions & bonuses
            if (!empty($emp->user_id)) {
                try {
                    $perf = $repPerfModel->calculatePerformance(intval($emp->user_id), $periodStart, $periodEnd);
                    if (!empty($perf['payroll'])) {
                        $p = $perf['payroll'];
                        $salesComm = floatval($p['sales_commission'] ?? 0);
                        $salesInc = floatval($p['sales_incentive'] ?? 0);
                        $prodBonus = floatval($p['productive_visits_bonus'] ?? 0);
                        $workBonus = floatval($p['working_days_bonus'] ?? 0);
                        $collBonus = floatval($p['collection_bonus'] ?? 0);

                        $commissions = round($salesComm + $salesInc + $prodBonus + $workBonus + $collBonus, 2);
                        $commissionBreakdown = [
                            'sales_commission' => $salesComm,
                            'sales_incentive' => $salesInc,
                            'productive_visits_bonus' => $prodBonus,
                            'working_days_bonus' => $workBonus,
                            'collection_bonus' => $collBonus
                        ];
                    }
                } catch (Throwable $e) {
                    error_log("Rep Performance calculation error for user {$emp->user_id}: " . $e->getMessage());
                }
            }

            // EPF & ETF Eligibility and Rates
            $isEpf = isset($emp->is_epf_eligible) ? intval($emp->is_epf_eligible) : 1;
            $isEtf = isset($emp->is_etf_eligible) ? intval($emp->is_etf_eligible) : 1;

            $epfEmpRate = isset($emp->epf_employee_rate) && floatval($emp->epf_employee_rate) > 0 ? floatval($emp->epf_employee_rate) : 8.00;
            $epfEmprRate = isset($emp->epf_employer_rate) && floatval($emp->epf_employer_rate) > 0 ? floatval($emp->epf_employer_rate) : 12.00;
            $etfEmprRate = isset($emp->etf_employer_rate) && floatval($emp->etf_employer_rate) > 0 ? floatval($emp->etf_employer_rate) : 3.00;

            $epfApplicableEarnings = $base; // EPF/ETF calculated on base salary

            $epfEmployeeAmount = $isEpf ? round($epfApplicableEarnings * ($epfEmpRate / 100), 2) : 0.00;
            $epfEmployerAmount = $isEpf ? round($epfApplicableEarnings * ($epfEmprRate / 100), 2) : 0.00;
            $etfEmployerAmount = $isEtf ? round($epfApplicableEarnings * ($etfEmprRate / 100), 2) : 0.00;

            // Calculate active loan deductions
            $loan_deduction = 0;
            $this->db->query("SELECT id, principal_amount, repayment_amount,
                             COALESCE((SELECT SUM(principal_amount) FROM employee_loan_repayments WHERE employee_loan_id = el.id), 0) as paid
                             FROM employee_loans el
                             WHERE employee_id = :emp_id AND status = 'Active'");
            $this->db->bind(':emp_id', $emp->id);
            $loans = $this->db->resultSet() ?: [];

            $loanDeductionDetails = [];
            foreach ($loans as $loan) {
                $balance = floatval($loan->principal_amount) - floatval($loan->paid);
                if ($balance > 0) {
                    $deduction = min(floatval($loan->repayment_amount), $balance);
                    $loan_deduction += $deduction;
                    $loanDeductionDetails[] = [
                        'loan_id' => $loan->id,
                        'amount' => $deduction
                    ];
                }
            }

            $gross = $base + $allowances + $commissions + $overtime;
            $net = $gross - $loan_deduction - $epfEmployeeAmount - $other_deductions;

            $totalGross += $gross;
            $totalNet += $net;
            $totalDeductions += $loan_deduction + $epfEmployeeAmount + $other_deductions;
            $totalEpfEmployee += $epfEmployeeAmount;
            $totalEpfEmployer += $epfEmployerAmount;
            $totalEtfEmployer += $etfEmployerAmount;

            $slips[] = [
                'employee_id' => $emp->id,
                'employee_name' => $emp->first_name . ' ' . $emp->last_name,
                'epf_member_no' => $emp->epf_member_no ?? '',
                'base_salary' => $base,
                'allowances' => $allowances,
                'commissions' => $commissions,
                'overtime' => $overtime,
                'loan_deduction' => $loan_deduction,
                'other_deductions' => $other_deductions,
                'epf_applicable_earnings' => $epfApplicableEarnings,
                'epf_employee_amount' => $epfEmployeeAmount,
                'epf_employer_amount' => $epfEmployerAmount,
                'etf_employer_amount' => $etfEmployerAmount,
                'gross_salary' => $gross,
                'net_salary' => $net,
                'loan_details' => $loanDeductionDetails,
                'commission_breakdown' => $commissionBreakdown
            ];
        }

        return [
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'total_gross' => $totalGross,
            'total_net' => $totalNet,
            'total_deductions' => $totalDeductions,
            'total_epf_employee' => $totalEpfEmployee,
            'total_epf_employer' => $totalEpfEmployer,
            'total_etf_employer' => $totalEtfEmployer,
            'slips' => $slips
        ];
    }

    public function savePayrollRun($periodStart, $periodEnd, $runDate, $totalGross, $slips, $userId) {
        try {
            $this->db->beginTransaction();

            $totalEpfEmployee = 0;
            $totalEpfEmployer = 0;
            $totalEtfEmployer = 0;

            foreach ($slips as $s) {
                $totalEpfEmployee += floatval($s['epf_employee_amount'] ?? 0);
                $totalEpfEmployer += floatval($s['epf_employer_amount'] ?? 0);
                $totalEtfEmployer += floatval($s['etf_employer_amount'] ?? 0);
            }

            // Insert Run
            $this->db->query("INSERT INTO payroll_runs (
                                run_date, period_start, period_end, total_gross, 
                                total_epf_employee, total_epf_employer, total_etf_employer, 
                                epf_payment_status, etf_payment_status, epf_paid_amount, etf_paid_amount,
                                created_by, status
                              ) 
                              VALUES (
                                :rdate, :pstart, :pend, :gross, 
                                :epf_emp, :epf_empr, :etf_empr, 
                                'Unpaid', 'Unpaid', 0.00, 0.00,
                                :uid, 'Draft'
                              )");
            $this->db->bind(':rdate', $runDate);
            $this->db->bind(':pstart', $periodStart);
            $this->db->bind(':pend', $periodEnd);
            $this->db->bind(':gross', $totalGross);
            $this->db->bind(':epf_emp', $totalEpfEmployee);
            $this->db->bind(':epf_empr', $totalEpfEmployer);
            $this->db->bind(':etf_empr', $totalEtfEmployer);
            $this->db->bind(':uid', $userId);
            $this->db->execute();
            $runId = $this->db->lastInsertId();

            // Insert Slips
            foreach ($slips as $s) {
                $this->db->query("INSERT INTO payroll_slips (
                                    payroll_run_id, employee_id, basic_salary, allowances, commissions, overtime, 
                                    loan_deduction, other_deductions, epf_applicable_earnings, epf_employee_amount, 
                                    epf_employer_amount, etf_employer_amount, gross_salary, net_salary, status
                                  )
                                  VALUES (
                                    :run_id, :emp_id, :base, :allowances, :commissions, :overtime, 
                                    :loan_ded, :other_ded, :epf_app, :epf_emp, 
                                    :epf_empr, :etf_empr, :gross, :net, 'Draft'
                                  )");
                
                $this->db->bind(':run_id', $runId);
                $this->db->bind(':emp_id', $s['employee_id']);
                $this->db->bind(':base', $s['base_salary']);
                $this->db->bind(':allowances', $s['allowances']);
                $this->db->bind(':commissions', $s['commissions']);
                $this->db->bind(':overtime', $s['overtime']);
                $this->db->bind(':loan_ded', $s['loan_deduction']);
                $this->db->bind(':other_ded', $s['other_deductions']);
                $this->db->bind(':epf_app', $s['epf_applicable_earnings'] ?? $s['base_salary']);
                $this->db->bind(':epf_emp', $s['epf_employee_amount'] ?? 0.00);
                $this->db->bind(':epf_empr', $s['epf_employer_amount'] ?? 0.00);
                $this->db->bind(':etf_empr', $s['etf_employer_amount'] ?? 0.00);
                $this->db->bind(':gross', $s['gross_salary']);
                $this->db->bind(':net', $s['net_salary']);
                $this->db->execute();
            }

            $this->db->commit();
            return $runId;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("savePayrollRun error: " . $e->getMessage());
            return false;
        }
    }

    public function approvePayroll($runId, $wageExpenseAccId, $salaryPayableAccId, $employeeLoansAccId, $userId) {
        $run = $this->getPayrollRunById($runId);
        if (!$run || $run->status != 'Draft') return false;

        $slips = $this->getSlipsByRunId($runId);
        
        $totalGross = 0;
        $totalNet = 0;
        $totalLoanDeductions = 0;
        $totalEpfEmployee = 0;
        $totalEpfEmployer = 0;
        $totalEtfEmployer = 0;

        foreach ($slips as $s) {
            $totalGross += floatval($s->gross_salary);
            $totalNet += floatval($s->net_salary);
            $totalLoanDeductions += floatval($s->loan_deduction);
            $totalEpfEmployee += floatval($s->epf_employee_amount ?? 0);
            $totalEpfEmployer += floatval($s->epf_employer_amount ?? 0);
            $totalEtfEmployer += floatval($s->etf_employer_amount ?? 0);
        }

        try {
            $this->db->beginTransaction();

            // Find System Accounts for EPF Payable (2001), ETF Payable (2002), EPF Employer Exp (6010), ETF Employer Exp (6020)
            $this->db->query("SELECT id, account_code FROM chart_of_accounts WHERE account_code IN ('2001', '2002', '2003', '6000', '6010', '6020')");
            $coaRows = $this->db->resultSet() ?: [];
            $coaMap = [];
            foreach ($coaRows as $c) {
                $coaMap[$c->account_code] = intval($c->id);
            }

            $epfPayableAccId = $coaMap['2001'] ?? $salaryPayableAccId;
            $etfPayableAccId = $coaMap['2002'] ?? $salaryPayableAccId;
            $epfExpenseAccId = $coaMap['6010'] ?? $wageExpenseAccId;
            $etfExpenseAccId = $coaMap['6020'] ?? $wageExpenseAccId;

            $desc = "Payroll Approval: " . date('M d', strtotime($run->period_start)) . " to " . date('M d, Y', strtotime($run->period_end));
            $reference = "PR-APP-" . $runId;
            
            $lines = [];

            // DEBITS
            $lines[] = ['account_id' => $wageExpenseAccId, 'debit' => $totalGross, 'credit' => 0, 'description' => 'Salaries & Wages Expense'];
            
            if ($totalEpfEmployer > 0) {
                $lines[] = ['account_id' => $epfExpenseAccId, 'debit' => $totalEpfEmployer, 'credit' => 0, 'description' => 'EPF Employer Contribution Expense (12%)'];
            }
            if ($totalEtfEmployer > 0) {
                $lines[] = ['account_id' => $etfExpenseAccId, 'debit' => $totalEtfEmployer, 'credit' => 0, 'description' => 'ETF Employer Contribution Expense (3%)'];
            }

            // CREDITS
            $totalEpfLiability = $totalEpfEmployee + $totalEpfEmployer;
            if ($totalEpfLiability > 0) {
                $lines[] = ['account_id' => $epfPayableAccId, 'debit' => 0, 'credit' => $totalEpfLiability, 'description' => 'EPF Payable (Employee 8% + Employer 12%)'];
            }

            if ($totalEtfEmployer > 0) {
                $lines[] = ['account_id' => $etfPayableAccId, 'debit' => 0, 'credit' => $totalEtfEmployer, 'description' => 'ETF Payable (Employer 3%)'];
            }

            $lines[] = ['account_id' => $salaryPayableAccId, 'debit' => 0, 'credit' => $totalNet, 'description' => 'Salary Payable (Net)'];

            if ($totalLoanDeductions > 0 && $employeeLoansAccId) {
                $lines[] = ['account_id' => $employeeLoansAccId, 'debit' => 0, 'credit' => $totalLoanDeductions, 'description' => 'Employee Loan Deductions Settlement'];
            } else if ($totalLoanDeductions > 0 && !$employeeLoansAccId) {
                // Fallback to salary payable if no loan account defined
                foreach ($lines as &$l) {
                    if ($l['account_id'] === $salaryPayableAccId) {
                        $l['credit'] += $totalLoanDeductions;
                    }
                }
            }

            require_once __DIR__ . '/JournalEntry.php';
            $journalModel = new JournalEntry();

            $postResult = $journalModel->postEntry(date('Y-m-d'), $reference, $desc, $lines, $userId);
            if ($postResult !== true) {
                throw new Exception("Failed to post journal entry: " . (is_string($postResult) ? $postResult : "Unknown error"));
            }

            $this->db->query("SELECT id FROM journal_entries WHERE reference = :ref");
            $this->db->bind(':ref', $reference);
            $jeRow = $this->db->single();
            $journalId = $jeRow ? intval($jeRow->id) : null;

            $this->db->query("UPDATE payroll_runs SET status = 'Approved', journal_entry_id = :jid WHERE id = :id");
            $this->db->bind(':jid', $journalId);
            $this->db->bind(':id', $runId);
            $this->db->execute();

            $this->db->query("UPDATE payroll_slips SET status = 'Approved' WHERE payroll_run_id = :id");
            $this->db->bind(':id', $runId);
            $this->db->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("approvePayroll error: " . $e->getMessage());
            return false;
        }
    }

    public function payPayroll($runId, $bankAccId, $salaryPayableAccId, $paymentDate, $userId) {
        $run = $this->getPayrollRunById($runId);
        if (!$run || $run->status != 'Approved') return false;

        $slips = $this->getSlipsByRunId($runId);
        $totalNet = 0;

        foreach ($slips as $s) {
            $totalNet += floatval($s->net_salary);
        }

        try {
            $this->db->beginTransaction();

            $desc = "Payroll Net Salary Payment: " . date('M d', strtotime($run->period_start)) . " to " . date('M d, Y', strtotime($run->period_end));
            $reference = "PR-PAY-" . $runId;
            
            $lines = [
                ['account_id' => $salaryPayableAccId, 'debit' => $totalNet, 'credit' => 0, 'description' => 'Salary Payable Settlement'],
                ['account_id' => $bankAccId, 'debit' => 0, 'credit' => $totalNet, 'description' => 'Bank Payment']
            ];

            require_once __DIR__ . '/JournalEntry.php';
            $journalModel = new JournalEntry();

            $postResult = $journalModel->postEntry($paymentDate, $reference, $desc, $lines, $userId);
            if ($postResult !== true) {
                throw new Exception("Failed to post journal entry");
            }

            $this->db->query("UPDATE payroll_runs SET status = 'Paid' WHERE id = :id");
            $this->db->bind(':id', $runId);
            $this->db->execute();

            $this->db->query("UPDATE payroll_slips SET status = 'Paid', payment_date = :pdate WHERE payroll_run_id = :id");
            $this->db->bind(':pdate', $paymentDate);
            $this->db->bind(':id', $runId);
            $this->db->execute();

            // Process Loan Repayments
            require_once __DIR__ . '/EmployeeLoan.php';
            $loanModel = new EmployeeLoan();

            foreach ($slips as $s) {
                if (floatval($s->loan_deduction) > 0) {
                    $deductionLeft = floatval($s->loan_deduction);
                    $activeLoans = $loanModel->getActiveLoansByEmployeeId($s->employee_id);
                    foreach ($activeLoans as $loan) {
                        if ($deductionLeft <= 0) break;
                        $balance = $loan->principal_balance;
                        if ($balance > 0) {
                            $payAmount = min($balance, $deductionLeft);
                            $loanModel->addRepayment([
                                'employee_loan_id' => $loan->id,
                                'payroll_slip_id' => $s->id,
                                'payment_date' => $paymentDate,
                                'principal_amount' => $payAmount,
                                'interest_amount' => 0,
                                'notes' => 'Payroll Auto Deduction',
                                'created_by' => $userId
                            ]);
                            $deductionLeft -= $payAmount;
                        }
                    }
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("payPayroll error: " . $e->getMessage());
            return false;
        }
    }

    // ==========================================
    // EPF & ETF MANAGEMENT & PAYMENT METHODS
    // ==========================================

    public function getEpfEtfLiabilities(?int $year = null, ?int $month = null, ?string $status = null): array {
        $sql = "SELECT pr.id as payroll_run_id, pr.period_start, pr.period_end, pr.run_date, pr.status as run_status,
                       pr.total_gross, pr.total_epf_employee, pr.total_epf_employer, pr.total_etf_employer,
                       pr.epf_payment_status, pr.etf_payment_status, pr.epf_paid_amount, pr.etf_paid_amount,
                       (SELECT COUNT(*) FROM payroll_slips WHERE payroll_run_id = pr.id) as employee_count
                FROM payroll_runs pr
                WHERE pr.status IN ('Approved', 'Paid')";
        
        if ($year !== null && $year > 0) {
            $sql .= " AND YEAR(pr.period_end) = " . intval($year);
        }
        if ($month !== null && $month > 0) {
            $sql .= " AND MONTH(pr.period_end) = " . intval($month);
        }
        if ($status !== null && $status !== '' && $status !== 'All') {
            $sql .= " AND (pr.epf_payment_status = '" . addslashes($status) . "' OR pr.etf_payment_status = '" . addslashes($status) . "')";
        }

        $sql .= " ORDER BY pr.period_end DESC";

        $this->db->query($sql);
        return $this->db->resultSet() ?: [];
    }

    public function getEpfEtfPaymentHistory(): array {
        $sql = "SELECT p.*, pr.period_start, pr.period_end, c.account_code as bank_code, c.account_name as bank_name, u.username as paid_by_user
                FROM epf_etf_payments p
                JOIN payroll_runs pr ON p.payroll_run_id = pr.id
                JOIN chart_of_accounts c ON p.bank_account_id = c.id
                JOIN users u ON p.created_by = u.id
                ORDER BY p.payment_date DESC, p.id DESC";
        $this->db->query($sql);
        return $this->db->resultSet() ?: [];
    }

    public function recordEpfEtfPayment(array $data, int $userId): array {
        $runId = intval($data['payroll_run_id']);
        $paymentType = strtoupper(trim($data['payment_type']));
        $amountPaid = floatval($data['amount_paid']);
        $bankAccountId = intval($data['bank_account_id']);
        $paymentDate = trim($data['payment_date']);
        $ref = trim($data['payment_reference'] ?? '');

        $run = $this->getPayrollRunById($runId);
        if (!$run || !in_array($run->status, ['Approved', 'Paid'])) {
            return ['status' => false, 'message' => 'Payroll run must be Approved or Paid before recording EPF/ETF payments.'];
        }

        if ($amountPaid <= 0) {
            return ['status' => false, 'message' => 'Payment amount must be greater than zero.'];
        }

        // Determine target liability COA account
        $targetCode = ($paymentType === 'EPF') ? '2001' : '2002';
        $this->db->query("SELECT id FROM chart_of_accounts WHERE account_code = :code LIMIT 1");
        $this->db->bind(':code', $targetCode);
        $payableAcc = $this->db->single();

        if (!$payableAcc) {
            return ['status' => false, 'message' => "COA Account $targetCode ($paymentType Payable) not found."];
        }
        $payableAccId = intval($payableAcc->id);

        $totalLiability = ($paymentType === 'EPF') 
            ? (floatval($run->total_epf_employee) + floatval($run->total_epf_employer))
            : floatval($run->total_etf_employer);

        $currentPaid = ($paymentType === 'EPF') ? floatval($run->epf_paid_amount) : floatval($run->etf_paid_amount);
        $remainingBalance = round($totalLiability - $currentPaid, 2);

        if ($amountPaid > $remainingBalance + 0.05) {
            return ['status' => false, 'message' => "Payment amount LKR " . number_format($amountPaid, 2) . " exceeds remaining $paymentType balance of LKR " . number_format($remainingBalance, 2)];
        }

        try {
            $this->db->beginTransaction();

            $paymentRef = !empty($ref) ? $ref : ($paymentType . "-REM-" . date('Ym', strtotime($run->period_end)) . "-" . $runId);
            $desc = "$paymentType Contribution Payment for period " . date('M Y', strtotime($run->period_end));

            // Post GL Journal Entry
            $lines = [
                ['account_id' => $payableAccId, 'debit' => $amountPaid, 'credit' => 0, 'description' => "$paymentType Payable Settlement"],
                ['account_id' => $bankAccountId, 'debit' => 0, 'credit' => $amountPaid, 'description' => "Bank Settlement"]
            ];

            require_once __DIR__ . '/JournalEntry.php';
            $journalModel = new JournalEntry();

            $postResult = $journalModel->postEntry($paymentDate, $paymentRef, $desc, $lines, $userId);
            if ($postResult !== true) {
                throw new Exception("Failed to post payment journal entry: " . (is_string($postResult) ? $postResult : ""));
            }

            $this->db->query("SELECT id FROM journal_entries WHERE reference = :ref ORDER BY id DESC LIMIT 1");
            $this->db->bind(':ref', $paymentRef);
            $jeRow = $this->db->single();
            $jeId = $jeRow ? intval($jeRow->id) : null;

            // Insert Payment Record
            $this->db->query("INSERT INTO epf_etf_payments (
                                payroll_run_id, payment_type, payment_date, period_start, period_end, 
                                total_liability, amount_paid, bank_account_id, payment_reference, 
                                journal_entry_id, notes, created_by
                              ) VALUES (
                                :run_id, :ptype, :pdate, :pstart, :pend, 
                                :total_liab, :paid, :bank_id, :ref, 
                                :je_id, :notes, :uid
                              )");
            $this->db->bind(':run_id', $runId);
            $this->db->bind(':ptype', $paymentType);
            $this->db->bind(':pdate', $paymentDate);
            $this->db->bind(':pstart', $run->period_start);
            $this->db->bind(':pend', $run->period_end);
            $this->db->bind(':total_liab', $totalLiability);
            $this->db->bind(':paid', $amountPaid);
            $this->db->bind(':bank_id', $bankAccountId);
            $this->db->bind(':ref', $paymentRef);
            $this->db->bind(':je_id', $jeId);
            $this->db->bind(':notes', $data['notes'] ?? '');
            $this->db->bind(':uid', $userId);
            $this->db->execute();

            // Update Payroll Run Status & Paid Amount
            $newPaidTotal = $currentPaid + $amountPaid;
            $newStatus = ($newPaidTotal >= $totalLiability - 0.01) ? 'Paid' : 'Partially Paid';

            if ($paymentType === 'EPF') {
                $this->db->query("UPDATE payroll_runs SET epf_paid_amount = :paid, epf_payment_status = :status WHERE id = :id");
            } else {
                $this->db->query("UPDATE payroll_runs SET etf_paid_amount = :paid, etf_payment_status = :status WHERE id = :id");
            }
            $this->db->bind(':paid', $newPaidTotal);
            $this->db->bind(':status', $newStatus);
            $this->db->bind(':id', $runId);
            $this->db->execute();

            $this->db->commit();
            return ['status' => true, 'message' => "$paymentType Payment of LKR " . number_format($amountPaid, 2) . " recorded successfully!"];

        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("recordEpfEtfPayment error: " . $e->getMessage());
            return ['status' => false, 'message' => 'Database error recording payment: ' . $e->getMessage()];
        }
    }

    // ==========================================
    // EPF & ETF COMPLIANCE REPORTS
    // ==========================================

    public function getMonthlyEpfReport(int $runId): array {
        $run = $this->getPayrollRunById($runId);
        if (!$run) return [];

        $slips = $this->getSlipsByRunId($runId);
        $reportSlips = [];
        $totalBasic = 0;
        $totalEpfEarnings = 0;
        $totalEmployee8 = 0;
        $totalEmployer12 = 0;
        $totalEpf20 = 0;

        foreach ($slips as $s) {
            $basic = floatval($s->basic_salary);
            $epfEarn = floatval($s->epf_applicable_earnings ?? $basic);
            $emp8 = floatval($s->epf_employee_amount ?? 0);
            $empr12 = floatval($s->epf_employer_amount ?? 0);
            $tot20 = $emp8 + $empr12;

            $totalBasic += $basic;
            $totalEpfEarnings += $epfEarn;
            $totalEmployee8 += $emp8;
            $totalEmployer12 += $empr12;
            $totalEpf20 += $tot20;

            $reportSlips[] = [
                'employee_code' => $s->employee_code,
                'employee_name' => $s->first_name . ' ' . $s->last_name,
                'epf_member_no' => $s->epf_member_no ?? '',
                'department' => $s->department,
                'basic_salary' => $basic,
                'epf_applicable_earnings' => $epfEarn,
                'employee_8_pct' => $emp8,
                'employer_12_pct' => $empr12,
                'total_20_pct' => $tot20
            ];
        }

        return [
            'run' => $run,
            'slips' => $reportSlips,
            'summary' => [
                'total_basic' => $totalBasic,
                'total_epf_earnings' => $totalEpfEarnings,
                'total_employee_8' => $totalEmployee8,
                'total_employer_12' => $totalEmployer12,
                'total_epf_20' => $totalEpf20
            ]
        ];
    }

    public function getMonthlyEtfReport(int $runId): array {
        $run = $this->getPayrollRunById($runId);
        if (!$run) return [];

        $slips = $this->getSlipsByRunId($runId);
        $reportSlips = [];
        $totalBasic = 0;
        $totalEtfEarnings = 0;
        $totalEmployer3 = 0;

        foreach ($slips as $s) {
            $basic = floatval($s->basic_salary);
            $etfEarn = floatval($s->epf_applicable_earnings ?? $basic);
            $empr3 = floatval($s->etf_employer_amount ?? 0);

            $totalBasic += $basic;
            $totalEtfEarnings += $etfEarn;
            $totalEmployer3 += $empr3;

            $reportSlips[] = [
                'employee_code' => $s->employee_code,
                'employee_name' => $s->first_name . ' ' . $s->last_name,
                'epf_member_no' => $s->epf_member_no ?? '',
                'department' => $s->department,
                'basic_salary' => $basic,
                'etf_applicable_earnings' => $etfEarn,
                'employer_3_pct' => $empr3
            ];
        }

        return [
            'run' => $run,
            'slips' => $reportSlips,
            'summary' => [
                'total_basic' => $totalBasic,
                'total_etf_earnings' => $totalEtfEarnings,
                'total_employer_3' => $totalEmployer3
            ]
        ];
    }

    public function getEmployeeContributionHistory(int $employeeId, ?int $year = null): array {
        $sql = "SELECT ps.*, pr.period_start, pr.period_end, pr.run_date, e.first_name, e.last_name, 
                       CONCAT('EMP-', e.id) as employee_code, e.epf_member_no
                FROM payroll_slips ps
                JOIN payroll_runs pr ON ps.payroll_run_id = pr.id
                JOIN employees e ON ps.employee_id = e.id
                WHERE pr.status IN ('Approved', 'Paid')";
        
        if ($employeeId > 0) {
            $sql .= " AND ps.employee_id = " . intval($employeeId);
        }
        if ($year !== null && $year > 0) {
            $sql .= " AND YEAR(pr.period_end) = " . intval($year);
        }

        $sql .= " ORDER BY pr.period_end DESC";

        $this->db->query($sql);
        return $this->db->resultSet() ?: [];
    }

    public function getEmployerContributionSummary(?int $year = null): array {
        $yearFilter = ($year !== null && $year > 0) ? intval($year) : intval(date('Y'));
        
        $sql = "SELECT MONTH(pr.period_end) as month_num,
                       DATE_FORMAT(pr.period_end, '%M %Y') as month_name,
                       SUM(pr.total_epf_employer) as total_epf_employer,
                       SUM(pr.total_etf_employer) as total_etf_employer,
                       SUM(pr.total_epf_employer + pr.total_etf_employer) as grand_total_employer
                FROM payroll_runs pr
                WHERE pr.status IN ('Approved', 'Paid')
                  AND YEAR(pr.period_end) = :year
                GROUP BY MONTH(pr.period_end), DATE_FORMAT(pr.period_end, '%M %Y')
                ORDER BY MONTH(pr.period_end) ASC";

        $this->db->query($sql);
        $this->db->bind(':year', $yearFilter);
        return $this->db->resultSet() ?: [];
    }

    public function getEpfEtfPayableReport(?int $year = null): array {
        $yearFilter = ($year !== null && $year > 0) ? intval($year) : intval(date('Y'));
        
        $sql = "SELECT pr.id as run_id, pr.period_start, pr.period_end,
                       (pr.total_epf_employee + pr.total_epf_employer) as epf_total_liability,
                       pr.epf_paid_amount,
                       ((pr.total_epf_employee + pr.total_epf_employer) - pr.epf_paid_amount) as epf_balance,
                       pr.epf_payment_status,
                       pr.total_etf_employer as etf_total_liability,
                       pr.etf_paid_amount,
                       (pr.total_etf_employer - pr.etf_paid_amount) as etf_balance,
                       pr.etf_payment_status
                FROM payroll_runs pr
                WHERE pr.status IN ('Approved', 'Paid')
                  AND YEAR(pr.period_end) = :year
                ORDER BY pr.period_end DESC";

        $this->db->query($sql);
        $this->db->bind(':year', $yearFilter);
        return $this->db->resultSet() ?: [];
    }
}