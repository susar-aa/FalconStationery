<!-- Inter Font & FontAwesome Icons -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
/* ============================================================
   SF PRO + APPLE DESIGN LANGUAGE — EPF & ETF SYSTEM
   ============================================================ */

:root {
    --c-bg:           #f2f2f7;
    --c-surface:      #ffffff;
    --c-surface2:     #f9f9fb;
    --c-fill:         rgba(120,120,128,0.08);
    --c-fill2:        rgba(120,120,128,0.14);
    --c-separator:    rgba(60,60,67,0.12);
    --c-separator2:   rgba(60,60,67,0.06);

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
    --r-xl: 24px;
    --r-pill: 999px;

    --ease-ios: cubic-bezier(0.25, 0.1, 0.25, 1);
    --dur-fast: 0.18s;
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

        --c-blue-light:   rgba(0,122,255,0.15);
        --c-green-light:  rgba(52,199,89,0.15);
        --c-orange-light: rgba(255,149,0,0.15);
        --c-purple-light: rgba(175,82,222,0.15);
        --c-red-light:    rgba(255,59,48,0.15);
    }
}

.ee-root {
    font-family: var(--f-system);
    font-size: 14.5px;
    color: var(--t-primary);
    background: var(--c-bg);
    min-height: 100vh;
    padding: 20px 24px 80px;
    -webkit-font-smoothing: antialiased;
}

/* Header Section */
.sf-page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}
.sf-page-title h1 {
    font-size: 26px;
    font-weight: 800;
    margin: 0 0 4px 0;
    letter-spacing: -0.03em;
    color: var(--t-primary);
    display: flex;
    align-items: center;
    gap: 10px;
}
.sf-page-title p {
    margin: 0;
    color: var(--t-secondary);
    font-size: 13.5px;
    font-weight: 500;
}

/* Flash Banners */
.flash-msg {
    padding: 12px 18px;
    border-radius: var(--r-md);
    margin-bottom: 20px;
    font-size: 13.5px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}
.flash-msg-success { background: var(--c-green-light); color: #15803d; border: 0.5px solid rgba(52,199,89,0.3); }
.flash-msg-error { background: var(--c-red-light); color: #b91c1c; border: 0.5px solid rgba(255,59,48,0.3); }

/* ---- Apple Stat Cards ---- */
.stat-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}
@media (max-width: 1100px) {
    .stat-row { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 600px) {
    .stat-row { grid-template-columns: 1fr; }
}

.stat-card {
    background: var(--c-surface);
    border-radius: var(--r-xl);
    padding: 18px 20px;
    box-shadow: var(--shadow-sm);
    border: 0.5px solid var(--c-separator);
    transition: transform var(--dur-fast) var(--ease-ios), box-shadow var(--dur-fast) var(--ease-ios);
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    gap: 16px;
}
.stat-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: var(--r-xl) var(--r-xl) 0 0;
}
.stat-card.blue::before   { background: var(--c-blue); }
.stat-card.purple::before { background: var(--c-purple); }
.stat-card.orange::before { background: var(--c-orange); }
.stat-card.red::before    { background: var(--c-red); }

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}
.stat-icon {
    width: 48px; height: 48px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}
.stat-card.blue   .stat-icon { background: var(--c-blue-light);   color: var(--c-blue); }
.stat-card.purple .stat-icon { background: var(--c-purple-light); color: var(--c-purple); }
.stat-card.orange .stat-icon { background: var(--c-orange-light); color: var(--c-orange); }
.stat-card.red    .stat-icon { background: var(--c-red-light);    color: var(--c-red); }

.stat-info { display: flex; flex-direction: column; justify-content: center; overflow: hidden; }
.stat-lbl {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: var(--t-label);
    margin-bottom: 4px;
    white-space: nowrap;
}
.stat-num {
    font-size: 21px;
    font-weight: 800;
    letter-spacing: -0.03em;
    color: var(--t-primary);
    line-height: 1.1;
    white-space: nowrap;
}

/* ---- Filter Shelf ---- */
.filter-shelf {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    align-items: center;
    margin-bottom: 24px;
    background: var(--c-surface);
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-lg);
    padding: 14px 20px;
    box-shadow: var(--shadow-xs);
}
.filter-chip {
    display: inline-flex; align-items: center; gap: 8px;
    background: var(--c-surface2);
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-pill);
    padding: 6px 14px;
    font-size: 13px;
    font-weight: 600;
    color: var(--t-secondary);
}
.filter-chip select {
    border: none;
    background: transparent;
    font-size: 13px;
    font-weight: 700;
    color: var(--t-primary);
    outline: none;
    cursor: pointer;
}

/* ---- Table Panels ---- */
.table-panel {
    background: var(--c-surface);
    border-radius: var(--r-xl);
    border: 0.5px solid var(--c-separator);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    margin-bottom: 28px;
}
.panel-header {
    padding: 18px 24px;
    background: var(--c-surface2);
    border-bottom: 0.5px solid var(--c-separator);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}
.panel-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 800;
    color: var(--t-primary);
    display: flex;
    align-items: center;
    gap: 8px;
}
.panel-subtitle {
    font-size: 12px;
    font-weight: 600;
    color: var(--t-label);
}

.sf-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
    font-size: 13.5px;
}
.sf-table th {
    background: var(--c-surface2);
    padding: 12px 18px;
    font-size: 11px;
    font-weight: 700;
    color: var(--t-label);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 0.5px solid var(--c-separator);
}
.sf-table td {
    padding: 14px 18px;
    border-bottom: 0.5px solid var(--c-separator2);
    vertical-align: middle;
    color: var(--t-primary);
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
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
}
.sf-badge .dot { width: 6px; height: 6px; border-radius: 50%; }
.badge-Unpaid { background: var(--c-red-light); color: #dc2626; }
.badge-Unpaid .dot { background: var(--c-red); }
.badge-Partially { background: var(--c-orange-light); color: #d97706; }
.badge-Partially .dot { background: var(--c-orange); }
.badge-Paid { background: var(--c-green-light); color: #16a34a; }
.badge-Paid .dot { background: var(--c-green); }

/* Buttons */
.btn-apple {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 700;
    border-radius: var(--r-pill);
    border: none;
    cursor: pointer;
    text-decoration: none !important;
    transition: transform var(--dur-fast) var(--ease-ios), opacity var(--dur-fast);
}
.btn-apple:hover { opacity: 0.92; transform: translateY(-1px); }
.btn-apple-primary { background: var(--c-blue); color: #ffffff !important; }
.btn-apple-success { background: #16a34a; color: #ffffff !important; }
.btn-apple-secondary { background: var(--c-fill2); color: var(--t-primary) !important; }

.act-group { display: flex; gap: 6px; justify-content: center; }

/* ---- Custom SF Glass Overlay Modal ---- */
.sf-modal-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 0, 0, 0.45);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 99999;
    padding: 20px;
    box-sizing: border-box;
}
.sf-modal-overlay.active {
    display: flex;
    animation: sfModalFadeIn 0.2s cubic-bezier(0.25, 0.1, 0.25, 1);
}
@keyframes sfModalFadeIn {
    from { opacity: 0; transform: scale(0.96); }
    to { opacity: 1; transform: scale(1); }
}
.sf-modal-card {
    background: var(--c-surface);
    width: 100%;
    max-width: 650px;
    border-radius: 24px;
    border: 0.5px solid var(--c-separator);
    box-shadow: 0 20px 50px rgba(0,0,0,0.2);
    overflow: hidden;
    display: flex;
    flex-direction: column;
}
.sf-modal-header {
    padding: 20px 24px;
    background: var(--c-surface2);
    border-bottom: 0.5px solid var(--c-separator);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.sf-modal-title {
    margin: 0;
    font-size: 18px;
    font-weight: 800;
    color: var(--t-primary);
    display: flex;
    align-items: center;
    gap: 10px;
}
.sf-modal-close {
    background: var(--c-fill2);
    border: none;
    width: 32px; height: 32px;
    border-radius: 50%;
    color: var(--t-secondary);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.15s;
}
.sf-modal-close:hover { background: var(--c-red-light); color: var(--c-red); }

.sf-modal-body {
    padding: 24px;
    max-height: calc(85vh - 130px);
    overflow-y: auto;
}
.sf-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 16px;
}
@media (max-width: 600px) {
    .sf-form-row { grid-template-columns: 1fr; }
}
.sf-form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.sf-form-label {
    font-size: 13px;
    font-weight: 700;
    color: var(--t-secondary);
    display: block;
}
.sf-form-control {
    width: 100% !important;
    box-sizing: border-box !important;
    padding: 11px 16px !important;
    height: 44px !important;
    border-radius: 12px !important;
    border: 1px solid var(--c-separator) !important;
    background: var(--c-surface2) !important;
    font-family: var(--f-system) !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    color: var(--t-primary) !important;
    outline: none !important;
    transition: all 0.18s ease !important;
}
.sf-form-control:focus {
    border-color: var(--c-blue) !important;
    box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.15) !important;
    background: var(--c-surface) !important;
}
textarea.sf-form-control {
    height: auto !important;
    min-height: 80px !important;
}

.sf-modal-footer {
    padding: 16px 24px;
    background: var(--c-surface2);
    border-top: 0.5px solid var(--c-separator);
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}
</style>

<div class="ee-root">
    <?php if (!empty($data['success'])): ?>
        <div class="flash-msg flash-msg-success">
            <i class="fa-solid fa-circle-check"></i>
            <span><?= htmlspecialchars($data['success']); ?></span>
        </div>
    <?php endif; ?>

    <?php if (!empty($data['error'])): ?>
        <div class="flash-msg flash-msg-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span><?= htmlspecialchars($data['error']); ?></span>
        </div>
    <?php endif; ?>

    <div class="sf-page-header">
        <div class="sf-page-title">
            <h1><i class="fa-solid fa-shield-halved" style="color: var(--c-blue);"></i> EPF & ETF Management</h1>
            <p>Manage Employees Provident Fund (20%) & Employees Trust Fund (3%) Contributions, Liabilities & Payments</p>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button type="button" onclick="openDirectPayModal()" class="btn-apple btn-apple-success" style="padding: 10px 22px;">
                <i class="fa-solid fa-plus"></i> Record EPF/ETF Payment
            </button>
            <a href="<?= APP_URL ?>/epfetf/reports" class="btn-apple btn-apple-primary" style="padding: 10px 22px;">
                <i class="fa-solid fa-chart-pie"></i> EPF/ETF Compliance Reports
            </a>
        </div>
    </div>

    <?php
    $totEpf = 0; $totEtf = 0; $pendingEpf = 0; $pendingEtf = 0;
    if (!empty($data['liabilities'])) {
        foreach ($data['liabilities'] as $l) {
            $epfLiab = floatval($l->total_epf_employee) + floatval($l->total_epf_employer);
            $etfLiab = floatval($l->total_etf_employer);
            $totEpf += $epfLiab;
            $totEtf += $etfLiab;
            $pendingEpf += max(0, $epfLiab - floatval($l->epf_paid_amount));
            $pendingEtf += max(0, $etfLiab - floatval($l->etf_paid_amount));
        }
    }
    ?>

    <!-- Apple-Style KPI Stat Cards Row -->
    <div class="stat-row">
        <div class="stat-card blue">
            <div class="stat-icon"><i class="fa-solid fa-shield-halved"></i></div>
            <div class="stat-info">
                <div class="stat-lbl">Total EPF Liability (20%)</div>
                <div class="stat-num">LKR <?= number_format($totEpf, 2) ?></div>
            </div>
        </div>

        <div class="stat-card purple">
            <div class="stat-icon"><i class="fa-solid fa-building-columns"></i></div>
            <div class="stat-info">
                <div class="stat-lbl">Total ETF Liability (3%)</div>
                <div class="stat-num">LKR <?= number_format($totEtf, 2) ?></div>
            </div>
        </div>

        <div class="stat-card orange">
            <div class="stat-icon"><i class="fa-solid fa-hourglass-half"></i></div>
            <div class="stat-info">
                <div class="stat-lbl">Pending EPF Payable</div>
                <div class="stat-num" style="color: var(--c-orange);">LKR <?= number_format($pendingEpf, 2) ?></div>
            </div>
        </div>

        <div class="stat-card red">
            <div class="stat-icon"><i class="fa-solid fa-circle-exclamation"></i></div>
            <div class="stat-info">
                <div class="stat-lbl">Pending ETF Payable</div>
                <div class="stat-num" style="color: var(--c-red);">LKR <?= number_format($pendingEtf, 2) ?></div>
            </div>
        </div>
    </div>

    <!-- Filter Shelf Bar -->
    <form method="GET" action="<?= APP_URL ?>/epfetf" class="filter-shelf">
        <div class="filter-chip">
            <i class="fa-solid fa-calendar-days" style="color: var(--c-blue);"></i>
            <span>Year:</span>
            <select name="year" onchange="this.form.submit()">
                <?php for($y = date('Y'); $y >= 2024; $y--): ?>
                    <option value="<?= $y ?>" <?= ($data['selected_year'] == $y) ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="filter-chip">
            <i class="fa-solid fa-calendar-minus" style="color: var(--c-purple);"></i>
            <span>Month:</span>
            <select name="month" onchange="this.form.submit()">
                <option value="">All Months</option>
                <?php for($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>" <?= ($data['selected_month'] == $m) ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="filter-chip">
            <i class="fa-solid fa-filter" style="color: var(--c-orange);"></i>
            <span>Status:</span>
            <select name="status" onchange="this.form.submit()">
                <option value="All">All Statuses</option>
                <option value="Unpaid" <?= ($data['selected_status'] === 'Unpaid') ? 'selected' : '' ?>>Unpaid</option>
                <option value="Partially Paid" <?= ($data['selected_status'] === 'Partially Paid') ? 'selected' : '' ?>>Partially Paid</option>
                <option value="Paid" <?= ($data['selected_status'] === 'Paid') ? 'selected' : '' ?>>Paid</option>
            </select>
        </div>
    </form>

    <!-- Monthly EPF/ETF Liabilities Panel -->
    <div class="table-panel">
        <div class="panel-header">
            <h3><i class="fa-solid fa-list-check" style="color: var(--c-blue);"></i> Monthly EPF & ETF Liabilities</h3>
            <span class="panel-subtitle">Liability Accounts: GL-2001 (EPF) & GL-2002 (ETF)</span>
        </div>
        <div style="overflow-x: auto;">
            <table class="sf-table">
                <thead>
                    <tr>
                        <th style="padding-left: 24px;">Period</th>
                        <th>Employees</th>
                        <th style="text-align: right;">Employee EPF (8%)</th>
                        <th style="text-align: right;">Employer EPF (12%)</th>
                        <th style="text-align: right; font-weight:800;">Total EPF (20%)</th>
                        <th style="text-align: right; font-weight:800;">Employer ETF (3%)</th>
                        <th style="text-align: center;">EPF Status</th>
                        <th style="text-align: center;">ETF Status</th>
                        <th style="text-align: center; padding-right: 24px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['liabilities'])): ?>
                        <?php foreach ($data['liabilities'] as $l): ?>
                            <?php 
                            $epfTot = floatval($l->total_epf_employee) + floatval($l->total_epf_employer);
                            $etfTot = floatval($l->total_etf_employer);
                            $epfBal = max(0, $epfTot - floatval($l->epf_paid_amount));
                            $etfBal = max(0, $etfTot - floatval($l->etf_paid_amount));
                            ?>
                            <tr>
                                <td style="padding-left: 24px; font-weight: 700;">
                                    <?= date('M Y', strtotime($l->period_end)) ?>
                                    <div style="font-size: 11.5px; color: var(--t-secondary); font-weight: 500; margin-top: 2px;">
                                        PR-<?= $l->payroll_run_id ?> (<?= date('M d', strtotime($l->period_start)) ?> - <?= date('M d', strtotime($l->period_end)) ?>)
                                    </div>
                                </td>
                                <td><span class="sf-badge" style="background: var(--c-fill2); color: var(--t-primary);"><?= $l->employee_count ?> Staff</span></td>
                                <td style="text-align: right;">LKR <?= number_format(floatval($l->total_epf_employee), 2) ?></td>
                                <td style="text-align: right;">LKR <?= number_format(floatval($l->total_epf_employer), 2) ?></td>
                                <td style="text-align: right; font-weight: 800; color: var(--c-blue);">
                                    LKR <?= number_format($epfTot, 2) ?>
                                    <?php if ($epfBal > 0 && $l->epf_paid_amount > 0): ?>
                                        <div style="font-size: 11px; color: var(--c-orange); font-weight: 700;">Bal: LKR <?= number_format($epfBal, 2) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right; font-weight: 800; color: var(--c-purple);">
                                    LKR <?= number_format($etfTot, 2) ?>
                                    <?php if ($etfBal > 0 && $l->etf_paid_amount > 0): ?>
                                        <div style="font-size: 11px; color: var(--c-orange); font-weight: 700;">Bal: LKR <?= number_format($etfBal, 2) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <span class="sf-badge badge-<?= str_replace(' ', '', $l->epf_payment_status) ?>">
                                        <span class="dot"></span> <?= htmlspecialchars($l->epf_payment_status) ?>
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="sf-badge badge-<?= str_replace(' ', '', $l->etf_payment_status) ?>">
                                        <span class="dot"></span> <?= htmlspecialchars($l->etf_payment_status) ?>
                                    </span>
                                </td>
                                <td style="text-align: center; padding-right: 24px;">
                                    <div class="act-group">
                                        <?php if ($epfBal > 0): ?>
                                            <button class="btn-apple btn-apple-primary" style="font-size: 11.5px; padding: 5px 12px;"
                                                    onclick="openPayModal(<?= $l->payroll_run_id ?>, 'EPF', <?= $epfBal ?>, '<?= date('M Y', strtotime($l->period_end)) ?>')">
                                                <i class="fa-solid fa-credit-card"></i> Pay EPF
                                            </button>
                                        <?php endif; ?>

                                        <?php if ($etfBal > 0): ?>
                                            <button class="btn-apple btn-apple-success" style="font-size: 11.5px; padding: 5px 12px;"
                                                    onclick="openPayModal(<?= $l->payroll_run_id ?>, 'ETF', <?= $etfBal ?>, '<?= date('M Y', strtotime($l->period_end)) ?>')">
                                                <i class="fa-solid fa-credit-card"></i> Pay ETF
                                            </button>
                                        <?php endif; ?>

                                        <?php if ($epfBal <= 0 && $etfBal <= 0): ?>
                                            <span style="font-size: 12px; color: var(--c-green); font-weight: 700;"><i class="fa-solid fa-circle-check"></i> Settled</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 60px 20px; color: var(--t-secondary);">
                                <i class="fa-solid fa-folder-open" style="font-size: 36px; opacity: 0.3; margin-bottom: 12px; display: block;"></i>
                                No approved payroll runs found for the selected period.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- EPF & ETF Payment History Log -->
    <div class="table-panel">
        <div class="panel-header">
            <h3><i class="fa-solid fa-clock-rotate-left" style="color: var(--c-purple);"></i> EPF & ETF Payment Settlement Log</h3>
            <span class="panel-subtitle">Bank Settlements & Journal References</span>
        </div>
        <div style="overflow-x: auto;">
            <table class="sf-table">
                <thead>
                    <tr>
                        <th style="padding-left: 24px;">Date</th>
                        <th>Type</th>
                        <th>Period</th>
                        <th style="text-align: right;">Amount Paid</th>
                        <th>Bank Account</th>
                        <th>Ref Number</th>
                        <th>Recorded By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['payment_history'])): ?>
                        <?php foreach ($data['payment_history'] as $p): ?>
                            <tr>
                                <td style="padding-left: 24px; font-weight: 600;"><?= date('M d, Y', strtotime($p->payment_date)) ?></td>
                                <td>
                                    <span class="sf-badge" style="background: <?= ($p->payment_type === 'EPF') ? 'var(--c-blue-light)' : 'var(--c-purple-light)' ?>; color: <?= ($p->payment_type === 'EPF') ? 'var(--c-blue)' : 'var(--c-purple)' ?>;">
                                        <?= $p->payment_type ?>
                                    </span>
                                </td>
                                <td><?= date('M Y', strtotime($p->period_end)) ?></td>
                                <td style="text-align: right; font-weight: 800; color: var(--c-green);">
                                    LKR <?= number_format(floatval($p->amount_paid), 2) ?>
                                </td>
                                <td><?= htmlspecialchars($p->bank_code . ' - ' . $p->bank_name) ?></td>
                                <td><code><?= htmlspecialchars($p->payment_reference ?? '') ?></code></td>
                                <td><?= htmlspecialchars($p->paid_by_user) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px 20px; color: var(--t-secondary);">
                                No EPF/ETF payment transactions recorded yet.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Custom SF Glass Overlay Modal for EPF/ETF Payment -->
<div class="sf-modal-overlay" id="payEpfEtfModalOverlay" onclick="if(event.target===this) closePayModal()">
    <div class="sf-modal-card">
        <div class="sf-modal-header">
            <h5 class="sf-modal-title" id="payModalTitle">
                <i class="fa-solid fa-credit-card" style="color: var(--c-blue);"></i> Record EPF / ETF Payment
            </h5>
            <button type="button" class="sf-modal-close" onclick="closePayModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form action="<?= APP_URL ?>/epfetf/pay" method="POST">
            <div class="sf-modal-body">
                <div class="sf-form-row">
                    <div class="sf-form-group">
                        <label class="sf-form-label">Select Payroll Period *</label>
                        <select name="payroll_run_id" id="modal_run_id" class="sf-form-control" onchange="onRunOrTypeChange()" required>
                            <?php if (!empty($data['liabilities'])): ?>
                                <?php foreach ($data['liabilities'] as $l): ?>
                                    <?php 
                                        $epfB = max(0, (floatval($l->total_epf_employee) + floatval($l->total_epf_employer)) - floatval($l->epf_paid_amount));
                                        $etfB = max(0, floatval($l->total_etf_employer) - floatval($l->etf_paid_amount));
                                    ?>
                                    <option value="<?= $l->payroll_run_id ?>" 
                                            data-epf-bal="<?= $epfB ?>" 
                                            data-etf-bal="<?= $etfB ?>" 
                                            data-period="<?= date('M Y', strtotime($l->period_end)) ?>">
                                        PR-<?= $l->payroll_run_id ?> — <?= date('M Y', strtotime($l->period_end)) ?> (EPF Bal: LKR <?= number_format($epfB, 2) ?> | ETF Bal: LKR <?= number_format($etfB, 2) ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="">No Unpaid Payroll Runs Available</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="sf-form-group">
                        <label class="sf-form-label">Payment Type *</label>
                        <select name="payment_type" id="modal_payment_type" class="sf-form-control" onchange="onRunOrTypeChange()" required>
                            <option value="EPF">EPF (Employees Provident Fund - 20%)</option>
                            <option value="ETF">ETF (Employees Trust Fund - 3%)</option>
                        </select>
                    </div>
                </div>

                <div class="sf-form-row">
                    <div class="sf-form-group">
                        <label class="sf-form-label">Payment Date *</label>
                        <input type="date" name="payment_date" class="sf-form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="sf-form-group">
                        <label class="sf-form-label">Select Bank Account *</label>
                        <select name="bank_account_id" class="sf-form-control" required>
                            <option value="">-- Choose Bank Account --</option>
                            <?php if (!empty($data['bank_accounts'])): foreach ($data['bank_accounts'] as $b): ?>
                                <option value="<?= $b->id ?>"><?= htmlspecialchars($b->account_code . ' - ' . $b->account_name) ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                </div>

                <div class="sf-form-row">
                    <div class="sf-form-group">
                        <label class="sf-form-label">Amount to Pay (LKR) *</label>
                        <input type="number" step="0.01" name="amount_paid" id="modal_amount_paid" class="sf-form-control" style="font-weight: 800; font-size: 16px; color: var(--c-blue);" required>
                        <small style="color: var(--c-orange); font-weight: 700; font-size: 11.5px;" id="modal_max_balance"></small>
                    </div>

                    <div class="sf-form-group">
                        <label class="sf-form-label">Bank Cheque / Payment Reference</label>
                        <input type="text" name="payment_reference" class="sf-form-control" placeholder="e.g. CHQ-948271 / CBO-EPF-1029">
                    </div>
                </div>

                <div class="sf-form-group">
                    <label class="sf-form-label">Notes</label>
                    <textarea name="notes" class="sf-form-control" rows="2" placeholder="Optional payment remarks or transaction reference notes..."></textarea>
                </div>
            </div>

            <div class="sf-modal-footer">
                <button type="button" class="btn-apple btn-apple-secondary" onclick="closePayModal()">Cancel</button>
                <button type="submit" class="btn-apple btn-apple-primary">Confirm & Post Payment Entry</button>
            </div>
        </form>
    </div>
</div>

<script>
function openDirectPayModal() {
    const overlay = document.getElementById('payEpfEtfModalOverlay');
    overlay.classList.add('active');
    onRunOrTypeChange();
}

function openPayModal(runId, type, balance, periodName) {
    const selectRun = document.getElementById('modal_run_id');
    const selectType = document.getElementById('modal_payment_type');
    
    if (selectRun) selectRun.value = runId;
    if (selectType) selectType.value = type;
    
    document.getElementById('modal_amount_paid').value = balance.toFixed(2);
    document.getElementById('modal_max_balance').innerText = 'Remaining ' + type + ' Liability Balance: LKR ' + balance.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

    const overlay = document.getElementById('payEpfEtfModalOverlay');
    overlay.classList.add('active');
}

function closePayModal() {
    const overlay = document.getElementById('payEpfEtfModalOverlay');
    overlay.classList.remove('active');
}

function onRunOrTypeChange() {
    const selectRun = document.getElementById('modal_run_id');
    const selectType = document.getElementById('modal_payment_type');
    if (!selectRun || !selectRun.options.length || selectRun.selectedIndex < 0) return;

    const opt = selectRun.options[selectRun.selectedIndex];
    const type = selectType.value;
    const epfBal = parseFloat(opt.getAttribute('data-epf-bal') || '0');
    const etfBal = parseFloat(opt.getAttribute('data-etf-bal') || '0');

    const targetBal = (type === 'EPF') ? epfBal : etfBal;
    document.getElementById('modal_amount_paid').value = targetBal.toFixed(2);
    document.getElementById('modal_max_balance').innerText = 'Remaining ' + type + ' Liability Balance: LKR ' + targetBal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}
</script>
