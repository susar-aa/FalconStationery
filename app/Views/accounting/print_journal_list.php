<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journal Entries Audit Report - Falcon ERP</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', -apple-system, sans-serif;
            font-size: 12px;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            background-color: #f1f5f9;
        }

        .no-print-bar {
            max-width: 1000px;
            margin: 0 auto 15px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            border: 1px solid #cbd5e1;
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
        .btn-secondary { background: #e2e8f0; color: #334155; }

        .report-card {
            max-width: 1000px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: 1px solid #cbd5e1;
            box-sizing: border-box;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 15px;
            border-bottom: 2px solid #0f172a;
            margin-bottom: 20px;
        }

        .header h2 { margin: 0; font-size: 20px; color: #0f172a; font-weight: 800; }
        .header p { margin: 2px 0 0 0; color: #64748b; font-size: 12px; }

        .report-title { text-align: right; }
        .report-title h1 { margin: 0; font-size: 18px; color: #0284c7; text-transform: uppercase; font-weight: 800; }

        .filter-bar {
            background: #f8fafc;
            padding: 10px 14px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            margin-bottom: 20px;
            font-size: 11.5px;
            color: #475569;
            display: flex;
            gap: 20px;
        }

        .list-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .list-table th {
            background: #f1f5f9;
            color: #334155;
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 8px 10px;
            border-top: 1px solid #cbd5e1;
            border-bottom: 2px solid #cbd5e1;
            text-align: left;
        }

        .list-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12px;
        }

        .badge-status {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-posted { background: #dcfce7; color: #166534; }
        .status-voided { background: #fee2e2; color: #991b1b; }
        .status-draft  { background: #fef3c7; color: #92400e; }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #94a3b8;
        }

        @media print {
            body { background: #fff; padding: 0; }
            .no-print-bar { display: none !important; }
            .report-card { box-shadow: none; border: none; padding: 0; max-width: 100%; }
            @page { size: A4 landscape; margin: 10mm; }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <div style="font-weight: 600; color: #334155;">
            <i class="fa-solid fa-print" style="color: #0284c7; margin-right: 6px;"></i> Print Journal Entries List
        </div>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" class="btn btn-primary"><i class="fa-solid fa-print"></i> Print List</button>
            <button onclick="window.close()" class="btn btn-secondary"><i class="fa-solid fa-xmark"></i> Close</button>
        </div>
    </div>

    <div class="report-card">
        <div class="header">
            <div>
                <h2>FALCON STATIONERY</h2>
                <p>Accounting Journal Entries Report</p>
            </div>
            <div class="report-title">
                <h1>JOURNAL REGISTER</h1>
                <p style="margin-top:3px; color: #64748b;">Total Entries: <?= count($data['entries']) ?></p>
            </div>
        </div>

        <div class="filter-bar">
            <div><strong>Status:</strong> <?= htmlspecialchars($data['filters']['status'] ?? 'All') ?></div>
            <div><strong>Search:</strong> <?= htmlspecialchars($data['filters']['search'] ?? 'None') ?></div>
            <div><strong>Date Range:</strong> <?= !empty($data['filters']['start_date']) ? $data['filters']['start_date'] : 'Beginning' ?> to <?= !empty($data['filters']['end_date']) ? $data['filters']['end_date'] : 'Today' ?></div>
        </div>

        <table class="list-table">
            <thead>
                <tr>
                    <th style="width: 12%;">Date</th>
                    <th style="width: 15%;">Reference #</th>
                    <th style="width: 43%;">Description / Memo</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 20%;">Posted By</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data['entries'])): ?>
                    <tr><td colspan="5" style="text-align: center; color: #64748b; padding: 20px;">No journal entries match the filter criteria.</td></tr>
                <?php else: ?>
                    <?php foreach ($data['entries'] as $entry): ?>
                        <tr>
                            <td style="font-weight: 500;"><?= date('Y-m-d', strtotime($entry->entry_date)) ?></td>
                            <td><strong><?= htmlspecialchars($entry->reference ?: 'JV-'.$entry->id) ?></strong></td>
                            <td style="color: #334155;"><?= htmlspecialchars($entry->description ?: '-') ?></td>
                            <td>
                                <?php 
                                $st = $entry->status ?? 'Posted';
                                $cls = $st === 'Posted' ? 'status-posted' : ($st === 'Voided' ? 'status-voided' : 'status-draft');
                                ?>
                                <span class="badge-status <?= $cls ?>"><?= htmlspecialchars($st) ?></span>
                            </td>
                            <td><?= htmlspecialchars($entry->username ?: 'System User') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="footer">
            <span>Report Generated: <?= date('Y-m-d H:i:s') ?></span>
            <span>Falcon ERP Accounting Module</span>
        </div>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => { window.print(); }, 300);
        });
    </script>
</body>
</html>
