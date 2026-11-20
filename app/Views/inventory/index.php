<?php
// ==========================================
// FALCON STATIONERY ERP — INVENTORY WORKSPACE
// Professional Viewport-Locked ERP Interface
// ==========================================

$items = $data['items'] ?? [];
$variationsByItem = $data['variations_by_item'] ?? [];

$stats = $data['stats'] ?? (object)[
    'total_items' => count($items),
    'low_stock_count' => 0,
    'out_of_stock_count' => 0
];

$inStockCount = max(0, (int)$stats->total_items - (int)$stats->low_stock_count - (int)$stats->out_of_stock_count);

$filters = $data['filters'] ?? [];
$filters['search'] = $filters['search'] ?? '';
$filters['min_price'] = $filters['min_price'] ?? '';
$filters['max_price'] = $filters['max_price'] ?? '';
$filters['stock_status'] = $filters['stock_status'] ?? '';
$filters['category_id'] = $filters['category_id'] ?? '';
$filters['status'] = $filters['status'] ?? '';
$categories = $data['categories'] ?? [];
$isCurrentlyAdmin = (strtolower($_SESSION['role'] ?? '') === 'admin');

$pagination = $data['pagination'] ?? [
    'current_page' => 1,
    'per_page' => 15,
    'total_items' => count($items),
    'total_pages' => 1
];

$currentPage = (int)$pagination['current_page'];
$perPage = (int)$pagination['per_page'];
$totalItems = (int)$pagination['total_items'];
$totalPages = (int)$pagination['total_pages'];

$startIndex = $totalItems > 0 ? (($currentPage - 1) * $perPage) + 1 : 0;
$endIndex = min($currentPage * $perPage, $totalItems);

$flashSuccess = $_SESSION['flash_success'] ?? null;
if ($flashSuccess) unset($_SESSION['flash_success']);
$flashError = $_SESSION['flash_error'] ?? null;
if ($flashError) unset($_SESSION['flash_error']);
$importResults = $_SESSION['import_results'] ?? null;
if ($importResults) unset($_SESSION['import_results']);

$csrfToken = $_SESSION['csrf_token'] ?? '';
?>

<!-- FontAwesome Icons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
/* ============================================================
   ENTERPRISE ERP DESIGN SYSTEM — SF PRO & REFINED GLASS AESTHETIC
   ============================================================ */

:root {
    --erp-font: 'SF Pro Display', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    --erp-mono: ui-monospace, 'SF Mono', Menlo, Monaco, Consolas, monospace;

    /* Surfaces & Borders */
    --erp-bg: #f8fafc;
    --erp-card-bg: #ffffff;
    --erp-border: #e2e8f0;
    --erp-border-subtle: #f1f5f9;
    --erp-th-bg: #f8fafc;
    --erp-row-alt: #f8fafc;
    --erp-row-hover: #f1f5f9;
    --erp-selected-row: #eff6ff;

    /* Text */
    --erp-text-main: #0f172a;
    --erp-text-muted: #64748b;
    --erp-text-label: #475569;

    /* Accents */
    --erp-primary: #0066cc;
    --erp-primary-hover: #0052a3;
    --erp-primary-light: #e8f2fc;

    --erp-success: #10b981;
    --erp-success-bg: #ecfdf5;
    --erp-success-border: #a7f3d0;

    --erp-warning: #f59e0b;
    --erp-warning-bg: #fffbeb;
    --erp-warning-border: #fde68a;

    --erp-danger: #ef4444;
    --erp-danger-bg: #fef2f2;
    --erp-danger-border: #fecaca;
}

@media (prefers-color-scheme: dark) {
    :root {
        --erp-bg: #090d16;
        --erp-card-bg: #111827;
        --erp-border: #1f2937;
        --erp-border-subtle: #182234;
        --erp-th-bg: #131d2e;
        --erp-row-alt: #141f33;
        --erp-row-hover: #1e293b;
        --erp-selected-row: #172554;

        --erp-text-main: #f8fafc;
        --erp-text-muted: #94a3b8;
        --erp-text-label: #cbd5e1;

        --erp-primary: #38bdf8;
        --erp-primary-hover: #60a5fa;
        --erp-primary-light: #162a45;
    }
}

/* Force body viewport lock */
html, body {
    height: 100vh !important;
    overflow: hidden !important;
    margin: 0;
    padding: 0;
    font-family: var(--erp-font);
}

/* Base ERP Workspace Container */
.erp-viewport-view.inv-erp-workspace {
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 0;
    max-height: 100%;
    overflow: hidden;
    gap: 8px;
    font-family: var(--erp-font);
    color: var(--erp-text-main);
    box-sizing: border-box;
    position: relative;
}

/* Form tag wrapping full workspace */
#filterForm {
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 0;
    overflow: hidden;
    gap: 8px;
    margin: 0;
}

/* 1. Slim Top Action & KPI Bar (No Redundant Bulky Headlines) */
.erp-top-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--erp-card-bg);
    border: 1px solid var(--erp-border);
    border-radius: 8px;
    padding: 6px 14px;
    flex-shrink: 0;
    gap: 12px;
}

/* Compact KPI Strip */
.erp-kpi-strip {
    display: flex;
    align-items: center;
    gap: 6px;
    background: var(--erp-th-bg);
    border: 1px solid var(--erp-border);
    border-radius: 6px;
    padding: 2px 6px;
}

.erp-kpi-item {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 2px 8px;
    font-size: 12px;
    border-right: 1px solid var(--erp-border);
}

.erp-kpi-item:last-child {
    border-right: none;
}

.erp-kpi-label {
    color: var(--erp-text-muted);
    font-weight: 500;
}

.erp-kpi-value {
    font-family: var(--erp-mono);
    font-weight: 700;
    font-size: 12.5px;
}

.erp-kpi-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    display: inline-block;
}
.dot-blue { background: #3b82f6; }
.dot-green { background: #10b981; }
.dot-amber { background: #f59e0b; }
.dot-red { background: #ef4444; }

/* Top Action Toolbar */
.erp-actions-group {
    display: flex;
    align-items: center;
    gap: 6px;
}

.erp-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 6px;
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.15s ease;
    white-space: nowrap;
    height: 28px;
    box-sizing: border-box;
}

.erp-btn-primary {
    background: var(--erp-primary);
    color: #ffffff !important;
    border-color: var(--erp-primary);
}
.erp-btn-primary:hover {
    background: var(--erp-primary-hover);
}

.erp-btn-outline {
    background: var(--erp-card-bg);
    color: var(--erp-text-main) !important;
    border-color: var(--erp-border);
}
.erp-btn-outline:hover {
    background: var(--erp-th-bg);
    border-color: #cbd5e1;
}

.erp-btn-danger-outline {
    background: var(--erp-card-bg);
    color: var(--erp-danger) !important;
    border-color: var(--erp-danger-border);
}
.erp-btn-danger-outline:hover {
    background: var(--erp-danger-bg);
}

/* 2. Filters Bar Above Table (Aligned to Top-Right Corner) */
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
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
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

/* Rule: Filters on Right Corner */
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
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    white-space: nowrap;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.erp-select {
    height: 29px;
    max-width: 140px;
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
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
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

.erp-input-sm {
    height: 29px;
    width: 65px;
    padding: 0 7px;
    font-size: 11.5px;
    font-family: var(--erp-font);
    border: 1px solid #d1d5db;
    border-radius: 6px;
    background: #ffffff;
    color: #1e293b;
    outline: none;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
    transition: border-color 0.15s, box-shadow 0.15s;
}
.erp-input-sm:hover {
    border-color: #94a3b8;
}
.erp-input-sm:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.12);
}
.erp-input-sm::placeholder {
    color: #94a3b8;
    font-size: 11px;
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
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
    transition: all 0.15s;
}
.erp-btn-reset:hover {
    background: #f8fafc;
    color: #0f172a;
    border-color: #94a3b8;
}

/* Bulk Selection Toolbar Strip */
.erp-bulk-strip {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #1e293b;
    color: #ffffff;
    border-radius: 6px;
    padding: 2px 8px;
    font-size: 11.5px;
}

.erp-bulk-strip.hidden {
    display: none !important;
}

.erp-bulk-btn {
    background: #334155;
    color: #ffffff;
    border: none;
    border-radius: 4px;
    padding: 2px 8px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.erp-bulk-btn:hover {
    background: #475569;
}
.erp-bulk-btn.danger {
    background: #dc2626;
}
.erp-bulk-btn.danger:hover {
    background: #b91c1c;
}

/* 3. Table Card & Scrollable Table Body Only */
.erp-table-card {
    flex: 1;
    min-height: 0;
    display: flex;
    flex-direction: column;
    background: var(--erp-card-bg);
    border: 1px solid var(--erp-border);
    border-radius: 8px;
    overflow: hidden;
    position: relative;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}

/* Dedicated Scrollable Table Wrapper */
.erp-table-scroll {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: auto;
    position: relative;
    padding-bottom: 54px; /* Extra clearance for the bottom floating bar */
}

/* Sticky Header */
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
    background: var(--erp-th-bg);
    color: var(--erp-text-label);
    font-weight: 600;
    font-size: 11.5px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 8px 12px;
    border-bottom: 2px solid var(--erp-border);
    white-space: nowrap;
}

/* Mandatory Zebra Striping */
.erp-table tbody tr:nth-child(even) {
    background-color: var(--erp-row-alt) !important;
}
.erp-table tbody tr:nth-child(odd) {
    background-color: var(--erp-card-bg) !important;
}

.erp-table tbody tr {
    border-bottom: 1px solid var(--erp-border-subtle);
    transition: background 0.1s ease;
}

.erp-table tbody tr:hover {
    background-color: var(--erp-row-hover) !important;
}

.erp-table tbody tr.row-selected {
    background-color: var(--erp-selected-row) !important;
}

.erp-table td {
    padding: 7px 12px;
    vertical-align: middle;
    color: var(--erp-text-main);
}

/* Cell Alignments */
.text-right { text-align: right !important; }
.text-center { text-align: center !important; }
.font-mono { font-family: var(--erp-mono) !important; }

/* Product Info Cell */
.erp-prod-cell {
    display: flex;
    align-items: center;
    gap: 10px;
}

.erp-prod-thumb {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    object-fit: cover;
    border: 1px solid var(--erp-border);
    background: #f1f5f9;
    flex-shrink: 0;
}

.erp-prod-title {
    font-weight: 600;
    color: var(--erp-text-main);
    line-height: 1.25;
}

.erp-prod-meta {
    font-size: 11px;
    color: var(--erp-text-muted);
    font-family: var(--erp-mono);
    margin-top: 1px;
}

.erp-tag-cat {
    display: inline-block;
    padding: 1px 6px;
    border-radius: 4px;
    background: var(--erp-th-bg);
    color: var(--erp-text-muted);
    font-size: 10.5px;
    font-weight: 500;
    border: 1px solid var(--erp-border);
}

/* Variations Expansion Row */
.erp-var-btn {
    background: transparent;
    border: none;
    color: var(--erp-primary);
    cursor: pointer;
    padding: 2px 4px;
    font-size: 11px;
    border-radius: 4px;
}
.erp-var-btn:hover {
    background: var(--erp-primary-light);
}

.variation-subrow {
    background: #f8fafc !important;
}
@media (prefers-color-scheme: dark) {
    .variation-subrow { background: #0d131f !important; }
}

.variation-subrow td {
    padding: 5px 12px 5px 44px;
    font-size: 11.5px;
    border-bottom: 1px dashed var(--erp-border);
}

.variation-tree-line {
    color: #94a3b8;
    margin-right: 6px;
    font-family: var(--erp-mono);
}

/* Status Badges */
.erp-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}

.badge-instock {
    background: var(--erp-success-bg);
    color: var(--erp-success);
    border: 1px solid var(--erp-success-border);
}

.badge-lowstock {
    background: var(--erp-warning-bg);
    color: var(--erp-warning);
    border: 1px solid var(--erp-warning-border);
}

.badge-outstock {
    background: var(--erp-danger-bg);
    color: var(--erp-danger);
    border: 1px solid var(--erp-danger-border);
}

.badge-inactive {
    background: #f1f5f9;
    color: #64748b;
    border: 1px solid #cbd5e1;
}

/* Interactive iOS Status Toggle Switch */
.erp-switch {
    position: relative;
    display: inline-block;
    width: 34px;
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
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
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
    background-color: #10b981; /* Active Green */
}

.erp-switch input:checked + .erp-slider:before {
    transform: translateX(16px);
}

.erp-switch input:disabled + .erp-slider {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Row Actions */
.erp-row-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 4px;
}

.erp-icon-btn {
    width: 26px;
    height: 26px;
    border-radius: 6px;
    border: 1px solid var(--erp-border);
    background: var(--erp-card-bg);
    color: var(--erp-text-muted);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.15s ease;
}
.erp-icon-btn:hover {
    color: var(--erp-primary);
    border-color: var(--erp-primary);
    background: var(--erp-primary-light);
}
.erp-icon-btn.danger:hover {
    color: var(--erp-danger);
    border-color: var(--erp-danger);
    background: var(--erp-danger-bg);
}

/* 4. Pinned Pagination Footer */
.erp-pagination-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px 14px;
    background: var(--erp-th-bg);
    border-top: 1px solid var(--erp-border);
    flex-shrink: 0;
    font-size: 12px;
}

.erp-pg-info {
    color: var(--erp-text-muted);
}
.erp-pg-info strong {
    color: var(--erp-text-main);
    font-family: var(--erp-mono);
}

.erp-pg-controls {
    display: flex;
    align-items: center;
    gap: 12px;
}

.erp-pg-size-select {
    height: 26px;
    padding: 0 4px;
    font-size: 11.5px;
    border: 1px solid var(--erp-border);
    border-radius: 4px;
    background: var(--erp-card-bg);
    color: var(--erp-text-main);
    outline: none;
}

.erp-pg-nav {
    display: flex;
    align-items: center;
    gap: 4px;
}

.erp-pg-btn {
    width: 26px;
    height: 26px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--erp-border);
    border-radius: 4px;
    background: var(--erp-card-bg);
    color: var(--erp-text-main);
    font-size: 11px;
    cursor: pointer;
}
.erp-pg-btn:hover:not(:disabled) {
    background: var(--erp-th-bg);
    border-color: #cbd5e1;
}
.erp-pg-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.erp-pg-page-indicator {
    font-family: var(--erp-mono);
    font-weight: 600;
    padding: 0 6px;
    font-size: 11.5px;
}

/* 5. Sleek Floating Island Bar (Search + Add New Button) - Standard Size */
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
    text-decoration: none;
    transition: transform 0.15s ease, background 0.15s ease, box-shadow 0.15s ease;
    box-shadow: 0 2px 6px rgba(0,0,0,0.12);
}
.cmd-cta:hover {
    background: #f8fafc;
    transform: scale(1.02);
}

/* Split View with Slide-Over Quick View */
.erp-content-split {
    display: flex;
    flex: 1;
    min-height: 0;
    position: relative;
    overflow: hidden;
}

/* Slide-Over Quick View Drawer */
.erp-drawer {
    position: absolute;
    top: 0;
    right: -420px;
    width: 380px;
    height: 100%;
    background: var(--erp-card-bg);
    border-left: 1px solid var(--erp-border);
    box-shadow: -4px 0 20px rgba(0,0,0,0.08);
    transition: right 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    z-index: 50;
    display: flex;
    flex-direction: column;
}

.erp-drawer.open {
    right: 0;
}

.erp-drawer-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    border-bottom: 1px solid var(--erp-border);
    background: var(--erp-th-bg);
}

.erp-drawer-title {
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--erp-text-main);
}

.erp-drawer-close {
    background: transparent;
    border: none;
    color: var(--erp-text-muted);
    cursor: pointer;
    font-size: 14px;
}
.erp-drawer-close:hover {
    color: var(--erp-danger);
}

.erp-drawer-body {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.erp-drawer-img-box {
    width: 100%;
    height: 180px;
    border: 1px solid var(--erp-border);
    border-radius: 8px;
    background: #f8fafc;
    overflow: hidden;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.erp-drawer-img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.erp-drawer-tile-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}

.erp-drawer-tile {
    background: var(--erp-th-bg);
    border: 1px solid var(--erp-border);
    border-radius: 6px;
    padding: 8px;
}

.erp-drawer-tile-lbl {
    font-size: 10.5px;
    text-transform: uppercase;
    color: var(--erp-text-muted);
    font-weight: 600;
}

.erp-drawer-tile-val {
    font-family: var(--erp-mono);
    font-weight: 700;
    font-size: 14px;
    margin-top: 2px;
}

/* Loading Overlay */
#table-loader {
    position: absolute;
    inset: 0;
    background: rgba(255, 255, 255, 0.7);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 40;
}
#table-loader.active {
    display: flex;
}
@media (prefers-color-scheme: dark) {
    #table-loader { background: rgba(17, 24, 39, 0.7); }
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
    z-index: 1000;
}
.erp-modal-backdrop.hidden {
    display: none !important;
}

.erp-modal-box {
    background: var(--erp-card-bg);
    border: 1px solid var(--erp-border);
    border-radius: 12px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    width: 90%;
    max-width: 480px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.erp-modal-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 18px;
    border-bottom: 1px solid var(--erp-border);
    background: var(--erp-th-bg);
}

.erp-modal-head h3 {
    margin: 0;
    font-size: 15px;
    font-weight: 700;
    color: var(--erp-text-main);
}

.erp-modal-body {
    padding: 18px;
    overflow-y: auto;
    max-height: 70vh;
}

.erp-modal-foot {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    padding: 12px 18px;
    border-top: 1px solid var(--erp-border);
    background: var(--erp-th-bg);
}

/* Alerts */
.erp-alert {
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.erp-alert.success {
    background: var(--erp-success-bg);
    color: var(--erp-success);
    border: 1px solid var(--erp-success-border);
}
.erp-alert.error {
    background: var(--erp-danger-bg);
    color: var(--erp-danger);
    border: 1px solid var(--erp-danger-border);
}
.erp-alert-close {
    background: transparent;
    border: none;
    color: inherit;
    cursor: pointer;
    font-size: 12px;
}
</style>

<div class="erp-viewport-view inv-erp-workspace">
<form id="filterForm" action="<?= APP_URL ?>/inventory" method="GET" onsubmit="event.preventDefault(); applyAjaxFilters();">
    <input type="hidden" name="page" id="currentPageInput" value="<?= $currentPage ?>">
    <input type="hidden" name="per_page" id="perPageInput" value="<?= $perPage ?>">

    <!-- 1. Slim Top Action & KPI Bar (No Redundant Headlines) -->
    <div class="erp-top-header">
        <!-- Compact KPI Strip -->
        <div class="erp-kpi-strip">
            <div class="erp-kpi-item">
                <span class="erp-kpi-dot dot-blue"></span>
                <span class="erp-kpi-label">Total:</span>
                <span class="erp-kpi-value" id="stat-total-items"><?= number_format($stats->total_items) ?></span>
            </div>
            <div class="erp-kpi-item">
                <span class="erp-kpi-dot dot-green"></span>
                <span class="erp-kpi-label">In Stock:</span>
                <span class="erp-kpi-value"><?= number_format($inStockCount) ?></span>
            </div>
            <div class="erp-kpi-item">
                <span class="erp-kpi-dot dot-amber"></span>
                <span class="erp-kpi-label">Low Stock:</span>
                <span class="erp-kpi-value" id="stat-low-stock"><?= number_format($stats->low_stock_count) ?></span>
            </div>
            <div class="erp-kpi-item">
                <span class="erp-kpi-dot dot-red"></span>
                <span class="erp-kpi-label">Out of Stock:</span>
                <span class="erp-kpi-value" id="stat-out-of-stock"><?= number_format($stats->out_of_stock_count) ?></span>
            </div>
        </div>

        <!-- Top Action Toolbar -->
        <div class="erp-actions-group">
            <button type="button" onclick="openCsvModal()" class="erp-btn erp-btn-outline" title="Import CSV">
                <i class="fa-solid fa-file-arrow-up"></i> Import
            </button>
            <a href="<?= APP_URL ?>/inventory/exportCSV" class="erp-btn erp-btn-outline" title="Export CSV">
                <i class="fa-solid fa-file-arrow-down"></i> Export
            </a>
            <a href="<?= APP_URL ?>/inventory/reserved" class="erp-btn erp-btn-outline" title="Stock Reservations">
                <i class="fa-solid fa-boxes-packing"></i> Reservations
            </a>
            <a href="<?= APP_URL ?>/inventory/deleted" class="erp-btn erp-btn-danger-outline" title="Trash / Deleted Products">
                <i class="fa-solid fa-trash-arrow-up"></i> Trash
            </a>
        </div>
    </div>

    <!-- Alerts Strip (if any) -->
    <?php if ($flashSuccess || isset($_GET['flash_success'])): ?>
    <div id="flash-success-alert" class="erp-alert success">
        <div><i class="fa-solid fa-circle-check" style="margin-right:6px;"></i> <?= htmlspecialchars($flashSuccess ?? $_GET['flash_success'] ?? '') ?></div>
        <button type="button" class="erp-alert-close" onclick="document.getElementById('flash-success-alert').style.display='none'"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <?php endif; ?>

    <?php if ($flashError): ?>
    <div id="flash-error-alert" class="erp-alert error">
        <div><i class="fa-solid fa-circle-exclamation" style="margin-right:6px;"></i> <?= htmlspecialchars($flashError) ?></div>
        <button type="button" class="erp-alert-close" onclick="document.getElementById('flash-error-alert').style.display='none'"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <?php endif; ?>

    <!-- 2. Filters Bar Above Table with Right-Corner Filter Alignment -->
    <div class="erp-filter-bar">
        <!-- Left: Active Item Count & Bulk Selection Controls -->
        <div class="erp-filter-left">
            <span class="erp-results-badge">
                <strong id="matching-count"><?= number_format($totalItems) ?></strong> products
            </span>

            <!-- Bulk Selection Strip (Shown only when rows are selected) -->
            <div id="bulkEditToolbar" class="erp-bulk-strip hidden">
                <span><strong id="selectedCountBadge">0</strong> selected</span>
                <button type="button" onclick="openBulkEditModal()" class="erp-bulk-btn"><i class="fa-solid fa-pen"></i> Edit</button>
                <button type="button" onclick="confirmBulkDelete()" class="erp-bulk-btn danger"><i class="fa-solid fa-trash-can"></i> Delete</button>
                <button type="button" onclick="clearSelection()" class="erp-bulk-btn"><i class="fa-solid fa-xmark"></i></button>
            </div>
        </div>

        <!-- Right: All Filtering Options Placed at Right Corner -->
        <div class="erp-filter-right">
            <!-- Category Filter -->
            <div class="erp-filter-control">
                <span class="erp-filter-label">Category:</span>
                <select name="category_id" id="categoryInput" class="erp-select" onchange="applyAjaxFilters()">
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

            <!-- Supplier Filter -->
            <div class="erp-filter-control">
                <span class="erp-filter-label">Supplier:</span>
                <select name="supplier_id" id="supplierInput" class="erp-select" onchange="applyAjaxFilters()">
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

            <!-- Stock Status Filter -->
            <div class="erp-filter-control">
                <span class="erp-filter-label">Stock:</span>
                <select name="stock_status" id="stockStatusInput" class="erp-select" onchange="applyAjaxFilters()">
                    <option value="" <?= ($filters['stock_status'] ?? '') === '' ? 'selected' : '' ?>>All Stock</option>
                    <option value="instock" <?= ($filters['stock_status'] ?? '') === 'instock' ? 'selected' : '' ?>>In Stock (&gt;5)</option>
                    <option value="lowstock" <?= ($filters['stock_status'] ?? '') === 'lowstock' ? 'selected' : '' ?>>Low Stock (1-5)</option>
                    <option value="outstock" <?= ($filters['stock_status'] ?? '') === 'outstock' ? 'selected' : '' ?>>Out of Stock (0)</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div class="erp-filter-control">
                <span class="erp-filter-label">Status:</span>
                <select name="status" id="itemStatusInput" class="erp-select" onchange="applyAjaxFilters()">
                    <option value="" <?= ($filters['status'] ?? '') === '' ? 'selected' : '' ?>>All Statuses</option>
                    <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <!-- Price Range (Min/Max) -->
            <div class="erp-filter-control">
                <span class="erp-filter-label">Price:</span>
                <input type="number" step="0.01" name="min_price" id="minPriceInput" class="erp-input-sm"
                       value="<?= htmlspecialchars($filters['min_price']) ?>" oninput="triggerSearchDelay()" placeholder="Min Rs">
                <span style="color: #94a3b8; font-weight: 500;">–</span>
                <input type="number" step="0.01" name="max_price" id="maxPriceInput" class="erp-input-sm"
                       value="<?= htmlspecialchars($filters['max_price']) ?>" oninput="triggerSearchDelay()" placeholder="Max Rs">
            </div>

            <!-- Reset Filters -->
            <button type="button" onclick="clearAllFilters()" class="erp-btn-reset" title="Reset all filters">
                <i class="fa-solid fa-arrow-rotate-left"></i> Reset
            </button>
        </div>
    </div>

    <!-- 3. Table Card (Full Height & Scrollable Table Rows Only with Zebra Striping) -->
    <div class="erp-content-split">
        <div class="erp-table-card">
            <div id="table-loader">
                <i class="fa-solid fa-spinner fa-spin" style="font-size:24px; color: var(--erp-primary);"></i>
            </div>

            <div id="table-container" class="erp-table-scroll">
                <table class="erp-table">
                    <thead>
                        <tr>
                            <th style="width:36px;" class="text-center">
                                <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)">
                            </th>
                            <th>Product Details</th>
                            <th>Category</th>
                            <th>Supplier</th>
                            <th class="text-right" style="width:110px;">Retail (LKR)</th>
                            <th class="text-right" style="width:110px;">B2B / W/S</th>
                            <th class="text-center" style="width:75px;">Qty</th>
                            <th class="text-center" style="width:85px;">Active</th>
                            <th class="text-center" style="width:110px;">Status</th>
                            <th class="text-right" style="width:100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="10" class="text-center" style="padding: 40px 16px; color: var(--erp-text-muted);">
                                <i class="fa-solid fa-box-open" style="font-size: 28px; margin-bottom: 8px; display: block; opacity: 0.5;"></i>
                                <strong style="font-size: 14px; color: var(--erp-text-main);">No products found</strong>
                                <div style="font-size: 12px; margin-top: 4px;">Try clearing filters or search criteria.</div>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($items as $item): ?>
                                <?php
                                    $price = (float)($item->selling_price ?? $item->price ?? 0);
                                    $wholesalePrice = (float)($item->wholesale_price ?? 0);
                                    $qty = (int)($item->qty ?? $item->quantity_on_hand ?? 0);
                                    $sku = $item->item_code ?? $item->sku ?? 'NO-SKU';
                                    $itemStatus = strtolower($item->status ?? 'active');

                                    // Resolve variations from batch preloaded data to avoid N+1 queries
                                    $variations = $variationsByItem[$item->id] ?? [];
                                    if (empty($variations) && !empty($item->variations_json)) {
                                        $decoded = json_decode(html_entity_decode($item->variations_json, ENT_QUOTES, 'UTF-8'));
                                        if (is_array($decoded)) {
                                            foreach ($decoded as $v) {
                                                $vObj = new stdClass();
                                                $vObj->id = $v->id ?? 0;
                                                $vObj->attribute_name = 'Option';
                                                $vObj->value_name = $v->attribute ?? $v->value ?? $v->value_name ?? '';
                                                $vObj->sku = $v->sku ?? '';
                                                $vObj->quantity_on_hand = $v->qty ?? $v->quantity_on_hand ?? $qty;
                                                $vObj->price = $v->price ?? $v->selling_price ?? $price;
                                                $vObj->wholesale_price = $v->wholesale_price ?? $wholesalePrice;
                                                $variations[] = $vObj;
                                            }
                                        }
                                    }

                                    // Image
                                    $img_src = APP_URL . '/public/assets/no-image.png';
                                    if (!empty($item->image_path)) {
                                        $img_src = APP_URL . '/uploads/products/' . basename($item->image_path);
                                    }

                                    // Quick View Data JSON
                                    $qvData = [
                                        'id' => $item->id,
                                        'name' => $item->name,
                                        'sku' => $sku,
                                        'code' => $item->sample_code ?? '',
                                        'price' => number_format($price, 2),
                                        'b2b' => number_format($wholesalePrice, 2),
                                        'qty' => $qty,
                                        'desc' => $item->description ?? '',
                                        'category' => $item->category_name ?? 'Uncategorized',
                                        'supplier' => $item->supplier_name ?? 'N/A',
                                        'brand' => $item->brand ?? 'N/A',
                                        'status' => $itemStatus,
                                        'img' => $img_src
                                    ];
                                ?>
                                <tr id="row_item_<?= $item->id ?>">
                                    <td class="text-center">
                                        <input type="checkbox" name="selected_items[]" value="<?= $item->id ?>" class="item-select-checkbox" onchange="updateSelection()">
                                    </td>
                                    <td>
                                        <div class="erp-prod-cell">
                                            <?php if (!empty($variations)): ?>
                                                <button type="button" class="erp-var-btn" onclick="toggleVariationsRow(<?= $item->id ?>, this)" title="Collapse / Expand Variations">
                                                    <i class="fa-solid fa-chevron-down"></i>
                                                </button>
                                            <?php else: ?>
                                                <span style="width: 14px; display: inline-block;"></span>
                                            <?php endif; ?>
                                            
                                            <img src="<?= $img_src ?>" class="erp-prod-thumb" alt="" onerror="this.src='https://placehold.co/64x64/e2e8f0/64748b?text=P'">
                                            
                                            <div>
                                                <div class="erp-prod-title">
                                                    <?= htmlspecialchars($item->name ?? 'Unnamed Product') ?>
                                                    <?php if (!empty($variations)): ?>
                                                        <span style="font-size: 10px; font-weight: 700; background: var(--erp-primary-light); color: var(--erp-primary); padding: 1px 5px; border-radius: 4px; margin-left: 4px;">
                                                            <?= count($variations) ?> vars
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="erp-prod-meta">
                                                    SKU: <?= htmlspecialchars($sku) ?>
                                                    <?= !empty($item->sample_code) ? ' · Code: ' . htmlspecialchars($item->sample_code) : '' ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($item->category_name ?? '-') ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($item->supplier_name ?? '-') ?>
                                    </td>
                                    <td class="text-right font-mono">
                                        <?= number_format($price, 2) ?>
                                    </td>
                                    <td class="text-right font-mono" style="color: var(--erp-text-muted);">
                                        <?= number_format($wholesalePrice, 2) ?>
                                    </td>
                                    <td class="text-center font-mono">
                                        <?php if ($qty <= 0): ?>
                                            <span style="color: var(--erp-danger); font-weight: 700;"><?= $qty ?></span>
                                        <?php elseif ($qty <= 5): ?>
                                            <span style="color: var(--erp-warning); font-weight: 700;"><?= $qty ?></span>
                                        <?php else: ?>
                                            <span style="color: var(--erp-text-main); font-weight: 600;"><?= $qty ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <!-- Sleek iOS Toggle Switch -->
                                        <label class="erp-switch" title="Toggle Active / Inactive">
                                            <input type="checkbox" onchange="toggleProductStatus(<?= $item->id ?>, this.checked ? 'active' : 'inactive', this)" <?= $itemStatus === 'active' ? 'checked' : '' ?>>
                                            <span class="erp-slider"></span>
                                        </label>
                                    </td>
                                    <td class="text-center" id="status_badge_<?= $item->id ?>">
                                        <?php if ($itemStatus === 'inactive'): ?>
                                            <span class="erp-badge badge-inactive">Inactive</span>
                                        <?php elseif ($qty <= 0): ?>
                                            <span class="erp-badge badge-outstock">Out of Stock</span>
                                        <?php elseif ($qty <= 5): ?>
                                            <span class="erp-badge badge-lowstock">Low (<?= $qty ?>)</span>
                                        <?php else: ?>
                                            <span class="erp-badge badge-instock">In Stock</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right">
                                        <div class="erp-row-actions">
                                            <button type="button" class="erp-icon-btn" onclick='openQuickView(<?= json_encode($qvData) ?>)' title="Quick View">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                            <a href="<?= APP_URL ?>/inventory/edit/<?= $item->id ?>" class="erp-icon-btn" title="Edit Product">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <button type="button" class="erp-icon-btn danger" onclick="confirmDelete(<?= $item->id ?>, '<?= htmlspecialchars(addslashes($item->name)) ?>')" title="Delete">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <?php if (!empty($variations)): ?>
                                    <?php foreach ($variations as $var): ?>
                                    <tr class="variation-subrow variations_row_<?= $item->id ?>">
                                        <td></td>
                                        <td>
                                            <span class="variation-tree-line">└─</span>
                                            <strong style="color: var(--erp-text-main);"><?= htmlspecialchars($var->value_name ?? 'Option') ?></strong>
                                            <span class="erp-prod-meta" style="margin-left:8px;">SKU: <?= htmlspecialchars($var->sku ?? '') ?></span>
                                        </td>
                                        <td style="color:var(--erp-text-muted); font-size:11.5px;">Variation</td>
                                        <td style="color:var(--erp-text-muted); font-size:11.5px;">-</td>
                                        <td class="text-right font-mono"><?= number_format($var->price ?? $price, 2) ?></td>
                                        <td class="text-right font-mono" style="color: var(--erp-text-muted);"><?= number_format($var->wholesale_price ?? $wholesalePrice, 2) ?></td>
                                        <td class="text-center font-mono">
                                            <strong><?= (int)($var->quantity_on_hand ?? 0) ?></strong>
                                        </td>
                                        <td></td>
                                        <td class="text-center">
                                            <?php $varQty = (int)($var->quantity_on_hand ?? 0); ?>
                                            <?php if ($varQty <= 0): ?>
                                                <span class="erp-badge badge-outstock">Out</span>
                                            <?php elseif ($varQty <= 5): ?>
                                                <span class="erp-badge badge-lowstock">Low</span>
                                            <?php else: ?>
                                                <span class="erp-badge badge-instock">Available</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right">
                                            <a href="<?= APP_URL ?>/inventory/edit/<?= $item->id ?>" class="erp-icon-btn" title="Edit variation">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- 4. Pinned Pagination Footer (Always visible at bottom of table card) -->
            <div class="erp-pagination-bar">
                <div class="erp-pg-info">
                    <?= $totalItems > 0 ? "Showing <strong>$startIndex</strong> – <strong>$endIndex</strong> of <strong>$totalItems</strong> products" : 'No results found' ?>
                </div>
                <div class="erp-pg-controls">
                    <label style="display:flex; align-items:center; gap:4px; font-size:11.5px; color:var(--erp-text-muted);">
                        <span>Per page:</span>
                        <select class="erp-pg-size-select" onchange="updatePageSize(this.value)">
                            <option value="15"   <?= $perPage === 15   ? 'selected' : '' ?>>15</option>
                            <option value="50"   <?= $perPage === 50   ? 'selected' : '' ?>>50</option>
                            <option value="100"  <?= $perPage === 100  ? 'selected' : '' ?>>100</option>
                            <option value="1000" <?= $perPage === 1000 ? 'selected' : '' ?>>All</option>
                        </select>
                    </label>

                    <?php if ($totalPages > 1): ?>
                    <div class="erp-pg-nav">
                        <button type="button" class="erp-pg-btn" onclick="navigatePage(<?= max(1, $currentPage - 1) ?>)" <?= $currentPage <= 1 ? 'disabled' : '' ?> title="Previous Page">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        <span class="erp-pg-page-indicator"><?= $currentPage ?> / <?= $totalPages ?></span>
                        <button type="button" class="erp-pg-btn" onclick="navigatePage(<?= min($totalPages, $currentPage + 1) ?>)" <?= $currentPage >= $totalPages ? 'disabled' : '' ?> title="Next Page">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Slide-over Quick View Drawer -->
        <div id="quickViewDrawer" class="erp-drawer">
            <div class="erp-drawer-header">
                <div class="erp-drawer-title"><i class="fa-solid fa-circle-info" style="margin-right:6px; color:var(--erp-primary);"></i> Product Details</div>
                <button type="button" class="erp-drawer-close" onclick="closeQuickView()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="erp-drawer-body">
                <div class="erp-drawer-img-box">
                    <img id="qv-img" src="" class="erp-drawer-img" alt="">
                </div>
                <div>
                    <h4 id="qv-name" style="margin:0 0 4px 0; font-size:14px; font-weight:700; color:var(--erp-text-main);"></h4>
                    <div id="qv-sku" style="font-size:11.5px; font-family:var(--erp-mono); color:var(--erp-text-muted);"></div>
                </div>

                <div class="erp-drawer-tile-grid">
                    <div class="erp-drawer-tile">
                        <div class="erp-drawer-tile-lbl">Retail Price</div>
                        <div id="qv-price" class="erp-drawer-tile-val" style="color:var(--erp-primary);"></div>
                    </div>
                    <div class="erp-drawer-tile">
                        <div class="erp-drawer-tile-lbl">B2B / Wholesale</div>
                        <div id="qv-b2b" class="erp-drawer-tile-val"></div>
                    </div>
                    <div class="erp-drawer-tile">
                        <div class="erp-drawer-tile-lbl">Stock on Hand</div>
                        <div id="qv-qty" class="erp-drawer-tile-val"></div>
                    </div>
                    <div class="erp-drawer-tile">
                        <div class="erp-drawer-tile-lbl">Status</div>
                        <div id="qv-status" style="margin-top:2px;"></div>
                    </div>
                </div>

                <div class="erp-drawer-tile">
                    <div class="erp-drawer-tile-lbl">Category & Brand</div>
                    <div style="font-size:12px; margin-top:2px;">
                        <span id="qv-category"></span> · <span id="qv-brand"></span>
                    </div>
                </div>

                <div class="erp-drawer-tile">
                    <div class="erp-drawer-tile-lbl">Description</div>
                    <div id="qv-desc" style="font-size:11.5px; color:var(--erp-text-muted); margin-top:2px; max-height:80px; overflow-y:auto;"></div>
                </div>

                <div style="display:flex; gap:8px; margin-top:auto;">
                    <a id="qv-edit-link" href="#" class="erp-btn erp-btn-primary" style="flex:1; justify-content:center;">
                        <i class="fa-solid fa-pen"></i> Edit Product
                    </a>
                    <a id="qv-ledger-link" href="#" class="erp-btn erp-btn-outline" style="flex:1; justify-content:center;">
                        <i class="fa-solid fa-chart-line"></i> Stock Ledger
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. Floating Island Bar (Search Bar + Add New Button) -->
    <div class="cmd-bar">
        <div class="cmd-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="search" id="searchInput"
                   value="<?= htmlspecialchars($filters['search']) ?>"
                   oninput="triggerSearchDelay()"
                   placeholder="Search catalog…">
            <i class="fa-solid fa-xmark cmd-clear-btn" id="searchClearBtn" onclick="clearSearchInput()" title="Clear search"></i>
        </div>
        <div class="cmd-divider"></div>
        <a href="<?= APP_URL ?>/inventory/add" class="cmd-cta" title="Create New Product">
            <i class="fa-solid fa-plus" style="font-size:11px;"></i> New
        </a>
    </div>

</form>
</div>

<!-- ============================================================
     MODALS: CSV IMPORT, DELETE (CLEAN ENTERPRISE), BULK EDIT
     ============================================================ -->

<!-- CSV Import Modal -->
<div id="csvImportModal" class="erp-modal-backdrop hidden">
    <div class="erp-modal-box">
        <div class="erp-modal-head">
            <h3><i class="fa-solid fa-file-csv" style="color:var(--erp-primary); margin-right:6px;"></i> Import Products via CSV</h3>
            <button type="button" onclick="closeCsvModal()" class="erp-drawer-close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="csvImportForm" action="<?= APP_URL ?>/inventory/importERPCSV" method="POST" enctype="multipart/form-data" onsubmit="return handleCsvImportSubmit(event)">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <div class="erp-modal-body">
                <div style="border: 2px dashed var(--erp-border); border-radius:8px; padding:24px; text-align:center; background:var(--erp-th-bg);">
                    <input type="file" name="csv_file" id="csvFileInput" accept=".csv" required onchange="logFileSelection(this)">
                    <div style="font-size:12px; color:var(--erp-text-muted); margin-top:8px;">Upload .csv file with columns: Item Code, Name, Selling Price, Cost, Qty, Category</div>
                </div>
                <div id="importDebugInfo" style="display:none; background:#f1f5f9; padding:8px; margin-top:8px; border-radius:6px; font-size:11px; font-family:monospace;">
                    <strong>File:</strong> <span id="fileInfoText"></span>
                </div>
            </div>
            <div class="erp-modal-foot">
                <button type="button" onclick="closeCsvModal()" class="erp-btn erp-btn-outline">Cancel</button>
                <button type="submit" id="csvImportBtn" class="erp-btn erp-btn-primary"><i class="fa-solid fa-upload"></i> Upload & Import</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal (Clean Enterprise Style matching user image) -->
<div id="deleteProductModal" class="erp-modal-backdrop hidden">
    <div class="erp-modal-box">
        <div class="erp-modal-head">
            <h3 style="color:var(--erp-danger); display:flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-triangle-exclamation"></i> Delete Product
            </h3>
            <button type="button" onclick="closeDeleteModal()" class="erp-drawer-close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="deleteProductForm" onsubmit="submitDeleteProduct(event)">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <div class="erp-modal-body">
                <input type="hidden" id="deleteItemId" name="item_id">
                <p style="font-size:14px; margin:0 0 16px 0; color:var(--erp-text-main);">
                    Are you sure you want to delete <strong id="deleteItemName" style="color:var(--erp-text-main);"></strong>?
                </p>
                <div id="deleteErrorContainer" class="erp-alert error" style="display:none; margin-bottom:12px;"></div>
                <label style="font-size:13px; font-weight:600; display:block; margin-bottom:6px; color:var(--erp-text-main);">
                    Confirm with Account Password:
                </label>
                <input type="password" name="password" id="deleteAdminPassword" class="erp-input-sm" style="width:100%; height:34px; padding:6px 10px; font-size:13px;" placeholder="Enter password" required autocomplete="current-password">
            </div>
            <div class="erp-modal-foot">
                <button type="button" onclick="closeDeleteModal()" class="erp-btn erp-btn-outline" style="padding:6px 16px;">Cancel</button>
                <button type="submit" id="deleteSubmitBtn" class="erp-btn erp-btn-primary" style="background:#dc2626; border-color:#dc2626; padding:6px 16px;">
                    <i id="deleteBtnSpinner" class="fa-solid fa-spinner fa-spin" style="display:none; margin-right:4px;"></i> Delete
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Edit Modal -->
<div id="bulkEditModal" class="erp-modal-backdrop hidden">
    <div class="erp-modal-box">
        <div class="erp-modal-head">
            <h3><i class="fa-solid fa-layer-group" style="margin-right:6px; color:var(--erp-primary);"></i> Bulk Edit Products</h3>
            <button type="button" onclick="closeBulkEditModal()" class="erp-drawer-close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="bulkEditForm" onsubmit="submitBulkEdit(event)">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <div class="erp-modal-body">
                <div style="font-size:12px; color:var(--erp-text-muted); margin-bottom:12px;">
                    Updating <strong id="bulkSelectedCount" style="color:var(--erp-text-main);">0</strong> selected products:
                </div>
                <div id="bulkEditErrorContainer" class="erp-alert error" style="display:none; margin-bottom:10px;"></div>

                <div style="display:flex; flex-direction:column; gap:10px;">
                    <!-- Category -->
                    <div style="border:1px solid var(--erp-border); border-radius:6px; padding:8px;">
                        <label style="display:flex; align-items:center; gap:6px; font-size:12px; font-weight:600; cursor:pointer;">
                            <input type="checkbox" name="update_category" value="1" id="bulkUpdateCategory" onchange="toggleBulkField('category')"> Update Category
                        </label>
                        <select name="category_id" id="bulkCategorySelect" class="erp-select" style="width:100%; margin-top:6px; pointer-events:none; opacity:0.4;">
                            <option value="">No Category</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat->id ?>"><?= htmlspecialchars($cat->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Retail Price -->
                    <div style="border:1px solid var(--erp-border); border-radius:6px; padding:8px;">
                        <label style="display:flex; align-items:center; gap:6px; font-size:12px; font-weight:600; cursor:pointer;">
                            <input type="checkbox" name="update_selling_price" value="1" id="bulkUpdateSellingPrice" onchange="toggleBulkField('selling_price')"> Update Retail Price
                        </label>
                        <div style="display:flex; gap:6px; margin-top:6px;">
                            <select name="selling_price_type" id="bulkSellingPriceType" class="erp-select" style="width:90px; pointer-events:none; opacity:0.4;">
                                <option value="flat">Flat</option>
                                <option value="pct_inc">+ %</option>
                                <option value="pct_dec">− %</option>
                            </select>
                            <input type="number" step="0.01" name="selling_price_val" id="bulkSellingPriceVal" class="erp-input-sm" style="flex:1; pointer-events:none; opacity:0.4;" placeholder="0.00">
                        </div>
                    </div>

                    <!-- B2B Price -->
                    <div style="border:1px solid var(--erp-border); border-radius:6px; padding:8px;">
                        <label style="display:flex; align-items:center; gap:6px; font-size:12px; font-weight:600; cursor:pointer;">
                            <input type="checkbox" name="update_wholesale_price" value="1" id="bulkUpdateWholesalePrice" onchange="toggleBulkField('wholesale_price')"> Update B2B Price
                        </label>
                        <div style="display:flex; gap:6px; margin-top:6px;">
                            <select name="wholesale_price_type" id="bulkWholesalePriceType" class="erp-select" style="width:90px; pointer-events:none; opacity:0.4;">
                                <option value="flat">Flat</option>
                                <option value="pct_inc">+ %</option>
                                <option value="pct_dec">− %</option>
                            </select>
                            <input type="number" step="0.01" name="wholesale_price_val" id="bulkWholesalePriceVal" class="erp-input-sm" style="flex:1; pointer-events:none; opacity:0.4;" placeholder="0.00">
                        </div>
                    </div>

                    <!-- Status -->
                    <div style="border:1px solid var(--erp-border); border-radius:6px; padding:8px;">
                        <label style="display:flex; align-items:center; gap:6px; font-size:12px; font-weight:600; cursor:pointer;">
                            <input type="checkbox" name="update_status" value="1" id="bulkUpdateStatus" onchange="toggleBulkField('status')"> Update Status
                        </label>
                        <select name="status" id="bulkStatusSelect" class="erp-select" style="width:100%; margin-top:6px; pointer-events:none; opacity:0.4;">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="erp-modal-foot">
                <button type="button" onclick="closeBulkEditModal()" class="erp-btn erp-btn-outline">Cancel</button>
                <button type="submit" id="bulkSubmitBtn" class="erp-btn erp-btn-primary">
                    <i id="bulkBtnSpinner" class="fa-solid fa-spinner fa-spin" style="display:none; margin-right:4px;"></i> Apply Changes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================
     JAVASCRIPT LOGIC
     ============================================================ -->
<script>
let searchTimer = null;
let activeDeleteId = null;

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '<?= htmlspecialchars($csrfToken) ?>';
}

// Search with Debounce & Clear Icon Visibility
function triggerSearchDelay() {
    clearTimeout(searchTimer);
    const searchVal = document.getElementById('searchInput').value;
    const clearBtn = document.getElementById('searchClearBtn');
    if (clearBtn) {
        clearBtn.style.display = searchVal.length > 0 ? 'inline-block' : 'none';
    }
    searchTimer = setTimeout(() => {
        document.getElementById('currentPageInput').value = 1;
        applyAjaxFilters();
    }, 280);
}

function clearSearchInput() {
    const sInput = document.getElementById('searchInput');
    if (sInput) {
        sInput.value = '';
        document.getElementById('searchClearBtn').style.display = 'none';
        document.getElementById('currentPageInput').value = 1;
        applyAjaxFilters();
    }
}

// Reset All Filters
function clearAllFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('categoryInput').value = '';
    const supInput = document.getElementById('supplierInput');
    if (supInput) supInput.value = '';
    document.getElementById('stockStatusInput').value = '';
    document.getElementById('itemStatusInput').value = '';
    document.getElementById('minPriceInput').value = '';
    document.getElementById('maxPriceInput').value = '';
    document.getElementById('currentPageInput').value = 1;
    const clearBtn = document.getElementById('searchClearBtn');
    if (clearBtn) clearBtn.style.display = 'none';
    applyAjaxFilters();
}

// AJAX Filter Fetching
function applyAjaxFilters() {
    const form = document.getElementById('filterForm');
    const loader = document.getElementById('table-loader');
    if (loader) loader.classList.add('active');

    const formData = new FormData(form);
    const paramsToKeep = new FormData();
    for (let [key, value] of formData.entries()) {
        if (!['selected_items[]','update_category','update_selling_price','update_wholesale_price','update_status'].includes(key)) {
            paramsToKeep.append(key, value);
        }
    }

    const queryParams = new URLSearchParams(paramsToKeep).toString();
    const requestUrl = form.getAttribute('action') + '?' + queryParams;

    fetch(requestUrl, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html'
        }
    })
    .then(r => { if (!r.ok) throw new Error('Network error'); return r.text(); })
    .then(html => {
        const doc = new DOMParser().parseFromString(html, 'text/html');
        const newTable = doc.getElementById('table-container');
        const oldTable = document.getElementById('table-container');
        if (newTable && oldTable) {
            oldTable.innerHTML = newTable.innerHTML;
        }

        // Update pagination bar
        const newPg = doc.querySelector('.erp-pagination-bar');
        const oldPg = document.querySelector('.erp-pagination-bar');
        if (newPg && oldPg) {
            oldPg.innerHTML = newPg.innerHTML;
        }

        // Update KPI stats & item counts
        ['stat-total-items','stat-low-stock','stat-out-of-stock','matching-count'].forEach(id => {
            const n = doc.getElementById(id), o = document.getElementById(id);
            if (n && o) o.innerHTML = n.innerHTML;
        });

        window.history.pushState({ path: requestUrl }, '', requestUrl);
        clearSelection();
    })
    .catch(err => console.error('Filter Error:', err))
    .finally(() => {
        if (loader) loader.classList.remove('active');
    });
}

// Variation Rows Toggle (Expanded by default, collapses/expands on click)
function toggleVariationsRow(itemId, btn) {
    const rows = document.querySelectorAll('.variations_row_' + itemId);
    if (!rows.length) return;
    const isVisible = (rows[0].style.display !== 'none');
    rows.forEach(r => r.style.display = isVisible ? 'none' : 'table-row');
    const icon = btn.querySelector('i');
    if (icon) {
        icon.className = isVisible ? 'fa-solid fa-chevron-right' : 'fa-solid fa-chevron-down';
    }
}

// Pagination Navigation
function navigatePage(pageNum) {
    document.getElementById('currentPageInput').value = pageNum;
    applyAjaxFilters();
}

function updatePageSize(size) {
    document.getElementById('perPageInput').value = size;
    document.getElementById('currentPageInput').value = 1;
    applyAjaxFilters();
}

// Quick View Drawer
function openQuickView(data) {
    if (!data) return;
    document.getElementById('qv-name').textContent = data.name || 'Product Details';
    document.getElementById('qv-sku').textContent = 'SKU: ' + (data.sku || '') + (data.code ? ' · Code: ' + data.code : '');
    document.getElementById('qv-price').textContent = 'LKR ' + (data.price || '0.00');
    document.getElementById('qv-b2b').textContent = 'LKR ' + (data.b2b || '0.00');
    document.getElementById('qv-qty').textContent = data.qty || '0';
    document.getElementById('qv-category').textContent = data.category || 'Uncategorized';
    document.getElementById('qv-brand').textContent = data.brand || 'N/A';
    document.getElementById('qv-desc').textContent = data.desc || 'No description provided.';
    document.getElementById('qv-img').src = data.img || 'https://placehold.co/180x180/e2e8f0/64748b?text=P';
    
    let badgeHtml = '';
    if (data.status === 'inactive') {
        badgeHtml = '<span class="erp-badge badge-inactive">Inactive</span>';
    } else if (parseInt(data.qty) <= 0) {
        badgeHtml = '<span class="erp-badge badge-outstock">Out of Stock</span>';
    } else if (parseInt(data.qty) <= 5) {
        badgeHtml = '<span class="erp-badge badge-lowstock">Low Stock</span>';
    } else {
        badgeHtml = '<span class="erp-badge badge-instock">In Stock</span>';
    }
    document.getElementById('qv-status').innerHTML = badgeHtml;

    document.getElementById('qv-edit-link').href = '<?= APP_URL ?>/inventory/edit/' + data.id;
    document.getElementById('qv-ledger-link').href = '<?= APP_URL ?>/stockledger/product/' + data.id;

    document.getElementById('quickViewDrawer').classList.add('open');
}

function closeQuickView() {
    document.getElementById('quickViewDrawer').classList.remove('open');
}

// Selection & Bulk Actions
function toggleSelectAll(cb) {
    document.querySelectorAll('.item-select-checkbox').forEach(c => {
        c.checked = cb.checked;
        const tr = c.closest('tr');
        if (tr) tr.classList.toggle('row-selected', cb.checked);
    });
    updateSelection();
}

function updateSelection() {
    const checkboxes = document.querySelectorAll('.item-select-checkbox');
    const selected = [...checkboxes].filter(c => c.checked);
    const toolbar = document.getElementById('bulkEditToolbar');
    const badge = document.getElementById('selectedCountBadge');
    const selectAllCb = document.getElementById('selectAllCheckbox');

    checkboxes.forEach(c => {
        const tr = c.closest('tr');
        if (tr) tr.classList.toggle('row-selected', c.checked);
    });

    if (selected.length > 0) {
        if (badge) badge.textContent = selected.length;
        if (toolbar) toolbar.classList.remove('hidden');
        if (selectAllCb) selectAllCb.checked = (selected.length === checkboxes.length);
    } else {
        if (toolbar) toolbar.classList.add('hidden');
        if (selectAllCb) selectAllCb.checked = false;
    }
}

function clearSelection() {
    document.querySelectorAll('.item-select-checkbox').forEach(c => {
        c.checked = false;
        const tr = c.closest('tr');
        if (tr) tr.classList.remove('row-selected');
    });
    const cb = document.getElementById('selectAllCheckbox');
    if (cb) cb.checked = false;
    updateSelection();
}

// Bulk Edit Modal
function openBulkEditModal() {
    const form = document.getElementById('bulkEditForm');
    form.querySelectorAll('input[name="item_ids[]"]').forEach(i => i.remove());

    let count = 0;
    document.querySelectorAll('.item-select-checkbox').forEach(cb => {
        if (cb.checked) {
            count++;
            const h = document.createElement('input');
            h.type = 'hidden'; h.name = 'item_ids[]'; h.value = cb.value;
            form.appendChild(h);
        }
    });
    document.getElementById('bulkSelectedCount').textContent = count;

    ['category','selling_price','wholesale_price','status'].forEach(f => {
        const ids = {
            category: 'bulkUpdateCategory',
            selling_price: 'bulkUpdateSellingPrice',
            wholesale_price: 'bulkUpdateWholesalePrice',
            status: 'bulkUpdateStatus'
        };
        const cb = document.getElementById(ids[f]);
        if (cb) cb.checked = false;
        toggleBulkField(f);
    });

    const err = document.getElementById('bulkEditErrorContainer');
    if (err) { err.style.display = 'none'; err.textContent = ''; }
    document.getElementById('bulkEditModal').classList.remove('hidden');
}

function closeBulkEditModal() {
    document.getElementById('bulkEditModal').classList.add('hidden');
}

function toggleBulkField(field) {
    const map = {
        category: { cb: 'bulkUpdateCategory', els: ['bulkCategorySelect'] },
        selling_price: { cb: 'bulkUpdateSellingPrice', els: ['bulkSellingPriceType','bulkSellingPriceVal'] },
        wholesale_price: { cb: 'bulkUpdateWholesalePrice', els: ['bulkWholesalePriceType','bulkWholesalePriceVal'] },
        status: { cb: 'bulkUpdateStatus', els: ['bulkStatusSelect'] }
    };
    const cfg = map[field];
    if (!cfg) return;
    const isOn = document.getElementById(cfg.cb)?.checked;
    cfg.els.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.style.pointerEvents = isOn ? 'auto' : 'none';
            el.style.opacity = isOn ? '1' : '0.4';
        }
    });
}

function submitBulkEdit(e) {
    e.preventDefault();
    const form = document.getElementById('bulkEditForm');
    const btn = document.getElementById('bulkSubmitBtn');
    const spinner = document.getElementById('bulkBtnSpinner');
    const err = document.getElementById('bulkEditErrorContainer');

    if (btn) btn.disabled = true;
    if (spinner) spinner.style.display = 'inline-block';
    if (err) { err.style.display = 'none'; err.textContent = ''; }

    const formData = new FormData(form);
    formData.append('csrf_token', getCsrfToken());

    fetch('<?= APP_URL ?>/inventory/bulkUpdate', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': getCsrfToken(),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(r => { if (!r.ok) throw new Error('Bulk update failed'); return r.json(); })
    .then(data => {
        if (data.success) {
            closeBulkEditModal();
            clearSelection();
            applyAjaxFilters();
        } else if (err) {
            err.textContent = data.error || 'Update failed.';
            err.style.display = 'block';
        }
    })
    .catch(ex => {
        if (err) { err.textContent = ex.message; err.style.display = 'block'; }
    })
    .finally(() => {
        if (btn) btn.disabled = false;
        if (spinner) spinner.style.display = 'none';
    });
}

function confirmBulkDelete() {
    const checkboxes = document.querySelectorAll('.item-select-checkbox');
    const selected = [...checkboxes].filter(c => c.checked);
    if (selected.length === 0) return;

    if (!confirm(`Are you sure you want to delete ${selected.length} selected product(s)?`)) {
        return;
    }

    const formData = new FormData();
    formData.append('csrf_token', getCsrfToken());
    selected.forEach(cb => formData.append('item_ids[]', cb.value));

    fetch('<?= APP_URL ?>/inventory/bulk_delete', {
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
            clearSelection();
            applyAjaxFilters();
        } else {
            alert(data.error || data.message || 'Bulk delete failed.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('An error occurred during bulk deletion.');
    });
}

// Single Product Status Toggle (Fixed with CSRF & Slider State Reversion)
function toggleProductStatus(id, targetStatus, switchInput) {
    const formData = new FormData();
    const token = getCsrfToken();
    formData.append('status', targetStatus);
    formData.append('csrf_token', token);

    if (switchInput) switchInput.disabled = true;

    fetch('<?= APP_URL ?>/inventory/toggle_status/' + id, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(r => {
        if (!r.ok) {
            return r.json().then(j => { throw new Error(j.message || 'Server returned status ' + r.status); }).catch(() => {
                throw new Error('Server returned status ' + r.status);
            });
        }
        return r.json();
    })
    .then(data => {
        if (data.success) {
            // Update the status badge cell dynamically
            const badgeCell = document.getElementById('status_badge_' + id);
            if (badgeCell) {
                if (targetStatus === 'inactive') {
                    badgeCell.innerHTML = '<span class="erp-badge badge-inactive">Inactive</span>';
                } else {
                    badgeCell.innerHTML = '<span class="erp-badge badge-instock">Active</span>';
                }
            }
        } else {
            if (switchInput) switchInput.checked = (targetStatus !== 'active');
            alert(data.message || 'Failed to toggle product status.');
        }
    })
    .catch(err => {
        console.error('Status toggle error:', err);
        if (switchInput) switchInput.checked = (targetStatus !== 'active');
        alert('Unable to update status: ' + (err.message || 'Security validation error. Please refresh.'));
    })
    .finally(() => {
        if (switchInput) switchInput.disabled = false;
    });
}

// Single Product Delete Modal & Submission
function confirmDelete(id, name) {
    activeDeleteId = id;
    document.getElementById('deleteItemId').value = id;
    document.getElementById('deleteItemName').textContent = name;
    const err = document.getElementById('deleteErrorContainer');
    if (err) { err.style.display = 'none'; err.textContent = ''; }
    const pw = document.getElementById('deleteAdminPassword');
    if (pw) pw.value = '';
    document.getElementById('deleteProductModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteProductModal').classList.add('hidden');
    activeDeleteId = null;
}

function submitDeleteProduct(e) {
    e.preventDefault();
    if (!activeDeleteId) return;

    const form = document.getElementById('deleteProductForm');
    const btn = document.getElementById('deleteSubmitBtn');
    const spinner = document.getElementById('deleteBtnSpinner');
    const err = document.getElementById('deleteErrorContainer');

    if (btn) btn.disabled = true;
    if (spinner) spinner.style.display = 'inline-block';
    if (err) { err.style.display = 'none'; err.textContent = ''; }

    const formData = new FormData(form);
    formData.append('csrf_token', getCsrfToken());

    fetch('<?= APP_URL ?>/inventory/delete/' + activeDeleteId, {
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
        } else if (err) {
            err.textContent = data.message || 'Failed to delete product.';
            err.style.display = 'block';
        }
    })
    .catch(ex => {
        if (err) { err.textContent = ex.message; err.style.display = 'block'; }
    })
    .finally(() => {
        if (btn) btn.disabled = false;
        if (spinner) spinner.style.display = 'none';
    });
}

// CSV Modal
function openCsvModal() {
    document.getElementById('csvImportModal').classList.remove('hidden');
}
function closeCsvModal() {
    document.getElementById('csvImportModal').classList.add('hidden');
}
function logFileSelection(input) {
    if (input.files && input.files[0]) {
        document.getElementById('fileInfoText').textContent = input.files[0].name + ' (' + (input.files[0].size / 1024).toFixed(1) + ' KB)';
        document.getElementById('importDebugInfo').style.display = 'block';
    }
}
function handleCsvImportSubmit(e) {
    const btn = document.getElementById('csvImportBtn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Importing...';
    }
    return true;
}

// ESC Key closes modals & drawer
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeQuickView();
        closeCsvModal();
        closeDeleteModal();
        closeBulkEditModal();
    }
});
</script>
