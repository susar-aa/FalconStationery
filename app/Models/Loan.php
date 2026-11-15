<?php
class Loan {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllBankAccounts() {
        $this->db->query("SELECT id FROM chart_of_accounts WHERE account_code = '1600' LIMIT 1");
        $parent = $this->db->single();
        $parentId = $parent ? $parent->id : 0;
        
        $this->db->query("SELECT id, account_code as account_number, account_name as bank_name 
                          FROM chart_of_accounts 
                          WHERE parent_id = :pid AND is_active = 1 
                          ORDER BY account_code ASC");
        $this->db->bind(':pid', $parentId);
        return $this->db->resultSet() ?: [];
    }

    public function getAllCashAccounts() {
        $this->db->query("SELECT id, account_code as account_number, account_name as bank_name 
                          FROM chart_of_accounts 
                          WHERE account_type = 'Asset' 
                            AND (account_code LIKE '10%' OR id = 8 OR parent_id = 8 OR account_name LIKE '%Cash in Hand%' OR account_name LIKE '%Petty Cash%')
                            AND is_active = 1 
                          ORDER BY account_code ASC");
        return $this->db->resultSet() ?: [];
    }

    public function getExpenseAccounts() {
        $this->db->query("SELECT id, account_code, account_name, account_type, account_category 
                          FROM chart_of_accounts 
                          WHERE account_type = 'Expense' AND is_active = 1 
                          ORDER BY account_code ASC");
        return $this->db->resultSet() ?: [];
    }

    public function getLiabilityAccounts() {
        $this->db->query("SELECT id, account_code, account_name, account_type, account_category 
                          FROM chart_of_accounts 
                          WHERE account_type = 'Liability' AND is_active = 1 
                          ORDER BY account_code ASC");
        return $this->db->resultSet() ?: [];
    }

    public function getLoans($filterStatus = null, $search = null) {
        $whereSql = "WHERE 1=1";
        $binds = [];

        if (!empty($filterStatus)) {
            $whereSql .= " AND l.status = :status";
            $binds[':status'] = $filterStatus;
        }

        if (!empty($search)) {
            $whereSql .= " AND (l.lender_name LIKE :search OR l.loan_number LIKE :search OR l.notes LIKE :search)";
            $binds[':search'] = $search . '%';
        }

        $this->db->query("
            SELECT 
                l.id, l.bank_account_id, l.liability_account_id, l.interest_expense_account_id, l.bank_charges_account_id,
                l.lender_name, l.loan_number, l.loan_type, l.principal_amount, l.monthly_installment,
                l.interest_rate, l.interest_type, l.loan_start_date, l.loan_term_months, l.repayment_frequency,
                l.first_payment_date, l.maturity_date, l.processing_fees, l.status, l.is_historical_opening,
                l.opening_balance, l.notes, l.created_at, l.updated_at,
                COALESCE(rep.total_principal_paid, 0.00) as total_principal_paid,
                COALESCE(rep.total_interest_paid, 0.00) as total_interest_paid,
                COALESCE(rep.total_charges_paid, 0.00) as total_charges_paid,
                COALESCE(rep.total_rebate_paid, 0.00) as total_rebate_paid,
                COALESCE(rep.total_amount_paid, 0.00) as total_amount_paid,
                COALESCE(rep.repayment_count, 0) as repayment_count,
                coa_liab.account_code as liability_account_code, coa_liab.account_name as liability_account_name,
                coa_int.account_code as interest_account_code, coa_int.account_name as interest_account_name,
                coa_bank.account_name as bank_account_name, coa_bank.account_code as bank_account_code
            FROM loans l
            LEFT JOIN (
                SELECT 
                    loan_id,
                    SUM(principal_amount) as total_principal_paid,
                    SUM(interest_amount) as total_interest_paid,
                    SUM(bank_charges + COALESCE(penalty_charges, 0)) as total_charges_paid,
                    SUM(COALESCE(early_settlement_rebate, 0)) as total_rebate_paid,
                    SUM(total_payment) as total_amount_paid,
                    COUNT(id) as repayment_count
                FROM loan_repayments
                GROUP BY loan_id
            ) rep ON rep.loan_id = l.id
            LEFT JOIN chart_of_accounts coa_liab ON l.liability_account_id = coa_liab.id
            LEFT JOIN chart_of_accounts coa_int ON l.interest_expense_account_id = coa_int.id
            LEFT JOIN chart_of_accounts coa_bank ON l.bank_account_id = coa_bank.id
            {$whereSql}
            ORDER BY l.created_at DESC
        ");

        foreach ($binds as $param => $val) {
            $this->db->bind($param, $val);
        }

        $loans = $this->db->resultSet() ?: [];
        foreach ($loans as $l) {
            $basePrincipal = ($l->is_historical_opening == 1 && floatval($l->opening_balance) > 0)
                ? floatval($l->opening_balance)
                : floatval($l->principal_amount);
            
            $l->principal_balance = max(0.00, $basePrincipal - floatval($l->total_principal_paid) - floatval($l->total_rebate_paid));
            $scheduleInfo = $this->calculateScheduleAndArrears($l);
            $l->next_due_date = $scheduleInfo['next_due_date'];
            $l->days_until_due = $scheduleInfo['days_until_due'];
            $l->is_overdue = $scheduleInfo['is_overdue'];
            $l->arrears_amount = $scheduleInfo['arrears_amount'];
            $l->installments_due_count = $scheduleInfo['installments_due_count'];
        }
        return $loans;
    }

    public function getLoanById($id) {
        $this->db->query("
            SELECT 
                l.id, l.bank_account_id, l.liability_account_id, l.interest_expense_account_id, l.bank_charges_account_id,
                l.lender_name, l.loan_number, l.loan_type, l.principal_amount, l.monthly_installment,
                l.interest_rate, l.interest_type, l.loan_start_date, l.loan_term_months, l.repayment_frequency,
                l.first_payment_date, l.maturity_date, l.processing_fees, l.status, l.is_historical_opening,
                l.opening_balance, l.notes, l.created_at, l.updated_at,
                COALESCE(rep.total_principal_paid, 0.00) as total_principal_paid,
                COALESCE(rep.total_interest_paid, 0.00) as total_interest_paid,
                COALESCE(rep.total_charges_paid, 0.00) as total_charges_paid,
                COALESCE(rep.total_rebate_paid, 0.00) as total_rebate_paid,
                COALESCE(rep.total_amount_paid, 0.00) as total_amount_paid,
                COALESCE(rep.repayment_count, 0) as repayment_count,
                coa_liab.account_code as liability_account_code, coa_liab.account_name as liability_account_name,
                coa_int.account_code as interest_account_code, coa_int.account_name as interest_account_name,
                coa_bank.account_name as bank_account_name, coa_bank.account_code as bank_account_code
            FROM loans l 
            LEFT JOIN (
                SELECT 
                    loan_id,
                    SUM(principal_amount) as total_principal_paid,
                    SUM(interest_amount) as total_interest_paid,
                    SUM(bank_charges + COALESCE(penalty_charges, 0)) as total_charges_paid,
                    SUM(COALESCE(early_settlement_rebate, 0)) as total_rebate_paid,
                    SUM(total_payment) as total_amount_paid,
                    COUNT(id) as repayment_count
                FROM loan_repayments
                GROUP BY loan_id
            ) rep ON rep.loan_id = l.id
            LEFT JOIN chart_of_accounts coa_liab ON l.liability_account_id = coa_liab.id
            LEFT JOIN chart_of_accounts coa_int ON l.interest_expense_account_id = coa_int.id
            LEFT JOIN chart_of_accounts coa_bank ON l.bank_account_id = coa_bank.id
            WHERE l.id = :id
            LIMIT 1
        ");
        $this->db->bind(':id', $id);
        $loan = $this->db->single();
        if ($loan) {
            $basePrincipal = ($loan->is_historical_opening == 1 && floatval($loan->opening_balance) > 0)
                ? floatval($loan->opening_balance)
                : floatval($loan->principal_amount);
            
            $loan->principal_balance = max(0.00, $basePrincipal - floatval($loan->total_principal_paid) - floatval($loan->total_rebate_paid));
            $scheduleInfo = $this->calculateScheduleAndArrears($loan);
            $loan->next_due_date = $scheduleInfo['next_due_date'];
            $loan->days_until_due = $scheduleInfo['days_until_due'];
            $loan->is_overdue = $scheduleInfo['is_overdue'];
            $loan->arrears_amount = $scheduleInfo['arrears_amount'];
            $loan->installments_due_count = $scheduleInfo['installments_due_count'];
        }
        return $loan;
    }

    public function calculateScheduleAndArrears($loan) {
        $firstPayDate = !empty($loan->first_payment_date) ? $loan->first_payment_date : $loan->loan_start_date;
        $installment = floatval($loan->monthly_installment ?? 0);
        $termMonths = intval($loan->loan_term_months ?? 0);
        $totalPaid = floatval($loan->total_amount_paid ?? 0);
        
        $today = new DateTime();
        $nextDueDate = null;
        $daysUntilDue = null;
        $isOverdue = false;
        $arrearsAmount = 0.00;
        $installmentsDueCount = 0;
        
        if ($loan->status === 'Active' && !empty($firstPayDate)) {
            try {
                $firstDateObj = new DateTime($firstPayDate);
                $diff = $firstDateObj->diff($today);
                $monthsElapsed = ($diff->y * 12) + $diff->m;
                
                if ($today >= $firstDateObj) {
                    $installmentsDueCount = $monthsElapsed + 1;
                    if ($termMonths > 0) {
                        $installmentsDueCount = min($installmentsDueCount, $termMonths);
                    }
                } else {
                    $installmentsDueCount = 0;
                }
                
                if ($installment > 0) {
                    $expectedPaid = $installmentsDueCount * $installment;
                    if ($expectedPaid > $totalPaid) {
                        $arrearsAmount = $expectedPaid - $totalPaid;
                        $isOverdue = true;
                    }
                }
                
                $currentPeriodDate = clone $firstDateObj;
                $periodsCounter = 0;
                while ($currentPeriodDate < $today && ($termMonths === 0 || $periodsCounter < $termMonths)) {
                    $currentPeriodDate->modify('+1 month');
                    $periodsCounter++;
                }
                $nextDueDate = $currentPeriodDate->format('Y-m-d');
                $dueDiff = $today->diff($currentPeriodDate);
                $daysUntilDue = (int)$dueDiff->format('%r%a');
            } catch (Exception $e) {}
        }
        
        return [
            'next_due_date' => $nextDueDate,
            'days_until_due' => $daysUntilDue,
            'is_overdue' => $isOverdue,
            'arrears_amount' => $arrearsAmount,
            'installments_due_count' => $installmentsDueCount
        ];
    }

    public function addLoan($data) {
        $this->db->beginTransaction();
        try {
            $isHistorical = !empty($data['is_historical_opening']) ? 1 : 0;
            $status = $isHistorical ? 'Active' : 'Pending';
            $openingBal = $isHistorical ? floatval($data['opening_balance'] ?? $data['principal_amount']) : 0.00;

            $this->db->query("INSERT INTO loans 
                (liability_account_id, interest_expense_account_id, bank_charges_account_id, lender_name, 
                 loan_number, loan_type, principal_amount, monthly_installment, interest_rate, interest_type, 
                 loan_start_date, loan_term_months, repayment_frequency, first_payment_date, maturity_date, 
                 status, is_historical_opening, opening_balance, notes) 
                VALUES 
                (:liability_acc, :interest_acc, :charges_acc, :lender, 
                 :loan_no, :loan_type, :principal, :installment, :interest, :interest_type, 
                 :start_date, :term, :freq, :first_pay, :maturity, 
                 :status, :is_historical, :opening_bal, :notes)");
            
            $this->db->bind(':liability_acc', $data['liability_account_id']);
            $this->db->bind(':interest_acc', !empty($data['interest_expense_account_id']) ? intval($data['interest_expense_account_id']) : null);
            $this->db->bind(':charges_acc', !empty($data['bank_charges_account_id']) ? intval($data['bank_charges_account_id']) : null);
            $this->db->bind(':lender', $data['lender_name']);
            $this->db->bind(':loan_no', $data['loan_number']);
            $this->db->bind(':loan_type', !empty($data['loan_type']) ? $data['loan_type'] : 'Term Loan');
            $this->db->bind(':principal', $data['principal_amount']);
            $this->db->bind(':installment', floatval($data['monthly_installment'] ?? 0));
            $this->db->bind(':interest', $data['interest_rate']);
            $this->db->bind(':interest_type', !empty($data['interest_type']) ? $data['interest_type'] : 'Flat Rate');
            $this->db->bind(':start_date', $data['loan_start_date']);
            $this->db->bind(':term', !empty($data['loan_term_months']) ? intval($data['loan_term_months']) : null);
            $this->db->bind(':freq', $data['repayment_frequency']);
            $this->db->bind(':first_pay', !empty($data['first_payment_date']) ? $data['first_payment_date'] : null);
            $this->db->bind(':maturity', !empty($data['maturity_date']) ? $data['maturity_date'] : null);
            $this->db->bind(':status', $status);
            $this->db->bind(':is_historical', $isHistorical);
            $this->db->bind(':opening_bal', $openingBal);
            $this->db->bind(':notes', $data['notes']);
            
            $this->db->execute();
            $newId = $this->db->lastInsertId();
            $this->db->commit();
            return $newId;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("addLoan failed: " . $e->getMessage());
            return false;
        }
    }

    public function updateLoan($data) {
        $this->db->beginTransaction();
        try {
            $this->db->query("UPDATE loans 
                SET liability_account_id = :liability_acc,
                    interest_expense_account_id = :interest_acc,
                    bank_charges_account_id = :charges_acc,
                    lender_name = :lender, 
                    loan_number = :loan_no, 
                    loan_type = :loan_type,
                    principal_amount = :principal, 
                    monthly_installment = :installment,
                    interest_rate = :interest, 
                    interest_type = :interest_type,
                    loan_start_date = :start_date, 
                    loan_term_months = :term, 
                    repayment_frequency = :freq, 
                    first_payment_date = :first_pay, 
                    maturity_date = :maturity, 
                    opening_balance = :opening_bal,
                    notes = :notes, 
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id");
            
            $this->db->bind(':id', $data['id']);
            $this->db->bind(':liability_acc', $data['liability_account_id']);
            $this->db->bind(':interest_acc', !empty($data['interest_expense_account_id']) ? intval($data['interest_expense_account_id']) : null);
            $this->db->bind(':charges_acc', !empty($data['bank_charges_account_id']) ? intval($data['bank_charges_account_id']) : null);
            $this->db->bind(':lender', $data['lender_name']);
            $this->db->bind(':loan_no', $data['loan_number']);
            $this->db->bind(':loan_type', !empty($data['loan_type']) ? $data['loan_type'] : 'Term Loan');
            $this->db->bind(':principal', $data['principal_amount']);
            $this->db->bind(':installment', floatval($data['monthly_installment'] ?? 0));
            $this->db->bind(':interest', $data['interest_rate']);
            $this->db->bind(':interest_type', !empty($data['interest_type']) ? $data['interest_type'] : 'Flat Rate');
            $this->db->bind(':start_date', !empty($data['loan_start_date']) ? $data['loan_start_date'] : null);
            $this->db->bind(':term', !empty($data['loan_term_months']) ? $data['loan_term_months'] : null);
            $this->db->bind(':freq', $data['repayment_frequency']);
            $this->db->bind(':first_pay', !empty($data['first_payment_date']) ? $data['first_payment_date'] : null);
            $this->db->bind(':maturity', !empty($data['maturity_date']) ? $data['maturity_date'] : null);
            $this->db->bind(':opening_bal', floatval($data['opening_balance'] ?? 0));
            $this->db->bind(':notes', $data['notes']);

            $res = $this->db->execute();
            $this->db->commit();
            return $res;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("updateLoan failed: " . $e->getMessage());
            return false;
        }
    }

    public function deleteLoan($id) {
        $loan = $this->getLoanById($id);
        if (!$loan) return false;

        $this->db->query("SELECT COUNT(id) as cnt FROM loan_repayments WHERE loan_id = :id");
        $this->db->bind(':id', $id);
        $repRow = $this->db->single();
        if ($repRow && intval($repRow->cnt) > 0) {
            return false;
        }

        $this->db->beginTransaction();
        try {
            // If loan has disbursement journal entry, clean it up safely
            $ref = "LOAN-DISB-" . $id;
            $this->db->query("SELECT id FROM journal_entries WHERE reference = :ref LIMIT 1");
            $this->db->bind(':ref', $ref);
            $je = $this->db->single();
            if ($je) {
                $this->db->query("DELETE FROM transactions WHERE journal_entry_id = :je_id");
                $this->db->bind(':je_id', $je->id);
                $this->db->execute();

                $this->db->query("DELETE FROM journal_entries WHERE id = :je_id");
                $this->db->bind(':je_id', $je->id);
                $this->db->execute();
            }

            $this->db->query("DELETE FROM loans WHERE id = :id");
            $this->db->bind(':id', $id);
            $this->db->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("deleteLoan failed: " . $e->getMessage());
            return false;
        }
    }

    public function disburseLoan($loanId, $bankAccountId, $processingFees, $userId) {
        $loan = $this->getLoanById($loanId);
        if (!$loan || $loan->status != 'Pending') return false;

        $this->db->beginTransaction();
        try {
            $this->db->query("UPDATE loans SET status = 'Active', bank_account_id = :bank_id, processing_fees = :fees WHERE id = :id");
            $this->db->bind(':bank_id', $bankAccountId);
            $this->db->bind(':fees', $processingFees);
            $this->db->bind(':id', $loanId);
            $this->db->execute();
            
            $this->syncDisbursementAccounting($loanId, $bankAccountId, $loan->principal_amount, $processingFees, $userId);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("disburseLoan failed: " . $e->getMessage());
            return false;
        }
    }

    private function syncDisbursementAccounting($loanId, $bankAccountId, $principalAmount, $fees, $userId) {
        $loan = $this->getLoanById($loanId);
        if (!$loan) return;

        $bankCoaId = $bankAccountId;
        $liabilityCoaId = $loan->liability_account_id;

        // Determine Bank Charges account dynamically
        $feesCoaId = !empty($loan->bank_charges_account_id) ? $loan->bank_charges_account_id : null;
        if (!$feesCoaId) {
            $this->db->query("SELECT id FROM chart_of_accounts WHERE account_code = '6100' OR (account_type = 'Expense' AND account_name LIKE 'Bank Fees%') LIMIT 1");
            $feesAccount = $this->db->single();
            $feesCoaId = $feesAccount ? $feesAccount->id : null;
        }

        $ref = "LOAN-DISB-" . $loanId;
        $this->db->query("SELECT id FROM journal_entries WHERE reference = :ref LIMIT 1");
        $this->db->bind(':ref', $ref);
        $oldJe = $this->db->single();
        if ($oldJe) {
            $this->db->query("DELETE FROM transactions WHERE journal_entry_id = :je_id");
            $this->db->bind(':je_id', $oldJe->id);
            $this->db->execute();

            $this->db->query("DELETE FROM journal_entries WHERE id = :je_id");
            $this->db->bind(':je_id', $oldJe->id);
            $this->db->execute();
        }

        $this->db->query("INSERT INTO journal_entries (entry_date, reference, description, created_by, status) VALUES (:date, :ref, :desc, :uid, 'Posted')");
        $this->db->bind(':date', date('Y-m-d'));
        $this->db->bind(':ref', $ref);
        $this->db->bind(':desc', "Loan Disbursement - " . $loan->lender_name . ($loan->loan_number ? " ({$loan->loan_number})" : ""));
        $this->db->bind(':uid', $userId);
        $this->db->execute();
        
        $jeId = $this->db->lastInsertId();
        
        $netBankAmount = floatval($principalAmount) - floatval($fees);
        
        // Debit Bank
        $this->db->query("INSERT INTO transactions (journal_entry_id, account_id, debit, credit, description) VALUES (:je_id, :acc, :debit, 0, :desc)");
        $this->db->bind(':je_id', $jeId);
        $this->db->bind(':acc', $bankCoaId);
        $this->db->bind(':debit', $netBankAmount);
        $this->db->bind(':desc', "Loan Funds Received - " . $loan->lender_name);
        $this->db->execute();

        // Debit Fees if applicable
        if (floatval($fees) > 0 && $feesCoaId) {
            $this->db->query("INSERT INTO transactions (journal_entry_id, account_id, debit, credit, description) VALUES (:je_id, :acc, :debit, 0, :desc)");
            $this->db->bind(':je_id', $jeId);
            $this->db->bind(':acc', $feesCoaId);
            $this->db->bind(':debit', floatval($fees));
            $this->db->bind(':desc', "Loan Processing Fees - " . $loan->lender_name);
            $this->db->execute();
        }

        // Credit Liability
        $this->db->query("INSERT INTO transactions (journal_entry_id, account_id, debit, credit, description) VALUES (:je_id, :acc, 0, :credit, :desc)");
        $this->db->bind(':je_id', $jeId);
        $this->db->bind(':acc', $liabilityCoaId);
        $this->db->bind(':credit', $principalAmount);
        $this->db->bind(':desc', "Loan Principal Liability - " . $loan->lender_name);
        $this->db->execute();
    }

    public function addRepayment($data) {
        $this->db->beginTransaction();
        try {
            $loan = $this->getLoanById($data['loan_id']);
            if (!$loan) {
                throw new Exception("Loan not found.");
            }

            $totalPayment = floatval($data['principal_amount']) + floatval($data['interest_amount']) + floatval($data['bank_charges']) + floatval($data['penalty_charges'] ?? 0);
            $bankingDate = !empty($data['banking_date']) ? $data['banking_date'] : (!empty($data['cheque_date']) ? $data['cheque_date'] : $data['payment_date']);
            
            $this->db->query("INSERT INTO loan_repayments 
                (loan_id, payment_date, principal_amount, interest_amount, bank_charges, penalty_charges, 
                 early_settlement_rebate, total_payment, payment_method, cheque_number, cheque_date, banking_date,
                 bank_account_id, reference, notes, created_by) 
                VALUES 
                (:loan_id, :payment_date, :principal, :interest, :charges, :penalty, 
                 :rebate, :total, :method, :chq_no, :chq_date, :banking_date,
                 :bank_acc, :ref, :notes, :user_id)");
            
            $this->db->bind(':loan_id', $data['loan_id']);
            $this->db->bind(':payment_date', $data['payment_date']);
            $this->db->bind(':principal', $data['principal_amount']);
            $this->db->bind(':interest', $data['interest_amount']);
            $this->db->bind(':charges', $data['bank_charges']);
            $this->db->bind(':penalty', floatval($data['penalty_charges'] ?? 0));
            $this->db->bind(':rebate', floatval($data['early_settlement_rebate'] ?? 0));
            $this->db->bind(':total', $totalPayment);
            $this->db->bind(':method', !empty($data['payment_method']) ? $data['payment_method'] : 'Bank Transfer');
            $this->db->bind(':chq_no', !empty($data['cheque_number']) ? $data['cheque_number'] : null);
            $this->db->bind(':chq_date', !empty($data['cheque_date']) ? $data['cheque_date'] : null);
            $this->db->bind(':banking_date', $bankingDate);
            $this->db->bind(':bank_acc', $data['bank_account_id']);
            $this->db->bind(':ref', $data['reference']);
            $this->db->bind(':notes', $data['notes']);
            $this->db->bind(':user_id', $data['created_by']);
            $this->db->execute();
            
            $repaymentId = $this->db->lastInsertId();

            // If payment method is Cheque, record in cheques table as Issued Cheque
            if (!empty($data['payment_method']) && $data['payment_method'] === 'Cheque') {
                $bankName = 'Bank Account';
                if (!empty($data['bank_account_id'])) {
                    $this->db->query("SELECT account_name FROM chart_of_accounts WHERE id = :bid");
                    $this->db->bind(':bid', $data['bank_account_id']);
                    $bRow = $this->db->single();
                    if ($bRow) $bankName = $bRow->account_name;
                }

                $cNum = !empty($data['cheque_number']) ? trim($data['cheque_number']) : ('LR-' . $repaymentId);
                $payeeName = !empty($loan->lender_name) ? $loan->lender_name : 'Loan Lender';

                $this->db->query("INSERT INTO cheques 
                    (type, payee_name, bank_name, cheque_number, amount, banking_date, status, bank_account_id, loan_repayment_id, loan_id, created_by)
                    VALUES 
                    ('Issued', :payee, :bank, :cnum, :amt, :bdate, 'Pending', :bid, :lrid, :lid, :uid)");
                $this->db->bind(':payee', $payeeName);
                $this->db->bind(':bank', $bankName);
                $this->db->bind(':cnum', $cNum);
                $this->db->bind(':amt', $totalPayment);
                $this->db->bind(':bdate', $bankingDate);
                $this->db->bind(':bid', $data['bank_account_id']);
                $this->db->bind(':lrid', $repaymentId);
                $this->db->bind(':lid', $loan->id);
                $this->db->bind(':uid', $data['created_by']);
                $this->db->execute();
            }
            
            // Sync GL Journal Entry
            $jeId = $this->syncRepaymentAccounting(
                $repaymentId, 
                $loan, 
                $data['bank_account_id'], 
                $data['principal_amount'], 
                $data['interest_amount'], 
                $data['bank_charges'], 
                floatval($data['penalty_charges'] ?? 0),
                floatval($data['early_settlement_rebate'] ?? 0),
                $totalPayment, 
                $data['payment_date'],
                $data['reference'],
                $data['payment_method'] ?? 'Bank Transfer',
                $data['created_by']
            );
            
            // Link JE back to repayment
            if ($jeId) {
                $this->db->query("UPDATE loan_repayments SET journal_entry_id = :je_id WHERE id = :id");
                $this->db->bind(':je_id', $jeId);
                $this->db->bind(':id', $repaymentId);
                $this->db->execute();
            }

            // Check if fully settled
            $freshLoan = $this->getLoanById($data['loan_id']);
            if ($freshLoan && $freshLoan->principal_balance <= 0) {
                $this->db->query("UPDATE loans SET status = 'Closed' WHERE id = :id");
                $this->db->bind(':id', $freshLoan->id);
                $this->db->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("addRepayment failed: " . $e->getMessage());
            return false;
        }
    }

    private function syncRepaymentAccounting($repaymentId, $loan, $bankAccountId, $principal, $interest, $charges, $penalties, $rebate, $total, $paymentDate, $reference, $paymentMethod, $userId) {
        $bankCoaId = $bankAccountId;
        $liabilityCoaId = $loan->liability_account_id;

        // Resolve Bank Charges Account
        $bankChargesCoaId = !empty($loan->bank_charges_account_id) ? $loan->bank_charges_account_id : null;
        if (!$bankChargesCoaId) {
            $this->db->query("SELECT id FROM chart_of_accounts WHERE account_code = '6100' OR (account_type = 'Expense' AND account_name LIKE 'Bank Fees%') LIMIT 1");
            $bankChargesAcc = $this->db->single();
            $bankChargesCoaId = $bankChargesAcc ? $bankChargesAcc->id : null;
        }

        // Resolve Interest Expense Account (Prioritize loan's configured account, then loan-specific search, then fallback)
        $intExpCoaId = !empty($loan->interest_expense_account_id) ? $loan->interest_expense_account_id : null;
        if (!$intExpCoaId) {
            // Check if lender name contains keywords matching existing COA (e.g. LOLC)
            if (stripos($loan->lender_name, 'LOLC') !== false) {
                $this->db->query("SELECT id FROM chart_of_accounts WHERE account_code = '6037' OR account_name LIKE '%LOLC Loan%' LIMIT 1");
                $matchedCoa = $this->db->single();
                if ($matchedCoa) $intExpCoaId = $matchedCoa->id;
            }
            if (!$intExpCoaId) {
                $this->db->query("SELECT id FROM chart_of_accounts WHERE account_code = '6150' OR (account_type = 'Expense' AND account_name LIKE 'Interest Expense%') LIMIT 1");
                $intExpAcc = $this->db->single();
                if ($intExpAcc) {
                    $intExpCoaId = $intExpAcc->id;
                } else {
                    $this->db->query("INSERT INTO chart_of_accounts (account_code, account_name, account_type, account_category, is_active) 
                                      VALUES ('6150', 'Interest Expense', 'Expense', 'Financial Expense', 1)");
                    $this->db->execute();
                    $intExpCoaId = $this->db->lastInsertId();
                }
            }
        }

        $ref = "LOAN-REP-" . $repaymentId;
        $desc = "Loan Repayment [{$paymentMethod}] - " . $loan->lender_name . ($reference ? " (Ref: {$reference})" : "");
        
        $this->db->query("INSERT INTO journal_entries (entry_date, reference, description, created_by, status) VALUES (:date, :ref, :desc, :uid, 'Posted')");
        $this->db->bind(':date', $paymentDate);
        $this->db->bind(':ref', $ref);
        $this->db->bind(':desc', $desc);
        $this->db->bind(':uid', $userId);
        $this->db->execute();
        
        $jeId = $this->db->lastInsertId();

        // 1. Credit Bank or Cash (Actual cash outflow)
        if ($total > 0) {
            $paymentTypeDesc = ($paymentMethod === 'Cash') ? 'Cash Payment' : (($paymentMethod === 'Cheque') ? 'Cheque Payment' : 'Bank Transfer');
            $this->db->query("INSERT INTO transactions (journal_entry_id, account_id, debit, credit, description) VALUES (:je_id, :acc, 0, :credit, :desc)");
            $this->db->bind(':je_id', $jeId);
            $this->db->bind(':acc', $bankCoaId);
            $this->db->bind(':credit', $total);
            $this->db->bind(':desc', "{$paymentTypeDesc}: " . $loan->lender_name);
            $this->db->execute();
        }

        // 2. Debit Principal Liability
        $principalDebit = floatval($principal);
        if ($principalDebit > 0) {
            $this->db->query("INSERT INTO transactions (journal_entry_id, account_id, debit, credit, description) VALUES (:je_id, :acc, :debit, 0, :desc)");
            $this->db->bind(':je_id', $jeId);
            $this->db->bind(':acc', $liabilityCoaId);
            $this->db->bind(':debit', $principalDebit);
            $this->db->bind(':desc', "Principal Repayment - " . $loan->lender_name);
            $this->db->execute();
        }

        // 3. Debit Interest Expense
        if (floatval($interest) > 0 && $intExpCoaId) {
            $this->db->query("INSERT INTO transactions (journal_entry_id, account_id, debit, credit, description) VALUES (:je_id, :acc, :debit, 0, :desc)");
            $this->db->bind(':je_id', $jeId);
            $this->db->bind(':acc', $intExpCoaId);
            $this->db->bind(':debit', floatval($interest));
            $this->db->bind(':desc', "Interest Payment - " . $loan->lender_name);
            $this->db->execute();
        }

        // 4. Debit Charges & Penalties
        $totalCharges = floatval($charges) + floatval($penalties);
        if ($totalCharges > 0 && $bankChargesCoaId) {
            $this->db->query("INSERT INTO transactions (journal_entry_id, account_id, debit, credit, description) VALUES (:je_id, :acc, :debit, 0, :desc)");
            $this->db->bind(':je_id', $jeId);
            $this->db->bind(':acc', $bankChargesCoaId);
            $this->db->bind(':debit', $totalCharges);
            $this->db->bind(':desc', "Bank Charges & Penalties - " . $loan->lender_name);
            $this->db->execute();
        }

        // 5. Early settlement rebate (Credit Other Income or discount)
        if (floatval($rebate) > 0) {
            $this->db->query("SELECT id FROM chart_of_accounts WHERE account_type = 'Revenue' AND (account_name LIKE '%Discount%' OR account_name LIKE '%Other Income%') LIMIT 1");
            $rebateAcc = $this->db->single();
            $rebateCoaId = $rebateAcc ? $rebateAcc->id : null;
            if ($rebateCoaId) {
                // Debit liability for the rebate portion settled without payment
                $this->db->query("INSERT INTO transactions (journal_entry_id, account_id, debit, credit, description) VALUES (:je_id, :acc, :debit, 0, :desc)");
                $this->db->bind(':je_id', $jeId);
                $this->db->bind(':acc', $liabilityCoaId);
                $this->db->bind(':debit', floatval($rebate));
                $this->db->bind(':desc', "Principal Rebate Settlement - " . $loan->lender_name);
                $this->db->execute();

                // Credit Other Income
                $this->db->query("INSERT INTO transactions (journal_entry_id, account_id, debit, credit, description) VALUES (:je_id, :acc, 0, :credit, :desc)");
                $this->db->bind(':je_id', $jeId);
                $this->db->bind(':acc', $rebateCoaId);
                $this->db->bind(':credit', floatval($rebate));
                $this->db->bind(':desc', "Loan Settlement Rebate/Discount - " . $loan->lender_name);
                $this->db->execute();
            }
        }

        return $jeId;
    }

    public function deleteRepayment($repaymentId, $userId) {
        $this->db->query("SELECT * FROM loan_repayments WHERE id = :id LIMIT 1");
        $this->db->bind(':id', $repaymentId);
        $rep = $this->db->single();
        if (!$rep) return false;

        $this->db->beginTransaction();
        try {
            // Revert linked Journal Entry & COA balances safely via voidEntry
            $jeId = !empty($rep->journal_entry_id) ? intval($rep->journal_entry_id) : 0;
            if ($jeId <= 0) {
                $ref = "LOAN-REP-" . $repaymentId;
                $this->db->query("SELECT id FROM journal_entries WHERE reference = :ref LIMIT 1");
                $this->db->bind(':ref', $ref);
                $oldJe = $this->db->single();
                if ($oldJe) $jeId = intval($oldJe->id);
            }
            if ($jeId > 0) {
                require_once __DIR__ . '/JournalEntry.php';
                $jeModel = new JournalEntry();
                $jeModel->voidEntry($jeId);

                // Now clean up transaction lines and journal entry
                $this->db->query("DELETE FROM transactions WHERE journal_entry_id = :je_id");
                $this->db->bind(':je_id', $jeId);
                $this->db->execute();

                $this->db->query("DELETE FROM journal_entries WHERE id = :je_id");
                $this->db->bind(':je_id', $jeId);
                $this->db->execute();
            }

            // Delete any linked cheque in cheques table
            $this->db->query("DELETE FROM cheques WHERE loan_repayment_id = :id");
            $this->db->bind(':id', $repaymentId);
            $this->db->execute();

            // Delete the repayment record
            $this->db->query("DELETE FROM loan_repayments WHERE id = :id");
            $this->db->bind(':id', $repaymentId);
            $this->db->execute();

            // Recheck loan status: if closed and now has balance, reopen to Active
            $freshLoan = $this->getLoanById($rep->loan_id);
            if ($freshLoan && $freshLoan->status === 'Closed' && $freshLoan->principal_balance > 0) {
                $this->db->query("UPDATE loans SET status = 'Active' WHERE id = :id");
                $this->db->bind(':id', $freshLoan->id);
                $this->db->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("deleteRepayment failed: " . $e->getMessage());
            return false;
        }
    }

    public function getRepayments($loanId) {
        $this->db->query("
            SELECT 
                r.id, r.loan_id, r.journal_entry_id, r.payment_date, r.principal_amount, 
                r.interest_amount, r.bank_charges, r.penalty_charges, r.early_settlement_rebate, 
                r.total_payment, r.payment_method, r.cheque_number, r.cheque_date, r.banking_date,
                r.bank_account_id, r.reference, r.notes, r.created_by, r.created_at,
                coa.account_name as bank_name, coa.account_code as account_number,
                u.username as created_by_username
            FROM loan_repayments r
            LEFT JOIN chart_of_accounts coa ON r.bank_account_id = coa.id
            LEFT JOIN users u ON r.created_by = u.id
            WHERE r.loan_id = :loan_id 
            ORDER BY r.payment_date DESC, r.id DESC
        ");
        $this->db->bind(':loan_id', $loanId);
        return $this->db->resultSet() ?: [];
    }

    public function getDashboardStats() {
        $stats = new stdClass();
        
        $this->db->query("
            SELECT 
                COUNT(id) as count, 
                COALESCE(SUM(CASE WHEN is_historical_opening = 1 AND opening_balance > 0 THEN opening_balance ELSE principal_amount END), 0) as total_principal 
            FROM loans 
            WHERE status = 'Active'
        ");
        $active = $this->db->single();
        
        $this->db->query("
            SELECT 
                COALESCE(SUM(lr.principal_amount), 0) as paid_principal, 
                COALESCE(SUM(lr.interest_amount), 0) as paid_interest,
                COALESCE(SUM(lr.bank_charges + COALESCE(lr.penalty_charges, 0)), 0) as paid_charges,
                COALESCE(SUM(COALESCE(lr.early_settlement_rebate, 0)), 0) as paid_rebates
            FROM loan_repayments lr 
            JOIN loans l ON lr.loan_id = l.id 
            WHERE l.status = 'Active'
        ");
        $paid = $this->db->single();

        $this->db->query("SELECT COUNT(id) as count FROM loans WHERE status = 'Pending'");
        $pending = $this->db->single();

        $this->db->query("SELECT COUNT(id) as count FROM loans WHERE status = 'Closed'");
        $closed = $this->db->single();
        
        $stats->active_loans = $active ? intval($active->count) : 0;
        $stats->pending_loans = $pending ? intval($pending->count) : 0;
        $stats->closed_loans = $closed ? intval($closed->count) : 0;
        $stats->total_principal = $active ? floatval($active->total_principal) : 0.00;
        $stats->paid_principal = $paid ? floatval($paid->paid_principal) : 0.00;
        $stats->paid_interest = $paid ? floatval($paid->paid_interest) : 0.00;
        $stats->paid_charges = $paid ? floatval($paid->paid_charges) : 0.00;
        $stats->paid_rebates = $paid ? floatval($paid->paid_rebates) : 0.00;
        $stats->outstanding_principal = max(0.00, $stats->total_principal - $stats->paid_principal - $stats->paid_rebates);
        
        return $stats;
    }
}
