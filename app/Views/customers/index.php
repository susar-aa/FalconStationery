<?php
$importResults = $_SESSION['customer_import_results'] ?? null;
if ($importResults) {
    unset($_SESSION['customer_import_results']);
}

// Dynamic Stats calculation
$totalCustomers = $data['summary_stats']['total_customers'] ?? count($data['customers'] ?? []);
$totalOutstanding = $data['summary_stats']['total_outstanding'] ?? 0;
$owedCustomersCount = $data['summary_stats']['owed_customers_count'] ?? 0;
$csrfToken = $data['csrf_token'] ?? '';
?>

<!-- FontAwesome 6 Icons Guarantee -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
/* ============================================================
   FALCON STATIONERY ERP: CUSTOMER CENTER ARCHITECTURE
   SF PRO DISPLAY + VIEWPORT-LOCKED ERP STANDARDS
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
    font-family: var(--erp-font, 'SF Pro Display', sans-serif);
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

.erp-viewport-view.cust-erp-workspace {
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
    padding: 0; /* Zero bottom padding so table reaches bottom of viewport */
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

/* 2. Top-Right Clean Filter Bar Above Table */
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

/* 3. Table Card with Dedicated Independent Scroll */
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
/* Alternating Zebra Striping */
.erp-table tbody tr:nth-child(even) {
    background: #fbfcfe;
}

/* Highlighted Styling for Newly Added Customers on Top */
.erp-table tbody tr.row-new-customer {
    background: #fffdf5 !important;
    border-left: 3.5px solid #f59e0b !important;
}
.erp-table tbody tr.row-new-customer:hover {
    background: #fef9c3 !important;
}
.row-new-customer .avatar-circle {
    background: #fef3c7 !important;
    color: #b45309 !important;
    border: 1px solid #fde68a !important;
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
.sf-badge .dot { width: 5px; height: 5px; border-radius: 50%; background: currentColor; }

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
.erp-icon-btn.approve { color: #16a34a; }
.erp-icon-btn.approve:hover { background: #ecfdf5; border-color: #86efac; color: #15803d; }
.erp-icon-btn.share { color: #059669; }
.erp-icon-btn.share:hover { background: #ecfdf5; border-color: #a7f3d0; color: #047857; }

/* 4. Pinned Pagination Footer */
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

.data-table { width: 100%; border-collapse: collapse; margin-top: 6px; }
.data-table th, .data-table td { padding: 7px 9px; text-align: left; border-bottom: 1px solid #f1f5f9; font-size: 12px; }
.data-table th { color: #475569; font-weight: 700; font-size: 10px; text-transform: uppercase; background: #f8fafc; }
.num-col { text-align: right !important; }

.drop-zone {
    border: 1.5px dashed #cbd5e1; padding: 22px; border-radius: 8px;
    text-align: center; background: #f8fafc; position: relative; cursor: pointer;
}
.drop-zone:hover { background: #f1f5f9; }
.drop-zone input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
.drop-zone-icon { font-size: 24px; margin-bottom: 6px; color: #94a3b8; }
.drop-zone-title { font-size: 12.5px; font-weight: 700; color: #0f172a; margin-bottom: 2px; }
.drop-zone-sub { font-size: 11px; color: #64748b; }

@keyframes spin { to { transform: rotate(360deg); } }
.spin { animation: spin 0.7s linear infinite; display: inline-block; }
.hidden { display: none !important; }
</style>

<script>
    // Ensure viewport-lock standards on parent layout
    document.body.classList.add('erp-locked');
</script>

<div class="erp-viewport-view cust-erp-workspace">

    <!-- Top Compact KPI Stats Strip -->
    <div class="erp-stats-strip">
        <div class="stat-pill">
            <span class="badge-dot" style="background:#2563eb;"></span>
            Total Customers: <strong id="stat-total-customers"><?= number_format($totalCustomers) ?></strong>
        </div>
        <div class="stat-pill">
            <span class="badge-dot" style="background:#ea580c;"></span>
            Total Outstanding: <strong id="stat-total-outstanding">Rs. <?= number_format($totalOutstanding, 2) ?></strong>
        </div>
        <div class="stat-pill">
            <span class="badge-dot" style="background:#dc2626;"></span>
            Owed Accounts: <strong id="stat-owed-count"><?= number_format($owedCustomersCount) ?></strong>
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

    <?php if ($importResults): ?>
        <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:6px; padding:6px 12px; font-size:12px; color:#1e40af; flex-shrink:0;">
            <strong>CSV Import Completed:</strong> Added <?= intval($importResults['added']) ?> customers, Updated <?= intval($importResults['updated']) ?> customers.
            <?php if (!empty($importResults['errors'])): ?>
                <span style="color:#dc2626; margin-left:8px;">(<?= count($importResults['errors']) ?> errors)</span>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Top-Right Clean Filter Toolbar (RULE 2) -->
    <div class="erp-filter-bar">
        <div class="erp-filter-left">
            <span class="erp-results-badge">Showing <strong id="matching-count"><?= $data['records_filtered'] ?? count($data['customers']) ?></strong> customers</span>
        </div>
        <div class="erp-filter-right">
            <!-- Route Filter -->
            <div class="erp-filter-control">
                <span class="erp-filter-label">Route:</span>
                <select id="filterRoute" class="erp-select" onchange="fetchCustomersAJAX(1)">
                    <option value="">All Routes</option>
                    <?php 
                    $routes = [];
                    if (!empty($data['mca_areas'])) {
                        foreach($data['mca_areas'] as $area) {
                            if(!empty($area->name) && !in_array($area->name, $routes)) { $routes[] = $area->name; }
                        }
                    }
                    sort($routes);
                    foreach($routes as $r): ?>
                        <option value="<?= htmlspecialchars(strtolower($r), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($r) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Ledger Status Filter -->
            <div class="erp-filter-control">
                <span class="erp-filter-label">Ledger:</span>
                <select id="filterStatus" class="erp-select" onchange="fetchCustomersAJAX(1)">
                    <option value="">All Accounts</option>
                    <option value="owed">Has Unpaid Balance</option>
                    <option value="cleared">Zero Balance</option>
                </select>
            </div>

            <!-- Account Operational Status Filter -->
            <div class="erp-filter-control">
                <span class="erp-filter-label">Status:</span>
                <select id="filterAccountStatus" class="erp-select" onchange="fetchCustomersAJAX(1)">
                    <option value="">All Statuses</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                    <option value="New">Pending Review</option>
                </select>
            </div>

            <!-- Reset Button -->
            <button type="button" class="erp-btn-reset" onclick="clearAllFilters()">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </button>
        </div>
    </div>

    <!-- Table Card with Dedicated Independent Scroll (RULE 1 & 3) -->
    <div class="erp-card-table">
        <div class="erp-table-scroll">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th>Customer / Company</th>
                        <th>Contact Details</th>
                        <th style="text-align:right;">Outstanding Balance</th>
                        <th>Ledger Status</th>
                        <th>Account Status</th>
                        <th style="text-align:center; width:100px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="custList">
                    <?php if (empty($data['customers'])): ?>
                        <tr>
                            <td colspan="6" style="text-align:center; padding:35px; color:#64748b;">
                                <i class="fa-solid fa-building" style="font-size:24px; margin-bottom:6px; color:#cbd5e1;"></i><br>
                                No customers registered yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($data['customers'] as $c): ?>
                            <?php 
                            $bal = floatval($c->outstanding_balance); 
                            $badgeCls = $bal > 0 ? 'badge-owed' : 'badge-active';
                            $badgeTxt = $bal > 0 ? 'Owed' : 'Cleared';
                            $accountStatus = $c->status ?? 'Active';
                            $isNew = isset($c->review_status) && $c->review_status === 'New';
                            $rowCls = 'customer-row' . ($isNew ? ' row-new-customer' : '');
                            ?>
                            <tr class="<?= $rowCls ?>" onclick="showCustomerProfile(<?= $c->id ?>)">
                                <td>
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <div class="avatar-circle"><?= strtoupper(substr($c->name ?? '', 0, 2)) ?></div>
                                        <div>
                                            <strong style="font-size:13px; font-weight:600; color:#0f172a;"><?= htmlspecialchars($c->name ?? '') ?></strong>
                                            <span style="font-size:11px; color:#64748b; display:block; margin-top:1px;">
                                                🏠 <?= !empty($c->address) ? htmlspecialchars($c->address) : '<span style="color:#dc2626; font-weight:600;">Missing Address</span>' ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="font-mono" style="font-size:12px; font-weight:500; display:block;">
                                        📞 <?= !empty($c->phone) ? htmlspecialchars($c->phone) : '<span style="color:#dc2626; font-weight:600;">Missing Phone</span>' ?>
                                    </span>
                                    <?php if (!empty($c->email)): ?>
                                        <span style="font-size:11px; color:#64748b; display:block; margin-top:1px;">
                                            <?= htmlspecialchars($c->email) ?>
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
                                <td>
                                    <div style="display:flex; flex-direction:column; gap:3px; align-items:flex-start;">
                                        <span class="sf-badge <?= $accountStatus === 'Active' ? 'badge-active' : 'badge-owed' ?>">
                                            <span class="dot"></span><?= $accountStatus ?>
                                        </span>
                                        <?php if ($isNew): ?>
                                            <span class="sf-badge badge-new">
                                                <span class="dot"></span>Needs Review
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="erp-row-actions" onclick="event.stopPropagation()">
                                        <?php if ($isNew): ?>
                                            <button type="button" class="erp-icon-btn approve" onclick="quickVerifyCustomer(<?= $c->id ?>)" title="Quick Verify">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                            <button type="button" class="erp-icon-btn view" onclick="showCustomerProfile(<?= $c->id ?>, 'profile')" title="Review & Correct">
                                                <i class="fa-solid fa-user-pen"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button type="button" class="erp-icon-btn view" onclick="showCustomerProfile(<?= $c->id ?>)" title="View Profile & Ledger">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                        <button type="button" class="erp-icon-btn share" onclick="sharePortal(<?= $c->id ?>, '<?= htmlspecialchars(addslashes($c->phone ?? '')) ?>', '<?= htmlspecialchars(addslashes($c->name ?? '')) ?>')" title="Share B2B Portal Link">
                                            <i class="fa-solid fa-share-nodes"></i>
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
                Showing <strong>1</strong> – <strong>25</strong> of <strong><?= $data['records_total'] ?? count($data['customers']) ?></strong>
            </div>
            <div style="display:flex; align-items:center; gap:14px;">
                <div style="display:flex; align-items:center; gap:6px;">
                    <span style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase;">Show:</span>
                    <select class="erp-select" style="height:25px; max-width:70px; padding:0 18px 0 6px;" onchange="updatePageSize(this.value)">
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
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
            <input type="text" id="searchInput" placeholder="Search customer, phone, code, territory..." onkeyup="onSearchInput()">
        </div>
        <div class="cmd-divider"></div>
        <a href="<?= APP_URL ?>/customer/exportCSV" class="cmd-icon" title="Export CSV"><i class="fa-solid fa-download"></i></a>
        <button type="button" class="cmd-icon" onclick="openModal('csvImportModal')" title="Import CSV"><i class="fa-solid fa-upload"></i></button>
        <button type="button" class="cmd-cta" onclick="openModal('addCustomerModal')"><i class="fa-solid fa-plus"></i> New Customer</button>
    </div>

</div>

<!-- ============================================================
     POPUP MODAL: CUSTOMER PROFILE LEDGER & DETAILS
     ============================================================ -->
<div id="customerProfileModal" class="modal-veil hidden" onclick="if(event.target === this) closeCustomerProfile()">
    <div class="sf-modal" style="width: 85%; max-width: 980px; height: 85vh; display: flex; flex-direction: column; padding: 0;">
        
        <!-- Modal Head Container -->
        <div class="modal-head" id="modal-header-container">
            <div style="display: flex; gap: 12px; align-items: center;">
                <div class="avatar-circle" style="width: 34px; height: 34px; font-size: 13px;">C</div>
                <div>
                    <h3 class="modal-title">Customer Details</h3>
                </div>
            </div>
            <button type="button" onclick="closeCustomerProfile()" class="modal-close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <!-- Modal Loader -->
        <div id="modal-loader" style="display:none; flex:1; align-items:center; justify-content:center; flex-direction:column; gap:10px; background:#fff;">
            <i class="fa-solid fa-spinner spin" style="font-size:28px; color:#2563eb;"></i>
            <span style="font-size:13px; color:#64748b; font-weight:500;">Loading customer profile...</span>
        </div>
        
        <!-- Modal Content Container -->
        <div id="modal-profile-content" style="flex:1; display:flex; flex-direction:column; overflow:hidden;">
            <!-- Content loads dynamically -->
        </div>

    </div>
</div>

<!-- ============================================================
     HIDDEN TEMPLATE SOURCES
     ============================================================ -->
<?php if ($data['selected_customer']): ?>
    <?php $c = $data['selected_customer']; $s = $data['stats']; ?>
    
    <div id="modal-header-source" class="hidden">
        <div style="display: flex; gap: 12px; align-items: center; min-width: 0; flex: 1;">
            <div class="avatar-circle" style="width: 36px; height: 36px; font-size: 14px; background: #eff6ff; color: #2563eb; flex-shrink: 0;">
                <?= strtoupper(substr($c->name ?? '', 0, 2)) ?>
            </div>
            <div style="min-width: 0; max-width: 250px;">
                <h3 class="modal-title" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0;" title="<?= htmlspecialchars($c->name ?? '') ?>">
                    <?= htmlspecialchars($c->name ?? '') ?>
                </h3>
                <div style="font-size: 11px; color: #64748b; display: flex; gap: 8px; margin-top: 1px; white-space: nowrap; overflow: hidden; align-items: center;">
                    <span>📞 <?= !empty($c->phone) ? htmlspecialchars($c->phone) : '<span style="color:#dc2626;">Missing</span>' ?></span>
                    <span>📍 <?= !empty($c->mca_name) ? htmlspecialchars($c->mca_name) : 'Unassigned' ?></span>
                    <?php if (isset($c->review_status) && $c->review_status === 'New'): ?>
                        <span class="sf-badge badge-new" style="font-size:9.5px; padding:1px 5px;">Needs Review</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div style="margin-right: 10px; flex-shrink: 0;">
            <a href="<?= APP_URL ?>/report/viewer/customer_statement?customer=<?= $c->id ?>&start_date=2000-01-01&end_date=2099-12-31" target="_blank" class="sf-btn neutral" style="padding: 5px 10px; font-size: 11.5px; gap: 4px;" title="Export statement">
                <i class="fa-solid fa-file-export" style="color:#2563eb;"></i> Statement
            </a>
        </div>

        <!-- Header Statistics Cards -->
        <div style="display: flex; gap: 6px; margin-right: 12px; flex-shrink: 0; align-items: center;">
            <div style="background: #f1f5f9; padding: 4px 8px; border-radius: 6px; text-align: center; min-width: 60px;">
                <div style="font-size: 8px; color: #64748b; text-transform: uppercase; font-weight: 700;">Orders</div>
                <div style="font-size: 12px; font-weight: bold; color: #0f172a;"><?= $s->total_orders ?></div>
            </div>
            <div style="background: #f1f5f9; padding: 4px 8px; border-radius: 6px; text-align: center; min-width: 85px;">
                <div style="font-size: 8px; color: #64748b; text-transform: uppercase; font-weight: 700;">Total Billed</div>
                <div style="font-size: 12px; font-weight: bold; color: #0f172a; font-family: var(--erp-mono);">Rs. <?= number_format($s->total_billed, 2) ?></div>
            </div>
            <div style="background: #ecfdf5; padding: 4px 8px; border-radius: 6px; border: 1px solid #a7f3d0; text-align: center; min-width: 85px;">
                <div style="font-size: 8px; color: #065f46; text-transform: uppercase; font-weight: 700;">Total Paid</div>
                <div style="font-size: 12px; font-weight: bold; color: #065f46; font-family: var(--erp-mono);">Rs. <?= number_format($s->total_paid, 2) ?></div>
            </div>
            <div style="background: <?= $s->outstanding > 0 ? '#fef2f2' : '#ecfdf5' ?>; padding: 4px 8px; border-radius: 6px; border: 1px solid <?= $s->outstanding > 0 ? '#fecaca' : '#a7f3d0' ?>; text-align: center; min-width: 85px;">
                <div style="font-size: 8px; color: <?= $s->outstanding > 0 ? '#991b1b' : '#065f46' ?>; text-transform: uppercase; font-weight: 700;">Outstanding</div>
                <div style="font-size: 12px; font-weight: bold; color: <?= $s->outstanding > 0 ? '#dc2626' : '#16a34a' ?>; font-family: var(--erp-mono);">Rs. <?= number_format($s->outstanding, 2) ?></div>
            </div>
        </div>

        <button type="button" onclick="closeCustomerProfile()" class="modal-close"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div id="modal-profile-content-source" class="hidden">
        <!-- Tab Navigation -->
        <div class="tabs">
            <button class="tab-btn active" onclick="switchModalTab('ledger')" id="mbtn_ledger">Activity Ledger</button>
            <button class="tab-btn" onclick="switchModalTab('invoices')" id="mbtn_invoices">Invoices</button>
            <button class="tab-btn" onclick="switchModalTab('cheques')" id="mbtn_cheques">Cheques (PDC)</button>
            <button class="tab-btn" onclick="switchModalTab('profile')" id="mbtn_profile">Profile & Settings</button>
        </div>

        <!-- TAB 1: Ledger -->
        <div id="mtab_ledger" class="tab-content active" style="padding: 16px; overflow-y: auto; flex: 1;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th class="num-col">Debit (Dr)</th>
                        <th class="num-col">Credit (Cr)</th>
                        <th class="num-col">Running Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($data['ledger'])): ?>
                        <tr><td colspan="5" style="text-align: center; color: #64748b; padding: 20px;">No financial activity yet.</td></tr>
                    <?php else: foreach($data['ledger'] as $l): ?>
                        <tr>
                            <td style="color:#64748b; font-size:12px;"><?= date('M d, Y', strtotime($l->date)) ?></td>
                            <td>
                                <strong><?= $l->type ?></strong>
                                <?php if($l->type == 'Invoice'): ?>
                                    <a href="<?= APP_URL ?>/sales/show/<?= $l->id ?>" target="_blank" style="color:#2563eb; font-size: 11px; margin-left: 5px; font-weight:bold; text-decoration:none;">
                                        <?= htmlspecialchars($l->ref) ?> ↗
                                    </a>
                                <?php elseif($l->type == 'Payment'): ?>
                                    <a href="<?= APP_URL ?>/customerpayment/receipt/<?= $l->id ?>" target="_blank" style="color:#16a34a; font-size: 11px; margin-left: 5px; font-weight:bold; text-decoration:none;">
                                        <?= htmlspecialchars($l->ref) ?> ↗
                                    </a>
                                <?php elseif($l->type == 'Credit Note'): ?>
                                    <a href="<?= APP_URL ?>/creditnote/show/<?= $l->id ?>" target="_blank" style="color:#dc2626; font-size: 11px; margin-left: 5px; font-weight:bold; text-decoration:none;">
                                        <?= htmlspecialchars($l->ref) ?> ↗
                                    </a>
                                <?php elseif($l->type == 'Cheque Returned'): ?>
                                    <a href="<?= APP_URL ?>/cheque?search=<?= urlencode($l->id) ?>" target="_blank" style="color:#dc2626; font-size: 11px; margin-left: 5px; font-weight:bold; text-decoration:none;">
                                        <?= htmlspecialchars($l->ref) ?> ↗
                                    </a>
                                <?php elseif($l->type == 'Journal Entry'): ?>
                                    <a href="<?= APP_URL ?>/accounting/print_journal/<?= $l->id ?>" target="_blank" style="color:#2563eb; font-size: 11px; margin-left: 5px; font-weight:bold; text-decoration:none;">
                                        <?= htmlspecialchars($l->ref) ?> ↗
                                    </a>
                                <?php elseif($l->type == 'Payment Reversal'): ?>
                                    <a href="<?= APP_URL ?>/customerpayment/receipt/<?= $l->id ?>" target="_blank" style="color: #ea580c; font-size: 11px; margin-left: 5px; font-weight:600; background: #fffbeb; padding: 2px 6px; border-radius: 4px; text-decoration:none;">
                                        <?= htmlspecialchars($l->ref) ?> ↗
                                    </a>
                                <?php else: ?>
                                    <span style="color:#64748b; font-size: 11px; margin-left: 5px;"><?= htmlspecialchars($l->ref) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="num-col" style="color:#0f172a; font-weight:500; font-family:var(--erp-mono);"><?= $l->debit > 0 ? 'Rs. ' . number_format($l->debit, 2) : '-' ?></td>
                            <td class="num-col" style="color:#16a34a; font-weight:500; font-family:var(--erp-mono);"><?= $l->credit > 0 ? 'Rs. ' . number_format($l->credit, 2) : '-' ?></td>
                            <td class="num-col" style="font-weight:bold; font-family:var(--erp-mono); color: <?= $l->balance > 0 ? '#dc2626' : '#16a34a' ?>;">Rs. <?= number_format($l->balance, 2) ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <!-- TAB 2: Invoices -->
        <div id="mtab_invoices" class="tab-content" style="padding: 16px; overflow-y: auto; flex: 1;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th class="num-col">Grand Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($data['invoices'])): ?>
                        <tr><td colspan="4" style="text-align:center; color:#64748b; padding: 20px;">No invoices found.</td></tr>
                    <?php else: foreach($data['invoices'] as $inv): ?>
                        <?php 
                            $trueInvTotal = $inv->total_amount;
                            if($inv->global_discount_val > 0) {
                                $trueInvTotal -= ($inv->global_discount_type == '%' ? ($inv->total_amount * $inv->global_discount_val / 100) : $inv->global_discount_val);
                            }
                            $trueInvTotal += $inv->tax_amount;
                        ?>
                        <tr>
                            <td><a href="<?= APP_URL ?>/sales/show/<?= $inv->id ?>" target="_blank" style="color:#2563eb; font-weight:bold; text-decoration:none;"><?= $inv->invoice_number ?></a></td>
                            <td style="color:#64748b; font-size:12px;"><?= date('M d, Y', strtotime($inv->invoice_date)) ?></td>
                            <td class="num-col" style="font-weight:bold; font-family:var(--erp-mono);">Rs. <?= number_format($trueInvTotal, 2) ?></td>
                            <td><span class="sf-badge badge-active"><?= $inv->status ?></span></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <!-- TAB 3: Cheques -->
        <div id="mtab_cheques" class="tab-content" style="padding: 16px; overflow-y: auto; flex: 1;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Bank & Date</th>
                        <th class="num-col">Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($data['cheques'])): ?>
                        <tr><td colspan="3" style="text-align:center; color:#64748b; padding: 20px;">No cheques recorded.</td></tr>
                    <?php else: foreach($data['cheques'] as $chk): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($chk->bank_name) ?></strong><br>
                                <span style="font-size:11px; color:#64748b;"><?= date('M d, Y', strtotime($chk->banking_date)) ?></span>
                            </td>
                            <td class="num-col" style="font-weight:bold; font-family:var(--erp-mono);">Rs. <?= number_format($chk->amount, 2) ?></td>
                            <td><span class="sf-badge badge-active"><?= $chk->status ?></span></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <!-- TAB 4: Profile & Settings -->
        <div id="mtab_profile" class="tab-content" style="padding: 16px; overflow-y: auto; flex: 1;">
            <div class="grid-2">
                <div>
                    <h4 style="margin:0 0 12px 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 6px; font-size:13px; font-weight:700; text-transform:uppercase; color:#475569;">
                        <?= (isset($c->review_status) && $c->review_status === 'New') ? 'Review Customer Profile' : 'Edit Customer Profile' ?>
                    </h4>
                    
                    <form action="<?= APP_URL ?>/customer/index/<?= $c->id ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        <input type="hidden" name="action" value="<?= (isset($c->review_status) && $c->review_status === 'New') ? 'review_customer' : 'update_customer' ?>">
                        <input type="hidden" name="customer_id" value="<?= $c->id ?>">
                        
                        <div class="sf-group">
                            <label>Customer Name *</label>
                            <input type="text" name="name" class="sf-input" value="<?= htmlspecialchars($c->name ?? '') ?>" required>
                        </div>
                        
                        <div class="grid-2">
                            <div class="sf-group">
                                <label>Email Address</label>
                                <input type="email" name="email" class="sf-input" value="<?= htmlspecialchars($c->email ?? '') ?>">
                            </div>
                            <div class="sf-group">
                                <label>Phone Number</label>
                                <input type="text" name="phone" class="sf-input" value="<?= htmlspecialchars($c->phone ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="grid-2">
                            <div class="sf-group">
                                <label>WhatsApp Number</label>
                                <input type="text" name="whatsapp" class="sf-input" value="<?= htmlspecialchars($c->whatsapp ?? '') ?>">
                            </div>
                            <div class="sf-group">
                                <label>Assigned Route</label>
                                <select name="mca_id" class="sf-input" style="height:36px;">
                                    <option value="">Unassigned</option>
                                    <?php foreach($data['mca_areas'] as $route): ?>
                                        <option value="<?= $route->id ?>" <?= $c->mca_id == $route->id ? 'selected' : '' ?>><?= htmlspecialchars($route->name ?? '') ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="grid-2">
                            <div class="sf-group">
                                <label>Opening Balance (Rs)</label>
                                <input type="number" step="0.01" name="opening_balance" class="sf-input" value="<?= htmlspecialchars($c->opening_balance ?? '0.00') ?>">
                            </div>
                            <div class="sf-group">
                                <label>Opening Balance Date</label>
                                <input type="date" name="opening_balance_date" class="sf-input" value="<?= htmlspecialchars($c->opening_balance_date ?? date('Y-m-d')) ?>">
                            </div>
                        </div>
                        
                        <div class="grid-2">
                            <div class="sf-group">
                                <label>Credit Limit (Rs)</label>
                                <input type="number" step="0.01" name="credit_limit" class="sf-input" value="<?= htmlspecialchars($c->credit_limit ?? '0.00') ?>">
                            </div>
                            <div class="sf-group">
                                <label>VAT / Tax Number</label>
                                <input type="text" name="vat_number" class="sf-input" value="<?= htmlspecialchars($c->vat_number ?? '') ?>" placeholder="e.g. VAT-123456">
                            </div>
                        </div>
                        
                        <div class="sf-group">
                            <label>Billing Address</label>
                            <textarea name="address" class="sf-input" rows="2" style="resize:vertical; min-height:45px;"><?= htmlspecialchars($c->address ?? '') ?></textarea>
                        </div>
                        
                        <div class="grid-2">
                            <div class="sf-group">
                                <label>Latitude (GPS)</label>
                                <input type="text" name="latitude" class="sf-input" value="<?= htmlspecialchars($c->latitude ?? '') ?>">
                            </div>
                            <div class="sf-group">
                                <label>Longitude (GPS)</label>
                                <input type="text" name="longitude" class="sf-input" value="<?= htmlspecialchars($c->longitude ?? '') ?>">
                            </div>
                        </div>

                        <div class="sf-group">
                            <label>Account Status</label>
                            <select name="status" class="sf-input" style="font-weight:600;">
                                <option value="Active" <?= ($c->status ?? 'Active') === 'Active' ? 'selected' : '' ?>>Active (Visible everywhere)</option>
                                <option value="Inactive" <?= ($c->status ?? 'Active') === 'Inactive' ? 'selected' : '' ?>>Inactive (Hidden from selectors)</option>
                            </select>
                        </div>
                        
                        <div style="text-align: right; margin-top: 10px; display: flex; justify-content: flex-end; gap: 8px;">
                            <?php if (isset($c->review_status) && $c->review_status === 'New'): ?>
                                <button type="button" class="sf-btn success" onclick="quickVerifyCustomer(<?= $c->id ?>)">
                                    <i class="fa-solid fa-circle-check"></i> Quick Verify
                                </button>
                                <button type="submit" class="sf-btn primary">Verify & Save</button>
                            <?php else: ?>
                                <button type="submit" class="sf-btn primary">Save Changes</button>
                            <?php endif; ?>
                        </div>
                    </form>

                    <!-- Workflow Details -->
                    <div style="margin-top: 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px;">
                        <h5 style="margin: 0 0 6px 0; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b;">Workflow Details</h5>
                        <ul style="list-style: none; padding: 0; margin: 0; font-size: 12px; display: flex; flex-direction: column; gap: 4px; color:#334155;">
                            <li><strong>Created By:</strong> <?= htmlspecialchars($c->created_by_username ?? 'System/Office') ?></li>
                            <li><strong>Created Date & Time:</strong> <?= !empty($c->created_at) ? date('M d, Y H:i:s', strtotime($c->created_at)) : 'N/A' ?></li>
                            <?php if (isset($c->review_status) && $c->review_status === 'Reviewed' && !empty($c->reviewed_by_username)): ?>
                                <li><strong>Reviewed By:</strong> <?= htmlspecialchars($c->reviewed_by_username) ?></li>
                                <li><strong>Reviewed Date & Time:</strong> <?= !empty($c->reviewed_at) ? date('M d, Y H:i:s', strtotime($c->reviewed_at)) : 'N/A' ?></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <!-- GPS Map View -->
                <div>
                    <h4 style="margin:0 0 12px 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 6px; font-size:13px; font-weight:700; text-transform:uppercase; color:#475569;">Map Location</h4>
                    <?php if (!empty($c->latitude) && !empty($c->longitude)): ?>
                        <div style="width: 100%; height: 230px; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden;">
                            <iframe width="100%" height="100%" frameborder="0" style="border:0;" 
                                    src="https://maps.google.com/maps?q=<?= $c->latitude ?>,<?= $c->longitude ?>&hl=es&z=14&output=embed">
                            </iframe>
                        </div>
                        <div style="margin-top: 6px; font-size: 11.5px; color: #64748b; text-align: center;">
                            GPS Coordinates: <code class="font-mono"><?= htmlspecialchars($c->latitude) ?>, <?= htmlspecialchars($c->longitude) ?></code>
                        </div>
                    <?php else: ?>
                        <div style="width: 100%; height: 230px; border-radius: 8px; border: 1px solid #e2e8f0; background: #f8fafc; display:flex; align-items:center; justify-content:center; flex-direction:column; color:#94a3b8;">
                            <i class="fa-solid fa-map-location-dot" style="font-size:32px; margin-bottom:8px;"></i>
                            <span style="font-size:12.5px; font-weight:500;">No GPS Location Captured</span>
                        </div>
                    <?php endif; ?>

                    <!-- Danger Zone: Delete Customer -->
                    <div style="margin-top: 24px; padding-top: 14px; border-top: 1px dashed #e2e8f0;">
                        <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 12px 14px; display: flex; justify-content: space-between; align-items: center; gap: 14px;">
                            <div>
                                <h4 style="margin: 0; font-size: 12.5px; font-weight: 700; color: #dc2626; display: flex; align-items: center; gap: 6px;">
                                    <i class="fa-solid fa-triangle-exclamation"></i> Danger Zone
                                </h4>
                                <p style="margin: 3px 0 0 0; font-size: 11px; color: #64748b; line-height: 1.3;">
                                    Delete customer record. Requires manager authorization.
                                </p>
                            </div>
                            <button type="button" class="sf-btn danger" onclick="confirmDeleteCustomer(<?= $c->id ?>, '<?= htmlspecialchars(addslashes($c->name ?? '')) ?>')" style="padding: 6px 12px; font-size: 11.5px;">
                                <i class="fa-solid fa-trash-can"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- ============================================================
     MODAL: DELETE CONFIRMATION
     ============================================================ -->
<div class="modal-veil hidden" id="deleteCustomerModal" onclick="if(event.target === this) closeModal('deleteCustomerModal')">
    <div class="sf-modal" style="width: 440px;">
        <div class="modal-head" style="border-bottom: none; padding-bottom: 0;">
            <h3 class="modal-title" style="color: #dc2626;"><i class="fa-solid fa-triangle-exclamation" style="margin-right: 6px;"></i> Delete Customer</h3>
            <button type="button" class="modal-close" onclick="closeModal('deleteCustomerModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form onsubmit="submitDeleteCustomer(event)">
            <div class="modal-body" style="padding-top: 10px;">
                <input type="hidden" id="delete-customer-id" value="">
                <p style="font-size: 13.5px; color: #0f172a; margin: 0 0 12px 0;">
                    Are you sure you want to delete <strong id="delete-customer-name"></strong>?
                </p>
                <div style="background: #fef2f2; border-left: 3px solid #dc2626; border-radius: 4px; padding: 10px; font-size: 11.5px; color: #991b1b; margin-bottom: 12px;">
                    <i class="fa-solid fa-shield-halved" style="margin-right: 4px;"></i>
                    This action will check for linked transactions (Invoices, Payments, Cheques) to prevent database corruption.
                </div>
                <div class="sf-group">
                    <label style="color: #dc2626; font-weight: 700;">Manager Password Required *</label>
                    <input type="password" id="delete-confirm-password" class="sf-input" style="border-color: #fca5a5;" placeholder="Enter admin/manager password" required>
                </div>
                <div id="delete-error-msg" style="display:none; color:#dc2626; font-size:12px; margin-top:6px;"></div>
            </div>
            <div class="modal-foot" style="border-top: none; padding-top: 0; background: transparent;">
                <button type="button" class="sf-btn neutral" onclick="closeModal('deleteCustomerModal')">Cancel</button>
                <button type="submit" class="sf-btn danger" id="delete-confirm-btn">Confirm Delete</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================
     MODAL: REGISTER NEW CUSTOMER
     ============================================================ -->
<div class="modal-veil hidden" id="addCustomerModal" onclick="if(event.target === this) closeModal('addCustomerModal')">
    <div class="sf-modal">
        <div class="modal-head">
            <h3 class="modal-title" style="color:#2563eb;"><i class="fa-solid fa-user-plus" style="margin-right:6px;"></i>Add New Customer</h3>
            <button type="button" class="modal-close" onclick="closeModal('addCustomerModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="<?= APP_URL ?>/customer/index" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <div class="modal-body">
                <input type="hidden" name="action" value="add_customer">
                
                <div class="sf-group">
                    <label>Customer / Company Name *</label>
                    <input type="text" name="name" class="sf-input" placeholder="e.g. Acme Corporation" required>
                </div>
                
                <div class="grid-2">
                    <div class="sf-group"><label>Email Address</label><input type="email" name="email" class="sf-input" placeholder="e.g. acme@example.com"></div>
                    <div class="sf-group"><label>Phone Number</label><input type="text" name="phone" class="sf-input" placeholder="e.g. +94771234567"></div>
                </div>
                
                <div class="grid-2">
                    <div class="sf-group"><label>WhatsApp Number</label><input type="text" name="whatsapp" class="sf-input" placeholder="e.g. +94771234567"></div>
                    <div class="sf-group">
                        <label>Assign Route</label>
                        <select name="mca_id" class="sf-input" style="height:36px;">
                            <option value="">Unassigned</option>
                            <?php foreach($data['mca_areas'] as $route): ?>
                                <option value="<?= $route->id ?>"><?= htmlspecialchars($route->name ?? '') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="sf-group">
                        <label>Opening Balance (Rs)</label>
                        <input type="number" step="0.01" name="opening_balance" class="sf-input" placeholder="0.00" value="0.00">
                    </div>
                    <div class="sf-group">
                        <label>Credit Limit (Rs)</label>
                        <input type="number" step="0.01" name="credit_limit" class="sf-input" placeholder="0.00" value="0.00">
                    </div>
                </div>
                
                <div class="grid-2">
                    <div class="sf-group">
                        <label>Account Status</label>
                        <select name="status" class="sf-input" style="font-weight:600; height:36px;">
                            <option value="Active" selected>Active (Visible everywhere)</option>
                            <option value="Inactive">Inactive (Hidden from selectors)</option>
                        </select>
                    </div>
                    <div class="sf-group">
                        <label>VAT / Tax Number</label>
                        <input type="text" name="vat_number" class="sf-input" placeholder="e.g. VAT-123456">
                    </div>
                </div>
                
                <div class="sf-group">
                    <label>Billing Address</label>
                    <textarea name="address" class="sf-input" rows="2" placeholder="e.g. 123 Main Street, Colombo" style="resize:vertical; min-height:45px;"></textarea>
                </div>
                
                <div class="grid-2">
                    <div class="sf-group"><label>Latitude (GPS)</label><input type="text" name="latitude" class="sf-input" placeholder="Optional"></div>
                    <div class="sf-group"><label>Longitude (GPS)</label><input type="text" name="longitude" class="sf-input" placeholder="Optional"></div>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="sf-btn neutral" onclick="closeModal('addCustomerModal')">Cancel</button>
                <button type="submit" class="sf-btn primary">Save & Register</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================
     MODAL: CSV IMPORT
     ============================================================ -->
<div class="modal-veil hidden" id="csvImportModal" onclick="if(event.target === this) closeModal('csvImportModal')">
    <div class="sf-modal" style="width:480px;">
        <div class="modal-head">
            <h3 class="modal-title" style="color:#16a34a;"><i class="fa-solid fa-file-csv" style="margin-right:6px;"></i>Import Customers (CSV)</h3>
            <button type="button" class="modal-close" onclick="closeModal('csvImportModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="<?= APP_URL ?>/customer/importCSV" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <div class="modal-body">
                <p style="font-size: 11.5px; color: #64748b; margin-top:-6px; margin-bottom: 14px; line-height:1.4;">
                    Select a CSV file containing customer details. Required column: <strong>Name</strong>.<br>
                    Supported: <strong>Name, Email, Phone, WhatsApp, Address, Latitude, Longitude, Route, Opening Balance</strong>.
                </p>
                <div class="drop-zone">
                    <input type="file" name="csv_file" accept=".csv" required>
                    <div class="drop-zone-icon"><i class="fa-solid fa-file-csv"></i></div>
                    <div class="drop-zone-title">Choose a CSV file</div>
                    <div class="drop-zone-sub">or drag and drop it here</div>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="sf-btn neutral" onclick="closeModal('csvImportModal')">Cancel</button>
                <button type="submit" class="sf-btn success">Import Customers</button>
            </div>
        </form>
    </div>
</div>

<script>
    // --- Pagination & AJAX State ---
    let currentPage = 1;
    let pageSize = 25;
    let searchDebounceTimer = null;
    const csrfToken = '<?= $csrfToken ?>';

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    function escapeJs(str) {
        if (!str) return '';
        return String(str).replace(/'/g, "\\'").replace(/"/g, '\\"');
    }

    function onSearchInput() {
        clearTimeout(searchDebounceTimer);
        searchDebounceTimer = setTimeout(() => {
            fetchCustomersAJAX(1);
        }, 300);
    }

    function fetchCustomersAJAX(page = 1) {
        currentPage = page;
        const query = document.getElementById('searchInput').value.trim();
        const routeFilter = document.getElementById('filterRoute').value.trim();
        const statusFilter = document.getElementById('filterStatus').value.trim();
        const accountStatusFilter = document.getElementById('filterAccountStatus').value.trim();
        
        const start = (currentPage - 1) * pageSize;

        const tbody = document.getElementById('custList');
        if (!tbody) return;
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding:35px; color:#64748b;"><i class="fa-solid fa-circle-notch spin" style="font-size:20px; color:#2563eb; margin-bottom:8px;"></i><br>Loading customers...</td></tr>`;

        const params = new URLSearchParams({
            action: 'api_list',
            start: start,
            length: pageSize,
            search: query,
            route: routeFilter,
            status: statusFilter,
            account_status: accountStatusFilter
        });

        fetch('<?= APP_URL ?>/customer/index?' + params.toString())
            .then(response => response.json())
            .then(data => {
                const rows = data.data || [];
                const recordsFiltered = data.recordsFiltered || 0;

                const countEl = document.getElementById('matching-count');
                if (countEl) countEl.textContent = recordsFiltered.toLocaleString();

                if (rows.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding:35px; color:#64748b;"><i class="fa-solid fa-building" style="font-size:24px; margin-bottom:6px; color:#cbd5e1;"></i><br>No matching customers found.</td></tr>`;
                } else {
                    let html = '';
                    rows.forEach(c => {
                        const bal = parseFloat(c.outstanding_balance || 0);
                        const badgeCls = bal > 0 ? 'badge-owed' : 'badge-active';
                        const badgeTxt = bal > 0 ? 'Owed' : 'Cleared';
                        const accountStatus = c.status || 'Active';
                        const isNew = c.review_status === 'New';
                        const rowCls = 'customer-row' + (isNew ? ' row-new-customer' : '');
                        const name = escapeHtml(c.name || '');
                        const initials = name.substring(0, 2).toUpperCase();
                        const phone = c.phone ? escapeHtml(c.phone) : '<span style="color:#dc2626; font-weight:600;">Missing Phone</span>';
                        const email = c.email ? `<span style="font-size:11px; color:#64748b; display:block; margin-top:1px;">${escapeHtml(c.email)}</span>` : '';
                        const address = c.address ? escapeHtml(c.address) : '<span style="color:#dc2626; font-weight:600;">Missing Address</span>';
                        const balFormatted = bal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        const balColor = bal > 0 ? '#dc2626' : '#16a34a';

                        html += `
                            <tr class="${rowCls}" onclick="showCustomerProfile(${c.id})">
                                <td>
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <div class="avatar-circle">${initials}</div>
                                        <div>
                                            <strong style="font-size:13px; font-weight:600; color:#0f172a;">${name}</strong>
                                            <span style="font-size:11px; color:#64748b; display:block; margin-top:1px;">
                                                🏠 ${address}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="font-mono" style="font-size:12px; font-weight:500; display:block;">📞 ${phone}</span>
                                    ${email}
                                </td>
                                <td style="text-align:right; font-weight:700; font-family:var(--erp-mono); font-size:13px; color: ${balColor};">
                                    Rs. ${balFormatted}
                                </td>
                                <td>
                                    <span class="sf-badge ${badgeCls}"><span class="dot"></span>${badgeTxt}</span>
                                </td>
                                <td>
                                    <div style="display:flex; flex-direction:column; gap:3px; align-items:flex-start;">
                                        <span class="sf-badge ${accountStatus === 'Active' ? 'badge-active' : 'badge-owed'}"><span class="dot"></span>${accountStatus}</span>
                                        ${isNew ? '<span class="sf-badge badge-new"><span class="dot"></span>Needs Review</span>' : ''}
                                    </div>
                                </td>
                                <td>
                                    <div class="erp-row-actions" onclick="event.stopPropagation()">
                                        ${isNew ? `
                                            <button type="button" class="erp-icon-btn approve" onclick="quickVerifyCustomer(${c.id})" title="Quick Verify"><i class="fa-solid fa-check"></i></button>
                                            <button type="button" class="erp-icon-btn view" onclick="showCustomerProfile(${c.id}, 'profile')" title="Review & Correct"><i class="fa-solid fa-user-pen"></i></button>
                                        ` : ''}
                                        <button type="button" class="erp-icon-btn view" onclick="showCustomerProfile(${c.id})" title="View Profile & Ledger"><i class="fa-solid fa-eye"></i></button>
                                        <button type="button" class="erp-icon-btn share" onclick="sharePortal(${c.id}, '${escapeJs(c.phone || '')}', '${escapeJs(c.name || '')}')" title="Share B2B Portal Link"><i class="fa-solid fa-share-nodes"></i></button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                    tbody.innerHTML = html;
                }

                // Update Pagination Controls
                const totalPages = Math.ceil(recordsFiltered / pageSize) || 1;
                const startIndex = recordsFiltered > 0 ? (currentPage - 1) * pageSize + 1 : 0;
                const endIndex = Math.min(currentPage * pageSize, recordsFiltered);

                const pgInfo = document.getElementById('pg-info-text');
                if (pgInfo) {
                    pgInfo.innerHTML = recordsFiltered > 0 
                        ? `Showing <strong>${startIndex}</strong> – <strong>${endIndex}</strong> of <strong>${recordsFiltered.toLocaleString()}</strong>` 
                        : 'No results';
                }

                const pgNav = document.getElementById('pg-nav-container');
                if (pgNav) {
                    document.getElementById('pg-current-text').textContent = `${currentPage} / ${totalPages}`;
                    document.getElementById('pg-prev-btn').classList.toggle('disabled', currentPage <= 1);
                    document.getElementById('pg-next-btn').classList.toggle('disabled', currentPage >= totalPages);
                }
            })
            .catch(err => {
                console.error(err);
                tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding:30px; color:#dc2626;"><i class="fa-solid fa-circle-exclamation" style="font-size:22px; margin-bottom:6px;"></i><br>Error loading customer data.</td></tr>`;
            });
    }

    function navigatePage(pageNum) {
        if (pageNum < 1) return;
        fetchCustomersAJAX(pageNum);
    }

    function updatePageSize(size) {
        pageSize = parseInt(size);
        fetchCustomersAJAX(1);
    }

    function clearAllFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('filterRoute').value = '';
        document.getElementById('filterStatus').value = '';
        document.getElementById('filterAccountStatus').value = '';
        fetchCustomersAJAX(1);
    }

    // --- Modal Control Helper functions ---
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    // --- Customer Profile Popup Modal Handlers ---
    function showCustomerProfile(id, tab = null) {
        const modal = document.getElementById('customerProfileModal');
        const loader = document.getElementById('modal-loader');
        const content = document.getElementById('modal-profile-content');
        
        if (modal) modal.classList.remove('hidden');
        if (loader) loader.style.display = 'flex';
        if (content) content.style.display = 'none';
        
        let targetUrl = '<?= APP_URL ?>/customer/index/' + id;
        if (tab) {
            targetUrl += '?tab=' + tab;
        }
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
                const headerContainer = document.getElementById('modal-header-container');
                
                if (newContent && newHeader) {
                    if (headerContainer) headerContainer.innerHTML = newHeader.innerHTML;
                    if (content) content.innerHTML = newContent.innerHTML;
                    
                    if (loader) loader.style.display = 'none';
                    if (content) content.style.display = 'flex';

                    if (tab) {
                        switchModalTab(tab);
                    }
                } else {
                    throw new Error('Malformed content returned from server');
                }
            })
            .catch(err => {
                console.error(err);
                if (content) {
                    content.innerHTML = `
                        <div style="padding:40px; text-align:center; color:#dc2626;">
                            <i class="fa-solid fa-triangle-exclamation" style="font-size:28px; margin-bottom:8px;"></i><br>
                            Failed to load customer profile details.
                        </div>`;
                    content.style.display = 'block';
                }
                if (loader) loader.style.display = 'none';
            });
    }

    function openPrepopulatedCustomerProfile(id) {
        const modal = document.getElementById('customerProfileModal');
        const content = document.getElementById('modal-profile-content');
        const headerContainer = document.getElementById('modal-header-container');
        
        const headerSrc = document.getElementById('modal-header-source');
        const contentSrc = document.getElementById('modal-profile-content-source');
        
        if (headerSrc && contentSrc) {
            if (headerContainer) headerContainer.innerHTML = headerSrc.innerHTML;
            if (content) content.innerHTML = contentSrc.innerHTML;
            if (modal) modal.classList.remove('hidden');

            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab');
            if (tab) {
                switchModalTab(tab);
            }
        }
    }

    function closeCustomerProfile() {
        document.getElementById('customerProfileModal').classList.add('hidden');
        const targetUrl = '<?= APP_URL ?>/customer/index';
        window.history.pushState({ path: targetUrl }, '', targetUrl);
    }

    function switchModalTab(tabName) {
        const content = document.getElementById('modal-profile-content');
        content.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
        content.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        
        const activeTab = content.querySelector('#mtab_' + tabName);
        const activeBtn = content.querySelector('#mbtn_' + tabName);
        
        if (activeTab) activeTab.style.display = 'block';
        if (activeBtn) activeBtn.classList.add('active');
    }

    function sharePortal(id, phone, name) {
        const encodedId = btoa(id);
        const portalLink = "<?= APP_URL ?>/portal/show/" + encodedId;
        const msg = `Hello ${name},\n\nYou can view your live account statement, outstanding balances, and download past invoices on our B2B Customer Portal here:\n\n${portalLink}\n\nThank you!`;
        
        if (phone && phone.trim() !== '') {
            let cleanPhone = phone.replace(/[^\d+]/g, '');
            if(cleanPhone.startsWith('0')) cleanPhone = '94' + cleanPhone.substring(1); 
            const waUrl = `https://wa.me/${cleanPhone}?text=${encodeURIComponent(msg)}`;
            window.open(waUrl, '_blank');
        } else {
            prompt("Customer has no phone number saved. Copy the link below to share manually:", portalLink);
        }
    }

    // --- Customer Delete Flow Scripts ---
    let activeDeleteCustomerId = null;

    function confirmDeleteCustomer(id, name) {
        activeDeleteCustomerId = id;
        document.getElementById('delete-customer-id').value = id;
        document.getElementById('delete-customer-name').textContent = name;
        document.getElementById('delete-confirm-password').value = '';
        document.getElementById('delete-error-msg').style.display = 'none';
        document.getElementById('delete-error-msg').textContent = '';
        openModal('deleteCustomerModal');
    }

    function submitDeleteCustomer(e) {
        e.preventDefault();
        const pwdInput = document.getElementById('delete-confirm-password');
        const errorMsg = document.getElementById('delete-error-msg');
        const submitBtn = document.getElementById('delete-confirm-btn');
        
        if (!pwdInput.value.trim()) {
            errorMsg.textContent = 'Password is required.';
            errorMsg.style.display = 'block';
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Deleting...';
        errorMsg.style.display = 'none';

        const fd = new FormData();
        fd.append('password', pwdInput.value);
        fd.append('csrf_token', csrfToken);

        fetch('<?= APP_URL ?>/customer/delete/' + activeDeleteCustomerId, {
            method: 'POST',
            body: fd
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                window.location.href = '<?= APP_URL ?>/customer/index';
            } else {
                errorMsg.textContent = data.error || 'Failed to delete customer.';
                errorMsg.style.display = 'block';
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Confirm Delete';
            }
        })
        .catch(err => {
            console.error(err);
            errorMsg.textContent = 'A connection error occurred. Please try again.';
            errorMsg.style.display = 'block';
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Confirm Delete';
        });
    }

    function quickVerifyCustomer(id) {
        if (confirm("Are you sure you want to mark this customer as verified/reviewed?")) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= APP_URL ?>/customer/index';
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = 'csrf_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);

            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'quick_review';
            form.appendChild(actionInput);
            
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'customer_id';
            idInput.value = id;
            form.appendChild(idInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    }

    function loadAsyncSummaryStats() {
        fetch('<?= APP_URL ?>/customer/index?action=api_summary_stats')
            .then(response => response.json())
            .then(stats => {
                if (document.getElementById('stat-total-customers')) {
                    document.getElementById('stat-total-customers').textContent = (stats.total_customers || 0).toLocaleString();
                }
                if (document.getElementById('stat-total-outstanding')) {
                    const bal = parseFloat(stats.total_outstanding || 0);
                    document.getElementById('stat-total-outstanding').textContent = 'Rs. ' + bal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
                if (document.getElementById('stat-owed-count')) {
                    document.getElementById('stat-owed-count').textContent = (stats.owed_customers_count || 0).toLocaleString();
                }
            })
            .catch(err => console.error("Error loading summary stats:", err));
    }

    // Handle initial state if selected customer exists on page load
    document.addEventListener('DOMContentLoaded', () => {
        <?php if ($data['selected_customer']): ?>
            openPrepopulatedCustomerProfile(<?= $data['selected_customer']->id ?>);
        <?php endif; ?>
        loadAsyncSummaryStats();
    });
</script>
