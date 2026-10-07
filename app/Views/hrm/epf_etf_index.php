<!-- Inter Font & FontAwesome Icons -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

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

.ee-root {
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

/* Flash Banners */
.flash-msg {
    padding: 12px 18px;
    border-radius: var(--r-md);
    margin-bottom: 20px;
    font-size: 14px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 10px;
}
.flash-msg-success { background: var(--c-green-light); color: #15803d; border: 0.5px solid rgba(52,199,89,0.3); }
.flash-msg-error { background: var(--c-red-light); color: #b91c1c; border: 0.5px solid rgba(255,59,48,0.3); }

/* KPI Stats Row */
.kpi-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}
.kpi-card {
    background: var(--c-surface);
    border-radius: var(--r-md);
    border: 0.5px solid var(--c-separator);
    padding: 18px 20px;
    box-shadow: var(--shadow-xs);
}
.kpi-card .kpi-label {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--t-secondary);
    letter-spacing: 0.05em;
    margin-bottom: 6px;
}
.kpi-card .kpi-val {
    font-size: 24px;
    font-weight: 800;
    color: var(--t-primary);
    letter-spacing: -0.02em;
}

/* Filter Bar */
.filter-card {
    background: var(--c-surface);
    border-radius: var(--r-md);
    border: 0.5px solid var(--c-separator);
    padding: 16px 20px;
    margin-bottom: 24px;
    display: flex;
    gap: 16px;
    align-items: center;
    flex-wrap: wrap;
}
.filter-group { display: flex; align-items: center; gap: 8px; }
.filter-group label { font-size: 13px; font-weight: 600; color: var(--t-secondary); }
.filter-select {
    padding: 8px 14px;
    border-radius: var(--r-sm);
    border: 1px solid var(--c-separator);
    background: var(--c-surface2);
    font-size: 13px;
    font-weight: 600;
    color: var(--t-primary);
}

/* Table Panels */
.table-panel {
    background: var(--c-surface);
    border-radius: var(--r-lg);
    border: 0.5px solid var(--c-separator);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    margin-bottom: 30px;
}
.panel-header {
    padding: 16px 24px;
    background: var(--c-surface2);
    border-bottom: 0.5px solid var(--c-separator);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.panel-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 700;
}
.sf-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
    font-size: 14px;
}
.sf-table th {
    background: var(--c-surface2);
    padding: 12px 18px;
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

/* Badges */
.sf-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: var(--r-pill);
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
}
.sf-badge .dot { width: 6px; height: 6px; border-radius: 50%; }
.badge-Unpaid { background: var(--c-red-light); color: #b91c1c; }
.badge-Unpaid .dot { background: var(--c-red); }
.badge-Partially { background: var(--c-orange-light); color: #c2410c; }
.badge-Partially .dot { background: var(--c-orange); }
.badge-Paid { background: var(--c-green-light); color: #15803d; }
.badge-Paid .dot { background: var(--c-green); }

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
.btn-sf-primary:hover { opacity: 0.9; transform: translateY(-1px); color: #fff; }
.btn-sf-success { background: #16a34a; color: #fff; }
.btn-sf-success:hover { background: #15803d; color: #fff; }
.btn-sf-secondary { background: var(--c-fill); color: var(--t-primary); }
.btn-sf-secondary:hover { background: var(--c-fill2); }
.act-group { display: flex; gap: 6px; justify-content: center; }
</style>

<div class="ee-root">
    <?php if (!empty($data['success'])): ?>
        <div class="flash-msg flash-msg-success">
            <i class="fa-solid fa-circle-check"></i>
            <span><?= htmlspecialchars($data['success']); ?></span>
        </div>
    <?php endif; ?>

    <?php if (!empty($data['error'])): ?>
        <div class="flash-msg flash-msg-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span><?= htmlspecialchars($data['error']); ?></span>
        </div>
    <?php endif; ?>

    <div class="sf-page-header">
        <div class="sf-page-title">
            <h1><i class="fa-solid fa-shield-halved" style="color: var(--c-blue);"></i> EPF & ETF Management</h1>
            <p>Manage Employees Provident Fund (20%) & Employees Trust Fund (3%) Contributions, Liabilities & Payments</p>
        </div>
        <div>
            <a href="<?= APP_URL ?>/epfetf/reports" class="btn-sf btn-sf-primary" style="padding: 10px 20px; border-radius: var(--r-pill);">
                <i class="fa-solid fa-chart-pie"></i> EPF/ETF Compliance Reports
            </a>
        </div>
    </div>

    <?php
    $totEpf = 0; $totEtf = 0; $pendingEpf = 0; $pendingEtf = 0;
    if (!empty($data['liabilities'])) {
        foreach ($data['liabilities'] as $l) {
            $epfLiab = floatval($l->total_epf_employee) + floatval($l->total_epf_employer);
            $etfLiab = floatval($l->total_etf_employer);
            $totEpf += $epfLiab;
            $totEtf += $etfLiab;
            $pendingEpf += max(0, $epfLiab - floatval($l->epf_paid_amount));
            $pendingEtf += max(0, $etfLiab - floatval($l->etf_paid_amount));
        }
    }
    ?>

    <!-- KPI Summary Row -->
    <div class="kpi-row">
        <div class="kpi-card">
            <div class="kpi-label">Total EPF Liability (20%)</div>
            <div class="kpi-val">LKR <?= number_format($totEpf, 2) ?></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Total ETF Liability (3%)</div>
            <div class="kpi-val">LKR <?= number_format($totEtf, 2) ?></div>
        </div>
        <div class="kpi-card" style="border-left: 4px solid var(--c-orange);">
            <div class="kpi-label">Pending EPF Payable</div>
            <div class="kpi-val" style="color: var(--c-orange);">LKR <?= number_format($pendingEpf, 2) ?></div>
        </div>
        <div class="kpi-card" style="border-left: 4px solid var(--c-red);">
            <div class="kpi-label">Pending ETF Payable</div>
            <div class="kpi-val" style="color: var(--c-red);">LKR <?= number_format($pendingEtf, 2) ?></div>
        </div>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="<?= APP_URL ?>/epfetf" class="filter-card">
        <div class="filter-group">
            <label><i class="fa-solid fa-calendar"></i> Year:</label>
            <select name="year" class="filter-select" onchange="this.form.submit()">
                <?php for($y = date('Y'); $y >= 2024; $y--): ?>
                    <option value="<?= $y ?>" <?= ($data['selected_year'] == $y) ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="filter-group">
            <label>Month:</label>
            <select name="month" class="filter-select" onchange="this.form.submit()">
                <option value="">All Months</option>
                <?php for($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>" <?= ($data['selected_month'] == $m) ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="filter-group">
            <label>Status:</label>
            <select name="status" class="filter-select" onchange="this.form.submit()">
                <option value="All">All Statuses</option>
                <option value="Unpaid" <?= ($data['selected_status'] === 'Unpaid') ? 'selected' : '' ?>>Unpaid</option>
                <option value="Partially Paid" <?= ($data['selected_status'] === 'Partially Paid') ? 'selected' : '' ?>>Partially Paid</option>
                <option value="Paid" <?= ($data['selected_status'] === 'Paid') ? 'selected' : '' ?>>Paid</option>
            </select>
        </div>
    </form>

    <!-- Monthly EPF/ETF Liabilities Panel -->
    <div class="table-panel">
        <div class="panel-header">
            <h3><i class="fa-solid fa-list-check"></i> Monthly EPF & ETF Liabilities</h3>
            <span style="font-size: 12px; color: var(--t-secondary);">Liability Accounts: GL-2001 (EPF) & GL-2002 (ETF)</span>
        </div>
        <table class="sf-table">
            <thead>
                <tr>
                    <th style="padding-left: 24px;">Period</th>
                    <th>Employees</th>
                    <th style="text-align: right;">Employee EPF (8%)</th>
                    <th style="text-align: right;">Employer EPF (12%)</th>
                    <th style="text-align: right; font-weight:800;">Total EPF (20%)</th>
                    <th style="text-align: right; font-weight:800;">Employer ETF (3%)</th>
                    <th style="text-align: center;">EPF Status</th>
                    <th style="text-align: center;">ETF Status</th>
                    <th style="text-align: center; padding-right: 24px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['liabilities'])): ?>
                    <?php foreach ($data['liabilities'] as $l): ?>
                        <?php 
                        $epfTot = floatval($l->total_epf_employee) + floatval($l->total_epf_employer);
                        $etfTot = floatval($l->total_etf_employer);
                        $epfBal = max(0, $epfTot - floatval($l->epf_paid_amount));
                        $etfBal = max(0, $etfTot - floatval($l->etf_paid_amount));
                        ?>
                        <tr>
                            <td style="padding-left: 24px; font-weight: 700;">
                                <?= date('M Y', strtotime($l->period_end)) ?>
                                <div style="font-size: 11px; color: var(--t-secondary); font-weight: 500;">
                                    PR-<?= $l->payroll_run_id ?> (<?= date('M d', strtotime($l->period_start)) ?> - <?= date('M d', strtotime($l->period_end)) ?>)
                                </div>
                            </td>
                            <td><span class="sf-badge" style="background: var(--c-fill2);"><?= $l->employee_count ?> Staff</span></td>
                            <td style="text-align: right;">LKR <?= number_format(floatval($l->total_epf_employee), 2) ?></td>
                            <td style="text-align: right;">LKR <?= number_format(floatval($l->total_epf_employer), 2) ?></td>
                            <td style="text-align: right; font-weight: 800; color: var(--c-blue);">
                                LKR <?= number_format($epfTot, 2) ?>
                                <?php if ($epfBal > 0 && $l->epf_paid_amount > 0): ?>
                                    <div style="font-size: 11px; color: var(--c-orange);">Bal: <?= number_format($epfBal, 2) ?></div>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right; font-weight: 800; color: var(--c-purple);">
                                LKR <?= number_format($etfTot, 2) ?>
                                <?php if ($etfBal > 0 && $l->etf_paid_amount > 0): ?>
                                    <div style="font-size: 11px; color: var(--c-orange);">Bal: <?= number_format($etfBal, 2) ?></div>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <span class="sf-badge badge-<?= str_replace(' ', '', $l->epf_payment_status) ?>">
                                    <span class="dot"></span> <?= htmlspecialchars($l->epf_payment_status) ?>
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <span class="sf-badge badge-<?= str_replace(' ', '', $l->etf_payment_status) ?>">
                                    <span class="dot"></span> <?= htmlspecialchars($l->etf_payment_status) ?>
                                </span>
                            </td>
                            <td style="text-align: center; padding-right: 24px;">
                                <div class="act-group">
                                    <?php if ($epfBal > 0): ?>
                                        <button class="btn-sf btn-sf-primary" style="border-radius: var(--r-pill); font-size: 11px; padding: 4px 10px;"
                                                onclick="openPayModal(<?= $l->payroll_run_id ?>, 'EPF', <?= $epfBal ?>, '<?= date('M Y', strtotime($l->period_end)) ?>')">
                                            <i class="fa-solid fa-credit-card"></i> Pay EPF
                                        </button>
                                    <?php endif; ?>

                                    <?php if ($etfBal > 0): ?>
                                        <button class="btn-sf btn-sf-success" style="border-radius: var(--r-pill); font-size: 11px; padding: 4px 10px;"
                                                onclick="openPayModal(<?= $l->payroll_run_id ?>, 'ETF', <?= $etfBal ?>, '<?= date('M Y', strtotime($l->period_end)) ?>')">
                                            <i class="fa-solid fa-credit-card"></i> Pay ETF
                                        </button>
                                    <?php endif; ?>

                                    <?php if ($epfBal <= 0 && $etfBal <= 0): ?>
                                        <span style="font-size: 12px; color: var(--c-green); font-weight: 700;"><i class="fa-solid fa-circle-check"></i> Settled</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 50px; color: var(--t-secondary);">
                            <i class="fa-solid fa-folder-open" style="font-size: 36px; opacity: 0.3; margin-bottom: 10px; display: block;"></i>
                            No approved payroll runs found for the selected period.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- EPF & ETF Payment History Log -->
    <div class="table-panel">
        <div class="panel-header">
            <h3><i class="fa-solid fa-clock-rotate-left"></i> EPF & ETF Payment Settlement Log</h3>
            <span style="font-size: 12px; color: var(--t-secondary);">Bank Settlements & Journal References</span>
        </div>
        <table class="sf-table">
            <thead>
                <tr>
                    <th style="padding-left: 24px;">Date</th>
                    <th>Type</th>
                    <th>Period</th>
                    <th style="text-align: right;">Amount Paid</th>
                    <th>Bank Account</th>
                    <th>Ref Number</th>
                    <th>Recorded By</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['payment_history'])): ?>
                    <?php foreach ($data['payment_history'] as $p): ?>
                        <tr>
                            <td style="padding-left: 24px; font-weight: 600;"><?= date('M d, Y', strtotime($p->payment_date)) ?></td>
                            <td>
                                <span class="sf-badge" style="background: <?= ($p->payment_type === 'EPF') ? 'var(--c-blue-light)' : 'var(--c-purple-light)' ?>; color: <?= ($p->payment_type === 'EPF') ? 'var(--c-blue)' : 'var(--c-purple)' ?>;">
                                    <?= $p->payment_type ?>
                                </span>
                            </td>
                            <td><?= date('M Y', strtotime($p->period_end)) ?></td>
                            <td style="text-align: right; font-weight: 700; color: var(--c-green);">
                                LKR <?= number_format(floatval($p->amount_paid), 2) ?>
                            </td>
                            <td><?= htmlspecialchars($p->bank_code . ' - ' . $p->bank_name) ?></td>
                            <td><code><?= htmlspecialchars($p->payment_reference ?? '') ?></code></td>
                            <td><?= htmlspecialchars($p->paid_by_user) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: var(--t-secondary);">
                            No EPF/ETF payment transactions recorded yet.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for EPF/ETF Payment -->
<div class="modal fade" id="payEpfEtfModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" action="<?= APP_URL ?>/epfetf/pay" method="POST" style="border-radius: var(--r-lg); border: none; box-shadow: var(--shadow-md);">
            <div class="modal-header" style="background: var(--c-surface2); border-bottom: 0.5px solid var(--c-separator);">
                <h5 class="modal-title" id="payModalTitle" style="font-weight: 800;"><i class="fa-solid fa-credit-card"></i> Record EPF / ETF Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <input type="hidden" name="payroll_run_id" id="modal_run_id">
                <input type="hidden" name="payment_type" id="modal_payment_type">

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; font-size: 13px;">Payment Type & Period</label>
                    <input type="text" class="form-control" id="modal_period_display" readonly style="background: var(--c-fill2); font-weight: 700;">
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; font-size: 13px;">Payment Date *</label>
                    <input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; font-size: 13px;">Select Bank Account *</label>
                    <select name="bank_account_id" class="form-select" required>
                        <option value="">-- Choose Bank Account --</option>
                        <?php if (!empty($data['bank_accounts'])): foreach ($data['bank_accounts'] as $b): ?>
                            <option value="<?= $b->id ?>"><?= htmlspecialchars($b->account_code . ' - ' . $b->account_name) ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; font-size: 13px;">Amount to Pay (LKR) *</label>
                    <input type="number" step="0.01" name="amount_paid" id="modal_amount_paid" class="form-control" style="font-weight: 800; font-size: 18px;" required>
                    <small class="text-muted" id="modal_max_balance"></small>
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; font-size: 13px;">Bank Cheque / Payment Reference</label>
                    <input type="text" name="payment_reference" class="form-control" placeholder="e.g. CHQ-948271 / CBO-EPF-1029">
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; font-size: 13px;">Notes</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Optional payment remarks..."></textarea>
                </div>
            </div>
            <div class="modal-footer" style="background: var(--c-surface2); border-top: 0.5px solid var(--c-separator);">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: var(--r-pill);">Cancel</button>
                <button type="submit" class="btn btn-primary" style="border-radius: var(--r-pill); font-weight: 700;">Confirm & Post Payment Entry</button>
            </div>
        </form>
    </div>
</div>

<script>
function openPayModal(runId, type, balance, periodName) {
    document.getElementById('modal_run_id').value = runId;
    document.getElementById('modal_payment_type').value = type;
    document.getElementById('modal_period_display').value = type + ' Settlement - ' + periodName;
    document.getElementById('modal_amount_paid').value = balance.toFixed(2);
    document.getElementById('modal_max_balance').innerText = 'Remaining Liability Balance: LKR ' + balance.toFixed(2);
    
    var modal = new bootstrap.Modal(document.getElementById('payEpfEtfModal'));
    modal.show();
}
</script>
