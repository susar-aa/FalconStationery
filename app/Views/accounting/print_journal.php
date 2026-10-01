<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journal Voucher - <?= htmlspecialchars($data['entry']->reference ?: 'JV-'.$data['entry']->id) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0f172a;
            --accent-color: #0284c7;
            --border-color: #cbd5e1;
            --bg-light: #f8fafc;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 13px;
            color: #1e293b;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
            background-color: #f1f5f9;
        }

        .no-print-bar {
            max-width: 850px;
            margin: 0 auto 15px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            border: 1px solid var(--border-color);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
        }

        .btn-primary { background: #0284c7; color: #ffffff; }
        .btn-primary:hover { background: #0369a1; }
        .btn-secondary { background: #e2e8f0; color: #334155; }
        .btn-secondary:hover { background: #cbd5e1; }

        .voucher-card {
            max-width: 850px;
            margin: 0 auto;
            background: #ffffff;
            padding: 36px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: 1px solid var(--border-color);
            box-sizing: border-box;
        }

        .voucher-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 20px;
            border-bottom: 2px solid #0f172a;
            margin-bottom: 24px;
        }

        .company-brand h2 {
            margin: 0 0 4px 0;
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
        }

        .company-brand p {
            margin: 0;
            font-size: 12px;
            color: #64748b;
        }

        .voucher-title-box {
            text-align: right;
        }

        .voucher-title-box h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            color: #0284c7;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .voucher-ref {
            font-size: 14px;
            font-weight: 700;
            color: #334155;
            margin-top: 4px;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            background: var(--bg-light);
            padding: 16px 20px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            margin-bottom: 24px;
        }

        .meta-item label {
            display: block;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.05em;
            margin-bottom: 3px;
        }

        .meta-item span {
            font-size: 13px;
            font-weight: 600;
            color: #0f172a;
        }

        .badge-status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-posted { background: #dcfce7; color: #166534; }
        .status-voided { background: #fee2e2; color: #991b1b; }
        .status-draft  { background: #fef3c7; color: #92400e; }

        .lines-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .lines-table th {
            background: #f1f5f9;
            color: #334155;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 10px 14px;
            border-top: 1px solid var(--border-color);
            border-bottom: 2px solid var(--border-color);
            text-align: left;
        }

        .lines-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
            font-size: 13px;
        }

        .lines-table tfoot tr td {
            padding: 12px 14px;
            border-top: 2px solid #0f172a;
            border-bottom: 2px solid #0f172a;
            font-weight: 700;
            background: #f8fafc;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .entity-tag {
            display: inline-block;
            font-size: 11px;
            color: #0284c7;
            font-weight: 600;
            margin-top: 2px;
        }

        .signature-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-top: 60px;
            padding-top: 10px;
        }

        .signature-box {
            text-align: center;
        }

        .signature-line {
            border-bottom: 1px dashed #94a3b8;
            height: 40px;
            margin-bottom: 8px;
        }

        .signature-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #475569;
            letter-spacing: 0.05em;
        }

        .voucher-footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #94a3b8;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .no-print-bar {
                display: none !important;
            }
            .voucher-card {
                box-shadow: none;
                border: none;
                padding: 0;
                max-width: 100%;
            }
            @page {
                size: A4 portrait;
                margin: 15mm;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <div style="font-weight: 600; color: #334155;">
            <i class="fa-solid fa-print" style="color: #0284c7; margin-right: 6px;"></i> Print Journal Voucher
        </div>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fa-solid fa-print"></i> Print Voucher
            </button>
            <button onclick="window.close()" class="btn btn-secondary">
                <i class="fa-solid fa-xmark"></i> Close
            </button>
        </div>
    </div>

    <div class="voucher-card">
        <!-- Header -->
        <div class="voucher-header">
            <div class="company-brand">
                <h2>FALCON STATIONERY</h2>
                <p>Enterprise Resource Planning System</p>
                <p style="margin-top:2px;">Financial Accounting & Journal System</p>
            </div>
            <div class="voucher-title-box">
                <h1>JOURNAL VOUCHER</h1>
                <div class="voucher-ref">Ref: <?= htmlspecialchars($data['entry']->reference ?: 'JV-'.$data['entry']->id) ?></div>
            </div>
        </div>

        <!-- Metadata -->
        <div class="meta-grid">
            <div class="meta-item">
                <label>Voucher Date</label>
                <span><?= date('F d, Y', strtotime($data['entry']->entry_date)) ?></span>
            </div>
            <div class="meta-item">
                <label>Status</label>
                <div>
                    <?php 
                    $st = $data['entry']->status ?? 'Posted';
                    $cls = $st === 'Posted' ? 'status-posted' : ($st === 'Voided' ? 'status-voided' : 'status-draft');
                    ?>
                    <span class="badge-status <?= $cls ?>"><?= htmlspecialchars($st) ?></span>
                </div>
            </div>
            <div class="meta-item">
                <label>Posted By</label>
                <span><?= htmlspecialchars($data['entry']->username ?: 'System User') ?></span>
            </div>
            <div class="meta-item">
                <label>System Entry ID</label>
                <span>#<?= intval($data['entry']->id) ?></span>
            </div>
            <div class="meta-item" style="grid-column: span 4;">
                <label>Description / Particulars</label>
                <span style="font-size: 13.5px; font-weight: 500; color: #0f172a;"><?= htmlspecialchars($data['entry']->description ?: 'N/A') ?></span>
            </div>
        </div>

        <!-- Double Entry Lines Table -->
        <table class="lines-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 45%;">Account Code & Description</th>
                    <th style="width: 25%;">Memo / Particulars</th>
                    <th style="width: 12.5%;" class="text-right">Debit (Rs)</th>
                    <th style="width: 12.5%;" class="text-right">Credit (Rs)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $totDebit = 0.0;
                $totCredit = 0.0;
                $idx = 1;
                foreach ($data['lines'] as $line):
                    $d = floatval($line->debit);
                    $c = floatval($line->credit);
                    $totDebit += $d;
                    $totCredit += $c;
                    $entityName = '';
                    if ($line->entity_type) {
                        $entityName = $line->customer_name ?: ($line->vendor_name ?: 'ID: ' . $line->entity_id);
                    }
                ?>
                <tr>
                    <td class="text-center" style="color: #64748b; font-weight: 600;"><?= $idx++ ?></td>
                    <td>
                        <strong style="color: #0f172a;"><?= htmlspecialchars($line->account_code) ?></strong> - <?= htmlspecialchars($line->account_name) ?>
                        <?php if(!empty($entityName)): ?>
                            <br><span class="entity-tag"><i class="fa-solid fa-user-tag"></i> <?= htmlspecialchars($line->entity_type) ?>: <?= htmlspecialchars($entityName) ?></span>
                        <?php endif; ?>
                    </td>
                    <td style="color: #475569;"><?= htmlspecialchars($line->description ?: '-') ?></td>
                    <td class="text-right" style="font-family: monospace; font-size: 13px; font-weight: 600; color: #0f172a;">
                        <?= $d > 0 ? number_format($d, 2) : '-' ?>
                    </td>
                    <td class="text-right" style="font-family: monospace; font-size: 13px; font-weight: 600; color: #0f172a;">
                        <?= $c > 0 ? number_format($c, 2) : '-' ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right" style="font-size: 12px; text-transform: uppercase; color: #475569;">Total Amount</td>
                    <td class="text-right" style="font-family: monospace; font-size: 14px; color: #0f172a;"><?= number_format($totDebit, 2) ?></td>
                    <td class="text-right" style="font-family: monospace; font-size: 14px; color: #0f172a;"><?= number_format($totCredit, 2) ?></td>
                </tr>
            </tfoot>
        </table>

        <!-- Signatures -->
        <div class="signature-grid">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-title">Prepared By</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-title">Checked / Verified By</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-title">Authorized / Approved By</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="voucher-footer">
            <span>Generated on: <?= date('Y-m-d H:i:s') ?></span>
            <span>Falcon ERP System — Journal Voucher</span>
        </div>
    </div>

    <script>
        // Auto trigger print dialog on page load
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 300);
        });
    </script>
</body>
</html>
