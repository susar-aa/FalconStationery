<!-- Inter Font & FontAwesome Icons -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<?php
$totalDraftCount = 0; $totalDraftAmount = 0;
$totalTransitCount = 0; $totalTransitAmount = 0;
$totalCompletedCount = 0; $totalCompletedAmount = 0;

if (!empty($data['deposits'])) {
    foreach ($data['deposits'] as $dep) {
        $tot = floatval($dep->total_deposit ?? 0);
        if ($dep->status === 'Draft') {
            $totalDraftCount++;
            $totalDraftAmount += $tot;
        } elseif ($dep->status === 'Sent to Bank') {
            $totalTransitCount++;
            $totalTransitAmount += $tot;
        } elseif ($dep->status === 'Completed') {
            $totalCompletedCount++;
            $totalCompletedAmount += $tot;
        }
    }
}
?>

<style>
:root {
    --c-bg:           #f2f2f7;
    --c-surface:      #ffffff;
    --c-surface2:     #f9f9fb;
    --c-fill:         rgba(120,120,128,0.12);
    --c-fill2:        rgba(120,120,128,0.16);
    --c-separator:    rgba(60,60,67,0.12);

    --c-blue:         #007aff;
    --c-blue-light:   #e5f2ff;
    --c-green:        #34c759;
    --c-green-light:  #e6f9ec;
    --c-orange:       #ff9500;
    --c-orange-light: #fff4e5;
    --c-purple:       #af52de;
    --c-purple-light: #f5e8ff;
    --c-red:          #ff3b30;
    --c-red-light:    #fff0ef;

    --f-system: -apple-system, 'SF Pro Display', 'SF Pro Text', 'Inter', sans-serif;
    --f-mono:   ui-monospace, 'SF Mono', 'Menlo', 'Monaco', monospace;

    --t-primary:   #1c1c1e;
    --t-secondary: #636366;
    --t-label:     #8e8e93;

    --shadow-xs:  0 1px 2px rgba(0,0,0,0.04);
    --shadow-sm:  0 2px 8px rgba(0,0,0,0.06);
    --shadow-md:  0 8px 24px rgba(0,0,0,0.08);

    --r-sm: 10px;
    --r-md: 14px;
    --r-lg: 20px;
    --r-pill: 999px;
}

@media (prefers-color-scheme: dark) {
    :root {
        --c-bg:           #121212;
        --c-surface:      #1e1e2e;
        --c-surface2:     #161622;
        --c-fill:         rgba(255,255,255,0.08);
        --c-fill2:        rgba(255,255,255,0.12);
        --c-separator:    rgba(255,255,255,0.15);
        --t-primary:   #f5f5f7;
        --t-secondary: #a1a1aa;
        --t-label:     #52525b;
    }
}

.deposit-root {
    font-family: var(--f-system);
    font-size: 15px;
    color: var(--t-primary);
    -webkit-font-smoothing: antialiased;
}

/* Page Header */
.sf-page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}
.sf-page-title h1 {
    font-size: 28px;
    font-weight: 800;
    margin: 0 0 4px 0;
    letter-spacing: -0.02em;
}
.sf-page-title p {
    margin: 0;
    color: var(--t-secondary);
    font-size: 14px;
}

/* KPI Cards */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}
@media (max-width: 640px) {
    .kpi-grid { grid-template-columns: 1fr; }
}
.kpi-card {
    background: var(--c-surface);
    border-radius: var(--r-md);
    padding: 20px;
    border: 0.5px solid var(--c-separator);
    box-shadow: var(--shadow-xs);
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.kpi-label {
    font-size: 11px;
    font-weight: 700;
    color: var(--t-secondary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.kpi-val {
    font-size: 24px;
    font-weight: 800;
    font-family: var(--f-mono);
    color: var(--t-primary);
}
.kpi-sub {
    font-size: 12px;
    color: var(--t-label);
    font-weight: 500;
}

/* Segmented Control & Controls Header */
.sf-controls-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 16px;
}
.sf-segments {
    display: inline-flex;
    background: var(--c-fill);
    padding: 4px;
    border-radius: var(--r-md);
    gap: 2px;
}
.sf-seg-btn {
    padding: 8px 18px;
    font-size: 13px;
    font-weight: 600;
    font-family: var(--f-system);
    border: none;
    background: transparent;
    color: var(--t-secondary);
    border-radius: var(--r-sm);
    cursor: pointer;
    transition: all 0.18s ease;
}
.sf-seg-btn.active {
    background: var(--c-surface);
    color: var(--t-primary);
    box-shadow: var(--shadow-xs);
}

/* Search Input */
.search-box {
    display: flex;
    align-items: center;
    gap: 8px;
    background: var(--c-surface);
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-pill);
    padding: 8px 16px;
    width: 280px;
    box-shadow: var(--shadow-xs);
}
.search-box i { color: var(--t-label); }
.search-box input {
    border: none;
    outline: none;
    background: transparent;
    font-size: 13px;
    color: inherit;
    width: 100%;
}

/* Table Panel */
.table-panel {
    background: var(--c-surface);
    border-radius: var(--r-lg);
    border: 0.5px solid var(--c-separator);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}
.sf-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
    font-size: 14px;
}
.sf-table th {
    background: var(--c-surface2);
    padding: 14px 18px;
    font-size: 11px;
    font-weight: 700;
    color: var(--t-secondary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 0.5px solid var(--c-separator);
}
.sf-table td {
    padding: 14px 18px;
    border-bottom: 0.5px solid var(--c-separator);
    vertical-align: middle;
}
.sf-table tr:last-child td { border-bottom: none; }
.sf-table tr:hover td { background: var(--c-fill); }

/* Pagination Styles */
.sf-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 20px;
    background: var(--c-surface2);
    border-top: 0.5px solid var(--c-separator);
    flex-wrap: wrap;
    gap: 12px;
}
.pg-info { font-size: 13px; color: var(--t-secondary); }
.pg-right { display: flex; align-items: center; gap: 16px; }
.pg-size-wrap { display: flex; align-items: center; gap: 8px; }
.pg-size-lbl { font-size: 11px; font-weight: 700; color: var(--t-label); text-transform: uppercase; letter-spacing: 0.05em; }
.pg-size-sel {
    font-family: var(--f-system); font-size: 13px; font-weight: 600;
    color: var(--t-primary); background: var(--c-surface);
    border: 0.5px solid var(--c-separator); border-radius: var(--r-sm);
    padding: 4px 8px; outline: none; cursor: pointer;
}
.pg-nav { display: flex; align-items: center; border: 0.5px solid var(--c-separator); border-radius: var(--r-sm); overflow: hidden; background: var(--c-surface); }
.pg-btn {
    width: 34px; height: 32px;
    display: flex; align-items: center; justify-content: center;
    background: transparent; border: none; cursor: pointer;
    color: var(--t-primary); font-size: 12px;
    transition: background 0.18s ease;
}
.pg-btn:hover:not(:disabled) { background: var(--c-fill); }
.pg-btn:disabled { opacity: 0.35; cursor: not-allowed; }
.pg-current {
    padding: 0 12px; font-size: 13px; font-weight: 600;
    color: var(--t-secondary); font-family: var(--f-mono);
    border-left: 0.5px solid var(--c-separator);
    border-right: 0.5px solid var(--c-separator);
    height: 32px; display: flex; align-items: center;
}

/* Badges */
.sf-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: var(--r-pill);
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
}
.sf-badge .dot { width: 6px; height: 6px; border-radius: 50%; }
.badge-Draft { background: var(--c-fill2); color: var(--t-secondary); }
.badge-Draft .dot { background: var(--t-label); }
.badge-Sent { background: var(--c-blue-light); color: var(--c-blue); }
.badge-Sent .dot { background: var(--c-blue); }
.badge-Completed { background: var(--c-green-light); color: #15803d; }
.badge-Completed .dot { background: var(--c-green); }

/* Buttons */
.btn-sf {
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 600;
    border-radius: var(--r-sm);
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    transition: all 0.18s ease;
}
.btn-sf-primary { background: var(--c-blue); color: #fff; }
.btn-sf-primary:hover { opacity: 0.9; transform: translateY(-1px); }
.btn-sf-secondary { background: var(--c-fill); color: var(--t-primary); }
.btn-sf-secondary:hover { background: var(--c-fill2); }
.btn-sf-success { background: var(--c-green); color: #fff; }
.btn-sf-success:hover { opacity: 0.9; }
.btn-sf-orange { background: var(--c-orange); color: #fff; }
.btn-sf-orange:hover { opacity: 0.9; }
.btn-sf-danger { background: var(--c-red-light); color: var(--c-red); }
.btn-sf-danger:hover { background: var(--c-red); color: #fff; }

.act-group { display: flex; gap: 6px; justify-content: center; }

/* Alerts */
.sf-alert {
    padding: 14px 18px;
    border-radius: var(--r-md);
    margin-bottom: 20px;
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}
.sf-alert-success { background: var(--c-green-light); color: #166534; border: 0.5px solid rgba(52,199,89,0.3); }
.sf-alert-error { background: var(--c-red-light); color: #991b1b; border: 0.5px solid rgba(255,59,48,0.3); }
</style>

<div class="deposit-root">
    <div class="sf-page-header">
        <div class="sf-page-title">
            <h1>Bank Deposits Workflow</h1>
            <p>Prepare, track, and clear cash & cheque bank deposits into general ledger accounts</p>
        </div>
        <div>
            <a href="<?= APP_URL ?>/deposit/create" class="btn-sf btn-sf-primary" style="padding: 10px 20px; border-radius: var(--r-pill); font-size: 14px;">
                <i class="fa-solid fa-plus"></i> New Bank Deposit
            </a>
        </div>
    </div>

    <!-- Alerts -->
    <?php if(!empty($data['success'])): ?>
        <div class="sf-alert sf-alert-success">
            <i class="fa-solid fa-circle-check" style="font-size: 18px;"></i>
            <span><?= htmlspecialchars($data['success']) ?></span>
        </div>
    <?php endif; ?>
    <?php if(!empty($data['error'])): ?>
        <div class="sf-alert sf-alert-error">
            <i class="fa-solid fa-triangle-exclamation" style="font-size: 18px;"></i>
            <span><?= htmlspecialchars($data['error']) ?></span>
        </div>
    <?php endif; ?>

    <!-- KPI Cards (Drafts & In Transit only) -->
    <div class="kpi-grid">
        <div class="kpi-card" style="justify-content: space-between;">
            <div>
                <div class="kpi-label" style="color: var(--t-secondary);">Draft Deposits</div>
                <div class="kpi-val">Rs: <?= number_format($totalDraftAmount, 2) ?></div>
                <div class="kpi-sub"><?= $totalDraftCount ?> Drafts pending submission</div>
            </div>
            <?php if ($totalDraftCount > 0): ?>
                <div style="margin-top: 12px;">
                    <button type="button" onclick="submitBulkSendAll()" class="btn-sf btn-sf-success" style="padding: 7px 14px; font-size: 12px; border-radius: var(--r-pill);">
                        <i class="fa-solid fa-paper-plane"></i> Send All (<?= $totalDraftCount ?>) Drafts to Bank
                    </button>
                </div>
            <?php endif; ?>
        </div>
        <div class="kpi-card">
            <div class="kpi-label" style="color: var(--c-blue);">In Transit (Sent to Bank)</div>
            <div class="kpi-val" style="color: var(--c-blue);">Rs: <?= number_format($totalTransitAmount, 2) ?></div>
            <div class="kpi-sub"><?= $totalTransitCount ?> Deposits awaiting bank clearance</div>
        </div>
    </div>

    <!-- Controls Bar -->
    <div class="sf-controls-bar">
        <div class="sf-segments">
            <button class="sf-seg-btn active" onclick="filterTable('all', this)">All Deposits</button>
            <button class="sf-seg-btn" onclick="filterTable('Draft', this)">Drafts (<?= $totalDraftCount ?>)</button>
            <button class="sf-seg-btn" onclick="filterTable('Sent to Bank', this)">In Transit (<?= $totalTransitCount ?>)</button>
            <button class="sf-seg-btn" onclick="filterTable('Completed', this)">Completed (<?= $totalCompletedCount ?>)</button>
        </div>

        <div style="display: flex; gap: 12px; align-items: center;">
            <?php if ($totalDraftCount > 0): ?>
                <button type="button" id="btnSendSelectedDrafts" class="btn-sf btn-sf-primary" style="display: none; padding: 8px 16px; border-radius: var(--r-pill);" onclick="submitBulkSendSelected()">
                    <i class="fa-solid fa-check-double"></i> Send Selected (<span id="selectedCount">0</span>)
                </button>
                <button type="button" id="btnSendAllDraftsHeader" onclick="submitBulkSendAll()" class="btn-sf btn-sf-success" style="padding: 8px 16px; border-radius: var(--r-pill);">
                    <i class="fa-solid fa-paper-plane"></i> Send All Drafts (<?= $totalDraftCount ?>)
                </button>
            <?php endif; ?>

            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="depositSearch" placeholder="Search deposit #, bank, date..." onkeyup="searchDeposits()">
            </div>
        </div>
    </div>

    <!-- Table Form Wrap for Bulk Submit -->
    <form id="bulkSendForm" action="<?= APP_URL ?>/deposit/bulkSend" method="POST">
        <input type="hidden" name="send_all" id="sendAllFlag" value="0">
        
        <!-- Table Panel -->
        <div class="table-panel">
            <table class="sf-table" id="depositsTable">
                <thead>
                    <tr>
                        <th style="width: 40px; text-align: center;">
                            <input type="checkbox" id="selectAllDraftsCheckbox" onclick="toggleSelectAllDrafts(this)" title="Select all visible drafts">
                        </th>
                        <th style="width: 130px;">Deposit #</th>
                        <th style="width: 110px;">Date</th>
                        <th>Destination Bank</th>
                        <th style="text-align: right; width: 130px;">Cash</th>
                        <th style="text-align: right; width: 130px;">Cheques</th>
                        <th style="text-align: right; width: 140px;">Grand Total</th>
                        <th style="text-align: center; width: 130px;">Status</th>
                        <th style="text-align: center; width: 220px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($data['deposits'])): ?>
                        <tr class="no-records-row">
                            <td colspan="9" style="text-align: center; color: var(--t-secondary); padding: 50px;">
                                <i class="fa-solid fa-building-columns" style="font-size: 36px; opacity: 0.4; margin-bottom: 12px; display: block;"></i>
                                No deposit records found. Click <strong>"New Bank Deposit"</strong> to start a cash/cheque batch.
                            </td>
                        </tr>
                    <?php else: foreach($data['deposits'] as $dep): 
                        $statusClass = str_replace(' ', '', $dep->status);
                    ?>
                        <tr class="deposit-row" data-status="<?= htmlspecialchars($dep->status) ?>">
                            <td style="text-align: center;">
                                <?php if ($dep->status === 'Draft'): ?>
                                    <input type="checkbox" name="deposit_ids[]" value="<?= $dep->id ?>" class="draft-cb" onchange="updateBulkSelectionState()">
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="font-family: var(--f-mono); font-weight: 700; color: var(--c-blue); background: var(--c-blue-light); padding: 4px 8px; border-radius: 6px; font-size: 13px;">
                                    #<?= htmlspecialchars($dep->deposit_number) ?>
                                </span>
                            </td>
                            <td style="font-size: 13px; font-weight: 500; color: var(--t-secondary);">
                                <?= date('M d, Y', strtotime($dep->deposit_date)) ?>
                            </td>
                            <td>
                                <strong style="font-weight: 600; color: var(--t-primary);"><?= htmlspecialchars($dep->bank_name) ?></strong>
                                <div style="font-size: 11px; color: var(--t-label);">By <?= htmlspecialchars($dep->prepared_by_name ?: 'System User') ?></div>
                            </td>
                            <td style="text-align: right; font-family: var(--f-mono); font-size: 13px;">
                                Rs <?= number_format($dep->cash_total, 2) ?>
                            </td>
                            <td style="text-align: right; font-family: var(--f-mono); font-size: 13px;">
                                Rs <?= number_format($dep->cheque_total, 2) ?>
                            </td>
                            <td style="text-align: right; font-weight: 700; font-family: var(--f-mono); font-size: 14px; color: var(--c-green);">
                                Rs <?= number_format($dep->total_deposit, 2) ?>
                            </td>
                            <td style="text-align: center;">
                                <div class="sf-badge badge-<?= $statusClass ?>">
                                    <div class="dot"></div> <?= htmlspecialchars($dep->status === 'Sent to Bank' ? 'In Transit' : $dep->status) ?>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <div class="act-group">
                                    <?php if ($dep->status === 'Draft'): ?>
                                        <a href="<?= APP_URL ?>/deposit/edit/<?= $dep->id ?>" class="btn-sf btn-sf-secondary" style="padding: 5px 10px; font-size: 12px;" title="Edit Draft">
                                            <i class="fa-solid fa-pen"></i> Edit
                                        </a>
                                        <a href="<?= APP_URL ?>/deposit/send/<?= $dep->id ?>" class="btn-sf btn-sf-success" style="padding: 5px 10px; font-size: 12px;" onclick="return confirm('Are you sure you want to send this deposit to the bank? This will debit Deposit in Transit and credit Cash/Cheque in Hand.')" title="Post to Transit">
                                            <i class="fa-solid fa-paper-plane"></i> Send
                                        </a>
                                        <a href="<?= APP_URL ?>/deposit/delete/<?= $dep->id ?>" class="btn-sf btn-sf-danger" style="padding: 5px 10px; font-size: 12px;" onclick="return confirm('Are you sure you want to delete this draft?')" title="Delete Draft">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    <?php elseif ($dep->status === 'Sent to Bank'): ?>
                                        <a href="<?= APP_URL ?>/deposit/process/<?= $dep->id ?>" class="btn-sf btn-sf-orange" style="padding: 5px 12px; font-size: 12px;" title="Realize & Process Deposit">
                                            <i class="fa-solid fa-gears"></i> Process
                                        </a>
                                        <a href="<?= APP_URL ?>/deposit/printSlip/<?= $dep->id ?>" target="_blank" class="btn-sf btn-sf-secondary" style="padding: 5px 10px; font-size: 12px;" title="Print Deposit Slip">
                                            <i class="fa-solid fa-print"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= APP_URL ?>/deposit/process/<?= $dep->id ?>" class="btn-sf btn-sf-secondary" style="padding: 5px 10px; font-size: 12px;" title="View Details">
                                            <i class="fa-solid fa-eye"></i> View
                                        </a>
                                        <a href="<?= APP_URL ?>/deposit/printSlip/<?= $dep->id ?>" target="_blank" class="btn-sf btn-sf-secondary" style="padding: 5px 10px; font-size: 12px;" title="Print Deposit Slip">
                                            <i class="fa-solid fa-print"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </form>

        <!-- Pagination Bar -->
        <div class="sf-pagination">
            <div class="pg-info" id="pgInfo">Showing <strong>0</strong> – <strong>0</strong> of <strong>0</strong> deposits</div>
            <div class="pg-right">
                <div class="pg-size-wrap">
                    <span class="pg-size-lbl">Show:</span>
                    <select class="pg-size-sel" id="pgSizeSel" onchange="changePageSize(this.value)">
                        <option value="10" selected>10 per page</option>
                        <option value="25">25 per page</option>
                        <option value="50">50 per page</option>
                        <option value="100">100 per page</option>
                    </select>
                </div>
                <div class="pg-nav" id="pgNav">
                    <button class="pg-btn" id="btnPrev" onclick="navigatePage(-1)" title="Previous Page">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <div class="pg-current" id="pgCurrent">1 / 1</div>
                    <button class="pg-btn" id="btnNext" onclick="navigatePage(1)" title="Next Page">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let activeFilter = 'all';
    let currentPage = 1;
    let pageSize = 10;
    let matchingRows = [];

    function filterTable(status, btn) {
        activeFilter = status;
        document.querySelectorAll('.sf-seg-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        searchDeposits();
    }

    function searchDeposits() {
        const query = document.getElementById('depositSearch').value.toLowerCase().trim();
        const rows = Array.from(document.querySelectorAll('.deposit-row'));

        matchingRows = rows.filter(row => {
            const status = row.getAttribute('data-status');
            const text = row.innerText.toLowerCase();

            const matchesStatus = (activeFilter === 'all') || (status === activeFilter);
            const matchesQuery = !query || text.includes(query);

            return matchesStatus && matchesQuery;
        });

        currentPage = 1;
        renderPagination();
    }

    function renderPagination() {
        const rows = document.querySelectorAll('.deposit-row');
        rows.forEach(r => r.style.display = 'none');

        const totalItems = matchingRows.length;
        const totalPages = Math.ceil(totalItems / pageSize) || 1;

        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        const startIndex = totalItems > 0 ? (currentPage - 1) * pageSize + 1 : 0;
        const endIndex = Math.min(currentPage * pageSize, totalItems);

        // Display page slice
        const slice = matchingRows.slice((currentPage - 1) * pageSize, currentPage * pageSize);
        slice.forEach(r => r.style.display = '');

        // Update info text
        const pgInfo = document.getElementById('pgInfo');
        if (pgInfo) {
            pgInfo.innerHTML = totalItems > 0
                ? `Showing <strong>${startIndex}</strong> – <strong>${endIndex}</strong> of <strong>${totalItems}</strong> deposits`
                : `No matching deposit records found`;
        }

        // Update pagination controls
        const pgCurrent = document.getElementById('pgCurrent');
        if (pgCurrent) pgCurrent.innerText = `${currentPage} / ${totalPages}`;

        const btnPrev = document.getElementById('btnPrev');
        if (btnPrev) btnPrev.disabled = (currentPage <= 1);

        const btnNext = document.getElementById('btnNext');
        if (btnNext) btnNext.disabled = (currentPage >= totalPages);
    }

    function navigatePage(direction) {
        currentPage += direction;
        renderPagination();
    }

    function changePageSize(size) {
        pageSize = parseInt(size) || 10;
        currentPage = 1;
        renderPagination();
    }

    function toggleSelectAllDrafts(master) {
        const cbs = document.querySelectorAll('.draft-cb');
        cbs.forEach(cb => {
            const tr = cb.closest('tr');
            if (tr && tr.style.display !== 'none') {
                cb.checked = master.checked;
            }
        });
        updateBulkSelectionState();
    }

    function updateBulkSelectionState() {
        const checked = document.querySelectorAll('.draft-cb:checked');
        const count = checked.length;
        const btnSelected = document.getElementById('btnSendSelectedDrafts');
        const selectedCountSpan = document.getElementById('selectedCount');
        
        if (selectedCountSpan) selectedCountSpan.innerText = count;
        if (btnSelected) {
            btnSelected.style.display = count > 0 ? 'inline-flex' : 'none';
        }
    }

    function submitBulkSendAll() {
        if (confirm('Are you sure you want to send ALL <?= $totalDraftCount ?> draft deposit(s) (totaling Rs <?= number_format($totalDraftAmount, 2) ?>) to the bank at once?\n\nThis will post transit entries to General Ledger and Bank Register.')) {
            document.querySelectorAll('.draft-cb').forEach(cb => cb.checked = false);
            document.getElementById('sendAllFlag').value = '1';
            document.getElementById('bulkSendForm').submit();
        }
    }

    function submitBulkSendSelected() {
        const count = document.querySelectorAll('.draft-cb:checked').length;
        if (count === 0) return;
        if (confirm(`Are you sure you want to send the ${count} selected draft deposit(s) to the bank?`)) {
            document.getElementById('sendAllFlag').value = '0';
            document.getElementById('bulkSendForm').submit();
        }
    }

    // Initialize pagination on DOM ready
    window.addEventListener('DOMContentLoaded', () => {
        searchDeposits();
    });
</script>
