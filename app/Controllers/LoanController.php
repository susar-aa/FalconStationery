<?php
class LoanController extends Controller {
    private $loanModel;
    private $coaModel;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . APP_URL . '/auth/login');
            exit;
        }
        
        $this->loanModel = $this->model('Loan');
        $this->coaModel = $this->model('ChartOfAccount');
    }

    public function index() {
        $status = isset($_GET['status']) && $_GET['status'] !== 'all' ? trim($_GET['status']) : null;
        $search = isset($_GET['search']) ? trim($_GET['search']) : null;

        $data = [
            'title' => 'Bank Loans',
            'content_view' => 'loans/index',
            'stats' => $this->loanModel->getDashboardStats(),
            'loans' => $this->loanModel->getLoans($status, $search),
            'current_status' => $status ?: 'all',
            'search' => $search ?: ''
        ];
        $this->view('layouts/main', $data);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $isHistorical = isset($_POST['is_historical_opening']) && $_POST['is_historical_opening'] == '1';
            $principal = floatval($_POST['principal_amount'] ?? 0);
            $openingBal = $isHistorical ? floatval($_POST['opening_balance'] ?? $principal) : $principal;

            $data = [
                'lender_name' => trim($_POST['lender_name'] ?? ''),
                'loan_number' => trim($_POST['loan_number'] ?? ''),
                'loan_type' => trim($_POST['loan_type'] ?? 'Term Loan'),
                'principal_amount' => $principal,
                'monthly_installment' => floatval($_POST['monthly_installment'] ?? 0),
                'interest_rate' => floatval($_POST['interest_rate'] ?? 0),
                'interest_type' => trim($_POST['interest_type'] ?? 'Flat Rate'),
                'loan_start_date' => $_POST['loan_start_date'] ?? date('Y-m-d'),
                'loan_term_months' => !empty($_POST['loan_term_months']) ? intval($_POST['loan_term_months']) : null,
                'repayment_frequency' => $_POST['repayment_frequency'] ?? 'Monthly',
                'first_payment_date' => !empty($_POST['first_payment_date']) ? $_POST['first_payment_date'] : null,
                'maturity_date' => !empty($_POST['maturity_date']) ? $_POST['maturity_date'] : null,
                'liability_account_id' => intval($_POST['liability_account_id'] ?? 0),
                'interest_expense_account_id' => !empty($_POST['interest_expense_account_id']) ? intval($_POST['interest_expense_account_id']) : null,
                'bank_charges_account_id' => !empty($_POST['bank_charges_account_id']) ? intval($_POST['bank_charges_account_id']) : null,
                'is_historical_opening' => $isHistorical ? 1 : 0,
                'opening_balance' => $openingBal,
                'notes' => trim($_POST['notes'] ?? '')
            ];

            if (empty($data['lender_name']) || $data['principal_amount'] <= 0 || empty($data['liability_account_id'])) {
                header('Location: ' . APP_URL . '/loan/create?error=validation');
                exit;
            }
            
            $loanId = $this->loanModel->addLoan($data);
            if ($loanId) {
                header('Location: ' . APP_URL . '/loan/show/' . $loanId . '?success=created');
                exit;
            } else {
                header('Location: ' . APP_URL . '/loan/create?error=failed');
                exit;
            }
        } else {
            $liabilities = $this->loanModel->getLiabilityAccounts();
            $expenses = $this->loanModel->getExpenseAccounts();
            
            $data = [
                'title' => 'Register New Loan',
                'content_view' => 'loans/create',
                'liabilities' => $liabilities,
                'expenses' => $expenses
            ];
            $this->view('layouts/main', $data);
        }
    }

    public function edit($id) {
        $loan = $this->loanModel->getLoanById($id);
        if (!$loan) {
            header('Location: ' . APP_URL . '/loan?error=notfound');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'id' => $id,
                'lender_name' => trim($_POST['lender_name'] ?? ''),
                'loan_number' => trim($_POST['loan_number'] ?? ''),
                'loan_type' => trim($_POST['loan_type'] ?? 'Term Loan'),
                'principal_amount' => floatval($_POST['principal_amount'] ?? $loan->principal_amount),
                'monthly_installment' => floatval($_POST['monthly_installment'] ?? 0),
                'interest_rate' => floatval($_POST['interest_rate'] ?? 0),
                'interest_type' => trim($_POST['interest_type'] ?? 'Flat Rate'),
                'loan_start_date' => $_POST['loan_start_date'] ?? $loan->loan_start_date,
                'loan_term_months' => !empty($_POST['loan_term_months']) ? intval($_POST['loan_term_months']) : null,
                'repayment_frequency' => $_POST['repayment_frequency'] ?? 'Monthly',
                'first_payment_date' => !empty($_POST['first_payment_date']) ? $_POST['first_payment_date'] : null,
                'maturity_date' => !empty($_POST['maturity_date']) ? $_POST['maturity_date'] : null,
                'liability_account_id' => intval($_POST['liability_account_id'] ?? $loan->liability_account_id),
                'interest_expense_account_id' => !empty($_POST['interest_expense_account_id']) ? intval($_POST['interest_expense_account_id']) : null,
                'bank_charges_account_id' => !empty($_POST['bank_charges_account_id']) ? intval($_POST['bank_charges_account_id']) : null,
                'opening_balance' => floatval($_POST['opening_balance'] ?? $loan->opening_balance),
                'notes' => trim($_POST['notes'] ?? '')
            ];
            
            if ($this->loanModel->updateLoan($data)) {
                header('Location: ' . APP_URL . '/loan/show/' . $id . '?success=updated');
                exit;
            } else {
                header('Location: ' . APP_URL . '/loan/edit/' . $id . '?error=failed');
                exit;
            }
        } else {
            $liabilities = $this->loanModel->getLiabilityAccounts();
            $expenses = $this->loanModel->getExpenseAccounts();
            
            $data = [
                'title' => 'Edit Loan: ' . htmlspecialchars($loan->lender_name),
                'content_view' => 'loans/edit',
                'loan' => $loan,
                'liabilities' => $liabilities,
                'expenses' => $expenses
            ];
            $this->view('layouts/main', $data);
        }
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->loanModel->deleteLoan($id)) {
                header('Location: ' . APP_URL . '/loan?success=deleted');
            } else {
                header('Location: ' . APP_URL . '/loan?error=delete_failed_active');
            }
            exit;
        }
    }

    public function show($id) {
        $loan = $this->loanModel->getLoanById($id);
        if (!$loan) {
            header('Location: ' . APP_URL . '/loan?error=notfound');
            exit;
        }
        
        $data = [
            'title' => 'Loan Details: ' . htmlspecialchars($loan->lender_name),
            'content_view' => 'loans/show',
            'loan' => $loan,
            'repayments' => $this->loanModel->getRepayments($id),
            'bank_accounts' => $this->loanModel->getAllBankAccounts(),
            'cash_accounts' => $this->loanModel->getAllCashAccounts()
        ];
        $this->view('layouts/main', $data);
    }

    public function disburse($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $bankAccountId = intval($_POST['bank_account_id'] ?? 0);
            $fees = floatval($_POST['processing_fees'] ?? 0);
            
            if ($bankAccountId <= 0) {
                header('Location: ' . APP_URL . '/loan/show/' . $id . '?error=invalid_bank');
                exit;
            }

            if ($this->loanModel->disburseLoan($id, $bankAccountId, $fees, $_SESSION['user_id'])) {
                header('Location: ' . APP_URL . '/loan/show/' . $id . '?success=disbursed');
            } else {
                header('Location: ' . APP_URL . '/loan/show/' . $id . '?error=failed');
            }
            exit;
        }
    }

    public function repay($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $method = trim($_POST['payment_method'] ?? 'Bank Transfer');
            if (!in_array($method, ['Bank Transfer', 'Standing Order', 'Direct Debit', 'Cheque', 'Cash'])) {
                $method = 'Bank Transfer';
            }

            if ($method === 'Cheque' && empty(trim($_POST['cheque_number'] ?? ''))) {
                header('Location: ' . APP_URL . '/loan/show/' . $id . '?error=missing_cheque_number');
                exit;
            }

            $data = [
                'loan_id' => $id,
                'payment_date' => $_POST['payment_date'] ?? date('Y-m-d'),
                'principal_amount' => floatval($_POST['principal_amount'] ?? 0),
                'interest_amount' => floatval($_POST['interest_amount'] ?? 0),
                'bank_charges' => floatval($_POST['bank_charges'] ?? 0),
                'penalty_charges' => floatval($_POST['penalty_charges'] ?? 0),
                'early_settlement_rebate' => floatval($_POST['early_settlement_rebate'] ?? 0),
                'payment_method' => $method,
                'cheque_number' => trim($_POST['cheque_number'] ?? ''),
                'cheque_date' => !empty($_POST['cheque_date']) ? $_POST['cheque_date'] : null,
                'banking_date' => !empty($_POST['banking_date']) ? $_POST['banking_date'] : (!empty($_POST['cheque_date']) ? $_POST['cheque_date'] : ($_POST['payment_date'] ?? date('Y-m-d'))),
                'bank_account_id' => intval($_POST['bank_account_id'] ?? 0),
                'reference' => trim($_POST['reference'] ?? ''),
                'notes' => trim($_POST['notes'] ?? ''),
                'created_by' => $_SESSION['user_id']
            ];
            
            if ($data['bank_account_id'] <= 0 || ($data['principal_amount'] <= 0 && $data['interest_amount'] <= 0 && $data['early_settlement_rebate'] <= 0)) {
                header('Location: ' . APP_URL . '/loan/show/' . $id . '?error=invalid_repayment');
                exit;
            }

            if ($this->loanModel->addRepayment($data)) {
                header('Location: ' . APP_URL . '/loan/show/' . $id . '?success=repayment');
            } else {
                header('Location: ' . APP_URL . '/loan/show/' . $id . '?error=failed');
            }
            exit;
        }
    }

    public function delete_repayment($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $loanId = intval($_POST['loan_id'] ?? 0);
            
            if ($this->loanModel->deleteRepayment($id, $_SESSION['user_id'])) {
                header('Location: ' . APP_URL . '/loan/show/' . $loanId . '?success=repayment_deleted');
            } else {
                header('Location: ' . APP_URL . '/loan/show/' . $loanId . '?error=delete_failed');
            }
            exit;
        }
    }
}
