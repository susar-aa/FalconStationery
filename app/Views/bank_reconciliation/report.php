<?php 
/**
 * Bank Reconciliation Statement / Audit Report - Curtiss & Falcon Stationery ERP
 * Print-Optimized, SF Pro Display & SF Mono Typography, Self-Contained Styles
 */
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
$totalMatchedDeposits = array_sum(array_column($matchedDeposits, 'debit'));
$totalMatchedCheques = array_sum(array_column($matchedCheques, 'credit'));

$stmtOpening = floatval($rec->statement_opening_balance);
$stmtClosing = floatval($rec->statement_closing_balance);
$clearedBankBalance = round($stmtOpening + $totalMatchedDeposits - $totalMatchedCheques, 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bank Reconciliation Statement - <?= htmlspecialchars($rec->reconciliation_number); ?></title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }
        body {
            font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8fafc;
            color: #0f172a;
            font-size: 13px;
            line-height: 1.45;
            margin: 0;
            padding: 24px 0;
        }
        .report-page-container {
            max-width: 860px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 32px 36px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .no-print-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .btn-secondary {
            background: #ffffff;
            color: #475569;
            border: 1px solid #cbd5e1;
        }
        .btn-secondary:hover {
            background: #f1f5f9;
            color: #0f172a;
        }
        .btn-primary {
            background: #0284c7;
            color: #ffffff;
            border: 1px solid #0284c7;
        }
        .btn-primary:hover {
            background: #0369a1;
        }

        .header-section {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 16px;
            margin-bottom: 24px;
            text-align: center;
        }
        .company-name {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
            margin: 0 0 4px 0;
        }
        .report-title {
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #475569;
            margin: 0 0 10px 0;
        }
        .meta-text {
            font-size: 12.5px;
            color: #334155;
            margin: 0;
        }
        .badge-status {
            display: inline-block;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 700;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .badge-finalized { background: #dcfce7; color: #15803d; }
        .badge-draft { background: #f1f5f9; color: #475569; }

        /* Report Summary Cards */
        .section-card {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            margin-bottom: 22px;
            overflow: hidden;
        }
        .section-header {
            background: #0f172a;
            color: #ffffff;
            font-weight: 700;
            font-size: 12.5px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 8px 14px;
            display: flex;
            justify-content: space-between;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .summary-table td {
            padding: 8px 14px;
            border-bottom: 1px solid #e2e8f0;
        }
        .summary-table tr:last-child td {
            border-bottom: none;
        }
        .summary-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .summary-table .highlight-row td {
            background-color: #f1f5f9;
            font-weight: 700;
        }
        .summary-table .row-success td {
            background-color: #f0fdf4;
            color: #15803d;
            font-weight: 700;
        }
        .summary-table .row-danger td {
            background-color: #fef2f2;
            color: #b91c1c;
            font-weight: 700;
        }

        /* Detail Tables with Zebra Striping (Mandatory Rule 3) */
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #cbd5e1;
            font-size: 12.5px;
            margin-top: 8px;
            margin-bottom: 22px;
        }
        .detail-table th {
            background: #f1f5f9;
            color: #475569;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 8px 10px;
            border-bottom: 1px solid #cbd5e1;
            text-align: left;
        }
        .detail-table td {
            padding: 7px 10px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
        }
        .detail-table tbody tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .num-mono {
            font-family: ui-monospace, 'SF Mono', Menlo, Monaco, Consolas, monospace;
            font-variant-numeric: tabular-nums;
        }
        .text-right { text-align: right; }

        .signature-strip {
            display: flex;
            justify-content: space-between;
            margin-top: 48px;
            padding-top: 14px;
            text-align: center;
        }
        .sig-box {
            width: 200px;
            border-top: 1px solid #0f172a;
            padding-top: 6px;
            font-size: 12px;
            font-weight: 600;
            color: #334155;
        }

        @media print {
            .no-print-bar { display: none !important; }
            body {
                background: #ffffff;
                padding: 0;
            }
            .report-page-container {
                border: none;
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="report-page-container">
        <!-- Top Action Bar (No Print) -->
        <div class="no-print-bar">
            <a href="<?= APP_URL; ?>/bankreconciliation/reconcile/<?= $rec->id; ?>" class="btn-action btn-secondary">
                &larr; Back to Workspace
            </a>
            <button onclick="window.print();" class="btn-action btn-primary">
                Print Statement / Export PDF
            </button>
        </div>

        <!-- Header -->
        <div class="header-section">
            <h1 class="company-name">Curtiss Enterprises / Falcon Stationery</h1>
            <div class="report-title">Bank Reconciliation Audit Statement</div>
            <p class="meta-text">
                <strong>Bank Account:</strong> <?= htmlspecialchars($rec->account_code . ' - ' . $rec->account_name); ?> &nbsp;|&nbsp;
                <strong>Statement Period:</strong> <?= date('M d, Y', strtotime($rec->statement_date_start)); ?> &rarr; <?= date('M d, Y', strtotime($rec->statement_date_end)); ?>
            </p>
            <p class="meta-text" style="margin-top: 4px; font-size: 12px; color: #64748b;">
                Reconciliation Reference: <span class="num-mono"><?= htmlspecialchars($rec->reconciliation_number); ?></span> &nbsp;|&nbsp;
                Status: <span class="badge-status <?= ($rec->status === 'Finalized') ? 'badge-finalized' : 'badge-draft'; ?>"><?= htmlspecialchars($rec->status); ?></span>
            </p>
        </div>

        <!-- Section 1: Bank Statement Movement Summary -->
        <div class="section-card">
            <div class="section-header">
                <span>1. Bank Statement & Cleared Activity Movement</span>
                <span class="num-mono" style="font-weight: normal; font-size: 11px;">Confirmed Statement Period</span>
            </div>
            <table class="summary-table">
                <tbody>
                    <tr>
                        <td style="font-weight: 700;">Bank Statement Confirmed Opening Balance</td>
                        <td class="text-right num-mono" style="font-weight: 700; width: 220px;">LKR <?= number_format($stmtOpening, 2); ?></td>
                    </tr>
                    <tr>
                        <td style="padding-left: 28px; color: #16a34a;">+ Add: Cleared Deposits / Inflows (Bank Statement)</td>
                        <td class="text-right num-mono" style="color: #16a34a;">+ LKR <?= number_format($totalMatchedDeposits, 2); ?></td>
                    </tr>
                    <tr>
                        <td style="padding-left: 28px; color: #dc2626;">- Less: Cleared Cheques / Outflows (Bank Statement)</td>
                        <td class="text-right num-mono" style="color: #dc2626;">- LKR <?= number_format($totalMatchedCheques, 2); ?></td>
                    </tr>
                    <tr class="highlight-row">
                        <td>Calculated Cleared Bank Balance</td>
                        <td class="text-right num-mono">LKR <?= number_format($clearedBankBalance, 2); ?></td>
                    </tr>
                    <tr class="highlight-row">
                        <td style="color: #0284c7;">Target Bank Statement Ending Balance</td>
                        <td class="text-right num-mono" style="color: #0284c7;">LKR <?= number_format($stmtClosing, 2); ?></td>
                    </tr>
                    <tr class="<?= (abs($rec->difference) > 0.009) ? 'row-danger' : 'row-success'; ?>">
                        <td>Unreconciled Difference (Target - Cleared)</td>
                        <td class="text-right num-mono">LKR <?= number_format($rec->difference, 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Section 2: Classical Bank to Book Reconciliation -->
        <div class="section-card">
            <div class="section-header" style="background: #334155;">
                <span>2. Classical Bank to Book Reconciliation</span>
                <span class="num-mono" style="font-weight: normal; font-size: 11px;">Adjusted Period Balances</span>
            </div>
            <table class="summary-table">
                <tbody>
                    <tr>
                        <td style="font-weight: 700;">Bank Statement Ending Balance</td>
                        <td class="text-right num-mono" style="font-weight: 700; width: 220px;">LKR <?= number_format($stmtClosing, 2); ?></td>
                    </tr>
                    <tr>
                        <td style="padding-left: 28px; color: #16a34a;">+ Add: Deposits in Transit (Unmatched Deposits)</td>
                        <td class="text-right num-mono" style="color: #16a34a;">+ LKR <?= number_format($totalDepositsInTransit, 2); ?></td>
                    </tr>
                    <tr>
                        <td style="padding-left: 28px; color: #dc2626;">- Less: Outstanding Cheques / Payments (Unmatched Payments)</td>
                        <td class="text-right num-mono" style="color: #dc2626;">- LKR <?= number_format($totalOutstandingCheques, 2); ?></td>
                    </tr>
                    <tr class="highlight-row">
                        <td style="color: #0284c7;">Adjusted Bank Balance</td>
                        <td class="text-right num-mono" style="color: #0284c7;">LKR <?= number_format($rec->adjusted_bank_balance, 2); ?></td>
                    </tr>
                    <tr class="highlight-row">
                        <td>General Ledger (Book) Reconciled Balance</td>
                        <td class="text-right num-mono">LKR <?= number_format($rec->adjusted_book_balance, 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Deposits in Transit Table (Mandatory Rule 3: Zebra Striping) -->
        <div>
            <div style="font-size: 13.5px; font-weight: 700; color: #0f172a; margin-bottom: 4px;">
                Deposits in Transit (Unmatched Deposits)
            </div>
            <table class="detail-table">
                <thead>
                    <tr>
                        <th style="width: 100px;">Date</th>
                        <th style="width: 120px;">Reference</th>
                        <th>Description</th>
                        <th class="text-right" style="width: 140px;">Amount (LKR)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($depositsInTransit)): ?>
                        <?php foreach ($depositsInTransit as $item): ?>
                            <tr>
                                <td class="num-mono"><?= date('Y-m-d', strtotime($item->transaction_date)); ?></td>
                                <td class="num-mono" style="font-weight: 600;"><?= htmlspecialchars($item->reference ?? '-'); ?></td>
                                <td>
                                    <?= htmlspecialchars($item->description); ?>
                                    <?php if (!empty($item->is_carried_forward)): ?>
                                        <span style="font-size: 10px; font-weight: 700; color: #b45309; background: #fef3c7; padding: 1px 5px; border-radius: 3px; margin-left: 4px;">Carried Forward</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right num-mono" style="font-weight: 700; color: #16a34a;"><?= number_format($item->debit, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align: center; color: #94a3b8; padding: 12px;">No deposits in transit for this period.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Outstanding Cheques Table (Mandatory Rule 3: Zebra Striping) -->
        <div>
            <div style="font-size: 13.5px; font-weight: 700; color: #0f172a; margin-bottom: 4px;">
                Outstanding Cheques & Payments (Unmatched Payments)
            </div>
            <table class="detail-table">
                <thead>
                    <tr>
                        <th style="width: 100px;">Date</th>
                        <th style="width: 120px;">Cheque / Ref #</th>
                        <th>Description</th>
                        <th class="text-right" style="width: 140px;">Amount (LKR)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($outstandingCheques)): ?>
                        <?php foreach ($outstandingCheques as $item): ?>
                            <tr>
                                <td class="num-mono"><?= date('Y-m-d', strtotime($item->transaction_date)); ?></td>
                                <td class="num-mono" style="font-weight: 600;"><?= htmlspecialchars($item->reference ?? '-'); ?></td>
                                <td>
                                    <?= htmlspecialchars($item->description); ?>
                                    <?php if (!empty($item->is_carried_forward)): ?>
                                        <span style="font-size: 10px; font-weight: 700; color: #b45309; background: #fef3c7; padding: 1px 5px; border-radius: 3px; margin-left: 4px;">Carried Forward</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right num-mono" style="font-weight: 700; color: #dc2626;"><?= number_format($item->credit, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align: center; color: #94a3b8; padding: 12px;">No outstanding cheques or payments.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Signatures -->
        <div class="signature-strip">
            <div class="sig-box">Prepared By (Accountant)</div>
            <div class="sig-box">Checked & Verified By</div>
            <div class="sig-box">Approved By (Director)</div>
        </div>
    </div>
</body>
</html>
