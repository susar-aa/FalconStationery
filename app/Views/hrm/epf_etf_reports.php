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
    --c-purple:       #af52de;
    --c-red:          #ff3b30;

    --f-system: -apple-system, 'SF Pro Display', 'SF Pro Text', 'Inter', sans-serif;
    --t-primary:   #1c1c1e;
    --t-secondary: #636366;

    --shadow-sm:  0 2px 8px rgba(0,0,0,0.06);
    --r-sm: 10px;
    --r-md: 14px;
    --r-lg: 20px;
    --r-pill: 999px;
}

.report-root {
    font-family: var(--f-system);
    font-size: 14px;
    color: var(--t-primary);
}

.report-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}
.report-header h1 {
    font-size: 26px;
    font-weight: 800;
    margin: 0;
}

/* Tabs */
.report-tabs {
    display: flex;
    gap: 8px;
    border-bottom: 1px solid var(--c-separator);
    margin-bottom: 24px;
    flex-wrap: wrap;
}
.report-tab {
    padding: 10px 18px;
    border-radius: var(--r-sm) var(--r-sm) 0 0;
    font-size: 13px;
    font-weight: 700;
    color: var(--t-secondary);
    text-decoration: none;
    background: var(--c-surface2);
    border: 0.5px solid var(--c-separator);
    border-bottom: none;
    transition: all 0.15s ease;
}
.report-tab.active {
    background: var(--c-blue);
    color: #fff;
    border-color: var(--c-blue);
}

/* Filters */
.report-filter {
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

/* Table */
.report-panel {
    background: var(--c-surface);
    border-radius: var(--r-lg);
    border: 0.5px solid var(--c-separator);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}
.rep-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.rep-table th {
    background: var(--c-surface2);
    padding: 12px 16px;
    font-size: 11px;
    font-weight: 700;
    color: var(--t-secondary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 0.5px solid var(--c-separator);
}
.rep-table td {
    padding: 12px 16px;
    border-bottom: 0.5px solid var(--c-separator);
    vertical-align: middle;
}
.rep-table tr:hover td { background: var(--c-fill); }
.rep-table tfoot td {
    background: var(--c-surface2);
    font-weight: 800;
    border-top: 2px solid var(--c-separator);
}

/* Print Utilities */
@media print {
    .report-tabs, .report-filter, .no-print { display: none !important; }
    .report-panel { border: none !important; box-shadow: none !important; }
    body { background: #fff !important; }
}
</style>

<div class="report-root">
    <div class="report-header">
        <div>
            <h1><i class="fa-solid fa-file-invoice-dollar" style="color: var(--c-blue);"></i> EPF & ETF Compliance Reports</h1>
            <p style="margin:4px 0 0 0; color: var(--t-secondary);">Statutory Monthly EPF (C Form), ETF Returns, and Contribution Summaries</p>
        </div>
        <div class="no-print">
            <button onclick="window.print()" class="btn btn-outline-secondary" style="border-radius: var(--r-pill); font-weight:600;">
                <i class="fa-solid fa-print"></i> Print Report
            </button>
            <a href="<?= APP_URL ?>/epfetf" class="btn btn-primary" style="border-radius: var(--r-pill); font-weight:600;">
                <i class="fa-solid fa-arrow-left"></i> Back to EPF/ETF Dashboard
            </a>
        </div>
    </div>

    <!-- Report Type Tabs -->
    <div class="report-tabs no-print">
        <a href="<?= APP_URL ?>/epfetf/reports?type=monthly_epf&run_id=<?= $data['selected_run_id'] ?>" class="report-tab <?= ($data['report_type'] === 'monthly_epf') ? 'active' : '' ?>">
            <i class="fa-solid fa-table"></i> Monthly EPF (20%)
        </a>
        <a href="<?= APP_URL ?>/epfetf/reports?type=monthly_etf&run_id=<?= $data['selected_run_id'] ?>" class="report-tab <?= ($data['report_type'] === 'monthly_etf') ? 'active' : '' ?>">
            <i class="fa-solid fa-table"></i> Monthly ETF (3%)
        </a>
        <a href="<?= APP_URL ?>/epfetf/reports?type=employee_history&employee_id=<?= $data['selected_employee_id'] ?>&year=<?= $data['selected_year'] ?>" class="report-tab <?= ($data['report_type'] === 'employee_history') ? 'active' : '' ?>">
            <i class="fa-solid fa-user-clock"></i> Employee History
        </a>
        <a href="<?= APP_URL ?>/epfetf/reports?type=employer_summary&year=<?= $data['selected_year'] ?>" class="report-tab <?= ($data['report_type'] === 'employer_summary') ? 'active' : '' ?>">
            <i class="fa-solid fa-chart-column"></i> Employer Expense Summary
        </a>
        <a href="<?= APP_URL ?>/epfetf/reports?type=payable_report&year=<?= $data['selected_year'] ?>" class="report-tab <?= ($data['report_type'] === 'payable_report') ? 'active' : '' ?>">
            <i class="fa-solid fa-receipt"></i> Payables & Settlement Status
        </a>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="<?= APP_URL ?>/epfetf/reports" class="report-filter no-print">
        <input type="hidden" name="type" value="<?= htmlspecialchars($data['report_type']) ?>">

        <?php if (in_array($data['report_type'], ['monthly_epf', 'monthly_etf'])): ?>
            <div class="d-flex align-items-center gap-2">
                <label style="font-weight: 700;">Select Payroll Run:</label>
                <select name="run_id" class="form-select" style="width: auto;" onchange="this.form.submit()">
                    <?php if(!empty($data['payroll_runs'])): foreach($data['payroll_runs'] as $r): ?>
                        <option value="<?= $r->id ?>" <?= ($data['selected_run_id'] == $r->id) ? 'selected' : '' ?>>
                            PR-<?= $r->id ?> (<?= date('M Y', strtotime($r->period_end)) ?>) - <?= date('M d', strtotime($r->period_start)) ?> to <?= date('M d, Y', strtotime($r->period_end)) ?>
                        </option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
        <?php endif; ?>

        <?php if ($data['report_type'] === 'employee_history'): ?>
            <div class="d-flex align-items-center gap-2">
                <label style="font-weight: 700;">Select Employee:</label>
                <select name="employee_id" class="form-select" style="width: auto;" onchange="this.form.submit()">
                    <option value="0">All Employees</option>
                    <?php if(!empty($data['employees'])): foreach($data['employees'] as $e): ?>
                        <option value="<?= $e->id ?>" <?= ($data['selected_employee_id'] == $e->id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($e->first_name . ' ' . $e->last_name . ' (' . ($e->employee_code ?? 'EMP-' . $e->id) . ')') ?>
                        </option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
        <?php endif; ?>

        <?php if (in_array($data['report_type'], ['employee_history', 'employer_summary', 'payable_report'])): ?>
            <div class="d-flex align-items-center gap-2">
                <label style="font-weight: 700;">Year:</label>
                <select name="year" class="form-select" style="width: auto;" onchange="this.form.submit()">
                    <?php for($y = date('Y'); $y >= 2024; $y--): ?>
                        <option value="<?= $y ?>" <?= ($data['selected_year'] == $y) ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        <?php endif; ?>
    </form>

    <!-- REPORT CONTENT DISPLAY -->
    <div class="report-panel">
        <?php if ($data['report_type'] === 'monthly_epf'): ?>
            <!-- Monthly EPF 20% Report -->
            <?php $rep = $data['report_data']; ?>
            <div style="padding: 20px; border-bottom: 1px solid var(--c-separator);">
                <h4 style="margin: 0; font-weight: 800;">Monthly EPF Return (Form C)</h4>
                <?php if (!empty($rep['run'])): ?>
                    <span class="text-muted">Period: <strong><?= date('F Y', strtotime($rep['run']->period_end)) ?></strong> (<?= date('M d, Y', strtotime($rep['run']->period_start)) ?> - <?= date('M d, Y', strtotime($rep['run']->period_end)) ?>)</span>
                <?php endif; ?>
            </div>
            <table class="rep-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Member No</th>
                        <th>Employee Name</th>
                        <th>Department</th>
                        <th style="text-align: right;">Basic Salary</th>
                        <th style="text-align: right;">EPF Earnings</th>
                        <th style="text-align: right;">Employee 8%</th>
                        <th style="text-align: right;">Employer 12%</th>
                        <th style="text-align: right; font-weight:800;">Total 20%</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rep['slips'])): $idx = 1; foreach ($rep['slips'] as $s): ?>
                        <tr>
                            <td><?= $idx++ ?></td>
                            <td><strong><?= htmlspecialchars($s['epf_member_no'] ?: 'N/A') ?></strong></td>
                            <td style="font-weight: 700;"><?= htmlspecialchars($s['employee_name']) ?></td>
                            <td><?= htmlspecialchars($s['department'] ?? 'General') ?></td>
                            <td style="text-align: right;">LKR <?= number_format($s['basic_salary'], 2) ?></td>
                            <td style="text-align: right;">LKR <?= number_format($s['epf_applicable_earnings'], 2) ?></td>
                            <td style="text-align: right; color: var(--c-blue);">LKR <?= number_format($s['employee_8_pct'], 2) ?></td>
                            <td style="text-align: right; color: var(--c-purple);">LKR <?= number_format($s['employer_12_pct'], 2) ?></td>
                            <td style="text-align: right; font-weight: 800;">LKR <?= number_format($s['total_20_pct'], 2) ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
                <?php if (!empty($rep['summary'])): ?>
                    <tfoot>
                        <tr>
                            <td colspan="4" style="text-align: right;">Grand Total:</td>
                            <td style="text-align: right;">LKR <?= number_format($rep['summary']['total_basic'], 2) ?></td>
                            <td style="text-align: right;">LKR <?= number_format($rep['summary']['total_epf_earnings'], 2) ?></td>
                            <td style="text-align: right; color: var(--c-blue);">LKR <?= number_format($rep['summary']['total_employee_8'], 2) ?></td>
                            <td style="text-align: right; color: var(--c-purple);">LKR <?= number_format($rep['summary']['total_employer_12'], 2) ?></td>
                            <td style="text-align: right; font-weight: 800; font-size: 15px;">LKR <?= number_format($rep['summary']['total_epf_20'], 2) ?></td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>

        <?php elseif ($data['report_type'] === 'monthly_etf'): ?>
            <!-- Monthly ETF 3% Report -->
            <?php $rep = $data['report_data']; ?>
            <div style="padding: 20px; border-bottom: 1px solid var(--c-separator);">
                <h4 style="margin: 0; font-weight: 800;">Monthly ETF Return (Form R4)</h4>
                <?php if (!empty($rep['run'])): ?>
                    <span class="text-muted">Period: <strong><?= date('F Y', strtotime($rep['run']->period_end)) ?></strong></span>
                <?php endif; ?>
            </div>
            <table class="rep-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>EPF/ETF Member No</th>
                        <th>Employee Name</th>
                        <th>Department</th>
                        <th style="text-align: right;">Basic Salary</th>
                        <th style="text-align: right;">ETF Earnings</th>
                        <th style="text-align: right; font-weight:800;">Employer ETF 3%</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rep['slips'])): $idx = 1; foreach ($rep['slips'] as $s): ?>
                        <tr>
                            <td><?= $idx++ ?></td>
                            <td><strong><?= htmlspecialchars($s['epf_member_no'] ?: 'N/A') ?></strong></td>
                            <td style="font-weight: 700;"><?= htmlspecialchars($s['employee_name']) ?></td>
                            <td><?= htmlspecialchars($s['department'] ?? 'General') ?></td>
                            <td style="text-align: right;">LKR <?= number_format($s['basic_salary'], 2) ?></td>
                            <td style="text-align: right;">LKR <?= number_format($s['etf_applicable_earnings'], 2) ?></td>
                            <td style="text-align: right; font-weight: 800; color: var(--c-purple);">LKR <?= number_format($s['employer_3_pct'], 2) ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
                <?php if (!empty($rep['summary'])): ?>
                    <tfoot>
                        <tr>
                            <td colspan="4" style="text-align: right;">Grand Total:</td>
                            <td style="text-align: right;">LKR <?= number_format($rep['summary']['total_basic'], 2) ?></td>
                            <td style="text-align: right;">LKR <?= number_format($rep['summary']['total_etf_earnings'], 2) ?></td>
                            <td style="text-align: right; font-weight: 800; font-size: 15px; color: var(--c-purple);">LKR <?= number_format($rep['summary']['total_employer_3'], 2) ?></td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>

        <?php elseif ($data['report_type'] === 'employee_history'): ?>
            <!-- Employee Contribution History -->
            <div style="padding: 20px; border-bottom: 1px solid var(--c-separator);">
                <h4 style="margin: 0; font-weight: 800;">Employee Contribution History</h4>
            </div>
            <table class="rep-table">
                <thead>
                    <tr>
                        <th>Period</th>
                        <th>Member No</th>
                        <th>Employee</th>
                        <th style="text-align: right;">EPF Earnings</th>
                        <th style="text-align: right;">Employee EPF (8%)</th>
                        <th style="text-align: right;">Employer EPF (12%)</th>
                        <th style="text-align: right;">Employer ETF (3%)</th>
                        <th style="text-align: right; font-weight:800;">Total Contributions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['report_data'])): foreach ($data['report_data'] as $h): ?>
                        <?php 
                        $emp8 = floatval($h->epf_employee_amount ?? 0);
                        $empr12 = floatval($h->epf_employer_amount ?? 0);
                        $etf3 = floatval($h->etf_employer_amount ?? 0);
                        $tot = $emp8 + $empr12 + $etf3;
                        ?>
                        <tr>
                            <td style="font-weight: 700;"><?= date('M Y', strtotime($h->period_end)) ?></td>
                            <td><?= htmlspecialchars($h->epf_member_no ?: 'N/A') ?></td>
                            <td><?= htmlspecialchars($h->first_name . ' ' . $h->last_name) ?></td>
                            <td style="text-align: right;">LKR <?= number_format(floatval($h->epf_applicable_earnings ?? $h->basic_salary), 2) ?></td>
                            <td style="text-align: right;">LKR <?= number_format($emp8, 2) ?></td>
                            <td style="text-align: right;">LKR <?= number_format($empr12, 2) ?></td>
                            <td style="text-align: right;">LKR <?= number_format($etf3, 2) ?></td>
                            <td style="text-align: right; font-weight: 800;">LKR <?= number_format($tot, 2) ?></td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="8" style="text-align: center; padding: 40px; color: var(--t-secondary);">No contribution history records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

        <?php elseif ($data['report_type'] === 'employer_summary'): ?>
            <!-- Employer Expense Summary -->
            <div style="padding: 20px; border-bottom: 1px solid var(--c-separator);">
                <h4 style="margin: 0; font-weight: 800;">Employer EPF & ETF Expense Summary (<?= $data['selected_year'] ?>)</h4>
            </div>
            <table class="rep-table">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th style="text-align: right;">Employer EPF Expense (12%)</th>
                        <th style="text-align: right;">Employer ETF Expense (3%)</th>
                        <th style="text-align: right; font-weight:800;">Total Employer Benefit Expense</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totEpf = 0; $totEtf = 0; $totGrand = 0;
                    if (!empty($data['report_data'])): foreach ($data['report_data'] as $sum): 
                        $epf = floatval($sum->total_epf_employer);
                        $etf = floatval($sum->total_etf_employer);
                        $tot = floatval($sum->grand_total_employer);
                        $totEpf += $epf; $totEtf += $etf; $totGrand += $tot;
                    ?>
                        <tr>
                            <td style="font-weight: 700;"><?= htmlspecialchars($sum->month_name) ?></td>
                            <td style="text-align: right;">LKR <?= number_format($epf, 2) ?></td>
                            <td style="text-align: right;">LKR <?= number_format($etf, 2) ?></td>
                            <td style="text-align: right; font-weight: 800; color: var(--c-blue);">LKR <?= number_format($tot, 2) ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td style="text-align: right;">Total for <?= $data['selected_year'] ?>:</td>
                        <td style="text-align: right;">LKR <?= number_format($totEpf, 2) ?></td>
                        <td style="text-align: right;">LKR <?= number_format($totEtf, 2) ?></td>
                        <td style="text-align: right; font-weight: 800; font-size: 15px;">LKR <?= number_format($totGrand, 2) ?></td>
                    </tr>
                </tfoot>
            </table>

        <?php elseif ($data['report_type'] === 'payable_report'): ?>
            <!-- Payables & Settlement Status -->
            <div style="padding: 20px; border-bottom: 1px solid var(--c-separator);">
                <h4 style="margin: 0; font-weight: 800;">EPF & ETF Payables Status Report (<?= $data['selected_year'] ?>)</h4>
            </div>
            <table class="rep-table">
                <thead>
                    <tr>
                        <th>Period</th>
                        <th style="text-align: right;">EPF Liability (20%)</th>
                        <th style="text-align: right;">EPF Paid</th>
                        <th style="text-align: right;">EPF Balance</th>
                        <th style="text-align: center;">EPF Status</th>
                        <th style="text-align: right;">ETF Liability (3%)</th>
                        <th style="text-align: right;">ETF Paid</th>
                        <th style="text-align: right;">ETF Balance</th>
                        <th style="text-align: center;">ETF Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['report_data'])): foreach ($data['report_data'] as $p): ?>
                        <tr>
                            <td style="font-weight: 700;"><?= date('M Y', strtotime($p->period_end)) ?></td>
                            <td style="text-align: right;">LKR <?= number_format(floatval($p->epf_total_liability), 2) ?></td>
                            <td style="text-align: right; color: var(--c-green);">LKR <?= number_format(floatval($p->epf_paid_amount), 2) ?></td>
                            <td style="text-align: right; font-weight:700; color: <?= (floatval($p->epf_balance) > 0) ? 'var(--c-red)' : 'var(--c-green)' ?>;">
                                LKR <?= number_format(floatval($p->epf_balance), 2) ?>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge <?= ($p->epf_payment_status === 'Paid') ? 'bg-success' : 'bg-warning' ?>"><?= $p->epf_payment_status ?></span>
                            </td>
                            <td style="text-align: right;">LKR <?= number_format(floatval($p->etf_total_liability), 2) ?></td>
                            <td style="text-align: right; color: var(--c-green);">LKR <?= number_format(floatval($p->etf_paid_amount), 2) ?></td>
                            <td style="text-align: right; font-weight:700; color: <?= (floatval($p->etf_balance) > 0) ? 'var(--c-red)' : 'var(--c-green)' ?>;">
                                LKR <?= number_format(floatval($p->etf_balance), 2) ?>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge <?= ($p->etf_payment_status === 'Paid') ? 'bg-success' : 'bg-warning' ?>"><?= $p->etf_payment_status ?></span>
                            </td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="9" style="text-align: center; padding: 40px; color: var(--t-secondary);">No payables records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
