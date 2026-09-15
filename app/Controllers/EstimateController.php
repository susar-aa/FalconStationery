<?php
class EstimateController extends Controller {
    private $customerModel;
    private $estimateModel;
    private $itemModel;
    private $companyModel;
    private $coaModel;
    private $invoiceModel;

    private $db;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) { header('Location: ' . APP_URL . '/auth/login'); exit; }
        $this->db = new Database();
        $this->customerModel = $this->model('Customer');
        $this->estimateModel = $this->model('Estimate');
        $this->itemModel = $this->model('Item');
        $this->companyModel = $this->model('Company');
        $this->coaModel = $this->model('ChartOfAccount');
        $this->invoiceModel = $this->model('Invoice');
    }

    public function index() {
        $accounts = $this->coaModel->getAccounts();
        $assets = array_filter($accounts, function($a) { return $a->account_type == 'Asset'; });
        $revenues = array_filter($accounts, function($a) { return $a->account_type == 'Revenue'; });

        $data = [
            'title' => 'Estimates & Quotes',
            'content_view' => 'estimates/index',
            'estimates' => $this->estimateModel->getAllEstimates(),
            'customers' => $this->customerModel->getAllCustomers(),
            'assets' => $assets,
            'revenues' => $revenues,
            'error' => '',
            'success' => ''
        ];

        // Handle Status Updates
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_status') {
            $this->estimateModel->updateStatus($_POST['estimate_id'], $_POST['new_status']);
            $data['success'] = "Estimate status updated.";
            $data['estimates'] = $this->estimateModel->getAllEstimates();
        }

        // Handle Conversion to Invoice
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'convert_to_invoice') {
            $estimateId = $_POST['estimate_id'];
            $arAccount = $_POST['ar_account'];
            $revAccount = $_POST['revenue_account'];

            $estimate = $this->estimateModel->getEstimateById($estimateId);
            $estimateItems = $this->estimateModel->getEstimateItems($estimateId);

            if ($estimate && count($estimateItems) > 0) {
                // Map items and calculate subtotal
                $subtotal = 0.00;
                $items = [];
                
                foreach ($estimateItems as $i) {
                    $itemTotal = floatval($i->quantity) * floatval($i->unit_price);
                    $subtotal += $itemTotal;

                    // Try to resolve item_id by searching by description (name)
                    $itemId = 0;
                    $this->db->query("SELECT id FROM items WHERE name = :name LIMIT 1");
                    $this->db->bind(':name', $i->description);
                    $rowItem = $this->db->single();
                    if ($rowItem) {
                        $itemId = $rowItem->id;
                    }

                    $items[] = [
                        'item_selection' => $itemId . '|0',
                        'description' => $i->description,
                        'quantity' => floatval($i->quantity),
                        'unit_price' => floatval($i->unit_price),
                        'discount_value' => 0.00,
                        'discount_type' => 'Rs',
                        'total' => $itemTotal
                    ];
                }

                // Generate new invoice number
                $this->db->query("SELECT id FROM invoices ORDER BY id DESC LIMIT 1");
                $lastRow = $this->db->single();
                $nextId = $lastRow ? ($lastRow->id + 1) : 1;
                $invoiceNumber = str_pad((string)$nextId, 5, '0', STR_PAD_LEFT);

                // Map estimate data to invoice data array
                $invoiceData = [
                    'customer_id' => $estimate->customer_id,
                    'invoice_number' => $invoiceNumber,
                    'invoice_date' => date('Y-m-d'),
                    'due_date' => date('Y-m-d', strtotime('+14 days')),
                    'subtotal' => $subtotal,
                    'global_discount_val' => 0.00,
                    'global_discount_type' => 'Rs',
                    'notes' => 'Converted from Estimate ' . $estimate->estimate_number,
                    'grand_total' => $subtotal,
                    'rep_route_id' => null,
                    'stock_status' => 'deducted'
                ];

                // Push to the true accounting engine
                if ($this->invoiceModel->createInvoiceWithAccounting($invoiceData, $items, $arAccount, $revAccount, $_SESSION['user_id'])) {
                    // Mark estimate as invoiced
                    $this->estimateModel->updateStatus($estimateId, 'Invoiced');
                    header('Location: ' . APP_URL . '/sales?success=1');
                    exit;
                } else {
                    $data['error'] = 'Accounting Engine Error: Failed to generate invoice and ledger entries.';
                }
            }
        }

        $this->view('layouts/main', $data);
    }

    public function create() {
        $this->db->query("SELECT id FROM estimates ORDER BY id DESC LIMIT 1");
        $lastRow = $this->db->single();
        $nextId = $lastRow ? ($lastRow->id + 1) : 1;

        // Fetch all active items
        $this->db->query("SELECT id, name, item_code, sample_code, price, wholesale_price, cost_price, type, variations_json FROM items ORDER BY name ASC");
        $catalogItems = $this->db->resultSet() ?: [];

        // Fetch all variations in ONE batch
        $this->db->query("
            SELECT ivo.id as var_opt_id, ivo.item_id, ivo.sku, ivo.price, ivo.wholesale_price, 
                   v.name as variation_name, vv.value_name
            FROM item_variation_options ivo
            JOIN variations v ON ivo.variation_id = v.id
            JOIN variation_values vv ON ivo.variation_value_id = vv.id
            ORDER BY ivo.item_id ASC, vv.value_name ASC
        ");
        $allVariations = $this->db->resultSet() ?: [];

        $varMap = [];
        foreach ($allVariations as $var) {
            $varMap[$var->item_id][] = $var;
        }

        // Build a lean, clean list of products for frontend autocomplete
        $preparedProducts = [];
        foreach ($catalogItems as $item) {
            $basePrice = 0.00;
            if (isset($item->price) && floatval($item->price) > 0) {
                $basePrice = floatval($item->price);
            } elseif (isset($item->wholesale_price) && floatval($item->wholesale_price) > 0) {
                $basePrice = floatval($item->wholesale_price);
            } elseif (isset($item->cost_price) && floatval($item->cost_price) > 0) {
                $basePrice = floatval($item->cost_price);
            }

            // Add base item
            $preparedProducts[] = [
                'id' => (int)$item->id,
                'name' => (string)$item->name,
                'sku' => (string)($item->item_code ?? ''),
                'sample_code' => (string)($item->sample_code ?? ''),
                'price' => (float)$basePrice,
                'variation' => ''
            ];

            // Add variations if present
            $itemVars = $varMap[$item->id] ?? [];
            if (!empty($itemVars)) {
                foreach ($itemVars as $v) {
                    $vPrice = (isset($v->price) && floatval($v->price) > 0) ? floatval($v->price) : $basePrice;
                    $varLabel = ($v->variation_name ? $v->variation_name . ': ' : '') . $v->value_name;
                    $preparedProducts[] = [
                        'id' => (int)$item->id,
                        'name' => (string)($item->name . ' (' . $varLabel . ')'),
                        'sku' => (string)($v->sku ?: ($item->item_code ?? '')),
                        'sample_code' => (string)($item->sample_code ?? ''),
                        'price' => (float)$vPrice,
                        'variation' => $varLabel
                    ];
                }
            } elseif (!empty($item->variations_json)) {
                $decoded = json_decode($item->variations_json);
                if (is_array($decoded)) {
                    foreach ($decoded as $dec) {
                        $vPrice = (isset($dec->price) && floatval($dec->price) > 0) ? floatval($dec->price) : $basePrice;
                        $varLabel = $dec->attribute ?? ($dec->value_name ?? 'Option');
                        $preparedProducts[] = [
                            'id' => (int)$item->id,
                            'name' => (string)($item->name . ' (' . $varLabel . ')'),
                            'sku' => (string)($dec->sku ?? ($item->item_code ?? '')),
                            'sample_code' => (string)($item->sample_code ?? ''),
                            'price' => (float)$vPrice,
                            'variation' => $varLabel
                        ];
                    }
                }
            }
        }

        $data = [
            'title' => 'Create Estimate',
            'content_view' => 'estimates/create',
            'customers' => $this->customerModel->getAllCustomers(),
            'catalog_items' => $preparedProducts,
            'estimate_number' => str_pad((string)$nextId, 5, '0', STR_PAD_LEFT),
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $estimateData = [
                'customer_id' => $_POST['customer_id'],
                'estimate_number' => $_POST['estimate_number'],
                'date' => $_POST['estimate_date'],
                'expiry_date' => $_POST['expiry_date']
            ];
            
            $items = [];
            for ($i = 0; $i < count($_POST['desc']); $i++) {
                if (!empty($_POST['desc'][$i]) && $_POST['qty'][$i] > 0 && $_POST['price'][$i] >= 0) {
                    $items[] = [
                        'desc' => $_POST['desc'][$i],
                        'qty' => $_POST['qty'][$i],
                        'price' => $_POST['price'][$i]
                    ];
                }
            }

            if (empty($items)) {
                $data['error'] = 'You must add at least one item.';
            } else {
                if ($this->estimateModel->createEstimate($estimateData, $items, $_SESSION['user_id'])) {
                    header('Location: ' . APP_URL . '/estimate?success=1');
                    exit;
                } else {
                    $data['error'] = 'Database Error: Failed to create estimate.';
                }
            }
        }
        $this->view('layouts/main', $data);
    }

        public function edit($id = null) {
        if (!$id) { header('Location: ' . APP_URL . '/estimate'); exit; }

        $estimate = $this->estimateModel->getEstimateById($id);
        if (!$estimate) { die("Estimate not found."); }
        if ($estimate->status === 'Invoiced') { die("Cannot edit an invoiced estimate."); }

        $estimateItems = $this->estimateModel->getEstimateItems($id);

        // Fetch all active items
        $this->db->query("SELECT id, name, item_code, sample_code, price, wholesale_price, cost_price, type, variations_json FROM items ORDER BY name ASC");
        $catalogItems = $this->db->resultSet() ?: [];

        // Fetch all variations in ONE batch
        $this->db->query("
            SELECT ivo.id as var_opt_id, ivo.item_id, ivo.sku, ivo.price, ivo.wholesale_price, 
                   v.name as variation_name, vv.value_name
            FROM item_variation_options ivo
            JOIN variations v ON ivo.variation_id = v.id
            JOIN variation_values vv ON ivo.variation_value_id = vv.id
            ORDER BY ivo.item_id ASC, vv.value_name ASC
        ");
        $allVariations = $this->db->resultSet() ?: [];

        $varMap = [];
        foreach ($allVariations as $var) {
            $varMap[$var->item_id][] = $var;
        }

        // Build a lean, clean list of products for frontend autocomplete
        $preparedProducts = [];
        foreach ($catalogItems as $item) {
            $basePrice = 0.00;
            if (isset($item->price) && floatval($item->price) > 0) {
                $basePrice = floatval($item->price);
            } elseif (isset($item->wholesale_price) && floatval($item->wholesale_price) > 0) {
                $basePrice = floatval($item->wholesale_price);
            } elseif (isset($item->cost_price) && floatval($item->cost_price) > 0) {
                $basePrice = floatval($item->cost_price);
            }

            // Add base item
            $preparedProducts[] = [
                'id' => (int)$item->id,
                'name' => (string)$item->name,
                'sku' => (string)($item->item_code ?? ''),
                'sample_code' => (string)($item->sample_code ?? ''),
                'price' => (float)$basePrice,
                'variation' => ''
            ];

            // Add variations if present
            $itemVars = $varMap[$item->id] ?? [];
            if (!empty($itemVars)) {
                foreach ($itemVars as $v) {
                    $vPrice = (isset($v->price) && floatval($v->price) > 0) ? floatval($v->price) : $basePrice;
                    $varLabel = ($v->variation_name ? $v->variation_name . ': ' : '') . $v->value_name;
                    $preparedProducts[] = [
                        'id' => (int)$item->id,
                        'name' => (string)($item->name . ' (' . $varLabel . ')'),
                        'sku' => (string)($v->sku ?: ($item->item_code ?? '')),
                        'sample_code' => (string)($item->sample_code ?? ''),
                        'price' => (float)$vPrice,
                        'variation' => $varLabel
                    ];
                }
            } elseif (!empty($item->variations_json)) {
                $decoded = json_decode($item->variations_json);
                if (is_array($decoded)) {
                    foreach ($decoded as $dec) {
                        $vPrice = (isset($dec->price) && floatval($dec->price) > 0) ? floatval($dec->price) : $basePrice;
                        $varLabel = $dec->attribute ?? ($dec->value_name ?? 'Option');
                        $preparedProducts[] = [
                            'id' => (int)$item->id,
                            'name' => (string)($item->name . ' (' . $varLabel . ')'),
                            'sku' => (string)($dec->sku ?? ($item->item_code ?? '')),
                            'sample_code' => (string)($item->sample_code ?? ''),
                            'price' => (float)$vPrice,
                            'variation' => $varLabel
                        ];
                    }
                }
            }
        }

        $data = [
            'title' => 'Edit Estimate',
            'content_view' => 'estimates/edit',
            'customers' => $this->customerModel->getAllCustomers(),
            'catalog_items' => $preparedProducts,
            'estimate' => $estimate,
            'estimate_items' => $estimateItems,
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $estimateData = [
                'customer_id' => $_POST['customer_id'],
                'date' => $_POST['estimate_date'],
                'expiry_date' => $_POST['expiry_date']
            ];
            
            $items = [];
            for ($i = 0; $i < count($_POST['desc']); $i++) {
                if (!empty($_POST['desc'][$i]) && $_POST['qty'][$i] > 0 && $_POST['price'][$i] >= 0) {
                    $items[] = [
                        'desc' => $_POST['desc'][$i],
                        'qty' => $_POST['qty'][$i],
                        'price' => $_POST['price'][$i]
                    ];
                }
            }

            if (empty($items)) {
                $data['error'] = 'You must add at least one item.';
            } else {
                if ($this->estimateModel->updateEstimate($id, $estimateData, $items)) {
                    header('Location: ' . APP_URL . '/estimate?success=1');
                    exit;
                } else {
                    $data['error'] = 'Database Error: Failed to update estimate.';
                }
            }
        }
        $this->view('layouts/main', $data);
    }

    public function show($id = null) {
        if (!$id) { header('Location: ' . APP_URL . '/estimate'); exit; }

        $estimate = $this->estimateModel->getEstimateById($id);
        if (!$estimate) { die("Estimate not found."); }

        $data = [
            'estimate' => $estimate,
            'items' => $this->estimateModel->getEstimateItems($id),
            'company' => $this->companyModel->getSettings()
        ];
        $this->view('estimates/show', $data);
    }

    public function download_pdf($id = null) {
        if (!$id) {
            header('Location: ' . APP_URL . '/estimate');
            exit;
        }

        $estimate = $this->estimateModel->getEstimateById($id);
        if (!$estimate) {
            die('Estimate not found.');
        }

        $items = $this->estimateModel->getEstimateItems($id);
        $company = $this->companyModel->getSettings();

        $subTotal = floatval($estimate->total_amount);
        $globalDiscountAmount = 0;
        if (floatval($estimate->global_discount_val) > 0) {
            if ($estimate->global_discount_type == '%') {
                $globalDiscountAmount = $subTotal * (floatval($estimate->global_discount_val) / 100);
            } else {
                $globalDiscountAmount = floatval($estimate->global_discount_val);
            }
        }
        $taxAmount = floatval($estimate->tax_amount ?? 0);
        $grandTotal = $subTotal - $globalDiscountAmount + $taxAmount;

        $logoHtml = '';
        if (!empty($company->logo_path)) {
            $logoFile = 'uploads/' . $company->logo_path;
            if (file_exists($logoFile)) {
                $type = pathinfo($logoFile, PATHINFO_EXTENSION);
                $imgData = file_get_contents($logoFile);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($imgData);
                $logoHtml = '<img src="' . $base64 . '" style="max-height: 55px; margin-bottom: 5px; object-fit: contain;">';
            } else {
                $logoHtml = '<div style="font-size: 14pt; font-weight: bold; text-transform: uppercase;">' . htmlspecialchars($company->company_name) . '</div>';
            }
        } else {
            $logoHtml = '<div style="font-size: 14pt; font-weight: bold; text-transform: uppercase;">' . htmlspecialchars($company->company_name) . '</div>';
        }

        $itemsHtml = '';
        $rowNum = 1;
        foreach ($items as $item) {
            $discText = '-';
            if (floatval($item->discount_value) > 0) {
                $discText = ($item->discount_type == '%') ? ($item->discount_value . '%') : number_format($item->discount_value, 2);
            }
            $itemsHtml .= '
            <tr>
                <td style="text-align: center; padding: 6px 4px; border-bottom: 1px solid #eaeaea; font-size: 8.5pt;">' . $rowNum++ . '</td>
                <td style="padding: 6px 4px; border-bottom: 1px solid #eaeaea; font-size: 8.5pt;">' . htmlspecialchars($item->description) . '</td>
                <td style="text-align: right; padding: 6px 4px; border-bottom: 1px solid #eaeaea; font-size: 8.5pt;">' . number_format($item->quantity, 0) . '</td>
                <td style="text-align: right; padding: 6px 4px; border-bottom: 1px solid #eaeaea; font-size: 8.5pt;">' . number_format($item->unit_price, 2) . '</td>
                <td style="text-align: right; padding: 6px 4px; border-bottom: 1px solid #eaeaea; font-size: 8.5pt;">' . $discText . '</td>
                <td style="text-align: right; padding: 6px 4px; border-bottom: 1px solid #eaeaea; font-size: 8.5pt;">' . number_format($item->total, 2) . '</td>
            </tr>';
        }

        $addressHtml = !empty($company->address) ? nl2br(htmlspecialchars($company->address)) . '<br>' : '';
        $phoneHtml = !empty($company->phone) ? 'Tel: ' . htmlspecialchars($company->phone) . '<br>' : '';
        $emailHtml = !empty($company->email) ? 'Email: ' . htmlspecialchars($company->email) . '<br>' : '';
        $taxHtml = !empty($company->tax_number) ? 'VAT/Tax Reg: ' . htmlspecialchars($company->tax_number) : '';

        $custAddressHtml = !empty($estimate->address) ? nl2br(htmlspecialchars($estimate->address)) . '<br>' : '';
        $custPhoneHtml = !empty($estimate->phone) ? 'Tel: ' . htmlspecialchars($estimate->phone) : '';

        $html = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Estimate ' . htmlspecialchars($estimate->estimate_number) . '</title>
            <style>
                body { font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif; font-size: 8.5pt; color: #000; line-height: 1.3; margin: 0; padding: 0; }
                .invoice-header { width: 100%; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px; }
                .company-info { width: 55%; float: left; }
                .invoice-meta { width: 40%; float: right; text-align: right; }
                .document-title { font-size: 18pt; font-weight: bold; text-transform: uppercase; margin-bottom: 8px; }
                .meta-table { width: 100%; border-collapse: collapse; }
                .meta-table th { text-align: right; font-size: 7.5pt; font-weight: bold; text-transform: uppercase; padding: 2px 10px 2px 0; white-space: nowrap; }
                .meta-table td { text-align: right; font-size: 8.5pt; padding: 2px 0; }
                .customer-section { width: 100%; margin-bottom: 15px; clear: both; }
                .bill-to { width: 48%; float: left; }
                .section-heading { font-size: 8pt; font-weight: bold; text-transform: uppercase; border-bottom: 1px solid #000; padding-bottom: 3px; margin-bottom: 6px; }
                .customer-name { font-size: 10pt; font-weight: bold; margin-bottom: 2px; }
                .table-items { width: 100%; border-collapse: collapse; margin-bottom: 20px; clear: both; }
                .table-items th { border-top: 2px solid #000; border-bottom: 2px solid #000; font-weight: bold; text-transform: uppercase; font-size: 7.5pt; text-align: left; padding: 6px 4px; }
                .table-items td { border-bottom: 1px solid #eaeaea; padding: 6px 4px; }
                .bottom-section { width: 100%; margin-bottom: 20px; clear: both; }
                .payment-info { width: 50%; float: left; font-size: 8pt; border: 1px solid #000; padding: 10px; background-color: #fafafa; box-sizing: border-box; }
                .summary-section { width: 45%; float: right; }
                .table-totals { width: 100%; border-collapse: collapse; }
                .table-totals th { text-align: right; font-weight: normal; color: #444; font-size: 8.5pt; padding: 4px 6px; }
                .table-totals td { text-align: right; font-size: 8.5pt; padding: 4px 6px; }
                .signature-section { width: 100%; margin-top: 30px; clear: both; }
                .signature-box { width: 30%; float: left; text-align: center; }
                .signature-line { border-bottom: 1px solid #000; margin-bottom: 4px; height: 30px; }
                .signature-label { font-size: 7.5pt; font-weight: bold; text-transform: uppercase; }
                .document-footer { width: 100%; border-top: 1px solid #ccc; padding-top: 5px; font-size: 7.5pt; color: #666; position: absolute; bottom: 0; }
            </style>
        </head>
        <body>
            <div class="main-content">
                <div class="invoice-header">
                    <div class="company-info">
                        ' . $logoHtml . '
                        <div style="font-size: 8pt; color: #222; margin-top: 5px;">
                            ' . $addressHtml . '
                            ' . $phoneHtml . '
                            ' . $emailHtml . '
                            ' . $taxHtml . '
                        </div>
                    </div>
                    <div class="invoice-meta">
                        <div class="document-title">ESTIMATE</div>
                        <table class="meta-table">
                            <tr>
                                <th>Estimate No:</th>
                                <td>' . htmlspecialchars($estimate->estimate_number) . '</td>
                            </tr>
                            <tr>
                                <th>Date:</th>
                                <td>' . date('d-M-Y', strtotime($estimate->estimate_date ?? date('Y-m-d'))) . '</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td><strong>' . strtoupper($estimate->status) . '</strong></td>
                            </tr>
                        </table>
                    </div>
                    <div style="clear: both;"></div>
                </div>

                <div class="customer-section">
                    <div class="bill-to">
                        <div class="section-heading">Estimate For</div>
                        <div class="customer-name">' . htmlspecialchars($estimate->customer_name) . '</div>
                        <div class="customer-details">
                            ' . $custAddressHtml . '
                            ' . $custPhoneHtml . '
                        </div>
                    </div>
                    <div style="clear: both;"></div>
                </div>

                <table class="table-items">
                    <thead>
                        <tr>
                            <th style="width: 5%; text-align: center;">#</th>
                            <th style="width: 45%;">Description</th>
                            <th style="width: 10%; text-align: right;">Qty</th>
                            <th style="width: 13%; text-align: right;">Price</th>
                            <th style="width: 12%; text-align: right;">Disc.</th>
                            <th style="width: 15%; text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        ' . $itemsHtml . '
                    </tbody>
                </table>

                <div class="bottom-section">
                    <div class="payment-info">
                        <div class="section-heading" style="border-bottom: 1px solid #ccc; margin-bottom: 8px; padding-bottom: 4px;">Terms & Conditions</div>
                        <div style="line-height: 1.5;">
                            • Prices valid for 30 days from date of estimate.<br>
                            • Subject to availability of stock.
                        </div>
                    </div>

                    <div class="summary-section">
                        <table class="table-totals">
                            <tr>
                                <th>Subtotal:</th>
                                <td style="text-align: right;">Rs. ' . number_format($subTotal, 2) . '</td>
                            </tr>
                            <tr>
                                <th>Discount:</th>
                                <td style="text-align: right;">Rs. ' . number_format($globalDiscountAmount, 2) . '</td>
                            </tr>
                            <tr>
                                <th>Tax / VAT:</th>
                                <td style="text-align: right;">Rs. ' . number_format($taxAmount, 2) . '</td>
                            </tr>
                            <tr style="border-top: 2px solid #000; border-bottom: 2px solid #000; font-weight: bold; font-size: 9.5pt;">
                                <th style="font-weight: bold; color: #000; text-align: right;">Grand Total:</th>
                                <td style="text-align: right; font-weight: bold;">Rs. ' . number_format($grandTotal, 2) . '</td>
                            </tr>
                        </table>
                    </div>
                    <div style="clear: both;"></div>
                </div>

                <div class="signature-section">
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <div class="signature-label">Prepared By</div>
                    </div>
                    <div class="signature-box" style="margin-left: 5%;">
                        <div class="signature-line"></div>
                        <div class="signature-label">Authorized Signatory</div>
                    </div>
                    <div class="signature-box" style="margin-left: 5%;">
                        <div class="signature-line"></div>
                        <div class="signature-label">Accepted By (Customer)</div>
                    </div>
                    <div style="clear: both;"></div>
                </div>
            </div>

            <div class="document-footer">
                <div style="float: left;">Generated dynamically on ' . date('d-M-Y H:i:s') . '</div>
                <div style="float: right;">Official Estimate</div>
                <div style="clear: both;"></div>
            </div>
        </body>
        </html>';

        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'estimate_' . preg_replace('/[^a-zA-Z0-9_-]/', '', $estimate->estimate_number) . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }
}