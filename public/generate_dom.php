<?php
define('APP_URL', 'http://localhost');
// We need to bootstrap the app enough to run the controller
require_once __DIR__ . '/../app/Config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Core/Controller.php';
require_once __DIR__ . '/../app/Controllers/PettyCashController.php';

// Mock models and functions
function hasPermission() { return true; }

class MockController extends Controller {
    public function view($view, $data = []) {
        extract($data);
        ob_start();
        require_once __DIR__ . '/../app/Views/' . $view . '.php';
        $html = ob_get_clean();
        file_put_contents(__DIR__ . '/debug_dom_offline.html', $html);
        echo "DOM Saved locally.";
    }
}

// Instantiate and run
$controller = new PettyCashController();
// Instead of $controller->index(), which uses session and flash messages, let's just construct $data manually!
$data = [
    'title' => 'Petty Cash Management',
    'content_view' => 'petty_cash/index',
    'config' => null,
    'ledger_balance' => 0,
    'config_limit' => 50000,
    'available_balance' => 50000,
    'pending_reimbursements' => 1,
    'transactions' => [],
    'reimbursements' => [],
    'pending_expenses' => [],
    'funding_accounts' => [],
    'expense_accounts' => [],
    'users' => [],
    'routes' => [],
    'filters' => [],
    'page' => 1,
    'total_pages' => 1,
    'csrf_token' => 'dummy',
    'error' => '',
    'success' => ''
];
$mock = new MockController();
$mock->view('layouts/main', $data);
