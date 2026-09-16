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

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'account_id' => intval($_POST['account_id']),
                'statement_date_start' => trim($_POST['statement_date_start']),
                'statement_date_end' => trim($_POST['statement_date_end']),
                'statement_opening_balance' => floatval($_POST['statement_opening_balance']),
                'statement_closing_balance' => floatval($_POST['statement_closing_balance'])
            ];

            $recId = $this->recModel->createDraft($data);
            if ($recId) {
                header('Location: ' . URLROOT . '/bankreconciliation/reconcile/' . $recId);
                exit;
            } else {
                flash('rec_message', 'Failed to create reconciliation draft', 'alert alert-danger');
                header('Location: ' . URLROOT . '/bankreconciliation/create');
                exit;
            }
        }
    }

    public function reconcile($id) {
        $reconciliation = $this->recModel->getReconciliationById($id);
        if (!$reconciliation) {
            flash('rec_message', 'Reconciliation record not found', 'alert alert-danger');
            header('Location: ' . URLROOT . '/bankreconciliation');
            exit;
        }

        $this->recModel->populateReconciliationItems($id, $reconciliation->account_id, $reconciliation->statement_date_end);
        $this->recModel->calculateBalances($id);
        $reconciliation = $this->recModel->getReconciliationById($id);

        $items = $this->recModel->getItems($id);
        $accounts = $this->coaModel->getAccounts();

        $data = [
            'content_view' => 'bank_reconciliation/reconcile',
            'reconciliation' => $reconciliation,
            'items' => $items,
            'accounts' => $accounts
        ];

        $this->view('layouts/main', $data);
    }

    public function match_ajax() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $itemId = intval($_POST['item_id'] ?? 0);
            $isMatched = intval($_POST['is_matched'] ?? 0);

            $success = $this->recModel->toggleMatchItem($itemId, $isMatched);
            if ($success) {
                echo json_encode(['status' => 'success']);
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
}
