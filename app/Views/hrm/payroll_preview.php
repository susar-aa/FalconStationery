
<?php $preview = $data['preview'] ?? []; ?>

<!-- Inter Font & FontAwesome Icons -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
/* ============================================================
   SF PRO + APPLE DESIGN LANGUAGE — PAYROLL PREVIEW
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

/* ---- Summary Grid ---- */
.summary-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 12px;
    margin-bottom: 24px;
}
@media (max-width: 1200px) {
    .summary-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 640px) {
    .summary-grid { grid-template-columns: repeat(2, 1fr); }
}

.summary-card {
    background: var(--c-surface);
    border-radius: var(--r-lg);
    padding: 16px;
    border: 0.5px solid var(--c-separator);
    box-shadow: var(--shadow-sm);
    text-align: center;
    position: relative;
    overflow: hidden;
}
.summary-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; height: 3px;
}
.summary-card.blue::before   { background: var(--c-blue); }
.summary-card.purple::before { background: var(--c-purple); }
.summary-card.red::before    { background: var(--c-red); }
.summary-card.orange::before { background: var(--c-orange); }
.summary-card.green::before  { background: var(--c-green); }

.summary-lbl {
    font-size: 10.5px;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: var(--t-label);
    margin-bottom: 6px;
}
.summary-val {
    font-size: 16px;
    font-weight: 800;
    color: var(--t-primary);
    font-family: var(--f-mono);
}
.summary-val.blue   { color: var(--c-blue); }
.summary-val.red    { color: var(--c-red); }
.summary-val.purple { color: var(--c-purple); }
.summary-val.orange { color: var(--c-orange); }
.summary-val.green  { color: var(--c-green); }

/* ---- Table Panel ---- */
.table-panel {
    background: var(--c-surface);
    border-radius: var(--r-xl);
    border: 0.5px solid var(--c-separator);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    margin-bottom: 24px;
}
.panel-header {
    padding: 18px 24px;
    border-bottom: 0.5px solid var(--c-separator);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.panel-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--t-primary);
    margin: 0;
}

.sf-table { width: 100%; border-collapse: collapse; }
.sf-table thead th {
    padding: 13px 16px; font-size: 11px; font-weight: 700;
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
    padding: 14px 16px; font-size: 13.5px; color: var(--t-primary); vertical-align: middle;
}

.sf-btn {
    padding: 10px 20px; border-radius: var(--r-pill); font-size: 13.5px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 8px; border: 0.5px solid transparent;
    cursor: pointer; transition: transform var(--dur-fast) var(--ease-spring), background var(--dur-fast), box-shadow var(--dur-fast);
    text-decoration: none; font-family: var(--f-system);
}
.sf-btn:active { transform: scale(0.97); }
.sf-btn.primary { background: var(--c-blue); color: #fff; box-shadow: 0 2px 8px rgba(0,122,255,0.25); }
.sf-btn.primary:hover { background: #0066d6; }
.sf-btn.success { background: var(--c-green); color: #fff; box-shadow: 0 2px 8px rgba(52,199,89,0.25); }
.sf-btn.success:hover { background: #2bb14e; }
.sf-btn.neutral { background: var(--c-surface); border-color: var(--c-separator); color: var(--t-primary); box-shadow: var(--shadow-xs); }
.sf-btn.neutral:hover { background: var(--c-surface2); }
</style>

<div class="sf-root">
    <div class="sf-wrap">
        
        <!-- Header -->
        <div class="sf-header">
            <div>
                <div class="sf-eyebrow"><i class="fa-solid fa-calculator"></i> Step 2 of 2: Verification</div>
                <h1 class="sf-title">Payroll Preview &amp; Verification</h1>
                <p class="sf-subtitle">Verify calculated gross wages, EPF/ETF statutory deductions, and net salary payouts before generating draft.</p>
            </div>
            <div>
                <a href="<?= APP_URL ?>/payroll" class="sf-btn neutral">
                    <i class="fa-solid fa-xmark"></i> Cancel
                </a>
            </div>
        </div>

        <!-- KPI Grid -->
        <div class="summary-grid">
            <div class="summary-card blue">
                <div class="summary-lbl">Pay Period</div>
                <div class="summary-val" style="font-size: 13.5px; font-weight: 700;">
                    <?= date('d M', strtotime($preview['period_start'] ?? date('Y-m-01'))) ?> &ndash; <?= date('d M Y', strtotime($preview['period_end'] ?? date('Y-m-t'))) ?>
                </div>
            </div>

            <div class="summary-card purple">
                <div class="summary-lbl">Total Gross</div>
                <div class="summary-val purple">Rs. <?= number_format($preview['total_gross'] ?? 0, 2) ?></div>
            </div>

            <div class="summary-card red">
                <div class="summary-lbl">EPF Employee (8%)</div>
                <div class="summary-val red">Rs. <?= number_format($preview['total_epf_employee'] ?? 0, 2) ?></div>
            </div>

            <div class="summary-card orange">
                <div class="summary-lbl">EPF Employer (12%)</div>
                <div class="summary-val orange">Rs. <?= number_format($preview['total_epf_employer'] ?? 0, 2) ?></div>
            </div>

            <div class="summary-card blue">
                <div class="summary-lbl">ETF Employer (3%)</div>
                <div class="summary-val blue">Rs. <?= number_format($preview['total_etf_employer'] ?? 0, 2) ?></div>
            </div>

            <div class="summary-card green">
                <div class="summary-lbl">Total Net Pay</div>
                <div class="summary-val green">Rs. <?= number_format($preview['total_net'] ?? 0, 2) ?></div>
            </div>
        </div>

        <!-- Breakdown Table -->
        <div class="table-panel">
            <div class="panel-header">
                <h3 class="panel-title"><i class="fa-solid fa-list-check" style="color: var(--c-blue); margin-right: 6px;"></i> Employee Payslips Calculation</h3>
                <span style="font-size: 12px; font-weight: 600; color: var(--t-secondary);">
                    <?= count($preview['slips'] ?? []) ?> Employees Included
                </span>
            </div>

            <div style="overflow-x: auto;">
                <table class="sf-table">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th style="text-align: right;">Base Salary</th>
                            <th style="text-align: right; color: var(--c-green);">+ Allowances</th>
                            <th style="text-align: right; color: var(--c-green);">+ Comms / OT</th>
                            <th style="text-align: right; font-weight: 800;">Gross Salary</th>
                            <th style="text-align: right; color: var(--c-red);">- EPF 8%</th>
                            <th style="text-align: right; color: var(--c-red);">- Loan Ded.</th>
                            <th style="text-align: right; color: var(--c-red);">- Other Ded.</th>
                            <th style="text-align: right; font-weight: 800; color: var(--c-green);">Net Pay</th>
                            <th style="text-align: right; color: var(--c-orange);">EPF 12% (Co)</th>
                            <th style="text-align: right; color: var(--c-purple);">ETF 3% (Co)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach(($preview['slips'] ?? []) as $slip): ?>
                            <tr>
                                <td>
                                    <a href="<?= APP_URL ?>/user/show/<?= $slip['employee_id'] ?>" style="font-weight: 700; text-decoration: none; color: var(--c-blue);">
                                        <?= htmlspecialchars($slip['employee_name']) ?>
                                    </a>
                                    <?php if(!empty($slip['loan_details'])): ?>
                                        <div style="font-size: 11px; color: var(--c-orange); font-weight: 600; margin-top: 2px;">
                                            <i class="fa-solid fa-circle-info"></i> Active Loan Deduction
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right; font-family: var(--f-mono);">Rs. <?= number_format($slip['base_salary'], 2) ?></td>
                                <td style="text-align: right; color: var(--c-green); font-family: var(--f-mono);">Rs. <?= number_format($slip['allowances'], 2) ?></td>
                                <td style="text-align: right; color: var(--c-green); font-family: var(--f-mono);">Rs. <?= number_format($slip['commissions'] + $slip['overtime'], 2) ?></td>
                                <td style="text-align: right; font-weight: 800; font-family: var(--f-mono);">Rs. <?= number_format($slip['gross_salary'], 2) ?></td>
                                <td style="text-align: right; color: var(--c-red); font-family: var(--f-mono);">Rs. <?= number_format($slip['epf_employee'] ?? 0, 2) ?></td>
                                <td style="text-align: right; color: var(--c-red); font-family: var(--f-mono);">Rs. <?= number_format($slip['loan_deduction'], 2) ?></td>
                                <td style="text-align: right; color: var(--c-red); font-family: var(--f-mono);">Rs. <?= number_format($slip['other_deductions'], 2) ?></td>
                                <td style="text-align: right; font-weight: 800; color: var(--c-green); font-family: var(--f-mono);">Rs. <?= number_format($slip['net_salary'], 2) ?></td>
                                <td style="text-align: right; color: var(--c-orange); font-family: var(--f-mono);">Rs. <?= number_format($slip['epf_employer'] ?? 0, 2) ?></td>
                                <td style="text-align: right; color: var(--c-purple); font-family: var(--f-mono);">Rs. <?= number_format($slip['etf_employer'] ?? 0, 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Action Card -->
        <div style="background: var(--c-surface); border-radius: var(--r-xl); padding: 20px 24px; border: 0.5px solid var(--c-separator); box-shadow: var(--shadow-sm); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
            <div style="font-size: 13.5px; color: var(--t-secondary);">
                <i class="fa-solid fa-shield-check" style="color: var(--c-green); margin-right: 6px;"></i>
                Ready to commit. Click confirm to store draft payroll and allow accounting posting.
            </div>
            <form action="<?= APP_URL ?>/payroll/run" method="POST" style="margin: 0;">
                <input type="hidden" name="action" value="run_payroll">
                <input type="hidden" name="period_start" value="<?= $preview['period_start'] ?>">
                <input type="hidden" name="period_end" value="<?= $preview['period_end'] ?>">
                <input type="hidden" name="run_date" value="<?= $data['run_date'] ?>">
                <button type="submit" class="sf-btn success">
                    <i class="fa-solid fa-circle-check"></i> Confirm &amp; Save as Draft Run
                </button>
            </form>
        </div>

    </div>
</div>



