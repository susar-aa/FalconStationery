<?php
$liabilities = $data['liabilities'] ?? [];
$expenses = $data['expenses'] ?? [];
$flashError = $_GET['error'] ?? null;
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
    --c-green:        #34c759;
    --c-orange:       #ff9500;
    --c-red:          #ff3b30;
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

.type-toggle-box {
    display: flex; gap: 12px; background: var(--c-bg); padding: 6px; border-radius: 12px; margin-bottom: 24px; border: 1px solid var(--c-separator);
}
.type-toggle-btn {
    flex: 1; padding: 12px; text-align: center; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.2s; user-select: none;
}
.type-toggle-btn.active {
    background: var(--c-surface); color: var(--c-blue); box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}

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
        <a href="<?= APP_URL ?>/loan" class="btn-back"><i class="fa-solid fa-chevron-left"></i> Back to Loans</a>
        
        <div class="loan-header">
            <h1 class="loan-title">Register Bank Loan</h1>
            <p style="color:var(--t-secondary); margin:0;">Configure loan liability, interest accounts, and repayment terms.</p>
        </div>

        <?php if ($flashError): ?>
        <div class="sf-alert error"><i class="fa-solid fa-circle-exclamation"></i> 
            <?php 
                if ($flashError == 'validation') echo "Please fill in all required fields (Lender, Principal, and Liability Account).";
                else echo "Failed to save loan. Please check your inputs.";
            ?>
        </div>
        <?php endif; ?>

        <div class="card-apple">
            <form action="<?= APP_URL ?>/loan/create" method="POST" id="loanCreateForm">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <input type="hidden" name="is_historical_opening" id="is_historical_opening" value="0">

                <!-- Loan Onboarding Mode Selection -->
                <label class="form-label">Loan Setup Mode</label>
                <div class="type-toggle-box">
                    <div class="type-toggle-btn active" id="btnModeNew" onclick="setLoanMode(0)">
                        <i class="fa-solid fa-plus-circle"></i> New Loan (Awaiting Disbursement to Bank)
                    </div>
                    <div class="type-toggle-btn" id="btnModeExisting" onclick="setLoanMode(1)">
                        <i class="fa-solid fa-clock-rotate-left"></i> Pre-existing Loan (Already Active / Opening Balance)
                    </div>
                </div>

                <div id="historicalAlert" style="display:none; padding:12px 16px; background:rgba(52, 199, 89, 0.1); border:1px solid rgba(52, 199, 89, 0.3); border-radius:8px; margin-bottom:20px; font-size:13px; color:var(--c-green);">
                    <i class="fa-solid fa-info-circle"></i> <strong>Existing Loan Mode:</strong> This loan will be activated immediately without creating a disbursement bank debit entry (preventing artificial bank ledger inflation).
                </div>

                <!-- Basic Information -->
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label">Lender / Financial Institution <span style="color:var(--c-red)">*</span></label>
                        <input type="text" name="lender_name" class="form-control" required placeholder="e.g. LOLC Finance, Bank of Ceylon, Commercial Bank">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Loan / Agreement No.</label>
                        <input type="text" name="loan_number" class="form-control" placeholder="e.g. CD/26/7090C-1">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label">Facility / Loan Type</label>
                        <select name="loan_type" class="form-select">
                            <option value="Term Loan">Term Loan</option>
                            <option value="Revolving Loan">Revolving Credit / Loan</option>
                            <option value="Lease / Hire Purchase">Lease / Hire Purchase</option>
                            <option value="Overdraft Facility">Overdraft Facility</option>
                            <option value="Cheque Discounting">Cheque Discounting / Factor</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Repayment Frequency <span style="color:var(--c-red)">*</span></label>
                        <select name="repayment_frequency" class="form-select" required>
                            <option value="Monthly" selected>Monthly</option>
                            <option value="Weekly">Weekly</option>
                            <option value="Quarterly">Quarterly</option>
                            <option value="Yearly">Yearly</option>
                            <option value="One-Time">One-Time (Bullet Payment)</option>
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
                                <option value="<?= $acc->id ?>" <?= in_array($acc->account_code, ['2500', '2510', '2511', '2512', '2513']) ? 'selected' : '' ?>>
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
                                    <option value="<?= $acc->id ?>">
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
                                    <option value="<?= $acc->id ?>" <?= $acc->account_code == '6100' ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($acc->account_code . ' — ' . $acc->account_name) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Financial Details -->
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label">Principal Amount (Rs.) <span style="color:var(--c-red)">*</span></label>
                        <input type="number" step="0.01" name="principal_amount" id="principal_amount" class="form-control" required placeholder="0.00">
                    </div>
                    <div class="form-group" id="groupOpeningBal" style="display:none;">
                        <label class="form-label">Current Balance / Opening (Rs.)</label>
                        <input type="number" step="0.01" name="opening_balance" id="opening_balance" class="form-control" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Monthly Installment (EMI)</label>
                        <input type="number" step="0.01" name="monthly_installment" class="form-control" placeholder="e.g. 108500.00">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Annual Interest Rate (%)</label>
                        <input type="number" step="0.01" name="interest_rate" class="form-control" placeholder="e.g. 14.50" value="0.00">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label">Interest Calculation Type</label>
                        <select name="interest_type" class="form-select">
                            <option value="Flat Rate">Flat Rate</option>
                            <option value="Reducing Balance">Reducing Balance / Amortized</option>
                            <option value="Fixed">Fixed Interest Amount</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Term (Months)</label>
                        <input type="number" name="loan_term_months" class="form-control" placeholder="e.g. 12, 24, 36">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label">Loan Start Date <span style="color:var(--c-red)">*</span></label>
                        <input type="date" name="loan_start_date" class="form-control" required value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">First Installment Due Date</label>
                        <input type="date" name="first_payment_date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Maturity Date</label>
                        <input type="date" name="maturity_date" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Notes & Security Details (Optional)</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Guarantors, security pledged, breakdown notes..."></textarea>
                </div>

                <div style="margin-top: 30px;">
                    <button type="submit" class="btn-apple"><i class="fa-solid fa-check"></i> Register Loan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function setLoanMode(isHistorical) {
    document.getElementById('is_historical_opening').value = isHistorical;
    if (isHistorical === 1) {
        document.getElementById('btnModeExisting').classList.add('active');
        document.getElementById('btnModeNew').classList.remove('active');
        document.getElementById('groupOpeningBal').style.display = 'block';
        document.getElementById('historicalAlert').style.display = 'block';
        const p = document.getElementById('principal_amount').value;
        if (p && !document.getElementById('opening_balance').value) {
            document.getElementById('opening_balance').value = p;
        }
    } else {
        document.getElementById('btnModeNew').classList.add('active');
        document.getElementById('btnModeExisting').classList.remove('active');
        document.getElementById('groupOpeningBal').style.display = 'none';
        document.getElementById('historicalAlert').style.display = 'none';
    }
}
</script>
