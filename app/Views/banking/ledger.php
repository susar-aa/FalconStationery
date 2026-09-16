<?php
// Dynamic calculations excluding voided transactions
$totalTransactions = 0;
$clearedCount = 0;
$unclearedCount = 0;
foreach ($data['transactions'] ?? [] as $t) {
    if (($t->status ?? '') === 'Voided') {
        continue;
    }
    $totalTransactions++;
    if ($t->is_cleared) {
        $clearedCount++;
    } else {
        $unclearedCount++;
    }
}
?>

<!-- Inter Font & FontAwesome Icons -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
/* ============================================================
   SF PRO + APPLE DESIGN LANGUAGE — BANK REGISTER
   ============================================================ */

:root {
    --c-bg:           #f2f2f7;
    --c-surface:      #ffffff;
    --c-surface2:     #f9f9fb;
    --c-fill:         rgba(120,120,128,0.12);
    --c-fill2:        rgba(120,120,128,0.16);
    --c-separator:    rgba(60,60,67,0.12);
    --c-separator2:   rgba(60,60,67,0.06);

    --c-blue:         #007aff;
    --c-blue-light:   #e5f2ff;
    --c-blue-mid:     #b3d6ff;
    --c-green:        #34c759;
    --c-green-light:  #e6f9ec;
    --c-orange:       #ff9500;
    --c-orange-light: #fff4e5;
    --c-red:          #ff3b30;
    --c-red-light:    #fff0ef;

    --f-system: -apple-system, 'SF Pro Display', 'SF Pro Text', 'Inter', 'Helvetica Neue', sans-serif;
    --f-mono:   ui-monospace, 'SF Mono', 'Menlo', 'Monaco', monospace;

    --t-primary:   #1c1c1e;
    --t-secondary: #636366;
    --t-tertiary:  #aeaeb2;
    --t-label:     #8e8e93;

    --shadow-xs:  0 1px 2px rgba(0,0,0,0.04);
    --shadow-sm:  0 2px 8px rgba(0,0,0,0.06), 0 1px 3px rgba(0,0,0,0.04);
    --shadow-md:  0 8px 24px rgba(0,0,0,0.08), 0 2px 6px rgba(0,0,0,0.04);
    --shadow-xl:  0 24px 48px rgba(0,0,0,0.14), 0 4px 12px rgba(0,0,0,0.06);

    --r-xs: 6px;
    --r-sm: 10px;
    --r-md: 14px;
    --r-lg: 20px;
    --r-xl: 26px;
    --r-pill: 999px;

    --dur-fast:    0.18s;
}

@media (prefers-color-scheme: dark) {
    :root {
        --c-bg:           #121212;
        --c-surface:      #1e1e2e;
        --c-surface2:     #161622;
        --c-fill:         rgba(255,255,255,0.08);
        --c-fill2:        rgba(255,255,255,0.12);
        --c-separator:    rgba(255,255,255,0.15);
        --c-separator2:   rgba(255,255,255,0.08);
        --t-primary:   #f5f5f7;
        --t-secondary: #a1a1aa;
        --t-tertiary:  #71717a;
        --t-label:     #52525b;
    }
}

.cust-root {
    font-family: var(--f-system);
    font-size: 15px;
    color: var(--t-primary);
    background: var(--c-bg);
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

.cust-wrap {
    max-width: 1400px;
    margin: 0 auto;
    padding: 24px 32px 60px;
}

/* ---- Header & Breadcrumb ---- */
.sf-header {
    margin-bottom: 24px;
}
.sf-breadcrumb {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 13px; font-weight: 600;
    color: var(--t-secondary); text-decoration: none;
    margin-bottom: 12px; transition: color var(--dur-fast);
}
.sf-breadcrumb:hover { color: var(--c-blue); }
.sf-breadcrumb i { font-size: 11px; }

.sf-title-row {
    display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 16px;
}
.sf-title-col h1 {
    font-size: 32px; font-weight: 800; letter-spacing: -0.03em; margin: 0; color: var(--t-primary); line-height: 1.1;
}
.sf-title-col p {
    font-size: 15px; color: var(--t-secondary); margin: 6px 0 0 0; font-weight: 500;
}

/* ---- Quick Stats Grid ---- */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 16px;
    margin-bottom: 32px;
}
.stat-card {
    background: var(--c-surface);
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-lg);
    padding: 20px;
    display: flex; align-items: center; gap: 16px;
    box-shadow: var(--shadow-sm);
}
.stat-icon {
    width: 48px; height: 48px; border-radius: var(--r-md);
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; flex-shrink: 0;
}
.stat-card.blue  .stat-icon { background: var(--c-blue-light);   color: var(--c-blue); }
.stat-card.green .stat-icon { background: var(--c-green-light);  color: var(--c-green); }
.stat-card.orange .stat-icon { background: var(--c-orange-light); color: var(--c-orange); }

.stat-info { display: flex; flex-direction: column; justify-content: center; }
.stat-num {
    font-size: 22px;
    font-weight: 700;
    letter-spacing: -0.04em;
    color: var(--t-primary);
    line-height: 1.1;
    margin-bottom: 2px;
}
.stat-lbl {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: var(--t-label);
}

/* ---- Card Wrapper ---- */
.sf-card {
    background: var(--c-surface);
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-xl);
    box-shadow: var(--shadow-md);
    overflow: hidden;
}

/* ---- Table ---- */
.sf-table-wrapper { width: 100%; overflow-x: auto; }
.sf-table { width: 100%; border-collapse: collapse; text-align: left; white-space: nowrap; }
.sf-table th, .sf-table td { padding: 14px 18px; border-bottom: 0.5px solid var(--c-separator); }
.sf-table th {
    background: var(--c-surface2);
    font-size: 12px; font-weight: 700;
    letter-spacing: 0.02em; text-transform: uppercase; color: var(--t-label);
    position: sticky; top: 0; z-index: 10;
}
.sf-table td { font-size: 14.5px; font-weight: 500; color: var(--t-primary); }
.sf-table tr:last-child td { border-bottom: none; }
.sf-table tr:hover td { background: var(--c-fill2); }

.td-date { color: var(--t-secondary); font-size: 13.5px; }
.td-ref { font-family: var(--f-mono); font-size: 13px; font-weight: 600; color: var(--t-primary); }
.td-desc { max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.td-num { text-align: right !important; font-variant-numeric: tabular-nums; font-family: var(--f-mono); font-size: 14px; }
.td-debit { color: var(--c-green); font-weight: 700; }
.td-credit { color: var(--c-red); font-weight: 700; }
.td-center { text-align: center !important; }

/* ---- Status Pills ---- */
.status-pill {
    display: inline-flex; align-items: center; justify-content: center;
    padding: 4px 10px; border-radius: var(--r-pill); font-size: 11.5px; font-weight: 700;
    letter-spacing: 0.02em; text-transform: uppercase;
}
.status-pill.cleared { background: var(--c-green-light); color: var(--c-green); }
.status-pill.uncleared { background: var(--c-orange-light); color: var(--c-orange); }

/* ---- Empty State ---- */
.empty-state { text-align: center; padding: 60px 20px; }
.empty-icon { font-size: 48px; color: var(--c-separator); margin-bottom: 16px; }
.empty-title { font-size: 18px; font-weight: 700; color: var(--t-primary); margin: 0 0 8px 0; }
.empty-desc { font-size: 14px; color: var(--t-secondary); margin: 0; }
</style>

<div class="cust-root">
    <div class="cust-wrap">
        
        <div class="sf-header">
            <a href="<?= APP_URL ?>/banking" class="sf-breadcrumb"><i class="fa-solid fa-chevron-left"></i> Back to Banking</a>
            <div class="sf-title-row">
                <div class="sf-title-col">
                    <h1>Bank Register</h1>
                    <p><?= htmlspecialchars($data['account']->account_code) ?> — <?= htmlspecialchars($data['account']->account_name) ?></p>
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-icon"><i class="fa-solid fa-wallet"></i></div>
                <div class="stat-info">
                    <div class="stat-num">Rs: <?= number_format($data['account']->balance, 2) ?></div>
                    <div class="stat-lbl">Current Balance</div>
                </div>
            </div>
            <div class="stat-card green">
                <div class="stat-icon"><i class="fa-solid fa-check-circle"></i></div>
                <div class="stat-info">
                    <div class="stat-num"><?= $clearedCount ?></div>
                    <div class="stat-lbl">Cleared Entries</div>
                </div>
            </div>
            <div class="stat-card orange">
                <div class="stat-icon"><i class="fa-regular fa-clock"></i></div>
                <div class="stat-info">
                    <div class="stat-num"><?= $unclearedCount ?></div>
                    <div class="stat-lbl">Pending Entries</div>
                </div>
            </div>
        </div>

        <div class="sf-card">
            <div class="sf-table-wrapper">
                <table class="sf-table">
                    <thead>
                        <tr>
                            <th style="width: 130px;">Date</th>
                            <th style="width: 160px;">Reference</th>
                            <th>Description</th>
                            <th class="td-num" style="width: 140px;">Debit (Rs:)</th>
                            <th class="td-num" style="width: 140px;">Credit (Rs:)</th>
                            <th class="td-center" style="width: 120px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data['transactions'])): ?>
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <div class="empty-icon"><i class="fa-solid fa-book-open"></i></div>
                                        <h3 class="empty-title">No Transactions Found</h3>
                                        <p class="empty-desc">This account ledger is currently empty.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: foreach($data['transactions'] as $t): 
                            $isVoided = (($t->status ?? '') === 'Voided');
                        ?>
                            <tr style="<?= $isVoided ? 'opacity: 0.6; background: rgba(255, 59, 48, 0.05);' : '' ?>">
                                <td class="td-date"><?= date('M d, Y', strtotime($t->entry_date)) ?></td>
                                <td class="td-ref"><?= htmlspecialchars($t->reference) ?></td>
                                <td class="td-desc" title="<?= htmlspecialchars($t->description) ?>">
                                    <?= htmlspecialchars($t->description) ?>
                                    <?php if ($isVoided): ?>
                                        <span style="color: var(--c-red); font-weight: 700; margin-left: 6px;">[VOIDED]</span>
                                    <?php endif; ?>
                                </td>
                                <td class="td-num td-debit" style="<?= $isVoided ? 'text-decoration: line-through;' : '' ?>"><?= $t->debit > 0 ? number_format($t->debit, 2) : '-' ?></td>
                                <td class="td-num td-credit" style="<?= $isVoided ? 'text-decoration: line-through;' : '' ?>"><?= $t->credit > 0 ? number_format($t->credit, 2) : '-' ?></td>
                                <td class="td-center">
                                    <?php if ($isVoided): ?>
                                        <span class="status-pill voided" style="background: var(--c-red-light); color: var(--c-red);">Voided</span>
                                    <?php elseif ($t->is_cleared): ?>
                                        <span class="status-pill cleared">Cleared</span>
                                    <?php else: ?>
                                        <span class="status-pill uncleared">Uncleared</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>