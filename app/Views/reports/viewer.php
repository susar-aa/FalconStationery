<?php
$reportKey = $data['reportKey'];
$metadata = $data['metadata'];
$warehouses = $data['warehouses'] ?? [];
$columns = $metadata['columns'] ?? [];
$reportTitle = $metadata['title'] ?? 'ERP Analytical Report';

// Default date range: start of current month to today
$defaultFrom = date('Y-m-01');
$defaultTo = date('Y-m-d');
?>

<!-- Phosphor Icons Suite -->
<script src="https://unpkg.com/@phosphor-icons/web"></script>

<style>
/* ============================================================
   ERP PROFESSIONAL DESIGN STANDARDS — REPORT VIEWER (100VH LOCKED)
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
    --f-main: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    --f-mono: 'SF Mono', ui-monospace, Menlo, Monaco, Consolas, monospace;
    --c-bg: #eef2f6;
    --c-surface: #ffffff;
    --c-border: #e2e8f0;
    --c-text-main: #0f172a;
    --c-text-muted: #64748b;
    --c-navy-dark: #0f172a;
    --c-navy-hover: #1e293b;
    --c-blue-accent: #2563eb;
    --c-blue-light: #eff6ff;
}

body, html {
    margin: 0;
    padding: 0;
    height: 100vh;
    overflow: hidden !important;
    font-family: var(--f-main);
    background-color: var(--c-bg);
}

.report-viewer-viewport {
    height: calc(100vh - 60px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    position: relative;
    padding: 12px 18px 0;
    box-sizing: border-box;
}

/* TOP WORKSPACE NAVIGATION STRIP */
.viewer-top-shelf {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
    flex-shrink: 0;
}

.shelf-left {
    display: flex;
    align-items: center;
    gap: 10px;
}

.btn-circle-back {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #ffffff;
    border: 1px solid var(--c-border);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #334155;
    text-decoration: none;
    font-size: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    transition: all 0.15s ease;
}
.btn-circle-back:hover {
    background: #f8fafc;
    color: var(--c-text-main);
    border-color: #cbd5e1;
}

.hub-title-pill {
    background: #ffffff;
    border: 1px solid var(--c-border);
    border-radius: 999px;
    padding: 7px 18px;
    font-size: 13.5px;
    font-weight: 700;
    color: var(--c-text-main);
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    display: flex;
    align-items: center;
    gap: 8px;
}

.branch-select-pill {
    background: #ffffff;
    border: 1px solid var(--c-border);
    border-radius: 999px;
    padding: 6px 14px;
    font-size: 12.5px;
    font-weight: 600;
    color: #334155;
    display: flex;
    align-items: center;
    gap: 7px;
    cursor: pointer;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}
.branch-select-pill select {
    border: none;
    background: transparent;
    font-family: var(--f-main);
    font-size: 12.5px;
    font-weight: 600;
    color: #334155;
    outline: none;
    cursor: pointer;
}

.shelf-right-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.action-btn-soft {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: #ffffff;
    border: 1px solid var(--c-border);
    border-radius: 8px;
    padding: 7px 14px;
    font-family: var(--f-main);
    font-size: 13px;
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
.action-btn-soft i {
    font-size: 16px;
    color: #64748b;
}

.action-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: var(--c-navy-dark);
    border: 1px solid var(--c-navy-dark);
    border-radius: 8px;
    padding: 7px 16px;
    font-family: var(--f-main);
    font-size: 13px;
    font-weight: 600;
    color: #ffffff;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.15s ease;
    box-shadow: 0 2px 4px rgba(15, 23, 42, 0.12);
}
.action-btn-primary:hover {
    background: var(--c-navy-hover);
}
.action-btn-primary i {
    font-size: 16px;
    color: #ffffff;
}

/* FILTER STRIP & REPORT CARD CONTAINER */
.viewer-card-container {
    flex: 1;
    background: #ffffff;
    border: 1px solid var(--c-border);
    border-radius: 14px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    min-height: 0;
}

/* DEDICATED TOOLBAR DIRECTLY ABOVE TABLE */
.erp-filter-strip {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 22px;
    border-bottom: 1px solid var(--c-border);
    background: #ffffff;
    flex-shrink: 0;
}

.report-header-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--c-text-main);
    letter-spacing: -0.01em;
}

.erp-filter-group-right {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: nowrap;
}

.filter-input-wrap {
    display: flex;
    align-items: center;
    gap: 6px;
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
    padding: 6px 10px;
    border: 1px solid var(--c-border);
    border-radius: 7px;
    font-family: var(--f-main);
    font-size: 12.5px;
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
    gap: 5px;
    padding: 6px 12px;
    border: 1px solid var(--c-border);
    border-radius: 7px;
    background: #f8fafc;
    color: #475569;
    font-family: var(--f-main);
    font-size: 12.5px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
}
.btn-filter-reset:hover {
    background: #f1f5f9;
    color: var(--c-text-main);
    border-color: #cbd5e1;
}

/* SCROLLABLE TABLE VIEWPORT ONLY (ZERO OUTER PAGE SCROLL) */
.erp-table-scroll {
    flex: 1;
    overflow-y: auto;
    overflow-x: auto;
    min-height: 0;
    position: relative;
    background: #ffffff;
}

.erp-data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13.5px;
    text-align: left;
}

.erp-data-table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    background: #f8fafc;
    border-bottom: 1px solid var(--c-border);
    padding: 10px 16px;
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
.erp-data-table thead th.sort-asc::after {
    content: " ↑";
    color: var(--c-blue-accent);
}
.erp-data-table thead th.sort-desc::after {
    content: " ↓";
    color: var(--c-blue-accent);
}

.erp-data-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background-color 0.1s ease;
}
/* MANDATORY ZEBRA STRIPING */
.erp-data-table tbody tr:nth-child(even) {
    background-color: #f8fafc;
}
.erp-data-table tbody tr:hover {
    background-color: #f1f5f9 !important;
}

.erp-data-table tbody td {
    padding: 11px 16px;
    color: #1e293b;
    vertical-align: middle;
}

/* MONOSPACE DATA COLUMNS */
.val-monospace {
    font-family: var(--f-mono);
    font-size: 13px;
    font-weight: 500;
}
.val-currency {
    font-family: var(--f-mono);
    font-size: 13px;
    text-align: right;
    font-weight: 600;
    color: #0f172a;
}
.val-discount {
    font-family: var(--f-mono);
    font-size: 13px;
    text-align: right;
    font-weight: 600;
    color: #dc2626;
}

/* STATUS & PAYMENT BADGES */
.status-pill {
    display: inline-flex;
    align-items: center;
    padding: 3px 9px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.02em;
}
.status-pill-completed, .status-pill-active, .status-pill-paid {
    background: #dcfce7;
    color: #15803d;
}
.status-pill-pending, .status-pill-unpaid {
    background: #ffedd5;
    color: #c2410c;
}
.status-pill-voided, .status-pill-cancelled {
    background: #fee2e2;
    color: #b91c1c;
}

.method-tag {
    font-family: var(--f-main);
    font-size: 12px;
    font-weight: 700;
    color: #2563eb;
    text-transform: uppercase;
}

/* PINNED SUMMARY FOOTER BAR */
.erp-pinned-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 22px;
    border-top: 1px solid var(--c-border);
    background: #ffffff;
    flex-shrink: 0;
    z-index: 10;
}

.footer-summary-label {
    font-size: 12px;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.footer-summary-val {
    font-family: var(--f-mono);
    font-size: 15px;
    font-weight: 700;
    color: var(--c-text-main);
}

.footer-pagination-strip {
    display: flex;
    align-items: center;
    gap: 14px;
    font-size: 12.5px;
    color: #64748b;
}
.pagination-nav-btns {
    display: flex;
    align-items: center;
    gap: 4px;
}
.btn-page-nav {
    padding: 4px 10px;
    border: 1px solid var(--c-border);
    border-radius: 6px;
    background: #ffffff;
    color: #334155;
    font-family: var(--f-main);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
}
.btn-page-nav:hover:not(:disabled) {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: var(--c-text-main);
}
.btn-page-nav:disabled {
    opacity: 0.45;
    cursor: not-allowed;
}

/* CUSTOMIZE COLUMNS MODAL */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(4px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}
.modal-dialog-card {
    width: 440px;
    background: #ffffff;
    border-radius: 14px;
    border: 1px solid var(--c-border);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    overflow: hidden;
    display: flex;
    flex-direction: column;
}
.modal-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
}
.modal-card-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--c-text-main);
}
.btn-modal-close {
    border: none;
    background: transparent;
    font-size: 18px;
    color: #94a3b8;
    cursor: pointer;
}
.modal-card-body {
    padding: 16px 20px;
    max-height: 360px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.col-toggle-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13.5px;
    color: #334155;
    cursor: pointer;
}
.col-toggle-item input[type="checkbox"] {
    width: 16px;
    height: 16px;
    cursor: pointer;
    accent-color: var(--c-blue-accent);
}
.modal-card-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    padding: 14px 20px;
    border-top: 1px solid #f1f5f9;
    background: #f8fafc;
}

/* EXPORT DROPDOWN MENU */
.dropdown-menu-wrap {
    position: relative;
    display: inline-block;
}
.dropdown-export-menu {
    position: absolute;
    top: calc(100% + 4px);
    right: 0;
    background: #ffffff;
    border: 1px solid var(--c-border);
    border-radius: 9px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    width: 160px;
    display: none;
    flex-direction: column;
    padding: 4px;
    z-index: 1000;
}
.dropdown-export-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
    color: #334155;
    text-decoration: none;
    transition: all 0.15s ease;
}
.dropdown-export-item:hover {
    background: #f1f5f9;
    color: var(--c-text-main);
}
</style>

<div class="report-viewer-viewport erp-viewport-view">
    <!-- TOP WORKSPACE NAVIGATION STRIP -->
    <div class="viewer-top-shelf">
        <div class="shelf-left">
            <a href="<?= APP_URL ?>/report" class="btn-circle-back" title="Back to Reports Hub">
                <i class="ph ph-arrow-left"></i>
            </a>
            <div class="hub-title-pill">
                <span>Central Reports & Financial Statements</span>
            </div>
            <div class="branch-select-pill">
                <i class="ph ph-storefront"></i>
                <select id="headerBranchSelect">
                    <?php if (!empty($warehouses)): ?>
                        <?php foreach ($warehouses as $wh): ?>
                            <option value="<?= $wh->id ?>"><?= htmlspecialchars($wh->name) ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="1">Kandy Branch (KKK)</option>
                        <option value="2">Colombo Main Hub</option>
                    <?php endif; ?>
                </select>
                <i class="ph ph-caret-down" style="font-size: 12px; color: #94a3b8;"></i>
            </div>
        </div>

        <div class="shelf-right-actions">
            <a href="<?= APP_URL ?>/report" class="action-btn-soft">
                <i class="ph ph-arrow-left"></i>
                <span>Reports List</span>
            </a>
            <button type="button" class="action-btn-soft" id="btnOpenCustomizeCols">
                <i class="ph ph-columns"></i>
                <span>Customize Columns</span>
            </button>
            <div class="dropdown-menu-wrap">
                <button type="button" class="action-btn-soft" id="btnToggleExportMenu">
                    <i class="ph ph-download-simple"></i>
                    <span>Export Options</span>
                    <i class="ph ph-caret-down" style="font-size: 12px;"></i>
                </button>
                <div class="dropdown-export-menu" id="exportDropdownMenu">
                    <a href="javascript:void(0)" class="dropdown-export-item" onclick="triggerExport('csv')">
                        <i class="ph ph-file-csv"></i>
                        <span>Export CSV</span>
                    </a>
                    <a href="javascript:void(0)" class="dropdown-export-item" onclick="triggerExport('excel')">
                        <i class="ph ph-file-xls"></i>
                        <span>Export Excel</span>
                    </a>
                    <a href="javascript:void(0)" class="dropdown-export-item" onclick="triggerExport('word')">
                        <i class="ph ph-file-doc"></i>
                        <span>Export Word</span>
                    </a>
                    <a href="javascript:void(0)" class="dropdown-export-item" onclick="triggerExport('json')">
                        <i class="ph ph-code"></i>
                        <span>Export JSON</span>
                    </a>
                </div>
            </div>
            <button type="button" class="action-btn-primary" onclick="triggerPrint()">
                <i class="ph ph-printer"></i>
                <span>Print Statement</span>
            </button>
        </div>
    </div>

    <!-- MAIN VIEWER CARD -->
    <div class="viewer-card-container">
        <!-- FILTER TOOLBAR STRIP (MANDATORY TOP-RIGHT CORNER) -->
        <div class="erp-filter-strip">
            <div class="report-header-title" id="displayReportTitle"><?= htmlspecialchars($reportTitle) ?></div>

            <div class="erp-filter-group-right">
                <!-- BRANCH DROPDOWN -->
                <div class="filter-input-wrap">
                    <span class="filter-label-tiny">BRANCH:</span>
                    <select id="filterBranch" class="filter-select-sm">
                        <option value="">All Branches</option>
                        <?php if (!empty($warehouses)): ?>
                            <?php foreach ($warehouses as $wh): ?>
                                <option value="<?= $wh->id ?>"><?= htmlspecialchars($wh->name) ?></option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="1" selected>Kandy Branch</option>
                            <option value="2">Colombo Main Hub</option>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- PRESET RANGE DROPDOWN -->
                <div class="filter-input-wrap">
                    <span class="filter-label-tiny">RANGE:</span>
                    <select id="filterRangePreset" class="filter-select-sm">
                        <option value="this_month" selected>This Month</option>
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="this_week">This Week</option>
                        <option value="this_quarter">This Quarter</option>
                        <option value="this_year">This Year</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>

                <!-- FROM DATE -->
                <div class="filter-input-wrap">
                    <span class="filter-label-tiny">FROM:</span>
                    <input type="date" id="filterStartDate" class="filter-date-sm" value="<?= $defaultFrom ?>">
                </div>

                <!-- TO DATE -->
                <div class="filter-input-wrap">
                    <span class="filter-label-tiny">TO:</span>
                    <input type="date" id="filterEndDate" class="filter-date-sm" value="<?= $defaultTo ?>">
                </div>

                <!-- RESET BUTTON -->
                <button type="button" class="btn-filter-reset" id="btnResetFilters" title="Reset all filters">
                    <i class="ph ph-arrow-counter-clockwise"></i>
                    <span>Reset</span>
                </button>
            </div>
        </div>

        <!-- SCROLLABLE TABLE BODY CONTAINER (ONLY ROWS SCROLL) -->
        <div class="erp-table-scroll">
            <table class="erp-data-table" id="reportTable">
                <thead>
                    <tr id="tableHeaderRow">
                        <?php foreach ($columns as $colKey => $colDef): ?>
                            <th data-col="<?= $colKey ?>" class="<?= (isset($colDef['align']) && $colDef['align'] === 'right') ? 'text-right' : '' ?>">
                                <?= htmlspecialchars($colDef['label']) ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody id="reportTableBody">
                    <tr>
                        <td colspan="<?= count($columns) ?>" style="text-align: center; padding: 40px; color: #94a3b8;">
                            <i class="ph ph-spinner ph-spin" style="font-size: 24px;"></i>
                            <p style="margin-top: 8px;">Loading report data...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- PINNED SUMMARY FOOTER -->
        <div class="erp-pinned-footer">
            <div class="footer-summary-label" id="footerTotalLabel">
                TOTAL <?= strtoupper(htmlspecialchars($reportTitle)) ?> NET REVENUE
            </div>

            <div class="footer-pagination-strip">
                <span id="paginationInfo">Showing 0 - 0 of 0</span>
                <div class="pagination-nav-btns">
                    <button type="button" class="btn-page-nav" id="btnPrevPage" disabled>
                        <i class="ph ph-caret-left"></i> Prev
                    </button>
                    <button type="button" class="btn-page-nav" id="btnNextPage" disabled>
                        Next <i class="ph ph-caret-right"></i>
                    </button>
                </div>
            </div>

            <div class="footer-summary-val" id="footerGrandTotalVal">
                0.00
            </div>
        </div>
    </div>
</div>

<!-- CUSTOMIZE COLUMNS MODAL -->
<div class="modal-overlay" id="customizeColsModal">
    <div class="modal-dialog-card">
        <div class="modal-card-header">
            <span class="modal-card-title">Customize Columns</span>
            <button type="button" class="btn-modal-close" id="btnCloseCustomizeCols">&times;</button>
        </div>
        <div class="modal-card-body" id="colsCheckboxContainer">
            <?php foreach ($columns as $colKey => $colDef): ?>
                <label class="col-toggle-item">
                    <input type="checkbox" value="<?= $colKey ?>" checked class="col-check-box">
                    <span><?= htmlspecialchars($colDef['label']) ?></span>
                </label>
            <?php endforeach; ?>
        </div>
        <div class="modal-card-footer">
            <button type="button" class="action-btn-soft" id="btnResetCols">Reset Default</button>
            <button type="button" class="action-btn-primary" id="btnApplyCols">Apply Columns</button>
        </div>
    </div>
</div>

<script>
const REPORT_KEY = <?= json_encode($reportKey) ?>;
const COLUMNS_DEF = <?= json_encode($columns) ?>;
const BASE_FETCH_URL = '<?= APP_URL ?>/report/fetch_data';
const BASE_EXPORT_URL = '<?= APP_URL ?>/report/export/' + REPORT_KEY;

let currentPage = 1;
const pageSize = 50;
let currentSortCol = null;
let currentSortDir = 'ASC';
let hiddenCols = new Set();

// Load stored hidden columns preference
try {
    const savedHidden = localStorage.getItem('erp_hidden_cols_' + REPORT_KEY);
    if (savedHidden) {
        hiddenCols = new Set(JSON.parse(savedHidden));
    }
} catch(e) {}

function formatCurrency(num) {
    const val = parseFloat(num) || 0;
    return val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function loadReportData() {
    const branch = document.getElementById('filterBranch').value;
    const startDate = document.getElementById('filterStartDate').value;
    const endDate = document.getElementById('filterEndDate').value;

    const params = new URLSearchParams({
        report: REPORT_KEY,
        page: currentPage,
        limit: pageSize,
        branch: branch,
        warehouse: branch,
        start_date: startDate,
        end_date: endDate
    });

    if (currentSortCol) {
        params.append('sort_col', currentSortCol);
        params.append('sort_dir', currentSortDir);
    }

    const tbody = document.getElementById('reportTableBody');
    tbody.innerHTML = `
        <tr>
            <td colspan="${Object.keys(COLUMNS_DEF).length}" style="text-align: center; padding: 40px; color: #94a3b8;">
                <i class="ph ph-spinner ph-spin" style="font-size: 24px;"></i>
                <p style="margin-top: 8px;">Loading report data...</p>
            </td>
        </tr>
    `;

    fetch(BASE_FETCH_URL + '?' + params.toString())
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="${Object.keys(COLUMNS_DEF).length}" style="text-align: center; padding: 30px; color: #ef4444;">
                            <i class="ph ph-warning-circle" style="font-size: 24px;"></i>
                            <p style="margin-top: 6px;">${data.message || 'Error executing query'}</p>
                        </td>
                    </tr>
                `;
                return;
            }

            renderRows(data.rows, data.total_rows, data.grand_totals);
        })
        .catch(err => {
            tbody.innerHTML = `
                <tr>
                    <td colspan="${Object.keys(COLUMNS_DEF).length}" style="text-align: center; padding: 30px; color: #ef4444;">
                        <i class="ph ph-warning-circle" style="font-size: 24px;"></i>
                        <p style="margin-top: 6px;">Failed to load report dataset: ${err.message}</p>
                    </td>
                </tr>
            `;
        });
}

function renderRows(rows, totalRows, grandTotals) {
    const tbody = document.getElementById('reportTableBody');

    if (!rows || rows.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="${Object.keys(COLUMNS_DEF).length}" style="text-align: center; padding: 40px; color: #94a3b8;">
                    <i class="ph ph-file-dashed" style="font-size: 32px;"></i>
                    <p style="margin-top: 8px;">No records found for the selected period.</p>
                </td>
            </tr>
        `;
        document.getElementById('paginationInfo').textContent = 'Showing 0 - 0 of 0';
        document.getElementById('btnPrevPage').disabled = true;
        document.getElementById('btnNextPage').disabled = true;
        document.getElementById('footerGrandTotalVal').textContent = '0.00';
        return;
    }

    let html = '';
    rows.forEach(row => {
        html += '<tr>';
        for (const [colKey, colDef] of Object.entries(COLUMNS_DEF)) {
            if (hiddenCols.has(colKey)) continue;

            let val = row[colKey] !== undefined ? row[colKey] : '';
            let cellHtml = '';
            let alignClass = (colDef.align === 'right') ? 'style="text-align: right;"' : '';

            if (colDef.type === 'currency') {
                const isDiscount = (colKey.includes('discount'));
                const cssClass = isDiscount ? 'val-discount' : 'val-currency';
                cellHtml = `<span class="${cssClass}">${formatCurrency(val)}</span>`;
            } else if (colDef.type === 'number') {
                cellHtml = `<span class="val-monospace">${val !== null ? val : '0'}</span>`;
            } else if (colDef.type === 'badge') {
                const statusStr = String(val).toUpperCase();
                let pillClass = 'status-pill-completed';
                if (statusStr.includes('PENDING') || statusStr.includes('UNPAID')) {
                    pillClass = 'status-pill-pending';
                } else if (statusStr.includes('VOID') || statusStr.includes('CANCEL')) {
                    pillClass = 'status-pill-voided';
                } else if (statusStr.includes('CARD') || statusStr.includes('BANK') || statusStr.includes('CASH')) {
                    cellHtml = `<span class="method-tag">${escapeHtml(val)}</span>`;
                }

                if (!cellHtml) {
                    cellHtml = `<span class="status-pill ${pillClass}">${escapeHtml(val)}</span>`;
                }
            } else {
                cellHtml = escapeHtml(val);
            }

            html += `<td ${alignClass}>${cellHtml}</td>`;
        }
        html += '</tr>';
    });

    tbody.innerHTML = html;

    // Update Pagination Controls
    const startIdx = (currentPage - 1) * pageSize + 1;
    const endIdx = Math.min(currentPage * pageSize, totalRows);
    document.getElementById('paginationInfo').textContent = `Showing ${startIdx} - ${endIdx} of ${totalRows.toLocaleString()}`;
    document.getElementById('btnPrevPage').disabled = (currentPage <= 1);
    document.getElementById('btnNextPage').disabled = (endIdx >= totalRows);

    // Update Grand Total in Pinned Footer
    let primarySum = 0;
    if (grandTotals) {
        if (grandTotals['grand_total'] !== undefined) {
            primarySum = grandTotals['grand_total'];
        } else if (grandTotals['net_revenue'] !== undefined) {
            primarySum = grandTotals['net_revenue'];
        } else if (grandTotals['total_revenue'] !== undefined) {
            primarySum = grandTotals['total_revenue'];
        } else if (grandTotals['val_cost'] !== undefined) {
            primarySum = grandTotals['val_cost'];
        } else if (grandTotals['total_valuation'] !== undefined) {
            primarySum = grandTotals['total_valuation'];
        } else if (grandTotals['total_sales'] !== undefined) {
            primarySum = grandTotals['total_sales'];
        } else if (grandTotals['total_value'] !== undefined) {
            primarySum = grandTotals['total_value'];
        } else if (grandTotals['total_amount'] !== undefined) {
            primarySum = grandTotals['total_amount'];
        } else if (grandTotals['amount'] !== undefined) {
            primarySum = grandTotals['amount'];
        } else if (grandTotals['total'] !== undefined) {
            primarySum = grandTotals['total'];
        } else {
            // fallback to first grand total key if available
            const keys = Object.keys(grandTotals);
            if (keys.length > 0) {
                primarySum = grandTotals[keys[0]];
            }
        }
    }
    document.getElementById('footerGrandTotalVal').textContent = formatCurrency(primarySum);
}

function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    return String(text)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function updatePresetDates(preset) {
    const startInput = document.getElementById('filterStartDate');
    const endInput = document.getElementById('filterEndDate');
    const now = new Date();

    if (preset === 'today') {
        const dStr = now.toISOString().split('T')[0];
        startInput.value = dStr;
        endInput.value = dStr;
    } else if (preset === 'yesterday') {
        const y = new Date(now);
        y.setDate(y.getDate() - 1);
        const dStr = y.toISOString().split('T')[0];
        startInput.value = dStr;
        endInput.value = dStr;
    } else if (preset === 'this_week') {
        const first = new Date(now.setDate(now.getDate() - now.getDay() + 1));
        const last = new Date();
        startInput.value = first.toISOString().split('T')[0];
        endInput.value = last.toISOString().split('T')[0];
    } else if (preset === 'this_month') {
        const y = now.getFullYear();
        const m = String(now.getMonth() + 1).padStart(2, '0');
        startInput.value = `${y}-${m}-01`;
        endInput.value = new Date().toISOString().split('T')[0];
    } else if (preset === 'this_quarter') {
        const currentQuarter = Math.floor((now.getMonth() + 3) / 3);
        const startMonth = String((currentQuarter - 1) * 3 + 1).padStart(2, '0');
        startInput.value = `${now.getFullYear()}-${startMonth}-01`;
        endInput.value = new Date().toISOString().split('T')[0];
    } else if (preset === 'this_year') {
        startInput.value = `${now.getFullYear()}-01-01`;
        endInput.value = new Date().toISOString().split('T')[0];
    }
}

function triggerExport(format) {
    const branch = document.getElementById('filterBranch').value;
    const startDate = document.getElementById('filterStartDate').value;
    const endDate = document.getElementById('filterEndDate').value;

    const params = new URLSearchParams({
        format: format,
        branch: branch,
        warehouse: branch,
        start_date: startDate,
        end_date: endDate
    });

    window.location.href = BASE_EXPORT_URL + '?' + params.toString();
    document.getElementById('exportDropdownMenu').style.display = 'none';
}

function triggerPrint() {
    window.print();
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    document.body.classList.add('erp-locked');
    // Range preset change
    document.getElementById('filterRangePreset').addEventListener('change', function() {
        if (this.value !== 'custom') {
            updatePresetDates(this.value);
            currentPage = 1;
            loadReportData();
        }
    });

    // Date inputs change
    document.getElementById('filterStartDate').addEventListener('change', function() {
        document.getElementById('filterRangePreset').value = 'custom';
        currentPage = 1;
        loadReportData();
    });
    document.getElementById('filterEndDate').addEventListener('change', function() {
        document.getElementById('filterRangePreset').value = 'custom';
        currentPage = 1;
        loadReportData();
    });

    // Branch filter change
    document.getElementById('filterBranch').addEventListener('change', function() {
        currentPage = 1;
        loadReportData();
    });
    document.getElementById('headerBranchSelect').addEventListener('change', function() {
        document.getElementById('filterBranch').value = this.value;
        currentPage = 1;
        loadReportData();
    });

    // Reset Filters button
    document.getElementById('btnResetFilters').addEventListener('click', function() {
        document.getElementById('filterBranch').value = '';
        document.getElementById('filterRangePreset').value = 'this_month';
        updatePresetDates('this_month');
        currentPage = 1;
        loadReportData();
    });

    // Pagination buttons
    document.getElementById('btnPrevPage').addEventListener('click', function() {
        if (currentPage > 1) {
            currentPage--;
            loadReportData();
        }
    });
    document.getElementById('btnNextPage').addEventListener('click', function() {
        currentPage++;
        loadReportData();
    });

    // Column sorting
    document.querySelectorAll('#tableHeaderRow th').forEach(th => {
        th.addEventListener('click', function() {
            const col = this.getAttribute('data-col');
            if (currentSortCol === col) {
                currentSortDir = (currentSortDir === 'ASC') ? 'DESC' : 'ASC';
            } else {
                currentSortCol = col;
                currentSortDir = 'ASC';
            }

            document.querySelectorAll('#tableHeaderRow th').forEach(t => {
                t.classList.remove('sort-asc', 'sort-desc');
            });
            this.classList.add(currentSortDir === 'ASC' ? 'sort-asc' : 'sort-desc');

            loadReportData();
        });
    });

    // Export options dropdown toggle
    const btnToggleExport = document.getElementById('btnToggleExportMenu');
    const exportMenu = document.getElementById('exportDropdownMenu');
    btnToggleExport.addEventListener('click', function(e) {
        e.stopPropagation();
        exportMenu.style.display = (exportMenu.style.display === 'flex') ? 'none' : 'flex';
    });
    document.addEventListener('click', function(e) {
        if (!exportMenu.contains(e.target) && e.target !== btnToggleExport) {
            exportMenu.style.display = 'none';
        }
    });

    // Customize columns modal
    const modal = document.getElementById('customizeColsModal');
    document.getElementById('btnOpenCustomizeCols').addEventListener('click', function() {
        // sync checkbox states
        document.querySelectorAll('.col-check-box').forEach(cb => {
            cb.checked = !hiddenCols.has(cb.value);
        });
        modal.style.display = 'flex';
    });
    document.getElementById('btnCloseCustomizeCols').addEventListener('click', function() {
        modal.style.display = 'none';
    });
    document.getElementById('btnResetCols').addEventListener('click', function() {
        hiddenCols.clear();
        localStorage.removeItem('erp_hidden_cols_' + REPORT_KEY);
        document.querySelectorAll('.col-check-box').forEach(cb => cb.checked = true);
        applyColumnVisibility();
        modal.style.display = 'none';
    });
    document.getElementById('btnApplyCols').addEventListener('click', function() {
        hiddenCols.clear();
        document.querySelectorAll('.col-check-box').forEach(cb => {
            if (!cb.checked) {
                hiddenCols.add(cb.value);
            }
        });
        localStorage.setItem('erp_hidden_cols_' + REPORT_KEY, JSON.stringify(Array.from(hiddenCols)));
        applyColumnVisibility();
        modal.style.display = 'none';
        loadReportData();
    });

    function applyColumnVisibility() {
        document.querySelectorAll('#tableHeaderRow th').forEach(th => {
            const col = th.getAttribute('data-col');
            if (hiddenCols.has(col)) {
                th.style.display = 'none';
            } else {
                th.style.display = '';
            }
        });
    }

    applyColumnVisibility();

    // Initial data load
    loadReportData();
});
</script>
