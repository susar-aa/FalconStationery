<!-- Inter Font & FontAwesome Icons -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<?php
$dep = $data['deposit'];
$isSent = $dep->status === 'Sent to Bank';
$isCompleted = $dep->status === 'Completed';
?>

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

.process-container {
    font-family: var(--f-system);
    font-size: 14px;
    color: var(--t-primary);
    -webkit-font-smoothing: antialiased;
    padding-bottom: 60px;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--t-secondary);
    text-decoration: none;
    font-weight: 600;
    font-size: 13px;
    margin-bottom: 20px;
    transition: color 0.18s ease;
}
.back-link:hover { color: var(--c-blue); }

.header-card {
    background: var(--c-surface);
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-lg);
    padding: 24px;
    box-shadow: var(--shadow-sm);
    margin-bottom: 24px;
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 0.5px solid var(--c-separator);
}
.detail-item { display: flex; flex-direction: column; gap: 4px; }
.detail-label { font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--t-label); letter-spacing: 0.05em; }
.detail-val { font-size: 15px; font-weight: 600; color: var(--t-primary); }

.sf-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 14px;
    border-radius: var(--r-pill);
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
}
.sf-badge .dot { width: 6px; height: 6px; border-radius: 50%; }
.badge-Sent { background: var(--c-blue-light); color: var(--c-blue); }
.badge-Sent .dot { background: var(--c-blue); }
.badge-Completed { background: var(--c-green-light); color: #15803d; }
.badge-Completed .dot { background: var(--c-green); }

.section-card {
    background: var(--c-surface);
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-lg);
    padding: 24px;
    box-shadow: var(--shadow-sm);
    margin-bottom: 24px;
}

.card-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--t-primary);
    border-bottom: 0.5px solid var(--c-separator);
    padding-bottom: 14px;
    margin: 0 0 20px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.form-group label {
    font-weight: 700;
    font-size: 11px;
    text-transform: uppercase;
    color: var(--t-secondary);
    letter-spacing: 0.05em;
}
.form-group input, .form-group textarea, .form-group select {
    padding: 10px 14px;
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-sm);
    font-size: 14px;
    background: var(--c-surface2);
    color: inherit;
    box-sizing: border-box;
    outline: none;
}
.form-group input:focus, .form-group textarea:focus, .form-group select:focus {
    border-color: var(--c-blue);
    background: var(--c-surface);
    box-shadow: 0 0 0 3px var(--c-blue-light);
}

.item-table { width: 100%; border-collapse: collapse; text-align: left; }
.item-table th, .item-table td { padding: 12px 16px; border-bottom: 0.5px solid var(--c-separator); font-size: 13px; }
.item-table th { background: var(--c-surface2); font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--t-secondary); letter-spacing: 0.05em; }

.action-select {
    padding: 6px 10px;
    border-radius: var(--r-sm);
    border: 0.5px solid var(--c-separator);
    font-size: 12px;
    font-weight: 700;
    background: var(--c-surface2);
    color: var(--t-primary);
}

.btn-sf {
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 600;
    border-radius: var(--r-pill);
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: all 0.18s ease;
}
.btn-sf-primary { background: var(--c-blue); color: #fff; }
.btn-sf-primary:hover { opacity: 0.9; }
.btn-sf-success { background: var(--c-green); color: #fff; }
.btn-sf-success:hover { opacity: 0.9; }
.btn-sf-secondary { background: var(--c-fill); color: var(--t-primary); }
.btn-sf-secondary:hover { background: var(--c-fill2); }
</style>

<div class="process-container">
    <a href="<?= APP_URL ?>/deposit" class="back-link">
        <i class="fa-solid fa-arrow-left"></i> Back to Deposits Workflow
    </a>

    <div class="header-card">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <i class="fa-solid fa-building-columns" style="font-size: 24px; color: var(--c-blue);"></i>
                <h2 style="margin: 0; font-size: 22px; font-weight: 800;">Deposit: #<?= htmlspecialchars($dep->deposit_number) ?></h2>
            </div>
            <div class="sf-badge badge-<?= str_replace(' ', '', $dep->status) ?>">
                <div class="dot"></div> <?= htmlspecialchars($dep->status === 'Sent to Bank' ? 'In Transit' : $dep->status) ?>
            </div>
        </div>
        
        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Deposit Date</span>
                <span class="detail-val"><?= date('M d, Y', strtotime($dep->deposit_date)) ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Destination Bank Account</span>
                <span class="detail-val"><?= htmlspecialchars($dep->bank_name) ?> (<?= htmlspecialchars($dep->bank_code) ?>)</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Prepared By</span>
                <span class="detail-val"><?= htmlspecialchars($dep->prepared_by_name ?: 'System User') ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Grand Total Sent</span>
                <span class="detail-val" style="color: var(--c-green); font-family: var(--f-mono);">Rs <?= number_format($dep->total_deposit, 2) ?></span>
            </div>
        </div>
    </div>

    <!-- Audit Journal Entries Link if Completed -->
    <?php if ($isCompleted): ?>
        <div class="section-card" style="border-left: 4px solid var(--c-green); background: var(--c-green-light);">
            <h3 class="card-title" style="color: #15803d; border-color: rgba(52,199,89,0.3);">
                <i class="fa-solid fa-link"></i> Audit Trail & General Ledger Links
            </h3>
            <div style="display: flex; flex-direction: column; gap: 10px; font-size: 13px;">
                <div>
                    <strong>1. Transit Entry Reference:</strong> 
                    <span style="font-family: var(--f-mono); font-weight: 700; color: var(--c-blue); background: var(--c-surface); padding: 3px 8px; border-radius: 6px;"># DEP-<?= htmlspecialchars($dep->deposit_number) ?></span>
                    <span style="color: var(--t-secondary); margin-left: 6px;">(Posted when Sent to Bank. Funds debited to Deposit in Transit accounts)</span>
                </div>
                <?php if (!empty($dep->realization_journal_entry_id)): 
                    $db = new Database();
                    $db->query("SELECT reference FROM journal_entries WHERE id = :jid");
                    $db->bind(':jid', $dep->realization_journal_entry_id);
                    $rjeRow = $db->single();
                    $rjeRef = $rjeRow ? $rjeRow->reference : '';
                ?>
                    <div>
                        <strong>2. Realization Entry Reference:</strong>
                        <span style="font-family: var(--f-mono); font-weight: 700; color: #15803d; background: var(--c-surface); padding: 3px 8px; border-radius: 6px;"># <?= htmlspecialchars($rjeRef) ?></span>
                        <span style="color: var(--t-secondary); margin-left: 6px;">(Posted when Processed. Moved cleared funds from transit into Destination Bank Account)</span>
                    </div>
                <?php endif; ?>
                <div style="font-size: 12px; color: var(--c-orange); margin-top: 4px; font-weight: 500;">
                    <i class="fa-solid fa-circle-info"></i> <em>If any cheques were returned or rejected, their individual reversal journal entries are prefixed with <strong>REV-</strong> in audit logs.</em>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <form action="<?= APP_URL ?>/deposit/process/<?= $dep->id ?>" method="POST">
        <!-- CRITICAL: CSRF Token injection -->
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

        <!-- Cash Details -->
        <?php if ($dep->cash_total > 0): ?>
            <div class="section-card">
                <h3 class="card-title">
                    <i class="fa-solid fa-money-bill-wave" style="color: var(--c-green);"></i> Cash Processing
                </h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                    <div style="font-size: 13px; line-height: 1.6;">
                        <strong style="color: var(--t-secondary); text-transform: uppercase; font-size: 11px; letter-spacing: 0.05em;">Cash Denominations Breakdown:</strong>
                        <table style="width: 100%; margin-top: 10px; font-family: var(--f-mono); font-size: 13px;">
                            <?php 
                            $denoms = [5000, 2000, 1000, 500, 100, 50, 20];
                            foreach ($denoms as $d):
                                $field = "cash_" . $d;
                                if (intval($dep->$field) > 0):
                            ?>
                                <tr>
                                    <td style="padding: 4px 0;">Rs <?= $d ?> ✕ <?= $dep->$field ?></td>
                                    <td style="text-align: right; padding: 4px 0;">Rs <?= number_format($d * $dep->$field, 2) ?></td>
                                </tr>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                            <tr style="font-weight: 800; border-top: 0.5px solid var(--c-separator);">
                                <td style="padding-top: 8px;">Total Cash Sent:</td>
                                <td style="text-align: right; padding-top: 8px; color: var(--c-blue);">Rs <?= number_format($dep->cash_total, 2) ?></td>
                            </tr>
                        </table>
                    </div>
                    <div>
                        <div class="form-group">
                            <label>Accepted Cash Amount (Rs) *</label>
                            <?php if ($isSent): ?>
                                <input type="number" name="accepted_cash_amount" step="0.01" min="0.00" max="<?= $dep->cash_total ?>" value="<?= htmlspecialchars($dep->cash_total) ?>" required style="font-size: 16px; font-weight: 700; font-family: var(--f-mono); width: 240px; color: var(--c-green);">
                                <span style="font-size: 12px; color: var(--t-secondary); margin-top: 6px;">Prefilled with total cash sent. Modify if bank statement confirmation differs.</span>
                            <?php else: ?>
                                <div style="font-size: 18px; font-weight: 800; font-family: var(--f-mono); color: var(--c-green); padding: 10px 0;">
                                    Rs <?= number_format($dep->accepted_cash_amount, 2) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Cheques Details -->
        <?php 
        $hasCheques = false;
        foreach ($data['items'] as $item) {
            if ($item->cheque_id !== null) { $hasCheques = true; break; }
        }
        if ($hasCheques):
        ?>
            <div class="section-card">
                <h3 class="card-title">
                    <i class="fa-solid fa-money-check-dollar" style="color: var(--c-orange);"></i> Cheque Clearing Details
                </h3>
                <table class="item-table">
                    <thead>
                        <tr>
                            <th>Cheque Detail</th>
                            <th>Customer</th>
                            <th style="text-align: right;">Amount</th>
                            <th style="text-align: center; width: 190px;">Action Resolution</th>
                            <th>Reason (If Return / Reject)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['items'] as $item): 
                            if ($item->cheque_id === null) continue;
                        ?>
                            <tr>
                                <td>
                                    <strong style="color: var(--c-blue);">No: <?= htmlspecialchars($item->cheque_number) ?></strong><br>
                                    <span style="font-size: 11px; color: var(--t-secondary);"><?= htmlspecialchars($item->bank_name) ?> | <?= date('Y-m-d', strtotime($item->banking_date)) ?></span>
                                </td>
                                <td style="font-weight: 600; color: var(--t-primary);"><?= htmlspecialchars($item->customer_name) ?></td>
                                <td style="text-align: right; font-weight: 700; font-family: var(--f-mono);">Rs <?= number_format($item->cheque_amount, 2) ?></td>
                                <td style="text-align: center;">
                                    <?php if ($isSent): ?>
                                        <select name="cheque_action[<?= $item->cheque_id ?>]" class="action-select" onchange="toggleReason(<?= $item->cheque_id ?>, this.value)">
                                            <option value="Clear" style="color: var(--c-green); font-weight: bold;">✔️ Clear / Pass</option>
                                            <option value="Return" style="color: var(--c-orange); font-weight: bold;">❌ Return</option>
                                            <option value="Reject" style="color: var(--c-red); font-weight: bold;">⚠️ Reject</option>
                                        </select>
                                    <?php else: ?>
                                        <?php 
                                        $badgeColor = 'var(--t-secondary)';
                                        if ($item->status === 'Passed') $badgeColor = 'var(--c-green)';
                                        elseif ($item->status === 'Returned') $badgeColor = 'var(--c-orange)';
                                        elseif ($item->status === 'Rejected') $badgeColor = 'var(--c-red)';
                                        ?>
                                        <span style="font-weight: 800; color: <?= $badgeColor ?>; text-transform: uppercase; font-size: 12px;">
                                            <?= htmlspecialchars($item->status === 'Passed' ? 'Cleared' : $item->status) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($isSent): ?>
                                        <input type="text" name="rejection_reason[<?= $item->cheque_id ?>]" id="reason_<?= $item->cheque_id ?>" placeholder="Enter return reason..." style="display: none; width: 100%; font-size: 13px; padding: 6px 10px;">
                                    <?php else: ?>
                                        <span style="color: var(--t-secondary); font-size: 13px; font-style: italic;"><?= htmlspecialchars($item->rejection_reason ?: '-') ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- Remarks Section -->
        <div class="section-card">
            <h3 class="card-title">
                <i class="fa-solid fa-note-sticky" style="color: var(--c-purple);"></i> Audit & Confirmation Notes
            </h3>
            <div class="form-group">
                <?php if ($isSent): ?>
                    <textarea name="approval_remarks" rows="3" placeholder="Enter bank confirmation statement notes or audit remarks..."></textarea>
                <?php else: ?>
                    <div style="font-size: 14px; line-height: 1.6; font-style: italic; color: var(--t-secondary);">
                        <?= nl2br(htmlspecialchars($dep->approval_remarks ?: 'No remarks provided.')) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 12px; margin-top: 30px;">
            <a href="<?= APP_URL ?>/deposit" class="btn-sf btn-sf-secondary">Back</a>
            <a href="<?= APP_URL ?>/deposit/printSlip/<?= $dep->id ?>" target="_blank" class="btn-sf btn-sf-primary">
                <i class="fa-solid fa-print"></i> Print Slip
            </a>
            <?php if ($isSent): ?>
                <button type="submit" class="btn-sf btn-sf-success" onclick="return confirm('Are you sure you want to finalize this deposit processing? Cleared items will post to the bank account ledger; returned cheques will be reversed.')">
                    <i class="fa-solid fa-circle-check"></i> Complete Processing
                </button>
            <?php endif; ?>
        </div>
    </form>
</div>

<script>
    function toggleReason(chequeId, val) {
        const input = document.getElementById('reason_' + chequeId);
        if (input) {
            if (val === 'Return' || val === 'Reject') {
                input.style.display = 'block';
                input.required = true;
            } else {
                input.style.display = 'none';
                input.required = false;
            }
        }
    }
</script>
