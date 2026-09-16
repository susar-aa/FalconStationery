<?php require APPROOT . '/Views/layouts/header.php'; ?>
<div class="container py-4" style="max-width: 700px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 text-gray-800 font-weight-bold">Start Bank Reconciliation</h2>
            <p class="text-muted small mb-0">Select bank account and enter statement period details</p>
        </div>
        <a href="<?php echo URLROOT; ?>/bankreconciliation" class="btn btn-outline-secondary btn-sm rounded-pill">
            <i class="fas fa-arrow-left mr-1"></i> Back to History
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-lg">
        <div class="card-body p-4">
            <form action="<?php echo URLROOT; ?>/bankreconciliation/store" method="POST">
                <div class="form-group mb-4">
                    <label class="font-weight-bold text-dark mb-1">Select Bank Account <span class="text-danger">*</span></label>
                    <select name="account_id" class="form-control form-control-lg rounded-lg" required>
                        <option value="">-- Choose Bank Account --</option>
                        <?php foreach ($data['bankAccounts'] as $acc): ?>
                            <option value="<?php echo $acc->id; ?>">
                                <?php echo htmlspecialchars($acc->account_code . ' - ' . $acc->account_name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group mb-4">
                        <label class="font-weight-bold text-dark mb-1">Statement Start Date <span class="text-danger">*</span></label>
                        <input type="date" name="statement_date_start" class="form-control form-control-lg rounded-lg" value="<?php echo date('Y-m-01'); ?>" required>
                    </div>
                    <div class="col-md-6 form-group mb-4">
                        <label class="font-weight-bold text-dark mb-1">Statement End Date <span class="text-danger">*</span></label>
                        <input type="date" name="statement_date_end" class="form-control form-control-lg rounded-lg" value="<?php echo date('Y-m-t'); ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group mb-4">
                        <label class="font-weight-bold text-dark mb-1">Statement Opening Balance (LKR) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="statement_opening_balance" class="form-control form-control-lg rounded-lg" placeholder="0.00" required>
                    </div>
                    <div class="col-md-6 form-group mb-4">
                        <label class="font-weight-bold text-dark mb-1">Statement Closing Balance (LKR) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="statement_closing_balance" class="form-control form-control-lg rounded-lg" placeholder="0.00" required>
                    </div>
                </div>

                <div class="pt-3 border-top d-flex justify-content-end gap-2">
                    <a href="<?php echo URLROOT; ?>/bankreconciliation" class="btn btn-light btn-lg rounded-pill px-4 mr-2">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm">
                        <i class="fas fa-play mr-2"></i> Start Reconciliation Workspace
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require APPROOT . '/Views/layouts/footer.php'; ?>
