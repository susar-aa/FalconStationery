<?php
/**
 * Journal Entries Workspace - Curtiss & Falcon Stationery ERP
 * Viewport-Locked (100vh), Top-Right Filter Toolbar, SF Pro Display & Zebra Striping
 */
$stats = $data['stats'] ?? (object)[
    'total_count' => $data['total_entries'] ?? 0,
    'posted_count' => 0,
    'voided_count' => 0,
    'draft_count' => 0
];
$totalEntries = (int)($data['total_entries'] ?? 0);
$postedCount = (int)($stats->posted_count ?? 0);
$voidedCount = (int)($stats->voided_count ?? 0);

$search = $data['filters']['search'] ?? '';
$status = $data['filters']['status'] ?? 'All';
$source = $data['filters']['source'] ?? 'manual';
$startDate = $data['filters']['start_date'] ?? '';
$endDate = $data['filters']['end_date'] ?? '';
$limit = (int)($data['limit'] ?? 25);
$page = (int)($data['page'] ?? 1);
$totalPages = (int)($data['total_pages'] ?? 1);

$startRecord = $totalEntries > 0 ? (($page - 1) * $limit + 1) : 0;
$endRecord = min($totalEntries, $page * $limit);

$qArgs = [];
if (!empty($search)) $qArgs['search'] = $search;
if ($status !== 'All') $qArgs['status'] = $status;
if ($source !== 'manual') $qArgs['source'] = $source;
if (!empty($startDate)) $qArgs['start_date'] = $startDate;
if (!empty($endDate)) $qArgs['end_date'] = $endDate;
if ($limit !== 25) $qArgs['limit'] = $limit;
$qString = http_build_query($qArgs);
$qPrefix = !empty($qString) ? '&' . $qString : '';

$csrfToken = $_SESSION['csrf_token'] ?? '';
$reopenModalOnError = (!empty($data['error']) && $_SERVER['REQUEST_METHOD'] === 'POST');
?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
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
    --f-main: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    --f-mono: 'SF Mono', ui-monospace, Menlo, Monaco, Consolas, monospace;
    --c-bg: #eef2f6;
    --c-surface: #ffffff;
    --c-border: #e2e8f0;
    --c-text-main: #0f172a;
    --c-text-muted: #64748b;
    --c-blue: #2563eb;
    --c-green: #16a34a;
    --c-red: #dc2626;
    --c-amber: #d97706;
}

html, body {
    height: 100vh !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: hidden !important;
    font-family: var(--f-main);
    background-color: var(--c-bg);
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

.erp-journal-workspace {
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 0;
    max-height: 100%;
    flex: 1;
    overflow: hidden;
    gap: 8px;
    box-sizing: border-box;
    font-family: var(--f-main);
    color: var(--c-text-main);
}

/* 1. Compact Header & KPI Strip */
.jrn-top-strip {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
    gap: 12px;
    padding: 2px 0;
}
.jrn-left-group {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.stat-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #ffffff;
    border: 1px solid var(--c-border);
    border-radius: 999px;
    padding: 4px 12px;
    font-size: 12px;
    color: #475569;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
}
.stat-pill strong {
    font-family: var(--f-mono);
    font-weight: 700;
    color: #0f172a;
}
.stat-pill.total { background: #eff6ff; border-color: #bfdbfe; color: #1e40af; }
.stat-pill.total strong { color: #1d4ed8; }
.stat-pill.posted { background: #f0fdf4; border-color: #bbf7d0; color: #166534; }
.stat-pill.posted strong { color: #15803d; }
.stat-pill.voided { background: #fef2f2; border-color: #fecaca; color: #991b1b; }
.stat-pill.voided strong { color: #dc2626; }
.badge-dot { width: 7px; height: 7px; border-radius: 50%; }

.jrn-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}
.erp-btn-soft {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: #ffffff;
    border: 1px solid var(--c-border);
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    color: #334155;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.15s ease;
}
.erp-btn-soft:hover { background: #f8fafc; border-color: #cbd5e1; color: #0f172a; }
.erp-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    background: #0f172a;
    border: 1px solid #0f172a;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    color: #ffffff;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.15s ease;
}
.erp-btn-primary:hover { background: #1e293b; }
.erp-btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }
.erp-btn-danger {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 5px;
    font-size: 11.5px;
    font-weight: 600;
    color: #dc2626;
    cursor: pointer;
    transition: all 0.15s ease;
}
.erp-btn-danger:hover { background: #fee2e2; border-color: #fca5a5; }
.erp-btn-xs {
    padding: 4px 10px;
    font-size: 11.5px;
    border-radius: 5px;
}

/* Inline Flash Alert */
.erp-alert {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 7px 14px;
    border-radius: 6px;
    font-size: 12.5px;
    font-weight: 500;
    flex-shrink: 0;
}
.erp-alert.success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
.erp-alert.error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }

/* 2. Main Table Card */
.jrn-table-card {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: #ffffff;
    border: 1px solid var(--c-border);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    min-height: 0;
}

/* Rule 2: Top-Right Filter Bar */
.erp-filter-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 7px 14px;
    border-bottom: 1px solid var(--c-border);
    background: #ffffff;
    flex-shrink: 0;
    gap: 10px;
    flex-wrap: wrap;
}
.filter-left-info {
    font-size: 12px;
    color: #64748b;
    font-weight: 500;
}
.filter-right-form {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-left: auto;
    flex-wrap: wrap;
}
.erp-filter-input, .erp-filter-select {
    height: 28px;
    padding: 0 9px;
    font-size: 12px;
    font-family: inherit;
    color: #1e293b;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 5px;
    outline: none;
}
.erp-filter-input:focus, .erp-filter-select:focus {
    border-color: var(--c-blue);
    background: #ffffff;
}
.erp-filter-apply {
    height: 28px;
    padding: 0 12px;
    font-size: 12px;
    font-weight: 600;
    background: var(--c-blue);
    color: #ffffff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
.erp-filter-apply:hover { background: #1d4ed8; }
.erp-filter-reset {
    height: 28px;
    padding: 0 10px;
    font-size: 12px;
    font-weight: 600;
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
    border-radius: 5px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.erp-filter-reset:hover { background: #e2e8f0; color: #0f172a; }

/* Rule 1: Scrollable Table Body Only */
.erp-table-scroll {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: auto;
    background: #ffffff;
}
.erp-jrn-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 12.5px;
    text-align: left;
}
.erp-jrn-table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    background: #f8fafc;
    color: #475569;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 8px 12px;
    border-bottom: 1px solid #cbd5e1;
    white-space: nowrap;
}
.erp-jrn-table tbody td {
    padding: 8px 12px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    color: #1e293b;
}
/* Rule 3: Mandatory Zebra Striping */
.erp-jrn-table tbody tr:nth-child(even) td {
    background-color: #f8fafc;
}
.erp-jrn-table tbody tr:hover td {
    background-color: #eff6ff !important;
}

.num-mono {
    font-family: var(--f-mono);
    font-variant-numeric: tabular-nums;
}

.sf-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 2px 8px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
}
.sf-badge .dot { width: 6px; height: 6px; border-radius: 50%; }
.badge-posted { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
.badge-posted .dot { background: #16a34a; }
.badge-voided { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; text-decoration: line-through; }
.badge-voided .dot { background: #dc2626; }
.badge-draft { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
.badge-draft .dot { background: #d97706; }

.badge-source {
    font-size: 10px;
    font-weight: 600;
    padding: 1px 6px;
    border-radius: 4px;
    margin-left: 4px;
}
.badge-source.manual { background: #eff6ff; color: #1d4ed8; }
.badge-source.system { background: #f1f5f9; color: #475569; }

/* Pinned Pagination Footer */
.erp-pagination-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 7px 14px;
    border-top: 1px solid var(--c-border);
    background: #ffffff;
    font-size: 12px;
    color: #64748b;
    flex-shrink: 0;
}
.page-links { display: flex; align-items: center; gap: 4px; }
.page-link {
    padding: 4px 10px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 5px;
    color: #334155;
    text-decoration: none;
    font-weight: 600;
    font-size: 12px;
}
.page-link:hover { background: #f1f5f9; }
.page-link.active { background: #0f172a; color: #ffffff; border-color: #0f172a; }

/* Modals */
.erp-modal-veil {
    position: fixed; inset: 0; z-index: 9999;
    background: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(3px);
    display: none; align-items: center; justify-content: center;
}
.erp-modal-veil.open { display: flex; }
.erp-modal-box {
    background: #ffffff;
    width: 680px; max-width: 94vw; max-height: 90vh;
    border-radius: 10px; border: 1px solid var(--c-border);
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.12);
    display: flex; flex-direction: column; overflow: hidden;
}
.erp-modal-box.wide { width: 920px; }
.erp-modal-box.compact { width: 440px; }
.erp-modal-head {
    display: flex; justify-content: space-between; align-items: center;
    padding: 12px 18px; border-bottom: 1px solid var(--c-border); background: #f8fafc;
}
.erp-modal-head h3 { margin: 0; font-size: 15px; font-weight: 700; color: #0f172a; }
.erp-modal-close {
    background: #e2e8f0; border: none; width: 26px; height: 26px;
    border-radius: 50%; cursor: pointer; color: #475569;
    display: flex; align-items: center; justify-content: center;
}
.erp-modal-close:hover { background: #cbd5e1; color: #0f172a; }
.erp-modal-body { padding: 16px 18px; overflow-y: auto; flex: 1; }
.erp-modal-foot {
    padding: 10px 18px; border-top: 1px solid var(--c-border); background: #f8fafc;
    display: flex; justify-content: flex-end; gap: 8px; align-items: center;
}

.form-grid-3 { display: grid; grid-template-columns: 1fr 1fr 2fr; gap: 12px; margin-bottom: 14px; }
.form-group label { display: block; margin-bottom: 4px; font-size: 11.5px; font-weight: 600; color: #334155; }
.form-control {
    width: 100%; padding: 6px 10px; border: 1px solid #cbd5e1; border-radius: 5px;
    background: #ffffff; color: #0f172a; font-size: 12.5px; font-family: inherit;
    outline: none; box-sizing: border-box;
}
.form-control:focus { border-color: var(--c-blue); }

.journal-input-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
.journal-input-table th {
    padding: 7px 8px; font-size: 11px; font-weight: 700; color: #475569;
    text-transform: uppercase; border-bottom: 1px solid #cbd5e1; background: #f8fafc; text-align: left;
}
.journal-input-table td { padding: 6px 8px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
.journal-input-table input[type="number"] { text-align: right; font-family: var(--f-mono); }
.totals-row { font-weight: 700; background: #f8fafc; font-family: var(--f-mono); text-align: right; padding: 8px !important; }
.diff-warning { color: #dc2626; font-size: 12px; font-weight: 600; display: none; }
</style>

<div class="erp-viewport-view erp-journal-workspace">
    <!-- Compact Top Header & KPI Strip -->
    <div class="jrn-top-strip">
        <div class="jrn-left-group">
            <div class="stat-pill total">
                <span class="badge-dot" style="background: #2563eb;"></span>
                <span>Total Entries:</span>
                <strong><?= number_format($totalEntries) ?></strong>
            </div>
            <div class="stat-pill posted">
                <span class="badge-dot" style="background: #16a34a;"></span>
                <span>Posted:</span>
                <strong><?= number_format($postedCount) ?></strong>
            </div>
            <div class="stat-pill voided">
                <span class="badge-dot" style="background: #dc2626;"></span>
                <span>Voided:</span>
                <strong><?= number_format($voidedCount) ?></strong>
            </div>
        </div>

        <div class="jrn-actions">
            <a href="<?= APP_URL ?>/accounting/print_journal_list?<?= http_build_query($qArgs) ?>" target="_blank" class="erp-btn-soft">
                <i class="fa-solid fa-print"></i> Print List
            </a>
            <button type="button" class="erp-btn-primary" onclick="openNewJournalModal()">
                <i class="fa-solid fa-plus"></i> New Journal Entry
            </button>
        </div>
    </div>

    <?php if (!empty($data['error']) && !$reopenModalOnError): ?>
        <div class="erp-alert error">
            <span><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($data['error']) ?></span>
            <button type="button" onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:inherit;">&times;</button>
        </div>
    <?php endif; ?>
    <?php if (!empty($data['success'])): ?>
        <div class="erp-alert success">
            <span><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($data['success']) ?></span>
            <button type="button" onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:inherit;">&times;</button>
        </div>
    <?php endif; ?>

    <!-- Main Table Card -->
    <div class="jrn-table-card">
        <!-- Rule 2: Top-Right Filter Bar -->
        <div class="erp-filter-bar">
            <div class="filter-left-info">
                Showing <strong><?= $startRecord ?>–<?= $endRecord ?></strong> of <strong><?= number_format($totalEntries) ?></strong> entries
            </div>
            <form action="<?= APP_URL ?>/accounting/journal" method="GET" class="filter-right-form">
                <input type="text" name="search" class="erp-filter-input" placeholder="Search ref or memo..." value="<?= htmlspecialchars($search) ?>" style="width: 170px;">

                <select name="source" class="erp-filter-select" title="Entry Source">
                    <option value="manual" <?= $source === 'manual' ? 'selected' : '' ?>>Manual Entries</option>
                    <option value="system" <?= $source === 'system' ? 'selected' : '' ?>>System Auto</option>
                    <option value="all" <?= $source === 'all' ? 'selected' : '' ?>>All Sources</option>
                </select>

                <select name="status" class="erp-filter-select" title="Status">
                    <option value="All" <?= $status === 'All' ? 'selected' : '' ?>>All Statuses</option>
                    <option value="Posted" <?= $status === 'Posted' ? 'selected' : '' ?>>Posted</option>
                    <option value="Voided" <?= $status === 'Voided' ? 'selected' : '' ?>>Voided</option>
                    <option value="Draft" <?= $status === 'Draft' ? 'selected' : '' ?>>Draft</option>
                </select>

                <input type="date" name="start_date" class="erp-filter-input" value="<?= htmlspecialchars($startDate) ?>" title="Start Date">
                <span style="color: #94a3b8; font-size: 11px;">to</span>
                <input type="date" name="end_date" class="erp-filter-input" value="<?= htmlspecialchars($endDate) ?>" title="End Date">
                <input type="hidden" name="limit" value="<?= $limit ?>">

                <button type="submit" class="erp-filter-apply"><i class="fa-solid fa-filter"></i> Apply</button>
                <a href="<?= APP_URL ?>/accounting/journal" class="erp-filter-reset"><i class="fa-solid fa-rotate-left"></i> Reset</a>
            </form>
        </div>

        <!-- Rule 1: Scrollable Table Rows Only -->
        <div class="erp-table-scroll">
            <table class="erp-jrn-table">
                <thead>
                    <tr>
                        <th style="width: 110px;">Date</th>
                        <th style="width: 150px;">Reference</th>
                        <th>Description</th>
                        <th style="text-align: center; width: 70px;">Lines</th>
                        <th style="text-align: right; width: 130px;">Amount (Rs.)</th>
                        <th style="width: 110px;">Status</th>
                        <th style="width: 120px;">Posted By</th>
                        <th style="text-align: right; width: 210px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['entries'])): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 48px; color: #94a3b8;">
                                No journal entries found matching criteria.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data['entries'] as $entry): ?>
                        <tr>
                            <td class="num-mono" style="white-space: nowrap; color: #475569;">
                                <?= date('Y-m-d', strtotime($entry->entry_date)) ?>
                            </td>
                            <td class="num-mono" style="font-weight: 700; color: #0f172a; white-space: nowrap;">
                                <?= htmlspecialchars($entry->reference ?: ('JE-' . $entry->id)) ?>
                                <?php if ($source === 'all'): ?>
                                    <span class="badge-source <?= $entry->is_manual ? 'manual' : 'system' ?>">
                                        <?= $entry->is_manual ? 'Manual' : 'Auto' ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="color: #334155;">
                                <?= htmlspecialchars($entry->description) ?>
                            </td>
                            <td class="num-mono" style="text-align: center; color: #64748b;">
                                <?= (int)($entry->lines_count ?? 0) ?>
                            </td>
                            <td class="num-mono" style="text-align: right; font-weight: 700; color: #0f172a;">
                                <?= number_format((float)($entry->total_amount ?? 0), 2) ?>
                            </td>
                            <td>
                                <?php if ($entry->status === 'Posted'): ?>
                                    <span class="sf-badge badge-posted"><span class="dot"></span><?= $entry->status ?></span>
                                <?php elseif ($entry->status === 'Voided'): ?>
                                    <span class="sf-badge badge-voided"><span class="dot"></span><?= $entry->status ?></span>
                                <?php else: ?>
                                    <span class="sf-badge badge-draft"><span class="dot"></span><?= $entry->status ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="color: #475569;">
                                <?= htmlspecialchars($entry->username ?? 'System') ?>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <div style="display: inline-flex; gap: 5px; align-items: center;">
                                    <button type="button" class="erp-btn-soft erp-btn-xs" onclick="viewJournal(<?= (int)$entry->id ?>)">
                                        <i class="fa-solid fa-eye"></i> View
                                    </button>
                                    <a href="<?= APP_URL ?>/accounting/print_journal/<?= (int)$entry->id ?>" target="_blank" class="erp-btn-soft erp-btn-xs">
                                        <i class="fa-solid fa-print"></i> Print
                                    </a>
                                    <?php if ($entry->status === 'Posted' && empty($entry->is_closed)): ?>
                                        <button type="button" class="erp-btn-danger" onclick="openVoidModal(<?= (int)$entry->id ?>, '<?= htmlspecialchars($entry->reference ?: ('JE-' . $entry->id), ENT_QUOTES) ?>')">
                                            <i class="fa-solid fa-ban"></i> Void
                                        </button>
                                    <?php else: ?>
                                        <span style="color: #94a3b8; font-size: 11px; padding: 0 6px;">Locked</span>
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
        <div class="erp-pagination-footer">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span>Page <strong><?= $page ?></strong> of <strong><?= $totalPages ?></strong></span>
                <label style="display: inline-flex; align-items: center; gap: 5px;">
                    <span>Rows:</span>
                    <select class="erp-filter-select" style="height: 24px; font-size: 11px;" onchange="changePageSize(this.value)">
                        <?php foreach ([15, 25, 50, 100] as $opt): ?>
                            <option value="<?= $opt ?>" <?= $limit === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>
            <div class="page-links">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?><?= $qPrefix ?>" class="page-link">&laquo; Prev</a>
                <?php endif; ?>
                <?php
                $pStart = max(1, $page - 2);
                $pEnd = min($totalPages, $page + 2);
                for ($i = $pStart; $i <= $pEnd; $i++):
                ?>
                    <a href="?page=<?= $i ?><?= $qPrefix ?>" class="page-link <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?><?= $qPrefix ?>" class="page-link">Next &raquo;</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- New Journal Entry Modal -->
<div id="newJournalModal" class="erp-modal-veil <?= $reopenModalOnError ? 'open' : '' ?>" onclick="if(event.target === this) closeNewJournalModal()">
    <div class="erp-modal-box wide">
        <div class="erp-modal-head">
            <h3><i class="fa-solid fa-pen-to-square" style="color: var(--c-blue); margin-right: 6px;"></i> New Manual Journal Entry</h3>
            <button type="button" class="erp-modal-close" onclick="closeNewJournalModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="<?= APP_URL ?>/accounting/journal" method="POST" id="journalForm" style="display: flex; flex-direction: column; flex: 1; min-height: 0;">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <div class="erp-modal-body">
                <?php if ($reopenModalOnError): ?>
                    <div class="erp-alert error" style="margin-bottom: 12px;">
                        <span><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($data['error']) ?></span>
                    </div>
                <?php endif; ?>

                <div style="display: flex; gap: 10px; align-items: center; background: #f8fafc; padding: 8px 12px; border-radius: 6px; border: 1px solid var(--c-border); margin-bottom: 12px;">
                    <span style="font-size: 11.5px; font-weight: 600; color: #475569; white-space: nowrap;">Quick Template:</span>
                    <select id="templateSelector" class="form-control" onchange="loadTemplate(this.value)" style="max-width: 340px;">
                        <option value="">-- Select Template --</option>
                        <option value="rent">Record Rent Expense (Debit Rent, Credit Bank)</option>
                        <option value="utility">Record Utility Bill (Debit Utilities, Credit Bank)</option>
                        <option value="revenue">Record Customer Payment (Debit Bank, Credit AR)</option>
                        <option value="payroll">Record Payroll (Debit Salaries, Credit Bank)</option>
                    </select>
                    <button type="button" id="undoTemplateBtn" class="erp-btn-soft erp-btn-xs" style="display:none;" onclick="undoTemplateLoad()">Undo</button>
                </div>

                <div class="form-grid-3">
                    <div class="form-group">
                        <label>Entry Date <span style="color: #dc2626;">*</span></label>
                        <input type="date" name="entry_date" class="form-control num-mono" value="<?= htmlspecialchars($_POST['entry_date'] ?? date('Y-m-d')) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Reference #</label>
                        <input type="text" name="reference" class="form-control num-mono" placeholder="e.g. JV-1001" value="<?= htmlspecialchars($_POST['reference'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Description / Memo <span style="color: #dc2626;">*</span></label>
                        <input type="text" name="description" class="form-control" placeholder="Memo for this journal entry..." value="<?= htmlspecialchars($_POST['description'] ?? '') ?>" required>
                    </div>
                </div>

                <table class="journal-input-table" id="linesTable">
                    <thead>
                        <tr>
                            <th style="width: 36%;">Account</th>
                            <th style="width: 32%;">Line Memo</th>
                            <th style="width: 14%; text-align: right;">Debit (Rs.)</th>
                            <th style="width: 14%; text-align: right;">Credit (Rs.)</th>
                            <th style="width: 4%;"></th>
                        </tr>
                    </thead>
                    <tbody id="journalBody">
                        <?php for ($r = 0; $r < 2; $r++): ?>
                        <tr>
                            <td>
                                <select name="account_id[]" class="form-control" required onchange="handleAccountChange(this)">
                                    <option value="">Select Account...</option>
                                    <optgroup label="Entities (AR / AP)">
                                        <option value="Customer">Customer (AR - 1200)</option>
                                        <option value="Vendor">Supplier (AP - 2000)</option>
                                    </optgroup>
                                    <optgroup label="Ledger Accounts">
                                    <?php foreach ($data['accounts'] as $acc): ?>
                                        <option value="<?= (int)$acc->id ?>"><?= htmlspecialchars($acc->account_code . ' - ' . $acc->account_name) ?></option>
                                    <?php endforeach; ?>
                                    </optgroup>
                                </select>
                                <div class="line-entity-search-container" style="display: none; position: relative; margin-top: 4px;">
                                    <input type="hidden" name="line_entity_id[]" class="line-entity-id">
                                    <input type="text" class="form-control line-entity-search" style="font-size: 12px; padding: 4px 8px;" placeholder="Search entity..." autocomplete="off" onkeyup="filterLineEntities(this)" onfocus="showLineEntityDropdown(this)" onblur="hideLineEntityDropdown(this)">
                                    <div class="line-entity-dropdown" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 5px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); z-index: 100; max-height: 150px; overflow-y: auto; margin-top: 2px;"></div>
                                </div>
                            </td>
                            <td><input type="text" name="line_description[]" class="form-control" placeholder="Line-specific note"></td>
                            <td><input type="number" name="debit[]" class="form-control debit-input" step="0.01" min="0" oninput="calcTotals()"></td>
                            <td><input type="number" name="credit[]" class="form-control credit-input" step="0.01" min="0" oninput="calcTotals()"></td>
                            <td></td>
                        </tr>
                        <?php endfor; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" style="padding: 8px;">
                                <button type="button" class="erp-btn-soft erp-btn-xs" onclick="addRow()"><i class="fa-solid fa-plus"></i> Add Line</button>
                            </td>
                            <td class="totals-row" id="totalDebit">0.00</td>
                            <td class="totals-row" id="totalCredit">0.00</td>
                            <td class="totals-row"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="erp-modal-foot">
                <span class="diff-warning" id="diffWarning" style="margin-right: auto;"></span>
                <button type="button" class="erp-btn-soft" onclick="closeNewJournalModal()">Cancel</button>
                <button type="submit" class="erp-btn-primary" id="btnSubmit"><i class="fa-solid fa-check"></i> Post Journal Entry</button>
            </div>
        </form>
    </div>
</div>

<!-- Void Journal Confirmation Modal -->
<div id="voidJournalModal" class="erp-modal-veil" onclick="if(event.target === this) closeVoidModal()">
    <div class="erp-modal-box compact">
        <div class="erp-modal-head">
            <h3><i class="fa-solid fa-triangle-exclamation" style="color: #dc2626; margin-right: 6px;"></i> Void Journal Entry</h3>
            <button type="button" class="erp-modal-close" onclick="closeVoidModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="<?= APP_URL ?>/accounting/void_journal" method="POST" id="voidJournalForm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="entry_id" id="voidEntryId" value="">
            <div class="erp-modal-body">
                <p style="margin: 0 0 8px 0; font-size: 13px; color: #334155;">
                    Are you sure you want to void journal entry <strong id="voidEntryRef" class="num-mono"></strong>?
                </p>
                <p style="margin: 0; font-size: 12px; color: #64748b;">
                    This will permanently mark the entry as <strong>Voided</strong> and reverse all affected Chart of Accounts ledger balances.
                </p>
            </div>
            <div class="erp-modal-foot">
                <button type="button" class="erp-btn-soft" onclick="closeVoidModal()">Cancel</button>
                <button type="submit" class="erp-btn-primary" style="background: #dc2626; border-color: #dc2626;" id="btnConfirmVoid">
                    <i class="fa-solid fa-ban"></i> Confirm Void
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Journal Details Modal -->
<div id="journalDetailsModal" class="erp-modal-veil" onclick="if(event.target === this) closeJournalModal()">
    <div class="erp-modal-box">
        <div class="erp-modal-head">
            <h3>Journal Entry Details</h3>
            <button type="button" class="erp-modal-close" onclick="closeJournalModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="erp-modal-body">
            <div id="modalLoader" style="display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 10px; padding: 40px 0;">
                <i class="fa-solid fa-spinner fa-spin" style="font-size: 28px; color: var(--c-blue);"></i>
                <span style="font-size: 13px; color: #64748b;">Loading journal details...</span>
            </div>
            <div id="modalError" style="display: none; text-align: center; color: #dc2626; padding: 20px 0;">
                <i class="fa-solid fa-triangle-exclamation" style="font-size: 32px; margin-bottom: 8px;"></i>
                <p id="modalErrorMsg" style="margin: 0; font-weight: 500;"></p>
            </div>
            <div id="modalContent" style="display: none;">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 16px; background: #f8fafc; padding: 12px 14px; border-radius: 6px; border: 1px solid var(--c-border);">
                    <div>
                        <div style="font-size: 10.5px; text-transform: uppercase; color: #64748b; font-weight: 700;">Entry Date</div>
                        <div id="detDate" class="num-mono" style="font-weight: 600; color: #0f172a; font-size: 13px;"></div>
                    </div>
                    <div>
                        <div style="font-size: 10.5px; text-transform: uppercase; color: #64748b; font-weight: 700;">Reference #</div>
                        <div id="detRef" class="num-mono" style="font-weight: 700; color: var(--c-blue); font-size: 13px;"></div>
                    </div>
                    <div style="grid-column: span 2;">
                        <div style="font-size: 10.5px; text-transform: uppercase; color: #64748b; font-weight: 700;">Description / Memo</div>
                        <div id="detDesc" style="color: #0f172a; font-size: 13px;"></div>
                    </div>
                    <div>
                        <div style="font-size: 10.5px; text-transform: uppercase; color: #64748b; font-weight: 700;">Posted By</div>
                        <div id="detUser" style="color: #475569; font-size: 12.5px;"></div>
                    </div>
                    <div>
                        <div style="font-size: 10.5px; text-transform: uppercase; color: #64748b; font-weight: 700;">Status</div>
                        <div id="detStatus"></div>
                    </div>
                </div>

                <div style="border: 1px solid var(--c-border); border-radius: 6px; overflow: hidden;">
                    <table class="erp-jrn-table" style="font-size: 12.5px;">
                        <thead>
                            <tr>
                                <th>Account</th>
                                <th>Memo</th>
                                <th style="text-align: right; width: 115px;">Debit (Rs.)</th>
                                <th style="text-align: right; width: 115px;">Credit (Rs.)</th>
                            </tr>
                        </thead>
                        <tbody id="detLinesBody"></tbody>
                        <tfoot>
                            <tr style="font-weight: 700; background: #f8fafc; border-top: 1px solid var(--c-border);">
                                <td colspan="2" style="padding: 8px 12px; text-align: right; color: #475569;">Total</td>
                                <td id="detTotalDebit" class="num-mono" style="padding: 8px 12px; text-align: right;">0.00</td>
                                <td id="detTotalCredit" class="num-mono" style="padding: 8px 12px; text-align: right;">0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="erp-modal-foot">
            <button type="button" class="erp-btn-primary" onclick="printCurrentJournalModal()"><i class="fa-solid fa-print"></i> Print Voucher</button>
            <button type="button" class="erp-btn-soft" onclick="closeJournalModal()">Close</button>
        </div>
    </div>
</div>

<script>
const customersList = <?= json_encode($data['customers'] ?? []) ?>;
const suppliersList = <?= json_encode($data['suppliers'] ?? []) ?>;

function changePageSize(newLimit) {
    const url = new URL(window.location.href);
    url.searchParams.set('limit', newLimit);
    url.searchParams.set('page', '1');
    window.location.href = url.toString();
}

function openNewJournalModal() {
    document.getElementById('newJournalModal').classList.add('open');
    calcTotals();
}
function closeNewJournalModal() {
    document.getElementById('newJournalModal').classList.remove('open');
}

function openVoidModal(id, ref) {
    document.getElementById('voidEntryId').value = id;
    document.getElementById('voidEntryRef').textContent = ref;
    document.getElementById('voidJournalModal').classList.add('open');
}
function closeVoidModal() {
    document.getElementById('voidJournalModal').classList.remove('open');
}

function handleAccountChange(selectEl) {
    const type = selectEl.value;
    const container = selectEl.closest('td').querySelector('.line-entity-search-container');
    const searchInput = container.querySelector('.line-entity-search');
    const hiddenInput = container.querySelector('.line-entity-id');

    searchInput.value = '';
    hiddenInput.value = '';

    if (type === 'Customer' || type === 'Vendor') {
        container.style.display = 'block';
        const list = type === 'Customer' ? customersList : suppliersList;
        renderLineEntityDropdown(container, list);
    } else {
        container.style.display = 'none';
    }
}

function filterLineEntities(inputEl) {
    const container = inputEl.closest('td');
    const type = container.querySelector('select[name="account_id[]"]').value;
    const list = type === 'Customer' ? customersList : suppliersList;
    const q = inputEl.value.toLowerCase();
    const filtered = list.filter(item => item.name.toLowerCase().includes(q));
    renderLineEntityDropdown(container, filtered);
    container.querySelector('.line-entity-dropdown').style.display = 'block';
}

function renderLineEntityDropdown(container, list) {
    const dd = container.querySelector('.line-entity-dropdown');
    dd.innerHTML = '';
    if (list.length === 0) {
        dd.innerHTML = '<div style="padding: 8px 12px; color: #94a3b8; font-size: 12px;">No results found.</div>';
        return;
    }
    list.slice(0, 50).forEach(item => {
        const div = document.createElement('div');
        div.style.padding = '6px 10px';
        div.style.cursor = 'pointer';
        div.style.fontSize = '12px';
        div.style.borderBottom = '1px solid #f1f5f9';
        div.textContent = item.name;
        div.onmousedown = function() { selectLineEntity(container, item.id, item.name); };
        div.onmouseover = function() { div.style.background = '#f1f5f9'; };
        div.onmouseout = function() { div.style.background = 'transparent'; };
        dd.appendChild(div);
    });
}

function selectLineEntity(container, id, name) {
    container.querySelector('.line-entity-id').value = id;
    container.querySelector('.line-entity-search').value = name;
    container.querySelector('.line-entity-dropdown').style.display = 'none';
}

function showLineEntityDropdown(inputEl) {
    const container = inputEl.closest('td');
    const type = container.querySelector('select[name="account_id[]"]').value;
    if (type === 'Customer' || type === 'Vendor') {
        filterLineEntities(inputEl);
        container.querySelector('.line-entity-dropdown').style.display = 'block';
    }
}

function hideLineEntityDropdown(inputEl) {
    setTimeout(() => {
        const container = inputEl.closest('.line-entity-search-container');
        if (container && container.querySelector('.line-entity-dropdown')) {
            container.querySelector('.line-entity-dropdown').style.display = 'none';
        }
    }, 200);
}

const accountOptions = `
    <option value="">Select Account...</option>
    <optgroup label="Entities (AR / AP)">
        <option value="Customer">Customer (AR - 1200)</option>
        <option value="Vendor">Supplier (AP - 2000)</option>
    </optgroup>
    <optgroup label="Ledger Accounts">
    <?php foreach ($data['accounts'] as $acc): ?>
        <option value="<?= (int)$acc->id ?>"><?= htmlspecialchars($acc->account_code . ' - ' . $acc->account_name) ?></option>
    <?php endforeach; ?>
    </optgroup>
`;

let previousState = null;

function addRow() {
    const tbody = document.getElementById('journalBody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <select name="account_id[]" class="form-control" required onchange="handleAccountChange(this)">${accountOptions}</select>
            <div class="line-entity-search-container" style="display: none; position: relative; margin-top: 4px;">
                <input type="hidden" name="line_entity_id[]" class="line-entity-id">
                <input type="text" class="form-control line-entity-search" style="font-size: 12px; padding: 4px 8px;" placeholder="Search entity..." autocomplete="off" onkeyup="filterLineEntities(this)" onfocus="showLineEntityDropdown(this)" onblur="hideLineEntityDropdown(this)">
                <div class="line-entity-dropdown" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 5px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); z-index: 100; max-height: 150px; overflow-y: auto; margin-top: 2px;"></div>
            </div>
        </td>
        <td><input type="text" name="line_description[]" class="form-control" placeholder="Line-specific note"></td>
        <td><input type="number" name="debit[]" class="form-control debit-input" step="0.01" min="0" oninput="calcTotals()"></td>
        <td><input type="number" name="credit[]" class="form-control credit-input" step="0.01" min="0" oninput="calcTotals()"></td>
        <td><button type="button" class="erp-btn-danger" onclick="removeRow(this)"><i class="fa-solid fa-trash"></i></button></td>
    `;
    tbody.appendChild(tr);
}

function removeRow(btn) {
    btn.closest('tr').remove();
    calcTotals();
}

function calcTotals() {
    let debits = 0;
    let credits = 0;
    document.querySelectorAll('.debit-input').forEach(input => { debits += parseFloat(input.value) || 0; });
    document.querySelectorAll('.credit-input').forEach(input => { credits += parseFloat(input.value) || 0; });

    document.getElementById('totalDebit').innerText = debits.toFixed(2);
    document.getElementById('totalCredit').innerText = credits.toFixed(2);

    const btnSubmit = document.getElementById('btnSubmit');
    const warning = document.getElementById('diffWarning');
    const linesCount = document.querySelectorAll('#journalBody tr').length;

    if (linesCount < 2) {
        btnSubmit.disabled = true;
        warning.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i> At least 2 lines required.';
        warning.style.display = 'inline-block';
    } else if (Math.abs(debits - credits) > 0.001) {
        btnSubmit.disabled = true;
        warning.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i> Debits and Credits must balance!';
        warning.style.display = 'inline-block';
    } else if (debits <= 0) {
        btnSubmit.disabled = true;
        warning.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i> Amount must be greater than zero.';
        warning.style.display = 'inline-block';
    } else {
        btnSubmit.disabled = false;
        warning.style.display = 'none';
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}

function captureState() {
    const rows = document.querySelectorAll('#journalBody tr');
    const lines = [];
    rows.forEach(row => {
        lines.push({
            account_id: row.querySelector('select[name="account_id[]"]')?.value || '',
            entity_id: row.querySelector('.line-entity-id')?.value || '',
            entity_search: row.querySelector('.line-entity-search')?.value || '',
            line_description: row.querySelector('input[name="line_description[]"]')?.value || '',
            debit: row.querySelector('.debit-input')?.value || '',
            credit: row.querySelector('.credit-input')?.value || ''
        });
    });
    previousState = {
        entry_date: document.querySelector('#journalForm input[name="entry_date"]').value,
        reference: document.querySelector('#journalForm input[name="reference"]').value,
        description: document.querySelector('#journalForm input[name="description"]').value,
        lines: lines
    };
}

function undoTemplateLoad() {
    if (!previousState) return;
    document.querySelector('#journalForm input[name="entry_date"]').value = previousState.entry_date;
    document.querySelector('#journalForm input[name="reference"]').value = previousState.reference;
    document.querySelector('#journalForm input[name="description"]').value = previousState.description;

    const tbody = document.getElementById('journalBody');
    tbody.innerHTML = '';
    previousState.lines.forEach((line, index) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <select name="account_id[]" class="form-control" required onchange="handleAccountChange(this)">${accountOptions}</select>
                <div class="line-entity-search-container" style="display: none; position: relative; margin-top: 4px;">
                    <input type="hidden" name="line_entity_id[]" class="line-entity-id">
                    <input type="text" class="form-control line-entity-search" style="font-size: 12px; padding: 4px 8px;" placeholder="Search entity..." autocomplete="off" onkeyup="filterLineEntities(this)" onfocus="showLineEntityDropdown(this)" onblur="hideLineEntityDropdown(this)">
                    <div class="line-entity-dropdown" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 5px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); z-index: 100; max-height: 150px; overflow-y: auto; margin-top: 2px;"></div>
                </div>
            </td>
            <td><input type="text" name="line_description[]" class="form-control" placeholder="Line-specific note" value="${escapeHtml(line.line_description)}"></td>
            <td><input type="number" name="debit[]" class="form-control debit-input" step="0.01" min="0" value="${line.debit}" oninput="calcTotals()"></td>
            <td><input type="number" name="credit[]" class="form-control credit-input" step="0.01" min="0" value="${line.credit}" oninput="calcTotals()"></td>
            <td>${index < 2 ? '' : '<button type="button" class="erp-btn-danger" onclick="removeRow(this)"><i class="fa-solid fa-trash"></i></button>'}</td>
        `;
        tbody.appendChild(tr);
        if (line.account_id) {
            const accSelect = tr.querySelector('select[name="account_id[]"]');
            accSelect.value = line.account_id;
            handleAccountChange(accSelect);
        }
        if (line.entity_id && (line.account_id === 'Customer' || line.account_id === 'Vendor')) {
            tr.querySelector('.line-entity-id').value = line.entity_id;
            tr.querySelector('.line-entity-search').value = line.entity_search;
        }
    });

    previousState = null;
    document.getElementById('undoTemplateBtn').style.display = 'none';
    calcTotals();
}

function loadTemplate(type) {
    if (!type) return;
    captureState();
    document.getElementById('undoTemplateBtn').style.display = 'inline-flex';

    const tbody = document.getElementById('journalBody');
    tbody.innerHTML = '';
    addRow();
    addRow();

    const newSelects = tbody.querySelectorAll('select[name="account_id[]"]');
    const newDebits = tbody.querySelectorAll('.debit-input');
    const options1 = Array.from(newSelects[0].options);
    const options2 = Array.from(newSelects[1].options);

    let memo = "", match1 = null, match2 = null;
    if (type === 'rent') {
        memo = "Rent Expense for current month";
        match1 = options1.find(opt => opt.text.toLowerCase().includes('rent'));
        match2 = options2.find(opt => opt.text.toLowerCase().includes('cash') || opt.text.toLowerCase().includes('bank'));
    } else if (type === 'utility') {
        memo = "Utility Bill payment";
        match1 = options1.find(opt => opt.text.toLowerCase().includes('utilit') || opt.text.toLowerCase().includes('electric') || opt.text.toLowerCase().includes('water'));
        match2 = options2.find(opt => opt.text.toLowerCase().includes('cash') || opt.text.toLowerCase().includes('bank'));
    } else if (type === 'revenue') {
        memo = "Record customer payment receipt";
        match1 = options1.find(opt => opt.text.toLowerCase().includes('cash') || opt.text.toLowerCase().includes('bank'));
        match2 = options2.find(opt => opt.text.toLowerCase().includes('receivable') || opt.text.toLowerCase().includes('debtor'));
    } else if (type === 'payroll') {
        memo = "Monthly salary disbursement";
        match1 = options1.find(opt => opt.text.toLowerCase().includes('salari') || opt.text.toLowerCase().includes('salary') || opt.text.toLowerCase().includes('wage') || opt.text.toLowerCase().includes('payroll'));
        match2 = options2.find(opt => opt.text.toLowerCase().includes('cash') || opt.text.toLowerCase().includes('bank'));
    }

    if (match1) newSelects[0].value = match1.value;
    if (match2) newSelects[1].value = match2.value;

    document.querySelector('#journalForm input[name="description"]').value = memo;
    newDebits[0].focus();
    document.getElementById('templateSelector').value = '';
    calcTotals();
}

// Double-submission protection
document.getElementById('journalForm')?.addEventListener('submit', function() {
    const btn = document.getElementById('btnSubmit');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Posting...';
    }
});

let currentViewingJournalId = null;

function viewJournal(id) {
    currentViewingJournalId = id;
    const veil = document.getElementById('journalDetailsModal');
    const loader = document.getElementById('modalLoader');
    const error = document.getElementById('modalError');
    const content = document.getElementById('modalContent');

    veil.classList.add('open');
    loader.style.display = 'flex';
    error.style.display = 'none';
    content.style.display = 'none';

    fetch('<?= APP_URL ?>/accounting/journal_details/' + id)
        .then(res => res.json())
        .then(res => {
            loader.style.display = 'none';
            if (res.status === 'success') {
                const entry = res.data.entry;
                const lines = res.data.lines;

                document.getElementById('detDate').innerText = new Date(entry.entry_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                document.getElementById('detRef').innerText = entry.reference || ('JE-' + entry.id);
                document.getElementById('detDesc').innerText = entry.description || 'No description';
                document.getElementById('detUser').innerText = entry.username || 'System';

                const statusEl = document.getElementById('detStatus');
                if (entry.status === 'Posted') {
                    statusEl.innerHTML = '<span class="sf-badge badge-posted"><span class="dot"></span>' + entry.status + '</span>';
                } else if (entry.status === 'Voided') {
                    statusEl.innerHTML = '<span class="sf-badge badge-voided"><span class="dot"></span>' + entry.status + '</span>';
                } else {
                    statusEl.innerHTML = '<span class="sf-badge badge-draft"><span class="dot"></span>' + entry.status + '</span>';
                }

                const tbody = document.getElementById('detLinesBody');
                tbody.innerHTML = '';
                let totalDebit = 0, totalCredit = 0;

                lines.forEach(line => {
                    const tr = document.createElement('tr');
                    const debitVal = parseFloat(line.debit) || 0;
                    const creditVal = parseFloat(line.credit) || 0;
                    totalDebit += debitVal;
                    totalCredit += creditVal;

                    let entityText = '';
                    if (line.entity_type) {
                        const name = line.customer_name || line.vendor_name || ('ID ' + line.entity_id);
                        entityText = `<div style="font-size: 11px; color: var(--c-blue); margin-top: 2px; font-weight: 600;">
                            <i class="fa-solid fa-user-tag"></i> ${escapeHtml(line.entity_type)}: ${escapeHtml(name)}
                        </div>`;
                    }

                    tr.innerHTML = `
                        <td><strong>${escapeHtml(line.account_code)}</strong> - ${escapeHtml(line.account_name)}${entityText}</td>
                        <td style="color: #64748b;">${escapeHtml(line.description) || '-'}</td>
                        <td class="num-mono" style="text-align: right;">${debitVal > 0 ? debitVal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '-'}</td>
                        <td class="num-mono" style="text-align: right;">${creditVal > 0 ? creditVal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '-'}</td>
                    `;
                    tbody.appendChild(tr);
                });

                document.getElementById('detTotalDebit').innerText = totalDebit.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                document.getElementById('detTotalCredit').innerText = totalCredit.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                content.style.display = 'block';
            } else {
                document.getElementById('modalErrorMsg').innerText = res.message || 'Failed to load journal details.';
                error.style.display = 'block';
            }
        })
        .catch(() => {
            loader.style.display = 'none';
            document.getElementById('modalErrorMsg').innerText = 'An error occurred while fetching details.';
            error.style.display = 'block';
        });
}

function closeJournalModal() {
    document.getElementById('journalDetailsModal').classList.remove('open');
}

function printCurrentJournalModal() {
    if (currentViewingJournalId) {
        window.open('<?= APP_URL ?>/accounting/print_journal/' + currentViewingJournalId, '_blank');
    }
}

calcTotals();
</script>
