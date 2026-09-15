<?php
$stats = $data['stats'] ?? [];
$filters = $data['filters'] ?? [];
?>

<!-- Inter Font & FontAwesome Icons -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
/* ============================================================
   SF PRO + APPLE DESIGN LANGUAGE — EXPENSES & AP MANAGEMENT
   ============================================================ */
:root {
    --c-bg:           #f2f2f7;
    --c-surface:      #ffffff;
    --c-surface2:     #f9f9fb;
    --c-fill:         rgba(120,120,128,0.12);
    --c-fill2:        rgba(120,120,128,0.16);
    --c-separator:    rgba(60,60,67,0.12);
    --c-separator2:   rgba(60,60,67,0.06);

    --c-blue:         #007aff;
    --c-blue-light:   #e5f2ff;
    --c-green:        #34c759;
    --c-green-light:  #e6f9ec;
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

.exp-root {
    font-family: var(--f-system);
    font-size: 14px;
    color: var(--t-primary);
    background: var(--c-bg);
    padding: 24px;
    min-height: 100vh;
}

.exp-wrap {
    max-width: 1360px;
    margin: 0 auto;
}

/* ---- Page Header Bar ---- */
.exp-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}
.exp-title-area h1 {
    font-size: 26px;
    font-weight: 800;
    letter-spacing: -0.03em;
    color: var(--t-primary);
    margin: 0 0 4px 0;
}
.exp-title-area p {
    font-size: 13px;
    color: var(--t-secondary);
    margin: 0;
}
.exp-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--c-blue);
    color: #ffffff;
    font-weight: 600;
    font-size: 14px;
    padding: 10px 20px;
    border-radius: var(--r-pill);
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 14px rgba(0,122,255,0.3);
    transition: transform var(--dur-fast), background var(--dur-fast);
    text-decoration: none;
}
.exp-btn-primary:hover {
    background: #0066d6;
    transform: translateY(-1px);
}

/* ---- Stats Bar ---- */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 14px;
    margin-bottom: 24px;
}
.stat-card {
    background: var(--c-surface);
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-lg);
    padding: 16px 18px;
    box-shadow: var(--shadow-sm);
    display: flex;
    align-items: center;
    gap: 14px;
}
.stat-icon {
    width: 44px;
    height: 44px;
    border-radius: var(--r-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.stat-card.blue   .stat-icon { background: var(--c-blue-light);   color: var(--c-blue); }
.stat-card.orange .stat-icon { background: var(--c-orange-light); color: var(--c-orange); }
.stat-card.green  .stat-icon { background: var(--c-green-light);  color: var(--c-green); }
.stat-card.red    .stat-icon { background: var(--c-red-light);    color: var(--c-red); }

.stat-info { display: flex; flex-direction: column; }
.stat-num {
    font-size: 20px;
    font-weight: 700;
    letter-spacing: -0.03em;
    color: var(--t-primary);
    line-height: 1.1;
    margin-bottom: 3px;
}
.stat-lbl {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: var(--t-label);
}

/* ---- Filter Bar ---- */
.filter-shelf {
    background: var(--c-surface);
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-lg);
    padding: 14px 18px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-sm);
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    align-items: center;
}
.filter-input {
    background: var(--c-bg);
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-pill);
    padding: 8px 16px;
    font-size: 13px;
    color: var(--t-primary);
    outline: none;
    transition: border-color var(--dur-fast);
}
.filter-input:focus {
    border-color: var(--c-blue);
}
.filter-select {
    background: var(--c-bg);
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-pill);
    padding: 8px 16px;
    font-size: 13px;
    color: var(--t-primary);
    outline: none;
}
.filter-btn {
    background: var(--c-blue-light);
    color: var(--c-blue);
    border: none;
    border-radius: var(--r-pill);
    padding: 8px 16px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
}
.filter-btn-reset {
    background: transparent;
    color: var(--t-secondary);
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-pill);
    padding: 8px 16px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    text-decoration: none;
}

/* ---- Table Card ---- */
.table-card {
    background: var(--c-surface);
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-lg);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}
.exp-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13.5px;
}
.exp-table th {
    background: var(--c-surface2);
    color: var(--t-label);
    font-weight: 700;
    font-size: 11px;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    padding: 12px 18px;
    border-bottom: 0.5px solid var(--c-separator);
    text-align: left;
}
.exp-table td {
    padding: 14px 18px;
    border-bottom: 0.5px solid var(--c-separator2);
    color: var(--t-primary);
}
.exp-table tr:last-child td { border-bottom: none; }
.exp-table tr:hover { background: rgba(0,122,255,0.02); }

.badge-pmethod {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: var(--r-pill);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.02em;
}
.badge-pmethod.petty-cash { background: var(--c-orange-light); color: var(--c-orange); }
.badge-pmethod.bank-transfer { background: var(--c-blue-light); color: var(--c-blue); }
.badge-pmethod.cheque { background: #f3e8ff; color: #9333ea; }
.badge-pmethod.cash { background: var(--c-green-light); color: var(--c-green); }
.badge-pmethod.credit { background: var(--c-red-light); color: var(--c-red); }

/* ---- Modal Backdrop & Content ---- */
.modal-backdrop {
    position: fixed;
    top: 0; left: 0; width: 100vw; height: 100vh;
    background: rgba(0,0,0,0.4);
    backdrop-filter: blur(8px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}
.modal-box {
    background: var(--c-surface);
    border-radius: var(--r-xl);
    width: 100%;
    max-width: 650px;
    padding: 28px;
    box-shadow: var(--shadow-xl);
    border: 0.5px solid var(--c-separator);
    max-height: 90vh;
    overflow-y: auto;
}
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.modal-header h2 {
    font-size: 20px;
    font-weight: 800;
    margin: 0;
    letter-spacing: -0.02em;
}
.modal-close {
    background: var(--c-fill);
    border: none;
    width: 32px; height: 32px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 16px;
    display: flex; align-items: center; justify-content: center;
    color: var(--t-secondary);
}
.form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.form-group { margin-bottom: 16px; }
.form-group label {
    display: block;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    color: var(--t-label);
    margin-bottom: 6px;
}
.form-control-sf {
    width: 100%;
    background: var(--c-bg);
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-sm);
    padding: 10px 14px;
    font-size: 14px;
    color: var(--t-primary);
    outline: none;
    box-sizing: border-box;
    font-family: inherit;
}
.form-control-sf:focus {
    border-color: var(--c-blue);
    box-shadow: 0 0 0 3px rgba(0,122,255,0.12);
}
</style>

<div class="exp-root">
    <div class="exp-wrap">

        <!-- Page Header -->
        <div class="exp-header">
            <div class="exp-title-area">
                <h1>Expenses & Accounts Payable</h1>
                <p>Record business expenses, vendor bills, and automated double-entry accounting ledger entries.</p>
            </div>
            <div>
                <button class="exp-btn-primary" onclick="openExpenseModal()">
                    <i class="fa-solid fa-plus"></i> Record Expense
                </button>
            </div>
        </div>

        <?php if(!empty($_GET['success'])): ?>
            <div style="background: var(--c-green-light); color: var(--c-green); padding: 14px 18px; border-radius: var(--r-md); margin-bottom: 20px; font-weight: 600;">
                <i class="fa-solid fa-circle-check"></i> Expense recorded and posted to double-entry accounting ledgers successfully!
            </div>
        <?php endif; ?>

        <?php if(!empty($data['error'])): ?>
            <div style="background: var(--c-red-light); color: var(--c-red); padding: 14px 18px; border-radius: var(--r-md); margin-bottom: 20px; font-weight: 600;">
                <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($data['error']) ?>
            </div>
        <?php endif; ?>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-icon"><i class="fa-solid fa-receipt"></i></div>
                <div class="stat-info">
                    <span class="stat-num">Rs. <?= number_format($stats['total_amount'] ?? 0, 2) ?></span>
                    <span class="stat-lbl">Total Expenses (<?= number_format($stats['total_count'] ?? 0) ?> Records)</span>
                </div>
            </div>

            <div class="stat-card orange">
                <div class="stat-icon"><i class="fa-solid fa-vault"></i></div>
                <div class="stat-info">
                    <span class="stat-num">Rs. <?= number_format($stats['petty_cash_total'] ?? 0, 2) ?></span>
                    <span class="stat-lbl">Petty Cash Paid</span>
                </div>
            </div>

            <div class="stat-card green">
                <div class="stat-icon"><i class="fa-solid fa-building-columns"></i></div>
                <div class="stat-info">
                    <span class="stat-num">Rs. <?= number_format(($stats['bank_transfer_total'] ?? 0) + ($stats['cheque_total'] ?? 0), 2) ?></span>
                    <span class="stat-lbl">Bank & Cheque Paid</span>
                </div>
            </div>

            <div class="stat-card red">
                <div class="stat-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                <div class="stat-info">
                    <span class="stat-num">Rs. <?= number_format($stats['ap_credit_total'] ?? 0, 2) ?></span>
                    <span class="stat-lbl">AP / Credit Payable</span>
                </div>
            </div>
        </div>

        <!-- Filter Shelf -->
        <form method="GET" action="<?= APP_URL ?>/expenses" class="filter-shelf">
            <input type="text" name="search" class="filter-input" placeholder="Search reference, description..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
            
            <select name="payment_method" class="filter-select">
                <option value="All">All Payment Methods</option>
                <option value="Cash" <?= ($filters['payment_method'] ?? '') === 'Cash' ? 'selected' : '' ?>>Cash</option>
                <option value="Petty Cash" <?= ($filters['payment_method'] ?? '') === 'Petty Cash' ? 'selected' : '' ?>>Petty Cash</option>
                <option value="Bank Transfer" <?= ($filters['payment_method'] ?? '') === 'Bank Transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                <option value="Cheque" <?= ($filters['payment_method'] ?? '') === 'Cheque' ? 'selected' : '' ?>>Cheque</option>
                <option value="Credit" <?= ($filters['payment_method'] ?? '') === 'Credit' ? 'selected' : '' ?>>Credit / AP</option>
            </select>

            <input type="date" name="date_from" class="filter-input" value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>">
            <input type="date" name="date_to" class="filter-input" value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>">

            <button type="submit" class="filter-btn"><i class="fa-solid fa-filter"></i> Apply</button>
            <a href="<?= APP_URL ?>/expenses" class="filter-btn-reset">Reset</a>
        </form>

        <!-- Expenses Data Table Card -->
        <div class="table-card">
            <table class="exp-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Reference</th>
                        <th>Vendor / Payee</th>
                        <th>Expense Account</th>
                        <th>Payment Method</th>
                        <th>Description</th>
                        <th style="text-align: right;">Amount (Rs.)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['expenses'])): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--t-secondary); padding: 30px;">
                                No expense records found. Click <strong>+ Record Expense</strong> to post a new expense.
                            </td>
                        </tr>
                    <?php else: foreach ($data['expenses'] as $exp): 
                        $pm = $exp->payment_method ?? 'Cash';
                        $pmClass = strtolower(str_replace(' ', '-', $pm));
                    ?>
                        <tr>
                            <td style="font-weight: 500;"><?= date('d M Y', strtotime($exp->expense_date)) ?></td>
                            <td><strong style="font-family: var(--f-mono); color: var(--c-blue);"><?= htmlspecialchars($exp->reference) ?></strong></td>
                            <td style="font-weight: 600;"><?= htmlspecialchars($exp->vendor_name ?? 'Miscellaneous') ?></td>
                            <td><span style="font-size: 12px; color: var(--t-secondary);"><?= htmlspecialchars($exp->expense_account_name ?? 'Expense Account') ?></span></td>
                            <td>
                                <span class="badge-pmethod <?= $pmClass ?>">
                                    <?php if ($pm === 'Petty Cash'): ?>
                                        <i class="fa-solid fa-vault"></i> Petty Cash
                                    <?php elseif ($pm === 'Bank Transfer'): ?>
                                        <i class="fa-solid fa-building-columns"></i> Bank Transfer
                                    <?php elseif ($pm === 'Cheque'): ?>
                                        <i class="fa-solid fa-money-check"></i> Cheque
                                    <?php elseif ($pm === 'Credit'): ?>
                                        <i class="fa-solid fa-file-invoice"></i> Credit / AP
                                    <?php else: ?>
                                        <i class="fa-solid fa-money-bill-wave"></i> Cash
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td style="color: var(--t-secondary);"><?= htmlspecialchars($exp->description) ?></td>
                            <td style="text-align: right; font-weight: 700; font-family: var(--f-mono); font-size: 15px;">
                                Rs. <?= number_format(floatval($exp->amount), 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- Modal: Record Expense -->
<div id="expenseModal" class="modal-backdrop">
    <div class="modal-box">
        <div class="modal-header">
            <h2>Record Business Expense</h2>
            <button class="modal-close" onclick="closeExpenseModal()">&times;</button>
        </div>

        <form action="<?= APP_URL ?>/expenses" method="POST">
            <input type="hidden" name="action" value="create_expense">

            <!-- Payment Method & Preset Selector -->
            <div class="form-group">
                <label>Payment Method *</label>
                <select id="expPaymentMethod" name="payment_method" class="form-control-sf" onchange="onPaymentMethodChange()" required>
                    <option value="Cash">Cash (Cash in Hand)</option>
                    <option value="Petty Cash">Petty Cash (Direct Petty Cash Ledger & Voucher)</option>
                    <option value="Bank Transfer">Bank Transfer (Bank Asset Account)</option>
                    <option value="Cheque">Cheque (Bank Account / Cheque Issued)</option>
                    <option value="Credit">Credit / Accounts Payable (Vendor Credit)</option>
                </select>
            </div>

            <!-- Double Entry COA Mappings -->
            <div style="background: var(--c-surface2); padding: 16px; border-radius: var(--r-md); border: 0.5px solid var(--c-separator); margin-bottom: 18px;">
                <h4 style="margin: 0 0 12px 0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: var(--c-orange);">
                    <i class="fa-solid fa-scale-balanced"></i> COA Double Entry Accounting Routing
                </h4>
                <div class="form-grid-2">
                    <div class="form-group" style="margin: 0;">
                        <label>Debit Account (Expense Category) *</label>
                        <select name="expense_account" class="form-control-sf" required>
                            <option value="">Select Expense Account...</option>
                            <?php foreach ($data['expense_accounts'] as $acc): ?>
                                <option value="<?= $acc->id ?>"><?= $acc->account_code ?> - <?= htmlspecialchars($acc->account_name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin: 0;">
                        <label>Credit Account (Payment Source) *</label>
                        <select id="expPaymentAccount" name="payment_account" class="form-control-sf" required>
                            <!-- Populated dynamically based on payment method selection -->
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label>Vendor / Payee</label>
                    <select name="vendor_id" class="form-control-sf">
                        <option value="">Miscellaneous / None</option>
                        <?php foreach ($data['vendors'] as $ven): ?>
                            <option value="<?= $ven->id ?>"><?= htmlspecialchars($ven->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Expense Date *</label>
                    <input type="date" name="expense_date" class="form-control-sf" value="<?= date('Y-m-d') ?>" required>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label>Reference # / Bill #</label>
                    <input type="text" name="reference" class="form-control-sf" placeholder="Auto-generated if left blank">
                </div>

                <div class="form-group">
                    <label>Amount (Rs.) *</label>
                    <input type="number" name="amount" class="form-control-sf" step="0.01" min="0.01" placeholder="0.00" required style="font-weight: 700; font-size: 16px;">
                </div>
            </div>

            <div class="form-group">
                <label>Description / Note *</label>
                <input type="text" name="description" class="form-control-sf" placeholder="e.g. Fuel purchase, Office refreshments, Utilities..." required>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px;">
                <button type="button" class="filter-btn-reset" onclick="closeExpenseModal()">Cancel</button>
                <button type="submit" class="exp-btn-primary"><i class="fa-solid fa-check"></i> Post Expense to Ledger</button>
            </div>
        </form>
    </div>
</div>

<script>
const coaAccounts = {
    cashInHand: <?= json_encode($data['cash_in_hand_acc'] ? $data['cash_in_hand_acc']->id : null) ?>,
    pettyCash: <?= json_encode($data['petty_cash_acc'] ? $data['petty_cash_acc']->id : null) ?>,
    apCredit: <?= json_encode($data['ap_credit_acc'] ? $data['ap_credit_acc']->id : null) ?>,
    bankAccounts: <?= json_encode($data['bank_accounts'] ?? []) ?>
};

function openExpenseModal() {
    document.getElementById('expenseModal').style.display = 'flex';
    onPaymentMethodChange();
}

function closeExpenseModal() {
    document.getElementById('expenseModal').style.display = 'none';
}

function onPaymentMethodChange() {
    const pMethod = document.getElementById('expPaymentMethod').value;
    const creditSelect = document.getElementById('expPaymentAccount');
    creditSelect.innerHTML = '';

    if (pMethod === 'Cash') {
        if (coaAccounts.cashInHand) {
            creditSelect.innerHTML = `<option value="${coaAccounts.cashInHand}">1000 - Cash in Hand</option>`;
        } else {
            creditSelect.innerHTML = `<option value="">Cash in Hand Account Not Configured</option>`;
        }
    } else if (pMethod === 'Petty Cash') {
        if (coaAccounts.pettyCash) {
            creditSelect.innerHTML = `<option value="${coaAccounts.pettyCash}">1020 - Petty Cash Account</option>`;
        } else {
            creditSelect.innerHTML = `<option value="">Petty Cash Account Not Configured</option>`;
        }
    } else if (pMethod === 'Credit') {
        if (coaAccounts.apCredit) {
            creditSelect.innerHTML = `<option value="${coaAccounts.apCredit}">2000 - Accounts Payable (Vendor Credit)</option>`;
        } else {
            creditSelect.innerHTML = `<option value="">Accounts Payable Account Not Configured</option>`;
        }
    } else if (pMethod === 'Bank Transfer' || pMethod === 'Cheque') {
        if (coaAccounts.bankAccounts && coaAccounts.bankAccounts.length > 0) {
            coaAccounts.bankAccounts.forEach(ba => {
                const opt = document.createElement('option');
                opt.value = ba.id;
                opt.textContent = `${ba.account_code} - ${ba.bank_name || ba.account_name} (${ba.account_number || ''})`;
                creditSelect.appendChild(opt);
            });
        } else {
            creditSelect.innerHTML = `<option value="">No Bank Accounts Found in COA</option>`;
        }
    }
}
</script>