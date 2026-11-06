<?php
$advances = $data['advances'] ?? [];
$totalCount = $data['total_count'] ?? 0;
$totalPages = $data['total_pages'] ?? 1;
$currentPage = $data['current_page'] ?? 1;
$filters = $data['filters'] ?? [];
$metrics = $data['metrics'] ?? [];
$employees = $data['employees'] ?? [];
$bankAccounts = $data['bank_accounts'] ?? [];
?>

<!-- Apple SF Pro + Modern Design System Styles -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
:root {
    --c-bg: #f2f2f7;
    --c-surface: #ffffff;
    --c-surface2: #f9f9fb;
    --c-separator: rgba(60,60,67,0.12);
    --c-separator2: rgba(60,60,67,0.06);
    --c-blue: #007aff;
    --c-blue-light: #e5f2ff;
    --c-green: #34c759;
    --c-green-light: #e6f9ec;
    --c-purple: #af52de;
    --c-purple-light: #f5eeff;
    --c-orange: #ff9500;
    --c-orange-light: #fff4e5;
    --c-red: #ff3b30;
    --c-red-light: #fff0ef;
    --f-system: -apple-system, 'SF Pro Display', 'SF Pro Text', 'Inter', 'Helvetica Neue', sans-serif;
    --f-mono: ui-monospace, 'SF Mono', 'Menlo', monospace;
    --t-primary: #1c1c1e;
    --t-secondary: #636366;
    --t-tertiary: #aeaeb2;
    --shadow-sm: 0 2px 8px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.03);
    --shadow-md: 0 8px 24px rgba(0,0,0,0.08);
    --r-sm: 8px;
    --r-md: 12px;
    --r-lg: 16px;
    --r-xl: 22px;
}

.sa-root {
    font-family: var(--f-system);
    font-size: 14px;
    color: var(--t-primary);
    background: var(--c-bg);
    min-height: 100vh;
    padding-bottom: 80px;
}

.sa-wrap {
    max-width: 1440px;
    margin: 0 auto;
    padding: 24px 24px 60px;
}

/* Header */
.sa-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}
.sa-eyebrow {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--c-blue);
    margin-bottom: 4px;
}
.sa-title {
    font-size: 28px;
    font-weight: 800;
    letter-spacing: -0.02em;
    margin: 0 0 4px;
    color: var(--t-primary);
    display: flex;
    align-items: center;
    gap: 10px;
}
.sa-subtitle {
    font-size: 13.5px;
    color: var(--t-secondary);
    margin: 0;
}

/* Buttons */
.sa-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: var(--r-md);
    font-size: 13.5px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.18s ease;
}
.sa-btn.primary {
    background: var(--c-blue);
    color: #ffffff !important;
    box-shadow: 0 2px 8px rgba(0,122,255,0.28);
}
.sa-btn.primary:hover {
    background: #0066d6;
    transform: translateY(-1px);
}
.sa-btn.secondary {
    background: var(--c-surface);
    color: var(--t-primary) !important;
    border: 1px solid var(--c-separator);
}
.sa-btn.secondary:hover {
    background: var(--c-surface2);
}
.sa-btn.sm {
    padding: 6px 12px;
    font-size: 12px;
    border-radius: var(--r-sm);
}

/* Alerts */
.sa-alert {
    padding: 14px 18px;
    border-radius: var(--r-md);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 13.5px;
    font-weight: 500;
}
.sa-alert.success { background: var(--c-green-light); color: #1e7e34; border: 1px solid #c3e6cb; }
.sa-alert.error { background: var(--c-red-light); color: #d9251a; border: 1px solid #f5c6cb; }

/* Metrics Grid */
.sa-metrics-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 24px;
}
@media (max-width: 992px) {
    .sa-metrics-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 540px) {
    .sa-metrics-grid { grid-template-columns: 1fr; }
}

.sa-metric-card {
    background: var(--c-surface);
    border-radius: var(--r-lg);
    padding: 20px;
    border: 1px solid var(--c-separator);
    box-shadow: var(--shadow-sm);
    position: relative;
    overflow: hidden;
}
.sa-metric-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
}
.sa-metric-card.blue::before { background: var(--c-blue); }
.sa-metric-card.orange::before { background: var(--c-orange); }
.sa-metric-card.green::before { background: var(--c-green); }
.sa-metric-card.purple::before { background: var(--c-purple); }

.sa-metric-lbl {
    font-size: 12px;
    font-weight: 600;
    color: var(--t-secondary);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.sa-metric-val {
    font-size: 26px;
    font-weight: 800;
    letter-spacing: -0.02em;
    font-family: var(--f-mono);
}
.sa-metric-val.blue { color: var(--c-blue); }
.sa-metric-val.orange { color: var(--c-orange); }
.sa-metric-val.green { color: var(--c-green); }
.sa-metric-val.purple { color: var(--c-purple); }

/* Filter Bar */
.sa-filter-panel {
    background: var(--c-surface);
    border-radius: var(--r-lg);
    padding: 16px 20px;
    border: 1px solid var(--c-separator);
    box-shadow: var(--shadow-sm);
    margin-bottom: 24px;
}
.sa-filter-form {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    gap: 12px;
}
.sa-field {
    display: flex;
    flex-direction: column;
    gap: 4px;
    flex: 1;
    min-width: 140px;
}
.sa-field.search-field { flex: 2; min-width: 200px; }
.sa-field label {
    font-size: 11px;
    font-weight: 700;
    color: var(--t-secondary);
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.sa-input, .sa-select {
    height: 38px;
    padding: 0 12px;
    border-radius: var(--r-sm);
    border: 1px solid var(--c-separator);
    background: var(--c-surface2);
    font-size: 13px;
    color: var(--t-primary);
    font-family: var(--f-system);
    outline: none;
    transition: border-color 0.15s ease;
}
.sa-input:focus, .sa-select:focus {
    border-color: var(--c-blue);
    background: var(--c-surface);
}

/* Two-column layout */
.sa-layout-split {
    display: grid;
    grid-template-columns: 3fr 1fr;
    gap: 20px;
}
@media (max-width: 1100px) {
    .sa-layout-split { grid-template-columns: 1fr; }
}

/* Table Card */
.sa-table-panel {
    background: var(--c-surface);
    border-radius: var(--r-lg);
    border: 1px solid var(--c-separator);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}
.sa-panel-head {
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid var(--c-separator2);
}
.sa-panel-title {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    color: var(--t-primary);
    display: flex;
    align-items: center;
    gap: 8px;
}

.sa-table {
    width: 100%;
    border-collapse: collapse;
}
.sa-table th {
    background: var(--c-surface2);
    padding: 12px 16px;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--t-secondary);
    text-align: left;
    border-bottom: 1px solid var(--c-separator);
}
.sa-table td {
    padding: 14px 16px;
    font-size: 13px;
    border-bottom: 1px solid var(--c-separator2);
    color: var(--t-primary);
    vertical-align: middle;
}
.sa-table tbody tr:hover {
    background: #fbfbfd;
}

/* Status Badges */
.sa-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 11.5px;
    font-weight: 700;
    letter-spacing: 0.02em;
}
.sa-badge.paid { background: #e5f2ff; color: var(--c-blue); }
.sa-badge.partially-recovered { background: #fff4e5; color: #b45309; }
.sa-badge.fully-recovered { background: #e6f9ec; color: #15803d; }
.sa-badge.cancelled { background: #fee2e2; color: #b91c1c; }

.sa-method-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    background: var(--c-surface2);
    border: 1px solid var(--c-separator);
    color: var(--t-secondary);
}
.sa-method-badge.petty-cash { background: #fdf2f8; border-color: #fbcfe8; color: #9d174d; }
.sa-method-badge.bank-transfer { background: #eff6ff; border-color: #bfdbfe; color: #1d4ed8; }
.sa-method-badge.cash-in-hand { background: #f0fdf4; border-color: #bbf7d0; color: #15803d; }

/* Outstanding Balances Sidebar */
.sa-balances-panel {
    background: var(--c-surface);
    border-radius: var(--r-lg);
    border: 1px solid var(--c-separator);
    box-shadow: var(--shadow-sm);
    padding: 18px;
}
.sa-balance-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid var(--c-separator2);
}
.sa-balance-item:last-child { border-bottom: none; }
.sa-bal-name { font-weight: 600; font-size: 13px; color: var(--t-primary); }
.sa-bal-sub { font-size: 11px; color: var(--t-secondary); margin-top: 2px; }
.sa-bal-val { font-family: var(--f-mono); font-weight: 700; font-size: 13.5px; color: var(--c-red); }

/* Modal */
.sa-modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.45);
    backdrop-filter: blur(4px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.sa-modal-overlay.active { display: flex; }
.sa-modal-card {
    background: var(--c-surface);
    border-radius: var(--r-xl);
    width: 100%;
    max-width: 620px;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--c-separator);
    overflow: hidden;
    animation: saModalSlide 0.22s ease-out;
}
@keyframes saModalSlide {
    from { opacity: 0; transform: scale(0.96) translateY(10px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
.sa-modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--c-separator2);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.sa-modal-title {
    font-size: 18px;
    font-weight: 800;
    margin: 0;
    color: var(--t-primary);
    display: flex;
    align-items: center;
    gap: 8px;
}
.sa-modal-body {
    padding: 24px;
    max-height: 80vh;
    overflow-y: auto;
}
.sa-modal-footer {
    padding: 16px 24px;
    border-top: 1px solid var(--c-separator2);
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    background: var(--c-surface2);
}

.sa-form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
.sa-form-grid .full { grid-column: span 2; }
.sa-form-lbl {
    font-size: 12px;
    font-weight: 700;
    color: var(--t-primary);
    margin-bottom: 6px;
    display: block;
}
.sa-form-lbl span { color: var(--c-red); }
.sa-form-input, .sa-form-select, .sa-form-textarea {
    width: 100%;
    box-sizing: border-box;
    padding: 10px 14px;
    border-radius: var(--r-sm);
    border: 1px solid var(--c-separator);
    background: var(--c-surface2);
    font-size: 13.5px;
    color: var(--t-primary);
    font-family: var(--f-system);
    outline: none;
    transition: all 0.15s ease;
}
.sa-form-input:focus, .sa-form-select:focus, .sa-form-textarea:focus {
    border-color: var(--c-blue);
    background: var(--c-surface);
    box-shadow: 0 0 0 3px rgba(0,122,255,0.12);
}
.sa-form-textarea { resize: vertical; min-height: 70px; }

.sa-coa-callout {
    background: #f0f7ff;
    border-left: 4px solid var(--c-blue);
    padding: 12px 14px;
    border-radius: 4px;
    font-size: 12.5px;
    color: #1e40af;
    margin-top: 6px;
}
</style>

<div class="sa-root">
    <div class="sa-wrap">

        <!-- Header -->
        <div class="sa-header">
            <div>
                <div class="sa-eyebrow"><i class="fa-solid fa-briefcase"></i> Operations &bull; Human Resources</div>
                <h1 class="sa-title">
                    <i class="fa-solid fa-hand-holding-dollar" style="color: var(--c-blue);"></i> Salary Advance Management
                </h1>
                <p class="sa-subtitle">Issue advances, link payment sources, manage asset accounts and automate payroll recovery.</p>
            </div>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="<?= APP_URL ?>/salaryadvance/export_csv?<?= http_build_query($filters) ?>" class="sa-btn secondary">
                    <i class="fa-solid fa-file-csv"></i> Export CSV
                </a>
                <button type="button" onclick="openIssueModal()" class="sa-btn primary">
                    <i class="fa-solid fa-plus-circle"></i> Issue New Advance
                </button>
            </div>
        </div>

        <!-- Flash Alerts -->
        <?php if(!empty($data['success'])): ?>
            <div class="sa-alert success">
                <i class="fa-solid fa-circle-check"></i>
                <div><?= htmlspecialchars($data['success']) ?></div>
            </div>
        <?php endif; ?>
        <?php if(!empty($data['error'])): ?>
            <div class="sa-alert error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div><?= htmlspecialchars($data['error']) ?></div>
            </div>
        <?php endif; ?>

        <!-- KPI Metrics Grid -->
        <div class="sa-metrics-grid">
            <div class="sa-metric-card blue">
                <div class="sa-metric-lbl"><i class="fa-solid fa-coins"></i> Total Issued</div>
                <div class="sa-metric-val blue">Rs. <?= number_format($metrics['total_issued'] ?? 0, 2) ?></div>
            </div>

            <div class="sa-metric-card orange">
                <div class="sa-metric-lbl"><i class="fa-solid fa-clock-rotate-left"></i> Total Outstanding</div>
                <div class="sa-metric-val orange">Rs. <?= number_format($metrics['total_outstanding'] ?? 0, 2) ?></div>
            </div>

            <div class="sa-metric-card purple">
                <div class="sa-metric-lbl"><i class="fa-solid fa-calendar-check"></i> Issued This Month</div>
                <div class="sa-metric-val purple">Rs. <?= number_format($metrics['month_issued'] ?? 0, 2) ?></div>
            </div>

            <div class="sa-metric-card green">
                <div class="sa-metric-lbl"><i class="fa-solid fa-circle-check"></i> Total Recovered</div>
                <div class="sa-metric-val green">Rs. <?= number_format($metrics['total_recovered'] ?? 0, 2) ?></div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="sa-filter-panel">
            <form action="<?= APP_URL ?>/salaryadvance" method="GET" class="sa-filter-form">
                <div class="sa-field search-field">
                    <label>Search</label>
                    <input type="text" name="search" class="sa-input" placeholder="Advance #, Employee, Ref..." value="<?= htmlspecialchars($filters['search']) ?>">
                </div>

                <div class="sa-field">
                    <label>Employee</label>
                    <select name="employee_id" class="sa-select">
                        <option value="">All Employees</option>
                        <?php foreach($employees as $emp): ?>
                            <option value="<?= $emp->id ?>" <?= ($filters['employee_id'] == $emp->id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($emp->first_name . ' ' . $emp->last_name) ?> (<?= htmlspecialchars($emp->advance_account_code) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="sa-field">
                    <label>Status</label>
                    <select name="status" class="sa-select">
                        <option value="All" <?= ($filters['status'] === 'All') ? 'selected' : '' ?>>All Statuses</option>
                        <option value="Paid" <?= ($filters['status'] === 'Paid') ? 'selected' : '' ?>>Paid (Unrecovered)</option>
                        <option value="Partially Recovered" <?= ($filters['status'] === 'Partially Recovered') ? 'selected' : '' ?>>Partially Recovered</option>
                        <option value="Fully Recovered" <?= ($filters['status'] === 'Fully Recovered') ? 'selected' : '' ?>>Fully Recovered</option>
                        <option value="Cancelled" <?= ($filters['status'] === 'Cancelled') ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>

                <div class="sa-field">
                    <label>Date From</label>
                    <input type="date" name="start_date" class="sa-input" value="<?= htmlspecialchars($filters['start_date']) ?>">
                </div>

                <div class="sa-field">
                    <label>Date To</label>
                    <input type="date" name="end_date" class="sa-input" value="<?= htmlspecialchars($filters['end_date']) ?>">
                </div>

                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="sa-btn primary sm"><i class="fa-solid fa-filter"></i> Apply</button>
                    <a href="<?= APP_URL ?>/salaryadvance" class="sa-btn secondary sm"><i class="fa-solid fa-rotate-left"></i> Reset</a>
                </div>
            </form>
        </div>

        <!-- Main Layout Split -->
        <div class="sa-layout-split">
            
            <!-- Advances History Table -->
            <div class="sa-table-panel">
                <div class="sa-panel-head">
                    <h3 class="sa-panel-title">
                        <i class="fa-solid fa-list-check" style="color: var(--c-blue);"></i> Salary Advance History
                    </h3>
                    <span style="font-size: 12px; font-weight: 600; color: var(--t-secondary);">
                        Showing <?= count($advances) ?> of <?= $totalCount ?> records
                    </span>
                </div>

                <div style="overflow-x: auto;">
                    <table class="sa-table">
                        <thead>
                            <tr>
                                <th>Advance #</th>
                                <th>Employee</th>
                                <th>Date</th>
                                <th style="text-align: right;">Amount</th>
                                <th style="text-align: right;">Recovered</th>
                                <th style="text-align: right;">Outstanding</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($advances)): ?>
                                <tr>
                                    <td colspan="9" style="text-align: center; padding: 40px; color: var(--t-secondary);">
                                        <i class="fa-solid fa-inbox" style="font-size: 28px; margin-bottom: 8px; display: block; opacity: 0.5;"></i>
                                        No salary advances found matching the criteria.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($advances as $row): 
                                    $stClass = strtolower(str_replace(' ', '-', $row->status));
                                    $mClass = strtolower(str_replace(' ', '-', $row->payment_method));
                                ?>
                                    <tr>
                                        <td>
                                            <a href="<?= APP_URL ?>/salaryadvance/voucher/<?= $row->id ?>" target="_blank" style="font-weight: 700; font-family: var(--f-mono); text-decoration: none; color: var(--c-blue);">
                                                <?= htmlspecialchars($row->advance_number) ?>
                                            </a>
                                            <?php if(!empty($row->reference_no)): ?>
                                                <div style="font-size: 11px; color: var(--t-secondary);"><?= htmlspecialchars($row->reference_no) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div style="font-weight: 700;">
                                                <?= htmlspecialchars($row->first_name . ' ' . $row->last_name) ?>
                                            </div>
                                            <div style="font-size: 11px; color: var(--t-secondary);">
                                                <?= htmlspecialchars($row->department ?? 'General') ?> &bull; <?= htmlspecialchars($row->advance_account_code) ?>
                                            </div>
                                        </td>
                                        <td style="white-space: nowrap; font-size: 12.5px;">
                                            <?= date('d M Y', strtotime($row->payment_date)) ?>
                                        </td>
                                        <td style="text-align: right; font-weight: 700; font-family: var(--f-mono);">
                                            Rs. <?= number_format(floatval($row->advance_amount), 2) ?>
                                        </td>
                                        <td style="text-align: right; color: var(--c-green); font-family: var(--f-mono);">
                                            Rs. <?= number_format(floatval($row->recovered_amount), 2) ?>
                                        </td>
                                        <td style="text-align: right; font-weight: 800; font-family: var(--f-mono); color: <?= floatval($row->outstanding_amount) > 0 ? 'var(--c-red)' : 'var(--t-secondary)' ?>;">
                                            Rs. <?= number_format(floatval($row->outstanding_amount), 2) ?>
                                        </td>
                                        <td>
                                            <span class="sa-method-badge <?= $mClass ?>">
                                                <?php if($row->payment_method === 'Petty Cash'): ?><i class="fa-solid fa-wallet"></i><?php endif; ?>
                                                <?php if($row->payment_method === 'Cash in Hand'): ?><i class="fa-solid fa-money-bill-wave"></i><?php endif; ?>
                                                <?php if($row->payment_method === 'Bank Transfer'): ?><i class="fa-solid fa-building-columns"></i><?php endif; ?>
                                                <?php if($row->payment_method === 'Cheque'): ?><i class="fa-solid fa-money-check"></i><?php endif; ?>
                                                <?= htmlspecialchars($row->payment_method) ?>
                                            </span>
                                            <div style="font-size: 10.5px; color: var(--t-tertiary); margin-top: 2px;">
                                                Src: <?= htmlspecialchars($row->source_account_code) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="sa-badge <?= $stClass ?>">
                                                &bull; <?= htmlspecialchars($row->status) ?>
                                            </span>
                                        </td>
                                        <td style="text-align: right; white-space: nowrap;">
                                            <a href="<?= APP_URL ?>/salaryadvance/voucher/<?= $row->id ?>" target="_blank" class="sa-btn secondary sm" title="Print Voucher">
                                                <i class="fa-solid fa-print"></i>
                                            </a>
                                            <?php if($row->status !== 'Cancelled' && floatval($row->recovered_amount) <= 0.001): ?>
                                                <form action="<?= APP_URL ?>/salaryadvance/cancel/<?= $row->id ?>" method="POST" style="display: inline-block; margin: 0;" onsubmit="return confirm('Are you sure you want to cancel and void this advance? This will reverse the posted journal entry.');">
                                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                    <button type="submit" class="sa-btn secondary sm" style="color: var(--c-red);" title="Cancel / Reverse Advance">
                                                        <i class="fa-solid fa-ban"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if($totalPages > 1): ?>
                    <div style="padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--c-separator2);">
                        <div style="font-size: 12.5px; color: var(--t-secondary);">
                            Page <?= $currentPage ?> of <?= $totalPages ?>
                        </div>
                        <div style="display: flex; gap: 6px;">
                            <?php if($currentPage > 1): ?>
                                <a href="<?= APP_URL ?>/salaryadvance?<?= http_build_query(array_merge($filters, ['page' => $currentPage - 1])) ?>" class="sa-btn secondary sm">Previous</a>
                            <?php endif; ?>
                            <?php if($currentPage < $totalPages): ?>
                                <a href="<?= APP_URL ?>/salaryadvance?<?= http_build_query(array_merge($filters, ['page' => $currentPage + 1])) ?>" class="sa-btn secondary sm">Next</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Employee-wise Outstanding Balances Sidebar -->
            <div class="sa-balances-panel">
                <h4 style="margin: 0 0 14px 0; font-size: 14px; font-weight: 700; color: var(--t-primary); display: flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-user-clock" style="color: var(--c-orange);"></i> Outstanding By Employee
                </h4>

                <?php 
                $activeBalances = $metrics['employee_balances'] ?? [];
                if(empty($activeBalances)): 
                ?>
                    <div style="font-size: 12.5px; color: var(--t-secondary); text-align: center; padding: 20px 0;">
                        <i class="fa-solid fa-check-double" style="color: var(--c-green); font-size: 20px; display: block; margin-bottom: 6px;"></i>
                        All employee advances have been recovered.
                    </div>
                <?php else: ?>
                    <?php foreach($activeBalances as $bal): ?>
                        <div class="sa-balance-item">
                            <div>
                                <a href="<?= APP_URL ?>/salaryadvance?employee_id=<?= $bal->employee_id ?>" style="text-decoration: none;" class="sa-bal-name">
                                    <?= htmlspecialchars($bal->first_name . ' ' . $bal->last_name) ?>
                                </a>
                                <div class="sa-bal-sub">
                                    <?= htmlspecialchars($bal->department ?? 'General') ?> &bull; <?= $bal->advance_count ?> Active
                                </div>
                            </div>
                            <div class="sa-bal-val">
                                Rs. <?= number_format(floatval($bal->outstanding_advance), 2) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>

    </div>
</div>

<!-- Modal: Issue New Salary Advance -->
<div id="issueAdvanceModal" class="sa-modal-overlay">
    <div class="sa-modal-card">
        <div class="sa-modal-header">
            <h3 class="sa-modal-title">
                <i class="fa-solid fa-file-invoice-dollar" style="color: var(--c-blue);"></i> Issue New Salary Advance
            </h3>
            <button type="button" onclick="closeIssueModal()" style="background: none; border: none; font-size: 18px; color: var(--t-secondary); cursor: pointer;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="<?= APP_URL ?>/salaryadvance/store" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="sa-modal-body">
                <div class="sa-form-grid">
                    
                    <!-- Employee -->
                    <div class="full">
                        <label class="sa-form-lbl">Select Employee <span>*</span></label>
                        <select name="employee_id" id="modalEmployeeSelect" class="sa-form-select" required onchange="handleEmployeeChange()">
                            <option value="">-- Choose Employee --</option>
                            <?php foreach($employees as $emp): ?>
                                <option value="<?= $emp->id ?>" 
                                        data-coa="<?= htmlspecialchars($emp->advance_account_code . ' - ' . $emp->advance_account_name) ?>"
                                        data-out="<?= number_format($emp->outstanding_advance, 2) ?>"
                                        data-salary="<?= number_format(floatval($emp->base_salary), 2) ?>">
                                    <?= htmlspecialchars($emp->first_name . ' ' . $emp->last_name) ?> (<?= htmlspecialchars($emp->department ?? 'General') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <div id="employeeCoaCallout" class="sa-coa-callout" style="display: none;">
                            <strong>COA Mapping:</strong> <span id="calloutAccountName">-</span><br>
                            <strong>Current Outstanding Advance:</strong> Rs. <span id="calloutOutstanding">-</span>
                        </div>
                    </div>

                    <!-- Advance Amount -->
                    <div>
                        <label class="sa-form-lbl">Advance Amount (Rs.) <span>*</span></label>
                        <input type="number" step="0.01" min="1" name="advance_amount" class="sa-form-input" placeholder="e.g. 10000" required>
                    </div>

                    <!-- Payment Date -->
                    <div>
                        <label class="sa-form-lbl">Payment Date <span>*</span></label>
                        <input type="date" name="payment_date" class="sa-form-input" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <!-- Payment Method -->
                    <div class="full">
                        <label class="sa-form-lbl">Payment Method <span>*</span></label>
                        <select name="payment_method" id="modalPaymentMethod" class="sa-form-select" required onchange="handlePaymentMethodChange()">
                            <option value="Petty Cash" selected>Petty Cash (1020 - Petty Cash &bull; Auto-Syncs to Petty Cash Management)</option>
                            <option value="Cash in Hand">Cash in Hand (1000 - Cash in Hand)</option>
                            <option value="Bank Transfer">Bank Transfer (Select Registered Bank)</option>
                            <option value="Cheque">Cheque (Select Bank Account)</option>
                        </select>
                    </div>

                    <!-- Registered Bank Account (Conditional) -->
                    <div class="full" id="bankAccountGroup" style="display: none;">
                        <label class="sa-form-lbl">Source Bank Account <span>*</span></label>
                        <select name="bank_account_id" id="modalBankAccount" class="sa-form-select">
                            <option value="">-- Choose Bank Account --</option>
                            <?php foreach($bankAccounts as $ba): ?>
                                <option value="<?= $ba->id ?>">
                                    <?= htmlspecialchars($ba->bank_name) ?> - <?= htmlspecialchars($ba->account_number) ?> (<?= htmlspecialchars($ba->account_code) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Reference Number -->
                    <div>
                        <label class="sa-form-lbl">Reference / Cheque No.</label>
                        <input type="text" name="reference_no" class="sa-form-input" placeholder="e.g. CHQ-00192 or Transfer Ref">
                    </div>

                    <!-- Description / Notes -->
                    <div class="full">
                        <label class="sa-form-lbl">Description / Notes</label>
                        <textarea name="notes" class="sa-form-textarea" placeholder="Add any details, reason, or notes..."></textarea>
                    </div>

                </div>
            </div>

            <div class="sa-modal-footer">
                <button type="button" onclick="closeIssueModal()" class="sa-btn secondary">Cancel</button>
                <button type="submit" class="sa-btn primary"><i class="fa-solid fa-check"></i> Issue &amp; Post Entries</button>
            </div>
        </form>
    </div>
</div>

<script>
function openIssueModal() {
    document.getElementById('issueAdvanceModal').classList.add('active');
}
function closeIssueModal() {
    document.getElementById('issueAdvanceModal').classList.remove('active');
}

function handleEmployeeChange() {
    const select = document.getElementById('modalEmployeeSelect');
    const opt = select.options[select.selectedIndex];
    const callout = document.getElementById('employeeCoaCallout');
    
    if (select.value) {
        document.getElementById('calloutAccountName').innerText = opt.getAttribute('data-coa');
        document.getElementById('calloutOutstanding').innerText = opt.getAttribute('data-out');
        callout.style.display = 'block';
    } else {
        callout.style.display = 'none';
    }
}

function handlePaymentMethodChange() {
    const method = document.getElementById('modalPaymentMethod').value;
    const bankGroup = document.getElementById('bankAccountGroup');
    const bankSelect = document.getElementById('modalBankAccount');
    
    if (method === 'Bank Transfer' || method === 'Cheque') {
        bankGroup.style.display = 'block';
        bankSelect.setAttribute('required', 'required');
    } else {
        bankGroup.style.display = 'none';
        bankSelect.removeAttribute('required');
    }
}

window.onclick = function(event) {
    const modal = document.getElementById('issueAdvanceModal');
    if (event.target === modal) {
        closeIssueModal();
    }
};
</script>

