<?php
// Dynamic Stats Calculation
$staffList = $data['staff'] ?? [];
$totalStaff = count($staffList);
$activeStaff = 0;
$totalBaseSalary = 0;
$systemLoginsCount = 0;
$departments = [];

foreach ($staffList as $st) {
    $stStatus = $st->employee_status ?: $st->user_status ?: 'Active';
    if ($stStatus === 'Active') {
        $activeStaff++;
        $totalBaseSalary += (float)($st->base_salary ?? 0);
    }
    if (!empty($st->username)) {
        $systemLoginsCount++;
    }
    if (!empty($st->department) && !in_array($st->department, $departments)) {
        $departments[] = $st->department;
    }
}
sort($departments);
?>

<!-- Inter Font & FontAwesome Icons -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
/* ============================================================
   SF PRO + APPLE DESIGN LANGUAGE — STAFF & USER REGISTRY
   ============================================================ */

:root {
    --c-bg:           #f2f2f7;
    --c-surface:      #ffffff;
    --c-surface2:     #f9f9fb;
    --c-fill:         rgba(120,120,128,0.10);
    --c-fill2:        rgba(120,120,128,0.16);
    --c-separator:    rgba(60,60,67,0.12);
    --c-separator2:   rgba(60,60,67,0.06);

    --c-blue:         #007aff;
    --c-blue-light:   #e5f2ff;
    --c-green:        #34c759;
    --c-green-light:  #e6f9ec;
    --c-purple:       #af52de;
    --c-purple-light: #f5eeff;
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

    --ease-spring: cubic-bezier(0.34, 1.56, 0.64, 1);
    --ease-ios:    cubic-bezier(0.25, 0.1, 0.25, 1);
    --dur-fast:    0.18s;
    --dur-mid:     0.28s;
    --dur-slow:    0.42s;
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

.sf-root {
    font-family: var(--f-system);
    font-size: 15px;
    color: var(--t-primary);
    background: var(--c-bg);
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

.sf-wrap {
    max-width: 1420px;
    margin: 0 auto;
    padding: 24px 24px 100px;
}

/* ---- Header ---- */
.sf-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}
.sf-eyebrow {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--c-blue);
    margin-bottom: 4px;
}
.sf-title {
    font-size: 30px;
    font-weight: 800;
    letter-spacing: -0.03em;
    line-height: 1.1;
    color: var(--t-primary);
    margin: 0 0 4px 0;
}
.sf-subtitle {
    font-size: 13.5px;
    color: var(--t-secondary);
    margin: 0;
}

.sf-header-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

/* ---- Stat Cards ---- */
.stat-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 24px;
}
@media (max-width: 1024px) {
    .stat-row { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 580px) {
    .stat-row { grid-template-columns: 1fr; }
}

.stat-card {
    background: var(--c-surface);
    border-radius: var(--r-xl);
    padding: 16px 20px;
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
.stat-card.green::before  { background: var(--c-green); }
.stat-card.purple::before { background: var(--c-purple); }
.stat-card.orange::before { background: var(--c-orange); }

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}
.stat-icon {
    width: 44px; height: 44px;
    border-radius: var(--r-md);
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.stat-card.blue   .stat-icon { background: var(--c-blue-light);   color: var(--c-blue); }
.stat-card.green  .stat-icon { background: var(--c-green-light);  color: var(--c-green); }
.stat-card.purple .stat-icon { background: var(--c-purple-light); color: var(--c-purple); }
.stat-card.orange .stat-icon { background: var(--c-orange-light); color: var(--c-orange); }

.stat-info { display: flex; flex-direction: column; justify-content: center; }
.stat-num {
    font-size: 22px;
    font-weight: 800;
    letter-spacing: -0.04em;
    color: var(--t-primary);
    line-height: 1.1;
    margin-bottom: 2px;
}
.stat-lbl {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: var(--t-label);
}

/* ---- Filter Shelf ---- */
.filter-shelf {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
    margin-bottom: 20px;
}
.filter-chip {
    display: inline-flex; align-items: center; gap: 8px;
    background: var(--c-surface);
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-pill);
    padding: 6px 14px;
    font-size: 13px;
    font-weight: 500;
    color: var(--t-secondary);
    box-shadow: var(--shadow-xs);
    transition: border-color var(--dur-fast), box-shadow var(--dur-fast);
}
.filter-chip:focus-within {
    border-color: var(--c-blue);
    box-shadow: 0 0 0 3px rgba(0,122,255,0.12);
}
.filter-input {
    border: none;
    outline: none;
    background: transparent;
    font-family: var(--f-system);
    font-size: 13px;
    color: var(--t-primary);
    width: 220px;
}
.filter-select {
    border: none;
    outline: none;
    background: transparent;
    font-family: var(--f-system);
    font-size: 13px;
    font-weight: 600;
    color: var(--t-primary);
    cursor: pointer;
}
.filter-select option {
    background: var(--c-surface);
    color: var(--t-primary);
}
.filter-reset {
    background: transparent;
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-pill);
    padding: 7px 14px;
    font-size: 12.5px;
    font-weight: 600;
    color: var(--t-secondary);
    cursor: pointer;
    transition: all var(--dur-fast);
}
.filter-reset:hover { background: var(--c-fill); color: var(--t-primary); }

.filter-count {
    margin-left: auto;
    font-size: 13px;
    color: var(--t-secondary);
    font-weight: 500;
}
.filter-count strong { color: var(--t-primary); font-weight: 700; }

/* ---- Alerts ---- */
.sf-alert {
    display: flex; align-items: flex-start; gap: 12px;
    background: var(--c-surface);
    border-radius: var(--r-md);
    padding: 14px 16px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-xs);
    border: 0.5px solid var(--c-separator);
    border-left-width: 4px;
    font-size: 13.5px;
}
.sf-alert.success { border-left-color: var(--c-green); }
.sf-alert.error   { border-left-color: var(--c-red); }
.sf-alert-icon { font-size: 17px; flex-shrink: 0; padding-top: 1px; }
.sf-alert.success .sf-alert-icon { color: var(--c-green); }
.sf-alert.error   .sf-alert-icon { color: var(--c-red); }
.sf-alert-msg   { color: var(--t-primary); font-weight: 500; }

/* ---- Table Panel ---- */
.table-panel {
    background: var(--c-surface);
    border-radius: var(--r-xl);
    border: 0.5px solid var(--c-separator);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    position: relative;
}
.sf-table { width: 100%; border-collapse: collapse; }
.sf-table thead th {
    padding: 13px 18px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--t-label);
    background: var(--c-surface2);
    border-bottom: 0.5px solid var(--c-separator);
    white-space: nowrap;
    text-align: left;
}
.sf-table tbody tr {
    transition: background var(--dur-fast);
    border-bottom: 0.5px solid var(--c-separator2);
}
.sf-table tbody tr:last-child { border-bottom: none; }
.sf-table tbody tr:hover { background: var(--c-fill); }
.sf-table td {
    padding: 14px 18px;
    font-size: 13.5px;
    color: var(--t-primary);
    vertical-align: middle;
}

/* ---- Badges & Elements ---- */
.avatar-badge {
    width: 38px; height: 38px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--c-blue-light), var(--c-purple-light));
    color: var(--c-blue);
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 14px; font-weight: 800;
    flex-shrink: 0;
    border: 1px solid var(--c-separator2);
}

.role-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 8px;
    border-radius: var(--r-xs);
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}
.role-admin      { background: var(--c-red-light); color: var(--c-red); }
.role-office     { background: var(--c-purple-light); color: var(--c-purple); }
.role-rep        { background: var(--c-orange-light); color: var(--c-orange); }
.role-driver     { background: var(--c-green-light); color: var(--c-green); }
.role-accountant { background: var(--c-blue-light); color: var(--c-blue); }
.role-default    { background: var(--c-fill); color: var(--t-secondary); }

.app-pill {
    font-size: 10px;
    padding: 2px 7px;
    background: var(--c-fill);
    color: var(--t-secondary);
    border-radius: var(--r-pill);
    font-weight: 600;
    display: inline-block;
    border: 0.5px solid var(--c-separator);
}

.sf-status-pill {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; border-radius: var(--r-pill);
    font-size: 11.5px; font-weight: 700;
}
.sf-status-pill.active { background: var(--c-green-light); color: var(--c-green); }
.sf-status-pill.terminated, .sf-status-pill.blocked { background: var(--c-red-light); color: var(--c-red); }
.sf-status-pill .dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

/* ---- Buttons ---- */
.sf-btn {
    padding: 8px 16px;
    border-radius: var(--r-pill);
    font-size: 13px; font-weight: 600;
    display: inline-flex; align-items: center; gap: 6px;
    border: 0.5px solid transparent; cursor: pointer;
    transition: transform var(--dur-fast) var(--ease-spring), background var(--dur-fast), box-shadow var(--dur-fast);
    text-decoration: none;
    font-family: var(--f-system);
}
.sf-btn:active { transform: scale(0.97); }
.sf-btn.primary { background: var(--c-blue); color: #fff; box-shadow: 0 2px 8px rgba(0,122,255,0.25); }
.sf-btn.primary:hover { background: #0066d6; }
.sf-btn.neutral { background: var(--c-surface); border-color: var(--c-separator); color: var(--t-primary); box-shadow: var(--shadow-xs); }
.sf-btn.neutral:hover { background: var(--c-surface2); }
.sf-btn.danger { background: var(--c-red); color: #fff; box-shadow: 0 2px 8px rgba(255,59,48,0.25); }
.sf-btn.danger:hover { background: #e03126; }
.sf-btn.sm { padding: 5px 12px; font-size: 12px; }

/* ---- SF Glass Overlay Modals ---- */
.sf-modal-overlay {
    display: none;
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.45);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.sf-modal-card {
    background: var(--c-surface);
    border: 0.5px solid var(--c-separator);
    box-shadow: var(--shadow-xl);
    border-radius: var(--r-xl);
    padding: 28px;
    width: 100%;
    max-width: 680px;
    color: var(--t-primary);
    box-sizing: border-box;
    max-height: 90vh;
    overflow-y: auto;
    animation: modalPop var(--dur-mid) var(--ease-ios);
}
@keyframes modalPop {
    from { opacity: 0; transform: scale(0.96) translateY(8px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}

.sf-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    padding-bottom: 14px;
    border-bottom: 0.5px solid var(--c-separator);
}
.sf-modal-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--t-primary);
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
}
.sf-modal-close {
    background: var(--c-fill);
    border: none;
    width: 30px; height: 30px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: var(--t-secondary);
    cursor: pointer;
    font-size: 14px;
    transition: background var(--dur-fast);
}
.sf-modal-close:hover { background: var(--c-fill2); color: var(--t-primary); }

.sf-form-section {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--c-blue);
    margin: 16px 0 10px 0;
    padding-bottom: 4px;
    border-bottom: 0.5px solid var(--c-separator2);
}

.sf-field { margin-bottom: 16px; }
.sf-field label {
    display: block;
    margin-bottom: 6px;
    font-size: 12.5px;
    font-weight: 600;
    color: var(--t-secondary);
}
.sf-input, .sf-select-input {
    width: 100%;
    padding: 9px 13px;
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-sm);
    background: var(--c-surface2);
    color: var(--t-primary);
    box-sizing: border-box;
    font-family: var(--f-system);
    font-size: 13.5px;
    outline: none;
    transition: border-color var(--dur-fast), box-shadow var(--dur-fast);
}
.sf-input:focus, .sf-select-input:focus {
    border-color: var(--c-blue);
    box-shadow: 0 0 0 3px rgba(0,122,255,0.12);
}

.sf-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}
@media (max-width: 580px) {
    .sf-grid-2 { grid-template-columns: 1fr; gap: 0; }
}

.sf-checkbox-box {
    background: var(--c-surface2);
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-sm);
    padding: 12px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    max-height: 130px;
    overflow-y: auto;
}
.sf-check-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    color: var(--t-primary);
}
</style>

<div class="sf-root">
    <div class="sf-wrap">
        
        <!-- Header -->
        <div class="sf-header">
            <div>
                <div class="sf-eyebrow"><i class="fa-solid fa-user-shield"></i> System &amp; Personnel Directory</div>
                <h1 class="sf-title">Staff &amp; User Registry</h1>
                <p class="sf-subtitle">Unified register to manage staff records, assigned security roles, and system portal permissions.</p>
            </div>
            <div class="sf-header-actions">
                <a href="<?= APP_URL ?>/user/roles" class="sf-btn neutral">
                    <i class="fa-solid fa-shield-halved"></i> Roles &amp; Permissions
                </a>
                <button class="sf-btn primary" onclick="document.getElementById('staffModal').style.display='flex'">
                    <i class="fa-solid fa-user-plus"></i> Add Staff Member
                </button>
            </div>
        </div>

        <!-- Flash Alerts -->
        <?php if(!empty($data['error'])): ?>
            <div class="sf-alert error">
                <i class="fa-solid fa-circle-exclamation sf-alert-icon"></i>
                <div class="sf-alert-msg"><?= htmlspecialchars($data['error']) ?></div>
            </div>
        <?php endif; ?>
        <?php if(!empty($data['success'])): ?>
            <div class="sf-alert success">
                <i class="fa-solid fa-circle-check sf-alert-icon"></i>
                <div class="sf-alert-msg"><?= htmlspecialchars($data['success']) ?></div>
            </div>
        <?php endif; ?>

        <!-- Stat Widgets Row -->
        <div class="stat-row">
            <div class="stat-card blue">
                <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                <div class="stat-info">
                    <div class="stat-num"><?= number_format($totalStaff) ?></div>
                    <div class="stat-lbl">Total Personnel</div>
                </div>
            </div>

            <div class="stat-card green">
                <div class="stat-icon"><i class="fa-solid fa-user-check"></i></div>
                <div class="stat-info">
                    <div class="stat-num"><?= number_format($activeStaff) ?></div>
                    <div class="stat-lbl">Active Employees</div>
                </div>
            </div>

            <div class="stat-card purple">
                <div class="stat-icon"><i class="fa-solid fa-wallet"></i></div>
                <div class="stat-info">
                    <div class="stat-num">Rs. <?= number_format($totalBaseSalary, 2) ?></div>
                    <div class="stat-lbl">Active Base Payroll</div>
                </div>
            </div>

            <div class="stat-card orange">
                <div class="stat-icon"><i class="fa-solid fa-shield-cat"></i></div>
                <div class="stat-info">
                    <div class="stat-num"><?= number_format($systemLoginsCount) ?></div>
                    <div class="stat-lbl">System User Logins</div>
                </div>
            </div>
        </div>

        <!-- Filter Shelf -->
        <div class="filter-shelf">
            <div class="filter-chip">
                <i class="fa-solid fa-magnifying-glass" style="color: var(--t-tertiary);"></i>
                <input type="text" id="staffSearchInput" class="filter-input" placeholder="Search by name, email, phone, title..." onkeyup="filterStaffTable()">
            </div>

            <div class="filter-chip">
                <i class="fa-solid fa-user-tag" style="color: var(--t-tertiary);"></i>
                <select id="roleFilterSelect" class="filter-select" onchange="filterStaffTable()">
                    <option value="">All Job Titles &amp; Roles</option>
                    <option value="Office">Office</option>
                    <option value="Rep">Rep</option>
                    <option value="Driver">Driver</option>
                    <option value="Admin">Admin</option>
                    <option value="Accountant">Accountant</option>
                </select>
            </div>

            <?php if (!empty($departments)): ?>
                <div class="filter-chip">
                    <i class="fa-solid fa-building" style="color: var(--t-tertiary);"></i>
                    <select id="deptFilterSelect" class="filter-select" onchange="filterStaffTable()">
                        <option value="">All Departments</option>
                        <?php foreach($departments as $dept): ?>
                            <option value="<?= htmlspecialchars($dept) ?>"><?= htmlspecialchars($dept) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <div class="filter-chip">
                <i class="fa-solid fa-circle-nodes" style="color: var(--t-tertiary);"></i>
                <select id="statusFilterSelect" class="filter-select" onchange="filterStaffTable()">
                    <option value="">All Statuses</option>
                    <option value="Active">Active</option>
                    <option value="Terminated">Terminated</option>
                </select>
            </div>

            <button class="filter-reset" onclick="resetStaffFilters()"><i class="fa-solid fa-arrow-rotate-left"></i> Reset</button>

            <div class="filter-count">
                Showing <strong id="visibleStaffCount"><?= count($staffList) ?></strong> of <strong><?= count($staffList) ?></strong> staff records
            </div>
        </div>

        <!-- Table Panel -->
        <div class="table-panel">
            <table class="sf-table" id="staffTable">
                <thead>
                    <tr>
                        <th>Staff Member</th>
                        <th>Job Title &amp; Dept</th>
                        <th>Contact Information</th>
                        <th>System Credentials &amp; Access</th>
                        <th>Status</th>
                        <th style="text-align: right;">Base Salary</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($staffList)): ?>
                        <tr id="noRecordsRow">
                            <td colspan="7" style="text-align: center; color: var(--t-secondary); padding: 50px 20px;">
                                <i class="fa-solid fa-users-slash" style="font-size: 32px; color: var(--t-tertiary); margin-bottom: 10px; display: block;"></i>
                                No staff records found in directory.
                            </td>
                        </tr>
                    <?php else: foreach($staffList as $s): 
                        $fullName = trim(($s->first_name ?? '') . ' ' . ($s->last_name ?? ''));
                        $initials = !empty($fullName) ? strtoupper(substr($s->first_name ?? '', 0, 1) . substr($s->last_name ?? '', 0, 1)) : 'SYS';
                        $status = $s->employee_status ?: $s->user_status ?: 'Active';
                        $email = $s->employee_email ?: $s->user_email ?: '';
                        $phone = $s->phone ?: '';
                        $jobTitle = $s->job_title ?: 'Staff';
                        $dept = $s->department ?: 'General';
                        
                        // Search text payload for fast filtering
                        $searchPayload = strtolower($fullName . ' ' . $email . ' ' . $phone . ' ' . $jobTitle . ' ' . $dept . ' ' . ($s->username ?? ''));
                    ?>
                    <tr class="staff-row" 
                        data-search="<?= htmlspecialchars($searchPayload) ?>"
                        data-role="<?= htmlspecialchars($jobTitle) ?>"
                        data-dept="<?= htmlspecialchars($dept) ?>"
                        data-status="<?= htmlspecialchars($status) ?>">
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div class="avatar-badge"><?= htmlspecialchars($initials) ?></div>
                                <div>
                                    <?php if(!empty($fullName)): ?>
                                        <div style="font-weight: 700; color: var(--t-primary); font-size: 14px;"><?= htmlspecialchars($fullName) ?></div>
                                    <?php else: ?>
                                        <div style="font-weight: 700; color: var(--t-secondary); font-style: italic;">System Account Only</div>
                                    <?php endif; ?>
                                    
                                    <?php if(!empty($s->hire_date)): ?>
                                        <div style="font-size: 11.5px; color: var(--t-label); margin-top: 2px;">
                                            <i class="fa-regular fa-calendar-check"></i> Hired <?= date('d M Y', strtotime($s->hire_date)) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: var(--t-primary);"><?= htmlspecialchars($jobTitle) ?></div>
                            <div style="font-size: 11.5px; color: var(--t-secondary); margin-top: 2px;"><?= htmlspecialchars($dept) ?></div>
                        </td>
                        <td>
                            <div style="font-size: 13px; font-weight: 500;"><?= htmlspecialchars($email ?: '—') ?></div>
                            <div style="font-size: 11.5px; color: var(--t-secondary); margin-top: 2px;"><?= htmlspecialchars($phone ?: '—') ?></div>
                        </td>
                        <td>
                            <?php if(!empty($s->username)): ?>
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    <div style="font-weight: 700; color: var(--c-blue); font-size: 12.5px; display: inline-flex; align-items: center; gap: 5px;">
                                        <i class="fa-solid fa-shield-cat"></i> <?= htmlspecialchars($s->username) ?>
                                    </div>
                                    
                                    <div>
                                        <?php if (!empty($s->roles)): ?>
                                            <?php foreach ($s->roles as $role): 
                                                $roleSlug = strtolower(str_replace(' ', '-', $role->name));
                                                $roleClass = in_array($roleSlug, ['admin', 'office', 'rep', 'driver', 'accountant']) ? 'role-' . $roleSlug : 'role-default';
                                            ?>
                                                <span class="role-badge <?= $roleClass ?>"><?= htmlspecialchars($role->name) ?></span>
                                            <?php endforeach; ?>
                                        <?php else: 
                                            $userRoleSlug = strtolower(str_replace(' ', '-', $s->user_role ?? ''));
                                            $roleClass = in_array($userRoleSlug, ['admin', 'office', 'rep', 'driver', 'accountant']) ? 'role-' . $userRoleSlug : 'role-default';
                                        ?>
                                            <span class="role-badge <?= $roleClass ?>"><?= htmlspecialchars($s->user_role ?? 'User') ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <?php if(!empty($s->accessible_apps)): ?>
                                        <div style="display: flex; flex-wrap: wrap; gap: 3px; margin-top: 2px;">
                                            <?php foreach(explode(',', $s->accessible_apps) as $app): ?>
                                                <span class="app-pill"><?= htmlspecialchars(trim($app)) ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if(!empty($s->signature_path)): ?>
                                        <div style="font-size: 11px; color: var(--c-green); font-weight: 600; display: inline-flex; align-items: center; gap: 4px; margin-top: 2px;">
                                            <i class="fa-solid fa-signature"></i> Signature Uploaded
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span style="font-size: 12px; color: var(--t-tertiary); font-style: italic;">No system login</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="sf-status-pill <?= strtolower($status) ?>">
                                <span class="dot"></span> <?= htmlspecialchars($status) ?>
                            </span>
                        </td>
                        <td style="text-align: right; font-weight: 700; color: var(--t-primary); font-family: var(--f-mono);">
                            <?= $s->base_salary ? ('Rs. ' . number_format($s->base_salary, 2)) : '—' ?>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: 6px; align-items: center;">
                                <button class="sf-btn neutral sm" 
                                        data-employee-id="<?= htmlspecialchars($s->employee_id ?? '') ?>"
                                        data-user-id="<?= htmlspecialchars($s->user_id ?? '') ?>"
                                        data-fname="<?= htmlspecialchars($s->first_name ?? '') ?>"
                                        data-lname="<?= htmlspecialchars($s->last_name ?? '') ?>"
                                        data-email="<?= htmlspecialchars($s->employee_email ?: $s->user_email ?: '') ?>"
                                        data-phone="<?= htmlspecialchars($s->phone ?? '') ?>"
                                        data-dept="<?= htmlspecialchars($s->department ?? '') ?>"
                                        data-title="<?= htmlspecialchars($s->job_title ?? '') ?>"
                                        data-salary="<?= htmlspecialchars($s->base_salary ?? '0') ?>"
                                        data-hdate="<?= htmlspecialchars($s->hire_date ?? '') ?>"
                                        data-status="<?= htmlspecialchars($status) ?>"
                                        data-username="<?= htmlspecialchars($s->username ?? '') ?>"
                                        data-user-role-id="<?= htmlspecialchars(!empty($s->roles) ? $s->roles[0]->id : '') ?>"
                                        data-user-role-ids="<?= htmlspecialchars(!empty($s->roles) ? implode(',', array_column($s->roles, 'id')) : '') ?>"
                                        data-apps="<?= htmlspecialchars($s->accessible_apps ?? '') ?>"
                                        data-sig-path="<?= htmlspecialchars($s->signature_path ?? '') ?>"
                                        onclick="openEditModal(this)">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </button>
                                
                                <?php if (!empty($s->employee_id)): ?>
                                    <a href="<?= APP_URL ?>/user/show/<?= $s->employee_id ?>" class="sf-btn neutral sm">
                                        <i class="fa-solid fa-id-card"></i> Profile
                                    </a>
                                <?php endif; ?>
                                
                                <?php if (!empty($s->user_id)): ?>
                                    <?php if ($s->user_id != ($_SESSION['user_id'] ?? 0)): ?>
                                        <button class="sf-btn danger sm" 
                                                onclick="openDeleteTransferModal(<?= $s->user_id ?>, '<?= htmlspecialchars($s->username, ENT_QUOTES) ?>')">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    <?php endif; ?>
                                <?php elseif (!empty($s->employee_id)): ?>
                                    <button class="sf-btn danger sm" 
                                            onclick="confirmDeleteEmployee(<?= $s->employee_id ?>, '<?= htmlspecialchars($fullName, ENT_QUOTES) ?>')">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- ============================================================
     ADD STAFF MEMBER MODAL
     ============================================================ -->
<div class="sf-modal-overlay" id="staffModal">
    <div class="sf-modal-card">
        <div class="sf-modal-header">
            <h3 class="sf-modal-title">
                <i class="fa-solid fa-user-plus" style="color: var(--c-blue);"></i> Add New Staff Member
            </h3>
            <button class="sf-modal-close" onclick="document.getElementById('staffModal').style.display='none'"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form action="<?= APP_URL ?>/user/create" method="POST" enctype="multipart/form-data">
            
            <div class="sf-form-section">1. Employee Personal &amp; Job Details</div>
            <div class="sf-grid-2">
                <div class="sf-field">
                    <label>First Name *</label>
                    <input type="text" name="first_name" class="sf-input" placeholder="e.g. Ruwinda" required>
                </div>
                <div class="sf-field">
                    <label>Last Name *</label>
                    <input type="text" name="last_name" class="sf-input" placeholder="e.g. Jayaweera" required>
                </div>
            </div>
            
            <div class="sf-grid-2">
                <div class="sf-field">
                    <label>Email Address</label>
                    <input type="email" name="email" class="sf-input" placeholder="ruvi@gmail.com">
                </div>
                <div class="sf-field">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="sf-input" placeholder="0761407877">
                </div>
            </div>
 
            <div class="sf-grid-2">
                <div class="sf-field">
                    <label>Department</label>
                    <input type="text" name="department" class="sf-input" placeholder="e.g. Sales / Operations">
                </div>
                <div class="sf-field">
                    <label>Job Title / Designation *</label>
                    <select name="job_title" class="sf-select-input" required>
                        <option value="Office">Office Staff</option>
                        <option value="Rep">Rep (Sales Representative)</option>
                        <option value="Driver">Driver</option>
                        <option value="Admin">Admin</option>
                        <option value="Accountant">Accountant</option>
                    </select>
                </div>
            </div>
 
            <div class="sf-grid-2">
                <div class="sf-field">
                    <label>Hire Date *</label>
                    <input type="date" name="hire_date" class="sf-input" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="sf-field">
                    <label>Base Salary (LKR) *</label>
                    <input type="number" name="base_salary" step="0.01" min="0" class="sf-input" placeholder="50000.00" required>
                </div>
            </div>

            <!-- Enable System Access Toggle -->
            <div style="margin-top: 15px; padding-top: 15px; border-top: 0.5px dashed var(--c-separator);">
                <label class="sf-check-label" style="font-size: 14px; font-weight: 700; color: var(--c-blue);">
                    <input type="checkbox" name="create_login" id="createLoginCheckbox" value="1" style="width:17px; height:17px;" onchange="toggleLoginFields()">
                    <span>Enable System Login Credentials &amp; Security Roles</span>
                </label>
            </div>

            <div id="loginFieldsContainer" style="display: none; margin-top: 12px;">
                <div class="sf-form-section">2. System User Credentials &amp; Access Controls</div>
                
                <div class="sf-grid-2">
                    <div class="sf-field">
                        <label>Username *</label>
                        <input type="text" name="username" id="loginUsername" class="sf-input" placeholder="e.g. ruwinda@falcon">
                    </div>
                    <div class="sf-field">
                        <label>Password *</label>
                        <input type="password" name="password" id="loginPassword" class="sf-input" placeholder="Enter password">
                    </div>
                </div>
                
                <div class="sf-field">
                    <label>Assigned System Security Roles *</label>
                    <div class="sf-checkbox-box">
                        <?php foreach (($data['roles'] ?? []) as $role): ?>
                            <label class="sf-check-label">
                                <input type="checkbox" name="roles[]" value="<?= $role->id ?>" style="width:16px; height:16px;">
                                <span><?= htmlspecialchars($role->name) ?></span>
                                <span style="font-size: 11px; color: var(--t-label);">— <?= htmlspecialchars($role->description) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="sf-field">
                    <label style="margin-bottom: 8px;">Mobile &amp; Web App Access Permissions *</label>
                    <div style="display: flex; flex-wrap: wrap; gap: 12px; background: var(--c-surface2); border: 0.5px solid var(--c-separator); padding: 12px; border-radius: var(--r-sm);">
                        <label class="sf-check-label">
                            <input type="checkbox" name="accessible_apps[]" value="ERP System" checked> ERP System
                        </label>
                        <label class="sf-check-label">
                            <input type="checkbox" name="accessible_apps[]" value="Driver App"> Driver App
                        </label>
                        <label class="sf-check-label">
                            <input type="checkbox" name="accessible_apps[]" value="Rep App"> Rep App
                        </label>
                        <label class="sf-check-label">
                            <input type="checkbox" name="accessible_apps[]" value="Curtiss Portal"> Curtiss Portal
                        </label>
                    </div>
                </div>

                <div class="sf-field">
                    <label>Digital Signature Image (PNG/JPG)</label>
                    <input type="file" name="signature" class="sf-input" accept=".png, .jpg, .jpeg" style="padding: 6px;">
                </div>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 0.5px solid var(--c-separator);">
                <button type="button" class="sf-btn neutral" onclick="document.getElementById('staffModal').style.display='none'">Cancel</button>
                <button type="submit" class="sf-btn primary"><i class="fa-solid fa-check"></i> Save Staff Member</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================
     EDIT STAFF MEMBER MODAL
     ============================================================ -->
<div class="sf-modal-overlay" id="editStaffModal">
    <div class="sf-modal-card">
        <div class="sf-modal-header">
            <h3 class="sf-modal-title">
                <i class="fa-solid fa-pen-to-square" style="color: var(--c-blue);"></i> Edit Staff Member Details
            </h3>
            <button class="sf-modal-close" onclick="document.getElementById('editStaffModal').style.display='none'"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form action="" method="POST" id="editStaffForm" enctype="multipart/form-data">
            
            <div class="sf-form-section">1. Employee Personal &amp; Job Details</div>
            <div class="sf-grid-2">
                <div class="sf-field">
                    <label>First Name *</label>
                    <input type="text" name="first_name" id="edit_first_name" class="sf-input" required>
                </div>
                <div class="sf-field">
                    <label>Last Name *</label>
                    <input type="text" name="last_name" id="edit_last_name" class="sf-input" required>
                </div>
            </div>
            
            <div class="sf-grid-2">
                <div class="sf-field">
                    <label>Email Address</label>
                    <input type="email" name="email" id="edit_email" class="sf-input">
                </div>
                <div class="sf-field">
                    <label>Phone Number</label>
                    <input type="text" name="phone" id="edit_phone" class="sf-input">
                </div>
            </div>
 
            <div class="sf-grid-2">
                <div class="sf-field">
                    <label>Department</label>
                    <input type="text" name="department" id="edit_department" class="sf-input">
                </div>
                <div class="sf-field">
                    <label>Job Title / Role *</label>
                    <select name="job_title" id="edit_job_title" class="sf-select-input" required>
                        <option value="Office">Office Staff</option>
                        <option value="Rep">Rep (Sales Representative)</option>
                        <option value="Driver">Driver</option>
                        <option value="Admin">Admin</option>
                        <option value="Accountant">Accountant</option>
                    </select>
                </div>
            </div>
 
            <div class="sf-grid-2">
                <div class="sf-field">
                    <label>Hire Date *</label>
                    <input type="date" name="hire_date" id="edit_hire_date" class="sf-input" required>
                </div>
                <div class="sf-field">
                    <label>Base Salary (LKR) *</label>
                    <input type="number" name="base_salary" id="edit_base_salary" step="0.01" min="0" class="sf-input" required>
                </div>
            </div>

            <div class="sf-grid-2">
                <div class="sf-field">
                    <label>Staff Status *</label>
                    <select name="status" id="edit_status" class="sf-select-input" required>
                        <option value="Active">Active</option>
                        <option value="Terminated">Terminated</option>
                    </select>
                </div>
            </div>

            <!-- Login credentials toggle -->
            <div style="margin-top: 15px; padding-top: 15px; border-top: 0.5px dashed var(--c-separator);">
                <label class="sf-check-label" style="font-size: 14px; font-weight: 700; color: var(--c-blue);">
                    <input type="checkbox" name="create_login" id="editCreateLoginCheckbox" value="1" style="width:17px; height:17px;" onchange="toggleEditLoginFields()">
                    <span>Enable System Credentials &amp; Access Controls</span>
                </label>
            </div>

            <div id="editLoginFieldsContainer" style="display: none; margin-top: 12px;">
                <div class="sf-form-section">2. User Credentials &amp; Permissions</div>
                
                <div class="sf-grid-2">
                    <div class="sf-field">
                        <label>Username *</label>
                        <input type="text" name="username" id="editLoginUsername" class="sf-input">
                    </div>
                    <div class="sf-field">
                        <label>Password (Leave blank to keep current)</label>
                        <input type="password" name="password" id="editLoginPassword" class="sf-input" placeholder="New password">
                    </div>
                </div>

                <div class="sf-field">
                    <label>Assigned System Security Roles *</label>
                    <div class="sf-checkbox-box">
                        <?php foreach (($data['roles'] ?? []) as $role): ?>
                            <label class="sf-check-label">
                                <input type="checkbox" name="roles[]" class="edit-role-checkbox" id="edit_role_<?= $role->id ?>" value="<?= $role->id ?>" style="width:16px; height:16px;">
                                <span><?= htmlspecialchars($role->name) ?></span>
                                <span style="font-size: 11px; color: var(--t-label);">— <?= htmlspecialchars($role->description) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="sf-grid-2">
                    <div class="sf-field">
                        <label>Login Status *</label>
                        <select name="user_status" id="editLoginStatus" class="sf-select-input">
                            <option value="Active">Active</option>
                            <option value="Blocked">Blocked</option>
                        </select>
                    </div>
                </div>

                <div class="sf-field">
                    <label style="margin-bottom: 8px;">Mobile/Web App Permissions *</label>
                    <div style="display: flex; flex-wrap: wrap; gap: 12px; background: var(--c-surface2); border: 0.5px solid var(--c-separator); padding: 12px; border-radius: var(--r-sm);">
                        <label class="sf-check-label">
                            <input type="checkbox" name="accessible_apps[]" value="ERP System" id="editAppERP"> ERP System
                        </label>
                        <label class="sf-check-label">
                            <input type="checkbox" name="accessible_apps[]" value="Driver App" id="editAppDriver"> Driver App
                        </label>
                        <label class="sf-check-label">
                            <input type="checkbox" name="accessible_apps[]" value="Rep App" id="editAppRep"> Rep App
                        </label>
                        <label class="sf-check-label">
                            <input type="checkbox" name="accessible_apps[]" value="Curtiss Portal" id="editAppPortal"> Curtiss Portal
                        </label>
                    </div>
                </div>

                <div class="sf-field">
                    <label>Digital Signature Image</label>
                    <div id="editSigContainer" style="display:none; align-items:center; gap:15px; margin-bottom:10px; background:var(--c-surface2); padding:10px; border-radius:var(--r-sm); border:0.5px solid var(--c-separator);">
                        <img id="editSigPreview" src="" style="max-height:40px; background:white; padding:3px; border-radius:4px;" alt="Signature">
                        <label class="sf-check-label" style="font-size:12px;">
                            <input type="checkbox" name="delete_signature" value="1"> Delete signature file
                        </label>
                    </div>
                    <input type="file" name="signature" class="sf-input" accept=".png, .jpg, .jpeg" style="padding: 6px;">
                </div>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 0.5px solid var(--c-separator);">
                <button type="button" class="sf-btn neutral" onclick="document.getElementById('editStaffModal').style.display='none'">Cancel</button>
                <button type="submit" class="sf-btn primary"><i class="fa-solid fa-check"></i> Update Staff Details</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================
     TRANSFER DATA & DELETE USER MODAL
     ============================================================ -->
<div class="sf-modal-overlay" id="transferDeleteModal">
    <div class="sf-modal-card" style="max-width: 520px;">
        <div class="sf-modal-header">
            <h3 class="sf-modal-title" style="color: var(--c-red);">
                <i class="fa-solid fa-triangle-exclamation"></i> Transfer Data &amp; Delete User
            </h3>
            <button class="sf-modal-close" onclick="closeDeleteTransferModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <p style="font-size: 13.5px; line-height: 1.5; margin-bottom: 20px; color: var(--t-secondary);">
            The user <strong id="delete_username_label" style="color: var(--t-primary);"></strong> has active sales, invoice, or route data associated with their account. Before deleting this account, you must select another active user/representative to transfer all their records to.
        </p>

        <form id="transferDeleteForm" method="POST" action="">
            <div class="sf-field">
                <label>Transfer Associated Data To *</label>
                <select name="transfer_to" class="sf-select-input" required id="transfer_to_select">
                    <!-- Populated dynamically via JS -->
                </select>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 0.5px solid var(--c-separator);">
                <button type="button" class="sf-btn neutral" onclick="closeDeleteTransferModal()">Cancel</button>
                <button type="submit" class="sf-btn danger"><i class="fa-solid fa-trash-can"></i> Transfer Data &amp; Delete</button>
            </div>
        </form>
    </div>
</div>

<script>
// JSON array of system users for data transfer options
const allSystemUsers = <?= json_encode(array_map(function($u) {
    return [
        'id' => $u->id,
        'username' => $u->username,
        'role' => $u->role,
        'name' => ($u->first_name ? ($u->first_name . ' ' . $u->last_name) : '')
    ];
}, $data['users'] ?? [])) ?>;

function filterStaffTable() {
    const searchVal = document.getElementById('staffSearchInput').value.toLowerCase().trim();
    const roleVal = document.getElementById('roleFilterSelect').value.toLowerCase().trim();
    const deptElem = document.getElementById('deptFilterSelect');
    const deptVal = deptElem ? deptElem.value.toLowerCase().trim() : '';
    const statusVal = document.getElementById('statusFilterSelect').value.toLowerCase().trim();

    const rows = document.querySelectorAll('.staff-row');
    let visibleCount = 0;

    rows.forEach(row => {
        const searchData = (row.getAttribute('data-search') || '').toLowerCase();
        const roleData = (row.getAttribute('data-role') || '').toLowerCase();
        const deptData = (row.getAttribute('data-dept') || '').toLowerCase();
        const statusData = (row.getAttribute('data-status') || '').toLowerCase();

        const matchesSearch = !searchVal || searchData.includes(searchVal);
        const matchesRole = !roleVal || roleData.includes(roleVal);
        const matchesDept = !deptVal || deptData === deptVal;
        const matchesStatus = !statusVal || statusData === statusVal;

        if (matchesSearch && matchesRole && matchesDept && matchesStatus) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    const countElem = document.getElementById('visibleStaffCount');
    if (countElem) countElem.innerText = visibleCount;

    const noRow = document.getElementById('noRecordsRow');
    if (noRow) {
        noRow.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
    }
}

function resetStaffFilters() {
    document.getElementById('staffSearchInput').value = '';
    document.getElementById('roleFilterSelect').value = '';
    const deptElem = document.getElementById('deptFilterSelect');
    if (deptElem) deptElem.value = '';
    document.getElementById('statusFilterSelect').value = '';
    filterStaffTable();
}

function toggleLoginFields() {
    const checked = document.getElementById('createLoginCheckbox').checked;
    const container = document.getElementById('loginFieldsContainer');
    container.style.display = checked ? 'block' : 'none';
    
    document.getElementById('loginUsername').required = checked;
    document.getElementById('loginPassword').required = checked;
}

function openEditModal(btn) {
    const employeeId = btn.getAttribute('data-employee-id');
    const userId = btn.getAttribute('data-user-id');
    const fname = btn.getAttribute('data-fname');
    const lname = btn.getAttribute('data-lname');
    const email = btn.getAttribute('data-email');
    const phone = btn.getAttribute('data-phone');
    const dept = btn.getAttribute('data-dept');
    const title = btn.getAttribute('data-title');
    const salary = btn.getAttribute('data-salary');
    const hdate = btn.getAttribute('data-hdate');
    const status = btn.getAttribute('data-status');
    
    const username = btn.getAttribute('data-username');
    const userRoleIdsStr = btn.getAttribute('data-user-role-ids') || '';
    const userRoleIds = userRoleIdsStr.split(',').filter(id => id).map(id => parseInt(id));
    const apps = btn.getAttribute('data-apps') || '';
    const sigPath = btn.getAttribute('data-sig-path') || '';

    // Set form action URL
    const submitId = userId ? userId : employeeId;
    document.getElementById('editStaffForm').action = '<?= APP_URL ?>/user/edit/' + submitId;

    document.getElementById('edit_first_name').value = fname;
    document.getElementById('edit_last_name').value = lname;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_phone').value = phone;
    document.getElementById('edit_department').value = dept;
    document.getElementById('edit_job_title').value = title;
    document.getElementById('edit_base_salary').value = salary;
    document.getElementById('edit_hire_date').value = hdate;
    document.getElementById('edit_status').value = status;

    // Reset login fields
    const hasLogin = !!username;
    document.getElementById('editCreateLoginCheckbox').checked = hasLogin;
    document.getElementById('editLoginUsername').value = username || '';
    document.getElementById('editLoginPassword').value = '';
    document.getElementById('editLoginStatus').value = status || 'Active';

    // Clear role checkboxes first
    document.querySelectorAll('.edit-role-checkbox').forEach(cb => {
        cb.checked = false;
    });
    // Check relevant role checkboxes
    userRoleIds.forEach(id => {
        const cb = document.getElementById('edit_role_' + id);
        if (cb) cb.checked = true;
    });

    // Set apps checkboxes
    const appList = apps.split(',').map(s => s.trim());
    document.getElementById('editAppERP').checked = appList.includes('ERP System');
    document.getElementById('editAppDriver').checked = appList.includes('Driver App');
    document.getElementById('editAppRep').checked = appList.includes('Rep App');
    document.getElementById('editAppPortal').checked = appList.includes('Curtiss Portal');

    // Signature Preview
    if (sigPath) {
        document.getElementById('editSigPreview').src = '<?= APP_URL ?>/public/uploads/' + sigPath;
        document.getElementById('editSigContainer').style.display = 'flex';
    } else {
        document.getElementById('editSigContainer').style.display = 'none';
    }

    toggleEditLoginFields();
    document.getElementById('editStaffModal').style.display = 'flex';
}

function toggleEditLoginFields() {
    const checked = document.getElementById('editCreateLoginCheckbox').checked;
    const container = document.getElementById('editLoginFieldsContainer');
    container.style.display = checked ? 'block' : 'none';
    
    document.getElementById('editLoginUsername').required = checked;
}

function confirmDeleteEmployee(id, name) {
    if (confirm("Are you sure you want to delete employee " + name + "? This employee has no active system login or sales records.")) {
        window.location.href = "<?= APP_URL ?>/user/delete_employee/" + id;
    }
}

function openDeleteTransferModal(userId, username) {
    document.getElementById('delete_username_label').innerText = username;
    document.getElementById('transferDeleteForm').action = '<?= APP_URL ?>/user/delete/' + userId;
    
    // Populate select dropdown dynamic options excluding the user being deleted
    const select = document.getElementById('transfer_to_select');
    select.innerHTML = '<option value="">-- Select Active Representative/User --</option>';
    
    allSystemUsers.forEach(u => {
        if (u.id != userId) {
            const opt = document.createElement('option');
            opt.value = u.id;
            opt.innerText = u.username + (u.name ? ' (' + u.name + ')' : '') + ' - ' + (u.role ? u.role.toUpperCase() : 'USER');
            select.appendChild(opt);
        }
    });
    
    document.getElementById('transferDeleteModal').style.display = 'flex';
}

function closeDeleteTransferModal() {
    document.getElementById('transferDeleteModal').style.display = 'none';
}
</script>