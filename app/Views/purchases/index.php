<!-- FontAwesome 6 Icons Guarantee -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
/* ============================================================
   FALCON STATIONERY ERP: PROCUREMENT & PURCHASE ORDERS (PO)
   CLEAN & PROFESSIONAL ERP VIEWPORT ARCHITECTURE
   ============================================================ */

html, body {
    height: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: hidden !important;
    background: var(--erp-bg, #f8fafc);
    font-family: var(--erp-font, 'SF Pro Display', -apple-system, BlinkMacSystemFont, sans-serif);
}

.po-erp-workspace {
    height: calc(100vh - 48px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    padding: 12px 18px;
    box-sizing: border-box;
    gap: 10px;
    position: relative;
}



.erp-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 600;
    padding: 5px 12px;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.15s ease;
    border: 1px solid transparent;
}
.erp-btn-outline {
    background: #ffffff;
    color: #334155;
    border-color: #cbd5e1;
}
.erp-btn-outline:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
    color: #0f172a;
}

/* 2. Top-Right Clean Filter Bar */
.erp-filter-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 6px 14px;
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
    height: 29px;
    max-width: 155px;
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
    height: 29px;
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
}

.erp-table-scroll {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: auto;
    padding-bottom: 54px; /* Clearance for floating bottom island */
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
    padding: 9px 14px;
    border-bottom: 1px solid #e2e8f0;
    white-space: nowrap;
}

.erp-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.1s;
}
.erp-table tbody tr:hover {
    background: #f8fafc !important;
}
/* Alternating Zebra Striping */
.erp-table tbody tr:nth-child(even) {
    background: #fbfcfe;
}

.erp-table td {
    padding: 10px 14px;
    vertical-align: middle;
    color: #1e293b;
}

.font-mono {
    font-family: var(--erp-mono, ui-monospace, monospace);
}

/* Badges */
.erp-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 2px 8px;
    border-radius: 9999px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.02em;
    white-space: nowrap;
}
.badge-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
}
.badge-draft {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
}
.badge-draft .badge-dot { background: #94a3b8; }
.badge-sent {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
}
.badge-sent .badge-dot { background: #3b82f6; }
.badge-received {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
}
.badge-received .badge-dot { background: #10b981; }

.badge-mix-warning {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
    font-size: 10px;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 4px;
    margin-left: 6px;
}

/* Quiet, Sleek ERP Row Actions */
.erp-row-actions {
    display: flex;
    align-items: center;
    gap: 4px;
    justify-content: flex-end;
}

.erp-icon-btn {
    width: 26px;
    height: 26px;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11.5px;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.15s ease;
}
.erp-icon-btn i {
    font-size: 11.5px;
    line-height: 1;
}
.erp-icon-btn:hover {
    color: #2563eb;
    border-color: #2563eb;
    background: #eff6ff;
}
.erp-icon-btn.success:hover {
    color: #059669;
    border-color: #059669;
    background: #ecfdf5;
}
.erp-icon-btn.danger:hover {
    color: #dc2626;
    border-color: #dc2626;
    background: #fef2f2;
}

/* 4. Pinned Pagination Bar */
.erp-pagination-bar {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 42px;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 16px;
    z-index: 20;
}

.erp-pg-info {
    font-size: 12px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 12px;
}

.erp-pg-buttons {
    display: flex;
    align-items: center;
    gap: 4px;
}

.erp-pg-btn {
    min-width: 28px;
    height: 28px;
    padding: 0 6px;
    border-radius: 5px;
    border: 1px solid #d1d5db;
    background: #ffffff;
    color: #334155;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.15s;
}
.erp-pg-btn i {
    font-size: 11px;
    color: #475569;
    line-height: 1;
}
.erp-pg-btn:hover:not(:disabled) {
    background: #f1f5f9;
    color: #0f172a;
    border-color: #94a3b8;
}
.erp-pg-btn.active {
    background: #2563eb;
    color: #ffffff !important;
    border-color: #2563eb;
}
.erp-pg-btn:disabled {
    opacity: 0.35;
    cursor: not-allowed;
    background: #f8fafc;
    border-color: #e2e8f0;
}

/* 5. Standard Floating Bottom Command Bar */
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
    color: rgba(255, 255, 255, 0.65);
    font-size: 14px;
}

.cmd-search input {
    background: transparent;
    border: none;
    outline: none;
    color: #ffffff;
    font-size: 14px;
    font-weight: 500;
    font-family: var(--erp-font);
    width: 320px;
    padding: 3px 0;
}
.cmd-search input::placeholder {
    color: rgba(255, 255, 255, 0.48);
}

.cmd-clear-btn {
    color: rgba(255, 255, 255, 0.4);
    cursor: pointer;
    font-size: 13px;
    display: none;
}
.cmd-clear-btn:hover {
    color: #ffffff;
}

.cmd-divider {
    width: 1px;
    height: 22px;
    background: rgba(255, 255, 255, 0.2);
}

.cmd-cta {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: #ffffff;
    color: #0f172a !important;
    font-size: 13px;
    font-weight: 700;
    padding: 7px 18px;
    border-radius: 9999px;
    border: none;
    text-decoration: none;
    cursor: pointer;
    transition: transform 0.15s ease, background 0.15s ease, box-shadow 0.15s ease;
    box-shadow: 0 2px 6px rgba(0,0,0,0.12);
}
.cmd-cta:hover {
    background: #f8fafc;
    transform: scale(1.02);
}

/* Alerts */
.erp-alert {
    padding: 7px 12px;
    border-radius: 6px;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.erp-alert.success {
    background: #ecfdf5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}
.erp-alert.error {
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #fecaca;
}
.erp-alert-close {
    background: transparent;
    border: none;
    color: inherit;
    cursor: pointer;
    font-size: 12px;
}

/* Modals */
.erp-modal-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1100;
}
.erp-modal-overlay.hidden { display: none; }

.erp-modal-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    width: 100%;
    max-width: 440px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
}
.erp-modal-header {
    padding: 14px 18px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.erp-modal-title {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
}
.erp-modal-body {
    padding: 18px;
    font-size: 13px;
    color: #334155;
    line-height: 1.5;
}
.erp-modal-footer {
    padding: 12px 18px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: flex-end;
    gap: 8px;
}

/* Loading overlay */
.erp-table-loader {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(255, 255, 255, 0.7);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 30;
}
.erp-table-loader.active { display: flex; }
</style>

<div class="erp-viewport-view po-erp-workspace">
<form id="filterForm" action="<?= APP_URL ?>/purchase" method="GET" onsubmit="event.preventDefault(); applyAjaxFilters();">
    <input type="hidden" name="page" id="currentPageInput" value="<?= $data['page'] ?? 1 ?>">
    <input type="hidden" name="per_page" id="perPageInput" value="<?= $data['per_page'] ?? 15 ?>">

    <!-- Alert Strip (if any) -->
    <?php if (!empty($data['success']) || isset($_GET['success'])): ?>
    <div id="flash-success-alert" class="erp-alert success">
        <div><i class="fa-solid fa-circle-check" style="margin-right:6px;"></i> <?= htmlspecialchars($data['success'] ?? $_GET['success'] ?? '') ?></div>
        <button type="button" class="erp-alert-close" onclick="document.getElementById('flash-success-alert').style.display='none'"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <?php endif; ?>

    <?php if (!empty($data['error']) || isset($_GET['error'])): ?>
    <div id="flash-error-alert" class="erp-alert error">
        <div><i class="fa-solid fa-circle-exclamation" style="margin-right:6px;"></i> <?= htmlspecialchars($data['error'] ?? $_GET['error'] ?? '') ?></div>
        <button type="button" class="erp-alert-close" onclick="document.getElementById('flash-error-alert').style.display='none'"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <?php endif; ?>

    <!-- 2. Clean Filters Bar Above Table with Top-Right Alignment -->
    <div class="erp-filter-bar">
        <!-- Left: Count -->
        <div class="erp-filter-left">
            <span class="erp-results-badge">
                Showing <strong id="matching-count"><?= number_format($data['total_items'] ?? count($data['pos'])) ?></strong> PO records
            </span>
        </div>

        <!-- Right: Filter Controls -->
        <div class="erp-filter-right">
            <!-- Supplier Filter -->
            <div class="erp-filter-control">
                <span class="erp-filter-label">Supplier:</span>
                <select name="vendor_id" id="vendorInput" class="erp-select" onchange="applyAjaxFilters()">
                    <option value="">All Suppliers</option>
                    <?php if (!empty($data['vendors'])): ?>
                        <?php foreach ($data['vendors'] as $ven): ?>
                        <option value="<?= $ven->id ?>" <?= (string)($data['filters']['vendor_id'] ?? '') === (string)$ven->id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ven->name) ?>
                        </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Status Filter -->
            <div class="erp-filter-control">
                <span class="erp-filter-label">Status:</span>
                <select name="status" id="statusInput" class="erp-select" onchange="applyAjaxFilters()">
                    <option value="" <?= ($data['filters']['status'] ?? '') === '' ? 'selected' : '' ?>>All Statuses</option>
                    <option value="Draft" <?= ($data['filters']['status'] ?? '') === 'Draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="Sent" <?= ($data['filters']['status'] ?? '') === 'Sent' ? 'selected' : '' ?>>Sent</option>
                    <option value="Received" <?= ($data['filters']['status'] ?? '') === 'Received' ? 'selected' : '' ?>>Received / Closed</option>
                </select>
            </div>

            <!-- Period Filter (Clean & Compact) -->
            <div class="erp-filter-control">
                <span class="erp-filter-label">Period:</span>
                <select name="period" id="periodInput" class="erp-select" onchange="applyAjaxFilters()">
                    <option value="" <?= empty($data['filters']['period']) ? 'selected' : '' ?>>All Time</option>
                    <option value="today" <?= ($data['filters']['period'] ?? '') === 'today' ? 'selected' : '' ?>>Today</option>
                    <option value="this_week" <?= ($data['filters']['period'] ?? '') === 'this_week' ? 'selected' : '' ?>>This Week</option>
                    <option value="this_month" <?= ($data['filters']['period'] ?? '') === 'this_month' ? 'selected' : '' ?>>This Month</option>
                    <option value="last_30" <?= ($data['filters']['period'] ?? '') === 'last_30' ? 'selected' : '' ?>>Last 30 Days</option>
                    <option value="this_year" <?= ($data['filters']['period'] ?? '') === 'this_year' ? 'selected' : '' ?>>This Year</option>
                </select>
            </div>

            <!-- Reset Filters -->
            <button type="button" onclick="clearAllFilters()" class="erp-btn-reset" title="Reset all filters">
                <i class="fa-solid fa-arrow-rotate-left"></i> Reset
            </button>
        </div>
    </div>

    <!-- 3. Table Card (Full Height & Scrollable Table Rows Only with Zebra Striping) -->
    <div class="erp-card-table">
        <!-- Loading Overlay -->
        <div id="table-loader" class="erp-table-loader">
            <div style="background:#fff; padding:12px 20px; border-radius:8px; box-shadow:0 4px 14px rgba(0,0,0,0.1); display:flex; align-items:center; gap:10px;">
                <i class="fa-solid fa-circle-notch fa-spin" style="color:#2563eb; font-size:16px;"></i>
                <span style="font-size:12px; font-weight:600; color:#334155;">Filtering Purchase Orders...</span>
            </div>
        </div>

        <div class="erp-table-scroll" id="table-scroll-container">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th style="width: 130px;">PO Number</th>
                        <th>Supplier / Vendor Details</th>
                        <th style="width: 120px;">PO Date</th>
                        <th style="width: 120px;">Expected Date</th>
                        <th class="text-center" style="width: 75px; text-align:center;">Items</th>
                        <th class="text-right" style="width: 130px; text-align:right;">Amount (LKR)</th>
                        <th class="text-center" style="width: 105px; text-align:center;">Status</th>
                        <th class="text-right" style="width: 140px; text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php if (empty($data['pos'])): ?>
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 44px 16px; text-align:center; color: #64748b;">
                            <i class="fa-solid fa-file-invoice" style="font-size: 28px; margin-bottom: 8px; display: block; opacity: 0.4;"></i>
                            <strong style="font-size: 14px; color: #0f172a;">No Purchase Orders found</strong>
                            <div style="font-size: 12px; margin-top: 4px;">Try clearing filters or search criteria.</div>
                        </td>
                    </tr>
                    <?php else: foreach ($data['pos'] as $po): ?>
                    <tr>
                        <td>
                            <strong class="font-mono" style="font-size: 13px; color: #2563eb; font-weight: 700;">
                                <?= htmlspecialchars($po->po_number) ?>
                            </strong>
                            <?php if (!empty($po->has_mix)): ?>
                                <span class="badge-mix-warning" title="Contains unassigned mixed variations requiring resolution">
                                    <i class="fa-solid fa-shuffle"></i> MIX
                                </span>
                            <?php endif; ?>
                            <div style="font-size: 10.5px; color: #94a3b8; margin-top: 2px;">
                                <?= htmlspecialchars($po->creator_name ?? 'System') ?>
                            </div>
                        </td>
                        <td>
                            <strong style="color: #0f172a; font-size: 13px;">
                                <?= htmlspecialchars($po->vendor_name) ?>
                            </strong>
                            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                <?php if (!empty($po->email)): ?>
                                    <span><i class="fa-solid fa-envelope" style="font-size: 10px; opacity:0.6;"></i> <?= htmlspecialchars($po->email) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($po->phone)): ?>
                                    <span style="margin-left: 8px;"><i class="fa-solid fa-phone" style="font-size: 10px; opacity:0.6;"></i> <?= htmlspecialchars($po->phone) ?></span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="font-mono" style="color: #334155; font-size: 12px;">
                            <?= date('Y-m-d', strtotime($po->po_date)) ?>
                        </td>
                        <td class="font-mono" style="color: #64748b; font-size: 12px;">
                            <?= !empty($po->expected_date) ? date('Y-m-d', strtotime($po->expected_date)) : '-' ?>
                        </td>
                        <td style="text-align: center;">
                            <span class="font-mono" style="font-weight: 700; color: #0f172a;">
                                <?= (int)($po->items_count ?? 1) ?>
                            </span>
                        </td>
                        <td class="font-mono" style="text-align: right; font-weight: 700; color: #0f172a; font-size: 13px;">
                            <?= number_format($po->total_amount, 2) ?>
                        </td>
                        <td style="text-align: center;">
                            <?php if ($po->status === 'Received'): ?>
                                <span class="erp-badge badge-received"><span class="badge-dot"></span>Received</span>
                            <?php elseif ($po->status === 'Sent'): ?>
                                <span class="erp-badge badge-sent"><span class="badge-dot"></span>Sent</span>
                            <?php else: ?>
                                <span class="erp-badge badge-draft"><span class="badge-dot"></span>Draft</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: right;">
                            <div class="erp-row-actions">
                                <!-- Print / Show View -->
                                <a href="<?= APP_URL ?>/purchase/show/<?= $po->id ?>" target="_blank" class="erp-icon-btn" title="Print / PDF View">
                                    <i class="fa-solid fa-print"></i>
                                </a>

                                <?php if ($po->status !== 'Received'): ?>
                                    <!-- Edit PO -->
                                    <a href="<?= APP_URL ?>/purchase/edit/<?= $po->id ?>" class="erp-icon-btn" title="Edit Purchase Order">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <!-- Receive to GRN -->
                                    <?php if (!empty($po->has_mix)): ?>
                                        <a href="<?= APP_URL ?>/purchase/resolve_mix_grn/<?= $po->id ?>" class="erp-icon-btn success" title="Resolve Mix Variations & Receive GRN">
                                            <i class="fa-solid fa-boxes-packing"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= APP_URL ?>/grn/create?po_id=<?= $po->id ?>" class="erp-icon-btn success" title="Receive to Goods Receipt (GRN)">
                                            <i class="fa-solid fa-boxes-packing"></i>
                                        </a>
                                    <?php endif; ?>

                                    <!-- Email Dispatch Modal Opener -->
                                    <?php if (!empty($po->email)): ?>
                                        <button type="button" class="erp-icon-btn" 
                                                onclick="openEmailModal(<?= $po->id ?>, '<?= htmlspecialchars(addslashes($po->po_number)) ?>', '<?= htmlspecialchars(addslashes($po->vendor_name)) ?>', '<?= htmlspecialchars(addslashes($po->email)) ?>')" 
                                                title="Dispatch PO via Email to <?= htmlspecialchars($po->email) ?>">
                                            <i class="fa-solid fa-paper-plane"></i>
                                        </button>
                                    <?php endif; ?>

                                    <!-- Delete PO Modal Opener -->
                                    <button type="button" class="erp-icon-btn danger" 
                                            onclick="openDeleteModal(<?= $po->id ?>, '<?= htmlspecialchars(addslashes($po->po_number)) ?>', '<?= htmlspecialchars(addslashes($po->vendor_name)) ?>')" 
                                            title="Delete Purchase Order">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <!-- 4. Pinned Pagination Bar at Bottom of Table Card -->
        <div class="erp-pagination-bar">
            <div class="erp-pg-info">
                <span>Page <strong><?= $data['page'] ?></strong> of <strong><?= max(1, $data['total_pages']) ?></strong></span>
                <span style="color:#cbd5e1;">|</span>
                <span>
                    Show:
                    <select name="per_page" onchange="updatePageSize(this.value)" class="erp-select" style="height:26px; padding:0 18px 0 6px; font-size:11.5px; width:65px;">
                        <option value="15" <?= ($data['per_page'] == 15) ? 'selected' : '' ?>>15</option>
                        <option value="25" <?= ($data['per_page'] == 25) ? 'selected' : '' ?>>25</option>
                        <option value="50" <?= ($data['per_page'] == 50) ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= ($data['per_page'] == 100) ? 'selected' : '' ?>>100</option>
                    </select>
                </span>
            </div>

            <div class="erp-pg-buttons" id="paginationControls">
                <button type="button" class="erp-pg-btn" onclick="navigatePage(1)" <?= ($data['page'] <= 1) ? 'disabled' : '' ?> title="First Page">
                    <i class="fa-solid fa-angles-left"></i>
                </button>
                <button type="button" class="erp-pg-btn" onclick="navigatePage(<?= $data['page'] - 1 ?>)" <?= ($data['page'] <= 1) ? 'disabled' : '' ?> title="Previous Page">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>

                <?php 
                    $cur = $data['page'];
                    $total = max(1, $data['total_pages']);
                    $start = max(1, $cur - 2);
                    $end = min($total, $cur + 2);
                    for ($p = $start; $p <= $end; $p++):
                ?>
                    <button type="button" class="erp-pg-btn <?= ($p == $cur) ? 'active' : '' ?>" onclick="navigatePage(<?= $p ?>)">
                        <?= $p ?>
                    </button>
                <?php endfor; ?>

                <button type="button" class="erp-pg-btn" onclick="navigatePage(<?= $data['page'] + 1 ?>)" <?= ($data['page'] >= $total) ? 'disabled' : '' ?> title="Next Page">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
                <button type="button" class="erp-pg-btn" onclick="navigatePage(<?= $total ?>)" <?= ($data['page'] >= $total) ? 'disabled' : '' ?> title="Last Page">
                    <i class="fa-solid fa-angles-right"></i>
                </button>
            </div>
        </div>
    </div>
</form>
</div>

<!-- 5. Sleek Floating Island Command Bar (Standard Size: 320px Input + Action Button) -->
<div class="cmd-bar">
    <div class="cmd-search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="searchInput" placeholder="Search PO, Supplier, Creator..." value="<?= htmlspecialchars($data['search'] ?? '') ?>" autocomplete="off">
        <i class="fa-solid fa-xmark cmd-clear-btn" id="searchClearBtn" onclick="clearSearchInput()" title="Clear search"></i>
    </div>
    <div class="cmd-divider"></div>
    <a href="<?= APP_URL ?>/purchase/wizard" class="cmd-cta" title="Create Purchase Order">
        <i class="fa-solid fa-plus"></i>
        <span>+ New PO</span>
    </a>
</div>

<!-- Modal: Delete PO Confirmation -->
<div id="deletePoModal" class="erp-modal-overlay hidden">
    <div class="erp-modal-card">
        <div class="erp-modal-header">
            <div class="erp-modal-title" style="color:#dc2626;">
                <i class="fa-solid fa-triangle-exclamation"></i> Delete Purchase Order
            </div>
            <button type="button" onclick="closeDeleteModal()" style="background:none; border:none; cursor:pointer; color:#94a3b8;"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="deletePoForm" onsubmit="handleDeleteSubmit(event)">
            <input type="hidden" name="action" value="delete_po">
            <input type="hidden" name="po_id" id="deletePoId">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <div class="erp-modal-body">
                <p>Are you sure you want to permanently delete Purchase Order <strong id="deletePoNumText" class="font-mono" style="color:#0f172a;"></strong> for supplier <strong id="deletePoVendorText"></strong>?</p>
                <p style="color:#dc2626; font-size:12px; margin-top:8px;">
                    <i class="fa-solid fa-circle-exclamation"></i> This operation cannot be undone. Received POs cannot be deleted.
                </p>
                <div id="deleteErrorMsg" style="display:none; color:#dc2626; font-size:12px; margin-top:8px;"></div>
            </div>
            <div class="erp-modal-footer">
                <button type="button" onclick="closeDeleteModal()" class="erp-btn erp-btn-outline">Cancel</button>
                <button type="submit" id="deleteSubmitBtn" class="erp-btn" style="background:#dc2626; color:#fff;">Delete PO</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Email Dispatch Confirmation -->
<div id="emailPoModal" class="erp-modal-overlay hidden">
    <div class="erp-modal-card">
        <div class="erp-modal-header">
            <div class="erp-modal-title" style="color:#2563eb;">
                <i class="fa-solid fa-paper-plane"></i> Dispatch PO via Email
            </div>
            <button type="button" onclick="closeEmailModal()" style="background:none; border:none; cursor:pointer; color:#94a3b8;"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="emailPoForm" onsubmit="handleEmailSubmit(event)">
            <input type="hidden" name="po_id" id="emailPoId">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <div class="erp-modal-body">
                <p>Send Purchase Order <strong id="emailPoNumText" class="font-mono"></strong> directly to supplier <strong id="emailPoVendorText"></strong>?</p>
                <div style="background:#f1f5f9; padding:10px 12px; border-radius:6px; margin-top:10px; font-size:12px; color:#334155;">
                    <i class="fa-solid fa-envelope" style="color:#64748b; margin-right:6px;"></i>
                    Recipient: <strong id="emailPoAddressText" style="color:#0f172a;"></strong>
                </div>
                <div id="emailErrorMsg" style="display:none; color:#dc2626; font-size:12px; margin-top:8px;"></div>
            </div>
            <div class="erp-modal-footer">
                <button type="button" onclick="closeEmailModal()" class="erp-btn erp-btn-outline">Cancel</button>
                <button type="submit" id="emailSubmitBtn" class="erp-btn" style="background:#2563eb; color:#fff;">
                    <i class="fa-solid fa-paper-plane"></i> Send Email
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// CSRF Token Helper
function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '<?= $_SESSION['csrf_token'] ?? '' ?>';
}

// Live Search Debounce
let searchTimer = null;
const searchInput = document.getElementById('searchInput');
const searchClearBtn = document.getElementById('searchClearBtn');

if (searchInput) {
    if (searchInput.value.trim().length > 0) {
        searchClearBtn.style.display = 'block';
    }
    searchInput.addEventListener('input', function() {
        searchClearBtn.style.display = this.value.trim().length > 0 ? 'block' : 'none';
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            document.getElementById('currentPageInput').value = 1;
            applyAjaxFilters();
        }, 320);
    });
}

function clearSearchInput() {
    if (searchInput) {
        searchInput.value = '';
        searchClearBtn.style.display = 'none';
        document.getElementById('currentPageInput').value = 1;
        applyAjaxFilters();
    }
}

function clearAllFilters() {
    if (searchInput) searchInput.value = '';
    if (searchClearBtn) searchClearBtn.style.display = 'none';
    document.getElementById('vendorInput').value = '';
    document.getElementById('statusInput').value = '';
    document.getElementById('periodInput').value = '';
    document.getElementById('currentPageInput').value = 1;
    applyAjaxFilters();
}

function navigatePage(page) {
    document.getElementById('currentPageInput').value = page;
    applyAjaxFilters();
}

function updatePageSize(size) {
    document.getElementById('perPageInput').value = size;
    document.getElementById('currentPageInput').value = 1;
    applyAjaxFilters();
}

// AJAX Live Filter Engine
function applyAjaxFilters() {
    const loader = document.getElementById('table-loader');
    if (loader) loader.classList.add('active');

    const form = document.getElementById('filterForm');
    const formData = new FormData(form);

    // Merge search input from floating bar
    if (searchInput) {
        formData.set('search', searchInput.value.trim());
    }

    const queryParams = new URLSearchParams(formData).toString();
    const targetUrl = '<?= APP_URL ?>/purchase?' + queryParams;

    fetch(targetUrl, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html'
        }
    })
    .then(res => {
        if (!res.ok) throw new Error('Network error');
        return res.text();
    })
    .then(html => {
        const doc = new DOMParser().parseFromString(html, 'text/html');
        
        // Swap Table Rows
        const newBody = doc.getElementById('tableBody');
        const oldBody = document.getElementById('tableBody');
        if (newBody && oldBody) oldBody.innerHTML = newBody.innerHTML;

        // Swap Pagination Controls
        const newPg = doc.getElementById('paginationControls');
        const oldPg = document.getElementById('paginationControls');
        if (newPg && oldPg) oldPg.innerHTML = newPg.innerHTML;

        // Update Counter
        const newCount = doc.getElementById('matching-count');
        const oldCount = document.getElementById('matching-count');
        if (newCount && oldCount) oldCount.innerHTML = newCount.innerHTML;

        window.history.pushState({}, '', targetUrl);
    })
    .catch(err => {
        console.error('Filter Error:', err);
    })
    .finally(() => {
        if (loader) loader.classList.remove('active');
    });
}

// Modal Handlers: Delete PO
function openDeleteModal(poId, poNum, vendor) {
    document.getElementById('deletePoId').value = poId;
    document.getElementById('deletePoNumText').textContent = poNum;
    document.getElementById('deletePoVendorText').textContent = vendor;
    document.getElementById('deleteErrorMsg').style.display = 'none';
    document.getElementById('deletePoModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deletePoModal').classList.add('hidden');
}

function handleDeleteSubmit(e) {
    e.preventDefault();
    const btn = document.getElementById('deleteSubmitBtn');
    const err = document.getElementById('deleteErrorMsg');
    const form = document.getElementById('deletePoForm');
    const formData = new FormData(form);

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Deleting...';
    err.style.display = 'none';

    fetch('<?= APP_URL ?>/purchase', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': getCsrfToken(),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeDeleteModal();
            applyAjaxFilters();
        } else {
            err.textContent = data.message || 'Failed to delete Purchase Order.';
            err.style.display = 'block';
        }
    })
    .catch(ex => {
        err.textContent = ex.message || 'An error occurred during deletion.';
        err.style.display = 'block';
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = 'Delete PO';
    });
}

// Modal Handlers: Email PO
function openEmailModal(poId, poNum, vendor, email) {
    document.getElementById('emailPoId').value = poId;
    document.getElementById('emailPoNumText').textContent = poNum;
    document.getElementById('emailPoVendorText').textContent = vendor;
    document.getElementById('emailPoAddressText').textContent = email;
    document.getElementById('emailErrorMsg').style.display = 'none';
    document.getElementById('emailPoModal').classList.remove('hidden');
}

function closeEmailModal() {
    document.getElementById('emailPoModal').classList.add('hidden');
}

function handleEmailSubmit(e) {
    e.preventDefault();
    const btn = document.getElementById('emailSubmitBtn');
    const err = document.getElementById('emailErrorMsg');
    const form = document.getElementById('emailPoForm');
    const formData = new FormData(form);

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending...';
    err.style.display = 'none';

    fetch('<?= APP_URL ?>/purchase/email_po', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': getCsrfToken(),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeEmailModal();
            applyAjaxFilters();
            alert(data.message || 'PO dispatched successfully!');
        } else {
            err.textContent = data.message || 'Failed to dispatch email.';
            err.style.display = 'block';
        }
    })
    .catch(ex => {
        err.textContent = ex.message || 'An error occurred while sending email.';
        err.style.display = 'block';
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Send Email';
    });
}

// ESC closes modals
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeDeleteModal();
        closeEmailModal();
    }
});
</script>
