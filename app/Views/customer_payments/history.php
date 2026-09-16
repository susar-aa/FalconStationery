<style>
    :root {
        --slate-50:  #f8fafc;
        --slate-100: #f1f5f9;
        --slate-200: #e2e8f0;
        --slate-300: #cbd5e1;
        --slate-400: #94a3b8;
        --slate-500: #64748b;
        --slate-600: #475569;
        --slate-700: #334155;
        --slate-800: #1e293b;
        --slate-900: #0f172a;
        --primary:       #2563eb;
        --primary-hover: #1d4ed8;
        --success:       #10b981;
        --success-light: #d1fae5;
        --warning:       #f59e0b;
        --warning-light: #fef3c7;
        --danger:        #ef4444;
        --danger-light:  #fee2e2;
        --white:         #ffffff;
        
        --radius-sm:     6px;
        --radius-md:     8px;
        --radius-lg:     14px;
        --shadow-sm:     0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
        --shadow-md:     0 4px 12px rgba(0,0,0,0.08), 0 2px 4px rgba(0,0,0,0.04);
        --font:          'Inter', system-ui, -apple-system, sans-serif;
    }

    .history-wrapper {
        font-family: var(--font);
        color: var(--slate-800);
        box-sizing: border-box;
    }

    .filter-panel {
        background: var(--white);
        border: 1px solid var(--slate-200);
        border-radius: var(--radius-md);
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: var(--shadow-sm);
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        align-items: end;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .form-group label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--slate-600);
    }
    .form-control {
        padding: 8px 12px;
        border: 1px solid var(--slate-300);
        border-radius: var(--radius-sm);
        font-family: var(--font);
        font-size: 12px;
        color: var(--slate-800);
        background: var(--white);
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 8px 16px;
        font-family: var(--font);
        font-size: 13px;
        font-weight: 600;
        border-radius: var(--radius-sm);
        border: 1px solid var(--slate-300);
        background: var(--white);
        color: var(--slate-700);
        cursor: pointer;
        text-decoration: none;
        transition: background 0.15s, border-color 0.15s;
        height: 35px;
        box-sizing: border-box;
    }
    .btn:hover { background: var(--slate-100); border-color: var(--slate-400); }
    .btn-primary { background: var(--primary); color: var(--white); border-color: var(--primary); }
    .btn-primary:hover { background: var(--primary-hover); }

    /* Data Table */
    .table-container {
        background: var(--white);
        border: 1px solid var(--slate-200);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .data-table th, .data-table td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--slate-200);
        text-align: left;
    }
    .data-table th {
        font-weight: 700;
        color: var(--slate-600);
        background: var(--slate-50);
        text-transform: uppercase;
        font-size: 11px;
    }
    .data-table tbody tr:hover { background: var(--slate-50); }

    /* Badges */
    .badge-entity {
        padding: 4px 8px;
        border-radius: var(--radius-sm);
        font-size: 11px;
        font-weight: 700;
    }
    .badge-entity-Customer { background: #dbeafe; color: #1e40af; }
    .badge-entity-Supplier { background: #f3e8ff; color: #6b21a8; }
    .badge-entity-ServiceProvider { background: #e0e7ff; color: #3730a3; }

    .badge-method {
        padding: 4px 8px;
        border-radius: var(--radius-sm);
        font-size: 11px;
        font-weight: 600;
    }
    .method-Cash { background: #fef3c7; color: #d97706; }
    .method-BankTransfer { background: #dbeafe; color: #1e40af; }
    .method-Cheque { background: #e0f2fe; color: #0369a1; }

    .status-badge {
        padding: 4px 8px;
        border-radius: var(--radius-sm);
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .status-Active { background: var(--success-light); color: var(--success); }
    .status-Reversed, .status-Voided { background: var(--danger-light); color: var(--danger); }

    .btn-action-small {
        padding: 5px 10px;
        font-size: 11px;
        font-weight: 600;
        border-radius: var(--radius-sm);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        border: 1px solid var(--slate-300);
        background: var(--white);
        color: var(--slate-700);
        transition: all 0.2s;
        text-decoration: none;
    }
    .btn-action-small:hover {
        background: var(--slate-100);
        border-color: var(--slate-400);
    }
    .btn-action-small.btn-edit {
        border-color: var(--primary);
        color: var(--primary);
    }
    .btn-action-small.btn-edit:hover { background: #eff6ff; }
    .btn-action-small.btn-danger {
        border-color: var(--danger);
        color: var(--danger);
    }
    .btn-action-small.btn-danger:hover { background: var(--danger-light); }

    .pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        background: var(--white);
        border-top: 1px solid var(--slate-200);
    }
    .page-info { font-size: 13px; color: var(--slate-600); }
    .page-controls { display: flex; gap: 5px; }
    .page-btn {
        padding: 6px 12px;
        border: 1px solid var(--slate-300);
        background: var(--white);
        color: var(--slate-700);
        font-size: 12px;
        font-weight: 600;
        border-radius: var(--radius-sm);
        cursor: pointer;
        text-decoration: none;
    }
    .page-btn:hover { background: var(--slate-50); border-color: var(--slate-400); }
    .page-btn.disabled { opacity: 0.5; cursor: not-allowed; pointer-events: none; }

    /* Modal Styling */
    .modal-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(15, 23, 42, 0.5);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 20px;
    }
    .modal-card {
        background: var(--white);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        width: 100%;
        max-width: 650px;
        max-height: 90vh;
        overflow-y: auto;
        border: 1px solid var(--slate-200);
    }
    .modal-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--slate-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-header h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: var(--slate-800);
    }
    .modal-close {
        background: none;
        border: none;
        font-size: 20px;
        color: var(--slate-400);
        cursor: pointer;
    }
    .modal-close:hover { color: var(--slate-700); }
    .modal-body { padding: 20px; }
    .modal-footer {
        padding: 16px 20px;
        border-top: 1px solid var(--slate-200);
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        background: var(--slate-50);
        border-bottom-left-radius: var(--radius-lg);
        border-bottom-right-radius: var(--radius-lg);
    }
    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }
</style>

<div class="history-wrapper">

    <!-- Notifications -->
    <?php if (!empty($data['error'])): ?>
        <div style="background: var(--danger-light); color: var(--danger); padding: 12px; border-radius: var(--radius-md); margin-bottom: 20px; border: 1px solid var(--danger); font-weight: 600;">
            <i class="ph ph-warning-circle"></i> <?= htmlspecialchars($data['error']) ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($data['success'])): ?>
        <div style="background: var(--success-light); color: var(--success); padding: 12px; border-radius: var(--radius-md); margin-bottom: 20px; border: 1px solid var(--success); font-weight: 600;">
            <i class="ph ph-check-circle"></i> <?= htmlspecialchars($data['success']) ?>
        </div>
    <?php endif; ?>

    <!-- Filter Panel -->
    <div class="filter-panel">
        <form method="GET" action="<?= APP_URL ?>/customerpayment/history">
            <div class="filter-grid">
                <div class="form-group">
                    <label>Entity Type</label>
                    <select name="entity_type" class="form-control">
                        <option value="">All Entities</option>
                        <option value="Customer" <?= ($data['filters']['entity_type'] ?? '') === 'Customer' ? 'selected' : '' ?>>Customer</option>
                        <option value="Supplier" <?= ($data['filters']['entity_type'] ?? '') === 'Supplier' ? 'selected' : '' ?>>Supplier</option>
                        <option value="Service Provider" <?= ($data['filters']['entity_type'] ?? '') === 'Service Provider' ? 'selected' : '' ?>>Service Provider</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Ref or Party Name..." value="<?= htmlspecialchars($data['filters']['search']) ?>">
                </div>
                <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($data['filters']['start_date']) ?>">
                </div>
                <div class="form-group">
                    <label>End Date</label>
                    <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($data['filters']['end_date']) ?>">
                </div>
                <div class="form-group">
                    <label>Method</label>
                    <select name="method" class="form-control">
                        <option value="">All Methods</option>
                        <option value="Cash" <?= $data['filters']['method'] === 'Cash' ? 'selected' : '' ?>>Cash</option>
                        <option value="Bank Transfer" <?= $data['filters']['method'] === 'Bank Transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                        <option value="Cheque" <?= $data['filters']['method'] === 'Cheque' ? 'selected' : '' ?>>Cheque</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="Active" <?= $data['filters']['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                        <option value="Reversed" <?= $data['filters']['status'] === 'Reversed' ? 'selected' : '' ?>>Reversed</option>
                    </select>
                </div>
                <div class="form-group" style="flex-direction: row; align-items: flex-end;">
                    <button type="submit" class="btn btn-primary"><i class="ph ph-funnel"></i> Filter</button>
                    <a href="<?= APP_URL ?>/customerpayment/history" class="btn"><i class="ph ph-arrows-clockwise"></i> Clear</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Reference</th>
                    <th>Type</th>
                    <th>Party / Name</th>
                    <th>Method</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['payments_history'])): ?>
                    <?php foreach($data['payments_history'] as $ph): ?>
                    <?php 
                        $entityClass = str_replace(' ', '', $ph->entity_type ?? 'Customer');
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($ph->payment_date ?? '') ?></td>
                        <td><strong><?= htmlspecialchars($ph->reference ?? '') ?></strong></td>
                        <td>
                            <span class="badge-entity badge-entity-<?= $entityClass ?>">
                                <?= htmlspecialchars($ph->entity_type ?? 'Customer') ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($ph->counterparty_name ?? '') ?></td>
                        <td>
                            <span class="badge-method method-<?= str_replace(' ', '', $ph->payment_method ?? '') ?>">
                                <?= htmlspecialchars($ph->payment_method ?? '') ?>
                            </span>
                        </td>
                        <td style="font-family: monospace; font-weight: 700;">Rs <?= number_format($ph->amount ?? 0, 2) ?></td>
                        <td>
                            <span class="status-badge status-<?= $ph->status ?? '' ?>">
                                <?= htmlspecialchars($ph->status ?? '') ?>
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <?php if (($ph->status ?? '') === 'Active'): ?>
                            <button type="button" class="btn-action-small btn-edit" onclick="triggerEdit('<?= htmlspecialchars($ph->entity_type ?? 'Customer') ?>', <?= $ph->id ?>)">
                                <i class="ph ph-pencil-simple"></i> Edit
                            </button>
                            <button type="button" class="btn-action-small btn-danger" onclick="triggerReversal('<?= htmlspecialchars($ph->entity_type ?? 'Customer') ?>', <?= $ph->id ?>, <?= $ph->amount ?>)">
                                <i class="ph ph-arrow-u-up-left"></i> Reverse
                            </button>
                            <?php endif; ?>

                            <?php if (($ph->entity_type ?? 'Customer') === 'Customer'): ?>
                            <a href="<?= APP_URL ?>/customerpayment/receipt/<?= $ph->id ?>" target="_blank" class="btn-action-small" title="Print Receipt">
                                <i class="ph ph-printer"></i>
                            </a>
                            <?php else: ?>
                            <a href="<?= APP_URL ?>/payment/receipt/<?= $ph->id ?>" target="_blank" class="btn-action-small" title="Print Voucher">
                                <i class="ph ph-printer"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--slate-500); padding: 40px;">No payment records match your criteria.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <?php if ($data['total_pages'] > 1): ?>
        <div class="pagination">
            <div class="page-info">
                Showing <?= number_format(count($data['payments_history'])) ?> of <?= number_format($data['total_records']) ?> records
            </div>
            <div class="page-controls">
                <?php 
                    $queryParams = $_GET;
                    unset($queryParams['url']);
                    unset($queryParams['page']);
                    $qStr = http_build_query($queryParams);
                    $baseLink = APP_URL . '/customerpayment/history?' . ($qStr ? $qStr . '&' : '');
                ?>
                <a href="<?= $baseLink . 'page=' . ($data['page'] - 1) ?>" class="page-btn <?= $data['page'] <= 1 ? 'disabled' : '' ?>">Previous</a>
                <span class="page-btn disabled">Page <?= $data['page'] ?> of <?= $data['total_pages'] ?></span>
                <a href="<?= $baseLink . 'page=' . ($data['page'] + 1) ?>" class="page-btn <?= $data['page'] >= $data['total_pages'] ? 'disabled' : '' ?>">Next</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Edit Payment Modal -->
<div id="editPaymentModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3><i class="ph ph-pencil-simple" style="color: var(--primary);"></i> Edit Payment Record</h3>
            <button type="button" class="modal-close" onclick="closeEditModal()">&times;</button>
        </div>
        <form method="POST" action="<?= APP_URL ?>/customerpayment/editPayment" id="editPaymentForm">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <input type="hidden" name="payment_id" id="edit_payment_id" value="0">
            <input type="hidden" name="entity_type" id="edit_entity_type" value="Customer">
            <input type="hidden" name="customer_id" id="edit_customer_id" value="0">
            <input type="hidden" name="supplier_id" id="edit_supplier_id" value="0">
            <input type="hidden" name="service_provider_id" id="edit_service_provider_id" value="0">

            <div class="modal-body">
                <div class="grid-2" style="margin-bottom: 15px;">
                    <div class="form-group">
                        <label>Category / Entity</label>
                        <input type="text" id="edit_entity_display" class="form-control" readonly style="background: var(--slate-100); font-weight: 700;">
                    </div>
                    <div class="form-group">
                        <label>Payment Date</label>
                        <input type="date" name="payment_date" id="edit_payment_date" class="form-control" required>
                    </div>
                </div>

                <div class="grid-2" style="margin-bottom: 15px;">
                    <div class="form-group">
                        <label>Reference #</label>
                        <input type="text" name="reference" id="edit_reference" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Payment Method</label>
                        <select name="payment_method" id="edit_payment_method" class="form-control" onchange="toggleChequeEditFields()" required>
                            <option value="Cash">Cash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                    </div>
                </div>

                <!-- Cheque Details Section -->
                <div id="edit_cheque_section" style="display: none; background: #f0f9ff; border: 1px solid #bae6fd; padding: 15px; border-radius: var(--radius-md); margin-bottom: 15px;">
                    <h4 style="margin: 0 0 10px 0; font-size: 12px; text-transform: uppercase; color: #0369a1;"><i class="ph ph-bank"></i> Cheque Information</h4>
                    <div class="grid-2">
                        <div class="form-group">
                            <label>Bank Name</label>
                            <input type="text" name="cheque_bank" id="edit_cheque_bank" class="form-control" placeholder="e.g. Commercial Bank">
                        </div>
                        <div class="form-group">
                            <label>Cheque Number (6 digits)</label>
                            <input type="text" name="cheque_number" id="edit_cheque_number" class="form-control" placeholder="123456" maxlength="6">
                        </div>
                    </div>
                    <div class="form-group" style="margin-top: 10px;">
                        <label>Cheque Date</label>
                        <input type="date" name="cheque_date" id="edit_cheque_date" class="form-control">
                    </div>
                </div>

                <div class="grid-2" style="margin-bottom: 15px;">
                    <div class="form-group">
                        <label id="edit_asset_label">Bank / Asset Account</label>
                        <select name="asset_account_id" id="edit_asset_account_id" class="form-control" required>
                            <?php if (!empty($data['asset_accounts'])): ?>
                                <?php foreach ($data['asset_accounts'] as $acc): ?>
                                <option value="<?= $acc->id ?>"><?= htmlspecialchars($acc->account_code . ' - ' . $acc->account_name) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label id="edit_control_label">Control Account (AR / AP)</label>
                        <select name="ar_account_id" id="edit_ar_account_id" class="form-control" required>
                            <?php if (!empty($data['accounts'])): ?>
                                <?php foreach ($data['accounts'] as $acc): ?>
                                <option value="<?= $acc->id ?>"><?= htmlspecialchars($acc->account_code . ' - ' . $acc->account_name) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Amount (Rs)</label>
                    <input type="number" step="0.01" name="amount" id="edit_amount" class="form-control" style="font-size: 16px; font-weight: 700; font-family: monospace;" required>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Notes / Memo</label>
                    <textarea name="notes" id="edit_notes" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="ph ph-check"></i> Save Changes & Update Accounts</button>
            </div>
        </form>
    </div>
</div>

<script>
function triggerReversal(entityType, id, amount) {
    const msg = `Are you absolutely sure you want to REVERSE this ${entityType} payment of Rs ${parseFloat(amount).toLocaleString()}?\n\nThis will restore unpaid balances, reverse ledger/CoA entries, and void associated cheque records.`;
    if (confirm(msg)) {
        if (entityType === 'Customer') {
            window.location.href = `<?= APP_URL ?>/customerpayment/reverseCustomerPayment/${id}?from=history`;
        } else {
            window.location.href = `<?= APP_URL ?>/payment/reverseSupplierPayment/${id}?from=history`;
        }
    }
}

function triggerEdit(entityType, id) {
    fetch(`<?= APP_URL ?>/customerpayment/getEditPaymentDataJson/${encodeURIComponent(entityType)}/${id}`)
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                alert(data.message || 'Failed to load payment details.');
                return;
            }
            populateEditModal(data);
        })
        .catch(err => {
            console.error(err);
            alert('An error occurred while fetching payment data.');
        });
}

function populateEditModal(data) {
    const p = data.payment;
    document.getElementById('edit_payment_id').value = p.id;
    document.getElementById('edit_entity_type').value = data.type;
    document.getElementById('edit_entity_display').value = `${data.type}: ${p.counterparty_name || 'N/A'}`;
    
    if (data.type === 'Customer') {
        document.getElementById('edit_customer_id').value = p.customer_id;
        document.getElementById('edit_supplier_id').value = 0;
        document.getElementById('edit_service_provider_id').value = 0;
        document.getElementById('edit_control_label').innerText = 'Accounts Receivable Account (AR)';
        document.getElementById('edit_ar_account_id').name = 'ar_account_id';
        if (data.ar_account_id) document.getElementById('edit_ar_account_id').value = data.ar_account_id;
    } else {
        document.getElementById('edit_customer_id').value = 0;
        document.getElementById('edit_supplier_id').value = p.vendor_id || 0;
        document.getElementById('edit_service_provider_id').value = p.service_provider_id || 0;
        document.getElementById('edit_control_label').innerText = 'Accounts Payable Account (AP)';
        document.getElementById('edit_ar_account_id').name = 'ap_account_id';
        if (data.ap_account_id) document.getElementById('edit_ar_account_id').value = data.ap_account_id;
    }

    document.getElementById('edit_payment_date').value = p.payment_date;
    document.getElementById('edit_reference').value = p.reference;
    document.getElementById('edit_payment_method').value = p.payment_method;
    document.getElementById('edit_amount').value = p.amount;
    document.getElementById('edit_notes').value = p.notes || '';

    if (data.asset_account_id) {
        document.getElementById('edit_asset_account_id').value = data.asset_account_id;
    }

    // Cheque fields
    if (data.cheque) {
        document.getElementById('edit_cheque_bank').value = data.cheque.bank_name || '';
        document.getElementById('edit_cheque_number').value = data.cheque.cheque_number || '';
        document.getElementById('edit_cheque_date').value = data.cheque.banking_date || '';
    } else {
        document.getElementById('edit_cheque_bank').value = '';
        document.getElementById('edit_cheque_number').value = '';
        document.getElementById('edit_cheque_date').value = '';
    }

    toggleChequeEditFields();

    // Show modal
    document.getElementById('editPaymentModal').style.display = 'flex';
}

function toggleChequeEditFields() {
    const method = document.getElementById('edit_payment_method').value;
    const sec = document.getElementById('edit_cheque_section');
    if (method === 'Cheque') {
        sec.style.display = 'block';
    } else {
        sec.style.display = 'none';
    }
}

function closeEditModal() {
    document.getElementById('editPaymentModal').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    // Log any server-side errors passed in URL query parameter
    const urlParams = new URLSearchParams(window.location.search);
    const errorMsg = urlParams.get('error');
    const successMsg = urlParams.get('success');

    if (errorMsg) {
        console.group('%c[Payment History Error Notification]', 'color: #ef4444; font-weight: bold; font-size: 14px;');
        console.error('Error Details:', errorMsg);
        console.info('Current URL Params:', Object.fromEntries(urlParams.entries()));
        console.groupEnd();
    }

    if (successMsg) {
        console.log('%c[Payment History Success]', 'color: #10b981; font-weight: bold;', successMsg);
    }

    // Add submit logging to editPaymentForm
    const editForm = document.getElementById('editPaymentForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            const formData = new FormData(editForm);
            const payload = {};
            formData.forEach((val, key) => payload[key] = val);

            console.group('%c[Edit Payment Submission]', 'color: #2563eb; font-weight: bold; font-size: 13px;');
            console.log('Target URL:', editForm.action);
            console.log('HTTP Method:', editForm.method);
            console.table(payload);

            // Validate cheque fields client-side with explicit console warnings
            if (payload.payment_method === 'Cheque') {
                if (!payload.cheque_bank || !payload.cheque_number || !payload.cheque_date) {
                    console.error('Cheque Validation Failed: Missing required bank, number, or date.', payload);
                } else if (!/^\d{6}$/.test(payload.cheque_number)) {
                    console.error('Cheque Validation Failed: Cheque number must be 6 numeric digits.', { cheque_number: payload.cheque_number });
                } else {
                    console.log('Cheque Validation Passed.');
                }
            }
            console.groupEnd();
        });
    }
});
</script>
