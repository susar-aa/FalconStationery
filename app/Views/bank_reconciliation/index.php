<?php require APPROOT . '/Views/layouts/header.php'; ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 text-gray-800 font-weight-bold">Bank Reconciliation</h2>
            <p class="text-muted small mb-0">Reconcile bank statements with General Ledger balances</p>
        </div>
        <a href="<?php echo URLROOT; ?>/bankreconciliation/create" class="btn btn-primary btn-sm px-3 shadow-sm rounded-pill">
            <i class="fas fa-plus-circle mr-1"></i> New Reconciliation
        </a>
    </div>

    <?php flash('rec_message'); ?>

    <div class="card border-0 shadow-sm rounded-lg">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4">Ref Number</th>
                            <th class="border-0">Bank Account</th>
                            <th class="border-0">Statement Period</th>
                            <th class="border-0 text-right">Stmt Closing</th>
                            <th class="border-0 text-right">Book Closing</th>
                            <th class="border-0 text-right">Difference</th>
                            <th class="border-0 text-center">Status</th>
                            <th class="border-0 text-center px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['reconciliations'])): ?>
                            <?php foreach ($data['reconciliations'] as $rec): ?>
                                <tr>
                                    <td class="px-4 font-weight-bold">
                                        <a href="<?php echo URLROOT; ?>/bankreconciliation/reconcile/<?php echo $rec->id; ?>" class="text-primary">
                                            <?php echo htmlspecialchars($rec->reconciliation_number); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold text-dark"><?php echo htmlspecialchars($rec->account_name); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($rec->account_code); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge badge-light border">
                                            <?php echo date('M d, Y', strtotime($rec->statement_date_start)); ?> - <?php echo date('M d, Y', strtotime($rec->statement_date_end)); ?>
                                        </span>
                                    </td>
                                    <td class="text-right font-weight-bold text-dark">
                                        LKR <?php echo number_format($rec->statement_closing_balance, 2); ?>
                                    </td>
                                    <td class="text-right font-weight-bold text-dark">
                                        LKR <?php echo number_format($rec->book_closing_balance, 2); ?>
                                    </td>
                                    <td class="text-right font-weight-bold <?php echo (abs($rec->difference) > 0.009) ? 'text-danger' : 'text-success'; ?>">
                                        LKR <?php echo number_format($rec->difference, 2); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($rec->status === 'Finalized'): ?>
                                            <span class="badge badge-success px-3 py-1 rounded-pill">Finalized</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning px-3 py-1 rounded-pill text-dark">Draft</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center px-4">
                                        <a href="<?php echo URLROOT; ?>/bankreconciliation/reconcile/<?php echo $rec->id; ?>" class="btn btn-sm btn-outline-primary rounded-pill mr-1">
                                            <i class="fas fa-edit mr-1"></i> <?php echo ($rec->status === 'Finalized') ? 'View Workspace' : 'Reconcile'; ?>
                                        </a>
                                        <a href="<?php echo URLROOT; ?>/bankreconciliation/report/<?php echo $rec->id; ?>" class="btn btn-sm btn-outline-secondary rounded-pill">
                                            <i class="fas fa-print mr-1"></i> Statement
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-university fa-3x mb-3 text-secondary opacity-50"></i>
                                    <p class="mb-0">No bank reconciliation records found.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require APPROOT . '/Views/layouts/footer.php'; ?>
