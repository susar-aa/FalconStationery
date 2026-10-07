<?php
declare(strict_types=1);

class EpfEtfController extends Controller {
    private $payrollModel;
    private $employeeModel;
    private $coaModel;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) { 
            header('Location: ' . APP_URL . '/auth/login'); 
            exit; 
        }
        $this->payrollModel = $this->model('Payroll');
        $this->employeeModel = $this->model('Employee');
        $this->coaModel = $this->model('ChartOfAccount');
    }

    public function index() {
        $this->checkPermission('hrm', 'view');

        $year = isset($_GET['year']) && $_GET['year'] !== '' ? intval($_GET['year']) : intval(date('Y'));
        $month = isset($_GET['month']) && $_GET['month'] !== '' ? intval($_GET['month']) : null;
        $status = isset($_GET['status']) && $_GET['status'] !== '' ? trim($_GET['status']) : null;

        $liabilities = $this->payrollModel->getEpfEtfLiabilities($year, $month, $status);
        $paymentHistory = $this->payrollModel->getEpfEtfPaymentHistory();
        $accounts = $this->coaModel->getAccounts();

        // Bank Accounts for payment selection
        $bankAccounts = array_filter($accounts, function($a) {
            return $a->account_type == 'Asset' && (
                $a->account_code >= '1000' && $a->account_code < '1200' || 
                stripos($a->account_name, 'bank') !== false || 
                stripos($a->account_name, 'cash') !== false
            );
        });

        $data = [
            'title' => 'EPF & ETF Management',
            'content_view' => 'hrm/epf_etf_index',
            'liabilities' => $liabilities,
            'payment_history' => $paymentHistory,
            'bank_accounts' => $bankAccounts,
            'selected_year' => $year,
            'selected_month' => $month,
            'selected_status' => $status,
            'error' => $_SESSION['flash_error'] ?? '',
            'success' => $_SESSION['flash_success'] ?? ''
        ];

        unset($_SESSION['flash_error'], $_SESSION['flash_success']);

        $this->view('layouts/main', $data);
    }

    public function pay() {
        $this->checkPermission('hrm', 'create_edit');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $payrollRunId = intval($_POST['payroll_run_id'] ?? 0);
                $paymentType = trim($_POST['payment_type'] ?? '');
                $paymentDate = trim($_POST['payment_date'] ?? date('Y-m-d'));
                $amountPaid = floatval($_POST['amount_paid'] ?? 0);
                $bankAccountId = intval($_POST['bank_account_id'] ?? 0);
                $paymentRef = trim($_POST['payment_reference'] ?? '');
                $notes = trim($_POST['notes'] ?? '');

                if ($payrollRunId <= 0 || !in_array($paymentType, ['EPF', 'ETF']) || $amountPaid <= 0 || $bankAccountId <= 0) {
                    $_SESSION['flash_error'] = 'Invalid payment submission. Please fill in all required fields.';
                    header('Location: ' . APP_URL . '/epfetf');
                    exit;
                }

                $paymentData = [
                    'payroll_run_id' => $payrollRunId,
                    'payment_type' => $paymentType,
                    'payment_date' => $paymentDate,
                    'amount_paid' => $amountPaid,
                    'bank_account_id' => $bankAccountId,
                    'payment_reference' => $paymentRef,
                    'notes' => $notes
                ];

                $res = $this->payrollModel->recordEpfEtfPayment($paymentData, intval($_SESSION['user_id']));

                if ($res['status']) {
                    $_SESSION['flash_success'] = $res['message'];
                } else {
                    $_SESSION['flash_error'] = $res['message'];
                }

            } catch (Throwable $e) {
                error_log("EPF/ETF Pay error: " . $e->getMessage());
                $_SESSION['flash_error'] = 'Failed to process EPF/ETF payment: ' . $e->getMessage();
            }
        }

        header('Location: ' . APP_URL . '/epfetf');
        exit;
    }

    public function reports() {
        $this->checkPermission('hrm', 'view');

        $reportType = $_GET['type'] ?? 'monthly_epf';
        $payrollRunId = isset($_GET['run_id']) ? intval($_GET['run_id']) : 0;
        $employeeId = isset($_GET['employee_id']) ? intval($_GET['employee_id']) : 0;
        $year = isset($_GET['year']) ? intval($_GET['year']) : intval(date('Y'));

        $runs = $this->payrollModel->getAllPayrollRuns();
        $employees = $this->employeeModel->getAllEmployees();

        if ($payrollRunId <= 0 && !empty($runs)) {
            $payrollRunId = intval($runs[0]->id);
        }

        $reportData = [];
        if ($reportType === 'monthly_epf') {
            $reportData = $this->payrollModel->getMonthlyEpfReport($payrollRunId);
        } elseif ($reportType === 'monthly_etf') {
            $reportData = $this->payrollModel->getMonthlyEtfReport($payrollRunId);
        } elseif ($reportType === 'employee_history') {
            $reportData = $this->payrollModel->getEmployeeContributionHistory($employeeId, $year);
        } elseif ($reportType === 'employer_summary') {
            $reportData = $this->payrollModel->getEmployerContributionSummary($year);
        } elseif ($reportType === 'payable_report') {
            $reportData = $this->payrollModel->getEpfEtfPayableReport($year);
        }

        $data = [
            'title' => 'EPF & ETF Compliance Reports',
            'content_view' => 'hrm/epf_etf_reports',
            'report_type' => $reportType,
            'payroll_runs' => $runs,
            'employees' => $employees,
            'selected_run_id' => $payrollRunId,
            'selected_employee_id' => $employeeId,
            'selected_year' => $year,
            'report_data' => $reportData
        ];

        $this->view('layouts/main', $data);
    }
}
