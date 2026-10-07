
<?php
$payrollRuns = $data['payroll_runs'] ?? [];
$totalRuns = count($payrollRuns);
$totalGrossVolume = array_sum(array_column($payrollRuns, 'total_gross'));
$approvedRuns = 0;
$paidRuns = 0;

foreach ($payrollRuns as $r) {
    if ($r->status === 'Approved') $approvedRuns++;
    if ($r->status === 'Paid') $paidRuns++;
}
?>

<!-- Inter Font & FontAwesome Icons -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
/* ============================================================
   SF PRO + APPLE DESIGN LANGUAGE — PAYROLL PROCESSING
   ============================================================ */

:root {
    --c-bg:           #f2f2f7;
    --c-surface:      #ffffff;
    --c-surface2:     #f9f9fb;
    --c-fill:         rgba(120,120,128,0.10);
    --c-fill2:        rgba(120,120,128,0.16);
    --c-separator:    rgba(60,60,67,0.12);
    --c-separator2:   rgba(60,60,67,0.06);

    --c-blue:         #007aff;
    --c-blue-light:   #e5f2ff;
    --c-green:        #34c759;
    --c-green-light:  #e6f9ec;
    --c-purple:       #af52de;
    --c-purple-light: #f5eeff;
    --c-orange:       #ff9500;
    --c-orange-light: #fff4e5;
    --c-red:          #ff3b30;
    --c-red-light:    #fff0ef;

    --f-system: -apple-system, 'SF Pro Display', 'SF Pro Text', 'Inter', 'Helvetica Neue', sans-serif;
    --f-mono:   ui-monospace, 'SF Mono', 'Menlo', 'Monaco', monospace;

    --t-primary:   #1c1c1e;
    --t-secondary: #636366;
    --t-tertiary:  #aeaeb2;
    --t-label:     #8e8e93;

    --shadow-xs:  0 1px 2px rgba(0,0,0,0.04);
    --shadow-sm:  0 2px 8px rgba(0,0,0,0.06), 0 1px 3px rgba(0,0,0,0.04);
    --shadow-md:  0 8px 24px rgba(0,0,0,0.08), 0 2px 6px rgba(0,0,0,0.04);
    --shadow-xl:  0 24px 48px rgba(0,0,0,0.14), 0 4px 12px rgba(0,0,0,0.06);

    --r-xs: 6px;
    --r-sm: 10px;
    --r-md: 14px;
    --r-lg: 20px;
    --r-xl: 26px;
    --r-pill: 999px;

    --ease-spring: cubic-bezier(0.34, 1.56, 0.64, 1);
    --ease-ios:    cubic-bezier(0.25, 0.1, 0.25, 1);
    --dur-fast:    0.18s;
    --dur-mid:     0.28s;
    --dur-slow:    0.42s;
}

@media (prefers-color-scheme: dark) {
    :root {
        --c-bg:           #121212;
        --c-surface:      #1e1e2e;
        --c-surface2:     #161622;
        --c-fill:         rgba(255,255,255,0.08);
        --c-fill2:        rgba(255,255,255,0.12);
        --c-separator:    rgba(255,255,255,0.15);
        --c-separator2:   rgba(255,255,255,0.08);
        --t-primary:   #f5f5f7;
        --t-secondary: #a1a1aa;
        --t-tertiary:  #71717a;
        --t-label:     #52525b;
    }
}

.sf-root {
    font-family: var(--f-system);
    font-size: 15px;
    color: var(--t-primary);
    background: var(--c-bg);
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

.sf-wrap {
    max-width: 1420px;
    margin: 0 auto;
    padding: 24px 24px 100px;
}

/* ---- Header ---- */
.sf-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}
.sf-eyebrow {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--c-blue);
    margin-bottom: 4px;
}
.sf-title {
    font-size: 30px;
    font-weight: 800;
    letter-spacing: -0.03em;
    line-height: 1.1;
    color: var(--t-primary);
    margin: 0 0 4px 0;
}
.sf-subtitle {
    font-size: 13.5px;
    color: var(--t-secondary);
    margin: 0;
}

/* ---- Stat Cards ---- */
.stat-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 24px;
}
@media (max-width: 1024px) {
    .stat-row { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 580px) {
    .stat-row { grid-template-columns: 1fr; }
}

.stat-card {
    background: var(--c-surface);
    border-radius: var(--r-xl);
    padding: 16px 20px;
    box-shadow: var(--shadow-sm);
    border: 0.5px solid var(--c-separator);
    transition: transform var(--dur-fast) var(--ease-ios), box-shadow var(--dur-fast) var(--ease-ios);
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    gap: 16px;
}
.stat-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: var(--r-xl) var(--r-xl) 0 0;
}
.stat-card.blue::before   { background: var(--c-blue); }
.stat-card.purple::before { background: var(--c-purple); }
.stat-card.orange::before { background: var(--c-orange); }
.stat-card.green::before  { background: var(--c-green); }

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}
.stat-icon {
    width: 44px; height: 44px;
    border-radius: var(--r-md);
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.stat-card.blue   .stat-icon { background: var(--c-blue-light);   color: var(--c-blue); }
.stat-card.purple .stat-icon { background: var(--c-purple-light); color: var(--c-purple); }
.stat-card.orange .stat-icon { background: var(--c-orange-light); color: var(--c-orange); }
.stat-card.green  .stat-icon { background: var(--c-green-light);  color: var(--c-green); }

.stat-info { display: flex; flex-direction: column; justify-content: center; }
.stat-num {
    font-size: 22px;
    font-weight: 800;
    letter-spacing: -0.04em;
    color: var(--t-primary);
    line-height: 1.1;
    margin-bottom: 2px;
}
.stat-lbl {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: var(--t-label);
}

/* ---- Filter Shelf ---- */
.filter-shelf {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
    margin-bottom: 20px;
}
.filter-chip {
    display: inline-flex; align-items: center; gap: 8px;
    background: var(--c-surface);
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-pill);
    padding: 6px 14px;
    font-size: 13px;
    font-weight: 500;
    color: var(--t-secondary);
    box-shadow: var(--shadow-xs);
    transition: border-color var(--dur-fast), box-shadow var(--dur-fast);
}
.filter-chip:focus-within {
    border-color: var(--c-blue);
    box-shadow: 0 0 0 3px rgba(0,122,255,0.12);
}
.filter-input {
    border: none; outline: none; background: transparent;
    font-family: var(--f-system); font-size: 13px; color: var(--t-primary);
    width: 220px;
}
.filter-select {
    border: none; outline: none; background: transparent;
    font-family: var(--f-system); font-size: 13px; font-weight: 600;
    color: var(--t-primary); cursor: pointer;
}
.filter-select option { background: var(--c-surface); color: var(--t-primary); }
.filter-reset {
    background: transparent; border: 0.5px solid var(--c-separator);
    border-radius: var(--r-pill); padding: 7px 14px; font-size: 12.5px;
    font-weight: 600; color: var(--t-secondary); cursor: pointer;
    transition: all var(--dur-fast);
}
.filter-reset:hover { background: var(--c-fill); color: var(--t-primary); }

.filter-count {
    margin-left: auto; font-size: 13px; color: var(--t-secondary); font-weight: 500;
}
.filter-count strong { color: var(--t-primary); font-weight: 700; }

/* ---- Alerts ---- */
.sf-alert {
    display: flex; align-items: flex-start; gap: 12px;
    background: var(--c-surface); border-radius: var(--r-md);
    padding: 14px 16px; margin-bottom: 20px; box-shadow: var(--shadow-xs);
    border: 0.5px solid var(--c-separator); border-left-width: 4px; font-size: 13.5px;
}
.sf-alert.success { border-left-color: var(--c-green); }
.sf-alert.error   { border-left-color: var(--c-red); }
.sf-alert-icon { font-size: 17px; flex-shrink: 0; padding-top: 1px; }
.sf-alert.success .sf-alert-icon { color: var(--c-green); }
.sf-alert.error   .sf-alert-icon { color: var(--c-red); }
.sf-alert-msg   { color: var(--t-primary); font-weight: 500; }

/* ---- Table Panel ---- */
.table-panel {
    background: var(--c-surface);
    border-radius: var(--r-xl);
    border: 0.5px solid var(--c-separator);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}
.sf-table { width: 100%; border-collapse: collapse; }
.sf-table thead th {
    padding: 13px 18px; font-size: 11px; font-weight: 700;
    letter-spacing: 0.06em; text-transform: uppercase; color: var(--t-label);
    background: var(--c-surface2); border-bottom: 0.5px solid var(--c-separator);
    white-space: nowrap; text-align: left;
}
.sf-table tbody tr {
    transition: background var(--dur-fast); border-bottom: 0.5px solid var(--c-separator2);
}
.sf-table tbody tr:last-child { border-bottom: none; }
.sf-table tbody tr:hover { background: var(--c-fill); }
.sf-table td {
    padding: 14px 18px; font-size: 13.5px; color: var(--t-primary); vertical-align: middle;
}

/* ---- Badges & Buttons ---- */
.sf-status-pill {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; border-radius: var(--r-pill);
    font-size: 11.5px; font-weight: 700;
}
.sf-status-pill.draft    { background: var(--c-fill2); color: var(--t-secondary); }
.sf-status-pill.approved { background: var(--c-blue-light); color: var(--c-blue); }
.sf-status-pill.paid     { background: var(--c-green-light); color: var(--c-green); }
.sf-status-pill .dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

.sf-btn {
    padding: 8px 16px; border-radius: var(--r-pill); font-size: 13px; font-weight: 600;
    display: inline-flex; align-items: center; gap: 6px; border: 0.5px solid transparent;
    cursor: pointer; transition: transform var(--dur-fast) var(--ease-spring), background var(--dur-fast), box-shadow var(--dur-fast);
    text-decoration: none; font-family: var(--f-system);
}
.sf-btn:active { transform: scale(0.97); }
.sf-btn.primary { background: var(--c-blue); color: #fff; box-shadow: 0 2px 8px rgba(0,122,255,0.25); }
.sf-btn.primary:hover { background: #0066d6; }
.sf-btn.neutral { background: var(--c-surface); border-color: var(--c-separator); color: var(--t-primary); box-shadow: var(--shadow-xs); }
.sf-btn.neutral:hover { background: var(--c-surface2); }
.sf-btn.sm { padding: 5px 12px; font-size: 12px; }

/* ---- SF Glass Overlay Modal ---- */
.sf-modal-overlay {
    display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.45); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
    z-index: 9999; align-items: center; justify-content: center; padding: 20px;
}
.sf-modal-card {
    background: var(--c-surface); border: 0.5px solid var(--c-separator);
    box-shadow: var(--shadow-xl); border-radius: var(--r-xl); padding: 28px;
    width: 100%; max-width: 520px; color: var(--t-primary); box-sizing: border-box;
    animation: modalPop var(--dur-mid) var(--ease-ios);
}
@keyframes modalPop {
    from { opacity: 0; transform: scale(0.96) translateY(8px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}

.sf-modal-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 20px; padding-bottom: 14px; border-bottom: 0.5px solid var(--c-separator);
}
.sf-modal-title { font-size: 18px; font-weight: 700; color: var(--t-primary); display: flex; align-items: center; gap: 8px; margin: 0; }
.sf-modal-close {
    background: var(--c-fill); border: none; width: 30px; height: 30px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center; color: var(--t-secondary);
    cursor: pointer; font-size: 14px; transition: background var(--dur-fast);
}
.sf-modal-close:hover { background: var(--c-fill2); color: var(--t-primary); }

.sf-field { margin-bottom: 16px; }
.sf-field label { display: block; margin-bottom: 6px; font-size: 12.5px; font-weight: 600; color: var(--t-secondary); }
.sf-input {
    width: 100%; padding: 9px 13px; border: 0.5px solid var(--c-separator); border-radius: var(--r-sm);
    background: var(--c-surface2); color: var(--t-primary); box-sizing: border-box;
    font-family: var(--f-system); font-size: 13.5px; outline: none; transition: border-color var(--dur-fast), box-shadow var(--dur-fast);
}
.sf-input:focus { border-color: var(--c-blue); box-shadow: 0 0 0 3px rgba(0,122,255,0.12); }
</style>

<div class="sf-root">
    <div class="sf-wrap">
        
        <!-- Header -->
        <div class="sf-header">
            <div>
                <div class="sf-eyebrow"><i class="fa-solid fa-coins"></i> Human Resource Accounting</div>
                <h1 class="sf-title">Payroll Runs</h1>
                <p class="sf-subtitle">Process monthly wages, calculate EPF/ETF contributions, and manage bank disbursements.</p>
            </div>
            <div>
                <button class="sf-btn primary" onclick="document.getElementById('newPayrollModal').style.display='flex'">
                    <i class="fa-solid fa-plus"></i> Run New Payroll
                </button>
            </div>
        </div>

        <!-- Flash Alerts -->
        <?php if(!empty($data['success'])): ?>
            <div class="sf-alert success">
                <i class="fa-solid fa-circle-check sf-alert-icon"></i>
                <div class="sf-alert-msg"><?= htmlspecialchars($data['success']) ?></div>
            </div>
        <?php endif; ?>
        <?php if(!empty($data['error'])): ?>
            <div class="sf-alert error">
                <i class="fa-solid fa-circle-exclamation sf-alert-icon"></i>
                <div class="sf-alert-msg"><?= htmlspecialchars($data['error']) ?></div>
            </div>
        <?php endif; ?>

        <!-- Stat Widgets Row -->
        <div class="stat-row">
            <div class="stat-card blue">
                <div class="stat-icon"><i class="fa-solid fa-receipt"></i></div>
                <div class="stat-info">
                    <div class="stat-num"><?= number_format($totalRuns) ?></div>
                    <div class="stat-lbl">Total Payroll Runs</div>
                </div>
            </div>

            <div class="stat-card purple">
                <div class="stat-icon"><i class="fa-solid fa-money-bill-wave"></i></div>
                <div class="stat-info">
                    <div class="stat-num">Rs. <?= number_format($totalGrossVolume, 2) ?></div>
                    <div class="stat-lbl">Cumulative Gross Volume</div>
                </div>
            </div>

            <div class="stat-card orange">
                <div class="stat-icon"><i class="fa-solid fa-file-signature"></i></div>
                <div class="stat-info">
                    <div class="stat-num"><?= number_format($approvedRuns) ?></div>
                    <div class="stat-lbl">Approved Runs</div>
                </div>
            </div>

            <div class="stat-card green">
                <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
                <div class="stat-info">
                    <div class="stat-num"><?= number_format($paidRuns) ?></div>
                    <div class="stat-lbl">Disbursed &amp; Paid</div>
                </div>
            </div>
        </div>

        <!-- Filter Shelf -->
        <div class="filter-shelf">
            <div class="filter-chip">
                <i class="fa-solid fa-magnifying-glass" style="color: var(--t-tertiary);"></i>
                <input type="text" id="payrollSearchInput" class="filter-input" placeholder="Search by Ref ID, user..." onkeyup="filterPayrollTable()">
            </div>

            <div class="filter-chip">
                <i class="fa-solid fa-filter" style="color: var(--t-tertiary);"></i>
                <select id="statusFilterSelect" class="filter-select" onchange="filterPayrollTable()">
                    <option value="">All Run Statuses</option>
                    <option value="Draft">Draft</option>
                    <option value="Approved">Approved</option>
                    <option value="Paid">Paid</option>
                </select>
            </div>

            <button class="filter-reset" onclick="resetPayrollFilters()"><i class="fa-solid fa-arrow-rotate-left"></i> Reset</button>

            <div class="filter-count">
                Showing <strong id="visibleCount"><?= count($payrollRuns) ?></strong> of <strong><?= count($payrollRuns) ?></strong> payroll runs
            </div>
        </div>

        <!-- Table Panel -->
        <div class="table-panel">
            <table class="sf-table" id="payrollTable">
                <thead>
                    <tr>
                        <th>Ref ID</th>
                        <th>Pay Period</th>
                        <th>Run Date</th>
                        <th style="text-align: right;">Total Gross Salary</th>
                        <th>Processed By</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($payrollRuns)): ?>
                        <tr id="noRecordsRow">
                            <td colspan="7" style="text-align: center; color: var(--t-secondary); padding: 50px 20px;">
                                <i class="fa-solid fa-receipt" style="font-size: 32px; color: var(--t-tertiary); margin-bottom: 10px; display: block;"></i>
                                No payroll runs recorded yet. Click "Run New Payroll" above to initiate a run.
                            </td>
                        </tr>
                    <?php else: foreach($payrollRuns as $run): 
                        $statusLower = strtolower($run->status);
                        $searchData = strtolower('pr-' . $run->id . ' ' . $run->username . ' ' . $run->status);
                    ?>
                        <tr class="payroll-row" data-search="<?= htmlspecialchars($searchData) ?>" data-status="<?= htmlspecialchars($run->status) ?>">
                            <td style="font-weight: 800; color: var(--c-blue); font-family: var(--f-mono);">PR-<?= $run->id ?></td>
                            <td style="font-weight: 600;">
                                <i class="fa-regular fa-calendar-days" style="color: var(--t-label); margin-right: 4px;"></i>
                                <?= date('d M', strtotime($run->period_start)) ?> &ndash; <?= date('d M Y', strtotime($run->period_end)) ?>
                            </td>
                            <td style="color: var(--t-secondary);"><?= date('d M Y', strtotime($run->run_date)) ?></td>
                            <td style="text-align: right; font-weight: 700; color: var(--t-primary); font-family: var(--f-mono);">
                                Rs. <?= number_format($run->total_gross, 2) ?>
                            </td>
                            <td style="font-weight: 500;">
                                <i class="fa-solid fa-user-gear" style="color: var(--t-tertiary); margin-right: 4px;"></i>
                                <?= htmlspecialchars($run->username) ?>
                            </td>
                            <td>
                                <span class="sf-status-pill <?= $statusLower ?>">
                                    <span class="dot"></span> <?= htmlspecialchars($run->status) ?>
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <a href="<?= APP_URL ?>/payroll/show/<?= $run->id ?>" class="sf-btn neutral sm">
                                    <i class="fa-solid fa-eye"></i> View Run
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- ============================================================
     NEW PAYROLL MODAL
     ============================================================ -->
<div class="sf-modal-overlay" id="newPayrollModal">
    <div class="sf-modal-card">
        <div class="sf-modal-header">
            <h3 class="sf-modal-title">
                <i class="fa-solid fa-calculator" style="color: var(--c-blue);"></i> Run New Payroll
            </h3>
            <button class="sf-modal-close" onclick="document.getElementById('newPayrollModal').style.display='none'"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form action="<?= APP_URL ?>/payroll/preview" method="POST">
            <div class="sf-field">
                <label>Period Start Date *</label>
                <input type="date" name="period_start" class="sf-input" required value="<?= date('Y-m-01') ?>">
            </div>
            
            <div class="sf-field">
                <label>Period End Date *</label>
                <input type="date" name="period_end" class="sf-input" required value="<?= date('Y-m-t') ?>">
            </div>
            
            <div class="sf-field">
                <label>Payroll Run Date *</label>
                <input type="date" name="run_date" class="sf-input" required value="<?= date('Y-m-d') ?>">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 0.5px solid var(--c-separator);">
                <button type="button" class="sf-btn neutral" onclick="document.getElementById('newPayrollModal').style.display='none'">Cancel</button>
                <button type="submit" class="sf-btn primary"><i class="fa-solid fa-arrow-right"></i> Preview Payroll</button>
            </div>
        </form>
    </div>
</div>

<script>
function filterPayrollTable() {
    const searchVal = document.getElementById('payrollSearchInput').value.toLowerCase().trim();
    const statusVal = document.getElementById('statusFilterSelect').value;

    const rows = document.querySelectorAll('.payroll-row');
    let visibleCount = 0;

    rows.forEach(row => {
        const searchData = row.getAttribute('data-search') || '';
        const statusData = row.getAttribute('data-status') || '';

        const matchesSearch = !searchVal || searchData.includes(searchVal);
        const matchesStatus = !statusVal || statusData === statusVal;

        if (matchesSearch && matchesStatus) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    const countElem = document.getElementById('visibleCount');
    if (countElem) countElem.innerText = visibleCount;

    const noRow = document.getElementById('noRecordsRow');
    if (noRow) {
        noRow.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
    }
}

function resetPayrollFilters() {
    document.getElementById('payrollSearchInput').value = '';
    document.getElementById('statusFilterSelect').value = '';
    filterPayrollTable();
}
</script>
