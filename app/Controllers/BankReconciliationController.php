<?php
class BankReconciliationController extends Controller {
    private $recModel;
    private $coaModel;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . APP_URL . '/auth/login');
            exit;
        }
        $this->generateCsrfToken();
        $this->recModel = $this->model('BankReconciliation');
        $this->coaModel = $this->model('ChartOfAccount');
    }

    public function index() {
        $this->checkPermission('accounting', 'view');
        $reconciliations = $this->recModel->getAllReconciliations();
        $bankAccounts = $this->recModel->getBankAccounts();
        $data = [
            'content_view' => 'bank_reconciliation/index',
            'reconciliations' => $reconciliations,
            'bankAccounts' => $bankAccounts
        ];
        $this->view('layouts/main', $data);
    }

    public function create() {
        $this->checkPermission('accounting', 'create_edit');
        $bankAccounts = $this->recModel->getBankAccounts();
        $selectedAccountId = intval($_GET['account_id'] ?? 0);
        $data = [
            'content_view' => 'bank_reconciliation/create',
            'bankAccounts' => $bankAccounts,
            'selectedAccountId' => $selectedAccountId
        ];
        $this->view('layouts/main', $data);
    }

    /**
     * AJAX endpoint: fetch confirmed opening balance and recommended dates for selected bank account
     */
    public function get_account_info_ajax() {
        $this->checkPermission('accounting', 'view');
        header('Content-Type: application/json');
        $accountId = intval($_GET['account_id'] ?? 0);
        $targetStart = !empty($_GET['start_date']) ? trim($_GET['start_date']) : null;

        if ($accountId <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid bank account.']);
            exit;
        }

        $info = $this->recModel->getNextOpeningBalance($accountId, $targetStart);
        echo json_encode(array_merge(['status' => 'success'], $info));
        exit;
    }

    public function store() {
        $this->checkPermission('accounting', 'create_edit');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrfOrDie();
            try {
                $accountId = intval($_POST['account_id'] ?? 0);
                $startDate = trim($_POST['statement_date_start'] ?? '');
                $endDate = trim($_POST['statement_date_end'] ?? '');

                if ($accountId <= 0 || empty($startDate) || empty($endDate)) {
                    $_SESSION['flash_error'] = 'Please select a bank account and statement date period.';
                    header('Location: ' . APP_URL . '/bankreconciliation/create');
                    exit;
                }

                // Retrieve confirmed opening balance from previous finalized month
                $info = $this->recModel->getNextOpeningBalance($accountId, $startDate);
                
                // If opening balance was submitted manually, use it; otherwise automatically use confirmed
                $openingBal = isset($_POST['statement_opening_balance']) && $_POST['statement_opening_balance'] !== '' 
                    ? floatval($_POST['statement_opening_balance']) 
                    : $info['opening_balance'];

                $data = [
                    'account_id' => $accountId,
                    'statement_date_start' => $startDate,
                    'statement_date_end' => $endDate,
                    'statement_opening_balance' => $openingBal,
                    'statement_closing_balance' => floatval($_POST['statement_closing_balance'] ?? 0)
                ];

                $recId = $this->recModel->createDraft($data);
                if ($recId) {
                    $_SESSION['flash_success'] = 'Reconciliation period created successfully.';
                    header('Location: ' . APP_URL . '/bankreconciliation/reconcile/' . $recId);
                    exit;
                } else {
                    $_SESSION['flash_error'] = 'Failed to create reconciliation workspace.';
                    header('Location: ' . APP_URL . '/bankreconciliation/create');
                    exit;
                }
            } catch (Throwable $e) {
                error_log("store reconciliation error: " . $e->getMessage());
                $_SESSION['flash_error'] = 'Failed to create reconciliation: ' . $e->getMessage();
                header('Location: ' . APP_URL . '/bankreconciliation/create');
                exit;
            }
        }
    }

    public function reconcile($id) {
        $this->checkPermission('accounting', 'view');
        try {
            $id = intval($id);
            $reconciliation = $this->recModel->getReconciliationById($id);
            if (!$reconciliation) {
                $_SESSION['flash_error'] = 'Reconciliation record not found.';
                header('Location: ' . APP_URL . '/bankreconciliation');
                exit;
            }

            if ($reconciliation->status !== 'Finalized') {
                $this->recModel->populateReconciliationItems($id, $reconciliation->account_id, $reconciliation->statement_date_end);
            }

            $summary = $this->recModel->calculateBalances($id);
            $reconciliation = $this->recModel->getReconciliationById($id);

            $items = $this->recModel->getItems($id);
            $accounts = $this->coaModel->getAccounts();

            $data = [
                'content_view' => 'bank_reconciliation/reconcile',
                'reconciliation' => $reconciliation,
                'items' => $items,
                'accounts' => $accounts,
                'summary' => $summary
            ];

            $this->view('layouts/main', $data);
        } catch (Throwable $e) {
            error_log("reconcile workspace error: " . $e->getMessage());
            $_SESSION['flash_error'] = 'Error loading reconciliation workspace: ' . $e->getMessage();
            header('Location: ' . APP_URL . '/bankreconciliation');
            exit;
        }
    }

    public function match_ajax() {
        $this->checkPermission('accounting', 'create_edit');
        $this->validateCsrfOrDie();
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $itemId = intval($_POST['item_id'] ?? 0);
            $isMatched = intval($_POST['is_matched'] ?? 0);

            $summary = $this->recModel->toggleMatchItem($itemId, $isMatched);
            if ($summary !== false) {
                echo json_encode([
                    'status' => 'success',
                    'summary' => $summary
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update match status']);
            }
            exit;
        }
    }

    public function bank_only_ajax() {
        $this->checkPermission('accounting', 'create_edit');
        $this->validateCsrfOrDie();
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $recId = intval($_POST['reconciliation_id'] ?? 0);
            $postData = [
                'transaction_date' => $_POST['transaction_date'] ?? date('Y-m-d'),
                'type' => $_POST['type'] ?? 'BANK_CHARGE',
                'amount' => floatval($_POST['amount'] ?? 0),
                'opposite_account_id' => intval($_POST['opposite_account_id'] ?? 0),
                'reference' => trim($_POST['reference'] ?? ''),
                'description' => trim($_POST['description'] ?? '')
            ];

            $res = $this->recModel->addBankOnlyTransaction($recId, $postData);
            if ($res) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to add bank adjustment entry.']);
            }
            exit;
        }
    }

    public function finalize_ajax() {
        $this->checkPermission('accounting', 'create_edit');
        $this->validateCsrfOrDie();
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $recId = intval($_POST['reconciliation_id'] ?? 0);
            $userId = $_SESSION['user_id'] ?? 1;

            $result = $this->recModel->finalizeReconciliation($recId, $userId);
            if ($result['status']) {
                echo json_encode(['status' => 'success', 'message' => $result['message']]);
            } else {
                echo json_encode(['status' => 'error', 'message' => $result['message']]);
            }
            exit;
        }
    }

    public function reopen_ajax() {
        $this->checkPermission('accounting', 'create_edit');
        $this->validateCsrfOrDie();
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $recId = intval($_POST['reconciliation_id'] ?? 0);
            $userId = $_SESSION['user_id'] ?? 1;

            $result = $this->recModel->reopenReconciliation($recId, $userId);
            if ($result['status']) {
                echo json_encode(['status' => 'success', 'message' => $result['message']]);
            } else {
                echo json_encode(['status' => 'error', 'message' => $result['message']]);
            }
            exit;
        }
    }

    public function report($id) {
        $this->checkPermission('accounting', 'view');
        $id = intval($id);
        $reconciliation = $this->recModel->getReconciliationById($id);
        if (!$reconciliation) {
            $_SESSION['flash_error'] = 'Reconciliation record not found';
            header('Location: ' . APP_URL . '/bankreconciliation');
            exit;
        }

        $items = $this->recModel->getItems($id);

        $data = [
            'reconciliation' => $reconciliation,
            'items' => $items
        ];

        $this->view('bank_reconciliation/report', $data);
    }

    public function delete($id) {
        $this->checkPermission('accounting', 'delete');
        $id = intval($id);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrfOrDie();
        }

        $userId = $_SESSION['user_id'] ?? 1;
        $result = $this->recModel->deleteReconciliation($id, $userId);
        if ($result['status']) {
            $_SESSION['flash_success'] = $result['message'];
        } else {
            $_SESSION['flash_error'] = $result['message'];
        }
        header('Location: ' . APP_URL . '/bankreconciliation');
        exit;
    }
}
