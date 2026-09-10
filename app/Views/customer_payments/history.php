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
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
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
    .status-Reversed { background: var(--danger-light); color: var(--danger); }

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
                    <label>Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Ref or Customer Name..." value="<?= htmlspecialchars($data['filters']['search']) ?>">
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
                    <th>Customer</th>
                    <th>Method</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['payments_history'])): ?>
                    <?php foreach($data['payments_history'] as $ph): ?>
                    <tr>
                        <td><?= htmlspecialchars($ph->payment_date ?? '') ?></td>
                        <td><strong><?= htmlspecialchars($ph->reference ?? '') ?></strong></td>
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
                            <button type="button" class="btn-action-small btn-danger" onclick="triggerReversal(<?= $ph->id ?>, <?= $ph->amount ?>)">
                                <i class="ph ph-arrow-u-up-left"></i> Reverse
                            </button>
                            <?php endif; ?>
                            <a href="<?= APP_URL ?>/customerpayment/receipt/<?= $ph->id ?>" target="_blank" class="btn-action-small">
                                <i class="ph ph-printer"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--slate-500); padding: 40px;">No payments match your criteria.</td>
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
                    // Build query string for pagination links
                    $queryParams = $_GET;
                    unset($queryParams['url']); // remove router param
                    unset($queryParams['page']); // we will append page manually
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

<script>
function triggerReversal(id, amount) {
    const msg = `Are you absolutely sure you want to REVERSE this customer payment of Rs ${parseFloat(amount).toLocaleString()}?\n\nThis will restore the unpaid balance of the allocated invoices, bounce associated cheques, and create reversing journal entries in the General Ledger.`;
    if (confirm(msg)) {
        window.location.href = `<?= APP_URL ?>/customerpayment/reverseCustomerPayment/${id}?from=history`;
    }
}
</script>
