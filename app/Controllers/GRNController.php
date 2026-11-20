<?php
class GRNController extends Controller {
    private $vendorModel;
    private $grnModel;
    private $poModel;
    private $itemModel;
    private $companyModel;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) { header('Location: ' . APP_URL . '/auth/login'); exit; }
        $this->vendorModel = $this->model('Supplier');
        $this->grnModel = $this->model('GRN');
        $this->poModel = $this->model('PurchaseOrder');
        $this->itemModel = $this->model('Item');
        $this->companyModel = $this->model('Company');
    }

    public function index() {
        $this->checkPermission('grn', 'view');
        $csrfToken = $this->generateCsrfToken();

        $search = trim($_GET['search'] ?? '');
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = isset($_GET['per_page']) && in_array((int)$_GET['per_page'], [15, 25, 50, 100]) ? (int)$_GET['per_page'] : 15;
        $offset = ($page - 1) * $limit;

        $filters = [
            'vendor_id' => $_GET['vendor_id'] ?? '',
            'status'    => $_GET['status'] ?? '',
            'period'    => $_GET['period'] ?? ''
        ];

        // Handle Delete GRN
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_grn') {
            $this->validateCsrfOrDie();
            $this->checkPermission('grn', 'delete');

            $grnId = intval($_POST['grn_id']);
            $grn = $this->grnModel->getGRNById($grnId);
            $grnItems = $this->grnModel->getGRNItems($grnId);
            $oldValues = [
                'grn' => $grn,
                'items' => $grnItems
            ];

            $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || 
                      (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

            try {
                if ($this->grnModel->deleteGRN($grnId)) {
                    $grnNum = $grn ? $grn->grn_number : $grnId;
                    $this->logActivity('GRN Deleted', 'Inventory', "GRN draft '{$grnNum}' deleted.", $grnId, $oldValues, null);

                    if ($isAjax) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => true, 'message' => "GRN draft '{$grnNum}' deleted successfully."]);
                        exit;
                    }
                    header("Location: " . APP_URL . "/grn?success=" . urlencode("GRN draft deleted successfully."));
                    exit;
                } else {
                    if ($isAjax) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'message' => 'Failed to delete GRN draft.']);
                        exit;
                    }
                    header("Location: " . APP_URL . "/grn?error=" . urlencode("Failed to delete GRN draft."));
                    exit;
                }
            } catch (Exception $e) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                    exit;
                }
                header("Location: " . APP_URL . "/grn?error=" . urlencode($e->getMessage()));
                exit;
            }
        }

        $totalGRNs = $this->grnModel->getTotalGRNs($search, $filters);
        
        $data = [
            'title' => 'Goods Receipt Notes (GRN)',
            'content_view' => 'grns/index',
            'grns' => $this->grnModel->getGRNsPaginated($search, $limit, $offset, $filters),
            'vendors' => $this->vendorModel->getActiveVendors(),
            'search' => $search,
            'filters' => $filters,
            'page' => $page,
            'per_page' => $limit,
            'total_items' => $totalGRNs,
            'total_pages' => ceil($totalGRNs / $limit),
            'csrf_token' => $csrfToken,
            'error' => '',
            'success' => $_GET['success'] ?? ''
        ];
        $this->view('layouts/main', $data);
    }

    public function show($id = null) {
        if (!$id) { header('Location: ' . APP_URL . '/grn'); exit; }
        $grn = $this->grnModel->getGRNById($id);
        if (!$grn) { die("GRN not found."); }

        $data = [
            'grn' => $grn,
            'items' => $this->grnModel->getGRNItems($id),
            'company' => $this->companyModel->getSettings()
        ];
        $this->view('grns/view', $data);
    }

    public function create() {
        $this->checkPermission('grn', 'create_edit');
        $csrfToken = $this->generateCsrfToken();

        $poId = $_GET['po_id'] ?? null;
        $prefilledVendor = '';
        $prefilledItems = [];
        $linkedPO = null;

        if ($poId) {
            $linkedPO = $this->poModel->getPOById($poId);
            $prefilledVendor = $linkedPO->vendor_id;
            $prefilledItems = $this->poModel->getPOItems($poId);
        }

        $catalogItems = $this->itemModel->getAllItems();
        $itemIds = array_map(function($i) { return (int)$i->id; }, $catalogItems);
        $variationsByItem = $this->itemModel->getVariationsForItems($itemIds);
        foreach($catalogItems as $item) {
            $item->variations = $variationsByItem[$item->id] ?? [];
        }

        $db = new Database();
        $db->query("SELECT id FROM goods_receipt_notes ORDER BY id DESC LIMIT 1");
        $lastRow = $db->single();
        $nextId = $lastRow ? ($lastRow->id + 1) : 1;

        $itemSupplierMappings = $this->itemModel->getAllItemSupplierMappings();

        $data = [
            'title' => 'Create Goods Receipt Note (GRN)',
            'content_view' => 'grns/create',
            'vendors' => $this->vendorModel->getActiveVendors(),
            'catalog_items' => $catalogItems,
            'item_supplier_mappings' => $itemSupplierMappings,
            'grn_number' => str_pad((string)$nextId, 5, '0', STR_PAD_LEFT),
            'prefilled_vendor' => $prefilledVendor,
            'linked_po' => $linkedPO,
            'prefilled_items' => $prefilledItems,
            'csrf_token' => $csrfToken,
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'save_grn') {
            // Check for max_input_vars truncation safety gate
            $maxInputVars = intval(ini_get('max_input_vars'));
            $postCount = count($_POST, COUNT_RECURSIVE);
            if ($maxInputVars > 0 && $postCount >= $maxInputVars) {
                try {
                    $this->logActivity('System Warning', 'Inventory', "POST variables count ({$postCount}) reached PHP limit max_input_vars ({$maxInputVars}) during GRN creation. Submission blocked.", null, null, null);
                } catch (Exception $e) {}
                
                $data['error'] = "Data Safety Gate: The GRN is too large to save safely on this server. PHP limit (max_input_vars = {$maxInputVars}) reached. Please contact your system administrator to increase max_input_vars to 10000.";
            } else {
            $grnData = [
                'vendor_id' => $_POST['vendor_id'],
                'po_id' => !empty($_POST['po_id']) ? $_POST['po_id'] : null,
                'grn_number' => $_POST['grn_number'],
                'receipt_number' => trim($_POST['receipt_number'] ?? '') ?: null,
                'grn_date' => $_POST['grn_date'],
                'notes' => trim($_POST['notes'])
            ];
            
            $items = [];
            if (isset($_POST['item_selection'])) {
                for ($i = 0; $i < count($_POST['item_selection']); $i++) {
                    $selection = $_POST['item_selection'][$i];
                    if (!empty($selection) && $_POST['qty'][$i] > 0 && $_POST['price'][$i] >= 0) {
                        list($itemId, $varOptId) = explode('|', $selection);
                        $items[] = [
                            'item_id' => $itemId,
                            'var_opt_id' => ($varOptId === '0') ? null : $varOptId,
                            'desc' => $_POST['desc'][$i],
                            'qty' => $_POST['qty'][$i],
                            'price' => $_POST['price'][$i],
                            'selling_price' => floatval($_POST['selling_price'][$i] ?? 0),
                            'wholesale_price' => floatval($_POST['wholesale_price'][$i] ?? 0),
                            'retail_margin' => floatval($_POST['retail_margin'][$i] ?? 0),
                            'wholesale_margin' => floatval($_POST['wholesale_margin'][$i] ?? 0)
                        ];
                    }
                }
            }

             if (empty($items)) { $data['error'] = 'You must add at least one item to receive.'; } 
             else {
                 try {
                     $grnId = $this->grnModel->createGRN($grnData, $items, $_SESSION['user_id']);
                     if ($grnId) {
                         // Auto-link / update supplier item relationship for future purchases
                         foreach ($items as $itm) {
                             if (!empty($itm['item_id'])) {
                                 $this->itemModel->linkItemToSupplier($itm['item_id'], $grnData['vendor_id'], $itm['price']);
                             }
                         }

                         $newValues = [
                             'grn' => $grnData,
                             'items' => $items
                         ];
                          $this->logActivity('GRN Created', 'Inventory', "GRN '{$grnData['grn_number']}' created, pending approval.", $grnId, null, $newValues);
                          header('Location: ' . APP_URL . '/grn?success=' . urlencode('GRN created successfully. Supplier-product links updated. Pending admin approval.')); exit; 
                      } else { $data['error'] = 'Database Error: Failed to create GRN.'; }
                 } catch (Exception $e) {
                     $data['error'] = 'Database Error: Failed to create GRN. Details: ' . $e->getMessage();
                 }
             }
         }
        }
        $this->view('layouts/main', $data);
    }

    public function edit($id = null) {
        $this->checkPermission('grn', 'create_edit');
        $csrfToken = $this->generateCsrfToken();

        if (!$id) { header('Location: ' . APP_URL . '/grn'); exit; }
        $grn = $this->grnModel->getGRNById($id);
        if (!$grn) { die("GRN not found."); }
        if ($grn->is_approved) {
            header('Location: ' . APP_URL . '/grn?error=' . urlencode('Approved Goods Receipt Notes cannot be edited.'));
            exit;
        }

        $catalogItems = $this->itemModel->getAllItems();
        $itemIds = array_map(function($i) { return (int)$i->id; }, $catalogItems);
        $variationsByItem = $this->itemModel->getVariationsForItems($itemIds);
        foreach($catalogItems as $item) {
            $item->variations = $variationsByItem[$item->id] ?? [];
        }
        $itemSupplierMappings = $this->itemModel->getAllItemSupplierMappings();

        $data = [
            'title' => 'Edit Goods Receipt Note (GRN)',
            'content_view' => 'grns/create',
            'vendors' => $this->vendorModel->getAllVendors(),
            'catalog_items' => $catalogItems,
            'item_supplier_mappings' => $itemSupplierMappings,
            'grn' => $grn,
            'prefilled_vendor' => $grn->vendor_id,
            'prefilled_items' => $this->grnModel->getGRNItems($id),
            'linked_po' => $grn->po_id ? $this->poModel->getPOById($grn->po_id) : null,
            'csrf_token' => $csrfToken,
            'error' => '',
            'success' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_grn') {
            // Check for max_input_vars truncation safety gate
            $maxInputVars = intval(ini_get('max_input_vars'));
            $postCount = count($_POST, COUNT_RECURSIVE);
            if ($maxInputVars > 0 && $postCount >= $maxInputVars) {
                try {
                    $this->logActivity('System Warning', 'Inventory', "POST variables count ({$postCount}) reached PHP limit max_input_vars ({$maxInputVars}) during GRN update. Submission blocked.", null, null, null);
                } catch (Exception $e) {}
                
                $data['error'] = "Data Safety Gate: The GRN is too large to save safely on this server. PHP limit (max_input_vars = {$maxInputVars}) reached. Please contact your system administrator to increase max_input_vars to 10000.";
            } else {
            $grnData = [
                'vendor_id' => $_POST['vendor_id'],
                'receipt_number' => trim($_POST['receipt_number'] ?? '') ?: null,
                'grn_date' => $_POST['grn_date'],
                'notes' => trim($_POST['notes'])
            ];
            
            $items = [];
            if (isset($_POST['item_selection'])) {
                for ($i = 0; $i < count($_POST['item_selection']); $i++) {
                    $selection = $_POST['item_selection'][$i];
                    if (!empty($selection) && $_POST['qty'][$i] > 0 && $_POST['price'][$i] >= 0) {
                        list($itemId, $varOptId) = explode('|', $selection);
                        $items[] = [
                            'item_id' => $itemId,
                            'var_opt_id' => ($varOptId === '0') ? null : $varOptId,
                            'desc' => $_POST['desc'][$i],
                            'qty' => $_POST['qty'][$i],
                            'price' => $_POST['price'][$i],
                            'selling_price' => floatval($_POST['selling_price'][$i] ?? 0),
                            'wholesale_price' => floatval($_POST['wholesale_price'][$i] ?? 0),
                            'retail_margin' => floatval($_POST['retail_margin'][$i] ?? 0),
                            'wholesale_margin' => floatval($_POST['wholesale_margin'][$i] ?? 0)
                        ];
                    }
                }
            }

            if (empty($items)) {
                $data['error'] = 'You must add at least one item to receive.';
            } else {
                try {
                    if ($this->grnModel->updateGRN($id, $grnData, $items, $_SESSION['user_id'])) {
                        // Auto-link / update supplier item relationship
                        foreach ($items as $itm) {
                            if (!empty($itm['item_id'])) {
                                $this->itemModel->linkItemToSupplier($itm['item_id'], $grnData['vendor_id'], $itm['price']);
                            }
                        }

                        $newValues = [
                            'grn' => $grnData,
                            'items' => $items
                        ];
                        $this->logActivity('GRN Updated', 'Inventory', "GRN '{$grn->grn_number}' updated.", $id, null, $newValues);
                        header('Location: ' . APP_URL . '/grn?success=' . urlencode('GRN updated successfully. Supplier-product links updated.')); exit;
                    } else {
                        $data['error'] = 'Database Error: Failed to update GRN.';
                    }
                } catch (Exception $e) {
                    $data['error'] = 'Database Error: Failed to update GRN. Details: ' . $e->getMessage();
                }
            }
        }
       }
        $this->view('layouts/main', $data);
    }

    public function approve($id = null) {
        if (!$id) { header('Location: ' . APP_URL . '/grn'); exit; }
        
        $this->validateCsrfOrDie();

        $role = strtolower($_SESSION['role'] ?? '');
        if ($role !== 'admin') {
            header('Location: ' . APP_URL . '/grn?error=Unauthorized: Only administrators can approve GRNs.');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['password'])) {
            header('Location: ' . APP_URL . '/grn?error=Password verification is required for approval.');
            exit;
        }

        $userModel = $this->model('User');
        $adminUser = $userModel->findUserByUsername($_SESSION['username']);
        if (!$adminUser || !password_verify($_POST['password'], $adminUser->password_hash)) {
            header('Location: ' . APP_URL . '/grn?error=Invalid administrator password. Approval failed.');
            exit;
        }

        try {
            if ($this->grnModel->approveGRN($id, $_SESSION['user_id'])) {
                $grn = $this->grnModel->getGRNById($id);
                $grnNum = $grn ? $grn->grn_number : $id;
                $this->logActivity('GRN Approved', 'Inventory', "GRN '{$grnNum}' approved and stock updated.", $id, null, null);
                header("Location: " . APP_URL . "/grn?success=GRN approved and stock updated successfully.");
                exit;
            } else {
                header("Location: " . APP_URL . "/grn?error=Failed to approve GRN.");
                exit;
            }
        } catch (Exception $e) {
            header("Location: " . APP_URL . "/grn?error=" . urlencode($e->getMessage()));
            exit;
        }
    }
}