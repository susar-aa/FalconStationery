<?php
// Calculate aggregate summaries for Revenue & Expense
$baseRevenue = 0; $baseExpense = 0;
$compRevenue = 0; $compExpense = 0;

foreach ($data['comparison_data'] as $row) {
    if ($row['account_type'] === 'Revenue') {
        $baseRevenue += $row['base_balance'];
        $compRevenue += $row['comp_balance'];
    } elseif ($row['account_type'] === 'Expense') {
        $baseExpense += $row['base_balance'];
        $compExpense += $row['comp_balance'];
    }
}

$baseNet = $baseRevenue - $baseExpense;
$compNet = $compRevenue - $compExpense;
$netVariance = $baseNet - $compNet;
$netPct = 0;
if (round($compNet, 2) != 0.0) {
    $netPct = ($netVariance / abs($compNet)) * 100;
} elseif (round($baseNet, 2) != 0.0) {
    $netPct = 100.0;
}

// Group comparison rows by account type
$groupedData = [];
foreach ($data['comparison_data'] as $row) {
    $type = $row['account_type'] ?? 'Other';
    if (!isset($groupedData[$type])) {
        $groupedData[$type] = [];
    }
    $groupedData[$type][] = $row;
}
?>

<!-- Phosphor Icons Suite -->
<script src="https://unpkg.com/@phosphor-icons/web"></script>

<style>
/* ============================================================
   ERP PROFESSIONAL DESIGN STANDARDS — COMPARATIVE STATEMENT (100VH)
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

.period-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 12px;
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
}

.erp-filter-group-right {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    justify-content: flex-end;
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

.change-up { color: #16a34a; font-weight: 700; }
.change-down { color: #dc2626; font-weight: 700; }
.change-flat { color: #64748b; }

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
            <button type="button" class="action-btn-soft" onclick="exportTableToCSV('comparative_statement_<?= $data['start_date'] ?>_to_<?= $data['end_date'] ?>.csv')">
                <i class="ph ph-file-csv"></i>
                <span>Export CSV</span>
            </button>
            <button type="button" class="action-btn-primary" onclick="window.print()">
                <i class="ph ph-printer"></i>
                <span>Print Report</span>
            </button>
        </div>
    </div>

    <!-- MAIN VIEWER CARD -->
    <div class="viewer-card-container">
        <!-- FILTER TOOLBAR STRIP (MANDATORY TOP-RIGHT CORNER) -->
        <div class="erp-filter-strip">
            <div class="report-header-group">
                <span class="report-header-title">Multi-Period Comparative Statement</span>
                <span class="period-badge">
                    <i class="ph ph-clock-counter-clockwise"></i>
                    <span><?= strtoupper($data['comparison_type']) ?>: <?= date('M d, Y', strtotime($data['start_date'])) ?> vs <?= date('M d, Y', strtotime($data['comp_start_date'])) ?></span>
                </span>
            </div>

            <form method="GET" action="" class="erp-filter-group-right">
                <div class="filter-input-wrap">
                    <span class="filter-label-tiny">TYPE:</span>
                    <select name="comparison_type" class="filter-select-sm">
                        <option value="mom" <?= $data['comparison_type'] === 'mom' ? 'selected' : '' ?>>Month-over-Month (MoM)</option>
                        <option value="yoy" <?= $data['comparison_type'] === 'yoy' ? 'selected' : '' ?>>Year-over-Year (YoY)</option>
                    </select>
                </div>
                <div class="filter-input-wrap">
                    <span class="filter-label-tiny">FROM:</span>
                    <input type="date" name="start_date" class="filter-date-sm" value="<?= htmlspecialchars($data['start_date']) ?>">
                </div>
                <div class="filter-input-wrap">
                    <span class="filter-label-tiny">TO:</span>
                    <input type="date" name="end_date" class="filter-date-sm" value="<?= htmlspecialchars($data['end_date']) ?>">
                </div>
                <button type="submit" class="btn-filter-action">
                    <i class="ph ph-arrows-clockwise"></i>
                    <span>Run Comparison</span>
                </button>
            </form>
        </div>

        <!-- SCROLLABLE TABLE BODY CONTAINER -->
        <div class="erp-table-scroll">
            <table class="erp-data-table" id="comparativeTable">
                <thead>
                    <tr>
                        <th style="width: 35%;">Account Name & Classification</th>
                        <th style="width: 15%; text-align: right;">Base Period (LKR)</th>
                        <th style="width: 15%; text-align: right;">Comparison Period (LKR)</th>
                        <th style="width: 18%; text-align: right;">Variance (LKR)</th>
                        <th style="width: 17%; text-align: right;">% Change</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($groupedData)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px; color: #94a3b8;">
                                <i class="ph ph-chart-line-down" style="font-size: 28px;"></i>
                                <p style="margin-top: 8px;">No comparative account transactions found for this period.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($groupedData as $accType => $rows): ?>
                            <?php 
                                $groupBase = 0; $groupComp = 0;
                                foreach ($rows as $r) {
                                    $groupBase += $r['base_balance'];
                                    $groupComp += $r['comp_balance'];
                                }
                                $groupVar = $groupBase - $groupComp;
                                $groupPct = 0;
                                if (round($groupComp, 2) != 0.0) {
                                    $groupPct = ($groupVar / abs($groupComp)) * 100;
                                } elseif (round($groupBase, 2) != 0.0) {
                                    $groupPct = 100.0;
                                }
                            ?>
                            <tr class="group-section-row">
                                <td colspan="5">
                                    <i class="ph ph-folder" style="margin-right: 6px;"></i>
                                    <span><?= htmlspecialchars($accType) ?> Accounts</span>
                                </td>
                            </tr>
                            <?php foreach ($rows as $r): ?>
                                <tr class="item-row">
                                    <td style="padding-left: 32px;">
                                        <span class="gl-code-pill">GL-<?= htmlspecialchars($r['account_code']) ?></span>
                                        <span><?= htmlspecialchars($r['account_name']) ?></span>
                                    </td>
                                    <td class="num-cell"><?= number_format($r['base_balance'], 2) ?></td>
                                    <td class="num-cell" style="color: #64748b;"><?= number_format($r['comp_balance'], 2) ?></td>
                                    <td class="num-cell <?= $r['variance'] >= 0 ? 'change-up' : 'change-down' ?>">
                                        <?= ($r['variance'] > 0 ? '+' : '') . number_format($r['variance'], 2) ?>
                                    </td>
                                    <td class="num-cell <?= $r['pct_change'] >= 0 ? 'change-up' : 'change-down' ?>">
                                        <?= ($r['pct_change'] > 0 ? '+' : '') . number_format($r['pct_change'], 1) ?>%
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="subtotal-row">
                                <td style="padding-left: 20px;">Subtotal: <?= htmlspecialchars($accType) ?></td>
                                <td class="num-cell"><?= number_format($groupBase, 2) ?></td>
                                <td class="num-cell" style="color: #64748b;"><?= number_format($groupComp, 2) ?></td>
                                <td class="num-cell <?= $groupVar >= 0 ? 'change-up' : 'change-down' ?>">
                                    <?= ($groupVar > 0 ? '+' : '') . number_format($groupVar, 2) ?>
                                </td>
                                <td class="num-cell <?= $groupPct >= 0 ? 'change-up' : 'change-down' ?>">
                                    <?= ($groupPct > 0 ? '+' : '') . number_format($groupPct, 1) ?>%
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- PINNED SUMMARY FOOTER -->
        <div class="erp-pinned-footer">
            <div class="footer-stat">
                <span>BASE NET (REV - EXP):</span>
                <strong>LKR <?= number_format($baseNet, 2) ?></strong>
            </div>
            <div class="footer-stat">
                <span>COMP NET:</span>
                <strong>LKR <?= number_format($compNet, 2) ?></strong>
            </div>
            <div class="footer-stat">
                <span>NET VARIANCE:</span>
                <strong class="<?= $netVariance >= 0 ? 'change-up' : 'change-down' ?>">
                    <?= ($netVariance > 0 ? '+' : '') . number_format($netVariance, 2) ?> (<?= ($netPct > 0 ? '+' : '') . number_format($netPct, 1) ?>%)
                </strong>
            </div>
        </div>
    </div>
</div>

<script>
function exportTableToCSV(filename) {
    const csv = [];
    const rows = document.querySelectorAll('#comparativeTable tr');
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
