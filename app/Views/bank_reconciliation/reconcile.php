<!-- Inter Font & FontAwesome Icons -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<?php 
$rec = $data['reconciliation']; 
$isFinalized = ($rec->status === 'Finalized');
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
    --c-red:          #ff3b30;
    --c-red-light:    #fff0ef;

    --f-system: -apple-system, 'SF Pro Display', 'SF Pro Text', 'Inter', sans-serif;
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

.rec-workspace {
    font-family: var(--f-system);
    font-size: 14px;
    color: var(--t-primary);
    -webkit-font-smoothing: antialiased;
}

.sf-page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 16px;
}
.sf-page-title h1 {
    font-size: 26px;
    font-weight: 800;
    margin: 0 0 4px 0;
    letter-spacing: -0.02em;
}
.sf-page-title p {
    margin: 0;
    color: var(--t-secondary);
    font-size: 13px;
}

/* KPI Summary Cards */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}
@media (max-width: 900px) {
    .kpi-grid { grid-template-columns: repeat(2, 1fr); }
}
.kpi-card {
    background: var(--c-surface);
    border-radius: var(--r-md);
    padding: 18px 20px;
    border: 0.5px solid var(--c-separator);
    box-shadow: var(--shadow-xs);
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.kpi-card.highlight { background: var(--c-blue); color: #fff; border-color: var(--c-blue); }
.kpi-card.highlight .kpi-label, .kpi-card.highlight .kpi-sub { color: rgba(255,255,255,0.8); }
.kpi-card.highlight .kpi-val { color: #fff; }

.kpi-card.danger { background: var(--c-red-light); border-color: rgba(255,59,48,0.3); }
.kpi-card.danger .kpi-val { color: var(--c-red); }
.kpi-card.success { background: var(--c-green-light); border-color: rgba(52,199,89,0.3); }
.kpi-card.success .kpi-val { color: #15803d; }

.kpi-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--t-secondary); }
.kpi-val { font-size: 22px; font-weight: 800; color: var(--t-primary); }
.kpi-sub { font-size: 12px; color: var(--t-label); }

/* Toolbar */
.toolbar-panel {
    background: var(--c-surface);
    border-radius: var(--r-md);
    border: 0.5px solid var(--c-separator);
    padding: 12px 18px;
    margin-bottom: 20px;
    display: flex;
    gap: 12px;
    align-items: center;
    flex-wrap: wrap;
}
.search-input {
    padding: 8px 14px;
    border-radius: var(--r-pill);
    border: 0.5px solid var(--c-separator);
    background: var(--c-surface2);
    color: var(--t-primary);
    font-size: 13px;
    outline: none;
    flex: 1;
    min-width: 200px;
}
.filter-select {
    padding: 8px 14px;
    border-radius: var(--r-pill);
    border: 0.5px solid var(--c-separator);
    background: var(--c-surface2);
    color: var(--t-primary);
    font-size: 13px;
    outline: none;
}

/* Table */
.table-panel {
    background: var(--c-surface);
    border-radius: var(--r-lg);
    border: 0.5px solid var(--c-separator);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}
.sf-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
    font-size: 13.5px;
}
.sf-table th {
    background: var(--c-surface2);
    padding: 12px 16px;
    font-size: 11px;
    font-weight: 700;
    color: var(--t-secondary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 0.5px solid var(--c-separator);
}
.sf-table td {
    padding: 12px 16px;
    border-bottom: 0.5px solid var(--c-separator);
    vertical-align: middle;
}
.sf-table tr:hover td { background: var(--c-fill); }

.btn-sf {
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 600;
    border-radius: var(--r-pill);
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    transition: all 0.18s ease;
}
.btn-sf-primary { background: var(--c-blue); color: #fff; }
.btn-sf-primary:hover { opacity: 0.9; color: #fff; }
.btn-sf-secondary { background: var(--c-fill); color: var(--t-primary); }
.btn-sf-success { background: var(--c-green); color: #fff; }
.btn-sf-success:disabled { opacity: 0.4; cursor: not-allowed; }

/* Modal */
.modal-overlay {
    display: none; position: fixed; inset: 0; z-index: 9999;
    background: rgba(0,0,0,0.4); backdrop-filter: blur(8px);
    align-items: center; justify-content: center;
}
.modal-overlay.open { display: flex; }
.modal-box {
    background: var(--c-surface); width: 480px;
    border-radius: var(--r-lg); padding: 24px;
    border: 0.5px solid var(--c-separator); box-shadow: var(--shadow-md);
}
.modal-box h3 { margin: 0 0 16px 0; font-size: 18px; font-weight: 700; }
.form-group { margin-bottom: 14px; }
.form-group label { display: block; font-size: 12px; font-weight: 700; margin-bottom: 4px; color: var(--t-secondary); }
.form-control-sf {
    width: 100%; padding: 8px 12px; font-size: 13px;
    background: var(--c-surface2); border: 0.5px solid var(--c-separator);
    border-radius: var(--r-sm); color: var(--t-primary); outline: none; box-sizing: border-box;
}
</style>

<div class="rec-workspace">
    <!-- Page Header -->
    <div class="sf-page-header">
        <div class="sf-page-title">
            <div style="display: flex; align-items: center; gap: 10px;">
                <h1>Reconciliation Workspace</h1>
                <?php if ($isFinalized): ?>
                    <span style="background: var(--c-green-light); color: #15803d; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: var(--r-pill);">FINALIZED & LOCKED</span>
                <?php else: ?>
                    <span style="background: var(--c-fill2); color: var(--t-secondary); font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: var(--r-pill);">DRAFT MODE</span>
                <?php endif; ?>
            </div>
            <p>
                <strong>Account:</strong> <?= htmlspecialchars($rec->account_code . ' - ' . $rec->account_name) ?> | 
                <strong>Period:</strong> <?= date('M d, Y', strtotime($rec->statement_date_start)) ?> to <?= date('M d, Y', strtotime($rec->statement_date_end)) ?> | 
                <strong>Ref:</strong> <?= htmlspecialchars($rec->reconciliation_number) ?>
            </p>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="<?= APP_URL ?>/bankreconciliation/report/<?= $rec->id ?>" class="btn-sf btn-sf-secondary" target="_blank">
                <i class="fa-solid fa-print"></i> Print Statement
            </a>
            <?php if (!$isFinalized): ?>
                <button type="button" class="btn-sf btn-sf-secondary" onclick="openBankOnlyModal()">
                    <i class="fa-solid fa-plus"></i> Bank-Only Entry
                </button>
                <button type="button" class="btn-sf btn-sf-success" id="btnFinalize" <?= (abs($rec->difference) > 0.009) ? 'disabled' : '' ?>>
                    <i class="fa-solid fa-check"></i> Finalize
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- KPI Summary Grid -->
    <div class="kpi-grid">
        <div class="kpi-card highlight">
            <div class="kpi-label">Statement Closing</div>
            <div class="kpi-val">LKR <?= number_format($rec->statement_closing_balance, 2) ?></div>
            <div class="kpi-sub">Bank Statement</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Adjusted Bank</div>
            <div class="kpi-val">LKR <?= number_format($rec->adjusted_bank_balance, 2) ?></div>
            <div class="kpi-sub">Stmt + Transit - Outstanding</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Adjusted Book / GL</div>
            <div class="kpi-val">LKR <?= number_format($rec->adjusted_book_balance, 2) ?></div>
            <div class="kpi-sub">General Ledger Balance</div>
        </div>
        <div class="kpi-card <?= (abs($rec->difference) > 0.009) ? 'danger' : 'success' ?>">
            <div class="kpi-label">Difference</div>
            <div class="kpi-val">LKR <?= number_format($rec->difference, 2) ?></div>
            <div class="kpi-sub"><?= (abs($rec->difference) > 0.009) ? 'Unbalanced' : 'Balanced' ?></div>
        </div>
    </div>

    <!-- Toolbar Filters -->
    <div class="toolbar-panel">
        <input type="text" id="searchTerm" class="search-input" placeholder="Search reference, description, amount...">
        <select id="filterMatch" class="filter-select">
            <option value="all">All Transactions</option>
            <option value="matched">Matched Only</option>
            <option value="unmatched">Unmatched Only (Outstanding)</option>
        </select>
        <select id="filterType" class="filter-select">
            <option value="all">All Types (Deposits & Payments)</option>
            <option value="debit">Deposits / Credits (Debit to Bank)</option>
            <option value="credit">Payments / Cheques (Credit to Bank)</option>
        </select>
        <button type="button" class="btn-sf btn-sf-secondary" style="font-size: 12px; padding: 6px 14px;" onclick="resetFilters()">Reset</button>
    </div>

    <!-- Matching Table -->
    <div class="table-panel">
        <table class="sf-table">
            <thead>
                <tr>
                    <th style="text-align: center; width: 60px;">Match</th>
                    <th>Date</th>
                    <th>Ref / Cheque #</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th style="text-align: right;">Deposit (Dr)</th>
                    <th style="text-align: right;">Payment (Cr)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['items'])): ?>
                    <?php foreach ($data['items'] as $item): ?>
                        <tr class="tx-row" 
                            data-matched="<?= $item->is_matched ? 'matched' : 'unmatched' ?>" 
                            data-type="<?= ($item->debit > 0) ? 'debit' : 'credit' ?>"
                            data-search="<?= strtolower(htmlspecialchars(($item->reference ?? '') . ' ' . ($item->description ?? '') . ' ' . $item->debit . ' ' . $item->credit)) ?>">
                            <td style="text-align: center;">
                                <input type="checkbox" 
                                       class="match-checkbox" 
                                       data-item-id="<?= $item->id ?>" 
                                       <?= $item->is_matched ? 'checked' : '' ?>
                                       <?= $isFinalized ? 'disabled' : '' ?> 
                                       style="transform: scale(1.3); cursor: pointer;">
                            </td>
                            <td><?= date('Y-m-d', strtotime($item->transaction_date)) ?></td>
                            <td style="font-weight: 700;"><?= htmlspecialchars($item->reference ?? '-') ?></td>
                            <td>
                                <div><?= htmlspecialchars($item->description) ?></div>
                                <?php if ($item->transaction_type === 'BANK_ONLY'): ?>
                                    <span style="font-size: 10px; font-weight: 700; color: var(--c-blue); background: var(--c-blue-light); padding: 2px 6px; border-radius: 4px;">Bank Entry</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($item->debit > 0): ?>
                                    <span style="color: #15803d; font-weight: 700; font-size: 11px;">Deposit / Inflow</span>
                                <?php else: ?>
                                    <span style="color: var(--t-secondary); font-weight: 700; font-size: 11px;">Payment / Outflow</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right; font-weight: 700; color: <?= ($item->debit > 0) ? 'var(--c-green)' : 'var(--t-label)' ?>;">
                                <?= ($item->debit > 0) ? 'LKR ' . number_format($item->debit, 2) : '-' ?>
                            </td>
                            <td style="text-align: right; font-weight: 700; color: <?= ($item->credit > 0) ? 'var(--c-red)' : 'var(--t-label)' ?>;">
                                <?= ($item->credit > 0) ? 'LKR ' . number_format($item->credit, 2) : '-' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: var(--t-secondary);">No ledger transactions found for this account and period.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for Bank-Only Entries -->
<?php if (!$isFinalized): ?>
<div class="modal-overlay" id="bankOnlyModal">
    <div class="modal-box">
        <h3>Add Bank-Only Adjustment</h3>
        <form id="bankOnlyForm">
            <input type="hidden" name="reconciliation_id" value="<?= $rec->id ?>">
            <div class="form-group">
                <label>Transaction Type <span style="color: var(--c-red);">*</span></label>
                <select name="type" class="form-control-sf" required>
                    <option value="CHARGE">Bank Charges / Fees (Expense)</option>
                    <option value="INTEREST">Bank Interest Earned (Income)</option>
                    <option value="DIRECT_DEPOSIT">Direct Bank Deposit / Credit</option>
                    <option value="DIRECT_DEBIT">Direct Bank Withdrawal / Debit</option>
                </select>
            </div>
            <div class="form-group">
                <label>Date <span style="color: var(--c-red);">*</span></label>
                <input type="date" name="transaction_date" class="form-control-sf" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group">
                <label>Amount (LKR) <span style="color: var(--c-red);">*</span></label>
                <input type="number" step="0.01" name="amount" class="form-control-sf" placeholder="0.00" required>
            </div>
            <div class="form-group">
                <label>Corresponding GL Account <span style="color: var(--c-red);">*</span></label>
                <select name="opposite_account_id" class="form-control-sf" required>
                    <option value="">-- Choose Account --</option>
                    <?php foreach ($data['accounts'] as $acc): ?>
                        <option value="<?= $acc->id; ?>">
                            <?= htmlspecialchars($acc->account_code . ' - ' . $acc->account_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Reference / Advice #</label>
                <input type="text" name="reference" class="form-control-sf" placeholder="e.g. BC-202609">
            </div>
            <div class="form-group">
                <label>Description</label>
                <input type="text" name="description" class="form-control-sf" placeholder="e.g. Monthly Bank Service Charge">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn-sf btn-sf-secondary" onclick="closeBankOnlyModal()">Cancel</button>
                <button type="submit" class="btn-sf btn-sf-primary">Save & Post Entry</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script>
function openBankOnlyModal() { document.getElementById('bankOnlyModal').classList.add('open'); }
function closeBankOnlyModal() { document.getElementById('bankOnlyModal').classList.remove('open'); }

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchTerm');
    const filterMatch = document.getElementById('filterMatch');
    const filterType = document.getElementById('filterType');

    function filterRows() {
        const query = searchInput.value.toLowerCase().trim();
        const matchVal = filterMatch.value;
        const typeVal = filterType.value;

        document.querySelectorAll('.tx-row').forEach(row => {
            const rowSearch = row.getAttribute('data-search');
            const rowMatched = row.getAttribute('data-matched');
            const rowType = row.getAttribute('data-type');

            let matchesQuery = !query || rowSearch.includes(query);
            let matchesMatch = (matchVal === 'all') || (rowMatched === matchVal);
            let matchesType = (typeVal === 'all') || (rowType === typeVal);

            row.style.display = (matchesQuery && matchesMatch && matchesType) ? '' : 'none';
        });
    }

    if (searchInput) searchInput.addEventListener('input', filterRows);
    if (filterMatch) filterMatch.addEventListener('change', filterRows);
    if (filterType) filterType.addEventListener('change', filterRows);

    window.resetFilters = function() {
        searchInput.value = '';
        filterMatch.value = 'all';
        filterType.value = 'all';
        filterRows();
    };

    // AJAX Match toggle
    document.querySelectorAll('.match-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            const itemId = this.getAttribute('data-item-id');
            const isMatched = this.checked ? 1 : 0;

            const formData = new FormData();
            formData.append('item_id', itemId);
            formData.append('is_matched', isMatched);

            fetch('<?= APP_URL ?>/bankreconciliation/match_ajax', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to update match');
                    this.checked = !this.checked;
                }
            })
            .catch(err => {
                console.error(err);
                alert('Server connection error.');
                this.checked = !this.checked;
            });
        });
    });

    // AJAX Add Bank Only Entry
    const bankForm = document.getElementById('bankOnlyForm');
    if (bankForm) {
        bankForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('<?= APP_URL ?>/bankreconciliation/bank_only_ajax', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to post bank entry');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Server connection error.');
            });
        });
    }

    // AJAX Finalize
    const btnFinalize = document.getElementById('btnFinalize');
    if (btnFinalize) {
        btnFinalize.addEventListener('click', function() {
            if (!confirm('Are you sure you want to finalize and lock this Bank Reconciliation?')) {
                return;
            }

            const formData = new FormData();
            formData.append('reconciliation_id', '<?= $rec->id ?>');

            fetch('<?= APP_URL ?>/bankreconciliation/finalize_ajax', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                if (data.status === 'success') {
                    window.location.reload();
                }
            })
            .catch(err => {
                console.error(err);
                alert('Server connection error.');
            });
        });
    }
});
</script>
