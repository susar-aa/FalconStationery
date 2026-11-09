<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - <?= htmlspecialchars($data['payment']->reference ?: 'REC-' . $data['payment']->id) ?> - Curtiss ERP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-page: #f1f5f9;
            --bg-card: #ffffff;
            --t-primary: #0f172a;
            --t-secondary: #475569;
            --t-muted: #64748b;
            --border: #e2e8f0;
            --c-brand: #4f46e5;
            --c-brand-light: #eef2ff;
            --c-success: #059669;
            --c-success-light: #ecfdf5;
            --c-danger: #dc2626;
            --c-danger-light: #fef2f2;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: var(--bg-page);
            color: var(--t-primary);
            font-size: 13.5px;
            line-height: 1.5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 28px 16px;
            -webkit-font-smoothing: antialiased;
        }

        /* Top Action Bar (Screen Only) */
        .action-bar {
            width: 100%;
            max-width: 800px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
            gap: 12px;
        }

        .bar-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .badge-preview {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #e0e7ff;
            color: #4338ca;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        .bar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            text-decoration: none;
            border: 1px solid transparent;
        }

        .btn-primary {
            background: var(--c-brand);
            color: #ffffff;
            box-shadow: 0 1px 3px rgba(79, 70, 229, 0.3);
        }

        .btn-primary:hover {
            background: #4338ca;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.35);
        }

        .btn-secondary {
            background: #ffffff;
            color: var(--t-secondary);
            border-color: var(--border);
        }

        .btn-secondary:hover {
            background: #f8fafc;
            color: var(--t-primary);
        }

        /* Receipt Card Container */
        .receipt-card {
            width: 100%;
            max-width: 800px;
            background: var(--bg-card);
            border-radius: 14px;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.07), 0 2px 6px -1px rgba(15, 23, 42, 0.04);
            border: 1px solid var(--border);
            padding: 36px 42px;
            margin-bottom: 28px;
            transition: all 0.2s;
        }

        /* Header */
        .receipt-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 22px;
            border-bottom: 1.5px solid var(--border);
            margin-bottom: 24px;
        }

        .company-info h2 {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 0.02em;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .company-info p {
            font-size: 12px;
            color: var(--t-muted);
            line-height: 1.45;
        }

        .receipt-title-box {
            text-align: right;
        }

        .receipt-title-box h1 {
            font-size: 20px;
            font-weight: 800;
            color: var(--c-brand);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }

        .receipt-meta {
            font-size: 12.5px;
            color: var(--t-secondary);
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .receipt-meta .ref-num {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-weight: 700;
            color: var(--t-primary);
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 32px;
            margin-bottom: 26px;
        }

        .info-card {
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 16px 18px;
        }

        .info-card h3 {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--t-muted);
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px dashed var(--border);
        }

        .info-card .party-name {
            font-size: 15px;
            font-weight: 700;
            color: var(--t-primary);
            margin-bottom: 4px;
        }

        .info-card .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            font-size: 12.5px;
            margin-bottom: 5px;
            line-height: 1.4;
        }

        .info-card .detail-row:last-child {
            margin-bottom: 0;
        }

        .detail-row .lbl {
            color: var(--t-muted);
            min-width: 95px;
        }

        .detail-row .val {
            color: var(--t-primary);
            font-weight: 600;
            text-align: right;
            word-break: break-word;
        }

        .badge-status {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-active {
            background: var(--c-success-light);
            color: var(--c-success);
        }

        .badge-reversed {
            background: var(--c-danger-light);
            color: var(--c-danger);
        }

        /* Amount Highlight Box */
        .amount-card {
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
            border: 1.5px solid #a7f3d0;
            border-radius: 12px;
            padding: 18px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
        }

        .amount-card .amount-label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #065f46;
        }

        .amount-card .amount-value {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 26px;
            font-weight: 800;
            color: #047857;
        }

        /* Table */
        .section-heading {
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--t-muted);
            margin-bottom: 10px;
        }

        .allocations-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 24px;
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
        }

        .allocations-table th {
            background: #f8fafc;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: var(--t-secondary);
            padding: 10px 14px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .allocations-table td {
            padding: 10px 14px;
            font-size: 12.5px;
            border-bottom: 1px solid var(--border);
            color: var(--t-primary);
        }

        .allocations-table tr:last-child td {
            border-bottom: none;
        }

        .allocations-table .num {
            text-align: right;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-weight: 600;
        }

        .unallocated-row td {
            background: #f8fafc;
            font-weight: 600;
        }

        /* Notes Box */
        .notes-card {
            background: #f8fafc;
            border: 1px solid var(--border);
            border-left: 3px solid var(--c-brand);
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 32px;
            font-size: 12px;
        }

        .notes-card strong {
            font-size: 11px;
            text-transform: uppercase;
            color: var(--t-muted);
            letter-spacing: 0.03em;
            display: block;
            margin-bottom: 3px;
        }

        /* Signatures */
        .signatures-grid {
            display: flex;
            justify-content: space-between;
            margin-top: 48px;
            padding-top: 10px;
        }

        .sig-box {
            width: 220px;
            text-align: center;
        }

        .sig-line {
            border-top: 1.5px solid #94a3b8;
            padding-top: 8px;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--t-secondary);
        }

        /* Print Media Styles */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .action-bar, .no-print {
                display: none !important;
            }

            .receipt-card {
                border: none !important;
                box-shadow: none !important;
                max-width: 100% !important;
                padding: 20px 24px !important;
                margin: 0 !important;
                border-radius: 0 !important;
            }

            .amount-card {
                background: #f0fdf4 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .info-card {
                background: #f8fafc !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .allocations-table th {
                background: #f8fafc !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <!-- Action Toolbar (Screen Only) -->
    <div class="action-bar no-print">
        <div class="bar-left">
            <span class="badge-preview">
                <i class="fa-solid fa-file-invoice"></i> Payment Receipt Preview
            </span>
        </div>
        <div class="bar-actions">
            <button onclick="window.close()" class="btn btn-secondary">
                <i class="fa-solid fa-xmark"></i> Close
            </button>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fa-solid fa-print"></i> Print Receipt
            </button>
        </div>
    </div>

    <!-- Receipt Card (Standard Centered Container) -->
    <div class="receipt-card">
        <!-- Header -->
        <div class="receipt-header">
            <div class="company-info">
                <h2>CURTISS ENTERPRISES</h2>
                <p>123 Business Avenue, Colombo, Sri Lanka<br>Phone: +94 11 2345678 | Email: finance@curtiss.com</p>
            </div>
            <div class="receipt-title-box">
                <h1>Payment Receipt</h1>
                <div class="receipt-meta">
                    <div>Receipt #: <span class="ref-num">REC-PAY-<?= str_pad($data['payment']->id, 5, '0', STR_PAD_LEFT) ?></span></div>
                    <div>Date: <span class="ref-num"><?= date('Y-m-d', strtotime($data['payment']->payment_date)) ?></span></div>
                </div>
            </div>
        </div>

        <!-- 2-Column Info Grid -->
        <div class="info-grid">
            <!-- Received From -->
            <div class="info-card">
                <h3>Received From</h3>
                <div class="party-name"><?= htmlspecialchars($data['payment']->customer_name) ?></div>
                <div style="font-size: 12px; color: var(--t-secondary); margin-bottom: 8px; line-height: 1.4;">
                    <?= htmlspecialchars($data['payment']->customer_address ?: 'No Address Stated') ?>
                </div>
                <div class="detail-row">
                    <span class="lbl">Phone:</span>
                    <span class="val"><?= htmlspecialchars($data['payment']->customer_phone ?: 'N/A') ?></span>
                </div>
                <?php if ($data['payment']->customer_email): ?>
                    <div class="detail-row">
                        <span class="lbl">Email:</span>
                        <span class="val"><?= htmlspecialchars($data['payment']->customer_email) ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Payment Information -->
            <div class="info-card">
                <h3>Payment Information</h3>
                <div class="detail-row">
                    <span class="lbl">Payment Method:</span>
                    <span class="val" style="color: var(--c-brand); font-weight: 700;"><?= htmlspecialchars($data['payment']->payment_method) ?></span>
                </div>

                <?php if ($data['payment']->payment_method === 'Bank Transfer'): ?>
                    <?php if (!empty($data['payment']->transfer_bank_name)): ?>
                        <div class="detail-row">
                            <span class="lbl">Transferred Bank:</span>
                            <span class="val" style="color: #1e3a8a; font-weight: 700;"><?= htmlspecialchars($data['payment']->transfer_bank_name) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($data['payment']->transfer_account_number)): ?>
                        <div class="detail-row">
                            <span class="lbl">Account Number:</span>
                            <span class="val" style="font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;"><?= htmlspecialchars($data['payment']->transfer_account_number) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($data['payment']->transfer_branch_name)): ?>
                        <div class="detail-row">
                            <span class="lbl">Branch:</span>
                            <span class="val"><?= htmlspecialchars($data['payment']->transfer_branch_name) ?></span>
                        </div>
                    <?php endif; ?>
                <?php elseif ($data['payment']->payment_method === 'Cheque'): ?>
                    <?php if (!empty($data['payment']->cheque_number)): ?>
                        <div class="detail-row">
                            <span class="lbl">Cheque Number:</span>
                            <span class="val" style="font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-weight: 700;"><?= htmlspecialchars($data['payment']->cheque_number) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($data['payment']->cheque_bank)): ?>
                        <div class="detail-row">
                            <span class="lbl">Cheque Bank:</span>
                            <span class="val"><?= htmlspecialchars($data['payment']->cheque_bank) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($data['payment']->cheque_date)): ?>
                        <div class="detail-row">
                            <span class="lbl">Banking Date:</span>
                            <span class="val"><?= htmlspecialchars($data['payment']->cheque_date) ?></span>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($data['payment']->reference): ?>
                    <div class="detail-row">
                        <span class="lbl">Reference #:</span>
                        <span class="val"><?= htmlspecialchars($data['payment']->reference) ?></span>
                    </div>
                <?php endif; ?>

                <div class="detail-row">
                    <span class="lbl">Payment Status:</span>
                    <span class="val">
                        <span class="badge-status <?= $data['payment']->status === 'Reversed' ? 'badge-reversed' : 'badge-active' ?>">
                            <?= htmlspecialchars($data['payment']->status) ?>
                        </span>
                    </span>
                </div>

                <?php if (!empty($data['payment']->journal_entry_id)): ?>
                    <div class="detail-row">
                        <span class="lbl">Posted Entry:</span>
                        <span class="val" style="font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;">JE-<?= str_pad($data['payment']->journal_entry_id, 5, '0', STR_PAD_LEFT) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Amount Box -->
        <div class="amount-card">
            <div class="amount-label">
                <i class="fa-solid fa-circle-check" style="margin-right: 4px;"></i> Total Amount Received
            </div>
            <div class="amount-value">
                Rs: <?= number_format($data['payment']->amount, 2) ?>
            </div>
        </div>

        <!-- Allocation Breakdown -->
        <div class="section-heading">Document Allocation Breakdown</div>
        <table class="allocations-table">
            <thead>
                <tr>
                    <th>Invoice Number</th>
                    <th>Invoice Date</th>
                    <th style="text-align: right;">Allocated Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data['allocations'])): ?>
                    <tr>
                        <td colspan="3" style="text-align: center; color: var(--t-muted); font-style: italic; padding: 22px;">
                            No direct invoice allocation. Paid as Advance Credit.
                        </td>
                    </tr>
                <?php else: foreach ($data['allocations'] as $alloc): ?>
                    <tr>
                        <td><strong style="color: var(--c-brand);"><?= htmlspecialchars($alloc->invoice_number) ?></strong></td>
                        <td style="color: var(--t-secondary);"><?= date('Y-m-d', strtotime($alloc->invoice_date)) ?></td>
                        <td class="num" style="color: #047857;">Rs: <?= number_format($alloc->amount, 2) ?></td>
                    </tr>
                <?php endforeach; endif; ?>

                <?php if ($data['payment']->unallocated_amount > 0.01): ?>
                    <tr class="unallocated-row">
                        <td colspan="2" style="text-align: right; color: var(--t-secondary);">Remaining Advance Balance (Unallocated)</td>
                        <td class="num" style="color: var(--c-brand);">Rs: <?= number_format($data['payment']->unallocated_amount, 2) ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Notes / Memo -->
        <?php if (!empty($data['payment']->notes)): ?>
            <div class="notes-card">
                <strong>Memo / Payment Notes</strong>
                <p style="color: var(--t-secondary);"><?= nl2br(htmlspecialchars($data['payment']->notes)) ?></p>
            </div>
        <?php endif; ?>

        <!-- Signatures -->
        <div class="signatures-grid">
            <div class="sig-box">
                <div class="sig-line">Prepared By (<?= htmlspecialchars($data['payment']->creator_name ?: 'Accounts Officer') ?>)</div>
            </div>
            <div class="sig-box">
                <div class="sig-line">Authorized Signatory</div>
            </div>
        </div>
    </div>

</body>
</html>