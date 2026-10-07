
<?php 
$run = $data['run']; 
$slips = $data['slips'];

$totalGross = array_sum(array_column($slips, 'gross_salary'));
$totalNet = array_sum(array_column($slips, 'net_salary'));
$totalEpfEmp = array_sum(array_column($slips, 'epf_employee'));
$totalEpfEmpr = array_sum(array_column($slips, 'epf_employer'));
$totalEtfEmpr = array_sum(array_column($slips, 'etf_employer'));
$totalDed = array_sum(array_column($slips, 'loan_deduction')) + array_sum(array_column($slips, 'other_deductions')) + $totalEpfEmp;
$statusLower = strtolower($run->status);
?>

<!-- Inter Font & FontAwesome Icons -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
/* ============================================================
   SF PRO + APPLE DESIGN LANGUAGE — PAYROLL RUN DETAILS
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
    display: flex;
    align-items: center;
    gap: 10px;
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

/* ---- Layout Split ---- */
.show-split {
    display: grid;
    grid-template-columns: 2.2fr 1fr;
    gap: 20px;
}
@media (max-width: 1024px) {
    .show-split { grid-template-columns: 1fr; }
}

/* ---- Table Panel ---- */
.table-panel {
    background: var(--c-surface);
    border-radius: var(--r-xl);
    border: 0.5px solid var(--c-separator);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
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

/* ---- Badges & Cards ---- */
.sf-status-pill {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; border-radius: var(--r-pill);
    font-size: 11.5px; font-weight: 700;
}
.sf-status-pill.draft    { background: var(--c-fill2); color: var(--t-secondary); }
.sf-status-pill.approved { background: var(--c-blue-light); color: var(--c-blue); }
.sf-status-pill.paid     { background: var(--c-green-light); color: var(--c-green); }
.sf-status-pill .dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

.action-card {
    background: var(--c-surface);
    border-radius: var(--r-xl);
    border: 0.5px solid var(--c-separator);
    box-shadow: var(--shadow-sm);
    padding: 24px;
    margin-bottom: 20px;
}
.action-card-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--t-primary);
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.sf-field { margin-bottom: 16px; }
.sf-field label { display: block; margin-bottom: 6px; font-size: 12.5px; font-weight: 600; color: var(--t-secondary); }
.sf-input, .sf-select-input {
    width: 100%; padding: 9px 13px; border: 0.5px solid var(--c-separator); border-radius: var(--r-sm);
    background: var(--c-surface2); color: var(--t-primary); box-sizing: border-box;
    font-family: var(--f-system); font-size: 13.5px; outline: none; transition: border-color var(--dur-fast), box-shadow var(--dur-fast);
}
.sf-input:focus, .sf-select-input:focus { border-color: var(--c-blue); box-shadow: 0 0 0 3px rgba(0,122,255,0.12); }
.sf-select-input option { background: var(--c-surface); color: var(--t-primary); }

.sf-btn {
    padding: 10px 18px; border-radius: var(--r-pill); font-size: 13.5px; font-weight: 700;
    display: inline-flex; align-items: center; justify-content: center; gap: 8px; border: 0.5px solid transparent;
    cursor: pointer; transition: transform var(--dur-fast) var(--ease-spring), background var(--dur-fast), box-shadow var(--dur-fast);
    text-decoration: none; font-family: var(--f-system); width: 100%;
}
.sf-btn:active { transform: scale(0.97); }
.sf-btn.primary { background: var(--c-blue); color: #fff; box-shadow: 0 2px 8px rgba(0,122,255,0.25); }
.sf-btn.primary:hover { background: #0066d6; }
.sf-btn.success { background: var(--c-green); color: #fff; box-shadow: 0 2px 8px rgba(52,199,89,0.25); }
.sf-btn.success:hover { background: #2bb14e; }
.sf-btn.neutral { background: var(--c-surface); border-color: var(--c-separator); color: var(--t-primary); box-shadow: var(--shadow-xs); width: auto; }
.sf-btn.neutral:hover { background: var(--c-surface2); }
.sf-btn.sm { padding: 5px 12px; font-size: 12px; width: auto; }

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
</style>

<div class="sf-root">
    <div class="sf-wrap">
        
        <!-- Header -->
        <div class="sf-header">
            <div>
                <div class="sf-eyebrow"><i class="fa-solid fa-file-invoice-dollar"></i> Payroll Run Management</div>
                <h1 class="sf-title">
                    Payroll Run: PR-<?= $run->id ?>
                    <span class="sf-status-pill <?= $statusLower ?>">
                        <span class="dot"></span> <?= htmlspecialchars($run->status) ?>
                    </span>
                </h1>
                <p class="sf-subtitle">Created on <?= date('d M Y', strtotime($run->run_date)) ?> for period <?= date('d M', strtotime($run->period_start)) ?> to <?= date('d M Y', strtotime($run->period_end)) ?>.</p>
            </div>
            <div>
                <a href="<?= APP_URL ?>/payroll" class="sf-btn neutral">
                    <i class="fa-solid fa-arrow-left"></i> Back to Payroll Directory
                </a>
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

        <!-- KPI Grid -->
        <div class="summary-grid">
            <div class="summary-card blue">
                <div class="summary-lbl">Pay Period</div>
                <div class="summary-val" style="font-size: 13.5px; font-weight: 700;">
                    <?= date('d M', strtotime($run->period_start)) ?> &ndash; <?= date('d M Y', strtotime($run->period_end)) ?>
                </div>
            </div>

            <div class="summary-card purple">
                <div class="summary-lbl">Total Gross Salary</div>
                <div class="summary-val purple">Rs. <?= number_format($totalGross, 2) ?></div>
            </div>

            <div class="summary-card red">
                <div class="summary-lbl">EPF Employee (8%)</div>
                <div class="summary-val red">Rs. <?= number_format($totalEpfEmp, 2) ?></div>
            </div>

            <div class="summary-card orange">
                <div class="summary-lbl">EPF Employer (12%)</div>
                <div class="summary-val orange">Rs. <?= number_format($totalEpfEmpr, 2) ?></div>
            </div>

            <div class="summary-card blue">
                <div class="summary-lbl">ETF Employer (3%)</div>
                <div class="summary-val blue">Rs. <?= number_format($totalEtfEmpr, 2) ?></div>
            </div>

            <div class="summary-card green">
                <div class="summary-lbl">Total Net Payable</div>
                <div class="summary-val green">Rs. <?= number_format($totalNet, 2) ?></div>
            </div>
        </div>

        <!-- Main Content Split -->
        <div class="show-split">
            
            <!-- Left: Payslips Table -->
            <div class="table-panel">
                <div class="panel-header">
                    <h3 class="panel-title"><i class="fa-solid fa-users-viewfinder" style="color: var(--c-blue); margin-right: 6px;"></i> Employee Payslips Breakdown</h3>
                    <span style="font-size: 12px; font-weight: 600; color: var(--t-secondary);"><?= count($slips) ?> Employees Included</span>
                </div>

                <div style="overflow-x: auto;">
                    <table class="sf-table">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th style="text-align: right;">Gross Salary</th>
                                <th style="text-align: right; color: var(--c-red);">- EPF 8%</th>
                                <th style="text-align: right; color: var(--c-red);">- Deductions</th>
                                <th style="text-align: right; font-weight: 800; color: var(--c-green);">Net Pay</th>
                                <th style="text-align: right; color: var(--c-orange);">EPF 12%</th>
                                <th style="text-align: right; color: var(--c-purple);">ETF 3%</th>
                                <th>Status</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($slips as $slip): 
                                $slipStatusLower = strtolower($slip->status);
                            ?>
                                <tr>
                                    <td>
                                        <a href="<?= APP_URL ?>/user/show/<?= $slip->employee_id ?>" style="font-weight: 700; text-decoration: none; color: var(--c-blue);">
                                            <?= htmlspecialchars($slip->first_name . ' ' . $slip->last_name) ?>
                                        </a>
                                    </td>
                                    <td style="text-align: right; font-family: var(--f-mono);">Rs. <?= number_format($slip->gross_salary, 2) ?></td>
                                    <td style="text-align: right; color: var(--c-red); font-family: var(--f-mono);">Rs. <?= number_format($slip->epf_employee ?? 0, 2) ?></td>
                                    <td style="text-align: right; color: var(--c-red); font-family: var(--f-mono);">Rs. <?= number_format($slip->loan_deduction + $slip->other_deductions, 2) ?></td>
                                    <td style="text-align: right; font-weight: 800; color: var(--c-green); font-family: var(--f-mono);">Rs. <?= number_format($slip->net_salary, 2) ?></td>
                                    <td style="text-align: right; color: var(--c-orange); font-family: var(--f-mono);">Rs. <?= number_format($slip->epf_employer ?? 0, 2) ?></td>
                                    <td style="text-align: right; color: var(--c-purple); font-family: var(--f-mono);">Rs. <?= number_format($slip->etf_employer ?? 0, 2) ?></td>
                                    <td>
                                        <span class="sf-status-pill <?= $slipStatusLower ?>">
                                            <span class="dot"></span> <?= htmlspecialchars($slip->status) ?>
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        <a href="<?= APP_URL ?>/payroll/payslip/<?= $slip->id ?>" target="_blank" class="sf-btn neutral sm">
                                            <i class="fa-solid fa-print"></i> Print
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right: Actions & Accounting Workflow -->
            <div>
                <div class="action-card">
                    <div class="action-card-title">
                        <span><i class="fa-solid fa-timeline" style="color: var(--c-blue); margin-right: 6px;"></i> Workflow Actions</span>
                        <span class="sf-status-pill <?= $statusLower ?>"><span class="dot"></span> <?= htmlspecialchars($run->status) ?></span>
                    </div>

                    <?php if($run->status == 'Draft'): ?>
                        <form action="<?= APP_URL ?>/payroll/approve/<?= $run->id ?>" method="POST">
                            <div class="sf-field">
                                <label>Wage Expense Account *</label>
                                <select name="wage_expense_account_id" class="sf-select-input" required>
                                    <?php foreach($data['expenses'] as $exp): ?>
                                        <option value="<?= $exp->id ?>" <?= (stripos($exp->account_name, 'wage') !== false || stripos($exp->account_name, 'salary') !== false) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($exp->account_name) ?> (<?= htmlspecialchars($exp->account_code) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="sf-field">
                                <label>Salary Payable Account (Liability) *</label>
                                <select name="salary_payable_account_id" class="sf-select-input" required>
                                    <?php foreach($data['liabilities'] as $lia): ?>
                                        <option value="<?= $lia->id ?>" <?= (stripos($lia->account_name, 'payable') !== false || stripos($lia->account_name, 'salary') !== false) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($lia->account_name) ?> (<?= htmlspecialchars($lia->account_code) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <span style="font-size: 11.5px; color: var(--t-label); display: block; margin-top: 4px;">Liability account to hold net pay until disbursed.</span>
                            </div>

                            <button type="submit" class="sf-btn primary" onclick="return confirm('Approve payroll and post wage expense journal entries?')">
                                <i class="fa-solid fa-file-circle-check"></i> Approve &amp; Post Journal Entries
                            </button>
                        </form>
                    <?php endif; ?>

                    <?php if($run->status == 'Approved'): ?>
                        <form action="<?= APP_URL ?>/payroll/pay/<?= $run->id ?>" method="POST">
                            <div class="sf-field">
                                <label>Payment Date *</label>
                                <input type="date" name="payment_date" class="sf-input" required value="<?= date('Y-m-d') ?>">
                            </div>

                            <div class="sf-field">
                                <label>Disburse From Bank Account *</label>
                                <select name="bank_account_id" class="sf-select-input" required>
                                    <?php foreach($data['banks'] as $b): ?>
                                        <option value="<?= $b->id ?>"><?= htmlspecialchars($b->account_name) ?> (<?= htmlspecialchars($b->account_code) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="sf-field">
                                <label>Salary Payable Account *</label>
                                <select name="salary_payable_account_id" class="sf-select-input" required>
                                    <?php foreach($data['liabilities'] as $lia): ?>
                                        <option value="<?= $lia->id ?>" <?= (stripos($lia->account_name, 'payable') !== false || stripos($lia->account_name, 'salary') !== false) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($lia->account_name) ?> (<?= htmlspecialchars($lia->account_code) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <span style="font-size: 11.5px; color: var(--t-label); display: block; margin-top: 4px;">Must match the liability account used during approval.</span>
                            </div>

                            <button type="submit" class="sf-btn success" onclick="return confirm('Mark payroll as Paid, disburse bank funds, and apply loan deductions?')">
                                <i class="fa-solid fa-money-bill-check"></i> Disburse &amp; Mark as Paid
                            </button>
                        </form>
                    <?php endif; ?>

                    <?php if($run->status == 'Paid'): ?>
                        <div style="background: var(--c-green-light); border: 0.5px solid rgba(52,199,89,0.3); color: var(--c-green); padding: 14px; border-radius: var(--r-md); font-size: 13.5px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-circle-check" style="font-size: 18px;"></i>
                            This payroll run is fully approved, posted, and paid.
                        </div>
                    <?php endif; ?>
                </div>

                <?php if(!empty($run->journal_entry_id)): ?>
                    <div class="action-card">
                        <div class="action-card-title">
                            <span><i class="fa-solid fa-book-journal-whills" style="color: var(--c-purple); margin-right: 6px;"></i> Ledger Posting</span>
                        </div>
                        <p style="font-size: 13px; color: var(--t-secondary); margin-bottom: 14px;">
                            Journal Entry Ref ID: <strong style="color: var(--t-primary); font-family: var(--f-mono);">JE-<?= $run->journal_entry_id ?></strong>
                        </p>
                        <a href="<?= APP_URL ?>/accounting/journal/<?= $run->journal_entry_id ?>" class="sf-btn neutral" style="width: 100%; box-sizing: border-box;">
                            <i class="fa-solid fa-up-right-from-square"></i> View Journal Entry
                        </a>
                    </div>
                <?php endif; ?>
            </div>

        </div>

    </div>
</div>


