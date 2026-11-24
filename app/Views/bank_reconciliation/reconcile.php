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

<!-- Phosphor Icons Suite -->
<script src="https://unpkg.com/@phosphor-icons/web"></script>

<style>
/* ============================================================
   ERP PROFESSIONAL DESIGN STANDARDS — BANK RECONCILIATION (100VH)
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

/* Viewport Lock Layout (Mandatory Rule 1) */
.erp-rec-workspace {
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
    font-family: var(--f-main);
    color: var(--c-text-main);
}

/* 1. COMPACT TOP HEADER STRIP */
.rec-top-strip {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
    gap: 12px;
    padding: 2px 0;
}
.rec-header-info {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: nowrap;
    overflow: hidden;
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
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.15s ease;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    white-space: nowrap;
    flex-shrink: 0;
}
.btn-back-crumb:hover {
    background: #f8fafc;
    color: var(--c-text-main);
    border-color: #cbd5e1;
}
.rec-title-group {
    display: flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
}
.rec-title-group .rec-title {
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    letter-spacing: -0.01em;
}
.rec-badge-state {
    font-size: 11px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 999px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    white-space: nowrap;
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

.rec-header-meta {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: #64748b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.account-code-pill {
    font-family: var(--f-mono);
    font-size: 11px;
    font-weight: 700;
    background: #ffffff;
    color: #334155;
    padding: 2px 7px;
    border-radius: 6px;
    border: 1px solid var(--c-border);
}
.account-name-text {
    font-weight: 600;
    color: #0f172a;
}
.rec-meta-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    color: #64748b;
}

.rec-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
}
.action-btn-soft {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 12px;
    background: #ffffff;
    border: 1px solid var(--c-border);
    border-radius: 7px;
    font-size: 12px;
    font-weight: 600;
    color: #334155;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.15s ease;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    white-space: nowrap;
}
.action-btn-soft:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: var(--c-text-main);
}
.action-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 14px;
    background: #16a34a;
    border: 1px solid #15803d;
    border-radius: 7px;
    font-size: 12px;
    font-weight: 600;
    color: #ffffff;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.15s ease;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    white-space: nowrap;
}
.action-btn-primary:hover {
    background: #15803d;
}
.action-btn-primary:disabled {
    opacity: 0.45;
    cursor: not-allowed;
    background: #94a3b8;
    border-color: #94a3b8;
}
.action-btn-warning {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 12px;
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
    border-radius: 7px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    white-space: nowrap;
}
.action-btn-warning:hover {
    background: #fef3c7;
}

/* 2. KPI SUMMARY METRIC CARDS (High-Density Bloomberg/Apple Style) */
.rec-kpi-bar {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    flex-shrink: 0;
}
.rec-kpi-card {
    background: #ffffff;
    border: 1px solid var(--c-border);
    border-radius: 8px;
    padding: 8px 12px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
    transition: all 0.15s ease;
}
.rec-kpi-card.interactive {
    cursor: pointer;
}
.rec-kpi-card.interactive:hover {
    border-color: #93c5fd;
    background: #fafcff;
}
.rec-kpi-card .kpi-header-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2px;
}
.rec-kpi-card .kpi-label {
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    color: #64748b;
    letter-spacing: 0.04em;
}
.rec-kpi-card .kpi-edit-hint {
    font-size: 10px;
    font-weight: 600;
    color: #3b82f6;
    display: inline-flex;
    align-items: center;
    gap: 2px;
}
.rec-kpi-card .kpi-value {
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    font-family: var(--f-mono);
    font-variant-numeric: tabular-nums;
    line-height: 1.25;
}
.rec-kpi-card .kpi-sub {
    font-size: 11px;
    color: #94a3b8;
    margin-top: 1px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.rec-kpi-card.target {
    background: #f0f7ff;
    border-color: #bfdbfe;
}
.rec-kpi-card.target .kpi-label { color: #1d4ed8; }
.rec-kpi-card.target .kpi-value { color: #1e40af; }

.rec-kpi-card.diff-danger {
    background: #fef2f2;
    border-color: #fecaca;
}
.rec-kpi-card.diff-danger .kpi-label { color: #dc2626; }
.rec-kpi-card.diff-danger .kpi-value { color: #b91c1c; }
.rec-kpi-card.diff-danger .kpi-sub { color: #ef4444; }

.rec-kpi-card.diff-success {
    background: #f0fdf4;
    border-color: #bbf7d0;
}
.rec-kpi-card.diff-success .kpi-label { color: #16a34a; }
.rec-kpi-card.diff-success .kpi-value { color: #15803d; }
.rec-kpi-card.diff-success .kpi-sub { color: #22c55e; }

/* 3. SECONDARY METRICS SEGMENTED BAR */
.rec-secondary-bar {
    background: #ffffff;
    border: 1px solid var(--c-border);
    border-radius: 7px;
    padding: 5px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 11.5px;
    color: #64748b;
    flex-shrink: 0;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
}
.sec-metric-item {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.sec-metric-item .metric-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
}
.sec-metric-divider {
    width: 1px;
    height: 14px;
    background-color: #e2e8f0;
}
.num-mono {
    font-family: var(--f-mono);
    font-variant-numeric: tabular-nums;
}
.metric-val {
    font-weight: 700;
    font-family: var(--f-mono);
}

/* 4. MAIN TABLE CARD & CONTROLS */
.rec-table-card {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: #ffffff;
    border: 1px solid var(--c-border);
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03);
    min-height: 0;
    position: relative;
}

/* Dedicated Filter Toolbar (Mandatory Rule 2: Top-Right Corner Filters) */
.erp-filter-bar {
    background: #ffffff;
    border-bottom: 1px solid var(--c-border);
    padding: 6px 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
    gap: 10px;
}
.filter-left-count {
    font-size: 12px;
    color: #64748b;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 6px;
}
.filter-right-group {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-left: auto;
}
.erp-search-wrap {
    position: relative;
    display: inline-flex;
    align-items: center;
}
.erp-search-wrap i {
    position: absolute;
    left: 8px;
    color: #94a3b8;
    font-size: 13px;
    pointer-events: none;
}
.erp-filter-input {
    height: 28px;
    padding: 0 8px 0 26px;
    font-size: 11.5px;
    font-family: inherit;
    color: #1e293b;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    outline: none;
    transition: all 0.15s ease;
    width: 170px;
}
.erp-filter-input:focus {
    border-color: #2563eb;
    background: #ffffff;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
}
.erp-filter-select {
    height: 28px;
    padding: 0 8px;
    font-size: 11.5px;
    font-family: inherit;
    color: #1e293b;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    outline: none;
    transition: all 0.15s ease;
    cursor: pointer;
}
.erp-filter-select:focus {
    border-color: #2563eb;
    background: #ffffff;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
}
.btn-erp-reset {
    height: 28px;
    padding: 0 8px;
    font-size: 11.5px;
    font-weight: 600;
    color: #64748b;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all 0.15s ease;
}
.btn-erp-reset:hover {
    background: #e2e8f0;
    color: #0f172a;
}

/* 5. SCROLLABLE TABLE VIEWPORT (Rule 1) */
.erp-table-scroll {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: auto;
    background: #ffffff;
}
.erp-rec-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 12.5px;
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
    padding: 7px 12px;
    border-bottom: 1px solid #cbd5e1;
    white-space: nowrap;
}
.erp-rec-table tbody td {
    padding: 7px 12px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    color: #334155;
}
.erp-rec-table tbody tr:hover td {
    background-color: #f1f5f9 !important;
}

/* Mandatory Zebra Striping (Rule 3) */
.erp-rec-table tbody tr:nth-child(even) td {
    background-color: #fbfcfe;
}
.erp-rec-table tbody tr.row-matched td {
    background-color: rgba(240, 253, 244, 0.45);
}
.erp-rec-table tbody tr.row-matched:nth-child(even) td {
    background-color: rgba(236, 253, 243, 0.7);
}

/* Flow pill badges */
.badge-flow {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 7px;
    border-radius: 5px;
    white-space: nowrap;
}
.badge-flow-in {
    color: #15803d;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
}
.badge-flow-out {
    color: #b91c1c;
    background: #fef2f2;
    border: 1px solid #fecaca;
}

.badge-carried {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    font-size: 10px;
    font-weight: 700;
    color: #b45309;
    background: #fef3c7;
    padding: 1px 6px;
    border-radius: 4px;
    margin-left: 6px;
    white-space: nowrap;
}
.badge-bank-entry {
    font-size: 10px;
    font-weight: 700;
    color: #0369a1;
    background: #e0f2fe;
    padding: 1px 6px;
    border-radius: 4px;
    margin-left: 6px;
    white-space: nowrap;
}

/* 6. PINNED SUMMARY FOOTER */
.erp-pinned-footer {
    background: #ffffff;
    border-top: 1px solid var(--c-border);
    padding: 6px 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
    font-size: 11.5px;
    color: #64748b;
}

/* 7. MODALS */
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
    max-width: 480px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transform: scale(0.96);
    transition: transform 0.2s ease;
}
.erp-modal-overlay.active .erp-modal-box {
    transform: scale(1);
}
.erp-modal-header {
    padding: 12px 18px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.erp-modal-title {
    font-size: 14.5px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
}
.erp-modal-close {
    border: none;
    background: none;
    cursor: pointer;
    color: #94a3b8;
    font-size: 18px;
    line-height: 1;
    padding: 0;
}
.erp-modal-close:hover {
    color: #0f172a;
}
.erp-modal-body {
    padding: 16px 18px;
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
    margin-bottom: 12px;
}
.modal-form-group label {
    display: block;
    font-size: 11.5px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 4px;
}
.modal-input, .modal-select {
    width: 100%;
    height: 34px;
    padding: 0 10px;
    font-size: 12.5px;
    font-family: inherit;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    box-sizing: border-box;
    outline: none;
    background: #ffffff;
}
.modal-input:focus, .modal-select:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
}
</style>

<div class="erp-viewport-view erp-rec-workspace">
    <!-- Top Strip -->
    <div class="rec-top-strip">
        <div class="rec-header-info">
            <a href="<?= APP_URL ?>/bankreconciliation" class="btn-back-crumb">
                <i class="ph ph-arrow-left"></i> Reconciliations
            </a>
            <div class="rec-title-group">
                <h1 class="rec-title">Reconciliation Workspace</h1>
                <?php if ($isFinalized): ?>
                    <span class="rec-badge-state badge-finalized-mode">Finalized & Locked</span>
                <?php else: ?>
                    <span class="rec-badge-state badge-draft-mode">Active Draft</span>
                <?php endif; ?>
            </div>
            <div class="rec-header-meta">
                <span>&bull;</span>
                <span class="account-code-pill"><?= htmlspecialchars($rec->account_code); ?></span>
                <span class="account-name-text"><?= htmlspecialchars($rec->account_name); ?></span>
                <span>&bull;</span>
                <span class="rec-meta-chip num-mono">
                    <i class="ph ph-calendar-blank"></i> <?= date('M d, Y', strtotime($rec->statement_date_start)); ?> &rarr; <?= date('M d, Y', strtotime($rec->statement_date_end)); ?>
                </span>
                <span>&bull;</span>
                <span class="rec-meta-chip num-mono"><?= htmlspecialchars($rec->reconciliation_number); ?></span>
            </div>
        </div>

        <div class="rec-actions">
            <a href="<?= APP_URL ?>/bankreconciliation/report/<?= $rec->id ?>" class="action-btn-soft" target="_blank" title="Print formal reconciliation report">
                <i class="ph ph-printer"></i> Print Statement
            </a>
            <?php if (!$isFinalized): ?>
                <button type="button" class="action-btn-soft" onclick="openTargetsModal()" title="Edit statement opening or target ending balances">
                    <i class="ph ph-sliders"></i> Edit Targets
                </button>
                <button type="button" class="action-btn-soft" onclick="openBankOnlyModal()" title="Record bank charges, interest, or direct bank items">
                    <i class="ph ph-plus-circle"></i> Bank Adjustment
                </button>
                <button type="button" class="action-btn-primary" id="btnFinalize" <?= (abs($rec->difference) > 0.009) ? 'disabled' : '' ?>>
                    <i class="ph ph-lock-key"></i> Finalize Period
                </button>
            <?php else: ?>
                <button type="button" class="action-btn-warning" id="btnReopen">
                    <i class="ph ph-lock-key-open"></i> Reopen for Editing
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- 2. KPI Summary Grid -->
    <div class="rec-kpi-bar">
        <div class="rec-kpi-card <?= !$isFinalized ? 'interactive' : '' ?>" <?= !$isFinalized ? 'onclick="openTargetsModal()" title="Click to adjust statement opening balance"' : '' ?>>
            <div class="kpi-header-row">
                <span class="kpi-label">Statement Opening</span>
                <?php if (!$isFinalized): ?><span class="kpi-edit-hint"><i class="ph ph-pencil-simple"></i> Edit</span><?php endif; ?>
            </div>
            <div class="kpi-value" id="stmtOpenVal">LKR <?= number_format($rec->statement_opening_balance, 2) ?></div>
            <div class="kpi-sub"><?= !$isFinalized ? 'Confirmed prior month closing' : 'Finalized closing from prior scope' ?></div>
        </div>

        <div class="rec-kpi-card target <?= !$isFinalized ? 'interactive' : '' ?>" <?= !$isFinalized ? 'onclick="openTargetsModal()" title="Click to adjust target ending balance"' : '' ?>>
            <div class="kpi-header-row">
                <span class="kpi-label">Target Statement Ending</span>
                <?php if (!$isFinalized): ?><span class="kpi-edit-hint"><i class="ph ph-pencil-simple"></i> Edit</span><?php endif; ?>
            </div>
            <div class="kpi-value" id="stmtCloseVal">LKR <?= number_format($rec->statement_closing_balance, 2) ?></div>
            <div class="kpi-sub">Target balance on physical bank statement</div>
        </div>

        <div class="rec-kpi-card">
            <div class="kpi-header-row">
                <span class="kpi-label">Cleared in Bank</span>
            </div>
            <div class="kpi-value" id="clearedVal">LKR <?= number_format($clearedBalance, 2) ?></div>
            <div class="kpi-sub">Opening + Matched Deposits &minus; Payments</div>
        </div>

        <div class="rec-kpi-card <?= (abs($rec->difference) > 0.009) ? 'diff-danger' : 'diff-success' ?>" id="diffCard">
            <div class="kpi-header-row">
                <span class="kpi-label">Difference</span>
            </div>
            <div class="kpi-value" id="diffVal">LKR <?= number_format($rec->difference, 2) ?></div>
            <div class="kpi-sub" id="diffSub"><?= (abs($rec->difference) > 0.009) ? 'Unreconciled Difference' : '0.00 (Balanced & Matched)' ?></div>
        </div>
    </div>

    <!-- 3. Secondary Metrics Segmented Strip -->
    <div class="rec-secondary-bar">
        <div class="sec-metric-item">
            <span class="metric-dot" style="background-color: #16a34a;"></span>
            <span>Deposits in Transit (Unmatched Dr):</span>
            <span class="metric-val" style="color: #15803d;" id="transitVal">LKR <?= number_format($summary['deposits_in_transit'] ?? 0, 2) ?></span>
        </div>
        <div class="sec-metric-divider"></div>
        <div class="sec-metric-item">
            <span class="metric-dot" style="background-color: #dc2626;"></span>
            <span>Outstanding Cheques (Unmatched Cr):</span>
            <span class="metric-val" style="color: #b91c1c;" id="outstandingVal">LKR <?= number_format($summary['outstanding_cheques'] ?? 0, 2) ?></span>
        </div>
        <div class="sec-metric-divider"></div>
        <div class="sec-metric-item">
            <span>Adjusted Bank:</span>
            <span class="metric-val" id="adjBankVal">LKR <?= number_format($rec->adjusted_bank_balance, 2) ?></span>
        </div>
        <div class="sec-metric-divider"></div>
        <div class="sec-metric-item">
            <span>Adjusted Book (GL):</span>
            <span class="metric-val" id="adjBookVal">LKR <?= number_format($rec->adjusted_book_balance, 2) ?></span>
        </div>
    </div>

    <!-- 4. Table Card with Top-Right Filter Toolbar -->
    <div class="rec-table-card">
        <div class="erp-filter-bar">
            <div class="filter-left-count">
                <i class="ph ph-list-dashes" style="color: #64748b;"></i>
                <span id="filteredResultsLabel">Showing <?= count($data['items']); ?> transactions</span>
            </div>
            <div class="filter-right-group">
                <div class="erp-search-wrap">
                    <i class="ph ph-magnifying-glass"></i>
                    <input type="text" id="searchTerm" class="erp-filter-input" placeholder="Search ref, desc, amount...">
                </div>

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

                <button type="button" id="btnResetFilters" class="btn-erp-reset" title="Clear all filters">
                    <i class="ph ph-arrow-counter-clockwise"></i> Reset
                </button>
            </div>
        </div>

        <!-- 5. Scrollable Table (Rule 1) -->
        <div class="erp-table-scroll">
            <table class="erp-rec-table">
                <thead>
                    <tr>
                        <th style="text-align: center; width: 50px;">
                            <input type="checkbox" id="selectAllCheckbox" <?= $isFinalized ? 'disabled' : '' ?> title="Select / Unselect All Visible" style="cursor: pointer; width: 15px; height: 15px; accent-color: #2563eb;">
                        </th>
                        <th style="width: 105px;">Date</th>
                        <th style="width: 130px;">Ref / Cheque #</th>
                        <th>Description</th>
                        <th style="width: 140px;">Flow Type</th>
                        <th style="text-align: right; width: 160px;">Deposit / Inflow (Dr)</th>
                        <th style="text-align: right; width: 160px; padding-right: 16px;">Payment / Outflow (Cr)</th>
                    </tr>
                </thead>
                <tbody id="txTableBody">
                    <?php if (!empty($data['items'])): ?>
                        <?php foreach ($data['items'] as $item): ?>
                            <tr class="tx-row <?= $item->is_matched ? 'row-matched' : '' ?>" 
                                data-item-id="<?= $item->id ?>"
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
                                           style="width: 16px; height: 16px; cursor: pointer; accent-color: #2563eb;">
                                </td>
                                <td class="num-mono" style="white-space: nowrap; color: #475569;">
                                    <?= date('Y-m-d', strtotime($item->transaction_date)) ?>
                                </td>
                                <td class="num-mono" style="font-weight: 700; color: #0f172a; white-space: nowrap;">
                                    <?= htmlspecialchars($item->reference ?? '-') ?>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 4px;">
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
                                        <span class="badge-flow badge-flow-in">
                                            <i class="ph ph-arrow-down-left"></i> Inflow (Dr)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-flow badge-flow-out">
                                            <i class="ph ph-arrow-up-right"></i> Outflow (Cr)
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right; font-weight: 700;" class="num-mono">
                                    <?= ($item->debit > 0) ? '<span style="color: #15803d;">LKR ' . number_format($item->debit, 2) . '</span>' : '-' ?>
                                </td>
                                <td style="text-align: right; font-weight: 700; padding-right: 16px;" class="num-mono">
                                    <?= ($item->credit > 0) ? '<span style="color: #b91c1c;">LKR ' . number_format($item->credit, 2) . '</span>' : '-' ?>
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

        <!-- 6. Pinned Footer -->
        <div class="erp-pinned-footer">
            <div>
                <span>Double-Entry Matching System &bull; Bank Statement Verification</span>
            </div>
            <div>
                <span id="footerCount"><?= count($data['items']); ?> total transactions in period scope</span>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Bank-Only Entries with CSRF -->
<?php if (!$isFinalized): ?>
<div class="erp-modal-overlay" id="bankOnlyModal">
    <div class="erp-modal-box">
        <div class="erp-modal-header">
            <h3 class="erp-modal-title">
                <i class="ph ph-plus-circle" style="color: #2563eb;"></i> Record Bank Statement Adjustment
            </h3>
            <button type="button" class="erp-modal-close" onclick="closeBankOnlyModal()">&times;</button>
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
                <button type="button" class="action-btn-soft" onclick="closeBankOnlyModal()">Cancel</button>
                <button type="submit" class="action-btn-soft" style="background: #2563eb; color: #ffffff; border-color: #2563eb;">
                    <i class="ph ph-floppy-disk"></i> Save & Post Entry
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Statement Targets Modal -->
<div class="erp-modal-overlay" id="statementTargetsModal">
    <div class="erp-modal-box">
        <div class="erp-modal-header">
            <h3 class="erp-modal-title">
                <i class="ph ph-sliders" style="color: #2563eb;"></i> Edit Statement Targets
            </h3>
            <button type="button" class="erp-modal-close" onclick="closeTargetsModal()">&times;</button>
        </div>
        <form id="statementTargetsForm">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <input type="hidden" name="reconciliation_id" value="<?= $rec->id ?>">
            <div class="erp-modal-body">
                <div class="modal-form-group">
                    <label>Statement Opening Balance (LKR) <span style="color: #dc2626;">*</span></label>
                    <input type="number" step="0.01" name="statement_opening_balance" id="targetOpeningInput" class="modal-input num-mono" value="<?= floatval($rec->statement_opening_balance) ?>" required>
                    <div style="display: flex; gap: 6px; margin-top: 6px; flex-wrap: wrap;">
                        <button type="button" class="action-btn-soft" style="font-size: 11px; padding: 2px 8px;" onclick="document.getElementById('targetOpeningInput').value = '0.00';">Set to 0.00 (Opening in table)</button>
                        <?php if (floatval($rec->statement_opening_balance) < 0): ?>
                            <button type="button" class="action-btn-soft" style="font-size: 11px; padding: 2px 8px;" onclick="document.getElementById('targetOpeningInput').value = '<?= abs(floatval($rec->statement_opening_balance)) ?>';">Make Positive (+<?= number_format(abs(floatval($rec->statement_opening_balance)), 2) ?>)</button>
                        <?php endif; ?>
                    </div>
                    <div style="font-size: 11px; color: #64748b; margin-top: 5px;">
                        💡 <strong>Hint:</strong> If your opening balance journal entry (e.g., ADJ-...) is already checked as a row in the table below, set this to <code>0.00</code> to prevent double-counting.
                    </div>
                </div>
                <div class="modal-form-group">
                    <label>Target Statement Ending Balance (LKR) <span style="color: #dc2626;">*</span></label>
                    <input type="number" step="0.01" name="statement_closing_balance" id="targetClosingInput" class="modal-input num-mono" value="<?= floatval($rec->statement_closing_balance) ?>" required>
                    <div style="font-size: 11px; color: #64748b; margin-top: 4px;">Closing balance stated on physical bank statement.</div>
                </div>
            </div>
            <div class="erp-modal-footer">
                <button type="button" class="action-btn-soft" onclick="closeTargetsModal()">Cancel</button>
                <button type="submit" class="action-btn-soft" id="btnSaveTargets" style="background: #2563eb; color: #ffffff; border-color: #2563eb;">
                    <i class="ph ph-check-circle"></i> Save & Recalculate
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

function openTargetsModal() {
    const modal = document.getElementById('statementTargetsModal');
    if (modal) modal.classList.add('active');
}
function closeTargetsModal() {
    const modal = document.getElementById('statementTargetsModal');
    if (modal) modal.classList.remove('active');
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchTerm');
    const filterMatch = document.getElementById('filterMatch');
    const filterOrigin = document.getElementById('filterOrigin');
    const filterType = document.getElementById('filterType');
    const btnReset = document.getElementById('btnResetFilters');
    const resultsLabel = document.getElementById('filteredResultsLabel');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');

    function filterRows() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const matchVal = filterMatch ? filterMatch.value : 'all';
        const originVal = filterOrigin ? filterOrigin.value : 'all';
        const typeVal = filterType ? filterType.value : 'all';

        let visibleCount = 0;
        let visibleUnmatched = 0;
        let visibleMatched = 0;

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
                if (rowMatched === 'matched') visibleMatched++;
                else visibleUnmatched++;
            } else {
                row.style.display = 'none';
            }
        });

        if (resultsLabel) {
            resultsLabel.textContent = `Showing ${visibleCount} transactions`;
        }

        if (selectAllCheckbox) {
            if (visibleCount === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            } else if (visibleUnmatched === 0) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else if (visibleMatched === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            }
        }
    }

    if (searchInput) searchInput.addEventListener('input', filterRows);
    if (filterMatch) filterMatch.addEventListener('change', filterRows);
    if (filterOrigin) filterOrigin.addEventListener('change', filterRows);
    if (filterType) filterType.addEventListener('change', filterRows);

    if (btnReset) {
        btnReset.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            if (filterMatch) filterMatch.value = 'all';
            if (filterOrigin) filterOrigin.value = 'all';
            if (filterType) filterType.value = 'all';
            filterRows();
        });
    }

    // Function to apply returned summary data to DOM
    function applySummary(s) {
        if (!s) return;
        const clearedVal = document.getElementById('clearedVal');
        const diffVal = document.getElementById('diffVal');
        const transitVal = document.getElementById('transitVal');
        const outstandingVal = document.getElementById('outstandingVal');
        const adjBankVal = document.getElementById('adjBankVal');
        const adjBookVal = document.getElementById('adjBookVal');

        if (clearedVal) clearedVal.textContent = 'LKR ' + s.cleared_balance.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        if (diffVal) diffVal.textContent = 'LKR ' + s.difference.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        if (transitVal) transitVal.textContent = 'LKR ' + s.deposits_in_transit.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        if (outstandingVal) outstandingVal.textContent = 'LKR ' + s.outstanding_cheques.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        if (adjBankVal) adjBankVal.textContent = 'LKR ' + s.adjusted_bank.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        if (adjBookVal) adjBookVal.textContent = 'LKR ' + s.adjusted_book.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

        const diffCard = document.getElementById('diffCard');
        const diffSub = document.getElementById('diffSub');
        const btnFinalize = document.getElementById('btnFinalize');

        if (Math.abs(s.difference) < 0.009) {
            if (diffCard) diffCard.className = 'rec-kpi-card diff-success';
            if (diffSub) diffSub.textContent = '0.00 (Balanced & Matched)';
            if (btnFinalize) btnFinalize.disabled = false;
        } else {
            if (diffCard) diffCard.className = 'rec-kpi-card diff-danger';
            if (diffSub) diffSub.textContent = 'Unreconciled Difference';
            if (btnFinalize) btnFinalize.disabled = true;
        }
    }

    // Single item toggle
    function toggleItemMatch(cb, callback) {
        const itemId = cb.getAttribute('data-item-id');
        const isMatched = cb.checked ? 1 : 0;
        const row = cb.closest('.tx-row');

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
                if (isMatched) {
                    row.classList.add('row-matched');
                } else {
                    row.classList.remove('row-matched');
                }
                applySummary(data.summary);
                if (callback) callback(null, data);
            } else {
                alert(data.message || 'Failed to update match');
                cb.checked = !cb.checked;
                if (callback) callback(data.message);
            }
        })
        .catch(err => {
            console.error("Match error:", err);
            cb.checked = !cb.checked;
            if (callback) callback(err);
        });
    }

    // Attach to single checkboxes
    document.querySelectorAll('.match-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            toggleItemMatch(this, () => {
                filterRows();
            });
        });
    });

    // Select All Checkbox Handler
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const targetState = this.checked;
            const visibleCheckboxes = Array.from(document.querySelectorAll('.tx-row'))
                .filter(row => row.style.display !== 'none')
                .map(row => row.querySelector('.match-checkbox'))
                .filter(cb => cb && !cb.disabled && cb.checked !== targetState);

            if (visibleCheckboxes.length === 0) return;

            // Sequential batch execution to prevent race condition on backend summary calculation
            let index = 0;
            function processNext() {
                if (index >= visibleCheckboxes.length) {
                    filterRows();
                    return;
                }
                const cb = visibleCheckboxes[index];
                cb.checked = targetState;
                toggleItemMatch(cb, (err) => {
                    index++;
                    processNext();
                });
            }
            processNext();
        });
    }

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

    // AJAX Update Statement Targets
    const targetsForm = document.getElementById('statementTargetsForm');
    if (targetsForm) {
        targetsForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btnSave = document.getElementById('btnSaveTargets');
            if (btnSave) btnSave.disabled = true;

            const formData = new FormData(this);

            fetch('<?= APP_URL ?>/bankreconciliation/update_statement_ajax', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (btnSave) btnSave.disabled = false;
                if (data.status === 'success' && data.summary) {
                    closeTargetsModal();

                    document.getElementById('stmtOpenVal').textContent = 'LKR ' + (data.statement_opening).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    document.getElementById('stmtCloseVal').textContent = 'LKR ' + (data.statement_closing).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    applySummary(data.summary);
                } else {
                    alert(data.message || 'Failed to update statement targets.');
                }
            })
            .catch(err => {
                if (btnSave) btnSave.disabled = false;
                console.error("Statement targets update error:", err);
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

    // Initial check for Select All state
    filterRows();
});
</script>
