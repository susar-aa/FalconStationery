<?php
$loan = $data['loan'] ?? null;
$liabilities = $data['liabilities'] ?? [];
$expenses = $data['expenses'] ?? [];
$flashError = $_GET['error'] ?? null;

$isPending = ($loan && $loan->status === 'Pending');
?>

<!-- Inter Font & FontAwesome Icons -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
:root {
    --c-bg:           #f2f2f7;
    --c-surface:      #ffffff;
    --c-separator:    rgba(60,60,67,0.12);
    --c-blue:         #007aff;
    --c-red:          #ff3b30;
    --c-orange:       #ff9500;
    --f-system: -apple-system, 'SF Pro Display', 'SF Pro Text', 'Inter', 'Helvetica Neue', sans-serif;
    --t-primary:   #1c1c1e;
    --t-secondary: #636366;
    --t-label:     #8e8e93;
    --shadow-sm:  0 2px 8px rgba(0,0,0,0.06), 0 1px 3px rgba(0,0,0,0.04);
    --r-md: 14px;
    --r-pill: 999px;
    --dur-fast:    0.18s;
}

@media (prefers-color-scheme: dark) {
    :root {
        --c-bg:           #121212;
        --c-surface:      #1e1e2e;
        --c-separator:    rgba(255,255,255,0.15);
        --t-primary:      #f5f5f7;
        --t-secondary:    #a1a1aa;
    }
}

.loan-root { font-family: var(--f-system); font-size: 15px; color: var(--t-primary); background: var(--c-bg); -webkit-font-smoothing: antialiased; }
.loan-wrap { max-width: 860px; margin: 0 auto; padding: 40px 24px 100px; }
.loan-header { margin-bottom: 24px; }
.loan-title { font-size: 32px; font-weight: 700; letter-spacing: -0.03em; color: var(--t-primary); margin-bottom: 4px; }
.btn-back { display: inline-flex; align-items: center; gap: 6px; color: var(--c-blue); text-decoration: none; font-weight: 600; margin-bottom: 16px; }

.card-apple {
    background: var(--c-surface); border-radius: var(--r-md); box-shadow: var(--shadow-sm); border: 0.5px solid var(--c-separator); padding: 32px;
}

.form-group { margin-bottom: 20px; }
.form-label { display: block; font-size: 13px; font-weight: 600; color: var(--t-secondary); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.04em; }
.form-control, .form-select {
    width: 100%; padding: 12px 16px; font-size: 15px; font-family: var(--f-system);
    border: 1px solid var(--c-separator); border-radius: 8px; background: var(--c-bg);
    color: var(--t-primary); transition: border-color var(--dur-fast);
}
.form-control:focus, .form-select:focus { outline: none; border-color: var(--c-blue); background: var(--c-surface); }
.form-control:read-only { background: var(--c-separator); cursor: not-allowed; opacity: 0.7; }

.btn-apple {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    background: var(--c-blue); color: #fff; border: none; border-radius: var(--r-pill);
    padding: 14px 28px; font-size: 16px; font-weight: 600; transition: all var(--dur-fast); cursor: pointer; width: 100%;
}
.btn-apple:hover { background: #0062cc; transform: translateY(-1px); }

.sf-alert { padding:15px; border-radius:var(--r-md); margin-bottom: 20px; font-size: 14px; display: flex; align-items: center; gap: 10px; }
.sf-alert.error { background: #fff0ef; color: var(--c-red); }
</style>

<div class="loan-root">
    <div class="loan-wrap">
        <a href="<?= APP_URL ?>/loan/show/<?= $loan->id ?>" class="btn-back"><i class="fa-solid fa-chevron-left"></i> Back to Loan Details</a>
        
        <div class="loan-header">
            <h1 class="loan-title">Edit Loan: <?= htmlspecialchars($loan->lender_name) ?></h1>
            <p style="color:var(--t-secondary); margin:0;">Update loan details, account mappings, and repayment terms.</p>
        </div>

        <?php if ($flashError): ?>
        <div class="sf-alert error"><i class="fa-solid fa-circle-exclamation"></i> Failed to update loan. Please check your inputs.</div>
        <?php endif; ?>

        <?php if (!$isPending && $loan->is_historical_opening == 0): ?>
        <div class="sf-alert" style="background: rgba(255, 149, 0, 0.1); color: var(--c-orange);">
            <i class="fa-solid fa-triangle-exclamation"></i> <strong>Note:</strong> Original principal amount is locked because this loan has already been disbursed. You can still modify installment, account mappings, and schedule dates.
        </div>
        <?php endif; ?>

        <div class="card-apple">
            <form action="<?= APP_URL ?>/loan/edit/<?= $loan->id ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label">Lender / Financial Institution <span style="color:var(--c-red)">*</span></label>
                        <input type="text" name="lender_name" class="form-control" required value="<?= htmlspecialchars($loan->lender_name) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Loan / Agreement No.</label>
                        <input type="text" name="loan_number" class="form-control" value="<?= htmlspecialchars($loan->loan_number) ?>">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label">Facility / Loan Type</label>
                        <select name="loan_type" class="form-select">
                            <option value="Term Loan" <?= ($loan->loan_type == 'Term Loan') ? 'selected' : '' ?>>Term Loan</option>
                            <option value="Revolving Loan" <?= ($loan->loan_type == 'Revolving Loan') ? 'selected' : '' ?>>Revolving Credit / Loan</option>
                            <option value="Lease / Hire Purchase" <?= ($loan->loan_type == 'Lease / Hire Purchase') ? 'selected' : '' ?>>Lease / Hire Purchase</option>
                            <option value="Overdraft Facility" <?= ($loan->loan_type == 'Overdraft Facility') ? 'selected' : '' ?>>Overdraft Facility</option>
                            <option value="Cheque Discounting" <?= ($loan->loan_type == 'Cheque Discounting') ? 'selected' : '' ?>>Cheque Discounting / Factor</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Repayment Frequency <span style="color:var(--c-red)">*</span></label>
                        <select name="repayment_frequency" class="form-select" required>
                            <option value="Monthly" <?= $loan->repayment_frequency == 'Monthly' ? 'selected' : '' ?>>Monthly</option>
                            <option value="Weekly" <?= $loan->repayment_frequency == 'Weekly' ? 'selected' : '' ?>>Weekly</option>
                            <option value="Quarterly" <?= $loan->repayment_frequency == 'Quarterly' ? 'selected' : '' ?>>Quarterly</option>
                            <option value="Yearly" <?= $loan->repayment_frequency == 'Yearly' ? 'selected' : '' ?>>Yearly</option>
                            <option value="One-Time" <?= $loan->repayment_frequency == 'One-Time' ? 'selected' : '' ?>>One-Time (Bullet Payment)</option>
                        </select>
                    </div>
                </div>

                <!-- Chart of Accounts Mappings -->
                <div style="background: var(--c-bg); border: 1px solid var(--c-separator); border-radius: 10px; padding: 18px; margin-bottom: 24px;">
                    <div style="font-weight: 700; font-size: 14px; margin-bottom: 14px; color: var(--t-primary); display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-book-bookmark text-primary"></i> General Ledger Account Mappings
                    </div>

                    <div class="form-group">
                        <label class="form-label">Liability Account (Balance Sheet) <span style="color:var(--c-red)">*</span></label>
                        <select name="liability_account_id" class="form-select" required>
                            <option value="">-- Select Liability Account --</option>
                            <?php foreach ($liabilities as $acc): ?>
                                <option value="<?= $acc->id ?>" <?= $loan->liability_account_id == $acc->id ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($acc->account_code . ' — ' . $acc->account_name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Dedicated Interest Expense Account</label>
                            <select name="interest_expense_account_id" class="form-select">
                                <option value="">-- Auto Resolve / Default (6150 / 6037) --</option>
                                <?php foreach ($expenses as $acc): ?>
                                    <option value="<?= $acc->id ?>" <?= $loan->interest_expense_account_id == $acc->id ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($acc->account_code . ' — ' . $acc->account_name) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Bank Charges / Fees Account</label>
                            <select name="bank_charges_account_id" class="form-select">
                                <option value="">-- Auto Resolve / Default (6100) --</option>
                                <?php foreach ($expenses as $acc): ?>
                                    <option value="<?= $acc->id ?>" <?= $loan->bank_charges_account_id == $acc->id ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($acc->account_code . ' — ' . $acc->account_name) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label">Principal Amount (Rs.) <span style="color:var(--c-red)">*</span></label>
                        <input type="number" step="0.01" name="principal_amount" class="form-control" required value="<?= htmlspecialchars($loan->principal_amount) ?>" <?= (!$isPending && $loan->is_historical_opening == 0) ? 'readonly' : '' ?>>
                    </div>
                    <?php if ($loan->is_historical_opening == 1): ?>
                    <div class="form-group">
                        <label class="form-label">Opening Balance (Rs.)</label>
                        <input type="number" step="0.01" name="opening_balance" class="form-control" value="<?= htmlspecialchars($loan->opening_balance) ?>">
                    </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label class="form-label">Monthly Installment (EMI)</label>
                        <input type="number" step="0.01" name="monthly_installment" class="form-control" value="<?= htmlspecialchars($loan->monthly_installment) ?>" placeholder="e.g. 108500.00">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Annual Interest Rate (%)</label>
                        <input type="number" step="0.01" name="interest_rate" class="form-control" value="<?= htmlspecialchars($loan->interest_rate) ?>">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label">Interest Calculation Type</label>
                        <select name="interest_type" class="form-select">
                            <option value="Flat Rate" <?= $loan->interest_type == 'Flat Rate' ? 'selected' : '' ?>>Flat Rate</option>
                            <option value="Reducing Balance" <?= $loan->interest_type == 'Reducing Balance' ? 'selected' : '' ?>>Reducing Balance / Amortized</option>
                            <option value="Fixed" <?= $loan->interest_type == 'Fixed' ? 'selected' : '' ?>>Fixed Interest Amount</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Term (Months)</label>
                        <input type="number" name="loan_term_months" class="form-control" value="<?= htmlspecialchars($loan->loan_term_months) ?>">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label">Loan Start Date <span style="color:var(--c-red)">*</span></label>
                        <input type="date" name="loan_start_date" class="form-control" required value="<?= htmlspecialchars($loan->loan_start_date) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">First Installment Due Date</label>
                        <input type="date" name="first_payment_date" class="form-control" value="<?= htmlspecialchars($loan->first_payment_date) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Maturity Date</label>
                        <input type="date" name="maturity_date" class="form-control" value="<?= htmlspecialchars($loan->maturity_date) ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Notes & Security Details (Optional)</label>
                    <textarea name="notes" class="form-control" rows="3" style="resize:vertical;"><?= htmlspecialchars($loan->notes) ?></textarea>
                </div>

                <div style="margin-top: 30px;">
                    <button type="submit" class="btn-apple"><i class="fa-solid fa-save"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
