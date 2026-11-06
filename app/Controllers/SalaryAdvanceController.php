<?php
declare(strict_types=1);

class SalaryAdvanceController extends Controller {
    private SalaryAdvance $salaryAdvanceModel;
    private Database $db;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . APP_URL . '/auth/login');
            exit;
        }

        require_once dirname(__DIR__) . '/Models/SalaryAdvance.php';
        $this->salaryAdvanceModel = new SalaryAdvance();
        $this->db = new Database();
    }

    public function index(): void {
        $this->checkPermission('hrm', 'view');

        $search = trim($_GET['search'] ?? '');
        $employeeId = intval($_GET['employee_id'] ?? 0);
        $status = trim($_GET['status'] ?? 'All');
        $startDate = trim($_GET['start_date'] ?? '');
        $endDate = trim($_GET['end_date'] ?? '');
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = 25;
        $offset = ($page - 1) * $limit;

        $filters = [
            'search' => $search,
            'employee_id' => $employeeId,
            'status' => $status,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];

        $advances = $this->salaryAdvanceModel->getAdvances($filters, $limit, $offset);
        $totalCount = $this->salaryAdvanceModel->getAdvancesCount($filters);
        $totalPages = max(1, (int)ceil($totalCount / $limit));

        $metrics = $this->salaryAdvanceModel->getMetrics();
        $employees = $this->salaryAdvanceModel->getActiveEmployeesWithAccounts();

        // Bank Accounts for Bank Transfer / Cheque dropdown
        $this->db->query("SELECT ba.id, ba.bank_name, ba.account_number, coa.account_name, coa.account_code 
                          FROM bank_accounts ba 
                          JOIN chart_of_accounts coa ON ba.chart_of_account_id = coa.id 
                          WHERE (ba.status = 'Active' OR ba.status IS NULL) 
                          ORDER BY ba.bank_name ASC");
        $bankAccounts = $this->db->resultSet() ?: [];

        $data = [
            'title' => 'Salary Advance Management',
            'content_view' => 'salary_advances/index',
            'advances' => $advances,
            'total_count' => $totalCount,
            'total_pages' => $totalPages,
            'current_page' => $page,
            'filters' => $filters,
            'metrics' => $metrics,
            'employees' => $employees,
            'bank_accounts' => $bankAccounts,
            'success' => $_SESSION['flash_success'] ?? '',
            'error' => $_SESSION['flash_error'] ?? ''
        ];

        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        $this->view('layouts/main', $data);
    }

    public function store(): void {
        $this->checkPermission('hrm', 'create_edit');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/salaryadvance');
            exit;
        }

        $employeeId = intval($_POST['employee_id'] ?? 0);
        $amount = floatval($_POST['advance_amount'] ?? 0);
        $paymentDate = trim($_POST['payment_date'] ?? date('Y-m-d'));
        $paymentMethod = trim($_POST['payment_method'] ?? 'Petty Cash');
        $bankAccountId = intval($_POST['bank_account_id'] ?? 0);
        $referenceNo = trim($_POST['reference_no'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        $result = $this->salaryAdvanceModel->issueAdvance([
            'employee_id' => $employeeId,
            'advance_amount' => $amount,
            'payment_date' => $paymentDate,
            'payment_method' => $paymentMethod,
            'bank_account_id' => $bankAccountId,
            'reference_no' => $referenceNo,
            'notes' => $notes
        ], intval($_SESSION['user_id']));

        if ($result['success']) {
            $_SESSION['flash_success'] = "Salary advance {$result['advance_number']} of Rs. " . number_format($amount, 2) . " issued successfully.";
            header('Location: ' . APP_URL . '/salaryadvance');
        } else {
            $_SESSION['flash_error'] = $result['message'];
            header('Location: ' . APP_URL . '/salaryadvance');
        }
        exit;
    }

    public function cancel(int|string $id): void {
        $this->checkPermission('hrm', 'create_edit');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/salaryadvance');
            exit;
        }

        $advanceId = intval($id);
        $result = $this->salaryAdvanceModel->cancelAdvance($advanceId, intval($_SESSION['user_id']));

        if ($result['success']) {
            $_SESSION['flash_success'] = $result['message'];
        } else {
            $_SESSION['flash_error'] = $result['message'];
        }

        header('Location: ' . APP_URL . '/salaryadvance');
        exit;
    }

    public function voucher(int|string $id): void {
        $this->checkPermission('hrm', 'view');

        $advanceId = intval($id);
        $advance = $this->salaryAdvanceModel->getAdvanceById($advanceId);

        if (!$advance) {
            die("<div style='padding:40px; font-family:sans-serif; text-align:center;'><h3>Advance Not Found</h3><p>The requested salary advance voucher could not be located.</p><a href='" . APP_URL . "/salaryadvance'>Back to Advances</a></div>");
        }

        $recoveries = $this->salaryAdvanceModel->getAdvanceRecoveries($advanceId);
        $amountInWords = $this->numberToWords(floatval($advance->advance_amount));

        $data = [
            'title' => "Advance Voucher - {$advance->advance_number}",
            'advance' => $advance,
            'recoveries' => $recoveries,
            'amount_in_words' => $amountInWords
        ];

        $this->view('salary_advances/voucher', $data);
    }

    public function export_csv(): void {
        $this->checkPermission('hrm', 'view');

        $filters = [
            'search' => trim($_GET['search'] ?? ''),
            'employee_id' => intval($_GET['employee_id'] ?? 0),
            'status' => trim($_GET['status'] ?? 'All'),
            'start_date' => trim($_GET['start_date'] ?? ''),
            'end_date' => trim($_GET['end_date'] ?? '')
        ];

        $advances = $this->salaryAdvanceModel->getAdvances($filters, null, null);

        $filename = 'Salary_Advances_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        fputcsv($output, [
            'Advance Number',
            'Employee Code',
            'Employee Name',
            'Department',
            'Job Title',
            'Date',
            'Amount (Rs.)',
            'Recovered (Rs.)',
            'Outstanding (Rs.)',
            'Payment Method',
            'Source Account',
            'Advance Account',
            'Status',
            'Reference',
            'Created By',
            'Notes'
        ]);

        foreach ($advances as $row) {
            fputcsv($output, [
                $row->advance_number,
                $row->employee_code ?? '',
                trim($row->first_name . ' ' . $row->last_name),
                $row->department ?? '',
                $row->job_title ?? '',
                $row->payment_date,
                number_format(floatval($row->advance_amount), 2, '.', ''),
                number_format(floatval($row->recovered_amount), 2, '.', ''),
                number_format(floatval($row->outstanding_amount), 2, '.', ''),
                $row->payment_method,
                $row->source_account_code . ' - ' . $row->source_account_name,
                $row->advance_account_code . ' - ' . $row->advance_account_name,
                $row->status,
                $row->reference_no ?? '',
                $row->created_by_name ?? '',
                $row->notes ?? ''
            ]);
        }

        fclose($output);
        exit;
    }

    private function numberToWords(float $number): string {
        $decimal = round($number - ($no = floor($number)), 2) * 100;
        $hundred = null;
        $digits_length = strlen((string)$no);
        $i = 0;
        $str = [];
        $words = [
            0 => '', 1 => 'One', 2 => 'Two',
            3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
            7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
            10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
            13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
            16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
            19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
            40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
            70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
        ];
        $digits = ['', 'Hundred', 'Thousand', 'Lakh', 'Crore'];
        while ($i < $digits_length) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += $divider == 10 ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                $str[] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
            } else $str[] = null;
        }
        $rupees = implode('', array_reverse(array_filter($str)));
        $cents = ($decimal > 0) ? " and " . ($words[$decimal / 10] ?? ($words[floor($decimal / 10) * 10] . " " . $words[$decimal % 10])) . ' Cents' : '';
        return trim(($rupees ? $rupees . 'Rupees' : '') . $cents . ' Only');
    }
}

