<?php
// Custom Alert Message Resolver
$successMsg = '';
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'bank_added': $successMsg = '🏦 New Bank Account successfully registered!'; break;
        case 'bank_edited': $successMsg = '✏️ Bank Account updated successfully!'; break;
        case 'bank_deleted': $successMsg = '🗑️ Bank Account deleted successfully!'; break;
        case 'transfer_completed': $successMsg = '💸 Inter-bank fund transfer recorded successfully!'; break;
        case 'quick_entry_completed': $successMsg = '⚡ Transaction successfully posted to the ledger!'; break;
        default: $successMsg = 'Success!'; break;
    }
}
?>

<!-- Inter Font & FontAwesome Icons -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
/* ============================================================
   SF PRO + APPLE DESIGN LANGUAGE — BANKING DASHBOARD
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
    --c-blue-mid:     #b3d6ff;
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

    --dur-fast:    0.18s;
    --dur-mid:     0.28s;
    --dur-slow:    0.42s;
    --ease-spring: cubic-bezier(0.34, 1.56, 0.64, 1);
    --ease-ios:    cubic-bezier(0.25, 0.1, 0.25, 1);
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

.cust-root {
    font-family: var(--f-system);
    font-size: 15px;
    color: var(--t-primary);
    background: var(--c-bg);
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

.cust-wrap {
    max-width: 1400px;
    margin: 0 auto;
    padding: 24px 32px 100px;
}

/* ---- Bank Cards Grid ---- */
.bank-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 20px;
}
.bank-card {
    background: var(--c-surface);
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-xl);
    padding: 24px;
    box-shadow: var(--shadow-sm);
    display: flex; flex-direction: column;
    position: relative;
    transition: transform var(--dur-fast), box-shadow var(--dur-fast);
}
.bank-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.bank-card.temp-bank { border-left: 4px solid var(--c-orange); }
.bank-card.real-bank { border-left: 4px solid var(--c-blue); }

.edit-trigger {
    position: absolute; top: 20px; right: 20px;
    color: var(--t-tertiary); cursor: pointer; transition: color var(--dur-fast);
    background: transparent; border: none; font-size: 16px;
}
.edit-trigger:hover { color: var(--c-blue); }

.b-head { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px; padding-right: 25px; }
.b-title { font-size: 18px; font-weight: 700; color: var(--t-primary); line-height: 1.2; }
.b-nickname { font-size: 13px; font-weight: 500; color: var(--t-secondary); display: block; margin-top: 2px; }

/* Status Pill */
.status-pill {
    display: inline-flex; align-items: center; justify-content: center;
    padding: 4px 10px; border-radius: var(--r-pill); font-size: 10px; font-weight: 800;
    letter-spacing: 0.04em; text-transform: uppercase;
}
.status-pill.temp { background: var(--c-orange-light); color: var(--c-orange); }
.status-pill.real { background: var(--c-blue-light); color: var(--c-blue); }

.b-code { font-size: 12px; color: var(--t-label); font-weight: 600; margin-bottom: 16px; }

.b-details { font-size: 13px; color: var(--t-secondary); margin-bottom: 24px; line-height: 1.5; }
.b-details strong { color: var(--t-primary); font-weight: 600; }

.b-bal-wrap { margin-bottom: 24px; }
.b-bal-lbl { font-size: 11px; font-weight: 700; color: var(--t-label); text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 4px; }
.b-bal-val { font-size: 28px; font-weight: 800; font-family: var(--f-mono); color: var(--c-blue); letter-spacing: -0.02em; }
.bank-card.temp-bank .b-bal-val { color: var(--c-orange); }

.b-actions { display: flex; gap: 10px; margin-top: auto; }
.sf-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    padding: 10px 18px; font-size: 13.5px; font-weight: 600;
    border-radius: var(--r-sm); border: none; cursor: pointer;
    transition: all var(--dur-fast); text-decoration: none;
}
.sf-btn-primary { background: var(--c-blue); color: #fff; }
.sf-btn-primary:hover { background: #0062cc; }
.sf-btn-secondary { background: var(--c-surface); color: var(--t-primary); border: 0.5px solid var(--c-separator); box-shadow: var(--shadow-xs); }
.sf-btn-secondary:hover { background: var(--c-fill); }
.sf-btn-success { background: var(--c-green); color: #fff; }
.sf-btn-success:hover { background: #2fb350; }
.sf-btn-danger { background: var(--c-red); color: #fff; }
.sf-btn-danger:hover { background: #e6352b; }
.b-actions .sf-btn { flex: 1; justify-content: center; font-size: 13px; padding: 8px 12px; }

/* ---- Alerts ---- */
.sf-alert { padding: 14px 18px; border-radius: var(--r-md); margin-bottom: 24px; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 10px; }
.sf-alert.success { background: var(--c-green-light); color: #1e4620; border: 1px solid rgba(52,199,89,0.3); }
.sf-alert.error { background: var(--c-red-light); color: #6e1a15; border: 1px solid rgba(255,59,48,0.3); }

/* ---- Command Bar (Dynamic Island style) ---- */
.cmd-bar {
    position: fixed;
    bottom: 28px; left: 50%;
    transform: translateX(-50%);
    background: rgba(28, 28, 30, 0.92);
    backdrop-filter: saturate(180%) blur(28px);
    -webkit-backdrop-filter: saturate(180%) blur(28px);
    border: 0.5px solid rgba(255,255,255,0.12);
    border-radius: var(--r-pill);
    padding: 7px 10px;
    display: flex; align-items: center; gap: 4px;
    box-shadow: var(--shadow-xl), 0 0 0 0.5px rgba(0,0,0,0.3);
    z-index: 100;
}
.cmd-search {
    display: flex; align-items: center; gap: 9px;
    background: rgba(255,255,255,0.1);
    border-radius: var(--r-pill);
    padding: 8px 14px;
    width: 196px;
    transition: width var(--dur-slow) var(--ease-ios),
                background var(--dur-mid);
}
.cmd-search:focus-within {
    width: 300px;
    background: rgba(255,255,255,0.18);
}
.cmd-search i { color: rgba(255,255,255,0.55); font-size: 14px; flex-shrink: 0; }
.cmd-search input {
    background: transparent; border: none; outline: none;
    color: #fff; font-size: 14px; font-weight: 500;
    font-family: var(--f-system); width: 100%;
}
.cmd-search input::placeholder { color: rgba(255,255,255,0.45); }
.cmd-divider { width: 0.5px; height: 22px; background: rgba(255,255,255,0.15); margin: 0 3px; }
.cmd-icon {
    width: 38px; height: 38px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: rgba(255,255,255,0.8); font-size: 15px;
    background: transparent; border: none; cursor: pointer; text-decoration: none;
    transition: background var(--dur-fast);
}
.cmd-icon:hover { background: rgba(255,255,255,0.12); color: #fff; }
.cmd-cta {
    display: flex; align-items: center; gap: 7px;
    background: #fff; color: #1c1c1e;
    border: none; border-radius: var(--r-pill);
    padding: 0 18px; height: 38px;
    font-size: 14px; font-weight: 700;
    font-family: var(--f-system);
    cursor: pointer; text-decoration: none;
    transition: transform var(--dur-fast) var(--ease-spring),
                background var(--dur-fast);
    margin-left: 2px;
}
.cmd-cta:hover { background: #e5e5ea; transform: scale(0.97); }

/* ---- Modals ---- */
.modal-backdrop {
    display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 2000; align-items: center; justify-content: center;
}
.modal-panel {
    background: var(--c-surface); width: 100%; max-width: 550px; border-radius: var(--r-xl);
    box-shadow: var(--shadow-xl); border: 0.5px solid var(--c-separator); overflow: hidden;
    display: flex; flex-direction: column; animation: modalFadeIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes modalFadeIn { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }

.modal-header { padding: 20px 24px; background: var(--c-surface2); border-bottom: 0.5px solid var(--c-separator); display: flex; justify-content: space-between; align-items: center; }
.modal-header h3 { margin: 0; font-size: 18px; font-weight: 700; color: var(--t-primary); }
.modal-close { background: transparent; border: none; font-size: 18px; color: var(--t-tertiary); cursor: pointer; transition: color var(--dur-fast); padding: 0; }
.modal-close:hover { color: var(--t-primary); }

.modal-body { padding: 24px; display: flex; flex-direction: column; gap: 20px; max-height: 60vh; overflow-y: auto; }
.sf-form-group { display: flex; flex-direction: column; gap: 6px; }
.sf-form-group label { font-size: 11px; font-weight: 700; color: var(--t-label); text-transform: uppercase; letter-spacing: 0.04em; }
.sf-input, .sf-select {
    padding: 10px 14px; font-family: var(--f-system); font-size: 14px; color: var(--t-primary);
    background: var(--c-bg); border: 1px solid var(--c-separator); border-radius: var(--r-sm);
    outline: none; transition: border-color var(--dur-fast), box-shadow var(--dur-fast); width: 100%; box-sizing: border-box;
}
.sf-input:focus, .sf-select:focus { border-color: var(--c-blue); box-shadow: 0 0 0 3px rgba(0,122,255,0.12); }

.modal-footer {
    padding: 20px 24px; background: var(--c-surface2); border-top: 0.5px solid var(--c-separator);
    display: flex; justify-content: flex-end; gap: 12px;
}
.grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.note-box { font-size: 12px; color: var(--t-secondary); padding: 12px; background: var(--c-bg); border-radius: var(--r-sm); border: 0.5px solid var(--c-separator); }
.td-mono { font-family: var(--f-mono); font-weight: 600; }
</style>

<div class="cust-root">
    <div class="cust-wrap">

        <!-- Feedback Alerts -->
        <?php if(!empty($successMsg)): ?>
            <div class="sf-alert success">
                <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($successMsg) ?>
            </div>
        <?php endif; ?>
        <?php if(!empty($data['error'])): ?>
            <div class="sf-alert error">
                <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($data['error']) ?>
            </div>
        <?php endif; ?>

        <!-- Bank Accounts Grid -->
        <div class="bank-grid" id="bankGrid">
            <?php if(empty($data['bank_accounts'])): ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 60px; color: var(--t-tertiary); border: 1.5px dashed var(--c-separator); border-radius: var(--r-xl); background: var(--c-surface);">
                    <i class="fa-solid fa-building-columns" style="font-size: 40px; margin-bottom: 12px;"></i>
                    <h3 style="margin:0 0 8px 0; color: var(--t-primary); font-size: 18px;">No bank accounts registered</h3>
                    <p style="margin:0; font-size: 14px;">Click "+ Add Bank" below to register a new account.</p>
                </div>
            <?php else: foreach($data['bank_accounts'] as $acc): ?>
                <?php 
                    $isTemp = $acc->account_code === '1605';
                    $cardClass = $isTemp ? 'temp-bank' : 'real-bank';
                    $tagText = $isTemp ? 'Clearing / Temp' : 'Bank Sub-ledger';
                    $tagClass = $isTemp ? 'temp' : 'real';
                    
                    // Search terms
                    $searchTerms = strtolower($acc->bank_name . ' ' . $acc->account_name . ' ' . $acc->account_code . ' ' . $acc->account_number . ' ' . $acc->branch_name);
                ?>
                <div class="bank-card <?= $cardClass ?> bank-card-item" data-search="<?= htmlspecialchars($searchTerms, ENT_QUOTES) ?>">
                    <?php if (!$isTemp): ?>
                        <button class="edit-trigger" onclick="openEditBankModal(<?= $acc->id ?>, '<?= htmlspecialchars($acc->account_name, ENT_QUOTES) ?>')" title="Edit Bank Account"><i class="fa-solid fa-pen-to-square"></i></button>
                    <?php endif; ?>
                    
                    <div class="b-head">
                        <div>
                            <span class="status-pill <?= $tagClass ?>"><?= $tagText ?></span>
                        </div>
                    </div>

                    <div class="b-title">
                        <?= htmlspecialchars($acc->bank_name ?? $acc->account_name) ?>
                        <?php if (!empty($acc->nickname)): ?>
                            <span class="b-nickname">"<?= htmlspecialchars($acc->nickname) ?>"</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="b-code">GL: <?= htmlspecialchars($acc->account_code) ?></div>
                    
                    <div class="b-details">
                        <?php if (!empty($acc->account_number)): ?>
                            <div><i class="fa-regular fa-credit-card" style="width: 16px; text-align: center;"></i> <strong><?= htmlspecialchars($acc->account_number) ?></strong> (<?= htmlspecialchars($acc->account_type) ?>)</div>
                            <div><i class="fa-solid fa-location-dot" style="width: 16px; text-align: center;"></i> <?= htmlspecialchars($acc->branch_name) ?> <?= !empty($acc->branch_code) ? '(' . htmlspecialchars($acc->branch_code) . ')' : '' ?> | <?= htmlspecialchars($acc->currency) ?></div>
                        <?php else: ?>
                            <div>System Clearing Account</div>
                            <div>&nbsp;</div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="b-bal-wrap">
                        <div class="b-bal-lbl">Current Ledger Balance</div>
                        <div class="b-bal-val">Rs <?= number_format($acc->balance, 2) ?></div>
                    </div>
                    
                    <div class="b-actions">
                        <a href="<?= APP_URL ?>/banking/ledger/<?= $acc->id ?>" class="sf-btn sf-btn-secondary"><i class="fa-solid fa-list"></i> Register</a>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>

    </div>
</div>

<!-- Command Bar -->
<div class="cmd-bar">
    <div class="cmd-search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="searchInput" oninput="searchBanks()" placeholder="Search banks...">
    </div>
    <div class="cmd-divider"></div>
    <button type="button" onclick="openTransferModal()" class="cmd-icon" title="Inter-Bank Transfer"><i class="fa-solid fa-money-bill-transfer"></i></button>
    <button type="button" onclick="openTransactionModal('withdrawal')" class="cmd-icon" title="Record Withdrawal"><i class="fa-solid fa-arrow-up-long"></i></button>
    <button type="button" onclick="openAddBankModal()" class="cmd-cta"><i class="fa-solid fa-plus" style="font-size:13px;"></i> Add Bank</button>
</div>

<!-- MODAL 1: Add Bank Account -->
<div class="modal-backdrop" id="addBankModal">
    <div class="modal-panel">
        <div class="modal-header">
            <h3><i class="fa-solid fa-building-columns"></i> Add Bank Account</h3>
            <button type="button" class="modal-close" onclick="closeModal('addBankModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="<?= APP_URL ?>/banking" method="POST">
            <input type="hidden" name="action" value="add_bank">
            <div class="modal-body">
                <div class="grid-2">
                    <div class="sf-form-group">
                        <label>Bank Name *</label>
                        <input type="text" name="bank_name" class="sf-input" placeholder="e.g. Commercial Bank" required>
                    </div>
                    <div class="sf-form-group">
                        <label>Branch Name *</label>
                        <input type="text" name="branch_name" class="sf-input" placeholder="e.g. Colombo Fort" required>
                    </div>
                </div>
                
                <div class="grid-2">
                    <div class="sf-form-group">
                        <label>Branch Code (Optional)</label>
                        <input type="text" name="branch_code" class="sf-input" placeholder="e.g. 018">
                    </div>
                    <div class="sf-form-group">
                        <label>Account Number *</label>
                        <input type="text" name="account_number" class="sf-input" placeholder="e.g. 100293810" required>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="sf-form-group">
                        <label>Account Holder Name *</label>
                        <input type="text" name="account_holder_name" class="sf-input" placeholder="e.g. Curtiss Enterprises (Pvt) Ltd" required>
                    </div>
                    <div class="sf-form-group">
                        <label>Nickname</label>
                        <input type="text" name="nickname" class="sf-input" placeholder="e.g. Primary Operating Account">
                    </div>
                </div>

                <div class="grid-2">
                    <div class="sf-form-group">
                        <label>Account Type *</label>
                        <select name="account_type" class="sf-select" required>
                            <option value="Current">Current</option>
                            <option value="Savings">Savings</option>
                            <option value="Personal">Personal</option>
                            <option value="Company">Company</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="sf-form-group">
                        <label>Currency *</label>
                        <input type="text" name="currency" class="sf-input" value="LKR" placeholder="e.g. LKR" required>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="sf-form-group">
                        <label>Opening Balance *</label>
                        <input type="number" name="opening_balance" class="sf-input" step="0.01" value="0.00" required>
                    </div>
                    <div class="sf-form-group">
                        <label>Opening Balance Date *</label>
                        <input type="date" name="opening_balance_date" class="sf-input" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
                
                <div class="sf-form-group">
                    <label>Account Status *</label>
                    <select name="status" class="sf-select" required>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
                
                <div class="note-box">
                    <i class="fa-solid fa-circle-info"></i> <strong>Accounting Note:</strong> An opening balance journal entry will be automatically posted to debit this bank ledger account and credit <strong>3000 - Owner Capital / Equity</strong>.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="sf-btn sf-btn-secondary" onclick="closeModal('addBankModal')">Cancel</button>
                <button type="submit" class="sf-btn sf-btn-primary">Add Bank Account</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 2: Edit Bank Account -->
<div class="modal-backdrop" id="editBankModal">
    <div class="modal-panel">
        <div class="modal-header">
            <h3><i class="fa-solid fa-pen-to-square"></i> Edit Bank Account</h3>
            <button type="button" class="modal-close" onclick="closeModal('editBankModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="<?= APP_URL ?>/banking" method="POST">
            <input type="hidden" name="action" value="edit_bank">
            <input type="hidden" name="bank_id" id="editBankId">
            <div class="modal-body">
                <div class="grid-2">
                    <div class="sf-form-group">
                        <label>Bank Name *</label>
                        <input type="text" name="bank_name" id="editBankName" class="sf-input" required>
                    </div>
                    <div class="sf-form-group">
                        <label>Branch Name *</label>
                        <input type="text" name="branch_name" id="editBranchName" class="sf-input" required>
                    </div>
                </div>
                
                <div class="grid-2">
                    <div class="sf-form-group">
                        <label>Branch Code (Optional)</label>
                        <input type="text" name="branch_code" id="editBranchCode" class="sf-input">
                    </div>
                    <div class="sf-form-group">
                        <label>Account Number *</label>
                        <input type="text" name="account_number" id="editAccountNumber" class="sf-input" required>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="sf-form-group">
                        <label>Account Holder Name *</label>
                        <input type="text" name="account_holder_name" id="editHolderName" class="sf-input" required>
                    </div>
                    <div class="sf-form-group">
                        <label>Nickname</label>
                        <input type="text" name="nickname" id="editNickname" class="sf-input" placeholder="e.g. Primary Operating Account">
                    </div>
                </div>

                <div class="grid-2">
                    <div class="sf-form-group">
                        <label>Account Type *</label>
                        <select name="account_type" id="editAccountType" class="sf-select" required>
                            <option value="Current">Current</option>
                            <option value="Savings">Savings</option>
                            <option value="Personal">Personal</option>
                            <option value="Company">Company</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="sf-form-group">
                        <label>Currency *</label>
                        <input type="text" name="currency" id="editCurrency" class="sf-input" required>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="sf-form-group">
                        <label>Opening Balance Date *</label>
                        <input type="date" name="opening_balance_date" id="editOpeningDate" class="sf-input" required>
                    </div>
                    <div class="sf-form-group">
                        <label>Account Status *</label>
                        <select name="status" id="editStatus" class="sf-select" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="justify-content: space-between;">
                <button type="button" class="sf-btn sf-btn-danger" onclick="openDeleteConfirmation()"><i class="fa-solid fa-trash"></i> Delete Bank</button>
                <div style="display: flex; gap: 8px;">
                    <button type="button" class="sf-btn sf-btn-secondary" onclick="closeModal('editBankModal')">Cancel</button>
                    <button type="submit" class="sf-btn sf-btn-primary">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Delete Bank Account Confirmation -->
<div class="modal-backdrop" id="deleteBankModal">
    <div class="modal-panel" style="max-width: 420px;">
        <div class="modal-header" style="background: var(--c-red-light); border-bottom-color: rgba(255,59,48,0.2);">
            <h3 style="color: var(--c-red);"><i class="fa-solid fa-triangle-exclamation"></i> Confirm Deletion</h3>
            <button type="button" class="modal-close" onclick="closeModal('deleteBankModal')" style="color: var(--c-red);"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="<?= APP_URL ?>/banking" method="POST">
            <input type="hidden" name="action" value="delete_bank">
            <input type="hidden" name="bank_id" id="deleteBankIdInput">
            <div class="modal-body">
                <p style="margin-top: 0; font-size: 14px; line-height: 1.5; color: var(--c-red); font-weight: 500;">
                    Warning: Deleting a bank account requires administrator/user verification. It will also ensure there are no transactions or active linkages.
                </p>
                <div class="sf-form-group">
                    <label style="color: var(--c-red);">Enter Password to Verify Identity *</label>
                    <input type="password" name="confirm_password" class="sf-input" placeholder="Verify password to confirm" required style="border-color: rgba(255,59,48,0.3);">
                </div>
            </div>
            <div class="modal-footer" style="background: var(--c-surface);">
                <button type="button" class="sf-btn sf-btn-secondary" onclick="closeModal('deleteBankModal')">Cancel</button>
                <button type="submit" class="sf-btn sf-btn-danger">Verify & Delete</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 3: Inter-bank Fund Transfer -->
<div class="modal-backdrop" id="transferModal">
    <div class="modal-panel">
        <div class="modal-header">
            <h3><i class="fa-solid fa-money-bill-transfer"></i> Inter-Bank Fund Transfer</h3>
            <button type="button" class="modal-close" onclick="closeModal('transferModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="<?= APP_URL ?>/banking" method="POST">
            <input type="hidden" name="action" value="transfer_funds">
            <div class="modal-body">
                <div class="sf-form-group">
                    <label>Transfer Date *</label>
                    <input type="date" name="entry_date" class="sf-input" value="<?= date('Y-m-d') ?>" required>
                </div>
                
                <div class="sf-form-group">
                    <label>From Bank Account (Source - Credits account) *</label>
                    <select name="from_account_id" class="sf-select" required>
                        <option value="">-- Select Source Bank --</option>
                        <?php foreach($data['bank_accounts'] as $acc): ?>
                            <option value="<?= $acc->id ?>"><?= htmlspecialchars($acc->account_name) ?> (Rs: <?= number_format($acc->balance, 2) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="sf-form-group">
                    <label>To Bank Account (Destination - Debits account) *</label>
                    <select name="to_account_id" class="sf-select" required>
                        <option value="">-- Select Destination Bank --</option>
                        <?php foreach($data['bank_accounts'] as $acc): ?>
                            <option value="<?= $acc->id ?>"><?= htmlspecialchars($acc->account_name) ?> (Rs: <?= number_format($acc->balance, 2) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="sf-form-group">
                    <label>Transfer Amount (Rs:) *</label>
                    <input type="number" name="amount" class="sf-input td-mono" step="0.01" min="0.01" placeholder="0.00" required style="font-size: 18px; color: var(--c-blue);">
                </div>
                
                <div class="sf-form-group">
                    <label>Memo / Reference *</label>
                    <input type="text" name="description" class="sf-input" placeholder="e.g. Weekly Cash Liquidity Sweep" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="sf-btn sf-btn-secondary" onclick="closeModal('transferModal')">Cancel</button>
                <button type="submit" class="sf-btn sf-btn-primary">Record Transfer</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 4: Record Deposit / Withdrawal (Quick Entries) -->
<div class="modal-backdrop" id="transactionModal">
    <div class="modal-panel">
        <div class="modal-header" id="transModalHeader">
            <h3 id="transModalTitle"><i class="fa-solid fa-money-check-dollar"></i> Record Transaction</h3>
            <button type="button" class="modal-close" onclick="closeModal('transactionModal')" id="transModalClose"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="<?= APP_URL ?>/banking" method="POST">
            <input type="hidden" name="action" value="quick_entry">
            <input type="hidden" name="type" id="transType" value="deposit">
            
            <div class="modal-body">
                <div class="sf-form-group">
                    <label>Transaction Date *</label>
                    <input type="date" name="entry_date" class="sf-input" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="sf-form-group">
                    <label id="transBankLabel">Bank Account *</label>
                    <select name="bank_account_id" class="sf-select" required>
                        <?php foreach($data['bank_accounts'] as $acc): ?>
                            <option value="<?= $acc->id ?>"><?= htmlspecialchars($acc->account_name) ?> (Rs: <?= number_format($acc->balance, 2) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="sf-form-group">
                    <label id="transOffsetLabel">Ledger Double-Entry Offset Account *</label>
                    <select name="offset_account_id" class="sf-select" required>
                        <option value="">-- Select Offset Account --</option>
                        <?php 
                        $db = new Database();
                        $db->query("SELECT * FROM chart_of_accounts ORDER BY account_type, account_name");
                        $allAccs = $db->resultSet();
                        foreach($allAccs as $acc): 
                        ?>
                            <option value="<?= $acc->id ?>"><?= $acc->account_code ?> - <?= htmlspecialchars($acc->account_name) ?> (<?= $acc->account_type ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="sf-form-group">
                    <label>Amount (Rs:) *</label>
                    <input type="number" name="amount" class="sf-input td-mono" step="0.01" min="0.01" placeholder="0.00" required style="font-size: 18px;" id="transAmountInput">
                </div>

                <div class="sf-form-group">
                    <label>Memo / Reference *</label>
                    <input type="text" name="description" class="sf-input" placeholder="e.g. Sales Collection Offset" required>
                </div>
            </div>
            
            <div class="modal-footer" id="transModalFooter">
                <button type="button" class="sf-btn sf-btn-secondary" onclick="closeModal('transactionModal')">Cancel</button>
                <button type="submit" class="sf-btn sf-btn-primary" id="transSubmitBtn">Save Transaction</button>
            </div>
        </form>
    </div>
</div>

<script>
    function searchBanks() {
        const input = document.getElementById('searchInput').value.toLowerCase();
        const cards = document.querySelectorAll('.bank-card-item');
        
        cards.forEach(card => {
            const terms = card.getAttribute('data-search') || '';
            if (terms.includes(input)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function openModal(id) {
        document.getElementById(id).style.display = 'flex';
    }
    
    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }
    
    function openAddBankModal() {
        openModal('addBankModal');
    }
    
    function openEditBankModal(id, name) {
        document.getElementById('editBankId').value = id;
        
        // Fetch details from the API
        fetch('<?= APP_URL ?>/banking/get_bank_details_json/' + id)
            .then(response => response.json())
            .then(data => {
                if (data && data.bank_name) {
                    document.getElementById('editBankName').value = data.bank_name;
                    document.getElementById('editBranchName').value = data.branch_name;
                    document.getElementById('editBranchCode').value = data.branch_code || '';
                    document.getElementById('editHolderName').value = data.account_holder_name;
                    document.getElementById('editAccountNumber').value = data.account_number;
                    document.getElementById('editNickname').value = data.nickname || '';
                    document.getElementById('editAccountType').value = data.account_type;
                    document.getElementById('editCurrency').value = data.currency || 'LKR';
                    document.getElementById('editOpeningDate').value = data.opening_balance_date;
                    document.getElementById('editStatus').value = data.bank_status || 'Active';
                }
            })
            .catch(err => {
                console.error("Error loading bank details:", err);
            });
            
        openModal('editBankModal');
    }
    
    function openDeleteConfirmation() {
        const bankId = document.getElementById('editBankId').value;
        document.getElementById('deleteBankIdInput').value = bankId;
        closeModal('editBankModal');
        openModal('deleteBankModal');
    }
    
    function openTransferModal() {
        openModal('transferModal');
    }
    
    function openTransactionModal(type) {
        document.getElementById('transType').value = type;
        const header = document.getElementById('transModalHeader');
        const title = document.getElementById('transModalTitle');
        const closeBtn = document.getElementById('transModalClose');
        const bankLabel = document.getElementById('transBankLabel');
        const offsetLabel = document.getElementById('transOffsetLabel');
        const submitBtn = document.getElementById('transSubmitBtn');
        const amountInput = document.getElementById('transAmountInput');
        const footer = document.getElementById('transModalFooter');
        
        if (type === 'deposit') {
            header.style.background = 'var(--c-green-light)';
            header.style.borderBottomColor = 'rgba(52,199,89,0.2)';
            title.style.color = 'var(--c-green)';
            title.innerHTML = '<i class="fa-solid fa-money-bill-wave"></i> Record Deposit';
            closeBtn.style.color = 'var(--c-green)';
            
            bankLabel.innerText = 'Bank Account (Receiving Funds) *';
            offsetLabel.innerText = 'From Account (Source of Funds / Revenue Offset) *';
            
            amountInput.style.color = 'var(--c-green)';
            submitBtn.className = 'sf-btn sf-btn-success';
            submitBtn.innerText = 'Record Deposit';
        } else {
            header.style.background = 'var(--c-red-light)';
            header.style.borderBottomColor = 'rgba(255,59,48,0.2)';
            title.style.color = 'var(--c-red)';
            title.innerHTML = '<i class="fa-solid fa-hand-holding-dollar"></i> Record Withdrawal';
            closeBtn.style.color = 'var(--c-red)';
            
            bankLabel.innerText = 'Bank Account (Withdrawing From) *';
            offsetLabel.innerText = 'To Account (Expense / Offset Paid) *';
            
            amountInput.style.color = 'var(--c-red)';
            submitBtn.className = 'sf-btn sf-btn-danger';
            submitBtn.innerText = 'Record Withdrawal';
        }
        
        openModal('transactionModal');
    }
</script>