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
    --c-red:          #ff3b30;
    --c-red-light:    #fff0ef;

    --f-system: -apple-system, 'SF Pro Display', 'SF Pro Text', 'Inter', sans-serif;
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

.create-container {
    font-family: var(--f-system);
    font-size: 14px;
    color: var(--t-primary);
    -webkit-font-smoothing: antialiased;
    max-width: 720px;
    margin: 0 auto;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--t-secondary);
    text-decoration: none;
    font-weight: 600;
    font-size: 13px;
    margin-bottom: 16px;
    transition: color 0.18s ease;
}
.back-link:hover { color: var(--c-blue); }

.page-header-row {
    margin-bottom: 24px;
}
.page-header-row h1 {
    font-size: 28px;
    font-weight: 800;
    margin: 0 0 4px 0;
    letter-spacing: -0.02em;
}
.page-header-row p {
    margin: 0;
    color: var(--t-secondary);
    font-size: 14px;
}

.form-card {
    background: var(--c-surface);
    border-radius: var(--r-lg);
    border: 0.5px solid var(--c-separator);
    box-shadow: var(--shadow-sm);
    padding: 32px;
}

.form-group {
    margin-bottom: 20px;
}
.form-group label {
    display: block;
    font-size: 13px;
    font-weight: 700;
    color: var(--t-primary);
    margin-bottom: 6px;
}
.form-control-sf {
    width: 100%;
    padding: 12px 16px;
    font-size: 14px;
    font-family: var(--f-system);
    background: var(--c-surface2);
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-sm);
    color: var(--t-primary);
    outline: none;
    transition: border-color 0.18s ease;
    box-sizing: border-box;
}
.form-control-sf:focus {
    border-color: var(--c-blue);
    background: var(--c-surface);
}

.grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 28px;
    padding-top: 20px;
    border-top: 0.5px solid var(--c-separator);
}

.btn-sf {
    padding: 10px 24px;
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
.btn-sf-primary:hover { opacity: 0.9; transform: translateY(-1px); color: #fff; }
.btn-sf-secondary { background: var(--c-fill); color: var(--t-primary); }
.btn-sf-secondary:hover { background: var(--c-fill2); }

.info-banner {
    padding: 12px 16px;
    border-radius: var(--r-sm);
    font-size: 13px;
    line-height: 1.45;
    margin-bottom: 20px;
    display: flex;
    align-items: flex-start;
    gap: 10px;
}
.banner-info { background: var(--c-blue-light); color: #1e40af; border: 0.5px solid rgba(0,122,255,0.2); }
.banner-warning { background: var(--c-orange-light); color: #9a3412; border: 0.5px solid rgba(255,149,0,0.3); }
</style>

<div class="create-container">
    <a href="<?= APP_URL ?>/bankreconciliation" class="back-link">
        <i class="fa-solid fa-arrow-left"></i> Back to History
    </a>

    <div class="page-header-row">
        <h1>Start Bank Reconciliation</h1>
        <p>Select bank account and enter statement period details</p>
    </div>

    <div class="form-card">
        <form action="<?= APP_URL ?>/bankreconciliation/store" method="POST" id="createRecForm">
            <div class="form-group">
                <label>Select Bank Account <span style="color: var(--c-red);">*</span></label>
                <select name="account_id" id="accountSelect" class="form-control-sf" required>
                    <option value="">-- Choose Bank Account --</option>
                    <?php foreach ($data['bankAccounts'] as $acc): ?>
                        <option value="<?= $acc->id; ?>">
                            <?= htmlspecialchars($acc->account_code . ' - ' . $acc->account_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Dynamic Balance & Status Notice -->
            <div id="noticeContainer" style="display: none;"></div>

            <div class="grid-2">
                <div class="form-group">
                    <label>Statement Start Date <span style="color: var(--c-red);">*</span></label>
                    <input type="date" name="statement_date_start" id="startDateInput" class="form-control-sf" value="<?= date('Y-m-01'); ?>" required>
                </div>
                <div class="form-group">
                    <label>Statement End Date <span style="color: var(--c-red);">*</span></label>
                    <input type="date" name="statement_date_end" id="endDateInput" class="form-control-sf" value="<?= date('Y-m-t'); ?>" required>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>Statement Opening Balance (LKR) <span style="color: var(--c-red);">*</span></label>
                    <input type="number" step="0.01" name="statement_opening_balance" id="openingBalInput" class="form-control-sf" placeholder="0.00" required>
                    <div id="obSourceNote" style="font-size: 11.5px; color: var(--t-label); margin-top: 4px;"></div>
                </div>
                <div class="form-group">
                    <label>Statement Closing Balance (LKR) <span style="color: var(--c-red);">*</span></label>
                    <input type="number" step="0.01" name="statement_closing_balance" id="closingBalInput" class="form-control-sf" placeholder="0.00" required>
                    <div style="font-size: 11.5px; color: var(--t-label); margin-top: 4px;">Ending balance from physical bank statement</div>
                </div>
            </div>

            <div class="form-actions">
                <a href="<?= APP_URL ?>/bankreconciliation" class="btn-sf btn-sf-secondary">Cancel</a>
                <button type="submit" class="btn-sf btn-sf-primary">
                    <i class="fa-solid fa-play"></i> Start Reconciliation Workspace
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const accSelect = document.getElementById('accountSelect');
    const startInput = document.getElementById('startDateInput');
    const endInput = document.getElementById('endDateInput');
    const obInput = document.getElementById('openingBalInput');
    const obNote = document.getElementById('obSourceNote');
    const noticeContainer = document.getElementById('noticeContainer');

    accSelect.addEventListener('change', function() {
        const accountId = this.value;
        if (!accountId) {
            noticeContainer.style.display = 'none';
            obNote.textContent = '';
            return;
        }

        fetch('<?= APP_URL ?>/bankreconciliation/get_account_info_ajax?account_id=' + accountId)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    obInput.value = data.opening_balance.toFixed(2);
                    if (data.suggested_start) startInput.value = data.suggested_start;
                    if (data.suggested_end) endInput.value = data.suggested_end;

                    obNote.textContent = data.message || '';

                    if (data.has_draft && data.draft_warning) {
                        noticeContainer.className = 'info-banner banner-warning';
                        noticeContainer.innerHTML = '<i class="fa-solid fa-triangle-exclamation" style="margin-top:2px;"></i><div><strong>Unfinalized Previous Draft:</strong> ' + data.draft_warning + '</div>';
                        noticeContainer.style.display = 'flex';
                    } else if (data.has_finalized) {
                        noticeContainer.className = 'info-banner banner-info';
                        noticeContainer.innerHTML = '<i class="fa-solid fa-circle-check" style="margin-top:2px;"></i><div><strong>Prior Month Confirmed:</strong> Automatically carried forward closing balance from ' + data.prev_rec_number + ' (Ending ' + data.prev_end_date + ').</div>';
                        noticeContainer.style.display = 'flex';
                    } else {
                        noticeContainer.className = 'info-banner banner-info';
                        noticeContainer.innerHTML = '<i class="fa-solid fa-info-circle" style="margin-top:2px;"></i><div><strong>Initial Setup:</strong> ' + (data.message || 'Starting initial reconciliation for this bank account.') + '</div>';
                        noticeContainer.style.display = 'flex';
                    }
                }
            })
            .catch(err => {
                console.error(err);
            });
    });
});
</script>
