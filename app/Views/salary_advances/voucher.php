<?php
$advance = $data['advance'];
$recoveries = $data['recoveries'] ?? [];
$amountInWords = $data['amount_in_words'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($data['title']) ?></title>
    <!-- Inter Font & FontAwesome Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --f-system: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --f-mono: ui-monospace, 'SF Mono', Menlo, Monaco, Consolas, monospace;
            --c-blue: #007aff;
            --c-text: #1c1c1e;
            --c-muted: #636366;
            --c-border: #e5e5ea;
        }

        body {
            font-family: var(--f-system);
            background-color: #f4f5f7;
            color: var(--c-text);
            padding: 30px 20px;
            margin: 0;
            -webkit-print-color-adjust: exact;
        }

        .print-actions {
            max-width: 820px;
            margin: 0 auto 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            border-radius: 999px;
            font-size: 13.5px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-print { background: var(--c-blue); color: #fff; box-shadow: 0 2px 8px rgba(0,122,255,0.25); }
        .btn-print:hover { background: #0066d6; }
        .btn-back { background: #fff; color: var(--c-text); border: 1px solid var(--c-border); }
        .btn-back:hover { background: #f9f9fb; }

        .voucher-card {
            max-width: 820px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--c-border);
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            padding: 36px 42px;
            box-sizing: border-box;
        }

        /* Header */
        .voucher-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid var(--c-blue);
            padding-bottom: 20px;
            margin-bottom: 24px;
        }
        .company-name { font-size: 24px; font-weight: 800; color: var(--c-blue); letter-spacing: -0.02em; }
        .company-sub { font-size: 12.5px; color: var(--c-muted); margin-top: 3px; }
        .voucher-badge {
            background: #e5f2ff;
            color: var(--c-blue);
            font-size: 13px;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 8px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            text-align: right;
        }
        .voucher-badge .sub { font-size: 11px; font-weight: 500; color: var(--c-muted); margin-top: 2px; }

        /* Grids */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            background: #f9f9fb;
            border-radius: 12px;
            padding: 16px 20px;
            border: 1px solid #efeff4;
            margin-bottom: 24px;
        }
        .info-item { font-size: 13px; line-height: 1.7; }
        .info-item strong { color: var(--c-text); display: inline-block; width: 140px; }

        /* Amount Banner */
        .amount-banner {
            background: #e5f2ff;
            border: 1.5px solid var(--c-blue);
            border-radius: 12px;
            padding: 18px 24px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        .amount-val { font-size: 26px; font-weight: 800; font-family: var(--f-mono); color: var(--c-blue); }
        .amount-words { font-size: 13.5px; font-weight: 600; color: #1e40af; margin-top: 4px; }

        /* Recovery Table */
        .section-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--c-muted);
            margin: 24px 0 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .recovery-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            border: 1px solid var(--c-border);
            border-radius: 8px;
            overflow: hidden;
        }
        .recovery-table th {
            background: #f2f2f7;
            padding: 10px 14px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--c-muted);
            text-align: left;
            border-bottom: 1px solid var(--c-border);
        }
        .recovery-table td {
            padding: 10px 14px;
            font-size: 13px;
            border-bottom: 1px solid #f2f2f7;
        }
        .recovery-table tr:last-child td { border-bottom: none; }

        /* Signatures */
        .sign-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 30px;
            margin-top: 55px;
            padding-top: 20px;
            text-align: center;
        }
        .sign-line {
            border-top: 1px dashed #aeaeb2;
            padding-top: 8px;
            font-size: 12px;
            font-weight: 600;
            color: var(--c-muted);
        }

        @media print {
            body { background: #fff; padding: 0; }
            .print-actions { display: none; }
            .voucher-card { border: none; box-shadow: none; padding: 0; }
        }
    </style>
</head>
<body>

<div class="print-actions">
    <a href="<?= APP_URL ?>/salaryadvance" class="btn btn-back">
        <i class="fa-solid fa-arrow-left"></i> Back to Advances
    </a>
    <button onclick="window.print()" class="btn btn-print">
        <i class="fa-solid fa-print"></i> Print Voucher
    </button>
</div>

<div class="voucher-card">
    
    <div class="voucher-header">
        <div>
            <div class="company-name">Falcon Stationery ERP</div>
            <div class="company-sub">Personnel &amp; Operational Management &bull; Official Payment Voucher</div>
        </div>
        <div class="voucher-badge">
            Salary Advance
            <div class="sub"><?= htmlspecialchars($advance->advance_number) ?></div>
        </div>
    </div>

    <!-- Info Grid -->
    <div class="info-grid">
        <div class="info-item">
            <div><strong>Employee Name:</strong> <?= htmlspecialchars($advance->first_name . ' ' . $advance->last_name) ?></div>
            <div><strong>Employee Code:</strong> <?= htmlspecialchars($advance->employee_code ?? 'N/A') ?></div>
            <div><strong>Department:</strong> <?= htmlspecialchars($advance->department ?? 'General') ?></div>
            <div><strong>Job Title:</strong> <?= htmlspecialchars($advance->job_title ?? 'Staff') ?></div>
        </div>
        <div class="info-item">
            <div><strong>Payment Date:</strong> <?= date('d M Y', strtotime($advance->payment_date)) ?></div>
            <div><strong>Payment Method:</strong> <?= htmlspecialchars($advance->payment_method) ?></div>
            <div><strong>Journal Ref:</strong> JE-<?= htmlspecialchars($advance->advance_number) ?></div>
            <?php if(!empty($advance->petty_cash_voucher_number)): ?>
                <div><strong>Petty Cash Voucher:</strong> <?= htmlspecialchars($advance->petty_cash_voucher_number) ?></div>
            <?php endif; ?>
            <?php if(!empty($advance->bank_name)): ?>
                <div><strong>Bank Account:</strong> <?= htmlspecialchars($advance->bank_name . ' - ' . $advance->account_number) ?></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Amount Banner -->
    <div class="amount-banner">
        <div>
            <div style="font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--c-muted);">Advance Principal Amount</div>
            <div class="amount-val">Rs. <?= number_format(floatval($advance->advance_amount), 2) ?></div>
            <div class="amount-words"><i class="fa-solid fa-spell-check"></i> <?= htmlspecialchars($amountInWords) ?></div>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--c-muted);">Outstanding Balance</div>
            <div style="font-size: 22px; font-weight: 800; font-family: var(--f-mono); color: <?= floatval($advance->outstanding_amount) > 0 ? '#ff3b30' : '#34c759' ?>;">
                Rs. <?= number_format(floatval($advance->outstanding_amount), 2) ?>
            </div>
            <div style="font-size: 12px; color: var(--c-muted); margin-top: 2px;">
                Status: <strong><?= htmlspecialchars($advance->status) ?></strong>
            </div>
        </div>
    </div>

    <!-- Accounting Classification -->
    <div style="background: #f9f9fb; border-radius: 10px; padding: 14px 18px; border: 1px solid var(--c-border); margin-bottom: 24px; font-size: 13px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
            <span><strong>Debit Account (Asset Receivable):</strong></span>
            <span><?= htmlspecialchars($advance->advance_account_code . ' - ' . $advance->advance_account_name) ?></span>
        </div>
        <div style="display: flex; justify-content: space-between;">
            <span><strong>Credit Account (Disbursement Source):</strong></span>
            <span><?= htmlspecialchars($advance->source_account_code . ' - ' . $advance->source_account_name) ?></span>
        </div>
        <?php if(!empty($advance->notes)): ?>
            <div style="margin-top: 10px; padding-top: 8px; border-top: 1px solid var(--c-border); color: var(--c-muted);">
                <strong>Notes / Reason:</strong> <?= htmlspecialchars($advance->notes) ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Recovery History (If Any) -->
    <?php if(!empty($recoveries)): ?>
        <div class="section-title"><i class="fa-solid fa-clock-rotate-left"></i> Payroll Recovery History</div>
        <table class="recovery-table">
            <thead>
                <tr>
                    <th>Recovery Date</th>
                    <th>Payroll Period</th>
                    <th>Recovered By</th>
                    <th>Notes</th>
                    <th style="text-align: right;">Amount Recovered</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($recoveries as $rec): ?>
                    <tr>
                        <td><?= date('d M Y', strtotime($rec->recovery_date)) ?></td>
                        <td>
                            <?php if(!empty($rec->period_start)): ?>
                                <?= date('d M', strtotime($rec->period_start)) ?> &ndash; <?= date('d M Y', strtotime($rec->period_end)) ?>
                            <?php else: ?>
                                Manual / Adjustment
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($rec->created_by_name ?? 'System') ?></td>
                        <td><?= htmlspecialchars($rec->notes ?? 'Payroll Deduction') ?></td>
                        <td style="text-align: right; font-weight: 700; font-family: var(--f-mono); color: #34c759;">
                            Rs. <?= number_format(floatval($rec->amount), 2) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- Signature Footer -->
    <div class="sign-row">
        <div>
            <div style="font-size: 12px; margin-bottom: 4px;"><?= htmlspecialchars($advance->created_by_name ?? 'Staff') ?></div>
            <div class="sign-line">Prepared By</div>
        </div>
        <div>
            <div style="font-size: 12px; margin-bottom: 4px;"><?= htmlspecialchars($advance->approved_by_name ?? 'Management') ?></div>
            <div class="sign-line">Authorized Approval</div>
        </div>
        <div>
            <div style="font-size: 12px; margin-bottom: 4px;"><?= htmlspecialchars($advance->first_name . ' ' . $advance->last_name) ?></div>
            <div class="sign-line">Employee Signature &amp; Date</div>
        </div>
    </div>

</div>

</body>
</html>

