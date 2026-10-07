<!-- Inter Font & FontAwesome Icons -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
/* ============================================================
   SF PRO + APPLE DESIGN LANGUAGE — EPF & ETF REPORTS
   ============================================================ */

:root {
    --c-bg:           #f2f2f7;
    --c-surface:      #ffffff;
    --c-surface2:     #f9f9fb;
    --c-fill:         rgba(120,120,128,0.08);
    --c-fill2:        rgba(120,120,128,0.14);
    --c-separator:    rgba(60,60,67,0.12);
    --c-separator2:   rgba(60,60,67,0.06);

    --c-blue:         #007aff;
    --c-blue-light:   #e5f2ff;
    --c-green:        #34c759;
    --c-green-light:  #e6f9ec;
    --c-orange:       #ff9500;
    --c-purple:       #af52de;
    --c-purple-light: #f5e8ff;
    --c-red:          #ff3b30;

    --f-system: -apple-system, 'SF Pro Display', 'SF Pro Text', 'Inter', 'Helvetica Neue', sans-serif;
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
    --r-xl: 24px;
    --r-pill: 999px;

    --ease-ios: cubic-bezier(0.25, 0.1, 0.25, 1);
    --dur-fast: 0.18s;
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
        --t-label:     #52525b;
    }
}

.report-root {
    font-family: var(--f-system);
    font-size: 14.5px;
    color: var(--t-primary);
    background: var(--c-bg);
    min-height: 100vh;
    padding: 20px 24px 80px;
    -webkit-font-smoothing: antialiased;
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
    margin: 0 0 4px 0;
    letter-spacing: -0.03em;
}

/* Tabs Segmented Bar */
.report-tabs {
    display: flex;
    gap: 8px;
    background: var(--c-surface);
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-pill);
    padding: 6px;
    margin-bottom: 24px;
    flex-wrap: wrap;
    box-shadow: var(--shadow-xs);
}
.report-tab {
    padding: 8px 18px;
    border-radius: var(--r-pill);
    font-size: 13px;
    font-weight: 700;
    color: var(--t-secondary);
    text-decoration: none !important;
    background: transparent;
    transition: all var(--dur-fast) var(--ease-ios);
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.report-tab:hover { color: var(--t-primary); }
.report-tab.active {
    background: var(--c-blue);
    color: #ffffff !important;
    box-shadow: 0 2px 8px rgba(0,122,255,0.3);
}

/* Filter Shelf */
.report-filter {
    background: var(--c-surface);
    border-radius: var(--r-lg);
    border: 0.5px solid var(--c-separator);
    padding: 14px 20px;
    margin-bottom: 24px;
    display: flex;
    gap: 16px;
    align-items: center;
    flex-wrap: wrap;
    box-shadow: var(--shadow-xs);
}
.filter-chip {
    display: inline-flex; align-items: center; gap: 8px;
    background: var(--c-surface2);
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-pill);
    padding: 6px 14px;
    font-size: 13px;
    font-weight: 600;
    color: var(--t-secondary);
}
.filter-chip select {
    border: none;
    background: transparent;
    font-size: 13px;
    font-weight: 700;
    color: var(--t-primary);
    outline: none;
    cursor: pointer;
}

/* Panel & Table */
.report-panel {
    background: var(--c-surface);
    border-radius: var(--r-xl);
    border: 0.5px solid var(--c-separator);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}
.rep-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13.5px;
}
.rep-table th {
    background: var(--c-surface2);
    padding: 12px 18px;
    font-size: 11px;
    font-weight: 700;
    color: var(--t-label);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 0.5px solid var(--c-separator);
}
.rep-table td {
    padding: 14px 18px;
    border-bottom: 0.5px solid var(--c-separator2);
    vertical-align: middle;
    color: var(--t-primary);
}
.rep-table tr:hover td { background: var(--c-fill); }
.rep-table tfoot td {
    background: var(--c-surface2);
    font-weight: 800;
    border-top: 2px solid var(--c-separator);
}

/* Apple Buttons */
.btn-apple {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 8px 18px;
    font-size: 13px;
    font-weight: 700;
    border-radius: var(--r-pill);
    border: none;
    cursor: pointer;
    text-decoration: none !important;
    transition: transform var(--dur-fast) var(--ease-ios), opacity var(--dur-fast);
}
.btn-apple:hover { opacity: 0.92; transform: translateY(-1px); }
.btn-apple-primary { background: var(--c-blue); color: #ffffff !important; }
.btn-apple-secondary { background: var(--c-fill2); color: var(--t-primary) !important; }

/* Print Utilities */
@media print {
    .report-tabs, .report-filter, .no-print { display: none !important; }
    .report-panel { border: none !important; box-shadow: none !important; }
    body { background: #fff !important; padding: 0 !important; }
}
</style>

<div class="report-root">
    <div class="report-header">
        <div>
            <h1><i class="fa-solid fa-file-invoice-dollar" style="color: var(--c-blue);"></i> EPF & ETF Compliance Reports</h1>
            <p style="margin:4px 0 0 0; color: var(--t-secondary);">Statutory Monthly EPF (Form C), ETF Returns (Form R4), and Contribution Summaries</p>
        </div>
        <div class="no-print" style="display:flex; gap:10px;">
            <button onclick="window.print()" class="btn-apple btn-apple-secondary">
                <i class="fa-solid fa-print"></i> Print Report
            </button>
            <a href="<?= APP_URL ?>/epfetf" class="btn-apple btn-apple-primary">
                <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Report Type Segmented Tabs -->
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
            <i class="fa-solid fa-receipt"></i> Payables & Status
        </a>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="<?= APP_URL ?>/epfetf/reports" class="report-filter no-print">
        <input type="hidden" name="type" value="<?= htmlspecialchars($data['report_type']) ?>">

        <?php if (in_array($data['report_type'], ['monthly_epf', 'monthly_etf'])): ?>
            <div class="filter-chip">
                <i class="fa-solid fa-receipt" style="color: var(--c-blue);"></i>
                <span>Payroll Run:</span>
                <select name="run_id" onchange="this.form.submit()">
                    <?php if(!empty($data['payroll_runs'])): foreach($data['payroll_runs'] as $r): ?>
                        <option value="<?= $r->id ?>" <?= ($data['selected_run_id'] == $r->id) ? 'selected' : '' ?>>
                            PR-<?= $r->id ?> (<?= date('M Y', strtotime($r->period_end)) ?>) - <?= date('M d', strtotime($r->period_start)) ?> to <?= date('M d, Y', strtotime($r->period_end)) ?>
                        </option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
        <?php endif; ?>

        <?php if ($data['report_type'] === 'employee_history'): ?>
            <div class="filter-chip">
                <i class="fa-solid fa-user" style="color: var(--c-purple);"></i>
                <span>Employee:</span>
                <select name="employee_id" onchange="this.form.submit()">
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
            <div class="filter-chip">
                <i class="fa-solid fa-calendar-days" style="color: var(--c-orange);"></i>
                <span>Year:</span>
                <select name="year" onchange="this.form.submit()">
                    <?php for($y = date('Y'); $y >= 2024; $y--): ?>
                        <option value="<?= $y ?>" <?= ($data['selected_year'] == $y) ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        <?php endif; ?>
    </form>

    <!-- REPORT CONTENT PANEL -->
    <div class="report-panel">
        <?php if ($data['report_type'] === 'monthly_epf'): ?>
            <!-- Monthly EPF 20% Report -->
            <?php $rep = $data['report_data']; ?>
            <div style="padding: 20px 24px; border-bottom: 0.5px solid var(--c-separator); background: var(--c-surface2);">
                <h4 style="margin: 0; font-weight: 800; font-size:16px;">Monthly EPF Return (Form C)</h4>
                <?php if (!empty($rep['run'])): ?>
                    <div style="font-size:12.5px; color: var(--t-secondary); margin-top:2px;">
                        Period: <strong><?= date('F Y', strtotime($rep['run']->period_end)) ?></strong> (<?= date('M d, Y', strtotime($rep['run']->period_start)) ?> - <?= date('M d, Y', strtotime($rep['run']->period_end)) ?>)
                    </div>
                <?php endif; ?>
            </div>
            <div style="overflow-x: auto;">
                <table class="rep-table">
                    <thead>
                        <tr>
                            <th style="padding-left:24px;">#</th>
                            <th>Member No</th>
                            <th>Employee Name</th>
                            <th>Department</th>
                            <th style="text-align: right;">Basic Salary</th>
                            <th style="text-align: right;">EPF Earnings</th>
                            <th style="text-align: right;">Employee 8%</th>
                            <th style="text-align: right;">Employer 12%</th>
                            <th style="text-align: right; font-weight:800; padding-right:24px;">Total 20%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($rep['slips'])): $idx = 1; foreach ($rep['slips'] as $s): ?>
                            <tr>
                                <td style="padding-left:24px; color:var(--t-label);"><?= $idx++ ?></td>
                                <td><strong><?= htmlspecialchars($s['epf_member_no'] ?: 'N/A') ?></strong></td>
                                <td style="font-weight: 700;"><?= htmlspecialchars($s['employee_name']) ?></td>
                                <td><?= htmlspecialchars($s['department'] ?? 'General') ?></td>
                                <td style="text-align: right;">LKR <?= number_format($s['basic_salary'], 2) ?></td>
                                <td style="text-align: right;">LKR <?= number_format($s['epf_applicable_earnings'], 2) ?></td>
                                <td style="text-align: right; color: var(--c-blue); font-weight:600;">LKR <?= number_format($s['employee_8_pct'], 2) ?></td>
                                <td style="text-align: right; color: var(--c-purple); font-weight:600;">LKR <?= number_format($s['employer_12_pct'], 2) ?></td>
                                <td style="text-align: right; font-weight: 800; padding-right:24px;">LKR <?= number_format($s['total_20_pct'], 2) ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                    <?php if (!empty($rep['summary'])): ?>
                        <tfoot>
                            <tr>
                                <td colspan="4" style="text-align: right; padding-left:24px;">Grand Total:</td>
                                <td style="text-align: right;">LKR <?= number_format($rep['summary']['total_basic'], 2) ?></td>
                                <td style="text-align: right;">LKR <?= number_format($rep['summary']['total_epf_earnings'], 2) ?></td>
                                <td style="text-align: right; color: var(--c-blue);">LKR <?= number_format($rep['summary']['total_employee_8'], 2) ?></td>
                                <td style="text-align: right; color: var(--c-purple);">LKR <?= number_format($rep['summary']['total_employer_12'], 2) ?></td>
                                <td style="text-align: right; font-weight: 800; font-size: 15px; color: var(--c-blue); padding-right:24px;">LKR <?= number_format($rep['summary']['total_epf_20'], 2) ?></td>
                            </tr>
                        </tfoot>
                    <?php endif; ?>
                </table>
            </div>

        <?php elseif ($data['report_type'] === 'monthly_etf'): ?>
            <!-- Monthly ETF 3% Report -->
            <?php $rep = $data['report_data']; ?>
            <div style="padding: 20px 24px; border-bottom: 0.5px solid var(--c-separator); background: var(--c-surface2);">
                <h4 style="margin: 0; font-weight: 800; font-size:16px;">Monthly ETF Return (Form R4)</h4>
                <?php if (!empty($rep['run'])): ?>
                    <div style="font-size:12.5px; color: var(--t-secondary); margin-top:2px;">
                        Period: <strong><?= date('F Y', strtotime($rep['run']->period_end)) ?></strong>
                    </div>
                <?php endif; ?>
            </div>
            <div style="overflow-x: auto;">
                <table class="rep-table">
                    <thead>
                        <tr>
                            <th style="padding-left:24px;">#</th>
                            <th>EPF/ETF Member No</th>
                            <th>Employee Name</th>
                            <th>Department</th>
                            <th style="text-align: right;">Basic Salary</th>
                            <th style="text-align: right;">ETF Earnings</th>
                            <th style="text-align: right; font-weight:800; padding-right:24px;">Employer ETF 3%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($rep['slips'])): $idx = 1; foreach ($rep['slips'] as $s): ?>
                            <tr>
                                <td style="padding-left:24px; color:var(--t-label);"><?= $idx++ ?></td>
                                <td><strong><?= htmlspecialchars($s['epf_member_no'] ?: 'N/A') ?></strong></td>
                                <td style="font-weight: 700;"><?= htmlspecialchars($s['employee_name']) ?></td>
                                <td><?= htmlspecialchars($s['department'] ?? 'General') ?></td>
                                <td style="text-align: right;">LKR <?= number_format($s['basic_salary'], 2) ?></td>
                                <td style="text-align: right;">LKR <?= number_format($s['etf_applicable_earnings'], 2) ?></td>
                                <td style="text-align: right; font-weight: 800; color: var(--c-purple); padding-right:24px;">LKR <?= number_format($s['employer_3_pct'], 2) ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                    <?php if (!empty($rep['summary'])): ?>
                        <tfoot>
                            <tr>
                                <td colspan="4" style="text-align: right; padding-left:24px;">Grand Total:</td>
                                <td style="text-align: right;">LKR <?= number_format($rep['summary']['total_basic'], 2) ?></td>
                                <td style="text-align: right;">LKR <?= number_format($rep['summary']['total_etf_earnings'], 2) ?></td>
                                <td style="text-align: right; font-weight: 800; font-size: 15px; color: var(--c-purple); padding-right:24px;">LKR <?= number_format($rep['summary']['total_employer_3'], 2) ?></td>
                            </tr>
                        </tfoot>
                    <?php endif; ?>
                </table>
            </div>

        <?php elseif ($data['report_type'] === 'employee_history'): ?>
            <!-- Employee Contribution History -->
            <div style="padding: 20px 24px; border-bottom: 0.5px solid var(--c-separator); background: var(--c-surface2);">
                <h4 style="margin: 0; font-weight: 800; font-size:16px;">Employee Contribution History</h4>
            </div>
            <div style="overflow-x: auto;">
                <table class="rep-table">
                    <thead>
                        <tr>
                            <th style="padding-left:24px;">Period</th>
                            <th>Member No</th>
                            <th>Employee</th>
                            <th style="text-align: right;">EPF Earnings</th>
                            <th style="text-align: right;">Employee EPF (8%)</th>
                            <th style="text-align: right;">Employer EPF (12%)</th>
                            <th style="text-align: right;">Employer ETF (3%)</th>
                            <th style="text-align: right; font-weight:800; padding-right:24px;">Total Contributions</th>
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
                                <td style="padding-left:24px; font-weight: 700;"><?= date('M Y', strtotime($h->period_end)) ?></td>
                                <td><?= htmlspecialchars($h->epf_member_no ?: 'N/A') ?></td>
                                <td><?= htmlspecialchars($h->first_name . ' ' . $h->last_name) ?></td>
                                <td style="text-align: right;">LKR <?= number_format(floatval($h->epf_applicable_earnings ?? $h->basic_salary), 2) ?></td>
                                <td style="text-align: right; color: var(--c-blue);">LKR <?= number_format($emp8, 2) ?></td>
                                <td style="text-align: right; color: var(--c-purple);">LKR <?= number_format($empr12, 2) ?></td>
                                <td style="text-align: right; color: var(--c-orange);">LKR <?= number_format($etf3, 2) ?></td>
                                <td style="text-align: right; font-weight: 800; padding-right:24px;">LKR <?= number_format($tot, 2) ?></td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="8" style="text-align: center; padding: 40px; color: var(--t-secondary);">No contribution history records found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($data['report_type'] === 'employer_summary'): ?>
            <!-- Employer Expense Summary -->
            <div style="padding: 20px 24px; border-bottom: 0.5px solid var(--c-separator); background: var(--c-surface2);">
                <h4 style="margin: 0; font-weight: 800; font-size:16px;">Employer EPF & ETF Expense Summary (<?= $data['selected_year'] ?>)</h4>
            </div>
            <div style="overflow-x: auto;">
                <table class="rep-table">
                    <thead>
                        <tr>
                            <th style="padding-left:24px;">Month</th>
                            <th style="text-align: right;">Employer EPF Expense (12%)</th>
                            <th style="text-align: right;">Employer ETF Expense (3%)</th>
                            <th style="text-align: right; font-weight:800; padding-right:24px;">Total Employer Benefit Expense</th>
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
                                <td style="padding-left:24px; font-weight: 700;"><?= htmlspecialchars($sum->month_name) ?></td>
                                <td style="text-align: right; color: var(--c-purple);">LKR <?= number_format($epf, 2) ?></td>
                                <td style="text-align: right; color: var(--c-orange);">LKR <?= number_format($etf, 2) ?></td>
                                <td style="text-align: right; font-weight: 800; color: var(--c-blue); padding-right:24px;">LKR <?= number_format($tot, 2) ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td style="text-align: right; padding-left:24px;">Total for <?= $data['selected_year'] ?>:</td>
                            <td style="text-align: right; color: var(--c-purple);">LKR <?= number_format($totEpf, 2) ?></td>
                            <td style="text-align: right; color: var(--c-orange);">LKR <?= number_format($totEtf, 2) ?></td>
                            <td style="text-align: right; font-weight: 800; font-size: 15px; color: var(--c-blue); padding-right:24px;">LKR <?= number_format($totGrand, 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        <?php elseif ($data['report_type'] === 'payable_report'): ?>
            <!-- Payables & Settlement Status -->
            <div style="padding: 20px 24px; border-bottom: 0.5px solid var(--c-separator); background: var(--c-surface2);">
                <h4 style="margin: 0; font-weight: 800; font-size:16px;">EPF & ETF Payables Status Report (<?= $data['selected_year'] ?>)</h4>
            </div>
            <div style="overflow-x: auto;">
                <table class="rep-table">
                    <thead>
                        <tr>
                            <th style="padding-left:24px;">Period</th>
                            <th style="text-align: right;">EPF Liability (20%)</th>
                            <th style="text-align: right;">EPF Paid</th>
                            <th style="text-align: right;">EPF Balance</th>
                            <th style="text-align: center;">EPF Status</th>
                            <th style="text-align: right;">ETF Liability (3%)</th>
                            <th style="text-align: right;">ETF Paid</th>
                            <th style="text-align: right;">ETF Balance</th>
                            <th style="text-align: center; padding-right:24px;">ETF Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['report_data'])): foreach ($data['report_data'] as $p): ?>
                            <tr>
                                <td style="padding-left:24px; font-weight: 700;"><?= date('M Y', strtotime($p->period_end)) ?></td>
                                <td style="text-align: right;">LKR <?= number_format(floatval($p->epf_total_liability), 2) ?></td>
                                <td style="text-align: right; color: var(--c-green); font-weight:600;">LKR <?= number_format(floatval($p->epf_paid_amount), 2) ?></td>
                                <td style="text-align: right; font-weight:700; color: <?= (floatval($p->epf_balance) > 0) ? 'var(--c-red)' : 'var(--c-green)' ?>;">
                                    LKR <?= number_format(floatval($p->epf_balance), 2) ?>
                                </td>
                                <td style="text-align: center;">
                                    <span class="badge <?= ($p->epf_payment_status === 'Paid') ? 'bg-success' : 'bg-warning' ?>"><?= $p->epf_payment_status ?></span>
                                </td>
                                <td style="text-align: right;">LKR <?= number_format(floatval($p->etf_total_liability), 2) ?></td>
                                <td style="text-align: right; color: var(--c-green); font-weight:600;">LKR <?= number_format(floatval($p->etf_paid_amount), 2) ?></td>
                                <td style="text-align: right; font-weight:700; color: <?= (floatval($p->etf_balance) > 0) ? 'var(--c-red)' : 'var(--c-green)' ?>;">
                                    LKR <?= number_format(floatval($p->etf_balance), 2) ?>
                                </td>
                                <td style="text-align: center; padding-right:24px;">
                                    <span class="badge <?= ($p->etf_payment_status === 'Paid') ? 'bg-success' : 'bg-warning' ?>"><?= $p->etf_payment_status ?></span>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="9" style="text-align: center; padding: 40px; color: var(--t-secondary);">No payables records found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
