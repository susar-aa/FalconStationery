<?php
// ============================================================
// FALCON STATIONERY ERP — INVENTORY WORKSPACE
// Clean Rounded Modern ERP Interface (Matching Reference UI)
// ============================================================

$items = $data['items'] ?? [];
$variationsByItem = $data['variations_by_item'] ?? [];
$batchesByItem = $data['batches_by_item'] ?? [];

$stats = $data['stats'] ?? (object)[
    'total_items' => count($items),
    'low_stock_count' => 0,
    'out_of_stock_count' => 0
];

$stockValuation = $data['stock_valuation'] ?? (object)[
    'total_cost' => 0.00,
    'total_retail' => 0.00
];

$deletedCount = $data['deleted_count'] ?? 0;
$warehouses = $data['warehouses'] ?? [];
$currentWarehouse = null;
if (!empty($warehouses)) {
    foreach ($warehouses as $wh) {
        if (!empty($wh->is_default)) {
            $currentWarehouse = $wh;
            break;
        }
    }
    if (!$currentWarehouse) {
        $currentWarehouse = $warehouses[0];
    }
}
$currentWarehouseName = $currentWarehouse ? $currentWarehouse->name : 'Kandy Branch (KKK)';

$filters = $data['filters'] ?? [];
$filters['search'] = $filters['search'] ?? '';
$filters['sort'] = $filters['sort'] ?? 'name_asc';
$filters['min_price'] = $filters['min_price'] ?? '';
$filters['max_price'] = $filters['max_price'] ?? '';
$filters['stock_status'] = $filters['stock_status'] ?? '';
$filters['category_id'] = $filters['category_id'] ?? '';
$filters['supplier_id'] = $filters['supplier_id'] ?? '';
$filters['status'] = $filters['status'] ?? '';
$categories = $data['categories'] ?? [];
$vendors = $data['vendors'] ?? [];
$isCurrentlyAdmin = (strtolower($_SESSION['role'] ?? '') === 'admin');

$pagination = $data['pagination'] ?? [
    'current_page' => 1,
    'per_page' => 25,
    'total_items' => count($items),
    'total_pages' => 1
];

$currentPage = (int)$pagination['current_page'];
$perPage = (int)$pagination['per_page'];
$totalItems = (int)$pagination['total_items'];
$totalPages = max(1, (int)$pagination['total_pages']);

$startIndex = $totalItems > 0 ? (($currentPage - 1) * $perPage) + 1 : 0;
$endIndex = min($currentPage * $perPage, $totalItems);

$flashSuccess = $_SESSION['flash_success'] ?? null;
if ($flashSuccess) unset($_SESSION['flash_success']);
$flashError = $_SESSION['flash_error'] ?? null;
if ($flashError) unset($_SESSION['flash_error']);

$csrfToken = $_SESSION['csrf_token'] ?? '';
?>

<!-- FontAwesome Icons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
/* ============================================================
   CLEAN ROUNDED ERP DESIGN SYSTEM (Apple & Modern SaaS Style)
   ============================================================ */

:root {
    --erp-font: 'SF Pro Display', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    --erp-mono: 'SF Mono', ui-monospace, Menlo, Monaco, Consolas, monospace;

    --bg-page: #f8fafc;
    --bg-card: #ffffff;
    --border-subtle: #e2e8f0;
    --border-light: #f1f5f9;

    --text-dark: #0f172a;
    --text-muted: #64748b;
    --text-light: #94a3b8;

    --c-primary: #1d4ed8;
    --c-green: #16a34a;
    --c-green-bg: #ecfdf5;
    --c-green-text: #059669;

    --c-purple-bg: #eff6ff;
    --c-purple-text: #4338ca;

    --c-red-bg: #fef2f2;
    --c-red-text: #dc2626;

    --c-amber-bg: #fffbeb;
    --c-amber-text: #d97706;
}

html, body {
    height: 100vh !important;
    overflow: hidden !important;
    margin: 0;
    padding: 0;
    background: var(--bg-page);
    font-family: var(--erp-font);
    color: var(--text-dark);
}

.inv-workspace-locked {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 46px);
    overflow: hidden;
    padding: 10px 22px 6px;
    box-sizing: border-box;
    gap: 10px;
    position: relative;
}

/* 1. Header Row (Top Pills) */
.inv-top-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    gap: 14px;
}
.inv-top-left {
    display: flex;
    align-items: center;
    gap: 8px;
}
.inv-top-right {
    display: flex;
    align-items: center;
    gap: 8px;
}

.pill-btn-icon {
    width: 32px;
    height: 32px;
    border-radius: 9999px;
    background: #ffffff;
    border: 1px solid var(--border-subtle);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
    text-decoration: none;
    font-size: 13px;
    cursor: pointer;
    box-shadow: 0 1px 2px rgba(0,0,0,0.02);
    transition: all 0.15s;
}
.pill-btn-icon:hover {
    background: #f1f5f9;
    color: var(--text-dark);
}

.pill-btn-static {
    height: 32px;
    padding: 0 14px;
    border-radius: 9999px;
    background: #ffffff;
    border: 1px solid var(--border-subtle);
    display: inline-flex;
    align-items: center;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-dark);
}

.pill-btn {
    height: 32px;
    padding: 0 14px;
    border-radius: 9999px;
    background: #ffffff;
    border: 1px solid var(--border-subtle);
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    font-weight: 600;
    color: #334155;
    text-decoration: none;
    cursor: pointer;
    box-shadow: 0 1px 2px rgba(0,0,0,0.02);
    transition: all 0.15s ease;
}
.pill-btn:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: var(--text-dark);
}

.pill-btn-danger {
    height: 32px;
    padding: 0 14px;
    border-radius: 9999px;
    background: #fef2f2;
    border: 1px solid #fee2e2;
    color: #ef4444;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.15s;
}
.pill-btn-danger:hover {
    background: #fee2e2;
    color: #dc2626;
}

.pill-btn-dark {
    height: 32px;
    padding: 0 18px;
    border-radius: 9999px;
    background: #0f172a;
    border: none;
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(15,23,42,0.12);
    transition: all 0.15s;
}
.pill-btn-dark:hover {
    background: #1e293b;
    transform: translateY(-1px);
}

/* 2. Sub-Header: Title & Valuation Strip */
.inv-sub-header {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
    font-size: 13px;
    color: var(--text-muted);
    padding: 0 2px;
}
.inv-sub-title {
    font-size: 16px;
    font-weight: 800;
    color: var(--text-dark);
    display: flex;
    align-items: center;
    gap: 6px;
}
.inv-sub-title .bullet {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #0f172a;
    display: inline-block;
}
.inv-valuation-label {
    color: var(--text-muted);
    font-size: 12.5px;
}
.inv-val-cost {
    font-family: var(--erp-mono);
    font-weight: 700;
    color: var(--text-dark);
}
.inv-val-retail {
    font-family: var(--erp-mono);
    font-weight: 700;
    color: var(--c-green);
}

/* 3. Filter Shelf (Clean Rounded Horizontal Pills) */
.inv-filter-shelf {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    flex-shrink: 0;
    padding: 2px 0;
}
.inv-filter-group {
    display: flex;
    align-items: center;
    gap: 6px;
}
.pill-label {
    font-size: 10.5px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    white-space: nowrap;
}
.pill-select {
    height: 29px;
    border-radius: 9999px;
    background: #ffffff;
    border: 1px solid var(--border-subtle);
    padding: 0 26px 0 12px;
    font-size: 12px;
    font-weight: 500;
    color: #1e293b;
    outline: none;
    font-family: var(--erp-font);
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 9px center;
    background-size: 10px;
    transition: border-color 0.15s, box-shadow 0.15s;
    box-shadow: 0 1px 2px rgba(0,0,0,0.02);
}
.pill-select:hover {
    border-color: #cbd5e1;
}
.pill-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.12);
}

/* 4. Main Table Card */
.inv-table-card {
    flex: 1;
    min-height: 0;
    background: #ffffff;
    border: 1px solid var(--border-subtle);
    border-radius: 18px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    position: relative;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}

.inv-table-scroll {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: auto;
    padding-bottom: 52px; /* Space for floating command island */
}

.inv-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12.5px;
    text-align: left;
}

.inv-table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    background: #ffffff;
    color: #64748b;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    padding: 12px 14px;
    border-bottom: 1px solid #e2e8f0;
    white-space: nowrap;
}

.inv-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.1s;
}
.inv-table tbody tr:hover {
    background: #f8fafc !important;
}
/* Alternating Zebra Striping */
.inv-table tbody tr:nth-child(even) {
    background: #fcfdfe;
}

.inv-table td {
    padding: 11px 14px;
    vertical-align: middle;
    color: #1e293b;
}

.font-mono {
    font-family: var(--erp-mono);
}
.font-bold {
    font-weight: 700;
}
.text-right {
    text-align: right;
}
.text-center {
    text-align: center;
}
.text-muted {
    color: var(--text-muted);
}
.text-red {
    color: #ef4444;
}
.text-slate {
    color: #0f172a;
}

/* Custom Rounded Checkbox */
.custom-checkbox {
    width: 16px;
    height: 16px;
    border-radius: 4px;
    border: 1.5px solid #cbd5e1;
    cursor: pointer;
    vertical-align: middle;
    accent-color: #1d4ed8;
}

/* Category & Supplier Pastel Badges */
.badge-cat {
    display: inline-flex;
    align-items: center;
    background: #e6f9f0;
    color: #059669;
    font-weight: 700;
    font-size: 10.5px;
    letter-spacing: 0.02em;
    padding: 3px 8px;
    border-radius: 6px;
    white-space: nowrap;
    text-transform: uppercase;
}
.badge-sup {
    display: inline-flex;
    align-items: center;
    background: #eef2ff;
    color: #4f46e5;
    font-weight: 700;
    font-size: 10.5px;
    letter-spacing: 0.02em;
    padding: 3px 8px;
    border-radius: 6px;
    white-space: nowrap;
    text-transform: uppercase;
}

/* Stock Alert Badges */
.pill-alert {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 9999px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}
.pill-alert.out {
    background: #fef2f2;
    color: #dc2626;
}
.pill-alert.low {
    background: #fffbeb;
    color: #d97706;
}
.pill-alert.in {
    background: #f0fdf4;
    color: #16a34a;
}

/* Status Badges */
.pill-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 9999px;
    font-size: 11.5px;
    font-weight: 600;
    white-space: nowrap;
}
.pill-status.active {
    background: #ecfdf5;
    color: #059669;
}
.pill-status.active .dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #10b981;
}
.pill-status.inactive {
    background: #f1f5f9;
    color: #64748b;
}
.pill-status.inactive .dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #94a3b8;
}

/* Lots Pill Button */
.pill-lots {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    border-radius: 9999px;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 10px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.15s;
}
.pill-lots.blue {
    background: #1d4ed8;
    color: #ffffff;
}
.pill-lots.blue:hover {
    background: #1e40af;
}
.pill-lots.grey {
    background: #ffffff;
    border: 1px solid var(--border-subtle);
    color: #64748b;
    cursor: default;
}

/* Chevron expand button next to product name */
.btn-expand-chevron {
    width: 20px;
    height: 20px;
    border-radius: 4px;
    background: #1d4ed8;
    color: #ffffff;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 9px;
    cursor: pointer;
    margin-right: 6px;
    vertical-align: middle;
    transition: transform 0.15s;
}
.btn-expand-chevron.collapsed {
    background: #f1f5f9;
    color: #64748b;
}

/* Action Dots Button & Dropdown */
.erp-dropdown {
    position: relative;
    display: inline-block;
}
.btn-action-dots {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    background: #ffffff;
    border: 1px solid var(--border-subtle);
    color: #64748b;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.15s;
}
.btn-action-dots:hover {
    background: #f8fafc;
    color: #0f172a;
    border-color: #cbd5e1;
}
.erp-dropdown-menu {
    position: absolute;
    right: 0;
    top: 32px;
    background: #ffffff;
    border: 1px solid var(--border-subtle);
    border-radius: 10px;
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.05);
    min-width: 160px;
    z-index: 50;
    padding: 5px;
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.erp-dropdown-menu.hidden {
    display: none !important;
}
.erp-dropdown-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 7px 10px;
    font-size: 12px;
    color: #334155;
    text-decoration: none;
    border-radius: 6px;
    background: none;
    border: none;
    text-align: left;
    width: 100%;
    cursor: pointer;
    transition: background 0.12s;
}
.erp-dropdown-item:hover {
    background: #f1f5f9;
    color: #0f172a;
}
.erp-dropdown-item.text-danger {
    color: #dc2626;
}
.erp-dropdown-item.text-danger:hover {
    background: #fef2f2;
}

/* Child Lots / Variations Row */
.child-lot-row {
    background: #f8fbfe !important;
    border-bottom: 1px dashed #e2e8f0 !important;
}
.child-lot-row.hidden {
    display: none !important;
}
.lot-accent-bar {
    width: 3px;
    height: 100%;
    background: #0284c7;
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
}
.lot-tree-icon {
    color: #0284c7;
    font-weight: 700;
    font-size: 13px;
    margin-right: 6px;
}
.badge-lot-active {
    display: inline-flex;
    align-items: center;
    background: #ecfdf5;
    color: #059669;
    font-weight: 600;
    font-size: 10.5px;
    padding: 2px 8px;
    border-radius: 9999px;
}

/* 5. Card Footer */
.inv-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 18px;
    background: #ffffff;
    border-top: 1px solid var(--border-subtle);
    flex-shrink: 0;
    font-size: 12px;
    color: var(--text-muted);
}
.inv-card-footer-left {
    display: flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}
.inv-card-footer-right {
    display: flex;
    align-items: center;
    gap: 8px;
}
.pill-page-btn {
    height: 28px;
    padding: 0 12px;
    border-radius: 9999px;
    background: #ffffff;
    border: 1px solid var(--border-subtle);
    color: #334155;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.12s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.pill-page-btn:hover:not(.disabled) {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: #0f172a;
}
.pill-page-btn.disabled {
    opacity: 0.45;
    cursor: not-allowed;
    pointer-events: none;
}
.page-indicator {
    font-size: 12.5px;
    font-weight: 700;
    color: #0f172a;
    padding: 0 4px;
}

/* 6. Floating Bottom Command Island */
.cmd-bar {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(15, 23, 42, 0.94);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 9999px;
    padding: 5px 8px 5px 14px;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
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
    font-family: var(--erp-font);
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
    padding: 6px 16px;
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

/* Modals */
.erp-modal-backdrop {
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
.erp-modal-backdrop.hidden {
    display: none !important;
}
.erp-modal-box {
    background: #ffffff;
    border: 1px solid var(--border-subtle);
    border-radius: 14px;
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);
    width: 100%;
    max-width: 440px;
    overflow: hidden;
    animation: modalScaleIn 0.18s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes modalScaleIn {
    from { opacity: 0; transform: scale(0.96) translateY(6px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
.erp-modal-head {
    padding: 14px 18px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.erp-modal-head h3 {
    margin: 0;
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
}
.erp-drawer-close {
    background: none;
    border: none;
    cursor: pointer;
    color: #94a3b8;
    font-size: 16px;
}
.erp-modal-body {
    padding: 16px 18px;
    font-size: 13px;
    color: #334155;
    line-height: 1.5;
}
.erp-modal-foot {
    padding: 12px 18px;
    background: #f8fafc;
    border-top: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
}
.erp-input-sm {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid var(--border-subtle);
    border-radius: 6px;
    font-size: 13px;
    color: #0f172a;
    font-family: inherit;
    outline: none;
    box-sizing: border-box;
}

/* Spinner */
.erp-spinner-overlay {
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
.erp-spinner-overlay.active {
    opacity: 1;
    pointer-events: auto;
}
</style>

<div class="inv-workspace-locked">
<form id="filterForm" action="<?= APP_URL ?>/inventory" method="GET" onsubmit="event.preventDefault(); applyAjaxFilters();">
    <input type="hidden" name="page" id="currentPageInput" value="<?= $currentPage ?>">
    <input type="hidden" name="per_page" id="perPageInput" value="<?= $perPage ?>">

    <!-- 1. Top Bar (Pills Cluster) -->
    <div class="inv-top-bar">
        <!-- Left: Navigation & Warehouse -->
        <div class="inv-top-left">
            <a href="javascript:history.back()" class="pill-btn-icon" title="Go Back">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <span class="pill-btn-static">Inventory</span>
            <div class="pill-dropdown-wrap">
                <button type="button" class="pill-btn" title="Current Active Branch / Warehouse">
                    <i class="fa-solid fa-building" style="color: #64748b;"></i>
                    <span><?= htmlspecialchars($currentWarehouseName) ?></span>
                    <i class="fa-solid fa-chevron-down" style="font-size: 10px; margin-left: 2px; color: #94a3b8;"></i>
                </button>
            </div>
        </div>

        <!-- Right: Actions Cluster -->
        <div class="inv-top-right">
            <a href="<?= APP_URL ?>/inventory/deleted" class="pill-btn-danger" title="View Deleted / Trashed Products">
                <i class="fa-solid fa-trash-can"></i>
                <span>Trash Bin (<?= (int)$deletedCount ?>)</span>
            </a>
            <button type="button" onclick="openCsvModal()" class="pill-btn" title="Import Products via CSV">
                <i class="fa-solid fa-file-import" style="color: #64748b;"></i>
                <span>Import CSV</span>
            </button>
            <a href="<?= APP_URL ?>/inventory/exportCSV" class="pill-btn" title="Export Products to CSV">
                <i class="fa-solid fa-file-export" style="color: #64748b;"></i>
                <span>Export CSV</span>
            </a>
            <a href="<?= APP_URL ?>/item/create" class="pill-btn-dark" title="Create New Product">
                <i class="fa-solid fa-plus"></i>
                <span>+ Add Product</span>
            </a>
        </div>
    </div>

    <!-- 2. Sub-Header: Title & Valuation Strip -->
    <div class="inv-sub-header">
        <span class="inv-sub-title">
            <span class="bullet"></span> Inventory
        </span>
        <span style="color: #cbd5e1; margin: 0 4px;">|</span>
        <span class="inv-valuation-label">Valuation:</span>
        <span class="inv-val-cost">LKR <?= number_format($stockValuation->total_cost, 2) ?></span>
        <span style="color: #64748b; font-size: 11.5px;">(Cost)</span>
        <span style="color: #94a3b8; margin: 0 2px;">•</span>
        <span class="inv-val-retail">LKR <?= number_format($stockValuation->total_retail, 2) ?></span>
        <span style="color: #64748b; font-size: 11.5px;">(Retail)</span>
    </div>

    <!-- 3. Filter Shelf (Clean Rounded Horizontal Pills) -->
    <div class="inv-filter-shelf">
        <!-- Sort -->
        <div class="inv-filter-group">
            <span class="pill-label">SORT:</span>
            <select name="sort" id="sortInput" class="pill-select" onchange="applyAjaxFilters()">
                <option value="name_asc"  <?= ($filters['sort'] ?? '') === 'name_asc'  ? 'selected' : '' ?>>A-Z (Product Name)</option>
                <option value="name_desc" <?= ($filters['sort'] ?? '') === 'name_desc' ? 'selected' : '' ?>>Z-A (Product Name)</option>
                <option value="price_asc" <?= ($filters['sort'] ?? '') === 'price_asc' ? 'selected' : '' ?>>Price (Low to High)</option>
                <option value="price_desc"<?= ($filters['sort'] ?? '') === 'price_desc'? 'selected' : '' ?>>Price (High to Low)</option>
                <option value="stock_asc" <?= ($filters['sort'] ?? '') === 'stock_asc' ? 'selected' : '' ?>>Stock (Low to High)</option>
                <option value="stock_desc"<?= ($filters['sort'] ?? '') === 'stock_desc'? 'selected' : '' ?>>Stock (High to Low)</option>
            </select>
        </div>

        <!-- Stock Status -->
        <div class="inv-filter-group">
            <span class="pill-label">STOCK:</span>
            <select name="stock_status" id="stockStatusInput" class="pill-select" onchange="applyAjaxFilters()">
                <option value="" <?= ($filters['stock_status'] ?? '') === '' ? 'selected' : '' ?>>All Stock Statuses</option>
                <option value="instock"  <?= ($filters['stock_status'] ?? '') === 'instock'  ? 'selected' : '' ?>>In Stock</option>
                <option value="lowstock" <?= ($filters['stock_status'] ?? '') === 'lowstock' ? 'selected' : '' ?>>Low Stock</option>
                <option value="outstock" <?= ($filters['stock_status'] ?? '') === 'outstock' ? 'selected' : '' ?>>Out of Stock</option>
            </select>
        </div>

        <!-- Status -->
        <div class="inv-filter-group">
            <span class="pill-label">STATUS:</span>
            <select name="status" id="itemStatusInput" class="pill-select" onchange="applyAjaxFilters()">
                <option value="" <?= ($filters['status'] ?? '') === '' ? 'selected' : '' ?>>All Statuses</option>
                <option value="active"   <?= ($filters['status'] ?? '') === 'active'   ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>

        <!-- Category -->
        <div class="inv-filter-group">
            <span class="pill-label">CATEGORY:</span>
            <select name="category_id" id="categoryInput" class="pill-select" onchange="applyAjaxFilters()">
                <option value="">All Categories</option>
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat->id ?>" <?= (string)($filters['category_id'] ?? '') === (string)$cat->id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat->name) ?>
                    </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <!-- Supplier -->
        <div class="inv-filter-group">
            <span class="pill-label">SUPPLIER:</span>
            <select name="supplier_id" id="supplierInput" class="pill-select" onchange="applyAjaxFilters()">
                <option value="">All Suppliers</option>
                <?php if (!empty($vendors)): ?>
                    <?php foreach ($vendors as $ven): ?>
                    <option value="<?= $ven->id ?>" <?= (string)($filters['supplier_id'] ?? '') === (string)$ven->id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ven->name) ?>
                    </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <!-- Reset Button -->
        <button type="button" onclick="clearAllFilters()" class="pill-btn" style="height: 29px; padding: 0 12px; font-size: 11.5px;" title="Reset All Filters">
            <i class="fa-solid fa-arrow-rotate-left" style="font-size: 11px;"></i>
            <span>Reset</span>
        </button>
    </div>

    <!-- 4. Table Card (Rounded Container with Viewport-Locked Scrolling) -->
    <div class="inv-table-card">
        <div id="table-loader" class="erp-spinner-overlay">
            <i class="fa-solid fa-spinner fa-spin" style="font-size: 26px; color: #1d4ed8;"></i>
        </div>

        <div id="table-container" class="inv-table-scroll">
            <table class="inv-table">
                <thead>
                    <tr>
                        <th style="width: 38px; text-align: center;">
                            <input type="checkbox" id="selectAllCheckbox" class="custom-checkbox" onchange="toggleSelectAll(this)">
                        </th>
                        <th style="width: 110px;">BARCODE</th>
                        <th style="min-width: 200px;">PRODUCT NAME</th>
                        <th style="width: 100px;">BRAND</th>
                        <th style="width: 110px;">CATEGORY</th>
                        <th style="width: 110px;">SUPPLIER</th>
                        <th style="width: 110px; text-align: right;">COST (LKR)</th>
                        <th style="width: 125px; text-align: right;">SELLING PRICE (LKR)</th>
                        <th style="width: 80px; text-align: right;">STOCK</th>
                        <th style="width: 115px; text-align: center;">STOCK ALERT</th>
                        <th style="width: 95px; text-align: center;">STATUS</th>
                        <th style="width: 110px; text-align: right;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="12" style="text-align: center; padding: 48px 16px; color: #94a3b8; font-style: italic;">
                            <i class="fa-solid fa-box-open" style="font-size: 28px; display: block; margin-bottom: 8px; color: #cbd5e1;"></i>
                            No products matching your filters.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <?php
                            $price = (float)($item->selling_price ?? $item->price ?? 0);
                            $cost = (float)($item->cost_price ?? 0);
                            $qty = (float)($item->qty ?? $item->quantity_on_hand ?? 0);
                            $sku = $item->item_code ?? $item->sku ?? 'NO-SKU';
                            $itemStatus = strtolower($item->status ?? 'active');

                            // Variations & Lots
                            $vars = $variationsByItem[$item->id] ?? [];
                            $batches = $batchesByItem[$item->id] ?? [];
                            $totalLots = count($vars) > 0 ? count($vars) : count($batches);
                            $hasLots = $totalLots > 0;
                        ?>
                        <tr id="row_item_<?= $item->id ?>">
                            <!-- Checkbox -->
                            <td class="text-center">
                                <input type="checkbox" name="selected_items[]" value="<?= $item->id ?>" class="custom-checkbox item-select-checkbox" onchange="updateSelection()">
                            </td>

                            <!-- Barcode -->
                            <td class="font-mono font-bold" style="color: #0f172a; font-size: 12px; white-space: nowrap;">
                                <?= htmlspecialchars($sku) ?>
                            </td>

                            <!-- Product Name (with expandable chevron if lots exist) -->
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <?php if ($hasLots): ?>
                                        <button type="button" class="btn-expand-chevron" id="btn-expand-<?= $item->id ?>" onclick="toggleLotRow(<?= $item->id ?>)" title="Expand / Collapse Lots">
                                            <i class="fa-solid fa-chevron-up"></i>
                                        </button>
                                    <?php endif; ?>
                                    <span class="font-bold" style="color: #0f172a; font-size: 13px;">
                                        <?= htmlspecialchars($item->name ?? 'Unnamed Product') ?>
                                    </span>
                                </div>
                            </td>

                            <!-- Brand -->
                            <td class="font-bold" style="color: #475569; font-size: 11.5px; text-transform: uppercase;">
                                <?= htmlspecialchars(!empty($item->brand) ? $item->brand : 'DEFAULT') ?>
                            </td>

                            <!-- Category -->
                            <td>
                                <span class="badge-cat">
                                    <?= htmlspecialchars($item->category_name ?? 'DEFAULT') ?>
                                </span>
                            </td>

                            <!-- Supplier -->
                            <td>
                                <span class="badge-sup">
                                    <?= htmlspecialchars($item->supplier_name ?? 'DEFAULT') ?>
                                </span>
                            </td>

                            <!-- Cost (LKR) -->
                            <td class="text-right font-mono font-bold" style="color: #1e293b;">
                                <?= number_format($cost, 2) ?>
                            </td>

                            <!-- Selling Price (LKR) -->
                            <td class="text-right font-mono font-bold" style="color: #0f172a;">
                                <?= number_format($price, 2) ?>
                            </td>

                            <!-- Stock Qty -->
                            <td class="text-right font-mono font-bold <?= $qty <= 0 ? 'text-red' : 'text-slate' ?>">
                                <?= number_format($qty, 2) ?>
                            </td>

                            <!-- Stock Alert -->
                            <td class="text-center">
                                <?php if ($qty <= 0): ?>
                                    <span class="pill-alert out">Out of Stock</span>
                                <?php elseif ($qty <= 5): ?>
                                    <span class="pill-alert low">Low Stock</span>
                                <?php else: ?>
                                    <span class="pill-alert in">In Stock</span>
                                <?php endif; ?>
                            </td>

                            <!-- Status -->
                            <td class="text-center">
                                <?php if ($itemStatus === 'active'): ?>
                                    <span class="pill-status active"><span class="dot"></span>Active</span>
                                <?php else: ?>
                                    <span class="pill-status inactive"><span class="dot"></span>Inactive</span>
                                <?php endif; ?>
                            </td>

                            <!-- Actions -->
                            <td class="text-right" style="white-space: nowrap;">
                                <div style="display: inline-flex; align-items: center; gap: 6px;">
                                    <!-- Lots Badge Button -->
                                    <?php if ($hasLots): ?>
                                        <button type="button" class="pill-lots blue" id="btn-lot-<?= $item->id ?>" onclick="toggleLotRow(<?= $item->id ?>)" title="Toggle Lots">
                                            <span><?= $totalLots ?> Lots</span>
                                            <i class="fa-solid fa-chevron-up"></i>
                                        </button>
                                    <?php else: ?>
                                        <span class="pill-lots grey">0 Lots →</span>
                                    <?php endif; ?>

                                    <!-- Dots Menu -->
                                    <div class="erp-dropdown">
                                        <button type="button" class="btn-action-dots" onclick="toggleActionDropdown(<?= $item->id ?>, event)" title="Actions">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>
                                        <div class="erp-dropdown-menu hidden" id="dropdown-menu-<?= $item->id ?>">
                                            <a href="<?= APP_URL ?>/inventory/edit/<?= $item->id ?>" class="erp-dropdown-item">
                                                <i class="fa-solid fa-pen-to-square"></i> Edit Product
                                            </a>
                                            <a href="<?= APP_URL ?>/stock_adjustments/create?item_id=<?= $item->id ?>" class="erp-dropdown-item">
                                                <i class="fa-solid fa-sliders"></i> Stock Adjustment
                                            </a>
                                            <?php if ($isCurrentlyAdmin): ?>
                                                <button type="button" class="erp-dropdown-item text-danger" onclick="openDeleteModal(<?= $item->id ?>, '<?= htmlspecialchars(addslashes($item->name)) ?>')">
                                                    <i class="fa-solid fa-trash-can"></i> Delete Product
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <!-- Child Lots / Variations Rows (Expanded by Default) -->
                        <?php if ($hasLots): ?>
                            <?php if (!empty($vars)): ?>
                                <?php foreach ($vars as $vIndex => $v): ?>
                                <tr class="child-lot-row lot-row-<?= $item->id ?>" style="position: relative;">
                                    <td style="position: relative; text-align: center;">
                                        <div class="lot-accent-bar"></div>
                                        <span class="lot-tree-icon">↳</span>
                                    </td>
                                    <td class="font-mono text-muted" style="font-size: 11px;">—</td>
                                    <td colspan="4">
                                        <span class="font-mono font-bold" style="color: #0f172a; font-size: 12px;">
                                            ↳ Lot #<?= htmlspecialchars($v->sku ?? "VAR-{$v->id}") ?>
                                        </span>
                                        <span style="font-size: 11px; color: #64748b; margin-left: 6px;">
                                            (<?= htmlspecialchars($v->value_name ?? 'Option') ?>)
                                        </span>
                                    </td>
                                    <td class="text-right font-mono font-bold" style="color: #1e293b;">
                                        <?= number_format(floatval($v->cost ?? $cost), 2) ?>
                                    </td>
                                    <td class="text-right font-mono font-bold" style="color: #0284c7;">
                                        <?= number_format(floatval($v->price ?? $price), 2) ?>
                                    </td>
                                    <td class="text-right font-mono font-bold" style="color: #0f172a;">
                                        <?= number_format(floatval($v->quantity_on_hand ?? 0), 2) ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge-lot-active">Lot Active</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="pill-status active"><span class="dot"></span>Active</span>
                                    </td>
                                    <td></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php elseif (!empty($batches)): ?>
                                <?php foreach ($batches as $bIndex => $b): ?>
                                <tr class="child-lot-row lot-row-<?= $item->id ?>" style="position: relative;">
                                    <td style="position: relative; text-align: center;">
                                        <div class="lot-accent-bar"></div>
                                        <span class="lot-tree-icon">↳</span>
                                    </td>
                                    <td class="font-mono text-muted" style="font-size: 11px;">—</td>
                                    <td colspan="4">
                                        <span class="font-mono font-bold" style="color: #0f172a; font-size: 12px;">
                                            ↳ Lot #<?= !empty($b->batch_code) ? htmlspecialchars($b->batch_code) : ("GRN" . htmlspecialchars($b->grn_number ?? $b->id)) ?>
                                        </span>
                                    </td>
                                    <td class="text-right font-mono font-bold" style="color: #1e293b;">
                                        <?= number_format(floatval($b->unit_cost ?? $cost), 2) ?>
                                    </td>
                                    <td class="text-right font-mono font-bold" style="color: #0284c7;">
                                        <?= number_format($price, 2) ?>
                                    </td>
                                    <td class="text-right font-mono font-bold" style="color: #0f172a;">
                                        <?= number_format(floatval($b->quantity_remaining ?? 0), 2) ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge-lot-active">Lot Active</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="pill-status active"><span class="dot"></span>Active</span>
                                    </td>
                                    <td></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endif; ?>

                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- 5. Card Footer: Valuation & Pinned Pagination -->
        <div class="inv-card-footer" id="pagination-footer">
            <div class="inv-card-footer-left">
                <span>Showing <?= $startIndex ?> to <?= $endIndex ?> of <?= $totalItems ?> entries</span>
                <span style="color: #cbd5e1; margin: 0 4px;">•</span>
                <span>Total Stock Valuation:</span>
                <span class="inv-val-cost">LKR <?= number_format($stockValuation->total_cost, 2) ?></span>
                <span style="color: #64748b; font-size: 11.5px;">(Cost)</span>
                <span style="color: #94a3b8; margin: 0 2px;">•</span>
                <span class="inv-val-retail">LKR <?= number_format($stockValuation->total_retail, 2) ?></span>
                <span style="color: #64748b; font-size: 11.5px;">(Retail)</span>
            </div>

            <div class="inv-card-footer-right">
                <span style="font-size: 11.5px; color: #64748b;">Per Page:</span>
                <select id="perPageSelect" class="pill-select" style="height: 27px; padding: 0 20px 0 8px; font-size: 11.5px;" onchange="changePerPage(this.value)">
                    <option value="15"  <?= $perPage == 15  ? 'selected' : '' ?>>15</option>
                    <option value="25"  <?= $perPage == 25  ? 'selected' : '' ?>>25</option>
                    <option value="50"  <?= $perPage == 50  ? 'selected' : '' ?>>50</option>
                    <option value="100" <?= $perPage == 100 ? 'selected' : '' ?>>100</option>
                </select>

                <button type="button" class="pill-page-btn <?= ($currentPage <= 1) ? 'disabled' : '' ?>" onclick="goToPage(<?= max(1, $currentPage - 1) ?>)" title="Previous Page">
                    ← Prev
                </button>

                <span class="page-indicator"><?= $currentPage ?>/<?= $totalPages ?></span>

                <button type="button" class="pill-page-btn <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>" onclick="goToPage(<?= min($totalPages, $currentPage + 1) ?>)" title="Next Page">
                    Next →
                </button>
            </div>
        </div>
    </div>
</form>
</div>

<!-- 6. Floating Bottom Command Island (Standard Search + Direct CTA) -->
<div class="cmd-bar">
    <div class="cmd-search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="searchInput" placeholder="Search catalog by name, SKU, code..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>" autocomplete="off">
        <i class="fa-solid fa-xmark cmd-clear-btn" id="searchClearBtn" onclick="clearSearchInput()" title="Clear search"></i>
    </div>
    <div class="cmd-divider"></div>
    <a href="<?= APP_URL ?>/item/create" class="cmd-cta" title="Add New Product">
        <i class="fa-solid fa-plus"></i>
        <span>+ Add Product</span>
    </a>
</div>

<!-- Modal 1: CSV Import Modal -->
<div id="csvImportModal" class="erp-modal-backdrop hidden">
    <div class="erp-modal-box">
        <div class="erp-modal-head">
            <h3><i class="fa-solid fa-file-csv" style="color: #1d4ed8; margin-right: 6px;"></i> Import Products via CSV</h3>
            <button type="button" onclick="closeCsvModal()" class="erp-drawer-close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="csvImportForm" action="<?= APP_URL ?>/inventory/importERPCSV" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <div class="erp-modal-body">
                <div style="border: 2px dashed #cbd5e1; border-radius: 8px; padding: 24px; text-align: center; background: #f8fafc;">
                    <input type="file" name="csv_file" id="csvFileInput" accept=".csv" required>
                    <div style="font-size: 12px; color: #64748b; margin-top: 8px;">Upload .csv file with columns: Item Code, Name, Selling Price, Cost, Qty, Category</div>
                </div>
            </div>
            <div class="erp-modal-foot">
                <button type="button" onclick="closeCsvModal()" class="pill-btn">Cancel</button>
                <button type="submit" class="pill-btn-dark"><i class="fa-solid fa-upload"></i> Upload & Import</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Delete Product Modal with Password Confirmation -->
<div id="deleteProductModal" class="erp-modal-backdrop hidden">
    <div class="erp-modal-box">
        <div class="erp-modal-head">
            <h3 style="color: #dc2626; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-triangle-exclamation"></i> Delete Product
            </h3>
            <button type="button" onclick="closeDeleteModal()" class="erp-drawer-close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="deleteProductForm" onsubmit="submitDeleteProduct(event)">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" id="deleteItemId" name="item_id">
            <div class="erp-modal-body">
                <p style="font-size: 13.5px; margin: 0 0 14px 0; color: #0f172a;">
                    Are you sure you want to delete <strong id="deleteItemName" style="color: #0f172a;"></strong>?
                </p>
                <div id="deleteErrorContainer" style="display: none; background: #fef2f2; color: #dc2626; padding: 8px 12px; border-radius: 6px; font-size: 12px; margin-bottom: 12px;"></div>
                <label style="font-size: 12px; font-weight: 700; display: block; margin-bottom: 6px; color: #475569; text-transform: uppercase;">
                    Confirm with Account Password:
                </label>
                <input type="password" name="password" id="deleteAdminPassword" class="erp-input-sm" placeholder="Enter password" required autocomplete="current-password">
            </div>
            <div class="erp-modal-foot">
                <button type="button" onclick="closeDeleteModal()" class="pill-btn">Cancel</button>
                <button type="submit" id="deleteSubmitBtn" class="pill-btn" style="background: #dc2626; border-color: #dc2626; color: #ffffff;">
                    <i id="deleteBtnSpinner" class="fa-solid fa-spinner fa-spin" style="display: none; margin-right: 4px;"></i> Delete
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Search Debounce
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
    }, 280);
});

function clearSearchInput() {
    searchInput.value = '';
    toggleClearBtn();
    document.getElementById('currentPageInput').value = 1;
    applyAjaxFilters();
    searchInput.focus();
}

function clearAllFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('categoryInput').value = '';
    document.getElementById('supplierInput').value = '';
    document.getElementById('stockStatusInput').value = '';
    document.getElementById('itemStatusInput').value = '';
    document.getElementById('sortInput').value = 'name_asc';
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
    const loader = document.getElementById('table-loader');
    if (loader) loader.classList.add('active');

    const form = document.getElementById('filterForm');
    const params = new URLSearchParams(new FormData(form));

    const query = searchInput.value.trim();
    if (query) {
        params.set('search', query);
    } else {
        params.delete('search');
    }

    const targetUrl = form.getAttribute('action') + '?' + params.toString();

    fetch(targetUrl, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => r.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        const newTable = doc.getElementById('table-container');
        const oldTable = document.getElementById('table-container');
        if (newTable && oldTable) oldTable.innerHTML = newTable.innerHTML;

        const newPg = doc.getElementById('pagination-footer');
        const oldPg = document.getElementById('pagination-footer');
        if (newPg && oldPg) oldPg.innerHTML = newPg.innerHTML;

        window.history.pushState({}, '', targetUrl);
    })
    .catch(err => console.error('Filter Error:', err))
    .finally(() => {
        if (loader) loader.classList.remove('active');
    });
}

// Expand / Collapse Lots and Variations
function toggleLotRow(itemId) {
    const lotRows = document.querySelectorAll('.lot-row-' + itemId);
    const expandBtn = document.getElementById('btn-expand-' + itemId);
    const lotBtn = document.getElementById('btn-lot-' + itemId);

    let isExpanded = false;
    lotRows.forEach(row => {
        if (!row.classList.contains('hidden')) {
            isExpanded = true;
        }
    });

    lotRows.forEach(row => {
        if (isExpanded) {
            row.classList.add('hidden');
        } else {
            row.classList.remove('hidden');
        }
    });

    if (isExpanded) {
        if (expandBtn) {
            expandBtn.classList.add('collapsed');
            expandBtn.innerHTML = '<i class="fa-solid fa-chevron-down"></i>';
        }
        if (lotBtn) {
            const countText = lotBtn.querySelector('span') ? lotBtn.querySelector('span').innerText : '';
            lotBtn.innerHTML = `<span>${countText}</span> <i class="fa-solid fa-chevron-down"></i>`;
        }
    } else {
        if (expandBtn) {
            expandBtn.classList.remove('collapsed');
            expandBtn.innerHTML = '<i class="fa-solid fa-chevron-up"></i>';
        }
        if (lotBtn) {
            const countText = lotBtn.querySelector('span') ? lotBtn.querySelector('span').innerText : '';
            lotBtn.innerHTML = `<span>${countText}</span> <i class="fa-solid fa-chevron-up"></i>`;
        }
    }
}

// Actions Dropdown Menu
let currentOpenDropdown = null;
function toggleActionDropdown(itemId, e) {
    e.stopPropagation();
    const menu = document.getElementById('dropdown-menu-' + itemId);
    if (currentOpenDropdown && currentOpenDropdown !== menu) {
        currentOpenDropdown.classList.add('hidden');
    }
    if (menu) {
        menu.classList.toggle('hidden');
        currentOpenDropdown = menu.classList.contains('hidden') ? null : menu;
    }
}

document.addEventListener('click', () => {
    if (currentOpenDropdown) {
        currentOpenDropdown.classList.add('hidden');
        currentOpenDropdown = null;
    }
});

// Select All Checkbox
function toggleSelectAll(master) {
    const checkboxes = document.querySelectorAll('.item-select-checkbox');
    checkboxes.forEach(cb => cb.checked = master.checked);
}
function updateSelection() {
    const checkboxes = document.querySelectorAll('.item-select-checkbox');
    const master = document.getElementById('selectAllCheckbox');
    if (master && checkboxes.length > 0) {
        master.checked = Array.from(checkboxes).every(cb => cb.checked);
    }
}

// Modals: CSV Import
function openCsvModal() {
    document.getElementById('csvImportModal').classList.remove('hidden');
}
function closeCsvModal() {
    document.getElementById('csvImportModal').classList.add('hidden');
}

// Modals: Delete Product
function openDeleteModal(id, name) {
    document.getElementById('deleteItemId').value = id;
    document.getElementById('deleteItemName').innerText = name;
    document.getElementById('deleteAdminPassword').value = '';
    document.getElementById('deleteErrorContainer').style.display = 'none';
    document.getElementById('deleteProductModal').classList.remove('hidden');
    setTimeout(() => document.getElementById('deleteAdminPassword').focus(), 80);
}
function closeDeleteModal() {
    document.getElementById('deleteProductModal').classList.add('hidden');
}

function submitDeleteProduct(e) {
    e.preventDefault();
    const id = document.getElementById('deleteItemId').value;
    const pwd = document.getElementById('deleteAdminPassword').value;
    const csrf = '<?= htmlspecialchars($csrfToken) ?>';
    const spinner = document.getElementById('deleteBtnSpinner');
    const errBox = document.getElementById('deleteErrorContainer');

    spinner.style.display = 'inline-block';
    errBox.style.display = 'none';

    fetch('<?= APP_URL ?>/inventory/deleteProduct', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams({
            id: id,
            password: pwd,
            csrf_token: csrf
        })
    })
    .then(r => r.json())
    .then(data => {
        spinner.style.display = 'none';
        if (data.success) {
            closeDeleteModal();
            applyAjaxFilters();
        } else {
            errBox.innerText = data.error || data.message || 'Failed to delete product.';
            errBox.style.display = 'block';
        }
    })
    .catch(err => {
        spinner.style.display = 'none';
        errBox.innerText = 'Network error occurred. Please try again.';
        errBox.style.display = 'block';
    });
}

// ESC Closes Modals
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeCsvModal();
        closeDeleteModal();
        if (currentOpenDropdown) {
            currentOpenDropdown.classList.add('hidden');
            currentOpenDropdown = null;
        }
    }
});
</script>
