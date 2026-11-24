<?php 
/**
 * Bank Reconciliation Workspace View - Curtiss & Falcon Stationery ERP
 * Viewport-Locked (100vh), Top-Right Filter Toolbar, SF Pro Display & Zebra Striping
 */
$rec = $data['reconciliation']; 
$summary = $data['summary'] ?? [];
$isFinalized = ($rec->status === 'Finalized');
$csrfToken = $_SESSION['csrf_token'] ?? '';

// Cleared balance = confirmed opening balance + matched deposits - matched payments
$matchedDeposits = $summary['matched_deposits'] ?? 0.0;
$matchedPayments = $summary['matched_payments'] ?? 0.0;
$clearedBalance = round(floatval($rec->statement_opening_balance) + $matchedDeposits - $matchedPayments, 2);
?>

<style>
/* Viewport Lock Layout (Mandatory Rule 1) */
.erp-rec-workspace {
    height: calc(100vh - 58px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: #f8fafc;
    font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: #1e293b;
    padding: 14px 20px 10px 20px;
    box-sizing: border-box;
}

/* Compact Workspace Header Strip */
.rec-top-strip {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    flex-shrink: 0;
}
.rec-header-info {
    display: flex;
    align-items: center;
    gap: 12px;
}
.rec-header-info .rec-title {
    font-size: 19px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    letter-spacing: -0.02em;
}
.rec-header-meta {
    font-size: 12.5px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 8px;
}
.rec-badge-state {
    font-size: 11px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 999px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.badge-draft-mode {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
}
.badge-finalized-mode {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.rec-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}
.btn-rec {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    font-size: 12.5px;
    font-weight: 600;
    border-radius: 7px;
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.15s ease;
}
.btn-rec-secondary {
    background: #ffffff;
    color: #334155;
    border-color: #cbd5e1;
}
.btn-rec-secondary:hover {
    background: #f1f5f9;
    color: #0f172a;
}
.btn-rec-primary {
    background: #0284c7;
    color: #ffffff;
}
.btn-rec-primary:hover {
    background: #0369a1;
}
.btn-rec-success {
    background: #16a34a;
    color: #ffffff;
}
.btn-rec-success:hover {
    background: #15803d;
}
.btn-rec-success:disabled {
    opacity: 0.45;
    cursor: not-allowed;
    background: #94a3b8;
}
.btn-rec-warning {
    background: #fffbeb;
    color: #b45309;
    border-color: #fde68a;
}
.btn-rec-warning:hover {
    background: #fef3c7;
}

/* KPI Summary Cards Bar */
.rec-kpi-bar {
    display: grid;
    grid-template-columns: 1fr 1fr 1.1fr 1.2fr;
    gap: 12px;
    margin-bottom: 8px;
    flex-shrink: 0;
}
.rec-kpi-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 10px 14px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
}
.rec-kpi-card .kpi-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    color: #64748b;
    letter-spacing: 0.04em;
    margin-bottom: 2px;
}
.rec-kpi-card .kpi-value {
    font-size: 17px;
    font-weight: 700;
    color: #0f172a;
    font-family: ui-monospace, 'SF Mono', Menlo, Monaco, Consolas, monospace;
    font-variant-numeric: tabular-nums;
}
.rec-kpi-card .kpi-hint {
    font-size: 11px;
    color: #94a3b8;
}

.rec-kpi-card.target {
    background: #f0f9ff;
    border-color: #bae6fd;
}
.rec-kpi-card.target .kpi-label { color: #0284c7; }
.rec-kpi-card.target .kpi-value { color: #0369a1; }

.rec-kpi-card.diff-danger {
    background: #fef2f2;
    border-color: #fecaca;
}
.rec-kpi-card.diff-danger .kpi-label { color: #dc2626; }
.rec-kpi-card.diff-danger .kpi-value { color: #b91c1c; }

.rec-kpi-card.diff-success {
    background: #f0fdf4;
    border-color: #bbf7d0;
}
.rec-kpi-card.diff-success .kpi-label { color: #16a34a; }
.rec-kpi-card.diff-success .kpi-value { color: #15803d; }

/* Secondary Metrics Bar */
.rec-secondary-bar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 6px 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12px;
    color: #64748b;
    margin-bottom: 10px;
    flex-shrink: 0;
}
.rec-secondary-bar .metric-val {
    font-weight: 700;
    font-family: ui-monospace, 'SF Mono', Menlo, monospace;
}

/* Dedicated Filter Toolbar (Mandatory Rule 2: Top-Right Corner Filters) */
.erp-filter-bar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px 8px 0 0;
    padding: 8px 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    flex-shrink: 0;
    border-bottom: none;
}
.filter-left-count {
    font-size: 12.5px;
    color: #64748b;
    font-weight: 500;
}
.filter-right-group {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-left: auto;
    flex-wrap: wrap;
}
.erp-filter-select, .erp-filter-input {
    height: 30px;
    padding: 0 10px;
    font-size: 12px;
    font-family: inherit;
    color: #1e293b;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    outline: none;
    transition: all 0.15s ease;
}
.erp-filter-select:focus, .erp-filter-input:focus {
    border-color: #0284c7;
    background: #ffffff;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.1);
}
.btn-erp-reset {
    height: 30px;
    padding: 0 10px;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.btn-erp-reset:hover {
    background: #e2e8f0;
    color: #0f172a;
}

/* Scrollable Table Viewport (Mandatory Rule 1) */
.erp-table-scroll {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: auto;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.erp-rec-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 13px;
    text-align: left;
}
.erp-rec-table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    background: #f8fafc;
    color: #475569;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 9px 12px;
    border-bottom: 1px solid #cbd5e1;
    white-space: nowrap;
}
.erp-rec-table tbody td {
    padding: 9px 12px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    color: #334155;
}
.erp-rec-table tbody tr:hover td {
    background-color: #f0fdf4 !important;
}

/* Mandatory Zebra Striping (Rule 3) */
.erp-rec-table tbody tr:nth-child(even) td {
    background-color: #f8fafc;
}

.num-mono {
    font-family: ui-monospace, 'SF Mono', Menlo, Monaco, Consolas, monospace;
    font-variant-numeric: tabular-nums;
}
.badge-carried {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    font-size: 10px;
    font-weight: 700;
    color: #b45309;
    background: #fef3c7;
    padding: 2px 6px;
    border-radius: 4px;
    margin-left: 6px;
}
.badge-bank-entry {
    font-size: 10px;
    font-weight: 700;
    color: #0369a1;
    background: #e0f2fe;
    padding: 2px 6px;
    border-radius: 4px;
    margin-left: 6px;
}

/* Pinned Summary Footer */
.erp-pinned-footer {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-top: none;
    border-radius: 0 0 8px 8px;
    padding: 8px 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
    font-size: 12px;
    color: #64748b;
}

/* Modal Dialogs */
.erp-modal-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(2px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s ease;
}
.erp-modal-overlay.active {
    opacity: 1;
    pointer-events: auto;
}
.erp-modal-box {
    background: #ffffff;
    border-radius: 12px;
    width: 100%;
    max-width: 500px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transform: scale(0.95);
    transition: transform 0.2s ease;
}
.erp-modal-overlay.active .erp-modal-box {
    transform: scale(1);
}
.erp-modal-header {
    padding: 14px 18px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.erp-modal-title {
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}
.erp-modal-body {
    padding: 18px;
    font-size: 13px;
    color: #475569;
}
.erp-modal-footer {
    padding: 10px 18px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: flex-end;
    gap: 8px;
}
.modal-form-group {
    margin-bottom: 14px;
}
.modal-form-group label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 4px;
}
.modal-input, .modal-select {
    width: 100%;
    height: 36px;
    padding: 0 10px;
    font-size: 13px;
    font-family: inherit;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    box-sizing: border-box;
    outline: none;
}
.modal-input:focus, .modal-select:focus {
    border-color: #0284c7;
}
</style>

<div class="erp-rec-workspace">
    <!-- Top Strip -->
    <div class="rec-top-strip">
        <div class="rec-header-info">
            <h1 class="rec-title">Reconciliation Workspace</h1>
            <?php if ($isFinalized): ?>
                <span class="rec-badge-state badge-finalized-mode">Finalized & Locked</span>
            <?php else: ?>
                <span class="rec-badge-state badge-draft-mode">Active Draft</span>
            <?php endif; ?>
            <div class="rec-header-meta">
                <span>&bull;</span>
                <strong style="color: #0f172a;"><?= htmlspecialchars($rec->account_name); ?></strong>
                <span class="num-mono">(<?= htmlspecialchars($rec->account_code); ?>)</span>
                <span>&bull;</span>
                <span class="num-mono"><?= date('M d, Y', strtotime($rec->statement_date_start)); ?> &rarr; <?= date('M d, Y', strtotime($rec->statement_date_end)); ?></span>
                <span>&bull;</span>
                <span class="num-mono"><?= htmlspecialchars($rec->reconciliation_number); ?></span>
            </div>
        </div>

        <div class="rec-actions">
            <a href="<?= APP_URL ?>/bankreconciliation" class="btn-rec btn-rec-secondary">
                <i class="ph ph-arrow-left"></i> Reconciliations
            </a>
            <a href="<?= APP_URL ?>/bankreconciliation/report/<?= $rec->id ?>" class="btn-rec btn-rec-secondary" target="_blank">
                <i class="ph ph-printer"></i> Print Statement
            </a>
            <?php if (!$isFinalized): ?>
                <button type="button" class="btn-rec btn-rec-secondary" onclick="openBankOnlyModal()">
                    <i class="ph ph-plus-circle"></i> Bank Adjustment
                </button>
                <button type="button" class="btn-rec btn-rec-success" id="btnFinalize" <?= (abs($rec->difference) > 0.009) ? 'disabled' : '' ?>>
                    <i class="ph ph-lock-key"></i> Finalize Period
                </button>
            <?php else: ?>
                <button type="button" class="btn-rec btn-rec-warning" id="btnReopen">
                    <i class="ph ph-lock-key-open"></i> Reopen for Editing
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- KPI Summary Grid -->
    <div class="rec-kpi-bar">
        <div class="rec-kpi-card">
            <div class="kpi-label">Statement Opening</div>
            <div class="kpi-value">LKR <?= number_format($rec->statement_opening_balance, 2) ?></div>
            <div class="kpi-hint">Confirmed prior month closing</div>
        </div>
        <div class="rec-kpi-card target">
            <div class="kpi-label">Target Statement Ending</div>
            <div class="kpi-value">LKR <?= number_format($rec->statement_closing_balance, 2) ?></div>
            <div class="kpi-hint">Target from bank statement</div>
        </div>
        <div class="rec-kpi-card">
            <div class="kpi-label">Cleared in Bank</div>
            <div class="kpi-value" id="clearedVal">LKR <?= number_format($clearedBalance, 2) ?></div>
            <div class="kpi-hint">Opening + Matched Debits - Credits</div>
        </div>
        <div class="rec-kpi-card <?= (abs($rec->difference) > 0.009) ? 'diff-danger' : 'diff-success' ?>" id="diffCard">
            <div class="kpi-label">Difference</div>
            <div class="kpi-value" id="diffVal">LKR <?= number_format($rec->difference, 2) ?></div>
            <div class="kpi-hint" id="diffSub"><?= (abs($rec->difference) > 0.009) ? 'Unreconciled Difference' : '0.00 (Perfect Match)' ?></div>
        </div>
    </div>

    <!-- Secondary Metrics Quick Bar -->
    <div class="rec-secondary-bar">
        <div>
            <span>Deposits in Transit (Unmatched Dr): </span>
            <span class="metric-val" style="color: #16a34a;" id="transitVal">LKR <?= number_format($summary['deposits_in_transit'] ?? 0, 2) ?></span>
        </div>
        <div>
            <span>Outstanding Cheques (Unmatched Cr): </span>
            <span class="metric-val" style="color: #dc2626;" id="outstandingVal">LKR <?= number_format($summary['outstanding_cheques'] ?? 0, 2) ?></span>
        </div>
        <div>
            <span>Adjusted Bank: </span>
            <span class="metric-val" id="adjBankVal">LKR <?= number_format($rec->adjusted_bank_balance, 2) ?></span>
        </div>
        <div>
            <span>Adjusted Book (GL): </span>
            <span class="metric-val" id="adjBookVal">LKR <?= number_format($rec->adjusted_book_balance, 2) ?></span>
        </div>
    </div>

    <!-- Filter Toolbar (Top-Right Placement - Mandatory Rule 2) -->
    <div class="erp-filter-bar">
        <div class="filter-left-count">
            <span id="filteredResultsLabel">Showing <?= count($data['items']); ?> transactions</span>
        </div>
        <div class="filter-right-group">
            <input type="text" id="searchTerm" class="erp-filter-input" placeholder="Search ref, desc, amount..." style="width: 180px;">

            <select id="filterMatch" class="erp-filter-select">
                <option value="all">All Match Status</option>
                <option value="matched">Matched Only</option>
                <option value="unmatched">Unmatched (Outstanding)</option>
            </select>

            <select id="filterOrigin" class="erp-filter-select">
                <option value="all">All Origins</option>
                <option value="current">Current Period</option>
                <option value="carried">Carried Forward</option>
            </select>

            <select id="filterType" class="erp-filter-select">
                <option value="all">All Flows (Dr / Cr)</option>
                <option value="debit">Deposits / Inflows (Dr)</option>
                <option value="credit">Payments / Outflows (Cr)</option>
            </select>

            <button type="button" id="btnResetFilters" class="btn-erp-reset">
                <i class="ph ph-arrow-counter-clockwise"></i> Reset
            </button>
        </div>
    </div>

    <!-- Scrollable Matching Table (Mandatory Rule 1) -->
    <div class="erp-table-scroll">
        <table class="erp-rec-table">
            <thead>
                <tr>
                    <th style="text-align: center; width: 60px;">Match</th>
                    <th>Date</th>
                    <th>Ref / Cheque #</th>
                    <th>Description</th>
                    <th>Flow Type</th>
                    <th style="text-align: right;">Deposit / Inflow (Dr)</th>
                    <th style="text-align: right; padding-right: 14px;">Payment / Outflow (Cr)</th>
                </tr>
            </thead>
            <tbody id="txTableBody">
                <?php if (!empty($data['items'])): ?>
                    <?php foreach ($data['items'] as $item): ?>
                        <tr class="tx-row" 
                            data-matched="<?= $item->is_matched ? 'matched' : 'unmatched' ?>" 
                            data-type="<?= ($item->debit > 0) ? 'debit' : 'credit' ?>"
                            data-origin="<?= $item->is_carried_forward ? 'carried' : 'current' ?>"
                            data-search="<?= strtolower(htmlspecialchars(($item->reference ?? '') . ' ' . ($item->description ?? '') . ' ' . $item->debit . ' ' . $item->credit)) ?>">
                            <td style="text-align: center;">
                                <input type="checkbox" 
                                       class="match-checkbox" 
                                       data-item-id="<?= $item->id ?>" 
                                       <?= $item->is_matched ? 'checked' : '' ?>
                                       <?= $isFinalized ? 'disabled' : '' ?> 
                                       style="width: 16px; height: 16px; cursor: pointer; accent-color: #0284c7;">
                            </td>
                            <td class="num-mono" style="white-space: nowrap;">
                                <?= date('Y-m-d', strtotime($item->transaction_date)) ?>
                            </td>
                            <td class="num-mono" style="font-weight: 700; color: #0f172a;">
                                <?= htmlspecialchars($item->reference ?? '-') ?>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; flex-wrap: wrap;">
                                    <span><?= htmlspecialchars($item->description) ?></span>
                                    <?php if ($item->is_carried_forward): ?>
                                        <span class="badge-carried">
                                            <i class="ph ph-clock-counter-clockwise"></i> Carried Forward
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($item->transaction_type === 'BANK_ONLY'): ?>
                                        <span class="badge-bank-entry">Bank Adjustment</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <?php if ($item->debit > 0): ?>
                                    <span style="color: #16a34a; font-weight: 700; font-size: 11px;">Deposit / Inflow</span>
                                <?php else: ?>
                                    <span style="color: #64748b; font-weight: 600; font-size: 11px;">Payment / Outflow</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right; font-weight: 700;" class="num-mono">
                                <?= ($item->debit > 0) ? '<span style="color: #16a34a;">LKR ' . number_format($item->debit, 2) . '</span>' : '-' ?>
                            </td>
                            <td style="text-align: right; font-weight: 700; padding-right: 14px;" class="num-mono">
                                <?= ($item->credit > 0) ? '<span style="color: #dc2626;">LKR ' . number_format($item->credit, 2) . '</span>' : '-' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">
                            No general ledger transactions found for this account and period.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pinned Footer -->
    <div class="erp-pinned-footer">
        <div>
            <span>Double-Entry Matching System &bull; Bank Statement Verification</span>
        </div>
        <div>
            <span id="footerCount"><?= count($data['items']); ?> total transactions in period scope</span>
        </div>
    </div>
</div>

<!-- Modal for Bank-Only Entries with CSRF -->
<?php if (!$isFinalized): ?>
<div class="erp-modal-overlay" id="bankOnlyModal">
    <div class="erp-modal-box">
        <div class="erp-modal-header">
            <h3 class="erp-modal-title">Record Bank Statement Adjustment</h3>
            <button type="button" onclick="closeBankOnlyModal()" style="border:none; background:none; cursor:pointer; color:#94a3b8; font-size:16px;">
                <i class="ph ph-x"></i>
            </button>
        </div>
        <form id="bankOnlyForm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken); ?>">
            <input type="hidden" name="reconciliation_id" value="<?= $rec->id ?>">
            <div class="erp-modal-body">
                <div class="modal-form-group">
                    <label>Transaction Type <span style="color: #dc2626;">*</span></label>
                    <select name="type" class="modal-select" required>
                        <option value="CHARGE">Bank Charges / Service Fees (Expense)</option>
                        <option value="INTEREST">Bank Interest Earned (Income)</option>
                        <option value="DIRECT_DEPOSIT">Direct Bank Deposit / Transfer In</option>
                        <option value="DIRECT_DEBIT">Direct Bank Debit / Standing Order</option>
                    </select>
                </div>
                <div class="modal-form-group">
                    <label>Transaction Date <span style="color: #dc2626;">*</span></label>
                    <input type="date" name="transaction_date" class="modal-input num-mono" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="modal-form-group">
                    <label>Amount (LKR) <span style="color: #dc2626;">*</span></label>
                    <input type="number" step="0.01" name="amount" class="modal-input num-mono" placeholder="0.00" required>
                </div>
                <div class="modal-form-group">
                    <label>Corresponding Account (GL) <span style="color: #dc2626;">*</span></label>
                    <select name="opposite_account_id" class="modal-select" required>
                        <option value="">-- Choose Account --</option>
                        <?php foreach ($data['accounts'] as $acc): ?>
                            <option value="<?= $acc->id; ?>">
                                <?= htmlspecialchars($acc->account_code . ' - ' . $acc->account_name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="modal-form-group">
                    <label>Reference / Advice #</label>
                    <input type="text" name="reference" class="modal-input" placeholder="e.g. BC-<?= date('Ym') ?>">
                </div>
                <div class="modal-form-group">
                    <label>Description</label>
                    <input type="text" name="description" class="modal-input" placeholder="e.g. Monthly Bank Service Charge">
                </div>
            </div>
            <div class="erp-modal-footer">
                <button type="button" class="btn-rec btn-rec-secondary" onclick="closeBankOnlyModal()">Cancel</button>
                <button type="submit" class="btn-rec btn-rec-primary">
                    <i class="ph ph-floppy-disk"></i> Save & Post Entry
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script>
const CSRF_TOKEN = '<?= $csrfToken ?>';

function openBankOnlyModal() {
    const modal = document.getElementById('bankOnlyModal');
    if (modal) modal.classList.add('active');
}
function closeBankOnlyModal() {
    const modal = document.getElementById('bankOnlyModal');
    if (modal) modal.classList.remove('active');
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchTerm');
    const filterMatch = document.getElementById('filterMatch');
    const filterOrigin = document.getElementById('filterOrigin');
    const filterType = document.getElementById('filterType');
    const btnReset = document.getElementById('btnResetFilters');
    const resultsLabel = document.getElementById('filteredResultsLabel');

    function filterRows() {
        const query = searchInput.value.toLowerCase().trim();
        const matchVal = filterMatch.value;
        const originVal = filterOrigin.value;
        const typeVal = filterType.value;

        let visibleCount = 0;

        document.querySelectorAll('.tx-row').forEach(row => {
            const rowSearch = row.getAttribute('data-search') || '';
            const rowMatched = row.getAttribute('data-matched');
            const rowOrigin = row.getAttribute('data-origin');
            const rowType = row.getAttribute('data-type');

            let matchesQuery = !query || rowSearch.includes(query);
            let matchesMatch = (matchVal === 'all') || (rowMatched === matchVal);
            let matchesOrigin = (originVal === 'all') || (rowOrigin === originVal);
            let matchesType = (typeVal === 'all') || (rowType === typeVal);

            if (matchesQuery && matchesMatch && matchesOrigin && matchesType) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (resultsLabel) {
            resultsLabel.textContent = `Showing ${visibleCount} transactions`;
        }
    }

    if (searchInput) searchInput.addEventListener('input', filterRows);
    if (filterMatch) filterMatch.addEventListener('change', filterRows);
    if (filterOrigin) filterOrigin.addEventListener('change', filterRows);
    if (filterType) filterType.addEventListener('change', filterRows);

    if (btnReset) {
        btnReset.addEventListener('click', function() {
            searchInput.value = '';
            filterMatch.value = 'all';
            filterOrigin.value = 'all';
            filterType.value = 'all';
            filterRows();
        });
    }

    // AJAX Match toggle with CSRF
    document.querySelectorAll('.match-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            const itemId = this.getAttribute('data-item-id');
            const isMatched = this.checked ? 1 : 0;
            const row = this.closest('.tx-row');

            const formData = new FormData();
            formData.append('csrf_token', CSRF_TOKEN);
            formData.append('item_id', itemId);
            formData.append('is_matched', isMatched);

            fetch('<?= APP_URL ?>/bankreconciliation/match_ajax', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success' && data.summary) {
                    row.setAttribute('data-matched', isMatched ? 'matched' : 'unmatched');
                    filterRows();

                    const s = data.summary;
                    document.getElementById('clearedVal').textContent = 'LKR ' + s.cleared_balance.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    document.getElementById('diffVal').textContent = 'LKR ' + s.difference.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    document.getElementById('transitVal').textContent = 'LKR ' + s.deposits_in_transit.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    document.getElementById('outstandingVal').textContent = 'LKR ' + s.outstanding_cheques.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    document.getElementById('adjBankVal').textContent = 'LKR ' + s.adjusted_bank.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    document.getElementById('adjBookVal').textContent = 'LKR ' + s.adjusted_book.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

                    const diffCard = document.getElementById('diffCard');
                    const diffSub = document.getElementById('diffSub');
                    const btnFinalize = document.getElementById('btnFinalize');

                    if (Math.abs(s.difference) < 0.009) {
                        diffCard.className = 'rec-kpi-card diff-success';
                        diffSub.textContent = '0.00 (Perfect Match)';
                        if (btnFinalize) btnFinalize.disabled = false;
                    } else {
                        diffCard.className = 'rec-kpi-card diff-danger';
                        diffSub.textContent = 'Unreconciled Difference';
                        if (btnFinalize) btnFinalize.disabled = true;
                    }
                } else {
                    alert(data.message || 'Failed to update match');
                    this.checked = !this.checked;
                }
            })
            .catch(err => {
                console.error("Match error:", err);
                alert('Server communication error.');
                this.checked = !this.checked;
            });
        });
    });

    // AJAX Add Bank Only Entry with CSRF
    const bankForm = document.getElementById('bankOnlyForm');
    if (bankForm) {
        bankForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('csrf_token', CSRF_TOKEN);

            fetch('<?= APP_URL ?>/bankreconciliation/bank_only_ajax', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to post bank adjustment.');
                }
            })
            .catch(err => {
                console.error("Bank entry error:", err);
                alert('Server communication error.');
            });
        });
    }

    // AJAX Finalize with CSRF
    const btnFinalize = document.getElementById('btnFinalize');
    if (btnFinalize) {
        btnFinalize.addEventListener('click', function() {
            if (!confirm('Are you sure you want to finalize and lock this Bank Reconciliation? This will lock matched transactions, mark them as cleared in the Bank Register, and carry forward the confirmed closing balance.')) {
                return;
            }

            const formData = new FormData();
            formData.append('csrf_token', CSRF_TOKEN);
            formData.append('reconciliation_id', '<?= $rec->id ?>');

            fetch('<?= APP_URL ?>/bankreconciliation/finalize_ajax', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                if (data.status === 'success') {
                    window.location.reload();
                }
            })
            .catch(err => {
                console.error("Finalize error:", err);
                alert('Server communication error.');
            });
        });
    }

    // AJAX Reopen with CSRF
    const btnReopen = document.getElementById('btnReopen');
    if (btnReopen) {
        btnReopen.addEventListener('click', function() {
            if (!confirm('Are you sure you want to reopen this finalized Bank Reconciliation for editing? Linked transactions will be unlocked.')) {
                return;
            }

            const formData = new FormData();
            formData.append('csrf_token', CSRF_TOKEN);
            formData.append('reconciliation_id', '<?= $rec->id ?>');

            fetch('<?= APP_URL ?>/bankreconciliation/reopen_ajax', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                if (data.status === 'success') {
                    window.location.reload();
                }
            })
            .catch(err => {
                console.error("Reopen error:", err);
                alert('Server communication error.');
            });
        });
    }
});
</script>
