<?php
$totalSuppliers = count($data['suppliers'] ?? []);
$totalOutstanding = 0;
$owedSuppliersCount = 0;
foreach ($data['suppliers'] ?? [] as $s) {
    $bal = floatval($s->outstanding_balance ?? 0);
    $totalOutstanding += $bal;
    if ($bal > 0) {
        $owedSuppliersCount++;
    }
}
$csrfToken = $data['csrf_token'] ?? ($_SESSION['csrf_token'] ?? '');
?>

<!-- FontAwesome 6 Icons Guarantee -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
/* ============================================================
   FALCON STATIONERY ERP: SUPPLIER CENTER ARCHITECTURE
   SF PRO DISPLAY + VIEWPORT-LOCKED ERP STANDARDS (RULES 1, 2, 3)
   ============================================================ */

@font-face {
    font-family: 'SF Pro Display';
    src: url('<?= APP_URL ?>/public/assets/fonts/sf_pro_display_regular.ttf') format('truetype');
    font-weight: 400;
}
@font-face {
    font-family: 'SF Pro Display';
    src: url('<?= APP_URL ?>/public/assets/fonts/sf_pro_display_medium.ttf') format('truetype');
    font-weight: 500;
}
@font-face {
    font-family: 'SF Pro Display';
    src: url('<?= APP_URL ?>/public/assets/fonts/sf_pro_display_semibold.ttf') format('truetype');
    font-weight: 600;
}
@font-face {
    font-family: 'SF Pro Display';
    src: url('<?= APP_URL ?>/public/assets/fonts/sf_pro_display_bold.ttf') format('truetype');
    font-weight: 700;
}

:root {
    --erp-bg: #f8fafc;
    --erp-card: #ffffff;
    --erp-border: #e2e8f0;
    --erp-text: #0f172a;
    --erp-muted: #64748b;
    --erp-font: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    --erp-mono: 'SF Mono', ui-monospace, Menlo, Monaco, Consolas, monospace;
}

html, body {
    height: 100vh !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: hidden !important;
    background: var(--erp-bg, #f8fafc);
    font-family: var(--erp-font);
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

.erp-viewport-view.sup-erp-workspace {
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 0;
    max-height: 100%;
    flex: 1;
    overflow: hidden;
    gap: 6px;
    box-sizing: border-box;
    position: relative;
    padding: 0; /* Zero bottom padding so table reaches bottom of screen */
}

/* 1. Sleek Compact KPI Stats Strip */
.erp-stats-strip {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    flex-shrink: 0;
}

.stat-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 9999px;
    padding: 3px 12px;
    font-size: 12px;
    color: #475569;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
}
.stat-pill strong {
    color: #0f172a;
    font-weight: 700;
    font-family: var(--erp-mono);
}
.badge-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    flex-shrink: 0;
}

/* 2. Top-Right Clean Filter Bar Above Table (RULE 2) */
.erp-filter-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 5px 14px;
    flex-shrink: 0;
    gap: 12px;
}

.erp-filter-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

.erp-results-badge {
    font-size: 12px;
    color: #64748b;
    white-space: nowrap;
    font-weight: 500;
}
.erp-results-badge strong {
    color: #0f172a;
    font-family: var(--erp-font);
    font-weight: 700;
}

.erp-filter-right {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: nowrap;
    justify-content: flex-end;
}

.erp-filter-control {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
}

.erp-filter-label {
    font-size: 10.5px;
    font-weight: 700;
    color: #475569;
    white-space: nowrap;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.erp-select {
    height: 28px;
    max-width: 165px;
    padding: 0 24px 0 10px;
    font-size: 12px;
    font-weight: 500;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    background: #ffffff;
    color: #1e293b;
    outline: none;
    font-family: var(--erp-font);
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 7px center;
    background-size: 10px;
    transition: border-color 0.15s, box-shadow 0.15s;
    text-overflow: ellipsis;
}
.erp-select:hover {
    border-color: #94a3b8;
}
.erp-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.12);
}

.erp-btn-reset {
    height: 28px;
    padding: 0 11px;
    font-size: 12px;
    font-weight: 600;
    color: #475569;
    background: #ffffff;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.15s;
}
.erp-btn-reset:hover {
    background: #f8fafc;
    color: #0f172a;
    border-color: #94a3b8;
}

/* 3. Table Card with Dedicated Independent Scroll (RULE 1 & 3) */
.erp-card-table {
    flex: 1;
    min-height: 0;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    position: relative;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

.erp-table-scroll {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: auto;
    position: relative;
    padding-bottom: 54px; /* Clearance inside table scroll so bottom rows clear the floating island */
}

.erp-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12.5px;
    text-align: left;
}

.erp-table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    background: #f8fafc;
    color: #475569;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    padding: 8px 14px;
    border-bottom: 1px solid #e2e8f0;
    white-space: nowrap;
}

.erp-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.1s;
    cursor: pointer;
}
.erp-table tbody tr:hover {
    background: #f8fafc !important;
}
/* Alternating Zebra Striping (RULE 3.2) */
.erp-table tbody tr:nth-child(even) {
    background-color: #f8fafc !important;
}
.erp-table tbody tr:nth-child(odd) {
    background-color: #ffffff !important;
}

.erp-table td {
    padding: 8px 14px;
    vertical-align: middle;
    color: #1e293b;
}

.font-mono {
    font-family: var(--erp-mono, 'SF Mono', monospace);
}

.avatar-circle {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #f1f5f9;
    color: #475569;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 700;
    flex-shrink: 0;
}

/* Badges */
.sf-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.02em;
    white-space: nowrap;
}
.sf-badge.badge-active { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
.sf-badge.badge-owed   { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
.sf-badge.badge-new    { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
.sf-badge.badge-Voided { background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; }
.sf-badge .dot { width: 5px; height: 5px; border-radius: 50%; background: currentColor; }

/* Interactive iOS Toggle Switch (RULE 3.5) */
.erp-switch {
    position: relative;
    display: inline-block;
    width: 32px;
    height: 18px;
    vertical-align: middle;
    margin: 0;
    cursor: pointer;
}
.erp-switch input {
    opacity: 0;
    width: 0;
    height: 0;
    position: absolute;
}
.erp-slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #cbd5e1;
    transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 18px;
}
.erp-slider:before {
    position: absolute;
    content: "";
    height: 14px;
    width: 14px;
    left: 2px;
    bottom: 2px;
    background-color: white;
    transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0,0,0,0.25);
}
.erp-switch input:checked + .erp-slider {
    background-color: #10b981;
}
.erp-switch input:checked + .erp-slider:before {
    transform: translateX(14px);
}

/* Action Buttons (Ghost Icon Style) */
.erp-row-actions {
    display: flex;
    align-items: center;
    gap: 4px;
    justify-content: center;
}
.erp-icon-btn {
    width: 25px;
    height: 25px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    font-size: 11px;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.12s ease;
    padding: 0;
}
.erp-icon-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
    border-color: #cbd5e1;
}
.erp-icon-btn.view { color: #2563eb; }
.erp-icon-btn.view:hover { background: #eff6ff; border-color: #bfdbfe; color: #1d4ed8; }

/* 4. Pinned Pagination Footer (RULE 1) */
.erp-pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px 14px;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    flex-shrink: 0;
    font-size: 12px;
    color: #64748b;
}
.erp-page-controls {
    display: flex;
    align-items: center;
    gap: 4px;
}
.erp-page-btn {
    min-width: 25px;
    height: 25px;
    padding: 0 5px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #334155;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.12s;
}
.erp-page-btn:hover:not(.disabled) {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: #0f172a;
}
.erp-page-btn.active {
    background: #2563eb;
    color: #ffffff;
    border-color: #2563eb;
}
.erp-page-btn.disabled {
    opacity: 0.45;
    cursor: not-allowed;
    pointer-events: none;
}

/* 5. Sleek Floating Island Bar (Search Bar + Add New Button) - Standard Inventory Style */
.cmd-bar {
    position: fixed;
    bottom: 24px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(24, 24, 27, 0.95);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-radius: 9999px;
    padding: 7px 10px 7px 18px;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: 0 12px 36px rgba(0, 0, 0, 0.3), 0 4px 12px rgba(0, 0, 0, 0.15);
    z-index: 100;
}
.cmd-search {
    display: flex;
    align-items: center;
    gap: 10px;
    position: relative;
    width: 320px;
}
.cmd-search i {
    color: rgba(255, 255, 255, 0.65);
    font-size: 14px;
    flex-shrink: 0;
}
.cmd-search input {
    background: transparent;
    border: none;
    outline: none;
    color: #ffffff;
    font-size: 14px;
    font-weight: 500;
    width: 100%;
    font-family: var(--erp-font);
    padding: 3px 0;
}
.cmd-search input::placeholder {
    color: rgba(255, 255, 255, 0.48);
}
.cmd-divider {
    width: 1px;
    height: 22px;
    background: rgba(255, 255, 255, 0.18);
}
.cmd-icon {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255, 255, 255, 0.85);
    font-size: 13px;
    background: transparent;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: background 0.15s;
}
.cmd-icon:hover {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff;
}
.cmd-cta {
    background: #ffffff;
    color: #0f172a !important;
    font-weight: 600;
    font-size: 13px;
    padding: 6px 16px;
    border-radius: 9999px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    white-space: nowrap;
    transition: transform 0.15s ease, background 0.15s ease, box-shadow 0.15s ease;
    box-shadow: 0 2px 6px rgba(0,0,0,0.12);
}
.cmd-cta:hover {
    background: #f8fafc;
    transform: scale(1.02);
}

/* Modals */
.modal-veil {
    position: fixed; inset: 0; z-index: 2000;
    background: rgba(0, 0, 0, 0.45);
    backdrop-filter: blur(6px);
    display: flex; align-items: center; justify-content: center;
}
.modal-veil.hidden { display: none; }
.sf-modal {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1);
    width: 520px; max-width: 95vw;
    overflow: hidden;
}
.modal-head {
    display: flex; justify-content: space-between; align-items: center;
    padding: 12px 18px; border-bottom: 1px solid #e2e8f0;
    background: #f8fafc;
}
.modal-title { font-size: 14.5px; font-weight: 700; margin: 0; color: #0f172a; }
.modal-close {
    background: transparent; border: none; width: 26px; height: 26px;
    border-radius: 4px; display: flex; align-items: center; justify-content: center;
    color: #64748b; cursor: pointer; font-size: 14px;
}
.modal-close:hover { background: #e2e8f0; color: #0f172a; }
.modal-body { padding: 16px 18px; }
.modal-foot {
    display: flex; justify-content: flex-end; gap: 8px;
    padding: 10px 18px; background: #f8fafc;
    border-top: 1px solid #e2e8f0;
}

.sf-btn {
    padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;
    display: inline-flex; align-items: center; gap: 6px; border: 1px solid transparent; cursor: pointer;
}
.sf-btn.primary { background: #2563eb; color: #fff; }
.sf-btn.primary:hover { background: #1d4ed8; }
.sf-btn.neutral { background: #fff; border-color: #d1d5db; color: #334155; }
.sf-btn.neutral:hover { background: #f8fafc; }
.sf-btn.danger { background: #dc2626; color: #fff; }
.sf-btn.danger:hover { background: #b91c1c; }
.sf-btn.success { background: #16a34a; color: #fff; }
.sf-btn.success:hover { background: #15803d; }
.sf-btn-small { padding: 4px 8px; font-size: 11px; }

.sf-group { margin-bottom: 10px; }
.sf-group label { display: block; margin-bottom: 4px; font-size: 10.5px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.03em; }
.sf-input {
    width: 100%; padding: 7px 9px;
    border-radius: 6px; border: 1px solid #d1d5db;
    background: #ffffff; color: #0f172a;
    font-size: 12.5px; outline: none; box-sizing: border-box; font-family: var(--erp-font);
}
.sf-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.12); }
.grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }

/* Tabs inside Profile Modal */
.tabs { display: flex; border-bottom: 1px solid #e2e8f0; background: #f8fafc; padding: 0 16px; }
.tab-btn {
    padding: 9px 14px; border: none; background: transparent; cursor: pointer;
    font-size: 12px; font-weight: 600; color: #64748b;
    border-bottom: 2px solid transparent; transition: 0.15s;
}
.tab-btn:hover { color: #2563eb; }
.tab-btn.active { color: #2563eb; border-bottom-color: #2563eb; }
.tab-content { display: none; }
.tab-content.active { display: block; }

/* Modal Data Table with Sticky Header & Zebra Striping (RULE 3.2) */
.data-table { width: 100%; border-collapse: collapse; margin-top: 6px; }
.data-table thead th {
    position: sticky;
    top: 0;
    z-index: 5;
    background: #f8fafc;
    color: #475569;
    font-weight: 700;
    font-size: 10px;
    text-transform: uppercase;
    padding: 8px 10px;
    border-bottom: 2px solid #e2e8f0;
}
.data-table tbody td {
    padding: 8px 10px;
    text-align: left;
    border-bottom: 1px solid #f1f5f9;
    font-size: 12px;
}
.data-table tbody tr:nth-child(even) { background-color: #f8fafc !important; }
.data-table tbody tr:nth-child(odd) { background-color: #ffffff !important; }
.data-table tbody tr:hover { background-color: #f1f5f9 !important; }
.num-col { text-align: right !important; }

@keyframes spin { to { transform: rotate(360deg); } }
.spin { animation: spin 0.7s linear infinite; display: inline-block; }
.hidden { display: none !important; }
</style>

<script>
    // Ensure viewport-lock standards on parent layout
    document.body.classList.add('erp-locked');
</script>

<div class="erp-viewport-view sup-erp-workspace">

    <!-- Top Compact KPI Stats Strip (RULE 3) -->
    <div class="erp-stats-strip">
        <div class="stat-pill">
            <span class="badge-dot" style="background:#2563eb;"></span>
            Total Suppliers: <strong id="stat-total-suppliers"><?= number_format($totalSuppliers) ?></strong>
        </div>
        <div class="stat-pill">
            <span class="badge-dot" style="background:#ea580c;"></span>
            Total Payable Outstanding: <strong id="stat-total-outstanding">Rs. <?= number_format($totalOutstanding, 2) ?></strong>
        </div>
        <div class="stat-pill">
            <span class="badge-dot" style="background:#dc2626;"></span>
            Accounts to Settle: <strong id="stat-owed-count"><?= number_format($owedSuppliersCount) ?></strong>
        </div>
    </div>

    <!-- Notifications -->
    <?php if (!empty($data['error'])): ?>
        <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:6px; padding:6px 12px; font-size:12px; color:#991b1b; display:flex; align-items:center; justify-content:space-between; flex-shrink:0;">
            <div style="display:flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span><?= htmlspecialchars($data['error']) ?></span>
            </div>
            <button type="button" style="background:none; border:none; color:#991b1b; cursor:pointer;" onclick="this.parentElement.remove()"><i class="fa-solid fa-xmark"></i></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($data['success'])): ?>
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:6px; padding:6px 12px; font-size:12px; color:#166534; display:flex; align-items:center; justify-content:space-between; flex-shrink:0;">
            <div style="display:flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-circle-check"></i>
                <span><?= htmlspecialchars($data['success']) ?></span>
            </div>
            <button type="button" style="background:none; border:none; color:#166534; cursor:pointer;" onclick="this.parentElement.remove()"><i class="fa-solid fa-xmark"></i></button>
        </div>
    <?php endif; ?>

    <!-- Top-Right Clean Filter Toolbar (RULE 2) -->
    <div class="erp-filter-bar">
        <div class="erp-filter-left">
            <span class="erp-results-badge">Showing <strong id="matching-count"><?= count($data['suppliers']) ?></strong> suppliers</span>
        </div>
        <div class="erp-filter-right">
            <!-- Ledger Filter -->
            <div class="erp-filter-control">
                <span class="erp-filter-label">Ledger:</span>
                <select id="filterStatus" class="erp-select" onchange="filterSuppliersList()">
                    <option value="">All Accounts</option>
                    <option value="owed">Has Unpaid Balance</option>
                    <option value="cleared">Zero Balance</option>
                </select>
            </div>

            <!-- Operational Status Filter -->
            <div class="erp-filter-control">
                <span class="erp-filter-label">Status:</span>
                <select id="filterAccountStatus" class="erp-select" onchange="filterSuppliersList()">
                    <option value="">All Statuses</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>

            <!-- Reset Button -->
            <button type="button" class="erp-btn-reset" onclick="clearAllFilters()">
                <i class="fa-solid fa-arrow-rotate-left"></i> Reset
            </button>
        </div>
    </div>

    <!-- Table Card with Dedicated Independent Scroll (RULE 1 & 3) -->
    <div class="erp-card-table">
        <div class="erp-table-scroll">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th>Supplier / Company</th>
                        <th>Contact Details</th>
                        <th style="text-align:right;">Outstanding Balance</th>
                        <th>Ledger Status</th>
                        <th style="text-align:center; width:90px;">Active</th>
                        <th style="text-align:center; width:80px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="supList">
                    <?php if (empty($data['suppliers'])): ?>
                        <tr>
                            <td colspan="6" style="text-align:center; padding:35px; color:#64748b;">
                                <i class="fa-solid fa-building" style="font-size:24px; margin-bottom:6px; color:#cbd5e1;"></i><br>
                                No suppliers registered yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($data['suppliers'] as $s): ?>
                            <?php 
                            $bal = floatval($s->outstanding_balance); 
                            $badgeCls = $bal > 0 ? 'badge-owed' : 'badge-active';
                            $badgeTxt = $bal > 0 ? 'Owed' : 'Cleared';
                            $accountStatus = $s->status ?? 'Active';
                            $isActive = ($accountStatus === 'Active');
                            ?>
                            <tr class="supplier-row" 
                                onclick="openSupplierModalPopup(<?= $s->id ?>)"
                                data-id="<?= $s->id ?>"
                                data-name="<?= htmlspecialchars(strtolower($s->name ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                data-phone="<?= htmlspecialchars(strtolower($s->phone ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                data-email="<?= htmlspecialchars(strtolower($s->email ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                data-outstanding="<?= $bal ?>"
                                data-status="<?= $accountStatus ?>">
                                
                                <td>
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <div class="avatar-circle"><?= strtoupper(substr($s->name ?? '', 0, 2)) ?></div>
                                        <div>
                                            <strong style="font-size:13px; font-weight:600; color:#0f172a;"><?= htmlspecialchars($s->name ?? '') ?></strong>
                                            <span style="font-size:11px; color:#64748b; display:block; margin-top:1px;">
                                                🏠 <?= !empty($s->address) ? htmlspecialchars($s->address) : '<span style="color:#dc2626; font-weight:600;">Missing Address</span>' ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="font-mono" style="font-size:12px; font-weight:500; display:block;">
                                        📞 <?= !empty($s->phone) ? htmlspecialchars($s->phone) : '<span style="color:#dc2626; font-weight:600;">Missing Phone</span>' ?>
                                    </span>
                                    <?php if (!empty($s->email)): ?>
                                        <span style="font-size:11px; color:#64748b; display:block; margin-top:1px;">
                                            <?= htmlspecialchars($s->email) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align:right; font-weight:700; font-family:var(--erp-mono); font-size:13px; color: <?= $bal > 0 ? '#dc2626' : '#16a34a' ?>;">
                                    Rs. <?= number_format($bal, 2) ?>
                                </td>
                                <td>
                                    <span class="sf-badge <?= $badgeCls ?>">
                                        <span class="dot"></span><?= $badgeTxt ?>
                                    </span>
                                </td>
                                <td style="text-align:center;" onclick="event.stopPropagation()">
                                    <!-- Interactive iOS Status Switch (RULE 3.5) -->
                                    <label class="erp-switch" title="Toggle Active / Inactive Status">
                                        <input type="checkbox" <?= $isActive ? 'checked' : '' ?> onchange="toggleSupplierStatus(<?= $s->id ?>, this)">
                                        <span class="erp-slider"></span>
                                    </label>
                                </td>
                                <td>
                                    <div class="erp-row-actions" onclick="event.stopPropagation()">
                                        <button type="button" class="erp-icon-btn view" onclick="openSupplierModalPopup(<?= $s->id ?>)" title="View Ledger & Profile">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pinned Pagination Footer (RULE 1) -->
        <div class="erp-pagination">
            <div id="pg-info-text">
                Showing <strong>1</strong> – <strong><?= min(15, count($data['suppliers'])) ?></strong> of <strong><?= count($data['suppliers']) ?></strong>
            </div>
            <div style="display:flex; align-items:center; gap:14px;">
                <div style="display:flex; align-items:center; gap:6px;">
                    <span style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase;">Show:</span>
                    <select class="erp-select" style="height:25px; max-width:70px; padding:0 18px 0 6px;" onchange="updatePageSize(this.value)">
                        <option value="15" selected>15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="999999">All</option>
                    </select>
                </div>
                <div class="erp-page-controls" id="pg-nav-container">
                    <button type="button" class="erp-page-btn" id="pg-prev-btn" onclick="navigatePage(currentPage - 1)">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <span id="pg-current-text" style="font-weight:600; padding:0 8px; font-size:12px; color:#0f172a;">1 / 1</span>
                    <button type="button" class="erp-page-btn" id="pg-next-btn" onclick="navigatePage(currentPage + 1)">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
         FLOATING BOTTOM ISLAND COMMAND BAR (RULE 3)
         ============================================================ -->
    <div class="cmd-bar">
        <div class="cmd-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="searchInput" placeholder="Search supplier, phone, email..." onkeyup="filterSuppliersList()">
        </div>
        <div class="cmd-divider"></div>
        <a href="<?= APP_URL ?>/supplier/exportCSV" class="cmd-icon" title="Export Suppliers to CSV"><i class="fa-solid fa-download"></i></a>
        <button type="button" class="cmd-cta" onclick="openModal('addSupplierModal')"><i class="fa-solid fa-plus"></i> New Supplier</button>
    </div>

</div>

<!-- ============================================================
     POPUP MODAL: SUPPLIER PROFILE LEDGER & DETAILS
     ============================================================ -->
<div id="supplierProfileModal" class="modal-veil hidden" onclick="if(event.target === this) closeSupplierProfile()">
    <div class="sf-modal" style="width: 85%; max-width: 1000px; height: 85vh; display: flex; flex-direction: column; padding: 0;">
        
        <div class="modal-head" id="modal-header-container">
            <div style="display: flex; gap: 12px; align-items: center;">
                <div class="avatar-circle" style="width: 34px; height: 34px; font-size: 13px;">S</div>
                <div>
                    <h3 class="modal-title">Supplier Details</h3>
                </div>
            </div>
            <button type="button" onclick="closeSupplierProfile()" class="modal-close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <div id="modal-loader" style="display:none; flex:1; align-items:center; justify-content:center; flex-direction:column; gap:10px; background:#fff;">
            <i class="fa-solid fa-spinner spin" style="font-size:28px; color:#2563eb;"></i>
            <span style="font-size:12.5px; color:#64748b; font-weight:500;">Loading supplier profile...</span>
        </div>
        
        <div id="modal-profile-content" style="flex:1; display:flex; flex-direction:column; overflow:hidden;">
            <!-- Content will load dynamically here -->
        </div>
    </div>
</div>

<!-- ============================================================
     HIDDEN TEMPLATE SOURCES (EXTRACTED VIA DOM PARSER)
     ============================================================ -->
<?php if (!empty($data['selected_supplier'])): ?>
    <?php $sup = $data['selected_supplier']; $stats = $data['stats']; ?>
    
    <div id="modal-header-source" class="hidden">
        <div style="display: flex; gap: 12px; align-items: center; min-width: 0; flex: 1;">
            <div class="avatar-circle" style="width: 36px; height: 36px; font-size: 14px; background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; flex-shrink: 0;">
                <?= strtoupper(substr($sup->name ?? '', 0, 2)) ?>
            </div>
            <div style="min-width: 0; max-width: 250px;">
                <h3 class="modal-title" style="font-size: 15px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0;" title="<?= htmlspecialchars($sup->name ?? '') ?>">
                    <?= htmlspecialchars($sup->name ?? '') ?>
                </h3>
                <div style="font-size: 11px; color: #64748b; display: flex; gap: 10px; margin-top: 2px; white-space: nowrap; overflow: hidden; align-items: center;">
                    <span>📞 <?= !empty($sup->phone) ? htmlspecialchars($sup->phone) : '<span style="color:#dc2626; font-weight:600;">Missing Phone</span>' ?></span>
                    <span>✉️ <?= !empty($sup->email) ? htmlspecialchars($sup->email) : '<span style="color:#dc2626; font-weight:600;">Missing Email</span>' ?></span>
                </div>
            </div>
        </div>
        
        <div style="margin-right: 12px; flex-shrink: 0;">
            <a href="<?= APP_URL ?>/report/viewer/supplier_statement?supplier=<?= $sup->id ?>&start_date=2000-01-01&end_date=2099-12-31" target="_blank" class="sf-btn neutral" style="padding: 5px 10px; font-size: 11.5px; gap: 5px;" title="Export detailed supplier statement">
                <i class="fa-solid fa-file-export" style="color:#2563eb;"></i> Export Statement
            </a>
        </div>

        <!-- Header Statistics Cards -->
        <div style="display: flex; gap: 8px; margin-right: 14px; flex-shrink: 0; align-items: center;">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 4px 10px; border-radius: 6px; text-align: center; min-width: 90px;">
                <div style="font-size: 8.5px; color: #64748b; text-transform: uppercase; font-weight: 700; letter-spacing: 0.04em;">Total Billed</div>
                <div style="font-size: 12px; font-weight: bold; color: #0f172a; margin-top: 1px; font-family: var(--erp-mono);">Rs. <?= number_format($stats->total_billed ?? 0, 2) ?></div>
            </div>
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; padding: 4px 10px; border-radius: 6px; text-align: center; min-width: 90px;">
                <div style="font-size: 8.5px; color: #166534; text-transform: uppercase; font-weight: 700; letter-spacing: 0.04em;">Total Paid</div>
                <div style="font-size: 12px; font-weight: bold; color: #166534; margin-top: 1px; font-family: var(--erp-mono);">Rs. <?= number_format($stats->total_paid ?? 0, 2) ?></div>
            </div>
            <div style="background: <?= ($stats->outstanding ?? 0) > 0 ? '#fef2f2' : '#f0fdf4' ?>; border: 1px solid <?= ($stats->outstanding ?? 0) > 0 ? '#fecaca' : '#bbf7d0' ?>; padding: 4px 10px; border-radius: 6px; text-align: center; min-width: 90px;">
                <div style="font-size: 8.5px; color: <?= ($stats->outstanding ?? 0) > 0 ? '#991b1b' : '#166534' ?>; text-transform: uppercase; font-weight: 700; letter-spacing: 0.04em;">Outstanding</div>
                <div style="font-size: 12px; font-weight: bold; color: <?= ($stats->outstanding ?? 0) > 0 ? '#991b1b' : '#166534' ?>; margin-top: 1px; font-family: var(--erp-mono);">Rs. <?= number_format($stats->outstanding ?? 0, 2) ?></div>
            </div>
        </div>

        <button type="button" onclick="closeSupplierProfile()" class="modal-close" style="width:26px; height:26px; flex-shrink: 0;"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div id="modal-profile-content-source" class="hidden">
        <!-- Tab Navigation -->
        <div class="tabs">
            <button class="tab-btn active" onclick="switchModalTab('ledger')" id="mbtn_ledger">Activity Ledger</button>
            <button class="tab-btn" onclick="switchModalTab('pos')" id="mbtn_pos">Purchase Orders</button>
            <button class="tab-btn" onclick="switchModalTab('products')" id="mbtn_products">Products List</button>
            <button class="tab-btn" onclick="switchModalTab('profile')" id="mbtn_profile">Profile & Settings</button>
        </div>

        <!-- TAB 1: Ledger -->
        <div id="mtab_ledger" class="tab-content active" style="padding: 16px; overflow-y: auto; flex: 1;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Ref / Desc</th>
                        <th class="num-col">Dr (Paid/Ret)</th>
                        <th class="num-col">Cr (Billed)</th>
                        <th class="num-col">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($data['ledger'])): ?>
                        <tr><td colspan="6" style="text-align: center; color: #64748b; padding: 20px;">No financial activity recorded.</td></tr>
                    <?php else: foreach($data['ledger'] as $l): ?>
                        <tr>
                            <td style="color:#64748b; font-size:12px;"><?= date('M d, Y', strtotime($l->date)) ?></td>
                            <td><strong><?= $l->type ?></strong></td>
                            <td>
                                <?php if($l->type == 'GRN'): ?>
                                    <a href="<?= APP_URL ?>/grn/show/<?= $l->id ?>" target="_blank" style="color:#2563eb; font-weight:bold; text-decoration:none;">
                                        <?= htmlspecialchars($l->ref) ?> ↗
                                    </a>
                                <?php elseif($l->type == 'Payment'): ?>
                                    <a href="<?= APP_URL ?>/payment/receipt/<?= $l->id ?>" target="_blank" style="color:#16a34a; font-weight:bold; text-decoration:none;" title="View Payment Voucher">
                                        <?= htmlspecialchars($l->ref) ?> ↗
                                    </a>
                                <?php elseif($l->type == 'Supplier Return'): ?>
                                    <a href="<?= APP_URL ?>/supplier-return/show/<?= $l->id ?>" target="_blank" style="color:#dc2626; font-weight:bold; text-decoration:none;">
                                        <?= htmlspecialchars($l->ref) ?> ↗
                                    </a>
                                <?php elseif($l->type == 'Journal Entry'): ?>
                                    <a href="<?= APP_URL ?>/accounting/print_journal/<?= $l->id ?>" target="_blank" style="color:#2563eb; font-weight:bold; text-decoration:none;" title="View Journal Voucher">
                                        <?= htmlspecialchars($l->ref) ?> ↗
                                    </a>
                                <?php elseif($l->type == 'Payment Reversal'): ?>
                                    <a href="<?= APP_URL ?>/payment/receipt/<?= $l->id ?>" target="_blank" style="color: #ea580c; font-size: 11px; font-weight:600; background: #fffbeb; padding: 2px 6px; border-radius: 4px; text-decoration:none;" title="View Reversed Payment">
                                        <?= htmlspecialchars($l->ref) ?> ↗
                                    </a>
                                <?php else: ?>
                                    <span style="color:#0f172a;"><?= htmlspecialchars($l->ref) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="num-col" style="color:#16a34a; font-weight:500; font-family:var(--erp-mono);"><?= $l->debit > 0 ? 'Rs. ' . number_format($l->debit, 2) : '-' ?></td>
                            <td class="num-col" style="font-weight:500; font-family:var(--erp-mono);"><?= $l->credit > 0 ? 'Rs. ' . number_format($l->credit, 2) : '-' ?></td>
                            <td class="num-col" style="font-weight:bold; font-family:var(--erp-mono); color: <?= $l->balance > 0 ? '#dc2626' : '#16a34a' ?>;">Rs. <?= number_format($l->balance, 2) ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <!-- TAB 2: Purchase Orders -->
        <div id="mtab_pos" class="tab-content" style="padding: 16px; overflow-y: auto; flex: 1;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Expected</th>
                        <th>PO Number</th>
                        <th>Status</th>
                        <th class="num-col">Total</th>
                        <th style="width:60px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($data['pos'])): ?>
                        <tr><td colspan="6" style="text-align: center; color: #64748b; padding: 20px;">No purchase orders recorded.</td></tr>
                    <?php else: foreach($data['pos'] as $po): ?>
                        <tr>
                            <td><?= date('M d, Y', strtotime($po->po_date)) ?></td>
                            <td style="color:#64748b;"><?= $po->expected_date ? date('M d, Y', strtotime($po->expected_date)) : 'N/A' ?></td>
                            <td><strong><?= htmlspecialchars($po->po_number) ?></strong></td>
                            <td><span class="sf-badge badge-active"><?= $po->status ?></span></td>
                            <td class="num-col" style="font-weight:700; font-family:var(--erp-mono);">Rs. <?= number_format($po->total_amount, 2) ?></td>
                            <td style="text-align:right;">
                                <a href="<?= APP_URL ?>/purchase/show/<?= $po->id ?>" target="_blank" class="sf-btn neutral sf-btn-small">View</a>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <!-- TAB 3: Products -->
        <div id="mtab_products" class="tab-content" style="padding: 16px; overflow-y: auto; flex: 1;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Product Name</th>
                        <th class="num-col">Last Cost</th>
                        <th class="num-col">Sell Price</th>
                        <th class="num-col">Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($data['products'])): ?>
                        <tr><td colspan="5" style="text-align: center; color: #64748b; padding: 20px;">No products assigned.</td></tr>
                    <?php else: foreach($data['products'] as $p): ?>
                        <tr>
                            <td style="color:#64748b; font-family:var(--erp-mono); font-size:12px;"><?= htmlspecialchars($p->sku ?: 'N/A') ?></td>
                            <td><strong><?= htmlspecialchars($p->product_name) ?></strong></td>
                            <td class="num-col" style="font-weight:600; font-family:var(--erp-mono);">Rs. <?= number_format($p->cost_price, 2) ?></td>
                            <td class="num-col" style="color:#2563eb; font-weight:600; font-family:var(--erp-mono);">Rs. <?= number_format($p->price, 2) ?></td>
                            <td class="num-col" style="font-weight:700; font-family:var(--erp-mono); color: <?= $p->quantity_on_hand > 5 ? '#0f172a' : '#ea580c' ?>;">
                                <?= number_format($p->quantity_on_hand) ?>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <!-- TAB 4: Profile -->
        <div id="mtab_profile" class="tab-content" style="padding: 16px; overflow-y: auto; flex: 1;">
            <form action="<?= APP_URL ?>/supplier/index/<?= $sup->id ?>" method="POST" style="max-width: 600px;">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="action" value="update_supplier">
                <input type="hidden" name="supplier_id" value="<?= $sup->id ?>">
                
                <div class="grid-2">
                    <div class="sf-group">
                        <label>Company Name *</label>
                        <input type="text" name="name" class="sf-input" value="<?= htmlspecialchars($sup->name) ?>" required>
                    </div>
                    <div class="sf-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="sf-input" value="<?= htmlspecialchars($sup->email) ?>">
                    </div>
                </div>
                <div class="grid-2">
                    <div class="sf-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" class="sf-input" value="<?= htmlspecialchars($sup->phone) ?>">
                    </div>
                    <div class="sf-group">
                        <label>Physical Address</label>
                        <textarea name="address" class="sf-input" rows="2" style="resize:vertical; min-height:46px;"><?= htmlspecialchars($sup->address) ?></textarea>
                    </div>
                </div>
                
                <div class="grid-2">
                    <div class="sf-group">
                        <label>Opening Balance (Rs)</label>
                        <input type="number" name="opening_balance" step="0.01" class="sf-input" value="<?= htmlspecialchars($sup->opening_balance ?? '0.00') ?>" placeholder="0.00">
                    </div>
                    <div class="sf-group">
                        <label>As of Date</label>
                        <input type="date" name="opening_balance_date" class="sf-input" value="<?= htmlspecialchars($sup->opening_balance_date ?? date('Y-m-d')) ?>">
                    </div>
                </div>

                <div class="sf-group">
                    <label>Account Status</label>
                    <select name="status" class="sf-input" style="font-weight:600;">
                        <option value="Active" <?= ($sup->status ?? 'Active') === 'Active' ? 'selected' : '' ?>>Active (Visible everywhere)</option>
                        <option value="Inactive" <?= ($sup->status ?? 'Active') === 'Inactive' ? 'selected' : '' ?>>Inactive (Hidden from selectors)</option>
                    </select>
                </div>
                
                <div style="text-align: right; margin-top: 10px;">
                    <button type="submit" class="sf-btn primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<!-- ============================================================
     ADD SUPPLIER MODAL
     ============================================================ -->
<div class="modal-veil hidden" id="addSupplierModal" onclick="if(event.target === this) closeModal('addSupplierModal')">
    <div class="sf-modal" style="width: 480px;">
        <div class="modal-head">
            <h3 class="modal-title">Register New Supplier</h3>
            <button type="button" class="modal-close" onclick="closeModal('addSupplierModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="<?= APP_URL ?>/supplier" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <div class="modal-body">
                <input type="hidden" name="action" value="add_supplier">
                <div class="sf-group">
                    <label>Company Name *</label>
                    <input type="text" name="name" class="sf-input" required placeholder="e.g. Acme Stationery Imports">
                </div>
                <div class="grid-2">
                    <div class="sf-group">
                        <label>Email</label>
                        <input type="email" name="email" class="sf-input" placeholder="vendor@example.com">
                    </div>
                    <div class="sf-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="sf-input" placeholder="011-2345678">
                    </div>
                </div>
                <div class="sf-group">
                    <label>Address</label>
                    <textarea name="address" class="sf-input" rows="2" style="resize:vertical;" placeholder="Supplier street address, city"></textarea>
                </div>
                <div class="grid-2">
                    <div class="sf-group">
                        <label>Opening Balance (Rs)</label>
                        <input type="number" name="opening_balance" step="0.01" class="sf-input" value="0.00" placeholder="0.00">
                    </div>
                    <div class="sf-group">
                        <label>As of Date</label>
                        <input type="date" name="opening_balance_date" class="sf-input" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="sf-group">
                    <label>Account Status</label>
                    <select name="status" class="sf-input" style="font-weight:600;">
                        <option value="Active" selected>Active (Visible everywhere)</option>
                        <option value="Inactive">Inactive (Hidden from selectors)</option>
                    </select>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="sf-btn neutral" onclick="closeModal('addSupplierModal')">Cancel</button>
                <button type="submit" class="sf-btn primary">Register Supplier</button>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    window.allSuppliers = [];
    window.filteredSuppliers = [];
    window.currentPage = 1;
    window.pageSize = 15;

    document.addEventListener("DOMContentLoaded", function() {
        const rows = document.querySelectorAll('#supList .supplier-row');
        rows.forEach(row => {
            window.allSuppliers.push({
                element: row,
                name: row.getAttribute('data-name') || '',
                phone: row.getAttribute('data-phone') || '',
                email: row.getAttribute('data-email') || '',
                outstanding: parseFloat(row.getAttribute('data-outstanding') || '0'),
                status: row.getAttribute('data-status') || 'Active'
            });
        });
        window.filteredSuppliers = [...window.allSuppliers];
        window.renderPagination();
        
        // Auto-load supplier profile modal from URL if provided
        const pathParts = window.location.pathname.split('/');
        const idIndex = pathParts.indexOf('index');
        let autoLoadId = null;
        if (idIndex !== -1 && pathParts.length > idIndex + 1) {
            autoLoadId = pathParts[idIndex + 1];
        } else {
            const urlParams = new URLSearchParams(window.location.search);
            autoLoadId = urlParams.get('supplier_id');
        }
        
        if (autoLoadId && !isNaN(autoLoadId)) {
            const tab = new URLSearchParams(window.location.search).get('tab');
            window.openSupplierModalPopup(autoLoadId, tab);
        }
    });

    window.filterSuppliersList = function() {
        const query = (document.getElementById('searchInput').value || '').toLowerCase().trim();
        const ledgerVal = document.getElementById('filterStatus').value;
        const accountVal = document.getElementById('filterAccountStatus').value;
        
        window.filteredSuppliers = window.allSuppliers.filter(s => {
            const matchQuery = !query || s.name.includes(query) || s.phone.includes(query) || s.email.includes(query);
            let matchLedger = true;
            if (ledgerVal === 'owed') matchLedger = (s.outstanding > 0);
            else if (ledgerVal === 'cleared') matchLedger = (s.outstanding <= 0);
            
            let matchAccount = true;
            if (accountVal) matchAccount = (s.status.toLowerCase() === accountVal.toLowerCase());
            
            return matchQuery && matchLedger && matchAccount;
        });
        
        window.currentPage = 1;
        window.renderPagination();
        document.getElementById('matching-count').innerText = window.filteredSuppliers.length;
    };

    window.clearAllFilters = function() {
        document.getElementById('searchInput').value = '';
        document.getElementById('filterStatus').value = '';
        document.getElementById('filterAccountStatus').value = '';
        window.filterSuppliersList();
    };

    window.renderPagination = function() {
        window.allSuppliers.forEach(s => s.element.style.display = 'none');
        
        if (window.filteredSuppliers.length === 0) {
            document.getElementById('pg-info-text').innerHTML = "No matching suppliers";
            document.getElementById('pg-current-text').innerText = "0 / 0";
            document.getElementById('pg-prev-btn').classList.add('disabled');
            document.getElementById('pg-next-btn').classList.add('disabled');
            return;
        }
        
        let totalPages = Math.ceil(window.filteredSuppliers.length / window.pageSize) || 1;
        if (window.currentPage > totalPages) window.currentPage = totalPages;
        if (window.currentPage < 1) window.currentPage = 1;
        
        let startIdx = (window.currentPage - 1) * window.pageSize;
        let endIdx = Math.min(startIdx + window.pageSize, window.filteredSuppliers.length);
        
        for (let i = startIdx; i < endIdx; i++) {
            window.filteredSuppliers[i].element.style.display = 'table-row';
        }
        
        document.getElementById('pg-info-text').innerHTML = `Showing <strong>${startIdx + 1}</strong> – <strong>${endIdx}</strong> of <strong>${window.filteredSuppliers.length}</strong>`;
        document.getElementById('pg-current-text').innerText = `${window.currentPage} / ${totalPages}`;
        
        document.getElementById('pg-prev-btn').classList.toggle('disabled', window.currentPage <= 1);
        document.getElementById('pg-next-btn').classList.toggle('disabled', window.currentPage >= totalPages);
    };
    
    window.navigatePage = function(page) {
        let totalPages = Math.ceil(window.filteredSuppliers.length / window.pageSize) || 1;
        if (page >= 1 && page <= totalPages) {
            window.currentPage = page;
            window.renderPagination();
        }
    };
    
    window.updatePageSize = function(size) {
        window.pageSize = parseInt(size) || 15;
        window.currentPage = 1;
        window.renderPagination();
    };

    window.openModal = function(id) { document.getElementById(id).classList.remove('hidden'); };
    window.closeModal = function(id) { document.getElementById(id).classList.add('hidden'); };

    // Interactive iOS Status Toggle with CSRF & AJAX Rollback
    window.toggleSupplierStatus = function(id, checkbox) {
        const originalState = !checkbox.checked;
        const newStatus = checkbox.checked ? 'Active' : 'Inactive';
        
        fetch('<?= APP_URL ?>/supplier/toggle_status/' + id, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?= htmlspecialchars($csrfToken) ?>'
            },
            body: JSON.stringify({
                status: newStatus,
                csrf_token: '<?= htmlspecialchars($csrfToken) ?>'
            })
        })
        .then(res => {
            if (!res.ok) throw new Error('Status update failed');
            return res.json();
        })
        .then(data => {
            if (!data.success) throw new Error(data.message || 'Error updating status');
            // Update in-memory supplier status
            const found = window.allSuppliers.find(s => s.element.getAttribute('data-id') == id);
            if (found) {
                found.status = data.status;
                found.element.setAttribute('data-status', data.status);
            }
        })
        .catch(err => {
            console.error('Toggle error:', err);
            checkbox.checked = originalState;
            alert('Failed to update operational status. Reverted to previous state.');
        });
    };

    window.openSupplierModalPopup = function(id, tab = null) {
        const modal = document.getElementById('supplierProfileModal');
        const loader = document.getElementById('modal-loader');
        const content = document.getElementById('modal-profile-content');
        
        modal.classList.remove('hidden');
        loader.style.display = 'flex';
        content.style.display = 'none';
        
        let targetUrl = '<?= APP_URL ?>/supplier/index/' + id;
        if (tab) { targetUrl += '?tab=' + tab; }
        window.history.pushState({ path: targetUrl }, '', targetUrl);
        
        fetch(targetUrl)
            .then(response => {
                if (!response.ok) throw new Error('Failed to load profile');
                return response.text();
            })
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                const newContent = doc.getElementById('modal-profile-content-source');
                const newHeader = doc.getElementById('modal-header-source');
                
                if (newContent && newHeader) {
                    content.innerHTML = newContent.innerHTML;
                    document.getElementById('modal-header-container').innerHTML = newHeader.innerHTML;
                    loader.style.display = 'none';
                    content.style.display = 'flex';
                    
                    if (tab) { window.switchModalTab(tab); } 
                    else { window.switchModalTab('ledger'); }
                } else {
                    throw new Error('Profile layout mismatch');
                }
            })
            .catch(err => {
                console.error(err);
                loader.style.display = 'none';
                content.innerHTML = `<div style="padding:40px; text-align:center; color:#dc2626; font-weight:600;"><i class="fa-solid fa-triangle-exclamation" style="font-size:24px; margin-bottom:10px; display:block;"></i>Failed to load supplier profile data.</div>`;
                content.style.display = 'block';
            });
    };

    window.closeSupplierProfile = function() {
        document.getElementById('supplierProfileModal').classList.add('hidden');
        window.history.pushState({ path: '<?= APP_URL ?>/supplier' }, '', '<?= APP_URL ?>/supplier');
    };

    window.switchModalTab = function(tabName) {
        const content = document.getElementById('modal-profile-content');
        content.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
        content.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        
        const tabEl = content.querySelector('#mtab_' + tabName);
        const btnEl = content.querySelector('#mbtn_' + tabName);
        
        if (tabEl) tabEl.style.display = 'block';
        if (btnEl) btnEl.classList.add('active');
        
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('tab', tabName);
        const newUrl = window.location.pathname + '?' + urlParams.toString();
        window.history.replaceState({ path: newUrl }, '', newUrl);
    };
})();
</script>
