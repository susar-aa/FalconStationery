<?php
$loan = $data['loan'] ?? null;
$repayments = $data['repayments'] ?? [];
$bank_accounts = $data['bank_accounts'] ?? [];
$cash_accounts = $data['cash_accounts'] ?? [];
$flashSuccess = $_GET['success'] ?? null;
$flashError = $_GET['error'] ?? null;

if (!$loan) {
    echo "<div style='padding:40px; text-align:center;'>Loan not found.</div>";
    return;
}
?>

<!-- Inter Font & FontAwesome Icons -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
:root {
    --c-bg:           #f2f2f7;
    --c-surface:      #ffffff;
    --c-surface2:     #f9f9fb;
    --c-separator:    rgba(60,60,67,0.12);
    --c-blue:         #007aff;
    --c-green:        #34c759;
    --c-orange:       #ff9500;
    --c-red:          #ff3b30;
    --f-system: -apple-system, 'SF Pro Display', 'SF Pro Text', 'Inter', 'Helvetica Neue', sans-serif;
    --f-mono:   ui-monospace, 'SF Mono', 'Menlo', 'Monaco', monospace;
    --t-primary:   #1c1c1e;
    --t-secondary: #636366;
    --t-label:     #8e8e93;
    --shadow-sm:  0 2px 8px rgba(0,0,0,0.06), 0 1px 3px rgba(0,0,0,0.04);
    --r-md: 14px;
    --r-lg: 20px;
    --r-pill: 999px;
    --dur-fast:    0.18s;
}

@media (prefers-color-scheme: dark) {
    :root {
        --c-bg:           #121212;
        --c-surface:      #1e1e2e;
        --c-surface2:     #161622;
        --c-separator:    rgba(255,255,255,0.15);
        --t-primary:      #f5f5f7;
        --t-secondary:    #a1a1aa;
    }
}

.loan-root { font-family: var(--f-system); font-size: 15px; color: var(--t-primary); background: var(--c-bg); -webkit-font-smoothing: antialiased; }
.loan-wrap { max-width: 1420px; margin: 0 auto; padding: 20px 24px 100px; }

.loan-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px; flex-wrap: wrap; gap: 16px; }
.loan-title { font-size: 32px; font-weight: 700; letter-spacing: -0.03em; color: var(--t-primary); margin-bottom: 4px; }
.btn-back { display: inline-flex; align-items: center; gap: 6px; color: var(--c-blue); text-decoration: none; font-weight: 600; margin-bottom: 16px; }

.btn-apple {
    display: inline-flex; align-items: center; gap: 8px;
    background: var(--c-blue); color: #fff; border: none; border-radius: var(--r-pill);
    padding: 10px 20px; font-size: 14px; font-weight: 600; transition: all var(--dur-fast); cursor: pointer; text-decoration: none;
}
.btn-apple.success { background: var(--c-green); }
.btn-apple.secondary { background: #e5e5ea; color: var(--t-primary); }
.btn-apple.danger { background: var(--c-red); color: #fff; }
@media (prefers-color-scheme: dark) { .btn-apple.secondary { background: #2c2c2e; color: #fff; } }
.btn-apple:hover { filter: brightness(0.9); transform: translateY(-1px); }

.card-apple {
    background: var(--c-surface); border-radius: var(--r-lg); box-shadow: var(--shadow-sm); border: 0.5px solid var(--c-separator); overflow: hidden; padding: 24px; margin-bottom: 24px;
}
.card-apple-title { font-size: 18px; font-weight: 700; margin-bottom: 18px; color: var(--t-primary); display: flex; justify-content: space-between; align-items: center; }

.info-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; align-items: center; }
.info-label { color: var(--t-secondary); }
.info-value { font-weight: 600; color: var(--t-primary); text-align: right; }
.info-value.mono { font-family: var(--f-mono); }

.sf-alert { padding:14px 18px; border-radius:var(--r-md); margin-bottom: 20px; font-size: 14px; display: flex; align-items: center; gap: 10px; }
.sf-alert.success { background: #e6f9ec; color: var(--c-green); }
.sf-alert.error { background: #fff0ef; color: var(--c-red); }
.sf-alert.warning { background: #fff4e5; color: var(--c-orange); }

.table-apple { width: 100%; border-collapse: collapse; }
.table-apple th { background: var(--c-surface2); padding: 12px 14px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--t-label); border-bottom: 1px solid var(--c-separator); text-align: left; }
.table-apple td { padding: 14px; border-bottom: 0.5px solid var(--c-separator); font-size: 13px; color: var(--t-primary); vertical-align: middle; }
.table-apple tr:last-child td { border-bottom: none; }
.table-apple tr:hover td { background: var(--c-surface2); }

.badge-apple { padding: 4px 10px; border-radius: var(--r-pill); font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; }
.badge-success { background: #e6f9ec; color: var(--c-green); }
.badge-warning { background: #fff4e5; color: var(--c-orange); }
.badge-danger { background: #fff0ef; color: var(--c-red); }
.badge-secondary { background: var(--c-surface2); color: var(--t-secondary); }

.progress-bar-container { background: var(--c-separator); height: 8px; border-radius: 4px; overflow: hidden; margin-top: 14px; }
.progress-bar-fill { background: var(--c-green); height: 100%; }

/* Modal Styles */
.modal-overlay { position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); z-index: 1000; display: none; align-items: center; justify-content: center; backdrop-filter: blur(4px); padding: 20px; }
.modal-overlay.active { display: flex; }
.modal-content { background: var(--c-surface); border-radius: var(--r-lg); width: 100%; max-width: 580px; padding: 28px; box-shadow: var(--shadow-sm); max-height: 90vh; overflow-y: auto; }
.modal-title { font-size: 22px; font-weight: 700; margin-bottom: 16px; }
.form-control, .form-select { width: 100%; padding: 10px 14px; font-size: 14px; border: 1px solid var(--c-separator); border-radius: 8px; background: var(--c-bg); color: var(--t-primary); margin-bottom: 16px; }
.form-label { display: block; font-size: 12px; font-weight: 600; color: var(--t-secondary); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.04em; }
</style>

<div class="loan-root">
    <div class="loan-wrap">
        <a href="<?= APP_URL ?>/loan" class="btn-back"><i class="fa-solid fa-chevron-left"></i> Back to Loans Register</a>
        
        <?php if ($flashSuccess): ?>
        <div class="sf-alert success">
            <i class="fa-solid fa-check-circle"></i> 
            <?php 
                if ($flashSuccess == 'repayment') echo "Repayment recorded and General Ledger journal entry posted successfully.";
                elseif ($flashSuccess == 'repayment_deleted') echo "Repayment reversed and associated journal entry deleted successfully.";
                elseif ($flashSuccess == 'disbursed') echo "Loan disbursed into bank account and opening journal entry posted.";
                else echo "Action completed successfully.";
            ?>
        </div>
        <?php endif; ?>

        <?php if ($flashError): ?>
        <div class="sf-alert error">
            <i class="fa-solid fa-circle-exclamation"></i> 
            <?php 
                if ($flashError == 'missing_cheque_number') echo "Cheque number is required when paying by Cheque.";
                elseif ($flashError == 'invalid_repayment') echo "Invalid repayment details. Ensure an account is selected and amount is greater than zero.";
                else echo "Action failed. Please verify accounts and entries.";
            ?>
        </div>
        <?php endif; ?>

        <!-- Arrears & Due Date Banner -->
        <?php if ($loan->status === 'Active'): ?>
            <?php if ($loan->is_overdue): ?>
                <div class="sf-alert error" style="border:1px solid rgba(255,59,48,0.3);">
                    <i class="fa-solid fa-triangle-exclamation" style="font-size:18px;"></i>
                    <div style="flex:1;">
                        <strong>Installment Overdue Alert:</strong> This loan has an estimated arrears balance of 
                        <span style="font-family:var(--f-mono); font-weight:700;">Rs. <?= number_format($loan->arrears_amount, 2) ?></span>. 
                        Next scheduled installment date was <strong><?= date('d M Y', strtotime($loan->next_due_date)) ?></strong>.
                    </div>
                </div>
            <?php elseif (!empty($loan->next_due_date)): ?>
                <div class="sf-alert warning" style="border:1px solid rgba(255,149,0,0.3);">
                    <i class="fa-solid fa-calendar-check" style="font-size:18px;"></i>
                    <div style="flex:1;">
                        <strong>Next Installment Due:</strong> <strong><?= date('d M Y', strtotime($loan->next_due_date)) ?></strong> 
                        (<?= ($loan->days_until_due >= 0) ? "in {$loan->days_until_due} days" : abs($loan->days_until_due) . " days ago" ?>)
                        <?php if ($loan->monthly_installment > 0): ?>
                            — Installment Amount: <span style="font-family:var(--f-mono); font-weight:600;">Rs. <?= number_format($loan->monthly_installment, 2) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="loan-header">
            <div>
                <h1 class="loan-title"><?= htmlspecialchars($loan->lender_name) ?></h1>
                <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin-top:4px;">
                    <span style="color:var(--t-secondary); font-size:14px;">Facility: <strong><?= htmlspecialchars($loan->loan_type ?? 'Term Loan') ?></strong></span>
                    <span style="color:var(--t-separator);">•</span>
                    <span style="color:var(--t-secondary); font-size:14px;">Agreement / No: <strong style="font-family:var(--f-mono);"><?= htmlspecialchars($loan->loan_number ?: 'N/A') ?></strong></span>
                    <?php if ($loan->is_historical_opening == 1): ?>
                        <span class="badge-apple badge-secondary"><i class="fa-solid fa-clock-rotate-left"></i> Pre-existing Onboarding</span>
                    <?php endif; ?>
                </div>
            </div>
            <div style="display:flex; gap:10px; align-items:center;">
                <a href="<?= APP_URL ?>/loan/edit/<?= $loan->id ?>" class="btn-apple secondary"><i class="fa-solid fa-pen"></i> Edit Loan</a>
                <?php if ($loan->status == 'Pending'): ?>
                    <button class="btn-apple success" onclick="openModal('disburseModal')"><i class="fa-solid fa-building-columns"></i> Disburse Funds</button>
                <?php elseif ($loan->status == 'Active'): ?>
                    <button class="btn-apple" onclick="openModal('repayModal')"><i class="fa-solid fa-money-bill-wave"></i> Record Repayment</button>
                <?php endif; ?>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
            <!-- Repayment History Table -->
            <div>
                <div class="card-apple" style="padding:0;">
                    <div style="padding: 20px 24px 12px; display:flex; justify-content:space-between; align-items:center;">
                        <h3 class="card-apple-title" style="margin:0;">Repayments Ledger</h3>
                        <span style="font-size:12px; color:var(--t-secondary);">Total Repayments: <strong><?= count($repayments) ?></strong></span>
                    </div>
                    <table class="table-apple">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Ref / Method</th>
                                <th style="text-align: right;">Principal</th>
                                <th style="text-align: right;">Interest</th>
                                <th style="text-align: right;">Charges / Rebate</th>
                                <th style="text-align: right;">Cash Paid</th>
                                <th style="text-align: center;">GL Entry</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($repayments)): ?>
                                <tr><td colspan="8" style="text-align: center; padding: 40px; color: var(--t-secondary);">No repayments recorded yet.</td></tr>
                            <?php else: ?>
                                <?php foreach($repayments as $rep): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight:600;"><?= date('d M Y', strtotime($rep->payment_date)) ?></div>
                                        <div style="font-size:11px; color:var(--t-label);"><?= htmlspecialchars($rep->bank_name ?? 'Bank') ?></div>
                                    </td>
                                    <td>
                                        <div style="font-family: var(--f-mono); font-weight:600;"><?= htmlspecialchars($rep->reference ?: 'PAY-' . $rep->id) ?></div>
                                        <div style="font-size:11px; color:var(--t-secondary); margin-top:3px;">
                                            <?php if ($rep->payment_method === 'Cash'): ?>
                                                <span class="badge-apple" style="background:rgba(52,199,89,0.12); color:#28a745; font-size:10.5px; font-weight:700; padding:2px 7px; border-radius:4px; display:inline-flex; align-items:center; gap:4px;">
                                                    <i class="fa-solid fa-money-bill-wave"></i> Cash
                                                </span>
                                            <?php elseif ($rep->payment_method === 'Cheque'): ?>
                                                <span class="badge-apple" style="background:rgba(255,149,0,0.12); color:#ff9500; font-size:10.5px; font-weight:700; padding:2px 7px; border-radius:4px; display:inline-flex; align-items:center; gap:4px;">
                                                    <i class="fa-solid fa-money-check"></i> Cheque<?= $rep->cheque_number ? ' #' . htmlspecialchars($rep->cheque_number) : '' ?>
                                                </span>
                                                <?php if (!empty($rep->banking_date)): ?>
                                                    <div style="font-size:10px; color:#2563eb; font-weight:600; margin-top:2px;"><i class="fa-solid fa-calendar-check"></i> Banking: <?= date('d M Y', strtotime($rep->banking_date)) ?></div>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="badge-apple" style="background:rgba(0,122,255,0.12); color:#007aff; font-size:10.5px; font-weight:700; padding:2px 7px; border-radius:4px; display:inline-flex; align-items:center; gap:4px;">
                                                    <i class="fa-solid fa-building-columns"></i> <?= htmlspecialchars($rep->payment_method ?: 'Bank Transfer') ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td style="text-align: right; font-family: var(--f-mono); font-weight:600;">Rs. <?= number_format($rep->principal_amount, 2) ?></td>
                                    <td style="text-align: right; font-family: var(--f-mono); color:var(--c-orange);">Rs. <?= number_format($rep->interest_amount, 2) ?></td>
                                    <td style="text-align: right; font-family: var(--f-mono); font-size:12px;">
                                        <?php if ($rep->bank_charges > 0 || $rep->penalty_charges > 0): ?>
                                            <div>+Rs. <?= number_format($rep->bank_charges + $rep->penalty_charges, 2) ?></div>
                                        <?php endif; ?>
                                        <?php if ($rep->early_settlement_rebate > 0): ?>
                                            <div style="color:var(--c-green);">-Rs. <?= number_format($rep->early_settlement_rebate, 2) ?> (Rebate)</div>
                                        <?php endif; ?>
                                        <?php if ($rep->bank_charges == 0 && $rep->penalty_charges == 0 && $rep->early_settlement_rebate == 0): ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: right; font-family: var(--f-mono); font-weight:700; color:var(--t-primary);">
                                        Rs. <?= number_format($rep->total_payment, 2) ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <?php if (!empty($rep->journal_entry_id)): ?>
                                            <span class="badge-apple badge-secondary" style="font-family:var(--f-mono); font-size:11px;">#JE-<?= $rep->journal_entry_id ?></span>
                                        <?php else: ?>
                                            <span style="color:var(--t-label); font-size:11px;">Posted</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: right;">
                                        <form action="<?= APP_URL ?>/loan/delete_repayment/<?= $rep->id ?>" method="POST" style="margin:0; display:inline;" onsubmit="return confirm('Reverse this repayment? This will delete the linked journal entry and recalculate the loan balance.');">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                            <input type="hidden" name="loan_id" value="<?= $loan->id ?>">
                                            <button type="submit" class="btn-apple danger" style="padding: 4px 10px; font-size: 11px;" title="Reverse Repayment">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Summary & Information Card -->
            <div>
                <div class="card-apple">
                    <h3 class="card-apple-title">Financial Summary</h3>
                    
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="info-value">
                            <?php if ($loan->status == 'Active'): ?>
                                <span class="badge-apple badge-success"><i class="fa-solid fa-circle-check"></i> Active</span>
                            <?php elseif ($loan->status == 'Pending'): ?>
                                <span class="badge-apple badge-warning"><i class="fa-solid fa-hourglass-half"></i> Pending Disbursement</span>
                            <?php elseif ($loan->status == 'Closed'): ?>
                                <span class="badge-apple badge-secondary"><i class="fa-solid fa-check-double"></i> Fully Settled</span>
                            <?php else: ?>
                                <span class="badge-apple badge-secondary"><?= htmlspecialchars($loan->status) ?></span>
                            <?php endif; ?>
                        </span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Original Principal</span>
                        <span class="info-value mono">Rs. <?= number_format($loan->principal_amount, 2) ?></span>
                    </div>
                    <?php if ($loan->is_historical_opening == 1 && $loan->opening_balance > 0): ?>
                    <div class="info-row">
                        <span class="info-label">Onboarding Balance</span>
                        <span class="info-value mono">Rs. <?= number_format($loan->opening_balance, 2) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="info-row">
                        <span class="info-label">Monthly Installment (EMI)</span>
                        <span class="info-value mono" style="font-weight:700;">Rs. <?= number_format($loan->monthly_installment ?? 0, 2) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Interest Rate</span>
                        <span class="info-value mono"><?= $loan->interest_rate ?>% (<?= htmlspecialchars($loan->interest_type ?? 'Flat Rate') ?>)</span>
                    </div>
                    
                    <hr style="border:0; border-top:1px solid var(--c-separator); margin: 14px 0;">
                    
                    <div class="info-row">
                        <span class="info-label">Principal Paid</span>
                        <span class="info-value mono" style="color:var(--c-green)">Rs. <?= number_format($loan->total_principal_paid, 2) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Interest Paid</span>
                        <span class="info-value mono" style="color:var(--c-orange)">Rs. <?= number_format($loan->total_interest_paid, 2) ?></span>
                    </div>
                    <?php if ($loan->total_charges_paid > 0): ?>
                    <div class="info-row">
                        <span class="info-label">Charges & Penalties</span>
                        <span class="info-value mono">Rs. <?= number_format($loan->total_charges_paid, 2) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($loan->total_rebate_paid > 0): ?>
                    <div class="info-row">
                        <span class="info-label">Early Settlement Rebate</span>
                        <span class="info-value mono" style="color:var(--c-green)">Rs. <?= number_format($loan->total_rebate_paid, 2) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <hr style="border:0; border-top:1px solid var(--c-separator); margin: 14px 0;">
                    
                    <div class="info-row" style="font-size:16px;">
                        <span class="info-label" style="font-weight:700;">Remaining Balance</span>
                        <span class="info-value mono" style="color:var(--c-red); font-weight:700;">Rs. <?= number_format($loan->principal_balance, 2) ?></span>
                    </div>
                    
                    <?php 
                        $effectiveBase = ($loan->is_historical_opening == 1 && $loan->opening_balance > 0) ? $loan->opening_balance : $loan->principal_amount;
                        $pct = ($effectiveBase > 0) ? min(100, round((($loan->total_principal_paid + $loan->total_rebate_paid) / $effectiveBase) * 100)) : 0;
                    ?>
                    <div class="progress-bar-container">
                        <div class="progress-bar-fill" style="width: <?= $pct ?>%;"></div>
                    </div>
                    <div style="text-align: right; font-size: 11px; color: var(--t-secondary); margin-top: 6px; font-weight: 600;"><?= $pct ?>% Principal Settled</div>
                </div>

                <!-- Account & Term Card -->
                <div class="card-apple">
                    <h3 class="card-apple-title">Account & Term Mapping</h3>
                    <div class="info-row">
                        <span class="info-label">Liability Account</span>
                        <span class="info-value" style="font-size:13px;"><?= htmlspecialchars($loan->liability_account_name ?? 'Liability (2500)') ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Interest Account</span>
                        <span class="info-value" style="font-size:13px;"><?= htmlspecialchars($loan->interest_account_name ?? 'Interest Expense (6150 / 6037)') ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Start Date</span>
                        <span class="info-value"><?= date('d M Y', strtotime($loan->loan_start_date)) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Maturity Date</span>
                        <span class="info-value"><?= $loan->maturity_date ? date('d M Y', strtotime($loan->maturity_date)) : 'N/A' ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Term</span>
                        <span class="info-value"><?= $loan->loan_term_months ? $loan->loan_term_months . ' Months' : 'N/A' ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Frequency</span>
                        <span class="info-value"><?= $loan->repayment_frequency ?></span>
                    </div>
                    
                    <?php if ($loan->notes): ?>
                    <hr style="border:0; border-top:1px solid var(--c-separator); margin: 14px 0;">
                    <div style="font-size: 13px; color: var(--t-secondary); line-height: 1.5;">
                        <strong style="display:block; margin-bottom:4px; color:var(--t-primary);">Security & Notes:</strong>
                        <?= nl2br(htmlspecialchars($loan->notes)) ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Disburse Modal -->
<div class="modal-overlay" id="disburseModal">
    <form class="modal-content" action="<?= APP_URL ?>/loan/disburse/<?= $loan->id ?>" method="POST">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
        <h5 class="modal-title">Disburse Loan Funds</h5>
        <p style="color:var(--t-secondary); font-size:13px; margin-bottom:18px;">
            This will activate the loan and post the opening General Ledger debit entry to your bank account.
        </p>
        
        <label class="form-label">Deposit Into Bank Account <span style="color:var(--c-red)">*</span></label>
        <select name="bank_account_id" class="form-select" required>
            <option value="">-- Select Bank Account --</option>
            <?php foreach($bank_accounts as $acc): ?>
                <option value="<?= $acc->id ?>"><?= htmlspecialchars($acc->bank_name . ' (' . $acc->account_number . ')') ?></option>
            <?php endforeach; ?>
        </select>
        
        <label class="form-label">Bank Processing Fees Deducted (Optional)</label>
        <input type="number" step="0.01" name="processing_fees" class="form-control" placeholder="0.00" value="0.00">
        
        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:10px;">
            <button type="button" class="btn-apple secondary" onclick="closeModal('disburseModal')">Cancel</button>
            <button type="submit" class="btn-apple success"><i class="fa-solid fa-check"></i> Confirm Disbursement</button>
        </div>
    </form>
</div>

<!-- Record Repayment Modal -->
<div class="modal-overlay" id="repayModal">
    <form class="modal-content" action="<?= APP_URL ?>/loan/repay/<?= $loan->id ?>" method="POST" id="repayForm">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
        <h5 class="modal-title">Record Loan Repayment</h5>
        <p style="color:var(--t-secondary); font-size:13px; margin-bottom:18px;">
            Records payment, reduces principal balance, and creates the double-entry GL journal entry automatically.
        </p>
        
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
            <div>
                <label class="form-label">Payment Date <span style="color:var(--c-red)">*</span></label>
                <input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div>
                <label class="form-label">Payment Method <span style="color:var(--c-red)">*</span></label>
                <select name="payment_method" id="payment_method" class="form-select" required onchange="handlePaymentMethodChange()">
                    <option value="Bank Transfer">Bank Transfer</option>
                    <option value="Cheque">Cheque Payment</option>
                    <option value="Cash">Cash Payment</option>
                    <option value="Standing Order">Standing Order / Direct Debit</option>
                </select>
            </div>
        </div>

        <div id="chequeFields" style="display:none; grid-template-columns:1fr 1fr 1fr; gap:14px; margin-bottom:14px; background:var(--c-surface2); padding:12px; border-radius:var(--r-md); border:1px dashed var(--c-separator);">
            <div>
                <label class="form-label">Cheque Number <span style="color:var(--c-red)">*</span></label>
                <input type="text" name="cheque_number" id="cheque_number_input" class="form-control" placeholder="e.g. 598779" maxlength="30">
            </div>
            <div>
                <label class="form-label">Cheque Issue Date</label>
                <input type="date" name="cheque_date" class="form-control" value="<?= date('Y-m-d') ?>">
            </div>
            <div>
                <label class="form-label">Banking Date <span style="color:var(--c-red)">*</span></label>
                <input type="date" name="banking_date" id="banking_date_input" class="form-control" value="<?= date('Y-m-d') ?>">
                <small style="font-size:11px; color:var(--t-secondary);">Cheque realization date</small>
            </div>
        </div>
        
        <div class="form-group" style="margin-bottom:14px;">
            <label class="form-label" id="account_label">Pay From Bank Account <span style="color:var(--c-red)">*</span></label>
            <select name="bank_account_id" id="repay_account_select" class="form-select" required>
                <option value="">-- Select Account --</option>
                <optgroup label="Bank Accounts" id="optgroup_banks">
                    <?php foreach($bank_accounts as $acc): ?>
                        <option value="<?= $acc->id ?>" data-type="bank"><?= htmlspecialchars($acc->bank_name . ' (' . $acc->account_number . ')') ?></option>
                    <?php endforeach; ?>
                </optgroup>
                <optgroup label="Cash Accounts / Registers" id="optgroup_cash">
                    <?php foreach($cash_accounts as $cacc): ?>
                        <option value="<?= $cacc->id ?>" data-type="cash"><?= htmlspecialchars($cacc->account_number . ' - ' . $cacc->bank_name) ?></option>
                    <?php endforeach; ?>
                </optgroup>
            </select>
        </div>
        
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div>
                <label class="form-label">Principal Amount <span style="color:var(--c-red)">*</span></label>
                <input type="number" step="0.01" name="principal_amount" id="rep_principal" class="form-control" required placeholder="0.00" oninput="calculateTotalPayment()">
            </div>
            <div>
                <label class="form-label">Interest Amount</label>
                <input type="number" step="0.01" name="interest_amount" id="rep_interest" class="form-control" placeholder="0.00" value="0.00" oninput="calculateTotalPayment()">
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div>
                <label class="form-label">Bank Charges / Fees</label>
                <input type="number" step="0.01" name="bank_charges" id="rep_charges" class="form-control" placeholder="0.00" value="0.00" oninput="calculateTotalPayment()">
            </div>
            <div>
                <label class="form-label">Late / Penalty Charges</label>
                <input type="number" step="0.01" name="penalty_charges" id="rep_penalties" class="form-control" placeholder="0.00" value="0.00" oninput="calculateTotalPayment()">
            </div>
        </div>

        <div class="form-group" style="margin-bottom:12px;">
            <label class="form-label">Early Settlement Rebate / Discount (Optional)</label>
            <input type="number" step="0.01" name="early_settlement_rebate" id="rep_rebate" class="form-control" placeholder="0.00" value="0.00" oninput="calculateTotalPayment()">
            <span style="font-size:11px; color:var(--t-secondary);">Reduces principal liability without cash outflow.</span>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div>
                <label class="form-label">Payment Reference</label>
                <input type="text" name="reference" class="form-control" placeholder="e.g. TXN-882190">
            </div>
            <div>
                <label class="form-label">Total Cash Payment (Rs.)</label>
                <input type="text" id="disp_total_payment" class="form-control" readonly style="font-weight:700; font-family:var(--f-mono); background:var(--c-surface2);" value="0.00">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Notes</label>
            <input type="text" name="notes" class="form-control" placeholder="Payment notes...">
        </div>
        
        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:10px;">
            <button type="button" class="btn-apple secondary" onclick="closeModal('repayModal')">Cancel</button>
            <button type="submit" class="btn-apple"><i class="fa-solid fa-check"></i> Save Repayment</button>
        </div>
    </form>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }

function handlePaymentMethodChange() {
    const method = document.getElementById('payment_method').value;
    const chqDiv = document.getElementById('chequeFields');
    const chqInput = document.getElementById('cheque_number_input');
    const chqBankDate = document.getElementById('banking_date_input');
    const accLabel = document.getElementById('account_label');
    const accSelect = document.getElementById('repay_account_select');
    const bankGroup = document.getElementById('optgroup_banks');
    const cashGroup = document.getElementById('optgroup_cash');

    if (method === 'Cheque') {
        chqDiv.style.display = 'grid';
        if (chqInput) chqInput.required = true;
        if (chqBankDate) chqBankDate.required = true;
        if (accLabel) accLabel.innerHTML = 'Issue Cheque From Bank Account <span style="color:var(--c-red)">*</span>';
        
        // Show bank accounts only
        if (bankGroup) { bankGroup.hidden = false; bankGroup.disabled = false; }
        if (cashGroup) { cashGroup.hidden = true; cashGroup.disabled = true; }
        
        if (accSelect.selectedOptions[0] && accSelect.selectedOptions[0].getAttribute('data-type') === 'cash') {
            accSelect.value = '';
        }
    } else if (method === 'Cash') {
        chqDiv.style.display = 'none';
        if (chqInput) chqInput.required = false;
        if (chqBankDate) chqBankDate.required = false;
        if (accLabel) accLabel.innerHTML = 'Pay From Cash Account / Register <span style="color:var(--c-red)">*</span>';
        
        // Show cash accounts only
        if (bankGroup) { bankGroup.hidden = true; bankGroup.disabled = true; }
        if (cashGroup) { cashGroup.hidden = false; cashGroup.disabled = false; }
        
        if (accSelect.selectedOptions[0] && accSelect.selectedOptions[0].getAttribute('data-type') === 'bank') {
            accSelect.value = '';
        }
        // If nothing selected, select first available cash register
        if (!accSelect.value && cashGroup && cashGroup.children.length > 0) {
            accSelect.value = cashGroup.children[0].value;
        }
    } else { // Bank Transfer or Standing Order
        chqDiv.style.display = 'none';
        if (chqInput) chqInput.required = false;
        if (chqBankDate) chqBankDate.required = false;
        if (accLabel) accLabel.innerHTML = 'Pay From Bank Account <span style="color:var(--c-red)">*</span>';
        
        // Show bank accounts only
        if (bankGroup) { bankGroup.hidden = false; bankGroup.disabled = false; }
        if (cashGroup) { cashGroup.hidden = true; cashGroup.disabled = true; }
        
        if (accSelect.selectedOptions[0] && accSelect.selectedOptions[0].getAttribute('data-type') === 'cash') {
            accSelect.value = '';
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    handlePaymentMethodChange();
});

function calculateTotalPayment() {
    const p = parseFloat(document.getElementById('rep_principal').value) || 0;
    const i = parseFloat(document.getElementById('rep_interest').value) || 0;
    const c = parseFloat(document.getElementById('rep_charges').value) || 0;
    const pen = parseFloat(document.getElementById('rep_penalties').value) || 0;
    const total = p + i + c + pen;
    document.getElementById('disp_total_payment').value = total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
</script>
