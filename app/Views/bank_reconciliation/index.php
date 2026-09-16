<!-- Inter Font & FontAwesome Icons -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
:root {
    --c-bg:           #f2f2f7;
    --c-surface:      #ffffff;
    --c-surface2:     #f9f9fb;
    --c-fill:         rgba(120,120,128,0.12);
    --c-fill2:        rgba(120,120,128,0.16);
    --c-separator:    rgba(60,60,67,0.12);

    --c-blue:         #007aff;
    --c-blue-light:   #e5f2ff;
    --c-green:        #34c759;
    --c-green-light:  #e6f9ec;
    --c-orange:       #ff9500;
    --c-orange-light: #fff4e5;
    --c-purple:       #af52de;
    --c-purple-light: #f5e8ff;
    --c-red:          #ff3b30;
    --c-red-light:    #fff0ef;

    --f-system: -apple-system, 'SF Pro Display', 'SF Pro Text', 'Inter', sans-serif;
    --f-mono:   ui-monospace, 'SF Mono', 'Menlo', 'Monaco', monospace;

    --t-primary:   #1c1c1e;
    --t-secondary: #636366;
    --t-label:     #8e8e93;

    --shadow-xs:  0 1px 2px rgba(0,0,0,0.04);
    --shadow-sm:  0 2px 8px rgba(0,0,0,0.06);
    --shadow-md:  0 8px 24px rgba(0,0,0,0.08);

    --r-sm: 10px;
    --r-md: 14px;
    --r-lg: 20px;
    --r-pill: 999px;
}

@media (prefers-color-scheme: dark) {
    :root {
        --c-bg:           #121212;
        --c-surface:      #1e1e2e;
        --c-surface2:     #161622;
        --c-fill:         rgba(255,255,255,0.08);
        --c-fill2:        rgba(255,255,255,0.12);
        --c-separator:    rgba(255,255,255,0.15);
        --t-primary:   #f5f5f7;
        --t-secondary: #a1a1aa;
        --t-label:     #52525b;
    }
}

.rec-root {
    font-family: var(--f-system);
    font-size: 15px;
    color: var(--t-primary);
    -webkit-font-smoothing: antialiased;
}

/* Page Header */
.sf-page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}
.sf-page-title h1 {
    font-size: 28px;
    font-weight: 800;
    margin: 0 0 4px 0;
    letter-spacing: -0.02em;
}
.sf-page-title p {
    margin: 0;
    color: var(--t-secondary);
    font-size: 14px;
}

/* Table Panel */
.table-panel {
    background: var(--c-surface);
    border-radius: var(--r-lg);
    border: 0.5px solid var(--c-separator);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}
.sf-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
    font-size: 14px;
}
.sf-table th {
    background: var(--c-surface2);
    padding: 14px 18px;
    font-size: 11px;
    font-weight: 700;
    color: var(--t-secondary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 0.5px solid var(--c-separator);
}
.sf-table td {
    padding: 14px 18px;
    border-bottom: 0.5px solid var(--c-separator);
    vertical-align: middle;
}
.sf-table tr:last-child td { border-bottom: none; }
.sf-table tr:hover td { background: var(--c-fill); }

/* Badges */
.sf-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: var(--r-pill);
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
}
.sf-badge .dot { width: 6px; height: 6px; border-radius: 50%; }
.badge-Draft { background: var(--c-fill2); color: var(--t-secondary); }
.badge-Draft .dot { background: var(--t-label); }
.badge-Finalized { background: var(--c-green-light); color: #15803d; }
.badge-Finalized .dot { background: var(--c-green); }

/* Buttons */
.btn-sf {
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 600;
    border-radius: var(--r-sm);
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    transition: all 0.18s ease;
}
.btn-sf-primary { background: var(--c-blue); color: #fff; }
.btn-sf-primary:hover { opacity: 0.9; transform: translateY(-1px); color: #fff; }
.btn-sf-secondary { background: var(--c-fill); color: var(--t-primary); }
.btn-sf-secondary:hover { background: var(--c-fill2); }
.act-group { display: flex; gap: 6px; justify-content: center; }
</style>

<div class="rec-root">
    <div class="sf-page-header">
        <div class="sf-page-title">
            <h1>Bank Reconciliation</h1>
            <p>Reconcile bank statements with General Ledger balances</p>
        </div>
        <div>
            <a href="<?= APP_URL ?>/bankreconciliation/create" class="btn-sf btn-sf-primary" style="padding: 10px 20px; border-radius: var(--r-pill); font-size: 14px;">
                <i class="fa-solid fa-plus"></i> New Reconciliation
            </a>
        </div>
    </div>

    <div class="table-panel">
        <table class="sf-table">
            <thead>
                <tr>
                    <th style="padding-left: 24px;">Ref Number</th>
                    <th>Bank Account</th>
                    <th>Statement Period</th>
                    <th style="text-align: right;">Stmt Closing</th>
                    <th style="text-align: right;">Book Closing</th>
                    <th style="text-align: right;">Difference</th>
                    <th style="text-align: center;">Status</th>
                    <th style="text-align: center; padding-right: 24px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['reconciliations'])): ?>
                    <?php foreach ($data['reconciliations'] as $rec): ?>
                        <tr>
                            <td style="padding-left: 24px; font-weight: 700;">
                                <a href="<?= APP_URL ?>/bankreconciliation/reconcile/<?= $rec->id ?>" style="color: var(--c-blue); text-decoration: none;">
                                    <?= htmlspecialchars($rec->reconciliation_number) ?>
                                </a>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: var(--t-primary);"><?= htmlspecialchars($rec->account_name) ?></div>
                                <div style="font-size: 12px; color: var(--t-secondary);"><?= htmlspecialchars($rec->account_code) ?></div>
                            </td>
                            <td>
                                <span style="font-weight: 600; font-size: 13px; color: var(--t-secondary);">
                                    <?= date('M d, Y', strtotime($rec->statement_date_start)) ?> - <?= date('M d, Y', strtotime($rec->statement_date_end)) ?>
                                </span>
                            </td>
                            <td style="text-align: right; font-weight: 700;">
                                LKR <?= number_format($rec->statement_closing_balance, 2) ?>
                            </td>
                            <td style="text-align: right; font-weight: 700;">
                                LKR <?= number_format($rec->book_closing_balance, 2) ?>
                            </td>
                            <td style="text-align: right; font-weight: 700; color: <?= (abs($rec->difference) > 0.009) ? 'var(--c-red)' : 'var(--c-green)' ?>;">
                                LKR <?= number_format($rec->difference, 2) ?>
                            </td>
                            <td style="text-align: center;">
                                <?php if ($rec->status === 'Finalized'): ?>
                                    <span class="sf-badge badge-Finalized"><span class="dot"></span> Finalized</span>
                                <?php else: ?>
                                    <span class="sf-badge badge-Draft"><span class="dot"></span> Draft</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center; padding-right: 24px;">
                                <div class="act-group">
                                    <a href="<?= APP_URL ?>/bankreconciliation/reconcile/<?= $rec->id ?>" class="btn-sf btn-sf-secondary" style="border-radius: var(--r-pill); font-size: 12px; padding: 5px 14px;">
                                        <i class="fa-solid fa-pen-to-square"></i> <?= ($rec->status === 'Finalized') ? 'View Workspace' : 'Reconcile' ?>
                                    </a>
                                    <a href="<?= APP_URL ?>/bankreconciliation/report/<?= $rec->id ?>" class="btn-sf btn-sf-secondary" style="border-radius: var(--r-pill); font-size: 12px; padding: 5px 14px;" target="_blank">
                                        <i class="fa-solid fa-print"></i> Statement
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 60px 20px; color: var(--t-secondary);">
                            <i class="fa-solid fa-university" style="font-size: 42px; opacity: 0.3; margin-bottom: 14px; display: block;"></i>
                            <div style="font-size: 16px; font-weight: 600; color: var(--t-primary); margin-bottom: 4px;">No Bank Reconciliations Found</div>
                            <div style="font-size: 13px;">Click "New Reconciliation" above to start reconciling your bank statement.</div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
