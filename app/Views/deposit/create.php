<!-- Inter Font & FontAwesome Icons -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<?php
$isEdit = !empty($data['deposit']);
$dep = $data['deposit'] ?? null;
$actionUrl = $isEdit ? APP_URL . '/deposit/update/' . $dep->id : APP_URL . '/deposit/store';
?>

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

@media (prefers-color-scheme: dark) {
    :root {
        --c-bg:           #121212;
        --c-surface:      #1e1e2e;
        --c-surface2:     #161622;
        --c-fill:         rgba(255,255,255,0.08);
        --c-fill2:        rgba(255,255,255,0.12);
        --c-separator:    rgba(255,255,255,0.15);
        --t-primary:   #f5f5f7;
        --t-secondary: #a1a1aa;
        --t-label:     #52525b;
    }
}

.create-container {
    font-family: var(--f-system);
    font-size: 14px;
    color: var(--t-primary);
    -webkit-font-smoothing: antialiased;
    padding-bottom: 100px; /* space for sticky bar */
}

/* Back Link */
.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--t-secondary);
    text-decoration: none;
    font-weight: 600;
    font-size: 13px;
    margin-bottom: 16px;
    transition: color 0.18s ease;
}
.back-link:hover { color: var(--c-blue); }

/* Header */
.page-title-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}
.page-title-row h2 {
    font-size: 24px;
    font-weight: 800;
    margin: 0;
    letter-spacing: -0.02em;
}

/* 2-Column Grid */
.grid-layout {
    display: grid;
    grid-template-columns: 480px 1fr;
    gap: 24px;
}
@media (max-width: 1024px) {
    .grid-layout { grid-template-columns: 1fr; }
}

/* Form Card */
.form-card {
    background: var(--c-surface);
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-lg);
    padding: 24px;
    box-shadow: var(--shadow-sm);
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.card-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--t-primary);
    border-bottom: 0.5px solid var(--c-separator);
    padding-bottom: 14px;
    margin: 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.form-group label {
    font-weight: 700;
    font-size: 11px;
    text-transform: uppercase;
    color: var(--t-secondary);
    letter-spacing: 0.05em;
}
.form-group input, .form-group select {
    padding: 10px 14px;
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-sm);
    font-size: 14px;
    background: var(--c-surface2);
    color: inherit;
    box-sizing: border-box;
    width: 100%;
    outline: none;
    transition: border-color 0.18s ease;
}
.form-group input:focus, .form-group select:focus {
    border-color: var(--c-blue);
    background: var(--c-surface);
    box-shadow: 0 0 0 3px var(--c-blue-light);
}

/* Denominations Table */
.denom-table { width: 100%; border-collapse: collapse; }
.denom-table td { padding: 10px 0; border-bottom: 0.5px dashed var(--c-separator); }
.denom-label { font-weight: 700; font-family: var(--f-mono); font-size: 14px; width: 90px; color: var(--t-primary); }
.denom-input { width: 85px !important; text-align: center; padding: 6px 8px !important; font-weight: 600; font-family: var(--f-mono); }
.denom-subtotal { font-family: var(--f-mono); font-size: 14px; text-align: right; font-weight: 600; width: 130px; color: var(--t-secondary); }

/* Cheque List Controls */
.cheque-search-bar {
    display: flex;
    gap: 12px;
    align-items: center;
    margin-bottom: 12px;
}
.cheque-search-input {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 8px;
    background: var(--c-surface2);
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-pill);
    padding: 8px 14px;
}
.cheque-search-input i { color: var(--t-label); font-size: 13px; }
.cheque-search-input input {
    border: none;
    outline: none;
    background: transparent;
    font-size: 13px;
    color: inherit;
    width: 100%;
}

.cheque-list {
    max-height: 480px;
    overflow-y: auto;
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-md);
    background: var(--c-surface);
}

.cheque-table { width: 100%; border-collapse: collapse; text-align: left; }
.cheque-table th, .cheque-table td { padding: 12px 14px; border-bottom: 0.5px solid var(--c-separator); font-size: 13px; }
.cheque-table th { background: var(--c-surface2); font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--t-secondary); letter-spacing: 0.05em; sticky: top; }
.cheque-table tr:hover td { background: var(--c-fill); }

.chk-select-btn {
    font-size: 12px;
    font-weight: 600;
    color: var(--c-blue);
    background: var(--c-blue-light);
    border: none;
    padding: 5px 10px;
    border-radius: var(--r-pill);
    cursor: pointer;
    transition: opacity 0.18s ease;
}
.chk-select-btn:hover { opacity: 0.8; }

/* Sticky Glassmorphic Bottom Bar */
.sticky-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: saturate(180%) blur(20px);
    -webkit-backdrop-filter: saturate(180%) blur(20px);
    border-top: 0.5px solid var(--c-separator);
    padding: 16px 32px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 1000;
    box-shadow: 0 -10px 30px rgba(0,0,0,0.08);
}
@media (prefers-color-scheme: dark) {
    .sticky-bar {
        background: rgba(26, 26, 38, 0.88);
        border-top-color: rgba(255, 255, 255, 0.12);
    }
}

.summary-group {
    display: flex;
    gap: 36px;
    align-items: center;
}
.summary-item {
    display: flex;
    flex-direction: column;
}
.summary-label { font-size: 10px; font-weight: 800; text-transform: uppercase; color: var(--t-label); letter-spacing: 0.05em; }
.summary-val { font-size: 22px; font-weight: 800; font-family: var(--f-mono); }
.summary-val.cash { color: var(--c-blue); }
.summary-val.cheque { color: var(--c-orange); }
.summary-val.total { color: var(--c-green); }

.btn-group { display: flex; gap: 12px; }

.btn-sf {
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 600;
    border-radius: var(--r-pill);
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: all 0.18s ease;
}
.btn-sf-primary { background: var(--c-blue); color: #fff; }
.btn-sf-primary:hover { opacity: 0.9; }
.btn-sf-success { background: var(--c-green); color: #fff; }
.btn-sf-success:hover { opacity: 0.9; }
.btn-sf-secondary { background: var(--c-fill); color: var(--t-primary); }
.btn-sf-secondary:hover { background: var(--c-fill2); }
</style>

<div class="create-container">
    <a href="<?= APP_URL ?>/deposit" class="back-link">
        <i class="fa-solid fa-arrow-left"></i> Back to Deposits Workflow
    </a>

    <div class="page-title-row">
        <h2><?= $isEdit ? 'Edit Bank Deposit Draft' : 'Prepare New Bank Deposit' ?></h2>
    </div>

    <?php if(!empty($data['error'])): ?>
        <div style="padding: 14px 18px; background: var(--c-red-light); color: var(--c-red); border: 0.5px solid rgba(255,59,48,0.3); border-radius: var(--r-md); margin-bottom: 24px; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($data['error']) ?>
        </div>
    <?php endif; ?>

    <form action="<?= $actionUrl ?>" method="POST" id="depositForm">
        <!-- CRITICAL: CSRF Token injection -->
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

        <div class="grid-layout">
            <!-- Left: Settings & Cash Denominations -->
            <div style="display: flex; flex-direction: column; gap: 24px;">
                <!-- Header Settings -->
                <div class="form-card">
                    <h3 class="card-title">
                        <span><i class="fa-solid fa-building-columns" style="color: var(--c-blue); margin-right: 8px;"></i> Deposit Settings</span>
                    </h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label>Deposit Date *</label>
                            <input type="date" name="deposit_date" value="<?= $isEdit ? htmlspecialchars($dep->deposit_date) : date('Y-m-d') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Destination Bank Account *</label>
                            <select name="destination_bank_account_id" required>
                                <option value="">-- Select Bank Account --</option>
                                <?php foreach($data['bank_accounts'] as $acc): ?>
                                    <option value="<?= $acc->id ?>" <?= ($isEdit && $dep->destination_bank_account_id == $acc->id) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($acc->account_name) ?> (<?= htmlspecialchars($acc->account_code) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Cash Denominations Breakdown -->
                <div class="form-card">
                    <h3 class="card-title">
                        <span><i class="fa-solid fa-money-bill-wave" style="color: var(--c-green); margin-right: 8px;"></i> Cash Denominations</span>
                    </h3>
                    <table class="denom-table">
                        <?php 
                        $denoms = [5000, 2000, 1000, 500, 100, 50, 20];
                        foreach ($denoms as $d):
                            $field = "cash_" . $d;
                            $val = $isEdit ? intval($dep->$field) : 0;
                        ?>
                            <tr>
                                <td class="denom-label">Rs: <?= $d ?></td>
                                <td style="text-align: center; width: 30px; color: var(--t-label);">✕</td>
                                <td style="width: 100px;">
                                    <input type="number" name="cash_<?= $d ?>" id="c_<?= $d ?>" class="denom-input form-group input" value="<?= $val ?>" min="0" oninput="calculateCash()">
                                </td>
                                <td class="denom-subtotal">Rs <span id="sub_<?= $d ?>">0.00</span></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr style="font-weight: 800; font-size: 15px;">
                            <td colspan="3" style="padding-top: 16px; border-bottom: none; color: var(--t-primary);">Total Cash Deposit</td>
                            <td style="padding-top: 16px; text-align: right; color: var(--c-blue); font-family: var(--f-mono); border-bottom: none;">Rs <span id="cash_total_txt">0.00</span></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Right: Cheque Selection -->
            <div class="form-card">
                <h3 class="card-title">
                    <span><i class="fa-solid fa-money-check-dollar" style="color: var(--c-orange); margin-right: 8px;"></i> Select Cheques to Deposit</span>
                </h3>

                <!-- Cheque Search & Select All Controls -->
                <div class="cheque-search-bar">
                    <div class="cheque-search-input">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="chequeSearchInput" placeholder="Search cheque #, customer, bank, date..." onkeyup="filterChequesList()">
                    </div>
                    <button type="button" class="chk-select-btn" onclick="toggleSelectAllCheques(true)">Select All</button>
                    <button type="button" class="chk-select-btn" style="color: var(--t-secondary); background: var(--c-fill);" onclick="toggleSelectAllCheques(false)">Clear</button>
                </div>
                
                <div class="cheque-list">
                    <table class="cheque-table" id="chequesTable">
                        <thead>
                            <tr>
                                <th style="width: 40px; text-align: center;">✓</th>
                                <th>Cheque Detail</th>
                                <th>Customer</th>
                                <th style="text-align: right;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Display cheques currently in this edit deposit -->
                            <?php 
                            $chequeIdsInDeposit = [];
                            if ($isEdit && !empty($data['deposit_cheques'])): 
                                foreach ($data['deposit_cheques'] as $item):
                                    $chequeIdsInDeposit[] = $item->cheque_id;
                            ?>
                                <tr class="cheque-row" style="background: var(--c-blue-light);">
                                    <td style="text-align: center;">
                                        <input type="checkbox" name="cheques[]" value="<?= $item->cheque_id ?>" data-amount="<?= $item->cheque_amount ?>" checked onchange="calculateCheques()">
                                    </td>
                                    <td>
                                        <strong style="color: var(--c-blue);">No: <?= htmlspecialchars($item->cheque_number) ?></strong><br>
                                        <span style="font-size: 11px; color: var(--t-secondary);"><?= htmlspecialchars($item->bank_name) ?> | <?= date('Y-m-d', strtotime($item->banking_date)) ?></span>
                                    </td>
                                    <td style="font-weight: 600; color: var(--t-primary);"><?= htmlspecialchars($item->customer_name) ?></td>
                                    <td style="text-align: right; font-weight: 700; font-family: var(--f-mono);">Rs <?= number_format($item->cheque_amount, 2) ?></td>
                                </tr>
                            <?php 
                                endforeach;
                            endif; 
                            ?>

                            <!-- Display other pending cheques -->
                            <?php 
                            $hasPending = false;
                            foreach($data['pending_cheques'] as $chk): 
                                // Skip if already rendered above in edit mode
                                if (in_array($chk->id, $chequeIdsInDeposit)) continue;
                                $hasPending = true;
                            ?>
                                <tr class="cheque-row">
                                    <td style="text-align: center;">
                                        <input type="checkbox" name="cheques[]" value="<?= $chk->id ?>" data-amount="<?= $chk->amount ?>" onchange="calculateCheques()">
                                    </td>
                                    <td>
                                        <strong>No: <?= htmlspecialchars($chk->cheque_number) ?></strong><br>
                                        <span style="font-size: 11px; color: var(--t-secondary);"><?= htmlspecialchars($chk->bank_name) ?> | <?= date('Y-m-d', strtotime($chk->banking_date)) ?></span>
                                    </td>
                                    <td style="font-weight: 600; color: var(--t-primary);"><?= htmlspecialchars($chk->customer_name) ?></td>
                                    <td style="text-align: right; font-weight: 700; font-family: var(--f-mono);">Rs <?= number_format($chk->amount, 2) ?></td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if(!$hasPending && empty($data['deposit_cheques'])): ?>
                                <tr class="no-cheques-row">
                                    <td colspan="4" style="text-align: center; color: var(--t-label); padding: 40px;">
                                        <i class="fa-solid fa-money-check" style="font-size: 32px; opacity: 0.3; margin-bottom: 8px; display: block;"></i>
                                        No pending cheques available in ledger.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div style="display: flex; justify-content: space-between; font-weight: 800; font-size: 15px; margin-top: auto; padding-top: 16px; border-top: 0.5px solid var(--c-separator);">
                    <span>Selected Cheques Total (<span id="chk_count">0</span>)</span>
                    <span style="color: var(--c-orange); font-family: var(--f-mono);">Rs <span id="cheque_total_txt">0.00</span></span>
                </div>
            </div>
        </div>

        <!-- Sticky Bottom Summary Bar -->
        <div class="sticky-bar">
            <div class="summary-group">
                <div class="summary-item">
                    <span class="summary-label">Cash Total</span>
                    <span class="summary-val cash">Rs <span id="lbl_cash">0.00</span></span>
                </div>
                <div class="summary-item" style="border-left: 0.5px solid var(--c-separator); padding-left: 36px;">
                    <span class="summary-label">Cheques Total</span>
                    <span class="summary-val cheque">Rs <span id="lbl_cheque">0.00</span></span>
                </div>
                <div class="summary-item" style="border-left: 0.5px solid var(--c-separator); padding-left: 36px;">
                    <span class="summary-label">Grand Total Deposit</span>
                    <span class="summary-val total">Rs <span id="lbl_total">0.00</span></span>
                </div>
            </div>
            <div class="btn-group">
                <a href="<?= APP_URL ?>/deposit" class="btn-sf btn-sf-secondary">Cancel</a>
                <button type="submit" class="btn-sf btn-sf-success">
                    <i class="fa-solid fa-floppy-disk"></i> Save Deposit Draft
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    const denoms = [5000, 2000, 1000, 500, 100, 50, 20];

    function calculateCash() {
        let cashTotal = 0;
        denoms.forEach(d => {
            const count = parseInt(document.getElementById('c_' + d).value) || 0;
            const subtotal = count * d;
            document.getElementById('sub_' + d).innerText = subtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            cashTotal += subtotal;
        });

        document.getElementById('cash_total_txt').innerText = cashTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('lbl_cash').innerText = cashTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        updateGrandTotal();
    }

    function calculateCheques() {
        let chequeTotal = 0;
        let count = 0;
        const checkboxes = document.querySelectorAll('input[name="cheques[]"]:checked');
        checkboxes.forEach(cb => {
            chequeTotal += parseFloat(cb.getAttribute('data-amount')) || 0;
            count++;
        });

        document.getElementById('chk_count').innerText = count;
        document.getElementById('cheque_total_txt').innerText = chequeTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('lbl_cheque').innerText = chequeTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        updateGrandTotal();
    }

    function updateGrandTotal() {
        let cash = 0;
        denoms.forEach(d => {
            const count = parseInt(document.getElementById('c_' + d).value) || 0;
            cash += count * d;
        });

        let cheque = 0;
        const checkboxes = document.querySelectorAll('input[name="cheques[]"]:checked');
        checkboxes.forEach(cb => {
            cheque += parseFloat(cb.getAttribute('data-amount')) || 0;
        });

        const grand = cash + cheque;
        document.getElementById('lbl_total').innerText = grand.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Filter Cheques List Real-time Search
    function filterChequesList() {
        const query = document.getElementById('chequeSearchInput').value.toLowerCase().trim();
        const rows = document.querySelectorAll('#chequesTable tbody .cheque-row');

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            if (!query || text.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Toggle Select All Visible Cheques
    function toggleSelectAllCheques(selectState) {
        const rows = document.querySelectorAll('#chequesTable tbody .cheque-row');
        rows.forEach(row => {
            if (row.style.display !== 'none') {
                const cb = row.querySelector('input[name="cheques[]"]');
                if (cb) cb.checked = selectState;
            }
        });
        calculateCheques();
    }

    // Initialize calculations on load
    window.addEventListener('DOMContentLoaded', () => {
        calculateCash();
        calculateCheques();
    });
</script>
