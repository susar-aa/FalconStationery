<?php
class ExpensesController extends Controller {
    private $vendorModel;
    private $expenseModel;
    private $coaModel;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) { header('Location: ' . APP_URL . '/auth/login'); exit; }
        $this->vendorModel = $this->model('Supplier');
        $this->expenseModel = $this->model('Expense');
        $this->coaModel = $this->model('ChartOfAccount');
    }

    public function index() {
        $filters = [
            'search' => trim($_GET['search'] ?? ''),
            'payment_method' => trim($_GET['payment_method'] ?? 'All'),
            'date_from' => trim($_GET['date_from'] ?? ''),
            'date_to' => trim($_GET['date_to'] ?? '')
        ];

        $accounts = $this->coaModel->getAccounts();
        $expense_accounts = array_filter($accounts, function($a) { return $a->account_type == 'Expense' && (!isset($a->is_active) || $a->is_active == 1); });
        
        $bankParentId = $this->coaModel->selfHealBankAccounts();
        $bank_accounts = $this->coaModel->getBankAccounts($bankParentId);

        // Fetch accounts for default COA options
        $cashInHandAcc = $this->coaModel->getAccountByCode('1000');
        $pettyCashAcc = $this->coaModel->getAccountByCode('1020');
        $apCreditAcc = $this->coaModel->getAccountByCode('2000');

        $data = [
            'title' => 'Expenses & AP Management',
            'content_view' => 'expenses/index',
            'expenses' => $this->expenseModel->getAllExpenses($filters),
            'stats' => $this->expenseModel->getExpenseStats(),
            'vendors' => $this->vendorModel->getActiveVendors(),
            'expense_accounts' => $expense_accounts,
            'bank_accounts' => $bank_accounts,
            'cash_in_hand_acc' => $cashInHandAcc,
            'petty_cash_acc' => $pettyCashAcc,
            'ap_credit_acc' => $apCreditAcc,
            'filters' => $filters,
            'error' => '',
            'success' => ''
        ];

        // Handle POST creation directly from modal/page
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_expense') {
            $expenseData = [
                'vendor_id' => $_POST['vendor_id'] ?? '',
                'reference' => trim($_POST['reference'] ?? ''),
                'expense_date' => $_POST['expense_date'] ?? date('Y-m-d'),
                'amount' => floatval($_POST['amount'] ?? 0),
                'payment_method' => $_POST['payment_method'] ?? 'Cash',
                'description' => trim($_POST['description'] ?? '')
            ];
            $expenseAccount = intval($_POST['expense_account'] ?? 0);
            $paymentAccount = intval($_POST['payment_account'] ?? 0);

            if ($expenseData['amount'] <= 0) {
                $data['error'] = 'Amount must be greater than zero.';
            } else if ($expenseAccount <= 0 || $paymentAccount <= 0) {
                $data['error'] = 'Please select valid Debit and Credit accounts for accounting entries.';
            } else {
                $result = $this->expenseModel->createExpenseWithAccounting($expenseData, $expenseAccount, $paymentAccount, $_SESSION['user_id']);
                if ($result === true) {
                    header('Location: ' . APP_URL . '/expenses?success=1');
                    exit;
                } else {
                    $data['error'] = is_string($result) ? $result : 'Failed to record expense.';
                }
            }
        }

        $this->view('layouts/main', $data);
    }

    public function create() {
        $this->index();
    }
}