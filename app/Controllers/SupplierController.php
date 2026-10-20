<?php
class SupplierController extends Controller {
    private $supplierModel;
    private $coaModel;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) { 
            header('Location: ' . APP_URL . '/auth/login'); 
            exit; 
        }
        $this->supplierModel = $this->model('Supplier');
        $this->coaModel = $this->model('ChartOfAccount');
    }

    public function index($id = null) {
        $suppliers = $this->supplierModel->getAllSuppliers();
        
        $selectedSupplier = null;
        $stats = null;
        $ledger = [];
        $pos = [];
        $products = [];

        // Pre-fetch Accounts for the Payment Modal
        $accounts = $this->coaModel->getAccounts();
        $assets = array_filter($accounts, function($a) { return $a->account_type == 'Asset'; });
        $liabilities = array_filter($accounts, function($a) { return $a->account_type == 'Liability'; });
        
        $apAccount = null;
        foreach($liabilities as $acc) {
            if (strpos(strtolower($acc->account_name), 'payable') !== false) {
                $apAccount = $acc; break;
            }
        }

        if ($id) {
            $selectedSupplier = $this->supplierModel->getSupplierById($id);
            if ($selectedSupplier) {
                $stats = $this->supplierModel->getSupplierStats($id);
                $ledger = $this->supplierModel->getActivityLedger($id);
                $pos = $this->supplierModel->getSupplierPOs($id);
                $products = $this->supplierModel->getSupplierProducts($id);
            }
        }

        $data = [
            'title' => 'Supplier Center',
            'content_view' => 'suppliers/index',
            'suppliers' => $suppliers,
            'selected_supplier' => $selectedSupplier,
            'stats' => $stats,
            'ledger' => $ledger,
            'pos' => $pos,
            'products' => $products,
            'assets' => $assets,
            'ap_account' => $apAccount,
            'error' => '',
            'success' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
            if ($_POST['action'] == 'add_supplier') {
                $supplierData = [
                    'name' => trim($_POST['name'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'phone' => trim($_POST['phone'] ?? ''),
                    'address' => trim($_POST['address'] ?? ''),
                    'opening_balance' => isset($_POST['opening_balance']) ? floatval($_POST['opening_balance']) : 0.00,
                    'opening_balance_date' => !empty($_POST['opening_balance_date']) ? $_POST['opening_balance_date'] : date('Y-m-d'),
                    'status' => trim($_POST['status'] ?? 'Active')
                ];
                if (!empty($supplierData['name'])) {
                    if ($this->supplierModel->addSupplier($supplierData)) {
                        header('Location: ' . APP_URL . '/supplier?success=added');
                        exit;
                    } else {
                        $data['error'] = 'Failed to add supplier.';
                    }
                } else {
                    $data['error'] = 'Supplier name is required.';
                }
            } elseif ($_POST['action'] == 'update_supplier') {
                $updateData = [
                    'id' => intval($_POST['supplier_id']),
                    'name' => trim($_POST['name'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'phone' => trim($_POST['phone'] ?? ''),
                    'address' => trim($_POST['address'] ?? ''),
                    'opening_balance' => isset($_POST['opening_balance']) ? floatval($_POST['opening_balance']) : 0.00,
                    'opening_balance_date' => !empty($_POST['opening_balance_date']) ? $_POST['opening_balance_date'] : date('Y-m-d'),
                    'status' => trim($_POST['status'] ?? 'Active')
                ];

                if (!empty($updateData['name'])) {
                    if ($this->supplierModel->updateSupplier($updateData)) {
                        header('Location: ' . APP_URL . '/supplier/index/' . $updateData['id'] . '?success=updated');
                        exit;
                    } else {
                        $data['error'] = 'Failed to update supplier.';
                    }
                }
            }
        }

        if (isset($_GET['success'])) {
            if ($_GET['success'] == 'added') {
                $data['success'] = "Supplier registered successfully!";
            } elseif ($_GET['success'] == 'updated') {
                $data['success'] = "Supplier profile updated successfully!";
            }
        }

        $this->view('layouts/main', $data);
    }

    /**
     * Export all suppliers to a standard CSV file with financial totals.
     */
    public function exportCSV() {
        if (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="suppliers_export_' . date('Y-m-d_His') . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        
        // Write UTF-8 BOM for Excel compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Column headers matching standard supplier attributes and balances
        fputcsv($output, [
            'Supplier ID',
            'Company Name',
            'Email',
            'Phone',
            'Address',
            'Status',
            'Opening Balance',
            'Opening Balance Date',
            'Total Billed',
            'Total Paid',
            'Total Returned',
            'Outstanding Balance'
        ]);

        $suppliers = $this->supplierModel->getAllSuppliers();

        foreach ($suppliers as $s) {
            fputcsv($output, [
                $s->id,
                $s->name,
                $s->email ?? '',
                $s->phone ?? '',
                $s->address ?? '',
                $s->status ?? 'Active',
                number_format(floatval($s->opening_balance ?? 0), 2, '.', ''),
                $s->opening_balance_date ?? '',
                number_format(floatval($s->total_billed ?? 0), 2, '.', ''),
                number_format(floatval($s->total_paid ?? 0), 2, '.', ''),
                number_format(floatval($s->total_returned ?? 0), 2, '.', ''),
                number_format(floatval($s->outstanding_balance ?? 0), 2, '.', '')
            ]);
        }

        fclose($output);
        exit;
    }
}

