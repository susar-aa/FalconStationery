<?php
$isCashAccount = (
    (isset($data['account']->account_category) && in_array(strtolower($data['account']->account_category), ['cash', 'cash & cash equivalents'])) ||
    in_array($data['account']->account_code ?? '', ['1000', '1010', '1020', '1030', '1040', '1090', '1100']) ||
    stripos($data['account']->account_name ?? '', 'cash') !== false
);
$registerTitle = $isCashAccount ? 'Cash Register' : 'Bank Register';

$account = $data['account'];
$transactions = $data['transactions'] ?? [];
$stats = $data['stats'] ?? [
    'current_balance' => (float)($account->balance ?? 0),
    'total_transactions' => count($transactions),
    'cleared_count' => 0,
    'uncleared_count' => 0,
    'total_inflow' => 0,
    'total_outflow' => 0
];
?>

<!-- Phosphor Icons Suite -->
<script src="https://unpkg.com/@phosphor-icons/web"></script>

<style>
/* ============================================================
   ERP PROFESSIONAL DESIGN STANDARDS — BANK REGISTER (100VH LOCKED)
   ============================================================ */
@font-face {
    font-family: 'SF Pro Display';
    src: url('<?= APP_URL ?>/public/assets/fonts/sf_pro_display_regular.ttf') format('truetype');
    font-weight: 400;
    font-style: normal;
}
@font-face {
    font-family: 'SF Pro Display';
    src: url('<?= APP_URL ?>/public/assets/fonts/sf_pro_display_medium.ttf') format('truetype');
    font-weight: 500;
    font-style: normal;
}
@font-face {
    font-family: 'SF Pro Display';
    src: url('<?= APP_URL ?>/public/assets/fonts/sf_pro_display_semibold.ttf') format('truetype');
    font-weight: 600;
    font-style: normal;
}
@font-face {
    font-family: 'SF Pro Display';
    src: url('<?= APP_URL ?>/public/assets/fonts/sf_pro_display_bold.ttf') format('truetype');
    font-weight: 700;
    font-style: normal;
}

:root {
    --f-main: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    --f-mono: 'SF Mono', ui-monospace, Menlo, Monaco, Consolas, monospace;
    --c-bg: #eef2f6;
    --c-surface: #ffffff;
    --c-border: #e2e8f0;
    --c-text-main: #0f172a;
    --c-text-muted: #64748b;
    --c-navy-dark: #0f172a;
    --c-blue-accent: #2563eb;
    --c-blue-light: #eff6ff;
    --c-green-accent: #16a34a;
    --c-green-light: #f0fdf4;
    --c-amber-accent: #d97706;
    --c-amber-light: #fffbeb;
    --c-red-accent: #dc2626;
    --c-red-light: #fef2f2;
}

html, body {
    height: 100vh !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: hidden !important;
    font-family: var(--f-main);
    background-color: var(--c-bg);
    -webkit-font-smoothing: antialiased;
}

body.erp-locked .app-container,
body:has(.erp-viewport-view) .app-container,
.app-container {
    margin-top: 70px !important;
    height: calc(100vh - 70px) !important;
    overflow: hidden !important;
    display: flex !important;
    flex-direction: column !important;
}

body.erp-locked .main-content,
body:has(.erp-viewport-view) .main-content,
.main-content {
    padding: 8px 16px 8px 16px !important;
    overflow: hidden !important;
    display: flex !important;
    flex-direction: column !important;
    height: 100% !important;
    min-height: 0 !important;
    flex: 1 !important;
}

.bank-register-workspace {
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 0;
    max-height: 100%;
    flex: 1;
    overflow: hidden;
    gap: 8px;
    box-sizing: border-box;
    position: relative;
}

/* 1. TOP HEADER STRIP & BREADCRUMBS */
.register-top-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    padding: 2px 2px;
}

.header-left-group {
    display: flex;
    align-items: center;
    gap: 12px;
}

.btn-back-crumb {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    background: #ffffff;
    border: 1px solid var(--c-border);
    border-radius: 8px;
    color: #475569;
    font-size: 12.5px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.15s ease;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
}
.btn-back-crumb:hover {
    background: #f8fafc;
    color: var(--c-text-main);
    border-color: #cbd5e1;
}

.register-title-badge {
    display: flex;
    align-items: center;
    gap: 8px;
}
.register-heading {
    font-size: 16px;
    font-weight: 700;
    color: var(--c-text-main);
    margin: 0;
}
.account-code-pill {
    font-family: var(--f-mono);
    font-size: 11px;
    font-weight: 700;
    background: #f1f5f9;
    color: #475569;
    padding: 2px 8px;
    border-radius: 6px;
    border: 1px solid var(--c-border);
}
.account-subtitle {
    font-size: 12.5px;
    font-weight: 500;
    color: var(--c-text-muted);
}

.header-right-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.action-btn-soft {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 13px;
    background: #ffffff;
    border: 1px solid var(--c-border);
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 600;
    color: #334155;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.15s ease;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
}
.action-btn-soft:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: var(--c-text-main);
}
.action-btn-soft.excel {
    color: #166534;
    border-color: #bbf7d0;
    background: #f0fdf4;
}
.action-btn-soft.excel:hover {
    background: #dcfce7;
    border-color: #86efac;
}

.action-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    background: var(--c-navy-dark);
    border: 1px solid var(--c-navy-dark);
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 600;
    color: #ffffff;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.15s ease;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}
.action-btn-primary:hover {
    background: #1e293b;
    border-color: #1e293b;
}

/* 2. HIGH-DENSITY KPI STATS STRIP */
.erp-stats-strip {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    flex-shrink: 0;
}

.stat-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #ffffff;
    border: 1px solid var(--c-border);
    border-radius: 9999px;
    padding: 4px 12px;
    font-size: 12px;
    color: #475569;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
}
.stat-pill strong {
    color: #0f172a;
    font-weight: 700;
    font-family: var(--f-mono);
}
.stat-pill.balance {
    background: #eff6ff;
    border-color: #bfdbfe;
    color: #1e40af;
}
.stat-pill.balance strong {
    color: #1d4ed8;
    font-size: 13px;
}
.stat-pill.cleared {
    background: #f0fdf4;
    border-color: #bbf7d0;
    color: #166534;
}
.stat-pill.cleared strong {
    color: #15803d;
}
.stat-pill.pending {
    background: #fffbeb;
    border-color: #fde68a;
    color: #92400e;
}
.stat-pill.pending strong {
    color: #b45309;
}
.badge-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
}

/* 3. MAIN WORKSPACE CARD & TABLE */
.register-table-card {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: #ffffff;
    border: 1px solid var(--c-border);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04);
    min-height: 0;
    position: relative;
}

/* DEDICATED TOP FILTER BAR (MANDATORY TOP-RIGHT CORNER) */
.erp-filter-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 16px;
    border-bottom: 1px solid var(--c-border);
    background: #ffffff;
    flex-shrink: 0;
    gap: 12px;
}

.erp-filter-left {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
}

.row-count-badge {
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    background: #f8fafc;
    border: 1px solid var(--c-border);
    padding: 3px 10px;
    border-radius: 6px;
    white-space: nowrap;
}

.erp-filter-right {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    justify-content: flex-end;
}

.filter-input-wrap {
    display: flex;
    align-items: center;
    gap: 5px;
}
.filter-label-tiny {
    font-size: 11px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    white-space: nowrap;
}
.filter-select-sm, .filter-date-sm {
    padding: 5px 9px;
    border: 1px solid var(--c-border);
    border-radius: 7px;
    font-family: var(--f-main);
    font-size: 12px;
    font-weight: 500;
    color: #334155;
    background: #ffffff;
    outline: none;
    transition: all 0.15s ease;
}
.filter-select-sm:focus, .filter-date-sm:focus {
    border-color: var(--c-blue-accent);
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
}

.btn-filter-reset {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 5px 10px;
    border: 1px solid var(--c-border);
    border-radius: 7px;
    background: #f8fafc;
    color: #475569;
    font-family: var(--f-main);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
}
.btn-filter-reset:hover {
    background: #f1f5f9;
    color: var(--c-text-main);
    border-color: #cbd5e1;
}

/* 4. SCROLLABLE DATA TABLE CONTAINER */
.erp-table-scroll {
    flex: 1;
    overflow-y: auto;
    overflow-x: auto;
    min-height: 0;
    position: relative;
    background: #ffffff;
    padding-bottom: 54px; /* Ensure floating bottom bar does not obscure last rows */
}

.erp-data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
    text-align: left;
}

.erp-data-table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    background: #f8fafc;
    border-bottom: 1px solid var(--c-border);
    padding: 9px 14px;
    font-size: 11px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    white-space: nowrap;
    cursor: pointer;
    user-select: none;
}
.erp-data-table thead th:hover {
    color: var(--c-text-main);
}

/* MANDATORY ZEBRA STRIPING */
.erp-data-table tbody tr.ledger-row {
    border-bottom: 1px solid #f1f5f9;
    transition: background-color 0.1s ease;
}
.erp-data-table tbody tr.ledger-row:nth-child(even) {
    background-color: #fafbfc;
}
.erp-data-table tbody tr.ledger-row:hover {
    background-color: #f1f5f9 !important;
}

.erp-data-table td {
    padding: 8px 14px;
    color: #334155;
    font-size: 13px;
    vertical-align: middle;
}

.td-date {
    font-family: var(--f-mono);
    color: #475569;
    font-size: 12.5px;
    white-space: nowrap;
}
.td-ref {
    font-family: var(--f-mono);
    font-size: 12px;
    font-weight: 600;
    color: #0f172a;
    white-space: nowrap;
}
.ref-pill {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 2px 6px;
    display: inline-block;
}
.td-desc {
    max-width: 320px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.td-num {
    text-align: right !important;
    font-family: var(--f-mono);
    font-variant-numeric: tabular-nums;
    font-size: 13px;
    font-weight: 600;
    white-space: nowrap;
}
.td-debit {
    color: var(--c-green-accent);
}
.td-credit {
    color: var(--c-red-accent);
}
.td-balance {
    color: var(--c-navy-dark);
    font-weight: 700;
}

/* Status Badges */
.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 8px;
    border-radius: 9999px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.02em;
    text-transform: uppercase;
    white-space: nowrap;
}
.status-pill.cleared {
    background: var(--c-green-light);
    color: var(--c-green-accent);
    border: 1px solid #bbf7d0;
}
.status-pill.uncleared {
    background: var(--c-amber-light);
    color: var(--c-amber-accent);
    border: 1px solid #fde68a;
}
.status-pill.voided {
    background: var(--c-red-light);
    color: var(--c-red-accent);
    border: 1px solid #fecaca;
}

/* 5. PINNED BOTTOM PAGINATION FOOTER */
.erp-pinned-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 16px;
    background: #f8fafc;
    border-top: 1px solid var(--c-border);
    flex-shrink: 0;
    position: relative;
    z-index: 5;
}

.footer-totals-strip {
    display: flex;
    align-items: center;
    gap: 14px;
    font-size: 12px;
    color: #475569;
}
.footer-totals-strip strong {
    font-family: var(--f-mono);
    color: #0f172a;
}

.pagination-controls {
    display: flex;
    align-items: center;
    gap: 8px;
}
.pagination-info {
    font-size: 12px;
    font-weight: 500;
    color: #64748b;
}
.btn-page-nav {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    background: #ffffff;
    border: 1px solid var(--c-border);
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    color: #334155;
    cursor: pointer;
    transition: all 0.15s ease;
}
.btn-page-nav:hover:not(:disabled) {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: var(--c-text-main);
}
.btn-page-nav:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
.page-size-select {
    padding: 3px 8px;
    border: 1px solid var(--c-border);
    border-radius: 6px;
    font-size: 12px;
    color: #334155;
    background: #ffffff;
    outline: none;
}

/* 6. FLOATING COMMAND BAR */
.erp-floating-command-bar {
    position: absolute;
    bottom: 48px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(15, 23, 42, 0.94);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 9999px;
    padding: 5px 8px;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.2);
    z-index: 20;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.floating-search-wrap {
    display: flex;
    align-items: center;
    gap: 6px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 9999px;
    padding: 4px 12px;
    width: 280px;
}
.floating-search-wrap i {
    color: #94a3b8;
    font-size: 14px;
}
.floating-search-input {
    border: none;
    background: transparent;
    color: #ffffff;
    font-family: var(--f-main);
    font-size: 12.5px;
    width: 100%;
    outline: none;
}
.floating-search-input::placeholder {
    color: #94a3b8;
}
.floating-search-clear {
    background: transparent;
    border: none;
    color: #94a3b8;
    font-size: 14px;
    cursor: pointer;
    padding: 0;
    display: none;
}
.floating-search-clear:hover {
    color: #ffffff;
}

.floating-btn-reconcile {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #2563eb;
    color: #ffffff;
    border: none;
    border-radius: 9999px;
    padding: 5px 14px;
    font-family: var(--f-main);
    font-size: 12.5px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: background 0.15s ease;
    white-space: nowrap;
}
.floating-btn-reconcile:hover {
    background: #1d4ed8;
}

@media print {
    .register-top-header, .erp-stats-strip, .erp-filter-bar, .erp-pinned-footer, .erp-floating-command-bar {
        display: none !important;
    }
    body, html, .app-container, .main-content, .bank-register-workspace, .register-table-card, .erp-table-scroll {
        height: auto !important;
        overflow: visible !important;
        background: #fff !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .erp-data-table thead th {
        position: static !important;
    }
}
</style>

<div class="erp-viewport-view bank-register-workspace">
    <!-- 1. TOP HEADER & NAVIGATION STRIP -->
    <div class="register-top-header">
        <div class="header-left-group">
            <a href="<?= APP_URL ?>/banking" class="btn-back-crumb">
                <i class="ph ph-arrow-left"></i>
                <span>Banking & Cash</span>
            </a>
            <div class="register-title-badge">
                <span class="account-code-pill">GL-<?= htmlspecialchars($account->account_code) ?></span>
                <h1 class="register-heading"><?= $registerTitle ?>: <?= htmlspecialchars($account->account_name) ?></h1>
            </div>
        </div>

        <div class="header-right-actions">
            <a href="<?= APP_URL ?>/banking/reconcile/<?= $account->id ?>" class="action-btn-primary">
                <i class="ph ph-check-square-offset"></i>
                <span>Reconcile Statement</span>
            </a>
            <a href="javascript:void(0)" onclick="triggerRegisterExport('excel')" class="action-btn-soft excel" id="btnExportTop">
                <i class="ph ph-file-xls"></i>
                <span>Export Excel</span>
            </a>
            <button type="button" class="action-btn-soft" onclick="window.print()">
                <i class="ph ph-printer"></i>
                <span>Print</span>
            </button>
        </div>
    </div>

    <!-- 2. HIGH-DENSITY KPI STATS STRIP -->
    <div class="erp-stats-strip">
        <div class="stat-pill balance">
            <span class="badge-dot" style="background:#2563eb;"></span>
            <span>Current Balance:</span>
            <strong>Rs. <?= number_format($stats['current_balance'], 2) ?></strong>
        </div>

        <div class="stat-pill cleared">
            <span class="badge-dot" style="background:#16a34a;"></span>
            <span>Cleared:</span>
            <strong id="statClearedCount"><?= $stats['cleared_count'] ?></strong>
        </div>

        <div class="stat-pill pending">
            <span class="badge-dot" style="background:#d97706;"></span>
            <span>Pending:</span>
            <strong id="statPendingCount"><?= $stats['uncleared_count'] ?></strong>
        </div>

        <div class="stat-pill">
            <i class="ph ph-arrow-down-left" style="color:#16a34a; font-weight:bold;"></i>
            <span>Inflows:</span>
            <strong id="statTotalInflow">Rs. <?= number_format($stats['total_inflow'], 2) ?></strong>
        </div>

        <div class="stat-pill">
            <i class="ph ph-arrow-up-right" style="color:#dc2626; font-weight:bold;"></i>
            <span>Outflows:</span>
            <strong id="statTotalOutflow">Rs. <?= number_format($stats['total_outflow'], 2) ?></strong>
        </div>

        <div class="stat-pill">
            <span>Total Entries:</span>
            <strong><?= $stats['total_transactions'] ?></strong>
        </div>
    </div>

    <!-- 3. MAIN WORKSPACE CARD -->
    <div class="register-table-card">
        <!-- TOP-RIGHT FILTER TOOLBAR -->
        <div class="erp-filter-bar">
            <div class="erp-filter-left">
                <span class="row-count-badge" id="filteredRecordsBadge">Showing <?= count($transactions) ?> entries</span>
            </div>

            <div class="erp-filter-right">
                <!-- FLOW FILTER -->
                <div class="filter-input-wrap">
                    <span class="filter-label-tiny">FLOW:</span>
                    <select id="filterFlow" class="filter-select-sm">
                        <option value="all" <?= ($data['flowFilter'] ?? '') === 'all' ? 'selected' : '' ?>>All Flows</option>
                        <option value="debit" <?= ($data['flowFilter'] ?? '') === 'debit' ? 'selected' : '' ?>>Deposits (In)</option>
                        <option value="credit" <?= ($data['flowFilter'] ?? '') === 'credit' ? 'selected' : '' ?>>Payments (Out)</option>
                    </select>
                </div>

                <!-- STATUS FILTER -->
                <div class="filter-input-wrap">
                    <span class="filter-label-tiny">STATUS:</span>
                    <select id="filterStatus" class="filter-select-sm">
                        <option value="all" <?= ($data['statusFilter'] ?? '') === 'all' ? 'selected' : '' ?>>All Statuses</option>
                        <option value="cleared" <?= ($data['statusFilter'] ?? '') === 'cleared' ? 'selected' : '' ?>>Cleared</option>
                        <option value="uncleared" <?= ($data['statusFilter'] ?? '') === 'uncleared' ? 'selected' : '' ?>>Uncleared</option>
                        <option value="voided" <?= ($data['statusFilter'] ?? '') === 'voided' ? 'selected' : '' ?>>Voided</option>
                    </select>
                </div>

                <!-- DATE RANGE PRESET -->
                <div class="filter-input-wrap">
                    <span class="filter-label-tiny">RANGE:</span>
                    <select id="filterRangePreset" class="filter-select-sm">
                        <option value="all_time" selected>All Time</option>
                        <option value="this_month">This Month</option>
                        <option value="today">Today</option>
                        <option value="this_week">This Week</option>
                        <option value="this_quarter">This Quarter</option>
                        <option value="this_year">This Year</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>

                <!-- FROM DATE -->
                <div class="filter-input-wrap">
                    <span class="filter-label-tiny">FROM:</span>
                    <input type="date" id="filterStartDate" class="filter-date-sm" value="<?= htmlspecialchars($data['startDate'] ?? '') ?>">
                </div>

                <!-- TO DATE -->
                <div class="filter-input-wrap">
                    <span class="filter-label-tiny">TO:</span>
                    <input type="date" id="filterEndDate" class="filter-date-sm" value="<?= htmlspecialchars($data['endDate'] ?? '') ?>">
                </div>

                <!-- RESET BUTTON -->
                <button type="button" class="btn-filter-reset" id="btnResetFilters" title="Reset all filters">
                    <i class="ph ph-arrow-counter-clockwise"></i>
                    <span>Reset</span>
                </button>
            </div>
        </div>

        <!-- 4. SCROLLABLE DATA TABLE VIEWPORT ONLY -->
        <div class="erp-table-scroll" id="tableScrollView">
            <table class="erp-data-table" id="bankRegisterTable">
                <thead>
                    <tr>
                        <th style="width: 110px;" data-col="date">Date</th>
                        <th style="width: 140px;" data-col="reference">Reference</th>
                        <th style="min-width: 250px;" data-col="description">Description</th>
                        <th style="width: 130px; text-align: right;" data-col="debit">Debit (Inflow)</th>
                        <th style="width: 130px; text-align: right;" data-col="credit">Credit (Outflow)</th>
                        <th style="width: 140px; text-align: right;" data-col="balance">Running Balance</th>
                        <th style="width: 110px; text-align: center;" data-col="status">Status</th>
                    </tr>
                </thead>
                <tbody id="bankRegisterBody">
                    <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 48px 20px; color: #94a3b8;">
                                <i class="ph ph-book-open" style="font-size: 32px;"></i>
                                <p style="margin-top: 8px; font-weight: 500;">No transactions found in this account register.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $t): 
                            $st = $t->status ?? $t->entry_status ?? '';
                            $isVoided = ($st === 'Voided');
                            $debit = (float)($t->debit ?? 0);
                            $credit = (float)($t->credit ?? 0);
                            $runningBal = (float)($t->running_balance ?? 0);
                            $dateIso = date('Y-m-d', strtotime($t->entry_date));
                            $dateFormatted = date('M d, Y', strtotime($t->entry_date));
                            $statusType = $isVoided ? 'voided' : (!empty($t->is_cleared) ? 'cleared' : 'uncleared');
                            $flowType = ($debit > 0) ? 'debit' : (($credit > 0) ? 'credit' : 'none');
                        ?>
                            <tr class="ledger-row" 
                                data-date="<?= $dateIso ?>"
                                data-status="<?= $statusType ?>"
                                data-flow="<?= $flowType ?>"
                                data-debit="<?= $debit ?>"
                                data-credit="<?= $credit ?>"
                                style="<?= $isVoided ? 'opacity: 0.6; background-color: #fff1f2 !important;' : '' ?>">
                                <td class="td-date"><?= $dateFormatted ?></td>
                                <td class="td-ref">
                                    <span class="ref-pill"><?= htmlspecialchars($t->reference ?? '') ?></span>
                                </td>
                                <td class="td-desc" title="<?= htmlspecialchars($t->description ?? '') ?>">
                                    <?= htmlspecialchars($t->description ?? '') ?>
                                    <?php if ($isVoided): ?>
                                        <span style="color: var(--c-red-accent); font-weight: 700; margin-left: 4px;">[VOIDED]</span>
                                    <?php endif; ?>
                                </td>
                                <td class="td-num td-debit" style="<?= $isVoided ? 'text-decoration: line-through;' : '' ?>">
                                    <?= ($debit > 0) ? number_format($debit, 2) : '-' ?>
                                </td>
                                <td class="td-num td-credit" style="<?= $isVoided ? 'text-decoration: line-through;' : '' ?>">
                                    <?= ($credit > 0) ? number_format($credit, 2) : '-' ?>
                                </td>
                                <td class="td-num td-balance">
                                    <?= number_format($runningBal, 2) ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php if ($isVoided): ?>
                                        <span class="status-pill voided">Voided</span>
                                    <?php elseif (!empty($t->is_cleared)): ?>
                                        <span class="status-pill cleared"><i class="ph ph-check" style="font-size: 11px;"></i> Cleared</span>
                                    <?php else: ?>
                                        <span class="status-pill uncleared"><i class="ph ph-clock" style="font-size: 11px;"></i> Uncleared</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr id="noMatchRow" style="display: none;">
                            <td colspan="7" style="text-align: center; padding: 48px 20px; color: #94a3b8;">
                                <i class="ph ph-magnifying-glass" style="font-size: 32px;"></i>
                                <p style="margin-top: 8px; font-weight: 600; color: #1e293b;">No transactions match the active filters</p>
                                <button type="button" onclick="resetAllFilters()" class="btn-filter-reset" style="margin-top: 8px; padding: 6px 14px;">
                                    Clear Filters
                                </button>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- 5. PINNED BOTTOM SUMMARY & PAGINATION FOOTER -->
        <div class="erp-pinned-footer">
            <div class="footer-totals-strip">
                <span>Inflows: <strong id="footInflowSum">Rs. 0.00</strong></span>
                <span>Outflows: <strong id="footOutflowSum">Rs. 0.00</strong></span>
                <span>Net: <strong id="footNetSum">Rs. 0.00</strong></span>
            </div>

            <div class="pagination-controls">
                <span class="pagination-info" id="paginationInfo">Showing 1 - 25 of <?= count($transactions) ?></span>
                <select id="pageSizeSelect" class="page-size-select">
                    <option value="25" selected>25 / page</option>
                    <option value="50">50 / page</option>
                    <option value="100">100 / page</option>
                    <option value="all">All records</option>
                </select>
                <button type="button" class="btn-page-nav" id="btnPrevPage" disabled>
                    <i class="ph ph-caret-left"></i> Prev
                </button>
                <button type="button" class="btn-page-nav" id="btnNextPage">
                    Next <i class="ph ph-caret-right"></i>
                </button>
            </div>
        </div>

        <!-- 6. SLEEK FLOATING COMMAND BAR -->
        <div class="erp-floating-command-bar">
            <div class="floating-search-wrap">
                <i class="ph ph-magnifying-glass"></i>
                <input type="text" id="floatingSearchInput" class="floating-search-input" placeholder="Live search date, ref, text, amount..." autocomplete="off">
                <button type="button" id="floatingSearchClear" class="floating-search-clear">
                    <i class="ph ph-x"></i>
                </button>
            </div>
            <a href="<?= APP_URL ?>/banking/reconcile/<?= $account->id ?>" class="floating-btn-reconcile">
                <i class="ph ph-check-square-offset"></i>
                <span>Reconcile</span>
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.body.classList.add('erp-locked');

    // DOM Elements
    const searchInput = document.getElementById('floatingSearchInput');
    const searchClear = document.getElementById('floatingSearchClear');
    const filterFlow = document.getElementById('filterFlow');
    const filterStatus = document.getElementById('filterStatus');
    const filterRangePreset = document.getElementById('filterRangePreset');
    const filterStartDate = document.getElementById('filterStartDate');
    const filterEndDate = document.getElementById('filterEndDate');
    const btnResetFilters = document.getElementById('btnResetFilters');
    const pageSizeSelect = document.getElementById('pageSizeSelect');
    const btnPrevPage = document.getElementById('btnPrevPage');
    const btnNextPage = document.getElementById('btnNextPage');
    const paginationInfo = document.getElementById('paginationInfo');
    const filteredBadge = document.getElementById('filteredRecordsBadge');
    const noMatchRow = document.getElementById('noMatchRow');
    const tableScroll = document.getElementById('tableScrollView');

    // Footer Totals Elements
    const footInflowSum = document.getElementById('footInflowSum');
    const footOutflowSum = document.getElementById('footOutflowSum');
    const footNetSum = document.getElementById('footNetSum');

    // All transaction rows
    const allRows = Array.from(document.querySelectorAll('.ledger-row'));
    let matchedRows = [...allRows];
    let currentPage = 1;
    let pageSize = 25;

    function formatNumber(num) {
        return (parseFloat(num) || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // 1. Preset Date Ranges
    function updateDatePreset(preset) {
        const now = new Date();
        if (preset === 'all_time') {
            filterStartDate.value = '';
            filterEndDate.value = '';
        } else if (preset === 'today') {
            const todayStr = now.toISOString().split('T')[0];
            filterStartDate.value = todayStr;
            filterEndDate.value = todayStr;
        } else if (preset === 'this_week') {
            const first = new Date(now.setDate(now.getDate() - now.getDay() + 1));
            filterStartDate.value = first.toISOString().split('T')[0];
            filterEndDate.value = new Date().toISOString().split('T')[0];
        } else if (preset === 'this_month') {
            const y = now.getFullYear();
            const m = String(now.getMonth() + 1).padStart(2, '0');
            filterStartDate.value = `${y}-${m}-01`;
            filterEndDate.value = new Date().toISOString().split('T')[0];
        } else if (preset === 'this_quarter') {
            const currentQuarter = Math.floor((now.getMonth() + 3) / 3);
            const startMonth = String((currentQuarter - 1) * 3 + 1).padStart(2, '0');
            filterStartDate.value = `${now.getFullYear()}-${startMonth}-01`;
            filterEndDate.value = new Date().toISOString().split('T')[0];
        } else if (preset === 'this_year') {
            filterStartDate.value = `${now.getFullYear()}-01-01`;
            filterEndDate.value = new Date().toISOString().split('T')[0];
        }
    }

    // 2. Filter & Search Logic
    function applyFilters() {
        const query = (searchInput ? searchInput.value : '').toLowerCase().trim();
        const queryNoComma = query.replace(/,/g, '');
        const flow = filterFlow.value;
        const status = filterStatus.value;
        const startDate = filterStartDate.value;
        const endDate = filterEndDate.value;

        if (searchClear) {
            searchClear.style.display = query.length > 0 ? 'inline-flex' : 'none';
        }

        let totalInflow = 0;
        let totalOutflow = 0;

        matchedRows = allRows.filter(row => {
            const rowFlow = row.getAttribute('data-flow');
            const rowStatus = row.getAttribute('data-status');
            const rowDate = row.getAttribute('data-date');
            const debit = parseFloat(row.getAttribute('data-debit')) || 0;
            const credit = parseFloat(row.getAttribute('data-credit')) || 0;

            // Flow filter
            if (flow !== 'all' && rowFlow !== flow) return false;

            // Status filter
            if (status !== 'all' && rowStatus !== status) return false;

            // Date filters
            if (startDate && rowDate < startDate) return false;
            if (endDate && rowDate > endDate) return false;

            // Search query filter
            if (query !== '') {
                const text = row.innerText.toLowerCase();
                const textNoComma = text.replace(/,/g, '');
                if (!text.includes(query) && !textNoComma.includes(queryNoComma)) {
                    return false;
                }
            }

            // Accumulate totals for matched active entries
            if (rowStatus !== 'voided') {
                totalInflow += debit;
                totalOutflow += credit;
            }

            return true;
        });

        // Update footer totals
        if (footInflowSum) footInflowSum.textContent = 'Rs. ' + formatNumber(totalInflow);
        if (footOutflowSum) footOutflowSum.textContent = 'Rs. ' + formatNumber(totalOutflow);
        if (footNetSum) {
            const net = totalInflow - totalOutflow;
            footNetSum.textContent = 'Rs. ' + formatNumber(net);
            footNetSum.style.color = net >= 0 ? 'var(--c-green-accent)' : 'var(--c-red-accent)';
        }

        // Update record badge
        if (filteredBadge) {
            filteredBadge.textContent = 'Showing ' + matchedRows.length + ' of ' + allRows.length + ' entries';
        }

        currentPage = 1;
        renderPaginatedView();
    }

    // 3. Render Paginated Rows
    function renderPaginatedView() {
        allRows.forEach(r => r.style.display = 'none');

        if (matchedRows.length === 0) {
            if (noMatchRow) noMatchRow.style.display = '';
            if (paginationInfo) paginationInfo.textContent = 'Showing 0 - 0 of 0';
            if (btnPrevPage) btnPrevPage.disabled = true;
            if (btnNextPage) btnNextPage.disabled = true;
            return;
        }

        if (noMatchRow) noMatchRow.style.display = 'none';

        const total = matchedRows.length;
        let startIndex = 0;
        let endIndex = total;

        if (pageSize !== 'all') {
            const size = parseInt(pageSize, 10);
            const totalPages = Math.ceil(total / size);
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            startIndex = (currentPage - 1) * size;
            endIndex = Math.min(startIndex + size, total);

            if (btnPrevPage) btnPrevPage.disabled = (currentPage <= 1);
            if (btnNextPage) btnNextPage.disabled = (currentPage >= totalPages);
        } else {
            if (btnPrevPage) btnPrevPage.disabled = true;
            if (btnNextPage) btnNextPage.disabled = true;
        }

        for (let i = startIndex; i < endIndex; i++) {
            matchedRows[i].style.display = '';
        }

        if (paginationInfo) {
            paginationInfo.textContent = `Showing ${startIndex + 1} - ${endIndex} of ${total}`;
        }

        if (tableScroll) tableScroll.scrollTop = 0;
    }

    // 4. Reset Filters
    window.resetAllFilters = function() {
        if (searchInput) searchInput.value = '';
        if (filterFlow) filterFlow.value = 'all';
        if (filterStatus) filterStatus.value = 'all';
        if (filterRangePreset) filterRangePreset.value = 'all_time';
        if (filterStartDate) filterStartDate.value = '';
        if (filterEndDate) filterEndDate.value = '';
        applyFilters();
    };

    // 5. Trigger Export with Current Filter Settings
    window.triggerRegisterExport = function(type) {
        const params = new URLSearchParams({
            export: 'excel'
        });
        if (searchInput && searchInput.value.trim() !== '') {
            params.set('q', searchInput.value.trim());
        }
        if (filterFlow && filterFlow.value !== 'all') {
            params.set('flow', filterFlow.value);
        }
        if (filterStatus && filterStatus.value !== 'all') {
            params.set('status', filterStatus.value);
        }
        if (filterStartDate && filterStartDate.value !== '') {
            params.set('start_date', filterStartDate.value);
        }
        if (filterEndDate && filterEndDate.value !== '') {
            params.set('end_date', filterEndDate.value);
        }

        window.location.href = '<?= APP_URL ?>/banking/ledger/<?= $account->id ?>?' + params.toString();
    };

    // Event Listeners
    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                searchInput.value = '';
                applyFilters();
            }
        });
    }
    if (searchClear) {
        searchClear.addEventListener('click', function() {
            searchInput.value = '';
            applyFilters();
            searchInput.focus();
        });
    }

    if (filterFlow) filterFlow.addEventListener('change', applyFilters);
    if (filterStatus) filterStatus.addEventListener('change', applyFilters);

    if (filterRangePreset) {
        filterRangePreset.addEventListener('change', function() {
            if (this.value !== 'custom') {
                updateDatePreset(this.value);
            }
            applyFilters();
        });
    }

    if (filterStartDate) {
        filterStartDate.addEventListener('change', function() {
            filterRangePreset.value = 'custom';
            applyFilters();
        });
    }
    if (filterEndDate) {
        filterEndDate.addEventListener('change', function() {
            filterRangePreset.value = 'custom';
            applyFilters();
        });
    }

    if (btnResetFilters) btnResetFilters.addEventListener('click', resetAllFilters);

    if (pageSizeSelect) {
        pageSizeSelect.addEventListener('change', function() {
            pageSize = this.value === 'all' ? 'all' : parseInt(this.value, 10);
            currentPage = 1;
            renderPaginatedView();
        });
    }

    if (btnPrevPage) {
        btnPrevPage.addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                renderPaginatedView();
            }
        });
    }

    if (btnNextPage) {
        btnNextPage.addEventListener('click', function() {
            currentPage++;
            renderPaginatedView();
        });
    }

    // Keyboard shortcut: Press '/' to focus floating search
    document.addEventListener('keydown', function(e) {
        if (e.key === '/' && document.activeElement !== searchInput && !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) {
            e.preventDefault();
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
    });

    // Initial Filter Run
    applyFilters();
});
</script>
