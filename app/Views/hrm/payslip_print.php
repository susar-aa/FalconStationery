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
            --f-system: 'Inter', -apple-system, sans-serif;
            --f-mono: ui-monospace, 'SF Mono', monospace;
        }
        body {
            font-family: var(--f-system);
            background-color: #f4f5f7;
            color: #1c1c1e;
            padding: 30px 20px;
            margin: 0;
            -webkit-print-color-adjust: exact;
        }
        .payslip-card {
            max-width: 820px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e5e5ea;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            padding: 36px 40px;
        }
        .print-bar {
            max-width: 820px;
            margin: 0 auto 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-print {
            padding: 9px 18px;
            background: #007aff;
            color: #fff;
            border: none;
            border-radius: 999px;
            font-weight: 600;
            font-size: 13.5px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 8px rgba(0,122,255,0.25);
        }
        .btn-print:hover { background: #0066d6; }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #007aff;
            padding-bottom: 20px;
            margin-bottom: 24px;
        }
        .company-title { font-size: 24px; font-weight: 800; color: #007aff; letter-spacing: -0.02em; }
        .company-sub { font-size: 12.5px; color: #636366; margin-top: 2px; }
        .slip-badge {
            background: #e5f2ff;
            color: #007aff;
            font-size: 13px;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 8px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .emp-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            background: #f9f9fb;
            border-radius: 12px;
            padding: 16px 20px;
            border: 1px solid #efeff4;
            margin-bottom: 24px;
        }
        .emp-item { font-size: 13px; line-height: 1.6; }
        .emp-item strong { color: #1c1c1e; display: inline-block; width: 130px; }

        .tables-split {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .section-box {
            border: 1px solid #e5e5ea;
            border-radius: 12px;
            overflow: hidden;
        }
        .section-header {
            background: #f2f2f7;
            padding: 10px 14px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #636366;
            border-bottom: 1px solid #e5e5ea;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
        }
        .detail-table td {
            padding: 10px 14px;
            font-size: 13px;
            border-bottom: 1px solid #f2f2f7;
        }
        .detail-table tr:last-child td { border-bottom: none; }
        .num-col { text-align: right; font-family: var(--f-mono); font-weight: 600; }

        .net-pay-banner {
            background: #e6f9ec;
            border: 1.5px solid #34c759;
            border-radius: 12px;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 24px 0;
        }
        .net-pay-title { font-size: 15px; font-weight: 700; color: #1c1c1e; }
        .net-pay-amount { font-size: 22px; font-weight: 800; color: #34c759; font-family: var(--f-mono); }

        .sign-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 50px;
            padding-top: 20px;
            text-align: center;
        }
        .sign-line {
            border-top: 1px dashed #aeaeb2;
            padding-top: 8px;
            font-size: 12px;
            font-weight: 600;
            color: #636366;
        }

        @media print {
            body { background-color: #fff; padding: 0; }
            .print-bar { display: none; }
            .payslip-card { border: none; box-shadow: none; padding: 0; }
        }
    </style>
</head>
<body>

<div class="print-bar">
    <div style="font-size: 13.5px; font-weight: 600; color: #636366;">Official Payslip Voucher</div>
    <button onclick="window.print()" class="btn-print"><i class="fa-solid fa-print"></i> Print Payslip</button>
</div>

<?php $slip = $data['slip']; ?>

<div class="payslip-card">
    
    <div class="header">
        <div>
            <div class="company-title">Falcon ERP Personnel Payroll</div>
            <div class="company-sub">Pay Period: <?= date('d M', strtotime($slip->period_start)) ?> &ndash; <?= date('d M Y', strtotime($slip->period_end)) ?></div>
        </div>
        <div class="slip-badge">Payslip PS-<?= $slip->id ?></div>
    </div>

    <div class="emp-grid">
        <div class="emp-item">
            <div><strong>Employee Name:</strong> <?= htmlspecialchars($slip->first_name . ' ' . $slip->last_name) ?></div>
            <div><strong>Job Title:</strong> <?= htmlspecialchars($slip->job_title ?: 'Staff') ?></div>
            <div><strong>Department:</strong> <?= htmlspecialchars($slip->department ?: 'General') ?></div>
        </div>
        <div class="emp-item">
            <div><strong>EPF Member No:</strong> <?= htmlspecialchars($slip->epf_member_no ?? 'N/A') ?></div>
            <div><strong>Run Status:</strong> <?= htmlspecialchars($slip->status) ?></div>
            <?php if(!empty($slip->payment_date)): ?>
                <div><strong>Payment Date:</strong> <?= date('d M Y', strtotime($slip->payment_date)) ?></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="tables-split">
        <!-- Earnings -->
        <div class="section-box">
            <div class="section-header"><i class="fa-solid fa-plus-circle" style="color: #34c759; margin-right: 4px;"></i> Earnings</div>
            <table class="detail-table">
                <tbody>
                    <tr><td>Basic Salary</td><td class="num-col">Rs. <?= number_format($slip->basic_salary, 2) ?></td></tr>
                    <tr><td>Allowances</td><td class="num-col">Rs. <?= number_format($slip->allowances, 2) ?></td></tr>
                    <tr><td>Commissions</td><td class="num-col">Rs. <?= number_format($slip->commissions, 2) ?></td></tr>
                    <tr><td>Overtime Pay</td><td class="num-col">Rs. <?= number_format($slip->overtime, 2) ?></td></tr>
                    <tr style="background: #f9f9fb; font-weight: 700;">
                        <td>Gross Earnings</td>
                        <td class="num-col" style="color: #007aff;">Rs. <?= number_format($slip->gross_salary, 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Deductions -->
        <div class="section-box">
            <div class="section-header"><i class="fa-solid fa-minus-circle" style="color: #ff3b30; margin-right: 4px;"></i> Deductions</div>
            <table class="detail-table">
                <tbody>
                    <tr><td>EPF Employee (8%)</td><td class="num-col" style="color: #ff3b30;">Rs. <?= number_format($slip->epf_employee ?? 0, 2) ?></td></tr>
                    <tr><td>Loan Repayments</td><td class="num-col" style="color: #ff3b30;">Rs. <?= number_format($slip->loan_deduction, 2) ?></td></tr>
                    <tr><td>Other Deductions</td><td class="num-col" style="color: #ff3b30;">Rs. <?= number_format($slip->other_deductions, 2) ?></td></tr>
                    <tr style="background: #f9f9fb; font-weight: 700;">
                        <td>Total Deductions</td>
                        <td class="num-col" style="color: #ff3b30;">Rs. <?= number_format(($slip->epf_employee ?? 0) + $slip->loan_deduction + $slip->other_deductions, 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Statutory Employer Contributions (Informational) -->
    <div class="section-box" style="margin-bottom: 20px;">
        <div class="section-header"><i class="fa-solid fa-building-columns" style="color: #af52de; margin-right: 4px;"></i> Employer Statutory Contributions (Not Deducted From Net Pay)</div>
        <table class="detail-table">
            <tbody>
                <tr><td>EPF Employer Contribution (12%)</td><td class="num-col" style="color: #ff9500;">Rs. <?= number_format($slip->epf_employer ?? 0, 2) ?></td></tr>
                <tr><td>ETF Employer Contribution (3%)</td><td class="num-col" style="color: #af52de;">Rs. <?= number_format($slip->etf_employer ?? 0, 2) ?></td></tr>
            </tbody>
        </table>
    </div>

    <!-- Net Pay Banner -->
    <div class="net-pay-banner">
        <div class="net-pay-title"><i class="fa-solid fa-wallet" style="margin-right: 6px;"></i> Net Payable Salary</div>
        <div class="net-pay-amount">Rs. <?= number_format($slip->net_salary, 2) ?></div>
    </div>
    
    <!-- Signature Footer -->
    <div class="sign-row">
        <div>
            <div class="sign-line">Employer / Authorized Signature</div>
        </div>
        <div>
            <div class="sign-line">Employee Signature &amp; Date</div>
        </div>
    </div>

</div>

</body>
</html>
