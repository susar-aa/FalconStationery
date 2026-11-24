<!-- Phosphor Icons Suite -->
<script src="https://unpkg.com/@phosphor-icons/web"></script>

<style>
/* ============================================================
   ERP PROFESSIONAL DESIGN STANDARDS — BALANCE SHEET (100VH LOCKED)
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
    justify-content: flex-end;
    margin-bottom: 12px;
    flex-shrink: 0;
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
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}
.action-btn-primary:hover {
    background: #1e293b;
    border-color: #1e293b;
}
.action-btn-primary i {
    font-size: 16px;
    color: #94a3b8;
}

/* MAIN CARD CONTAINER */
.viewer-card-container {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: #ffffff;
    border: 1px solid var(--c-border);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04);
    min-height: 0;
    margin-bottom: 8px;
}

/* FILTER STRIP (MANDATORY TOP-RIGHT CORNER) */
.erp-filter-strip {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 20px;
    border-bottom: 1px solid var(--c-border);
    background: #ffffff;
    flex-shrink: 0;
}

.report-header-group {
    display: flex;
    align-items: center;
    gap: 12px;
}

.report-header-title {
    font-size: 17px;
    font-weight: 700;
    color: var(--c-text-main);
    letter-spacing: -0.01em;
}

.integrity-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 12px;
}
.badge-ok {
    background: #ecfdf5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}
.badge-warn {
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.erp-filter-group-right {
    display: flex;
    align-items: center;
    gap: 10px;
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
.filter-date-sm {
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
.filter-date-sm:focus {
    border-color: var(--c-blue-accent);
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
}

.btn-filter-action {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 14px;
    border: 1px solid var(--c-border);
    border-radius: 7px;
    background: #2563eb;
    color: #ffffff;
    font-family: var(--f-main);
    font-size: 12.5px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
}
.btn-filter-action:hover {
    background: #1d4ed8;
}

/* SCROLLABLE TABLE BODY */
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
    font-size: 13px;
    text-align: left;
}

.erp-data-table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    background: #f8fafc;
    border-bottom: 1px solid var(--c-border);
    padding: 10px 18px;
    font-size: 11px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    white-space: nowrap;
}

/* Group Headers */
.group-section-row td {
    background: #f1f5f9;
    padding: 10px 18px;
    font-size: 13px;
    font-weight: 700;
    color: #1e293b;
    border-top: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

.group-badge-total {
    float: right;
    font-family: var(--f-mono);
    font-weight: 700;
    color: #1e293b;
    font-size: 13.5px;
}

/* Account Rows with Zebra Striping */
.erp-data-table tbody tr.item-row {
    border-bottom: 1px solid #f1f5f9;
    transition: background-color 0.1s ease;
}
.erp-data-table tbody tr.item-row:nth-child(even) {
    background-color: #fafbfc;
}
.erp-data-table tbody tr.item-row:hover {
    background-color: #f1f5f9;
}
.erp-data-table tbody tr.item-row td {
    padding: 9px 18px;
    color: #334155;
    font-size: 13px;
}

.gl-code-pill {
    display: inline-block;
    padding: 1px 6px;
    border-radius: 4px;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    color: #475569;
    font-family: var(--f-mono);
    font-size: 11px;
    font-weight: 600;
    margin-right: 8px;
}

.num-cell {
    text-align: right;
    font-family: var(--f-mono);
    font-size: 13px;
    font-weight: 600;
    color: #0f172a;
}

/* Subtotal & Grand Total Rows */
.subtotal-row td {
    background: #f8fafc;
    border-top: 1px solid #cbd5e1;
    border-bottom: 2px solid #94a3b8;
    padding: 9px 18px;
    font-weight: 700;
    font-size: 13px;
    color: #0f172a;
}
.subtotal-row .num-cell {
    font-weight: 700;
    color: #0f172a;
}

.grand-total-row td {
    background: #eff6ff;
    border-top: 2px solid #2563eb;
    border-bottom: 3px double #2563eb;
    padding: 12px 18px;
    font-weight: 800;
    font-size: 14px;
    color: #1e40af;
}
.grand-total-row .num-cell {
    font-size: 14.5px;
    font-weight: 800;
    color: #1e40af;
}

/* PINNED FOOTER */
.erp-pinned-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 20px;
    border-top: 1px solid var(--c-border);
    background: #f8fafc;
    flex-shrink: 0;
}
.footer-stat {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    font-weight: 600;
    color: #475569;
}
.footer-stat strong {
    font-family: var(--f-mono);
    color: #0f172a;
    font-size: 13px;
}

@media print {
    .glass-nav, .viewer-top-shelf, .erp-filter-strip, .erp-pinned-footer, button, a {
        display: none !important;
    }
    body, html, .report-viewer-viewport, .viewer-card-container, .erp-table-scroll {
        height: auto !important;
        overflow: visible !important;
        background: #fff !important;
        padding: 0 !important;
        margin: 0 !important;
        border: none !important;
        box-shadow: none !important;
    }
    .erp-data-table thead th {
        position: static !important;
    }
}
</style>

<div class="report-viewer-viewport erp-viewport-view">
    <!-- TOP WORKSPACE NAVIGATION STRIP -->
    <div class="viewer-top-shelf">
        <div class="shelf-right-actions">
            <a href="<?= APP_URL ?>/report" class="action-btn-soft">
                <i class="ph ph-arrow-left"></i>
                <span>Reports List</span>
            </a>
            <button type="button" class="action-btn-soft" onclick="exportTableToCSV('balance_sheet_<?= $data['end_date'] ?? date('Y-m-d') ?>.csv')">
                <i class="ph ph-file-csv"></i>
                <span>Export CSV</span>
            </button>
            <button type="button" class="action-btn-primary" onclick="window.print()">
                <i class="ph ph-printer"></i>
                <span>Print Statement</span>
            </button>
        </div>
    </div>

    <!-- MAIN VIEWER CARD -->
    <div class="viewer-card-container">
        <!-- FILTER TOOLBAR STRIP (MANDATORY TOP-RIGHT CORNER) -->
        <div class="erp-filter-strip">
            <div class="report-header-group">
                <span class="report-header-title">Balance Sheet</span>
                <?php if (round($data['total_assets'], 2) === round($data['total_liabilities_equity'], 2)): ?>
                    <span class="integrity-badge badge-ok">
                        <i class="ph ph-check-circle" style="font-size: 14px;"></i>
                        <span>Balanced</span>
                    </span>
                <?php else: ?>
                    <span class="integrity-badge badge-warn">
                        <i class="ph ph-warning-circle" style="font-size: 14px;"></i>
                        <span>Out of Balance (LKR <?= number_format(abs($data['total_assets'] - $data['total_liabilities_equity']), 2) ?>)</span>
                    </span>
                <?php endif; ?>
            </div>

            <form method="GET" action="" class="erp-filter-group-right">
                <div class="filter-input-wrap">
                    <span class="filter-label-tiny">AS OF DATE:</span>
                    <input type="date" name="end_date" class="filter-date-sm" value="<?= htmlspecialchars($data['end_date'] ?? date('Y-m-d')) ?>">
                </div>
                <button type="submit" class="btn-filter-action">
                    <i class="ph ph-magnifying-glass"></i>
                    <span>Update</span>
                </button>
            </form>
        </div>

        <!-- SCROLLABLE TABLE BODY CONTAINER -->
        <div class="erp-table-scroll">
            <table class="erp-data-table" id="balanceSheetTable">
                <thead>
                    <tr>
                        <th style="width: 65%;">Account Name & Classification</th>
                        <th style="width: 35%; text-align: right;">Balance (LKR)</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- ================= ASSETS SECTION ================= -->
                    <tr class="group-section-row">
                        <td>
                            <i class="ph ph-wallet" style="margin-right: 6px;"></i>
                            <span>Assets</span>
                        </td>
                        <td>
                            <span class="group-badge-total"><?= number_format($data['total_assets'], 2) ?></span>
                        </td>
                    </tr>
                    <?php if (empty($data['assets'])): ?>
                        <tr class="item-row">
                            <td colspan="2" style="padding-left: 32px; color: #94a3b8; font-style: italic;">No asset accounts recorded.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data['assets'] as $acc): ?>
                            <tr class="item-row">
                                <td style="padding-left: 32px;">
                                    <span class="gl-code-pill">GL-<?= htmlspecialchars($acc->account_code) ?></span>
                                    <span><?= htmlspecialchars($acc->account_name) ?></span>
                                </td>
                                <td class="num-cell"><?= number_format($acc->balance, 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <tr class="subtotal-row">
                        <td style="padding-left: 20px;">Total Assets</td>
                        <td class="num-cell"><?= number_format($data['total_assets'], 2) ?></td>
                    </tr>

                    <!-- ================= LIABILITIES SECTION ================= -->
                    <tr class="group-section-row">
                        <td>
                            <i class="ph ph-scales" style="margin-right: 6px;"></i>
                            <span>Liabilities</span>
                        </td>
                        <td>
                            <span class="group-badge-total"><?= number_format($data['total_liabilities'], 2) ?></span>
                        </td>
                    </tr>
                    <?php if (empty($data['liabilities'])): ?>
                        <tr class="item-row">
                            <td colspan="2" style="padding-left: 32px; color: #94a3b8; font-style: italic;">No liability accounts recorded.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data['liabilities'] as $acc): ?>
                            <tr class="item-row">
                                <td style="padding-left: 32px;">
                                    <span class="gl-code-pill">GL-<?= htmlspecialchars($acc->account_code) ?></span>
                                    <span><?= htmlspecialchars($acc->account_name) ?></span>
                                </td>
                                <td class="num-cell"><?= number_format($acc->balance, 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <tr class="subtotal-row">
                        <td style="padding-left: 20px;">Total Liabilities</td>
                        <td class="num-cell"><?= number_format($data['total_liabilities'], 2) ?></td>
                    </tr>

                    <!-- ================= EQUITY SECTION ================= -->
                    <tr class="group-section-row">
                        <td>
                            <i class="ph ph-chart-pie" style="margin-right: 6px;"></i>
                            <span>Equity</span>
                        </td>
                        <td>
                            <span class="group-badge-total"><?= number_format($data['total_equity'], 2) ?></span>
                        </td>
                    </tr>
                    <?php if (!empty($data['equities'])): ?>
                        <?php foreach ($data['equities'] as $acc): ?>
                            <tr class="item-row">
                                <td style="padding-left: 32px;">
                                    <span class="gl-code-pill">GL-<?= htmlspecialchars($acc->account_code) ?></span>
                                    <span><?= htmlspecialchars($acc->account_name) ?></span>
                                </td>
                                <td class="num-cell"><?= number_format($acc->balance, 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <tr class="item-row">
                        <td style="padding-left: 32px;">
                            <span class="gl-code-pill">P&L</span>
                            <span>Net Income / (Loss) (Current Operating Period)</span>
                        </td>
                        <td class="num-cell"><?= number_format($data['net_income'], 2) ?></td>
                    </tr>
                    <tr class="subtotal-row">
                        <td style="padding-left: 20px;">Total Equity</td>
                        <td class="num-cell"><?= number_format($data['total_equity'], 2) ?></td>
                    </tr>

                    <!-- ================= GRAND TOTAL: LIABILITIES & EQUITY ================= -->
                    <tr class="grand-total-row">
                        <td>Total Liabilities & Equity</td>
                        <td class="num-cell"><?= number_format($data['total_liabilities_equity'], 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- PINNED SUMMARY FOOTER -->
        <div class="erp-pinned-footer">
            <div class="footer-stat">
                <span>TOTAL ASSETS:</span>
                <strong>LKR <?= number_format($data['total_assets'], 2) ?></strong>
            </div>
            <div class="footer-stat">
                <span>TOTAL LIABILITIES & EQUITY:</span>
                <strong>LKR <?= number_format($data['total_liabilities_equity'], 2) ?></strong>
            </div>
        </div>
    </div>
</div>

<script>
function exportTableToCSV(filename) {
    const csv = [];
    const rows = document.querySelectorAll('#balanceSheetTable tr');
    for (let i = 0; i < rows.length; i++) {
        const row = [];
        const cols = rows[i].querySelectorAll('td, th');
        for (let j = 0; j < cols.length; j++) {
            let text = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, '').replace(/(\s\s+)/g, ' ').trim();
            text = text.replace(/"/g, '""');
            row.push('"' + text + '"');
        }
        if (row.length > 0) csv.push(row.join(','));
    }
    const csvFile = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const downloadLink = document.createElement('a');
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
</script>
