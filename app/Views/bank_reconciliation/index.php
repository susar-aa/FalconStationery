<?php
/**
 * Bank Reconciliation Index View - Curtiss & Falcon Stationery ERP
 * Viewport-Locked (100vh), Top-Right Filter Toolbar, SF Pro Display & Zebra Striping
 */
$reconciliations = $data['reconciliations'] ?? [];
$bankAccounts = $data['bankAccounts'] ?? [];
$csrfToken = $_SESSION['csrf_token'] ?? '';
?>

<style>
/* Viewport Lock Layout (Mandatory Rule 1) */
.erp-viewport-view {
    height: calc(100vh - 58px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: #f8fafc;
    font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: #1e293b;
    padding: 16px 24px;
    box-sizing: border-box;
}

/* Compact Workspace Header */
.erp-top-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    flex-shrink: 0;
}
.erp-module-tag {
    display: flex;
    align-items: center;
    gap: 10px;
}
.erp-module-tag .title {
    font-size: 20px;
    font-weight: 700;
    color: #0f172a;
    letter-spacing: -0.02em;
    margin: 0;
}
.erp-module-tag .badge-count {
    background: #e2e8f0;
    color: #475569;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 999px;
    font-family: ui-monospace, 'SF Mono', Menlo, monospace;
}
.header-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}
.btn-erp-primary {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 18px;
    background: #0284c7;
    color: #ffffff;
    font-size: 13px;
    font-weight: 600;
    border-radius: 8px;
    border: none;
    text-decoration: none;
    box-shadow: 0 1px 2px rgba(2, 132, 199, 0.2);
    transition: all 0.15s ease;
}
.btn-erp-primary:hover {
    background: #0369a1;
    color: #ffffff;
    transform: translateY(-1px);
}
.btn-erp-secondary {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    background: #ffffff;
    color: #334155;
    font-size: 12px;
    font-weight: 600;
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    text-decoration: none;
    transition: all 0.15s ease;
}
.btn-erp-secondary:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
    color: #0f172a;
}
.btn-erp-danger-soft {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 5px 10px;
    background: #fef2f2;
    color: #dc2626;
    font-size: 11.5px;
    font-weight: 600;
    border-radius: 6px;
    border: 1px solid #fecaca;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.15s ease;
}
.btn-erp-danger-soft:hover {
    background: #dc2626;
    color: #ffffff;
    border-color: #dc2626;
}

/* Dedicated Filter Toolbar (Mandatory Rule 2: Top-Right Corner Filters) */
.erp-filter-bar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px 10px 0 0;
    padding: 10px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    flex-shrink: 0;
    border-bottom: none;
}
.filter-left-summary {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: #64748b;
    font-weight: 500;
}
.filter-right-group {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-left: auto;
}
.erp-filter-select, .erp-filter-input {
    height: 32px;
    padding: 0 10px;
    font-size: 12.5px;
    font-family: inherit;
    color: #1e293b;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    outline: none;
    transition: border-color 0.15s ease;
}
.erp-filter-select:focus, .erp-filter-input:focus {
    border-color: #0284c7;
    background: #ffffff;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.1);
}
.btn-erp-reset {
    height: 32px;
    padding: 0 12px;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all 0.15s ease;
}
.btn-erp-reset:hover {
    background: #e2e8f0;
    color: #0f172a;
}

/* Scrollable Table Viewport Only (Mandatory Rule 1) */
.erp-table-scroll {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: auto;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.erp-data-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 13px;
    text-align: left;
}
.erp-data-table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    background: #f8fafc;
    color: #475569;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 10px 14px;
    border-bottom: 1px solid #cbd5e1;
    white-space: nowrap;
}
.erp-data-table tbody td {
    padding: 10px 14px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    color: #334155;
}
.erp-data-table tbody tr:hover td {
    background-color: #f0fdf4 !important;
}

/* Mandatory Zebra Striping (Rule 3) */
.erp-data-table tbody tr:nth-child(even) td {
    background-color: #f8fafc;
}

/* Typography & Numbers */
.num-mono {
    font-family: ui-monospace, 'SF Mono', Menlo, Monaco, Consolas, monospace;
    font-variant-numeric: tabular-nums;
}
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 8px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}
.status-badge.draft {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
}
.status-badge.draft .dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #94a3b8;
}
.status-badge.finalized {
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #bbf7d0;
}
.status-badge.finalized .dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #22c55e;
}

/* Pinned Footer (Mandatory Rule 1) */
.erp-pinned-footer {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-top: none;
    border-radius: 0 0 10px 10px;
    padding: 10px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
    font-size: 12px;
    color: #64748b;
}

/* Flash Alerts */
.erp-alert {
    padding: 10px 14px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}
.erp-alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.erp-alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

/* Modal Styles */
.erp-modal-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(2px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s ease;
}
.erp-modal-overlay.active {
    opacity: 1;
    pointer-events: auto;
}
.erp-modal-box {
    background: #ffffff;
    border-radius: 12px;
    width: 100%;
    max-width: 440px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transform: scale(0.95);
    transition: transform 0.2s ease;
}
.erp-modal-overlay.active .erp-modal-box {
    transform: scale(1);
}
.erp-modal-header {
    padding: 16px 20px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.erp-modal-title {
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}
.erp-modal-body {
    padding: 20px;
    font-size: 13.5px;
    color: #475569;
    line-height: 1.5;
}
.erp-modal-footer {
    padding: 12px 20px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: flex-end;
    gap: 8px;
}
</style>

<div class="erp-viewport-view">
    <!-- Flash Messages -->
    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="erp-alert erp-alert-success">
            <i class="ph ph-check-circle" style="font-size: 16px;"></i>
            <span><?= htmlspecialchars($_SESSION['flash_success']); ?></span>
            <?php unset($_SESSION['flash_success']); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="erp-alert erp-alert-error">
            <i class="ph ph-warning-circle" style="font-size: 16px;"></i>
            <span><?= htmlspecialchars($_SESSION['flash_error']); ?></span>
            <?php unset($_SESSION['flash_error']); ?>
        </div>
    <?php endif; ?>

    <!-- Workspace Header -->
    <div class="erp-top-header">
        <div class="erp-module-tag">
            <h1 class="title">Bank Reconciliation</h1>
            <span class="badge-count" id="headerCountBadge"><?= count($reconciliations); ?> Periods</span>
        </div>
        <div class="header-actions">
            <a href="<?= APP_URL ?>/banking" class="btn-erp-secondary">
                <i class="ph ph-arrow-left"></i> Bank Register
            </a>
            <a href="<?= APP_URL ?>/bankreconciliation/create" class="btn-erp-primary">
                <i class="ph ph-plus-circle"></i> New Period Setup
            </a>
        </div>
    </div>

    <!-- Filter Toolbar (Top-Right Placement - Mandatory Rule 2) -->
    <div class="erp-filter-bar">
        <div class="filter-left-summary">
            <span id="filteredResultsLabel">Showing <?= count($reconciliations); ?> reconciliation records</span>
        </div>
        <div class="filter-right-group">
            <select id="filterBank" class="erp-filter-select" style="min-width: 170px;">
                <option value="">All Bank Accounts</option>
                <?php foreach ($bankAccounts as $ba): ?>
                    <option value="<?= htmlspecialchars($ba->id); ?>">
                        <?= htmlspecialchars($ba->account_code . ' - ' . $ba->account_name); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select id="filterStatus" class="erp-filter-select" style="width: 130px;">
                <option value="">All Statuses</option>
                <option value="Draft">Draft</option>
                <option value="Finalized">Finalized</option>
            </select>

            <input type="text" id="filterSearch" class="erp-filter-input" placeholder="Search ref or bank..." style="width: 170px;">

            <button type="button" id="btnResetFilters" class="btn-erp-reset">
                <i class="ph ph-arrow-counter-clockwise"></i> Reset
            </button>
        </div>
    </div>

    <!-- Scrollable Table Viewport (Mandatory Rule 1) -->
    <div class="erp-table-scroll">
        <table class="erp-data-table" id="recDataTable">
            <thead>
                <tr>
                    <th style="padding-left: 18px;">Reference #</th>
                    <th>Bank Account</th>
                    <th>Statement Period</th>
                    <th style="text-align: right;">Stmt Closing</th>
                    <th style="text-align: right;">Book Closing</th>
                    <th style="text-align: right;">Difference</th>
                    <th style="text-align: center;">Status</th>
                    <th style="text-align: center; padding-right: 18px;">Workspace Actions</th>
                </tr>
            </thead>
            <tbody id="recTableBody">
                <?php if (!empty($reconciliations)): ?>
                    <?php foreach ($reconciliations as $rec): ?>
                        <tr class="rec-row" 
                            data-account-id="<?= htmlspecialchars($rec->account_id); ?>" 
                            data-status="<?= htmlspecialchars($rec->status); ?>"
                            data-search="<?= htmlspecialchars(strtolower($rec->reconciliation_number . ' ' . $rec->account_name . ' ' . $rec->account_code)); ?>">
                            <td style="padding-left: 18px;">
                                <a href="<?= APP_URL ?>/bankreconciliation/reconcile/<?= $rec->id ?>" style="color: #0284c7; font-weight: 700; text-decoration: none;" class="num-mono">
                                    <?= htmlspecialchars($rec->reconciliation_number) ?>
                                </a>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: #0f172a;"><?= htmlspecialchars($rec->account_name) ?></div>
                                <div style="font-size: 11.5px; color: #64748b;" class="num-mono"><?= htmlspecialchars($rec->account_code) ?></div>
                            </td>
                            <td>
                                <span class="num-mono" style="font-size: 12px; color: #475569; font-weight: 500;">
                                    <?= date('M d, Y', strtotime($rec->statement_date_start)) ?> &rarr; <?= date('M d, Y', strtotime($rec->statement_date_end)) ?>
                                </span>
                            </td>
                            <td style="text-align: right; font-weight: 700;" class="num-mono">
                                LKR <?= number_format($rec->statement_closing_balance, 2) ?>
                            </td>
                            <td style="text-align: right; font-weight: 600;" class="num-mono">
                                LKR <?= number_format($rec->book_closing_balance, 2) ?>
                            </td>
                            <td style="text-align: right; font-weight: 700;" class="num-mono">
                                <?php if (abs($rec->difference) > 0.009): ?>
                                    <span style="color: #dc2626;">LKR <?= number_format($rec->difference, 2) ?></span>
                                <?php else: ?>
                                    <span style="color: #16a34a;">0.00 (Balanced)</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <?php if ($rec->status === 'Finalized'): ?>
                                    <span class="status-badge finalized"><span class="dot"></span> Finalized</span>
                                <?php else: ?>
                                    <span class="status-badge draft"><span class="dot"></span> Draft</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center; padding-right: 18px;">
                                <div style="display: inline-flex; align-items: center; gap: 6px;">
                                    <a href="<?= APP_URL ?>/bankreconciliation/reconcile/<?= $rec->id ?>" class="btn-erp-secondary" style="padding: 4px 10px; font-size: 11.5px;">
                                        <i class="ph ph-squares-four"></i> <?= ($rec->status === 'Finalized') ? 'View Workspace' : 'Reconcile' ?>
                                    </a>
                                    <a href="<?= APP_URL ?>/bankreconciliation/report/<?= $rec->id ?>" class="btn-erp-secondary" style="padding: 4px 10px; font-size: 11.5px;" target="_blank" title="Print Statement / Audit Report">
                                        <i class="ph ph-printer"></i> Statement
                                    </a>
                                    <?php if ($rec->status !== 'Finalized'): ?>
                                        <button type="button" class="btn-erp-danger-soft" onclick="openDeleteRecModal(<?= $rec->id ?>, '<?= htmlspecialchars(addslashes($rec->reconciliation_number)) ?>')">
                                            <i class="ph ph-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr id="emptyRecRow">
                        <td colspan="8" style="text-align: center; padding: 60px 20px; color: #94a3b8;">
                            <i class="ph ph-bank" style="font-size: 40px; display: block; margin-bottom: 10px; opacity: 0.5;"></i>
                            <div style="font-size: 15px; font-weight: 600; color: #475569; margin-bottom: 4px;">No Bank Reconciliations Found</div>
                            <div style="font-size: 12.5px;">Click "New Period Setup" to initiate statement matching for any bank account.</div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pinned Pagination & Summary Footer -->
    <div class="erp-pinned-footer">
        <div>
            <span>Bank Reconciliation Ledger &bull; Continuous Closed-Period Integrity</span>
        </div>
        <div>
            <span id="footerCount"><?= count($reconciliations); ?> periods on record</span>
        </div>
    </div>
</div>

<!-- Secure Delete Modal with CSRF (Mandatory Rule 3) -->
<div class="erp-modal-overlay" id="deleteRecModal">
    <div class="erp-modal-box">
        <div class="erp-modal-header">
            <h3 class="erp-modal-title">Confirm Deletion</h3>
            <button type="button" onclick="closeDeleteRecModal()" style="border:none; background:none; cursor:pointer; color:#94a3b8; font-size:16px;">
                <i class="ph ph-x"></i>
            </button>
        </div>
        <form method="POST" id="deleteRecForm" action="">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken); ?>">
            <div class="erp-modal-body">
                Are you sure you want to delete reconciliation draft <strong id="deleteRecRefLabel" class="num-mono" style="color: #0f172a;"></strong>?
                <p style="margin: 10px 0 0 0; font-size: 12px; color: #dc2626;">
                    <i class="ph ph-warning-circle"></i> Any unfinalized bank adjustments associated with this draft will be removed. Historical ERP transactions remain intact.
                </p>
            </div>
            <div class="erp-modal-footer">
                <button type="button" class="btn-erp-secondary" onclick="closeDeleteRecModal()">Cancel</button>
                <button type="submit" class="btn-erp-danger-soft" style="background:#dc2626; color:#ffffff; border-color:#dc2626; padding:7px 16px;">
                    <i class="ph ph-trash"></i> Delete Draft
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Filter state & live search logic
document.addEventListener('DOMContentLoaded', function() {
    const filterBank = document.getElementById('filterBank');
    const filterStatus = document.getElementById('filterStatus');
    const filterSearch = document.getElementById('filterSearch');
    const btnReset = document.getElementById('btnResetFilters');
    const rows = document.querySelectorAll('.rec-row');
    const resultsLabel = document.getElementById('filteredResultsLabel');
    const headerCountBadge = document.getElementById('headerCountBadge');

    function applyFilters() {
        const bankVal = filterBank.value;
        const statusVal = filterStatus.value;
        const searchVal = filterSearch.value.trim().toLowerCase();

        let visibleCount = 0;

        rows.forEach(row => {
            const rowBank = row.getAttribute('data-account-id');
            const rowStatus = row.getAttribute('data-status');
            const rowSearch = row.getAttribute('data-search') || '';

            const matchBank = !bankVal || rowBank === bankVal;
            const matchStatus = !statusVal || rowStatus === statusVal;
            const matchSearch = !searchVal || rowSearch.includes(searchVal);

            if (matchBank && matchStatus && matchSearch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        resultsLabel.textContent = `Showing ${visibleCount} reconciliation records`;
        headerCountBadge.textContent = `${visibleCount} Periods`;
    }

    if (filterBank) filterBank.addEventListener('change', applyFilters);
    if (filterStatus) filterStatus.addEventListener('change', applyFilters);
    if (filterSearch) filterSearch.addEventListener('input', applyFilters);

    if (btnReset) {
        btnReset.addEventListener('click', function() {
            filterBank.value = '';
            filterStatus.value = '';
            filterSearch.value = '';
            applyFilters();
        });
    }
});

function openDeleteRecModal(id, refNumber) {
    const modal = document.getElementById('deleteRecModal');
    const form = document.getElementById('deleteRecForm');
    const label = document.getElementById('deleteRecRefLabel');
    form.action = '<?= APP_URL ?>/bankreconciliation/delete/' + id;
    label.textContent = refNumber;
    modal.classList.add('active');
}

function closeDeleteRecModal() {
    const modal = document.getElementById('deleteRecModal');
    modal.classList.remove('active');
}
</script>
