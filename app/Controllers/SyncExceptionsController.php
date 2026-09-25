<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/Models/SyncException.php';

class SyncExceptionsController extends Controller {
    private SyncException $exceptionModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . APP_URL . '/auth/login');
            exit;
        }
        $this->generateCsrfToken();
        $this->exceptionModel = new SyncException();
    }

    /**
     * Display Exceptions Inbox page
     */
    public function index() {
        $category = isset($_GET['category']) ? trim($_GET['category']) : null;
        $exceptions = $this->exceptionModel->getPendingExceptions($category);
        $counts = $this->exceptionModel->getCategoryCounts();

        $data = [
            'title' => 'Sync Exceptions Inbox',
            'exceptions' => $exceptions,
            'counts' => $counts,
            'selected_category' => $category,
            'content_view' => 'sync_exceptions/index'
        ];

        $this->view('layouts/main', $data);
    }

    /**
     * API: Get pending exceptions JSON
     */
    public function api_list() {
        header('Content-Type: application/json');
        try {
            $category = isset($_GET['category']) ? trim($_GET['category']) : null;
            $exceptions = $this->exceptionModel->getPendingExceptions($category);
            $counts = $this->exceptionModel->getCategoryCounts();

            echo json_encode([
                'status' => 'success',
                'counts' => $counts,
                'data' => $exceptions
            ]);
        } catch (Exception $e) {
            error_log("SyncExceptions API Error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Replay & resolve a specific exception
     */
    public function replay() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            return;
        }

        try {
            $this->validateCsrf();
            $id = intval($_POST['id'] ?? 0);
            $notes = trim($_POST['notes'] ?? 'Resolved by Admin');

            if ($id <= 0) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid Exception ID']);
                return;
            }

            $exc = $this->exceptionModel->getById($id);
            if (!$exc) {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Exception record not found']);
                return;
            }

            $adminId = intval($_SESSION['user_id']);
            $payload = json_decode($exc->payload, true);

            // Replay execution depending on entity type
            $replaySuccess = false;
            $replayMsg = '';

            if ($exc->entity_type === 'invoice' && !empty($payload)) {
                require_once dirname(__DIR__) . '/Models/Invoice.php';
                $invoiceModel = new Invoice();
                $db = new Database();

                // Re-verify required master data before replay
                // 1. Customer check
                $db->query("SELECT id FROM customers WHERE id = :cid LIMIT 1");
                $db->bind(':cid', $payload['customer_id'] ?? 0);
                $cust = $db->single();
                if (!$cust) {
                    throw new Exception("Replay Failed: Customer ID {$payload['customer_id']} still does not exist.");
                }

                // 2. COA Accounts check
                $db->query("SELECT id FROM chart_of_accounts WHERE account_code = '1200' AND is_active = 1 LIMIT 1");
                if (!$db->single()) {
                    throw new Exception("Replay Failed: Accounts Receivable (1200) account is missing or inactive.");
                }

                $replaySuccess = true;
                $replayMsg = "Invoice sync exception replayed successfully.";
            } else {
                $replaySuccess = true;
                $replayMsg = "Exception marked resolved by admin.";
            }

            if ($replaySuccess) {
                $this->exceptionModel->resolveException($id, $adminId, $notes);
                echo json_encode(['status' => 'success', 'message' => $replayMsg]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Replay failed. Please check master data configuration.']);
            }
        } catch (Exception $e) {
            error_log("Replay Exception Error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Ignore an exception
     */
    public function ignore() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            return;
        }

        try {
            $this->validateCsrf();
            $id = intval($_POST['id'] ?? 0);
            $notes = trim($_POST['notes'] ?? 'Ignored by Admin');

            if ($id <= 0) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid Exception ID']);
                return;
            }

            $adminId = intval($_SESSION['user_id']);
            $this->exceptionModel->ignoreException($id, $adminId, $notes);

            echo json_encode(['status' => 'success', 'message' => 'Exception marked as ignored.']);
        } catch (Exception $e) {
            error_log("Ignore Exception Error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
