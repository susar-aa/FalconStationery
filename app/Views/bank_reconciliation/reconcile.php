<?php 
$rec = $data['reconciliation']; 
$isFinalized = ($rec->status === 'Finalized');
?>

<div class="container-fluid py-4">
    <!-- Top Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h2 class="h3 mb-0 text-gray-800 font-weight-bold">Reconciliation Workspace</h2>
                <?php if ($isFinalized): ?>
                    <span class="badge badge-success px-3 py-1 rounded-pill">Finalized & Locked</span>
                <?php else: ?>
                    <span class="badge badge-warning px-3 py-1 rounded-pill text-dark">Draft Mode</span>
                <?php endif; ?>
            </div>
            <p class="text-muted small mb-0">
                <strong>Account:</strong> <?php echo htmlspecialchars($rec->account_code . ' - ' . $rec->account_name); ?> | 
                <strong>Period:</strong> <?php echo date('M d, Y', strtotime($rec->statement_date_start)); ?> to <?php echo date('M d, Y', strtotime($rec->statement_date_end)); ?> | 
                <strong>Ref:</strong> <?php echo htmlspecialchars($rec->reconciliation_number); ?>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo URLROOT; ?>/bankreconciliation/report/<?php echo $rec->id; ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3" target="_blank">
                <i class="fas fa-print mr-1"></i> Print Statement
            </a>
            <?php if (!$isFinalized): ?>
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" data-toggle="modal" data-target="#bankOnlyModal">
                    <i class="fas fa-plus-circle mr-1"></i> Add Bank-Only Entry
                </button>
                <button type="button" class="btn btn-success btn-sm rounded-pill px-4 shadow-sm" id="btnFinalize" <?php echo (abs($rec->difference) > 0.009) ? 'disabled' : ''; ?>>
                    <i class="fas fa-check-circle mr-1"></i> Finalize Reconciliation
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Summary Dashboard Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm rounded-lg bg-primary text-white h-100">
                <div class="card-body py-3">
                    <div class="text-uppercase small font-weight-bold opacity-75">Statement Closing Balance</div>
                    <div class="h3 font-weight-bold mb-0">LKR <?php echo number_format($rec->statement_closing_balance, 2); ?></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm rounded-lg bg-info text-white h-100">
                <div class="card-body py-3">
                    <div class="text-uppercase small font-weight-bold opacity-75">Adjusted Bank Balance</div>
                    <div class="h3 font-weight-bold mb-0">LKR <?php echo number_format($rec->adjusted_bank_balance, 2); ?></div>
                    <small class="opacity-75">Stmt + Deposits in Transit - Outstanding Cheques</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm rounded-lg bg-dark text-white h-100">
                <div class="card-body py-3">
                    <div class="text-uppercase small font-weight-bold opacity-75">Adjusted Book / GL Balance</div>
                    <div class="h3 font-weight-bold mb-0">LKR <?php echo number_format($rec->adjusted_book_balance, 2); ?></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm rounded-lg <?php echo (abs($rec->difference) > 0.009) ? 'bg-danger' : 'bg-success'; ?> text-white h-100">
                <div class="card-body py-3">
                    <div class="text-uppercase small font-weight-bold opacity-75">Difference</div>
                    <div class="h3 font-weight-bold mb-0">LKR <?php echo number_format($rec->difference, 2); ?></div>
                    <small class="opacity-75"><?php echo (abs($rec->difference) > 0.009) ? 'Unbalanced' : 'Balanced & Ready'; ?></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card border-0 shadow-sm rounded-lg mb-4">
        <div class="card-body py-2 px-3">
            <div class="row align-items-center">
                <div class="col-md-4 my-1">
                    <input type="text" id="searchTerm" class="form-control form-control-sm rounded-pill" placeholder="Search reference, description, amount...">
                </div>
                <div class="col-md-3 my-1">
                    <select id="filterMatch" class="form-control form-control-sm rounded-pill">
                        <option value="all">All Transactions</option>
                        <option value="matched">Matched Only</option>
                        <option value="unmatched">Unmatched Only (Outstanding)</option>
                    </select>
                </div>
                <div class="col-md-3 my-1">
                    <select id="filterType" class="form-control form-control-sm rounded-pill">
                        <option value="all">All Types (Deposits & Payments)</option>
                        <option value="debit">Deposits / Credits (Debit to Bank)</option>
                        <option value="credit">Payments / Cheques (Credit to Bank)</option>
                    </select>
                </div>
                <div class="col-md-2 my-1 text-right">
                    <button type="button" class="btn btn-sm btn-light rounded-pill px-3" onclick="resetFilters()">Reset</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Matching Table -->
    <div class="card border-0 shadow-sm rounded-lg">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 font-weight-bold text-gray-800">Transactions Ledger Matching</h5>
            <small class="text-muted">Check items that appear on your Bank Statement</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="txTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4 text-center" style="width: 60px;">Match</th>
                            <th class="border-0">Date</th>
                            <th class="border-0">Ref / Cheque #</th>
                            <th class="border-0">Description</th>
                            <th class="border-0">Type</th>
                            <th class="border-0 text-right">Deposit (Dr)</th>
                            <th class="border-0 text-right">Payment (Cr)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['items'])): ?>
                            <?php foreach ($data['items'] as $item): ?>
                                <tr class="tx-row" 
                                    data-matched="<?php echo $item->is_matched ? 'matched' : 'unmatched'; ?>" 
                                    data-type="<?php echo ($item->debit > 0) ? 'debit' : 'credit'; ?>"
                                    data-search="<?php echo strtolower(htmlspecialchars(($item->reference ?? '') . ' ' . ($item->description ?? '') . ' ' . $item->debit . ' ' . $item->credit)); ?>">
                                    <td class="px-4 text-center">
                                        <input type="checkbox" 
                                               class="match-checkbox" 
                                               data-item-id="<?php echo $item->id; ?>" 
                                               <?php echo $item->is_matched ? 'checked' : ''; ?>
                                               <?php echo $isFinalized ? 'disabled' : ''; ?> 
                                               style="transform: scale(1.3); cursor: pointer;">
                                    </td>
                                    <td><?php echo date('Y-m-d', strtotime($item->transaction_date)); ?></td>
                                    <td class="font-weight-bold text-dark"><?php echo htmlspecialchars($item->reference ?? '-'); ?></td>
                                    <td>
                                        <div><?php echo htmlspecialchars($item->description); ?></div>
                                        <?php if ($item->transaction_type === 'BANK_ONLY'): ?>
                                            <span class="badge badge-light border text-primary">Bank Entry</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($item->debit > 0): ?>
                                            <span class="badge badge-success px-2">Deposit / Inflow</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary px-2">Payment / Outflow</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right font-weight-bold <?php echo ($item->debit > 0) ? 'text-success' : 'text-muted'; ?>">
                                        <?php echo ($item->debit > 0) ? 'LKR ' . number_format($item->debit, 2) : '-'; ?>
                                    </td>
                                    <td class="text-right font-weight-bold <?php echo ($item->credit > 0) ? 'text-danger' : 'text-muted'; ?>">
                                        <?php echo ($item->credit > 0) ? 'LKR ' . number_format($item->credit, 2) : '-'; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">No ledger transactions found for this account and period.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Bank-Only Entries -->
<?php if (!$isFinalized): ?>
<div class="modal fade" id="bankOnlyModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-plus-circle mr-2"></i>Add Bank-Only Adjustment</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="bankOnlyForm">
                <input type="hidden" name="reconciliation_id" value="<?php echo $rec->id; ?>">
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold mb-1">Transaction Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-control rounded-lg" required>
                            <option value="CHARGE">Bank Charges / Fees (Expense)</option>
                            <option value="INTEREST">Bank Interest Earned (Income)</option>
                            <option value="DIRECT_DEPOSIT">Direct Bank Deposit / Credit</option>
                            <option value="DIRECT_DEBIT">Direct Bank Withdrawal / Debit</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold mb-1">Date <span class="text-danger">*</span></label>
                        <input type="date" name="transaction_date" class="form-control rounded-lg" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold mb-1">Amount (LKR) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" class="form-control rounded-lg" placeholder="0.00" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold mb-1">Corresponding GL Account <span class="text-danger">*</span></label>
                        <select name="opposite_account_id" class="form-control rounded-lg" required>
                            <option value="">-- Choose Account --</option>
                            <?php foreach ($data['accounts'] as $acc): ?>
                                <option value="<?php echo $acc->id; ?>">
                                    <?php echo htmlspecialchars($acc->account_code . ' - ' . $acc->account_name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold mb-1">Reference / Advice #</label>
                        <input type="text" name="reference" class="form-control rounded-lg" placeholder="e.g. BC-202609">
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold mb-1">Description</label>
                        <input type="text" name="description" class="form-control rounded-lg" placeholder="e.g. Monthly Bank Service Charge">
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">Save & Post Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
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

            if (matchesQuery && matchesMatch && matchesType) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
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

            fetch('<?php echo URLROOT; ?>/bankreconciliation/match_ajax', {
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

            fetch('<?php echo URLROOT; ?>/bankreconciliation/bank_only_ajax', {
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
            formData.append('reconciliation_id', '<?php echo $rec->id; ?>');

            fetch('<?php echo URLROOT; ?>/bankreconciliation/finalize_ajax', {
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
