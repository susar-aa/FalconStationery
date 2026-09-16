<?php 
$rec = $data['reconciliation']; 
$items = $data['items'];

$depositsInTransit = [];
$outstandingCheques = [];
$matchedDeposits = [];
$matchedCheques = [];

foreach ($items as $item) {
    if (!$item->is_matched) {
        if ($item->debit > 0) $depositsInTransit[] = $item;
        if ($item->credit > 0) $outstandingCheques[] = $item;
    } else {
        if ($item->debit > 0) $matchedDeposits[] = $item;
        if ($item->credit > 0) $matchedCheques[] = $item;
    }
}

$totalDepositsInTransit = array_sum(array_column($depositsInTransit, 'debit'));
$totalOutstandingCheques = array_sum(array_column($outstandingCheques, 'credit'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bank Reconciliation Statement - <?php echo htmlspecialchars($rec->reconciliation_number); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #fff; color: #333; }
        .statement-header { border-bottom: 2px solid #2b2b2b; padding-bottom: 15px; margin-bottom: 25px; }
        .table-summary td { padding: 6px 12px; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; margin: 0; }
        }
    </style>
</head>
<body class="p-4">
    <div class="container" style="max-width: 900px;">
        <div class="no-print mb-4 d-flex justify-content-between align-items-center">
            <a href="<?php echo URLROOT; ?>/bankreconciliation/reconcile/<?php echo $rec->id; ?>" class="btn btn-outline-secondary btn-sm">
                &larr; Back to Workspace
            </a>
            <button onclick="window.print();" class="btn btn-primary btn-sm px-4">
                Print Statement / Export PDF
            </button>
        </div>

        <div class="statement-header text-center">
            <h2 class="font-weight-bold mb-1">Falcon Stationery (Pvt) Ltd</h2>
            <h4 class="text-uppercase text-secondary mb-2">Bank Reconciliation Statement</h4>
            <p class="mb-0 text-muted">
                <strong>Bank Account:</strong> <?php echo htmlspecialchars($rec->account_code . ' - ' . $rec->account_name); ?> | 
                <strong>Statement Period Ending:</strong> <?php echo date('F d, Y', strtotime($rec->statement_date_end)); ?>
            </p>
            <p class="small text-muted mb-0">Reconciliation Ref: <?php echo htmlspecialchars($rec->reconciliation_number); ?> | Status: <?php echo htmlspecialchars($rec->status); ?></p>
        </div>

        <!-- Summary Calculation Block -->
        <div class="card border-dark mb-4">
            <div class="card-header bg-dark text-white font-weight-bold">Reconciliation Summary</div>
            <div class="card-body p-0">
                <table class="table table-bordered table-summary mb-0">
                    <tbody>
                        <tr>
                            <td class="font-weight-bold">Bank Statement Closing Balance</td>
                            <td class="text-right font-weight-bold" style="width: 200px;">LKR <?php echo number_format($rec->statement_closing_balance, 2); ?></td>
                        </tr>
                        <tr>
                            <td><span class="ml-3 text-success">+ Add: Deposits in Transit</span></td>
                            <td class="text-right text-success">LKR <?php echo number_format($totalDepositsInTransit, 2); ?></td>
                        </tr>
                        <tr>
                            <td><span class="ml-3 text-danger">- Less: Outstanding Cheques / Payments</span></td>
                            <td class="text-right text-danger">(LKR <?php echo number_format($totalOutstandingCheques, 2); ?>)</td>
                        </tr>
                        <tr class="bg-light">
                            <td class="font-weight-bold">Adjusted Bank Balance</td>
                            <td class="text-right font-weight-bold text-primary">LKR <?php echo number_format($rec->adjusted_bank_balance, 2); ?></td>
                        </tr>
                        <tr class="bg-light">
                            <td class="font-weight-bold">General Ledger (Book) Closing Balance</td>
                            <td class="text-right font-weight-bold text-dark">LKR <?php echo number_format($rec->adjusted_book_balance, 2); ?></td>
                        </tr>
                        <tr class="<?php echo (abs($rec->difference) > 0.009) ? 'table-danger' : 'table-success'; ?>">
                            <td class="font-weight-bold">Unreconciled Difference</td>
                            <td class="text-right font-weight-bold">LKR <?php echo number_format($rec->difference, 2); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Deposits in Transit Table -->
        <div class="mb-4">
            <h5 class="font-weight-bold text-dark border-bottom pb-2">Deposits in Transit (Unmatched Deposits)</h5>
            <table class="table table-sm table-striped border">
                <thead>
                    <tr class="bg-light">
                        <th>Date</th>
                        <th>Reference</th>
                        <th>Description</th>
                        <th class="text-right">Amount (LKR)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($depositsInTransit)): ?>
                        <?php foreach ($depositsInTransit as $item): ?>
                            <tr>
                                <td><?php echo date('Y-m-d', strtotime($item->transaction_date)); ?></td>
                                <td><?php echo htmlspecialchars($item->reference ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($item->description); ?></td>
                                <td class="text-right font-weight-bold"><?php echo number_format($item->debit, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center text-muted py-2">No deposits in transit.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Outstanding Cheques Table -->
        <div class="mb-4">
            <h5 class="font-weight-bold text-dark border-bottom pb-2">Outstanding Cheques & Payments (Unmatched Payments)</h5>
            <table class="table table-sm table-striped border">
                <thead>
                    <tr class="bg-light">
                        <th>Date</th>
                        <th>Cheque / Ref #</th>
                        <th>Description</th>
                        <th class="text-right">Amount (LKR)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($outstandingCheques)): ?>
                        <?php foreach ($outstandingCheques as $item): ?>
                            <tr>
                                <td><?php echo date('Y-m-d', strtotime($item->transaction_date)); ?></td>
                                <td><?php echo htmlspecialchars($item->reference ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($item->description); ?></td>
                                <td class="text-right font-weight-bold"><?php echo number_format($item->credit, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center text-muted py-2">No outstanding cheques.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-5 pt-4 border-top d-flex justify-content-between text-center">
            <div style="width: 220px; border-top: 1px solid #333; padding-top: 5px;">
                <strong>Prepared By</strong>
            </div>
            <div style="width: 220px; border-top: 1px solid #333; padding-top: 5px;">
                <strong>Checked & Verified By</strong>
            </div>
            <div style="width: 220px; border-top: 1px solid #333; padding-top: 5px;">
                <strong>Approved By</strong>
            </div>
        </div>
    </div>
</body>
</html>
