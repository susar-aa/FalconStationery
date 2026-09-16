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
        <form action="<?= APP_URL ?>/bankreconciliation/store" method="POST">
            <div class="form-group">
                <label>Select Bank Account <span style="color: var(--c-red);">*</span></label>
                <select name="account_id" class="form-control-sf" required>
                    <option value="">-- Choose Bank Account --</option>
                    <?php foreach ($data['bankAccounts'] as $acc): ?>
                        <option value="<?= $acc->id; ?>">
                            <?= htmlspecialchars($acc->account_code . ' - ' . $acc->account_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>Statement Start Date <span style="color: var(--c-red);">*</span></label>
                    <input type="date" name="statement_date_start" class="form-control-sf" value="<?= date('Y-m-01'); ?>" required>
                </div>
                <div class="form-group">
                    <label>Statement End Date <span style="color: var(--c-red);">*</span></label>
                    <input type="date" name="statement_date_end" class="form-control-sf" value="<?= date('Y-m-t'); ?>" required>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>Statement Opening Balance (LKR) <span style="color: var(--c-red);">*</span></label>
                    <input type="number" step="0.01" name="statement_opening_balance" class="form-control-sf" placeholder="0.00" required>
                </div>
                <div class="form-group">
                    <label>Statement Closing Balance (LKR) <span style="color: var(--c-red);">*</span></label>
                    <input type="number" step="0.01" name="statement_closing_balance" class="form-control-sf" placeholder="0.00" required>
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
