<!-- FontAwesome 6 Icons Guarantee -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
/* ============================================================
   FALCON STATIONERY ERP: GOODS RECEIPT NOTES (GRN)
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

.grn-erp-workspace {
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

/* 1. Top-Right Clean Filter Bar Above Table */
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
    font-family: var(--erp-font, 'SF Pro Display', sans-serif);
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
    font-family: var(--erp-font, 'SF Pro Display', sans-serif);
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

/* 2. Table Card with Dedicated Independent Scroll */
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
    font-family: var(--erp-mono, 'SF Mono', ui-monospace, monospace);
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
    flex-shrink: 0;
}
.badge-approved {
    background: #ecfdf5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}
.badge-approved .badge-dot {
    background: #10b981;
}
.badge-pending {
    background: #fffbeb;
    color: #92400e;
    border: 1px solid #fde68a;
}
.badge-pending .badge-dot {
    background: #f59e0b;
}

/* 3. Action Buttons (Ghost Icon Style) */
.erp-row-actions {
    display: flex;
    align-items: center;
    gap: 4px;
    justify-content: flex-end;
}
.erp-icon-btn {
    width: 26px;
    height: 26px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    font-size: 11.5px;
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
.erp-icon-btn.approve {
    color: #16a34a;
}
.erp-icon-btn.approve:hover {
    background: #ecfdf5;
    border-color: #86efac;
    color: #15803d;
}
.erp-icon-btn.delete {
    color: #dc2626;
}
.erp-icon-btn.delete:hover {
    background: #fef2f2;
    border-color: #fca5a5;
    color: #b91c1c;
}

/* 4. Pinned Pagination Footer */
.erp-pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 14px;
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
    min-width: 26px;
    height: 26px;
    padding: 0 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #334155;
    border-radius: 4px;
    font-size: 12px;
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

/* 5. Floating Bottom Command Island */
.cmd-bar {
    position: fixed;
    bottom: 22px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(15, 23, 42, 0.94);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 9999px;
    padding: 5px 8px 5px 12px;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.2);
    z-index: 50;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
.cmd-search {
    display: flex;
    align-items: center;
    gap: 8px;
    position: relative;
    width: 320px;
}
.cmd-search i {
    color: #94a3b8;
    font-size: 13px;
    flex-shrink: 0;
}
.cmd-search input {
    width: 100%;
    background: transparent;
    border: none;
    outline: none;
    color: #f8fafc;
    font-size: 13px;
    font-family: var(--erp-font, 'SF Pro Display', sans-serif);
    padding: 4px 22px 4px 0;
}
.cmd-search input::placeholder {
    color: #64748b;
}
.cmd-clear-btn {
    position: absolute;
    right: 4px;
    color: #64748b;
    cursor: pointer;
    font-size: 12px;
    display: none;
}
.cmd-clear-btn:hover {
    color: #f8fafc;
}
.cmd-divider {
    width: 1px;
    height: 20px;
    background: rgba(255, 255, 255, 0.18);
    flex-shrink: 0;
}
.cmd-cta {
    background: #ffffff;
    color: #0f172a;
    font-size: 12.5px;
    font-weight: 700;
    padding: 6px 14px;
    border-radius: 9999px;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    text-decoration: none;
    white-space: nowrap;
    transition: transform 0.15s, background 0.15s;
}
.cmd-cta:hover {
    background: #f1f5f9;
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
    background: none;
    border: none;
    cursor: pointer;
    color: inherit;
    font-size: 13px;
    padding: 0;
}

/* Modals */
.erp-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.55);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 100;
    padding: 16px;
}
.erp-modal-overlay.hidden {
    display: none !important;
}
.erp-modal-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
    width: 100%;
    max-width: 440px;
    overflow: hidden;
    animation: erpModalIn 0.18s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes erpModalIn {
    from { opacity: 0; transform: scale(0.96) translateY(6px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
.erp-modal-header {
    padding: 14px 18px;
    border-bottom: 1px solid #f1f5f9;
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
    padding: 16px 18px;
    font-size: 13px;
    color: #334155;
    line-height: 1.5;
}
.erp-modal-footer {
    padding: 12px 18px;
    background: #f8fafc;
    border-top: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
}
.erp-input {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 13px;
    color: #0f172a;
    font-family: inherit;
    outline: none;
    box-sizing: border-box;
    transition: border-color 0.15s;
}
.erp-input:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
}

/* AJAX Spinner */
.erp-loading-overlay {
    position: absolute;
    inset: 0;
    background: rgba(255, 255, 255, 0.65);
    backdrop-filter: blur(2px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 20;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.15s;
}
.erp-loading-overlay.active {
    opacity: 1;
    pointer-events: auto;
}
.erp-spinner {
    width: 28px;
    height: 28px;
    border: 3px solid #e2e8f0;
    border-top-color: #2563eb;
    border-radius: 50%;
    animation: erpSpin 0.6s linear infinite;
}
@keyframes erpSpin {
    to { transform: rotate(360deg); }
}
</style>

<div class="erp-viewport-view grn-erp-workspace">
<form id="filterForm" action="<?= APP_URL ?>/grn" method="GET" onsubmit="event.preventDefault(); applyAjaxFilters();">
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

    <!-- 1. Dedicated Filters Bar Above Table with Top-Right Alignment -->
    <div class="erp-filter-bar">
        <!-- Left: Count -->
        <div class="erp-filter-left">
            <span class="erp-results-badge">
                Showing <strong id="matching-count"><?= number_format($data['total_items'] ?? count($data['grns'])) ?></strong> GRN records
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
                            <option value="<?= $ven->id ?>" <?= (($data['filters']['vendor_id'] ?? '') == $ven->id) ? 'selected' : '' ?>>
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
                    <option value="pending" <?= ($data['filters']['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending Approval</option>
                    <option value="approved" <?= ($data['filters']['status'] ?? '') === 'approved' ? 'selected' : '' ?>>Approved</option>
                </select>
            </div>

            <!-- Period Filter -->
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
            <button type="button" class="erp-btn-reset" onclick="resetFilters()" title="Reset all filters">
                <i class="fa-solid fa-arrow-rotate-left"></i>
                <span>Reset</span>
            </button>
        </div>
    </div>

    <!-- 2. Table Card (Rows Scroll Only, Header Sticky, Pinned Pagination) -->
    <div class="erp-card-table">
        <div class="erp-loading-overlay" id="tableLoadingOverlay">
            <div class="erp-spinner"></div>
        </div>

        <div class="erp-table-scroll" id="tableScrollContainer">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th style="width: 140px;">GRN Number</th>
                        <th style="width: 105px;">Received Date</th>
                        <th style="min-width: 220px;">Supplier / Vendor</th>
                        <th style="width: 120px;">Linked PO</th>
                        <th style="width: 80px; text-align: center;">Items</th>
                        <th style="width: 130px; text-align: right;">Total Amount</th>
                        <th style="width: 135px; text-align: center;">Status</th>
                        <th style="width: 120px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php if (empty($data['grns'])): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 48px 16px; color: #94a3b8; font-style: italic;">
                            <i class="fa-solid fa-tray" style="font-size: 28px; display: block; margin-bottom: 8px; color: #cbd5e1;"></i>
                            No Goods Receipt Notes matching your filters.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($data['grns'] as $grn): ?>
                    <tr>
                        <!-- GRN Number -->
                        <td class="font-mono" style="font-weight: 700; color: #2563eb;">
                            <a href="<?= APP_URL ?>/grn/show/<?= $grn->id ?>" target="_blank" style="color: inherit; text-decoration: none;" title="Open Details / Print">
                                <?= htmlspecialchars($grn->grn_number) ?>
                            </a>
                            <?php if (!empty($grn->receipt_number)): ?>
                                <div style="font-size: 10.5px; color: #16a34a; font-weight: 500; margin-top: 1px;" title="Supplier Invoice / DC #">
                                    <i class="fa-solid fa-receipt" style="font-size: 9.5px;"></i> <?= htmlspecialchars($grn->receipt_number) ?>
                                </div>
                            <?php endif; ?>
                        </td>

                        <!-- Received Date -->
                        <td class="font-mono" style="color: #475569; font-size: 12px; white-space: nowrap;">
                            <?= date('Y-m-d', strtotime($grn->grn_date)) ?>
                        </td>

                        <!-- Supplier / Vendor -->
                        <td>
                            <div style="font-weight: 600; color: #0f172a;"><?= htmlspecialchars($grn->vendor_name) ?></div>
                            <div style="font-size: 11px; color: #64748b; margin-top: 1px;">
                                By <?= htmlspecialchars($grn->creator_name ?? 'System') ?>
                            </div>
                        </td>

                        <!-- Linked PO -->
                        <td>
                            <?php if (!empty($grn->po_number)): ?>
                                <a href="<?= APP_URL ?>/purchase/show/<?= $grn->po_id ?>" target="_blank" class="font-mono" style="display:inline-flex; align-items:center; gap:4px; font-size: 11.5px; font-weight: 600; color: #475569; text-decoration:none; background:#f1f5f9; padding: 2px 7px; border-radius: 4px; border:1px solid #e2e8f0;" title="View Purchase Order">
                                    <i class="fa-solid fa-link" style="font-size: 9.5px; color:#94a3b8;"></i>
                                    <?= htmlspecialchars($grn->po_number) ?>
                                </a>
                            <?php else: ?>
                                <span style="font-size: 11px; color: #94a3b8; font-style: italic;">Manual</span>
                            <?php endif; ?>
                        </td>

                        <!-- Items Count -->
                        <td class="font-mono" style="text-align: center; font-weight: 600; color: #334155;">
                            <?= number_format($grn->items_count ?? 0) ?>
                        </td>

                        <!-- Total Amount -->
                        <td class="font-mono" style="text-align: right; font-weight: 700; color: #0f172a; font-size: 13px;">
                            <?= number_format($grn->total_amount, 2) ?>
                        </td>

                        <!-- Status -->
                        <td style="text-align: center;">
                            <?php if ($grn->is_approved): ?>
                                <span class="erp-badge badge-approved" title="Approved by <?= htmlspecialchars($grn->approver_name ?? 'Admin') ?>">
                                    <span class="badge-dot"></span>Approved
                                </span>
                                <?php if (!empty($grn->approver_name)): ?>
                                    <div style="font-size: 10px; color: #94a3b8; margin-top: 1px;">
                                        by <?= htmlspecialchars($grn->approver_name) ?>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="erp-badge badge-pending">
                                    <span class="badge-dot"></span>Pending
                                </span>
                            <?php endif; ?>
                        </td>

                        <!-- Actions -->
                        <td style="text-align: right;">
                            <div class="erp-row-actions">
                                <!-- Print / Show View -->
                                <a href="<?= APP_URL ?>/grn/show/<?= $grn->id ?>" target="_blank" class="erp-icon-btn" title="Print / PDF View">
                                    <i class="fa-solid fa-print"></i>
                                </a>

                                <?php if (!$grn->is_approved): ?>
                                    <!-- Edit Draft -->
                                    <a href="<?= APP_URL ?>/grn/edit/<?= $grn->id ?>" class="erp-icon-btn" title="Edit GRN Draft">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <!-- Authorize & Approve (Admin Only) -->
                                    <?php if (strtolower($_SESSION['role'] ?? '') === 'admin'): ?>
                                        <button type="button" class="erp-icon-btn approve" title="Authorize & Approve GRN" onclick="openApprovalModal(<?= $grn->id ?>, '<?= htmlspecialchars($grn->grn_number, ENT_QUOTES) ?>')">
                                            <i class="fa-solid fa-circle-check"></i>
                                        </button>
                                    <?php endif; ?>

                                    <!-- Delete Pending Draft -->
                                    <button type="button" class="erp-icon-btn delete" title="Delete Draft" onclick="openDeleteModal(<?= $grn->id ?>, '<?= htmlspecialchars($grn->grn_number, ENT_QUOTES) ?>', '<?= htmlspecialchars($grn->vendor_name, ENT_QUOTES) ?>')">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pinned Pagination Footer -->
        <div class="erp-pagination" id="pagination-footer">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span>Page <?= (int)($data['page'] ?? 1) ?> of <?= max(1, (int)($data['total_pages'] ?? 1)) ?></span>
                <span style="color: #cbd5e1;">|</span>
                <label style="display: flex; align-items: center; gap: 6px; font-size: 11.5px; color: #64748b;">
                    Show:
                    <select id="perPageSelect" class="erp-select" style="height: 25px; padding: 0 18px 0 6px; max-width: 65px; font-size: 11.5px;" onchange="changePerPage(this.value)">
                        <option value="15" <?= ($data['per_page'] ?? 15) == 15 ? 'selected' : '' ?>>15</option>
                        <option value="25" <?= ($data['per_page'] ?? 15) == 25 ? 'selected' : '' ?>>25</option>
                        <option value="50" <?= ($data['per_page'] ?? 15) == 50 ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= ($data['per_page'] ?? 15) == 100 ? 'selected' : '' ?>>100</option>
                    </select>
                </label>
            </div>

            <div class="erp-page-controls">
                <?php
                    $currentPage = (int)($data['page'] ?? 1);
                    $totalPages = max(1, (int)($data['total_pages'] ?? 1));
                ?>
                <!-- First Page -->
                <button type="button" class="erp-page-btn <?= ($currentPage <= 1) ? 'disabled' : '' ?>" onclick="goToPage(1)" title="First Page">
                    <i class="fa-solid fa-angles-left"></i>
                </button>
                <!-- Prev Page -->
                <button type="button" class="erp-page-btn <?= ($currentPage <= 1) ? 'disabled' : '' ?>" onclick="goToPage(<?= max(1, $currentPage - 1) ?>)" title="Previous Page">
                    <i class="fa-solid fa-angle-left"></i>
                </button>

                <!-- Page Number Buttons (Window of 5) -->
                <?php
                    $startP = max(1, $currentPage - 2);
                    $endP = min($totalPages, $startP + 4);
                    if ($endP - $startP < 4) {
                        $startP = max(1, $endP - 4);
                    }
                    for ($p = $startP; $p <= $endP; $p++):
                ?>
                    <button type="button" class="erp-page-btn <?= ($p === $currentPage) ? 'active' : '' ?>" onclick="goToPage(<?= $p ?>)">
                        <?= $p ?>
                    </button>
                <?php endfor; ?>

                <!-- Next Page -->
                <button type="button" class="erp-page-btn <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>" onclick="goToPage(<?= min($totalPages, $currentPage + 1) ?>)" title="Next Page">
                    <i class="fa-solid fa-angle-right"></i>
                </button>
                <!-- Last Page -->
                <button type="button" class="erp-page-btn <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>" onclick="goToPage(<?= $totalPages ?>)" title="Last Page">
                    <i class="fa-solid fa-angles-right"></i>
                </button>
            </div>
        </div>
    </div>
</form>
</div>

<!-- Floating Bottom Island (Search + Primary Create CTA) -->
<div class="cmd-bar">
    <div class="cmd-search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="searchInput" placeholder="Search GRN, PO, Supplier..." value="<?= htmlspecialchars($data['search'] ?? '') ?>" autocomplete="off">
        <i class="fa-solid fa-xmark cmd-clear-btn" id="searchClearBtn" onclick="clearSearchInput()" title="Clear search"></i>
    </div>
    <div class="cmd-divider"></div>
    <a href="<?= APP_URL ?>/grn/create" class="cmd-cta" title="Create Goods Receipt Note">
        <i class="fa-solid fa-plus"></i>
        <span>+ New GRN</span>
    </a>
</div>

<!-- Modal: Delete GRN Confirmation -->
<div id="deleteModal" class="erp-modal-overlay hidden">
    <div class="erp-modal-card">
        <div class="erp-modal-header">
            <div class="erp-modal-title" style="color: #dc2626;">
                <i class="fa-solid fa-triangle-exclamation"></i> Delete GRN Draft
            </div>
            <button type="button" onclick="closeDeleteModal()" style="background:none; border:none; cursor:pointer; color:#94a3b8; font-size:16px;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="deleteForm" method="POST" action="<?= APP_URL ?>/grn">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '') ?>">
            <input type="hidden" name="action" value="delete_grn">
            <input type="hidden" name="grn_id" id="deleteGrnId" value="">

            <div class="erp-modal-body">
                <p style="margin: 0 0 10px;">Are you sure you want to permanently delete this pending GRN draft?</p>
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 12px;">
                    <div><strong>GRN Number:</strong> <span class="font-mono" id="deleteGrnNumText" style="color:#2563eb;"></span></div>
                    <div style="margin-top: 4px;"><strong>Supplier:</strong> <span id="deleteGrnVendorText"></span></div>
                </div>
                <p style="color: #64748b; font-size: 11.5px; margin: 10px 0 0;">
                    Note: Only unapproved drafts can be deleted. This action cannot be reversed.
                </p>
            </div>
            <div class="erp-modal-footer">
                <button type="button" onclick="closeDeleteModal()" class="erp-btn erp-btn-outline">Cancel</button>
                <button type="submit" class="erp-btn" style="background:#dc2626; color:#ffffff;">
                    <i class="fa-solid fa-trash-can"></i> Delete Permanently
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Admin Approval Password -->
<div id="approvalModal" class="erp-modal-overlay hidden">
    <div class="erp-modal-card">
        <div class="erp-modal-header">
            <div class="erp-modal-title" style="color: #16a34a;">
                <i class="fa-solid fa-circle-check"></i> Authorize & Approve GRN
            </div>
            <button type="button" onclick="closeApprovalModal()" style="background:none; border:none; cursor:pointer; color:#94a3b8; font-size:16px;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="approvalForm" method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '') ?>">
            <input type="hidden" name="action" value="approve_grn">

            <div class="erp-modal-body">
                <p style="margin: 0 0 10px; color: #334155;">
                    Authorizing GRN <strong class="font-mono" id="approvalGrnNumText" style="color:#16a34a;"></strong> will immediately:
                </p>
                <ul style="margin: 0 0 14px 18px; padding: 0; font-size: 12px; color: #475569; line-height: 1.6;">
                    <li>Increment warehouse stock levels for all received line items.</li>
                    <li>Update item FIFO batch tracking records.</li>
                    <li>Post journal entries to the financial ledger (Accounts Payable & Inventory).</li>
                </ul>

                <div>
                    <label style="display:block; margin-bottom:6px; font-size:11px; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:0.04em;">
                        Administrator Password *
                    </label>
                    <input type="password" name="password" id="approvalPassword" class="erp-input" required placeholder="Enter your admin account password" autocomplete="current-password">
                </div>
            </div>
            <div class="erp-modal-footer">
                <button type="button" onclick="closeApprovalModal()" class="erp-btn erp-btn-outline">Cancel</button>
                <button type="submit" class="erp-btn" style="background:#16a34a; color:#ffffff;">
                    <i class="fa-solid fa-check"></i> Confirm & Authorize
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Live Search Debounce
let searchTimer = null;
const searchInput = document.getElementById('searchInput');
const searchClearBtn = document.getElementById('searchClearBtn');

function toggleClearBtn() {
    if (searchInput.value.trim().length > 0) {
        searchClearBtn.style.display = 'block';
    } else {
        searchClearBtn.style.display = 'none';
    }
}
toggleClearBtn();

searchInput.addEventListener('input', function() {
    toggleClearBtn();
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        document.getElementById('currentPageInput').value = 1;
        applyAjaxFilters();
    }, 300);
});

function clearSearchInput() {
    searchInput.value = '';
    toggleClearBtn();
    document.getElementById('currentPageInput').value = 1;
    applyAjaxFilters();
    searchInput.focus();
}

function resetFilters() {
    document.getElementById('vendorInput').value = '';
    document.getElementById('statusInput').value = '';
    document.getElementById('periodInput').value = '';
    document.getElementById('searchInput').value = '';
    document.getElementById('currentPageInput').value = 1;
    toggleClearBtn();
    applyAjaxFilters();
}

function goToPage(page) {
    document.getElementById('currentPageInput').value = page;
    applyAjaxFilters();
}

function changePerPage(val) {
    document.getElementById('perPageInput').value = val;
    document.getElementById('currentPageInput').value = 1;
    applyAjaxFilters();
}

// AJAX Filter Dispatcher
function applyAjaxFilters() {
    const loader = document.getElementById('tableLoadingOverlay');
    if (loader) loader.classList.add('active');

    const form = document.getElementById('filterForm');
    const params = new URLSearchParams(new FormData(form));

    const query = searchInput.value.trim();
    if (query) {
        params.set('search', query);
    } else {
        params.delete('search');
    }

    const targetUrl = '<?= APP_URL ?>/grn?' + params.toString();

    fetch(targetUrl, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        // Update Table Body
        const newBody = doc.getElementById('tableBody');
        const oldBody = document.getElementById('tableBody');
        if (newBody && oldBody) oldBody.innerHTML = newBody.innerHTML;

        // Update Pagination Footer
        const newPagination = doc.getElementById('pagination-footer');
        const oldPagination = document.getElementById('pagination-footer');
        if (newPagination && oldPagination) oldPagination.innerHTML = newPagination.innerHTML;

        // Update Results Count
        const newCount = doc.getElementById('matching-count');
        const oldCount = document.getElementById('matching-count');
        if (newCount && oldCount) oldCount.innerHTML = newCount.innerHTML;

        window.history.pushState({}, '', targetUrl);
    })
    .catch(err => {
        console.error('GRN Filter Error:', err);
    })
    .finally(() => {
        if (loader) loader.classList.remove('active');
    });
}

// Modal Handlers: Delete GRN
function openDeleteModal(grnId, grnNum, vendor) {
    document.getElementById('deleteGrnId').value = grnId;
    document.getElementById('deleteGrnNumText').textContent = grnNum;
    document.getElementById('deleteGrnVendorText').textContent = vendor;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Modal Handlers: Admin Approval
function openApprovalModal(grnId, grnNum) {
    const modal = document.getElementById('approvalModal');
    const form = document.getElementById('approvalForm');
    const numText = document.getElementById('approvalGrnNumText');
    const pwdInput = document.getElementById('approvalPassword');

    form.action = `<?= APP_URL ?>/grn/approve/${grnId}`;
    numText.textContent = grnNum;
    pwdInput.value = '';
    modal.classList.remove('hidden');
    setTimeout(() => pwdInput.focus(), 80);
}

function closeApprovalModal() {
    document.getElementById('approvalModal').classList.add('hidden');
}

// ESC closes modals
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeDeleteModal();
        closeApprovalModal();
    }
});
</script>
