<?php
$csrfToken = $data['csrf_token'] ?? ($_SESSION['csrf_token'] ?? '');
$totalRecords = $data['total_records'] ?? 0;
$stats = $data['stats'] ?? null;
$totalOrders = $stats->total_orders ?? $totalRecords;
$totalAmount = floatval($stats->total_amount ?? 0);
$pendingCount = intval($stats->pending_count ?? 0);
$pendingAmount = floatval($stats->pending_amount ?? 0);
?>

<script>
document.body.classList.add('erp-locked');
</script>

<!-- FontAwesome 6 Icons Guarantee -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
/* ============================================================
   FALCON STATIONERY ERP: SALES ORDERS ARCHITECTURE
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

.erp-viewport-view.so-erp-workspace {
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
    padding: 0;
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
    margin: 0;
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
    max-width: 160px;
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
.erp-select:hover { border-color: #94a3b8; }
.erp-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.12);
}

.erp-input-date {
    height: 28px;
    padding: 0 8px;
    font-size: 11.5px;
    font-weight: 500;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    background: #ffffff;
    color: #1e293b;
    outline: none;
    font-family: var(--erp-font);
    box-sizing: border-box;
}
.erp-input-date:hover { border-color: #94a3b8; }
.erp-input-date:focus {
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
    text-decoration: none;
}
.erp-btn-reset:hover {
    background: #f8fafc;
    color: #0f172a;
    border-color: #94a3b8;
}

/* Bulk Action Bar */
.bulk-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 8px;
    padding: 6px 14px;
    flex-shrink: 0;
    animation: fadeIn 0.15s ease-in-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-4px); }
    to { opacity: 1; transform: translateY(0); }
}
.bulk-selected {
    background: #2563eb;
    color: #ffffff;
    padding: 2px 8px;
    border-radius: 9999px;
    font-size: 11px;
    font-weight: 700;
    font-family: var(--erp-mono);
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
    transition: background-color 0.12s ease;
}

/* MANDATORY ZEBRA STRIPING (RULE 3) */
.erp-table tbody tr:nth-child(even) {
    background-color: #f8fafc;
}
.erp-table tbody tr:hover {
    background-color: #f1f5f9;
}

.erp-table td {
    padding: 8px 14px;
    vertical-align: middle;
    color: #1e293b;
}

.txt-right { text-align: right !important; }
.txt-center { text-align: center !important; }

/* Status Badges */
.sf-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 7px;
    border-radius: 4px;
    font-size: 10.5px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    white-space: nowrap;
}
.badge-Invoiced { background: #dcfce7; color: #15803d; }
.badge-Pending  { background: #fef3c7; color: #b45309; }
.badge-Voided   { background: #f1f5f9; color: #64748b; }
.badge-Std      { background: #eff6ff; color: #1d4ed8; font-size: 10.5px; border: 1px solid #dbeafe; }
.badge-Route    { background: #f5f3ff; color: #6d28d9; font-size: 10.5px; border: 1px solid #ede9fe; }
.badge-dot-sm   { width: 5px; height: 5px; border-radius: 50%; background: currentColor; }

/* Row Actions */
.row-acts { display: flex; gap: 5px; justify-content: flex-end; }
.act-btn {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.15s;
    text-decoration: none;
}
.act-btn.convert { color: #10b981; }
.act-btn.convert:hover { background: #ecfdf5; border-color: #a7f3d0; }
.act-btn.print   { color: #64748b; }
.act-btn.print:hover   { background: #f1f5f9; border-color: #cbd5e1; }
.act-btn.edit    { color: #3b82f6; }
.act-btn.edit:hover    { background: #eff6ff; border-color: #bfdbfe; }
.act-btn.delete  { color: #ef4444; }
.act-btn.delete:hover  { background: #fef2f2; border-color: #fecaca; }

/* Checkboxes */
.sf-checkbox {
    accent-color: #2563eb;
    width: 15px;
    height: 15px;
    cursor: pointer;
    margin: 0;
    vertical-align: middle;
}

/* Pinned Table Pagination Footer (RULE 1) */
.erp-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 7px 16px;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    flex-shrink: 0;
    font-size: 12px;
    color: #64748b;
    z-index: 10;
}
.pg-info strong {
    color: #0f172a;
    font-weight: 700;
}
.pg-right {
    display: flex;
    align-items: center;
    gap: 14px;
}
.pg-size-wrap {
    display: flex;
    align-items: center;
    gap: 6px;
}
.pg-size-lbl {
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
}
.pg-size-sel {
    font-family: var(--erp-font);
    font-size: 11.5px;
    font-weight: 600;
    color: #1e293b;
    background: #f8fafc;
    border: 1px solid #d1d5db;
    border-radius: 5px;
    padding: 3px 6px;
    outline: none;
    cursor: pointer;
}
.pg-nav {
    display: flex;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    overflow: hidden;
}
.pg-btn {
    width: 28px;
    height: 26px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #ffffff;
    border: none;
    cursor: pointer;
    color: #475569;
    font-size: 11px;
    text-decoration: none;
    transition: background 0.12s;
}
.pg-btn:hover:not(.disabled) {
    background: #f1f5f9;
    color: #0f172a;
}
.pg-btn.disabled {
    opacity: 0.35;
    cursor: not-allowed;
    pointer-events: none;
}
.pg-btn + .pg-btn {
    border-left: 1px solid #e2e8f0;
}
.pg-current {
    padding: 0 10px;
    display: flex;
    align-items: center;
    font-size: 11.5px;
    font-weight: 600;
    color: #0f172a;
    background: #ffffff;
    border-left: 1px solid #e2e8f0;
    border-right: 1px solid #e2e8f0;
}

/* 4. Sleek Floating Island Command Bar Pill (RULE 3) */
.cmd-bar {
    position: fixed;
    bottom: 24px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(15, 23, 42, 0.95);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 9999px;
    padding: 6px 8px;
    display: flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.35);
    z-index: 100;
}

.cmd-search {
    display: flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.08);
    border-radius: 9999px;
    padding: 6px 14px;
    width: 320px;
}
.cmd-search i {
    color: rgba(255, 255, 255, 0.5);
    font-size: 13px;
    flex-shrink: 0;
}
.cmd-search input {
    background: transparent;
    border: none;
    outline: none;
    color: #ffffff;
    font-size: 13px;
    font-weight: 500;
    font-family: var(--erp-font);
    width: 100%;
}
.cmd-search input::placeholder {
    color: rgba(255, 255, 255, 0.45);
}

.cmd-divider {
    width: 1px;
    height: 20px;
    background: rgba(255, 255, 255, 0.15);
    margin: 0 2px;
}

.cmd-cta {
    display: flex;
    align-items: center;
    gap: 6px;
    background: #ffffff;
    color: #0f172a;
    border: none;
    border-radius: 9999px;
    padding: 0 16px;
    height: 32px;
    font-size: 13px;
    font-weight: 700;
    font-family: var(--erp-font);
    cursor: pointer;
    transition: all 0.15s;
    text-decoration: none;
    white-space: nowrap;
}
.cmd-cta:hover {
    background: #f1f5f9;
    transform: translateY(-1px);
}

/* Modals */
.sf-modal {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9999;
    background: rgba(15, 23, 42, 0.5);
    backdrop-filter: blur(4px);
    align-items: center;
    justify-content: center;
}
.sf-modal.open { display: flex; }

.sf-modal-box {
    background: #ffffff;
    width: 460px;
    max-width: 95vw;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.18);
    border: 1px solid #e2e8f0;
    animation: modalPop 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes modalPop {
    from { opacity: 0; transform: scale(0.96) translateY(8px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

.sf-form-group { margin-bottom: 14px; }
.sf-form-group label {
    display: block;
    margin-bottom: 5px;
    font-size: 11.5px;
    font-weight: 600;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}
.sf-input {
    width: 100%;
    padding: 8px 12px;
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #0f172a;
    font-size: 13px;
    outline: none;
    box-sizing: border-box;
    font-family: var(--erp-font);
}
.sf-input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15);
}

/* Alerts */
.sf-alert {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    background: #ffffff;
    border-radius: 6px;
    padding: 10px 14px;
    margin-bottom: 6px;
    border: 1px solid #e2e8f0;
    border-left-width: 4px;
    font-size: 12.5px;
}
.sf-alert.success { border-left-color: #10b981; }
.sf-alert.error   { border-left-color: #ef4444; }
.sf-alert-icon { font-size: 15px; flex-shrink: 0; margin-top: 2px; }
.sf-alert.success .sf-alert-icon { color: #10b981; }
.sf-alert.error   .sf-alert-icon { color: #ef4444; }
.sf-alert-title { font-weight: 700; color: #0f172a; margin-bottom: 2px; }
.sf-alert-msg   { color: #475569; font-size: 12px; }

/* Buttons */
.sf-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 7px 14px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 12.5px;
    border: 1px solid transparent;
    cursor: pointer;
    font-family: var(--erp-font);
    text-decoration: none;
    transition: all 0.15s;
}
.sf-btn-primary { background: #3b82f6; color: #ffffff; }
.sf-btn-primary:hover { background: #2563eb; }
.sf-btn-ghost { background: #ffffff; border-color: #cbd5e1; color: #475569; }
.sf-btn-ghost:hover { background: #f8fafc; color: #0f172a; }
.sf-btn-danger { background: #ef4444; color: #ffffff; }
.sf-btn-danger:hover { background: #dc2626; }
</style>

<div class="erp-viewport-view so-erp-workspace">

    <!-- Alert Notifications -->
    <?php if (isset($_SESSION['flash_success'])): ?>
    <div class="sf-alert success" id="success-alert">
        <i class="fa-solid fa-circle-check sf-alert-icon"></i>
        <div style="flex:1;">
            <div class="sf-alert-title">Success</div>
            <div class="sf-alert-msg"><?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
        </div>
        <button type="button" style="background:none; border:none; color:#94a3b8; cursor:pointer;" onclick="document.getElementById('success-alert').style.display='none';"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash_error'])): ?>
    <div class="sf-alert error" id="error-alert">
        <i class="fa-solid fa-triangle-exclamation sf-alert-icon"></i>
        <div style="flex:1;">
            <div class="sf-alert-title">Error</div>
            <div class="sf-alert-msg"><?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></div>
        </div>
        <button type="button" style="background:none; border:none; color:#94a3b8; cursor:pointer;" onclick="document.getElementById('error-alert').style.display='none';"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <?php endif; ?>

    <!-- 2. Top-Right Clean Filter Bar Above Table (RULE 2) -->
    <div class="erp-filter-bar">
        <div class="erp-filter-left">
            <span class="erp-results-badge">Showing <strong><?= number_format($totalRecords) ?></strong> sales orders</span>
        </div>
        <form id="filterForm" method="GET" action="<?= APP_URL ?>/salesorder" class="erp-filter-right">
            <!-- Hidden inputs -->
            <input type="hidden" name="search" id="mainSearchInput" value="<?= htmlspecialchars($data['search'] ?? '') ?>">
            <input type="hidden" name="limit" id="filterLimitInput" value="<?= $data['limit'] ?? 25 ?>">

            <!-- Customer Filter -->
            <div class="erp-filter-control">
                <span class="erp-filter-label">Customer:</span>
                <select name="customer_id" class="erp-select" onchange="this.form.submit()">
                    <option value="0">All Customers</option>
                    <?php foreach ($data['customers'] as $c): ?>
                        <option value="<?= $c->id ?>" <?= (isset($data['customer_id']) && $data['customer_id'] == $c->id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Date Range Filters -->
            <div class="erp-filter-control">
                <span class="erp-filter-label">Date:</span>
                <input type="date" name="start_date" value="<?= htmlspecialchars($data['start_date'] ?? '') ?>" class="erp-input-date" onchange="this.form.submit()" title="Start Date">
                <span style="color:#94a3b8; font-size:11px;">–</span>
                <input type="date" name="end_date" value="<?= htmlspecialchars($data['end_date'] ?? '') ?>" class="erp-input-date" onchange="this.form.submit()" title="End Date">
            </div>

            <!-- Channel / Source Type Filter -->
            <div class="erp-filter-control">
                <span class="erp-filter-label">Source:</span>
                <select name="source_type" class="erp-select" onchange="this.form.submit()">
                    <option value="">All Sources</option>
                    <option value="standard" <?= (isset($data['source_type']) && $data['source_type'] === 'standard') ? 'selected' : '' ?>>Standard SO</option>
                    <option value="route" <?= (isset($data['source_type']) && $data['source_type'] === 'route') ? 'selected' : '' ?>>Route Rep</option>
                </select>
            </div>

            <!-- Sales Rep Filter -->
            <div class="erp-filter-control">
                <span class="erp-filter-label">Rep:</span>
                <select name="rep_name" class="erp-select" onchange="this.form.submit()">
                    <option value="">All Reps</option>
                    <?php foreach ($data['sales_reps'] as $rep): ?>
                        <option value="<?= htmlspecialchars($rep->name) ?>" <?= (isset($data['rep_name']) && $data['rep_name'] === $rep->name) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($rep->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Status Filter -->
            <div class="erp-filter-control">
                <span class="erp-filter-label">Status:</span>
                <select name="status" class="erp-select" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="Pending" <?= (isset($data['status']) && $data['status'] === 'Pending') ? 'selected' : '' ?>>Pending</option>
                    <option value="Transferred" <?= (isset($data['status']) && $data['status'] === 'Transferred') ? 'selected' : '' ?>>Invoiced</option>
                    <option value="Completed" <?= (isset($data['status']) && $data['status'] === 'Completed') ? 'selected' : '' ?>>Completed</option>
                    <option value="Voided" <?= (isset($data['status']) && $data['status'] === 'Voided') ? 'selected' : '' ?>>Voided</option>
                </select>
            </div>

            <a href="<?= APP_URL ?>/salesorder" class="erp-btn-reset" title="Reset all filters">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </a>
        </form>
    </div>

    <!-- Bulk Action Toolbar (Displays when checkboxes are checked) -->
    <div id="bulkActionBar" class="bulk-bar" style="display: none;">
        <div style="display: flex; align-items: center; gap: 8px;">
            <span class="bulk-selected" id="selectedCountBadge">0 selected</span>
            <span style="font-size: 12px; font-weight: 600; color: #1e3a8a;">Bulk Actions:</span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
            <select name="bulk_action_choice" id="bulkActionChoice" onchange="toggleBulkActionInputs()" class="erp-select" style="max-width: 190px;">
                <option value="">-- Select Action --</option>
                <option value="change_date">Change Transaction Date</option>
                <option value="change_rep">Change Assigned Rep</option>
                <option value="delete">Delete Selected</option>
            </select>

            <div id="bulkDateInputContainer" style="display: none;">
                <input type="date" id="bulkDateInput" class="erp-input-date">
            </div>

            <div id="bulkRepInputContainer" style="display: none;">
                <select id="bulkRepSelect" class="erp-select" style="max-width: 170px;">
                    <option value="">-- Rep --</option>
                    <?php if (!empty($data['sales_reps'])): ?>
                        <?php foreach ($data['sales_reps'] as $rep): ?>
                            <option value="<?= htmlspecialchars($rep->name) ?>"><?= htmlspecialchars($rep->name) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div id="bulkPasswordInputContainer" style="display: none;">
                <input type="password" id="bulkAdminPassword" placeholder="Admin Password" class="sf-input" style="height: 28px; width: 140px; padding: 2px 8px; border-color: #ef4444;" autocomplete="new-password">
            </div>

            <button type="button" onclick="executeBulkAction()" class="sf-btn sf-btn-primary" style="height: 28px; padding: 0 12px; font-size: 11.5px;">
                <i class="fa-solid fa-check"></i> Apply
            </button>
            <button type="button" onclick="clearSelections()" class="erp-btn-reset" style="height: 28px;">Cancel</button>
        </div>
    </div>

    <!-- 3. Table Card with Dedicated Independent Scroll (RULE 1 & 3) -->
    <div class="erp-card-table">
        <form id="bulkForm" method="POST" action="<?= APP_URL ?>/salesorder/bulk_action" style="display: none;">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="bulk_action" id="hiddenBulkAction">
            <input type="hidden" name="bulk_date" id="hiddenBulkDate">
            <input type="hidden" name="bulk_rep" id="hiddenBulkRep">
            <input type="hidden" name="admin_password" id="hiddenAdminPassword">
            <div id="hiddenBulkIds"></div>
        </form>

        <div class="erp-table-scroll">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th style="width: 38px; text-align: center;">
                            <input type="checkbox" id="selectAllCheckbox" class="sf-checkbox" onchange="toggleSelectAll(this)">
                        </th>
                        <th style="width: 130px;">Order No</th>
                        <th style="width: 105px;">Date</th>
                        <th>Customer Name</th>
                        <th style="width: 150px;">Source / Channel</th>
                        <th style="width: 140px;" class="txt-right">Total Amount</th>
                        <th style="width: 110px;" class="txt-center">Status</th>
                        <th style="width: 140px;" class="txt-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="salesOrderTableBody">
                    <?php if (empty($data['orders'])): ?>
                        <tr>
                            <td colspan="8" style="padding: 50px; text-align: center; color: #64748b;">
                                <i class="fa-regular fa-folder-open" style="font-size: 28px; display: block; margin: 0 auto 8px; opacity: 0.5;"></i>
                                No sales orders matching the current criteria found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data['orders'] as $so): ?>
                            <tr>
                                <td style="text-align: center;">
                                    <input type="checkbox" value="<?= htmlspecialchars($so->source_type) ?>:<?= $so->id ?>" class="row-checkbox sf-checkbox" onchange="updateSelectedCount()">
                                </td>
                                <td>
                                    <strong style="color: #2563eb; font-family: var(--erp-mono); font-size: 13px;">
                                    <?php if ($so->source_type === 'standard'): ?>
                                        <a href="<?= APP_URL ?>/salesorder/show/<?= $so->id ?>" target="_blank" style="text-decoration: none; color: inherit;">
                                            <?= htmlspecialchars($so->document_number) ?>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= APP_URL ?>/sales/show/<?= $so->id ?>" target="_blank" style="text-decoration: none; color: inherit;">
                                            <?= htmlspecialchars($so->document_number) ?>
                                        </a>
                                    <?php endif; ?>
                                    </strong>
                                </td>
                                <td style="font-family: var(--erp-mono); font-size: 12px; color: #475569;">
                                    <?= date('Y-m-d', strtotime($so->document_date)) ?>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: #0f172a; font-size: 13px;"><?= htmlspecialchars($so->customer_name) ?></div>
                                    <?php if (!empty($so->rep_name)): ?>
                                        <div style="font-size: 11px; color: #64748b; margin-top: 1px;">
                                            <i class="fa-regular fa-user"></i> Rep: <?= htmlspecialchars($so->rep_name) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($so->source_type === 'standard'): ?>
                                        <span class="sf-badge badge-Std">📋 Standard SO</span>
                                    <?php else: ?>
                                        <span class="sf-badge badge-Route">🚚 Route Rep <?= !empty($so->route_name) ? '(' . htmlspecialchars($so->route_name) . ')' : '' ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="txt-right" style="font-weight: 700; font-family: var(--erp-mono); font-size: 13px; color: #0f172a;">
                                    Rs: <?= number_format($so->grand_total, 2) ?>
                                </td>
                                <td class="txt-center">
                                    <?php 
                                        $status = $so->status ?? 'Pending';
                                        $badgeClass = 'badge-Pending';
                                        if ($status === 'Transferred' || $status === 'Completed') { $badgeClass = 'badge-Invoiced'; }
                                        elseif ($status === 'Voided') { $badgeClass = 'badge-Voided'; }
                                    ?>
                                    <span class="sf-badge <?= $badgeClass ?>">
                                        <span class="badge-dot-sm"></span> <?= $status === 'Transferred' ? 'Invoiced' : $status ?>
                                    </span>
                                </td>
                                <td class="txt-right">
                                    <div class="row-acts">
                                        <!-- Print -->
                                        <?php if ($so->source_type === 'standard'): ?>
                                            <a href="<?= APP_URL ?>/salesorder/show/<?= $so->id ?>" target="_blank" class="act-btn print" title="Print Sales Order">
                                                <i class="fa-solid fa-print"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= APP_URL ?>/sales/show/<?= $so->id ?>" target="_blank" class="act-btn print" title="Print Route Order">
                                                <i class="fa-solid fa-print"></i>
                                            </a>
                                        <?php endif; ?>

                                        <!-- Edit -->
                                        <a href="<?= APP_URL ?>/sales/edit/<?= $so->id ?>?type=sales_order" target="_blank" class="act-btn edit" title="Edit Sales Order">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>

                                        <!-- Convert / Transfer to Invoice -->
                                        <?php if ($status !== 'Transferred' && $status !== 'Completed'): ?>
                                            <?php if ($so->source_type === 'standard'): ?>
                                                <a href="<?= APP_URL ?>/sales/create?from_so=<?= $so->id ?>" class="act-btn convert" title="Convert to Invoice">
                                                    <i class="fa-solid fa-file-export"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= APP_URL ?>/sales/create?from_so_route=<?= $so->id ?>" class="act-btn convert" title="Convert to Invoice">
                                                    <i class="fa-solid fa-file-export"></i>
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <!-- Delete -->
                                        <button type="button" class="act-btn delete" title="Delete Sales Order" onclick="openDeleteModal(<?= $so->id ?>, '<?= htmlspecialchars(addslashes($so->document_number)) ?>', '<?= htmlspecialchars(addslashes($so->source_type)) ?>')">
                                            <i class="fa-regular fa-trash-can"></i>
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
            <div class="pg-info">
                <?php
                $page = $data['page'] ?? 1;
                $limit = $data['limit'] ?? 25;
                $totalPages = $data['total_pages'] ?? 1;
                $startRecord = $totalRecords > 0 ? (($page - 1) * $limit + 1) : 0;
                $endRecord = min($page * $limit, $totalRecords);
                ?>
                Showing <strong><?= $startRecord ?></strong> – <strong><?= $endRecord ?></strong> of <strong><?= number_format($totalRecords) ?></strong>
            </div>
            <div class="pg-right">
                <div class="pg-size-wrap">
                    <span class="pg-size-lbl">SHOW:</span>
                    <select class="pg-size-sel" onchange="changeLimit(this.value)">
                        <option value="15" <?= $limit == 15 ? 'selected' : '' ?>>15</option>
                        <option value="25" <?= $limit == 25 ? 'selected' : '' ?>>25</option>
                        <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= $limit == 100 ? 'selected' : '' ?>>100</option>
                        <option value="1000" <?= $limit == 1000 ? 'selected' : '' ?>>All</option>
                    </select>
                </div>
                <div class="pg-nav">
                    <?php
                    $queryParams = $_GET;
                    $prevParams = $queryParams; $prevParams['page'] = max(1, $page - 1);
                    $nextParams = $queryParams; $nextParams['page'] = min($totalPages, $page + 1);
                    ?>
                    <a href="?<?= http_build_query($prevParams) ?>" class="pg-btn <?= $page <= 1 ? 'disabled' : '' ?>" title="Previous Page">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                    <div class="pg-current"><?= $page ?> / <?= $totalPages ?></div>
                    <a href="?<?= http_build_query($nextParams) ?>" class="pg-btn <?= $page >= $totalPages ? 'disabled' : '' ?>" title="Next Page">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- 4. Sleek Floating Island Command Bar Pill (RULE 3) -->
<div class="cmd-bar">
    <div class="cmd-search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" placeholder="Search order #, customer, notes..." value="<?= htmlspecialchars($data['search'] ?? '') ?>" onkeypress="if(event.key === 'Enter') { document.getElementById('mainSearchInput').value = this.value; document.getElementById('filterForm').submit(); }">
    </div>
    <div class="cmd-divider"></div>
    <a href="<?= APP_URL ?>/sales/create?type=sales_order" class="cmd-cta">
        <i class="fa-solid fa-plus"></i> Create Sales Order
    </a>
</div>

<!-- Delete Authentication Modal -->
<div id="deleteAuthModal" class="sf-modal">
    <div class="sf-modal-box">
        <h3 style="color: #ef4444; display: flex; align-items: center; gap: 8px; font-size: 16px; margin: 0 0 10px 0;">
            <i class="fa-solid fa-triangle-exclamation"></i> Confirm Deletion
        </h3>
        <p style="font-size: 13px; color: #475569; line-height: 1.5; margin-bottom: 16px;">
            You are about to delete <strong id="deleteRecordNum" style="color: #ef4444;"></strong>. This action requires administrative authentication and will be permanently recorded in the system audit trail.
        </p>
        
        <form id="deleteForm" method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            
            <div class="sf-form-group">
                <label>Reason for Deletion *</label>
                <textarea name="delete_reason" class="sf-input" required style="height: 60px; resize: none;" placeholder="Provide justification for deleting this record..."></textarea>
            </div>
            
            <div class="sf-form-group">
                <label>Admin Password *</label>
                <input type="password" name="password" class="sf-input" required placeholder="Enter your password to verify" autocomplete="current-password">
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 20px;">
                <button type="button" class="sf-btn sf-btn-ghost" onclick="closeDeleteModal()">Cancel</button>
                <button type="submit" class="sf-btn sf-btn-danger">
                    <i class="fa-regular fa-trash-can"></i> Verify & Delete
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Change limit helper
function changeLimit(newLimit) {
    document.getElementById('filterLimitInput').value = newLimit;
    document.getElementById('filterForm').submit();
}

// Delete modal logic
function openDeleteModal(id, number, sourceType) {
    const modal = document.getElementById('deleteAuthModal');
    const form = document.getElementById('deleteForm');
    const recordNumSpan = document.getElementById('deleteRecordNum');
    
    recordNumSpan.textContent = `Sales Order #${number}`;
    const queryParams = window.location.search || '';
    
    // Set form action based on the source table
    if (sourceType === 'standard') {
        form.action = `<?= APP_URL ?>/salesorder/delete/${id}${queryParams}`;
    } else {
        form.action = `<?= APP_URL ?>/sales/delete/${id}${queryParams}`;
    }
    
    form.reset();
    modal.classList.add('open');
}

function closeDeleteModal() {
    document.getElementById('deleteAuthModal').classList.remove('open');
}

// Outside click handler for modal
window.onclick = function(event) {
    const delModal = document.getElementById('deleteAuthModal');
    if (event.target === delModal) closeDeleteModal();
};

// Bulk Actions Logic
function toggleSelectAll(master) {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => cb.checked = master.checked);
    updateSelectedCount();
}

function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('.row-checkbox:checked');
    const bar = document.getElementById('bulkActionBar');
    const badge = document.getElementById('selectedCountBadge');
    
    if (checkboxes.length > 0) {
        bar.style.display = 'flex';
        badge.textContent = checkboxes.length + ' selected';
    } else {
        bar.style.display = 'none';
        const selectAll = document.getElementById('selectAllCheckbox');
        if (selectAll) selectAll.checked = false;
    }
}

function clearSelections() {
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
    const selectAll = document.getElementById('selectAllCheckbox');
    if (selectAll) selectAll.checked = false;
    updateSelectedCount();
}

function toggleBulkActionInputs() {
    const action = document.getElementById('bulkActionChoice').value;
    document.getElementById('bulkDateInputContainer').style.display = action === 'change_date' ? 'block' : 'none';
    document.getElementById('bulkRepInputContainer').style.display = action === 'change_rep' ? 'block' : 'none';
    document.getElementById('bulkPasswordInputContainer').style.display = action === 'delete' ? 'block' : 'none';
}

function executeBulkAction() {
    const action = document.getElementById('bulkActionChoice').value;
    if (!action) {
        alert('Please select a bulk action.');
        return;
    }

    const checkboxes = document.querySelectorAll('.row-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('No records selected.');
        return;
    }

    const hiddenForm = document.getElementById('bulkForm');
    const hiddenIdsContainer = document.getElementById('hiddenBulkIds');
    hiddenIdsContainer.innerHTML = '';

    checkboxes.forEach(cb => {
        const inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'ids[]';
        inp.value = cb.value;
        hiddenIdsContainer.appendChild(inp);
    });

    document.getElementById('hiddenBulkAction').value = action;

    if (action === 'change_date') {
        const dateVal = document.getElementById('bulkDateInput').value;
        if (!dateVal) {
            alert('Please select a new transaction date.');
            return;
        }
        document.getElementById('hiddenBulkDate').value = dateVal;
    } else if (action === 'change_rep') {
        const repVal = document.getElementById('bulkRepSelect').value;
        if (!repVal) {
            alert('Please select a representative.');
            return;
        }
        document.getElementById('hiddenBulkRep').value = repVal;
    } else if (action === 'delete') {
        const passVal = document.getElementById('bulkAdminPassword').value;
        if (!passVal) {
            alert('Admin password is required for bulk deletion.');
            return;
        }
        if (!confirm('Are you sure you want to permanently delete ' + checkboxes.length + ' selected records? This action requires administrator password verification.')) {
            return;
        }
        document.getElementById('hiddenAdminPassword').value = passVal;
    }

    hiddenForm.submit();
}
</script>
