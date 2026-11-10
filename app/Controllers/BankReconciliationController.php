<?php
class BankReconciliationController extends Controller {
    private $recModel;
    private $coaModel;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/users/login');
            exit;
        }
        $this->recModel = $this->model('BankReconciliation');
        $this->coaModel = $this->model('ChartOfAccount');
    }

    public function index() {
        $reconciliations = $this->recModel->getAllReconciliations();
        $data = [
            'content_view' => 'bank_reconciliation/index',
            'reconciliations' => $reconciliations
        ];
        $this->view('layouts/main', $data);
    }

    public function create() {
        $bankAccounts = $this->recModel->getBankAccounts();
        $data = [
            'content_view' => 'bank_reconciliation/create',
            'bankAccounts' => $bankAccounts
        ];
        $this->view('layouts/main', $data);
    }

    /**
     * AJAX endpoint: fetch confirmed opening balance and recommended dates for selected bank account
     */
    public function get_account_info_ajax() {
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $accountId = intval($_POST['account_id'] ?? 0);
                $startDate = trim($_POST['statement_date_start'] ?? '');
                $endDate = trim($_POST['statement_date_end'] ?? '');

                if ($accountId <= 0 || empty($startDate) || empty($endDate)) {
                    $_SESSION['flash_error'] = 'Please select a bank account and statement date period.';
                    header('Location: ' . URLROOT . '/bankreconciliation/create');
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
                    header('Location: ' . URLROOT . '/bankreconciliation/reconcile/' . $recId);
                    exit;
                } else {
                    $_SESSION['flash_error'] = 'Failed to create reconciliation workspace.';
                    header('Location: ' . URLROOT . '/bankreconciliation/create');
                    exit;
                }
            } catch (Throwable $e) {
                error_log("store reconciliation error: " . $e->getMessage());
                $_SESSION['flash_error'] = 'Failed to create reconciliation: ' . $e->getMessage();
                header('Location: ' . URLROOT . '/bankreconciliation/create');
                exit;
            }
        }
    }

    public function reconcile($id) {
        try {
            $id = intval($id);
            $reconciliation = $this->recModel->getReconciliationById($id);
            if (!$reconciliation) {
                $_SESSION['flash_error'] = 'Reconciliation record not found.';
                header('Location: ' . URLROOT . '/bankreconciliation');
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
            header('Location: ' . URLROOT . '/bankreconciliation');
            exit;
        }
    }

    public function match_ajax() {
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
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $recId = intval($_POST['reconciliation_id']);
            $postData = [
                'transaction_date' => $_POST['transaction_date'],
                'type' => $_POST['type'],
                'amount' => floatval($_POST['amount']),
                'opposite_account_id' => intval($_POST['opposite_account_id']),
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
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $recId = intval($_POST['reconciliation_id']);
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
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $recId = intval($_POST['reconciliation_id']);
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
        $reconciliation = $this->recModel->getReconciliationById($id);
        if (!$reconciliation) {
            flash('rec_message', 'Reconciliation record not found', 'alert alert-danger');
            header('Location: ' . URLROOT . '/bankreconciliation');
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
        $userId = $_SESSION['user_id'] ?? 1;
        $result = $this->recModel->deleteReconciliation($id, $userId);
        if ($result['status']) {
            $_SESSION['flash_success'] = $result['message'];
            if (function_exists('flash')) {
                flash('rec_message', $result['message'], 'alert alert-success');
            }
        } else {
            $_SESSION['flash_error'] = $result['message'];
            if (function_exists('flash')) {
                flash('rec_message', $result['message'], 'alert alert-danger');
            }
        }
        header('Location: ' . URLROOT . '/bankreconciliation');
        exit;
    }
}
