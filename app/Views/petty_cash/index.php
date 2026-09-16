<?php
declare(strict_types=1);

if (!function_exists('buildPettyCashUrl')) {
    function buildPettyCashUrl(array $overrides = []): string {
        $params = array_merge($_GET, $overrides);
        return APP_URL . '/pettycash?' . http_build_query($params);
    }
}
?>

<script>
    // Tab switching logic
    window.switchTab = function(evt, tabId) {
        var tabcontents = document.getElementsByClassName("tab-content");
        for (var i = 0; i < tabcontents.length; i++) {
            tabcontents[i].classList.remove("active");
        }

        var tablinks = document.getElementsByClassName("tab-btn");
        for (var i = 0; i < tablinks.length; i++) {
            tablinks[i].classList.remove("active");
        }

        var tabElement = document.getElementById(tabId);
        if (tabElement) {
            tabElement.classList.add("active");
            evt.currentTarget.classList.add("active");
        }
    };

    // Modal control logic
    window.openModal = function(modalId) {
        var modal = document.getElementById(modalId);
        if (!modal) {
            modal = document.querySelector('[id="' + modalId + '"]');
        }
        if (modal) {
            document.querySelectorAll('.modal [required]').forEach(el => el.removeAttribute('required'));
            modal.querySelectorAll('input[data-required="true"], select[data-required="true"], textarea[data-required="true"]').forEach(el => el.setAttribute('required', 'required'));
            modal.style.display = 'flex';
            void modal.offsetWidth; 
            modal.classList.remove('hidden'); 
            modal.classList.add('show');
            
            var firstInput = modal.querySelector('input:not([type="hidden"]), select, textarea');
            if (firstInput) {
                setTimeout(() => firstInput.focus(), 100);
            }
        } else {
            alert("Error: Modal '" + modalId + "' could not be loaded. Please refresh the page (Ctrl+F5) and try again.");
        }
    };

    window.closeModal = function(modalId) {
        var modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden'); 
            modal.classList.remove('show');
            setTimeout(function() {
                modal.style.display = 'none';
            }, 250);
        }
    };

    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal')) {
            window.closeModal(event.target.id);
        }
    });

    window.exportTransactionsExcel = function() {
        var form = document.querySelector('#transactionsTab form.filter-card');
        if (!form) return;
        var formData = new FormData(form);
        var params = new URLSearchParams(formData);
        window.location.href = '<?= APP_URL ?>/pettycash/export_excel?' + params.toString();
    };
</script>

<style>
/* ============================================================
   SF PRO + APPLE DESIGN LANGUAGE — PETTY CASH MANAGEMENT
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
    --c-green:        #34c759;
    --c-green-light:  #e6f9ec;
    --c-orange:       #ff9500;
    --c-orange-light: #fff4e5;
    --c-red:          #ff3b30;
    --c-red-light:    #fff0ef;
    --c-purple:       #af52de;
    --c-teal:         #5ac8fa;
    --c-teal-light:   #eaf8ff;

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
    --dur-mid:     0.28s;
    --dur-slow:    0.42s;
    --ease-ios:    cubic-bezier(0.25, 0.1, 0.25, 1);
    --ease-spring: cubic-bezier(0.34, 1.56, 0.64, 1);
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.inv-root {
    font-family: var(--f-system);
    font-size: 15px;
    color: var(--t-primary);
    background: var(--c-bg);
    -webkit-font-smoothing: antialiased;
}

.inv-wrap {
    max-width: 1420px;
    margin: 0 auto;
    padding: 0px 24px 120px;
}

.inv-eyebrow {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--c-blue);
    margin-bottom: 4px;
}
.inv-title {
    font-size: 30px;
    font-weight: 800;
    letter-spacing: -0.025em;
    color: #0f172a;
    line-height: 1.1;
}

/* ---- Stat Cards ---- */
.stat-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 24px;
}
.stat-card {
    background: var(--c-surface);
    border-radius: var(--r-xl);
    padding: 16px 20px;
    box-shadow: var(--shadow-sm);
    border: 0.5px solid var(--c-separator);
    transition: transform var(--dur-fast) var(--ease-ios), box-shadow var(--dur-fast) var(--ease-ios);
    display: flex;
    align-items: center;
    gap: 16px;
    position: relative;
    overflow: hidden;
}
.stat-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
}
.stat-card.blue::before   { background: var(--c-blue); }
.stat-card.orange::before { background: var(--c-orange); }
.stat-card.green::before  { background: var(--c-green); }
.stat-card.red::before     { background: var(--c-red); }

.stat-icon {
    width: 46px; height: 46px;
    border-radius: var(--r-md);
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0;
}
.stat-card.blue   .stat-icon { background: var(--c-blue-light);   color: var(--c-blue); }
.stat-card.orange .stat-icon { background: var(--c-orange-light); color: var(--c-orange); }
.stat-card.green  .stat-icon { background: var(--c-green-light);  color: var(--c-green); }
.stat-card.red    .stat-icon { background: var(--c-red-light);    color: var(--c-red); }

.stat-info { display: flex; flex-direction: column; justify-content: center; flex: 1; min-width: 0; }
.stat-num {
    font-size: 22px;
    font-weight: 700;
    letter-spacing: -0.03em;
    font-family: var(--f-mono);
    line-height: 1.1;
    margin-top: 2px;
}
.stat-card.blue   .stat-num { color: var(--t-primary); }
.stat-card.orange .stat-num { color: var(--c-orange); }
.stat-card.green  .stat-num { color: var(--c-green); }
.stat-card.red    .stat-num { color: var(--c-red); }
.stat-lbl {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: var(--t-label);
}

/* ---- Buttons ---- */
.sf-btn {
    padding: 9px 16px;
    border-radius: var(--r-md);
    font-size: 13px; font-weight: 600;
    font-family: var(--f-system);
    display: inline-flex; align-items: center; justify-content: center; gap: 7px;
    border: 0.5px solid transparent; cursor: pointer;
    transition: transform var(--dur-fast) var(--ease-spring), filter var(--dur-fast), background var(--dur-fast);
    text-decoration: none;
}
.sf-btn:active { transform: scale(0.97); }
.sf-btn.primary { background: var(--c-blue); color: #fff; }
.sf-btn.primary:hover { background: #0066cc; }
.sf-btn.neutral { background: var(--c-surface); border-color: var(--c-separator); color: var(--t-primary); box-shadow: var(--shadow-xs); }
.sf-btn.neutral:hover { background: var(--c-surface2); }
.sf-btn.danger { background: var(--c-red); color: #fff; }

/* ---- Filter Shelf ---- */
.filter-card {
    background: var(--c-surface2);
    border-bottom: 0.5px solid var(--c-separator);
    padding: 16px 20px;
    display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;
}
.filter-field {
    display: flex; flex-direction: column; gap: 4px;
    flex: 1 1 130px; min-width: 120px;
}
.filter-label {
    font-size: 11px; font-weight: 700;
    letter-spacing: 0.04em; text-transform: uppercase;
    color: var(--t-label);
}
.filter-control {
    width: 100%; height: 36px; padding: 0 10px;
    background: var(--c-surface);
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-sm);
    font-size: 13px; font-weight: 500; font-family: var(--f-system);
    color: var(--t-primary); outline: none;
    transition: border-color var(--dur-fast), box-shadow var(--dur-fast);
}
select.filter-control {
    appearance: none; -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%238e8e93' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 10px center; background-size: 12px 12px;
    padding-right: 28px;
}
.filter-control:focus {
    border-color: var(--c-blue);
    box-shadow: 0 0 0 3px rgba(0,122,255,0.12);
}
.filter-actions { display: flex; gap: 8px; align-items: flex-end; }

/* ---- Table Panel & Table ---- */
.table-panel {
    background: var(--c-surface);
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
.inv-table { width: 100%; border-collapse: collapse; table-layout: auto; }
.inv-table thead th {
    padding: 10px 10px;
    font-size: 11px; font-weight: 700;
    letter-spacing: 0.04em; text-transform: uppercase;
    color: var(--t-label); background: var(--c-surface2);
    border-bottom: 0.5px solid var(--c-separator);
    white-space: nowrap; text-align: left;
}
.inv-table tbody tr {
    transition: background var(--dur-fast);
    border-bottom: 0.5px solid var(--c-separator2);
}
.inv-table tbody tr:last-child { border-bottom: none; }
.inv-table tbody tr:hover { background: var(--c-fill2); }
.inv-table td {
    padding: 10px 10px;
    font-size: 12.5px; color: var(--t-primary);
    vertical-align: middle;
}

/* Badges */
.voucher-code {
    font-family: var(--f-mono); font-size: 11px; font-weight: 700;
    color: #0284c7; background: #f0f9ff;
    padding: 2px 6px; border-radius: 6px; border: 0.5px solid #bae6fd;
    display: inline-block; white-space: nowrap;
}
.sf-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 7px; border-radius: var(--r-pill);
    font-size: 10.5px; font-weight: 700; letter-spacing: 0.01em;
    white-space: nowrap;
}
.sf-badge .dot { width: 4px; height: 4px; border-radius: 50%; flex-shrink: 0; background: currentColor; }
.sf-badge.badge-active { background: var(--c-green-light); color: var(--c-green); }
.sf-badge.badge-low    { background: var(--c-orange-light); color: var(--c-orange); }
.sf-badge.badge-out    { background: var(--c-red-light); color: var(--c-red); }
.sf-badge.badge-blue   { background: var(--c-blue-light); color: var(--c-blue); }
.sf-badge.badge-teal   { background: var(--c-teal-light); color: #0284c7; }
.sf-badge.badge-orange { background: var(--c-orange-light); color: #d97706; }

.txt-green { color: var(--c-green) !important; font-family: var(--f-mono); font-weight: 700; }
.txt-red   { color: var(--c-red) !important; font-family: var(--f-mono); font-weight: 700; }

.act-pill {
    padding: 3px 8px; border-radius: var(--r-sm);
    font-size: 11px; font-weight: 600; text-decoration: none;
    display: inline-flex; align-items: center; gap: 3px;
    border: 0.5px solid var(--c-separator); background: var(--c-surface);
    color: var(--t-primary); transition: all var(--dur-fast);
    white-space: nowrap;
}
.act-pill:hover { background: var(--c-fill); }
.act-pill.print { color: #166534; border-color: #bbf7d0; background: #f0fdf4; }
.act-pill.print:hover { background: #dcfce7; }
.act-pill.pdf { color: #991b1b; border-color: #fecaca; background: #fef2f2; }
.act-pill.pdf:hover { background: #fee2e2; }
.act-pill.approve { color: #065f46; border-color: #a7f3d0; background: #ecfdf5; }
.act-pill.approve:hover { background: #d1fae5; }
.act-pill.reject, .act-pill.danger { color: #991b1b; border-color: #fecaca; background: #fef2f2; }
.act-pill.reject:hover, .act-pill.danger:hover { background: #fee2e2; }

/* ---- Pagination ---- */
.sf-pagination {
    display: flex; justify-content: space-between; align-items: center;
    padding: 14px 20px; background: var(--c-surface2);
    border-top: 0.5px solid var(--c-separator);
}
.pg-info { font-size: 13px; color: var(--t-secondary); font-weight: 500; }
.pg-info strong { color: var(--t-primary); font-weight: 700; }
.pg-right { display: flex; align-items: center; gap: 14px; }
.pg-size-wrap { display: flex; align-items: center; gap: 7px; }
.pg-size-lbl { font-size: 12px; color: var(--t-label); font-weight: 500; }
.pg-size-sel {
    font-family: var(--f-system); font-size: 13px; font-weight: 600;
    color: var(--t-primary); background: var(--c-surface);
    border: 0.5px solid var(--c-separator); border-radius: var(--r-sm);
    padding: 5px 9px; outline: none; cursor: pointer;
}
.pg-nav { display: flex; border: 0.5px solid var(--c-separator); border-radius: var(--r-sm); overflow: hidden; }
.pg-btn {
    width: 34px; height: 32px; display: flex; align-items: center; justify-content: center;
    background: var(--c-surface); border: none; cursor: pointer; color: var(--t-primary); font-size: 12px;
    transition: background var(--dur-fast); text-decoration: none;
}
.pg-btn:hover:not(:disabled) { background: var(--c-fill); }
.pg-btn:disabled { opacity: 0.35; cursor: not-allowed; }
.pg-current {
    padding: 0 14px; display: flex; align-items: center;
    font-size: 13px; font-weight: 600; color: var(--t-primary);
    background: var(--c-surface);
    border-left: 0.5px solid var(--c-separator); border-right: 0.5px solid var(--c-separator);
}

/* ---- Modals & Forms ---- */
.modal-veil {
    position: fixed; inset: 0; z-index: 500;
    background: rgba(0,0,0,0.38);
    backdrop-filter: saturate(180%) blur(12px);
    -webkit-backdrop-filter: saturate(180%) blur(12px);
    display: flex; align-items: center; justify-content: center;
    padding: 20px; opacity: 0; pointer-events: none;
    transition: opacity var(--dur-mid) var(--ease-ios);
}
.modal-veil.show, .modal-veil:not(.hidden) { opacity: 1; pointer-events: auto; }
.sf-modal {
    background: var(--c-surface);
    border-radius: var(--r-xl);
    border: 0.5px solid var(--c-separator);
    box-shadow: var(--shadow-xl);
    width: 100%; max-width: 460px;
    overflow: hidden;
    transform: translateY(16px) scale(0.97);
    transition: transform var(--dur-slow) var(--ease-spring);
}
.modal-veil.show .sf-modal, .modal-veil:not(.hidden) .sf-modal { transform: translateY(0) scale(1); }

.modal-head {
    padding: 18px 22px;
    border-bottom: 0.5px solid var(--c-separator);
    display: flex; align-items: center; justify-content: space-between;
    background: var(--c-surface2);
}
.modal-head h3 { font-size: 16px; font-weight: 700; color: var(--t-primary); display: flex; align-items: center; gap: 8px; }
.modal-close {
    width: 28px; height: 28px; border-radius: 50%;
    background: var(--c-fill); border: none; cursor: pointer;
    color: var(--t-secondary); font-size: 16px; line-height: 1;
    display: flex; align-items: center; justify-content: center;
    transition: background var(--dur-fast);
}
.modal-close:hover { background: var(--c-fill2); color: var(--t-primary); }

.modal-body { padding: 22px; max-height: 70vh; overflow-y: auto; }
.modal-foot {
    padding: 16px 22px; background: var(--c-surface2);
    border-top: 0.5px solid var(--c-separator);
    display: flex; justify-content: flex-end; gap: 10px;
}

.sf-group { margin-bottom: 16px; display: flex; flex-direction: column; gap: 6px; }
.sf-group label {
    font-size: 11px; font-weight: 700;
    letter-spacing: 0.05em; text-transform: uppercase;
    color: var(--t-secondary);
}
.sf-input {
    width: 100%; padding: 10px 14px;
    border-radius: var(--r-sm); border: 0.5px solid var(--c-separator);
    background: var(--c-surface2); color: var(--t-primary);
    font-size: 14px; font-family: var(--f-system); outline: none;
    transition: border-color var(--dur-fast), background var(--dur-fast), box-shadow var(--dur-fast);
    box-sizing: border-box;
}
.sf-input:focus {
    border-color: var(--c-blue); background: var(--c-surface);
    box-shadow: 0 0 0 3px rgba(0,122,255,0.12);
}
select.sf-input {
    appearance: none; -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%238e8e93' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 12px center; background-size: 14px 14px;
    padding-right: 36px;
}
textarea.sf-input { resize: vertical; min-height: 80px; }

.tbl-empty { text-align: center; padding: 48px 20px; }
.tbl-empty-icon { font-size: 32px; color: var(--t-tertiary); margin-bottom: 10px; }
.tbl-empty-title { font-size: 15px; font-weight: 600; color: var(--t-secondary); margin-bottom: 4px; }
.tbl-empty-sub   { font-size: 13px; color: var(--t-tertiary); }

.tab-content { display: none; }
.tab-content.active { display: block; }

.hidden { display: none !important; }
</style>

<div class="inv-root">
    <div class="inv-wrap" style="padding-top: 24px;">
        <!-- Header -->
        <div class="inv-header" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px;">
            <div>
                <div class="inv-eyebrow">Finance / Administration</div>
                <h1 class="inv-title">Petty Cash Dashboard</h1>
            </div>
            <div style="display: flex; gap: 10px;">
                <button class="sf-btn neutral" onclick="window.openModal('settingsModal')" title="Settings"><i class="fa-solid fa-gear"></i></button>
                <button class="sf-btn neutral" onclick="window.openModal('allocateModal')"><i class="fa-solid fa-building-columns"></i> Fund Float</button>
                <button class="sf-btn primary" onclick="window.openModal('expenseModal')"><i class="fa-solid fa-plus"></i> Record Expense</button>
            </div>
        </div>

        <!-- Flash messages -->
        <?php if (!empty($data['success'])): ?>
            <div style="background: var(--c-green-light); color: var(--c-green); border: 0.5px solid rgba(52,199,89,0.3); padding: 12px 16px; border-radius: var(--r-md); margin-bottom: 20px; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-circle-check"></i>
                <span><?= htmlspecialchars((string)$data['success']) ?></span>
            </div>
        <?php endif; ?>
        <?php if (!empty($data['error'])): ?>
            <div style="background: var(--c-red-light); color: var(--c-red); border: 0.5px solid rgba(255,59,48,0.3); padding: 12px 16px; border-radius: var(--r-md); margin-bottom: 20px; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span><?= htmlspecialchars((string)$data['error']) ?></span>
            </div>
        <?php endif; ?>

        <!-- KPI Grid -->
        <div class="stat-row">
            <!-- KPI 1: Ledger Balance -->
            <div class="stat-card blue">
                <div class="stat-icon"><i class="fa-solid fa-wallet"></i></div>
                <div class="stat-info">
                    <div class="stat-lbl" style="display:flex; justify-content:space-between; align-items:center;">Petty Cash Balance <span class="sf-badge badge-active"><span class="dot"></span>1020</span></div>
                    <div class="stat-num">Rs: <?= number_format((float)$data['ledger_balance'], 2) ?></div>
                    <?php $pct = $data['config_limit'] > 0 ? ($data['ledger_balance'] / $data['config_limit']) * 100 : 0; ?>
                    <div style="font-size: 11px; color: var(--t-tertiary); margin-top: 3px;"><?= round($pct) ?>% of limit</div>
                </div>
            </div>

            <!-- KPI 2: Configured Limit -->
            <div class="stat-card orange">
                <div class="stat-icon"><i class="fa-solid fa-sliders"></i></div>
                <div class="stat-info">
                    <div class="stat-lbl">Petty Cash Limit</div>
                    <div class="stat-num">Rs: <?= number_format((float)$data['config_limit'], 2) ?></div>
                    <div style="font-size: 11px; color: var(--t-tertiary); margin-top: 3px;">Custodian: <?= ($data['config'] && isset($data['config']->custodian_name)) ? htmlspecialchars((string)$data['config']->custodian_name) : 'Not Assigned' ?></div>
                </div>
            </div>

            <!-- KPI 3: Available Balance -->
            <div class="stat-card green">
                <div class="stat-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                <div class="stat-info">
                    <div class="stat-lbl">Available to Spend</div>
                    <div class="stat-num" style="color: <?= $data['available_balance'] <= 0 ? 'var(--c-red)' : 'var(--c-green)' ?>;">Rs: <?= number_format((float)$data['available_balance'], 2) ?></div>
                    <div style="font-size: 11px; color: var(--t-tertiary); margin-top: 3px;">Excl. pending approvals</div>
                </div>
            </div>

            <!-- KPI 4: Pending Reimbursements -->
            <div class="stat-card red">
                <div class="stat-icon"><i class="fa-solid fa-receipt"></i></div>
                <div class="stat-info">
                    <?php 
                    $threshold = ($data['config'] && isset($data['config']->reimbursement_threshold)) ? floatval($data['config']->reimbursement_threshold) : 10000.00;
                    $exceeded = $data['pending_reimbursements'] >= $threshold;
                    ?>
                    <div class="stat-lbl" style="display:flex; justify-content:space-between; align-items:center;">Pending Reimb. <?= $exceeded ? '<span class="sf-badge badge-out"><span class="dot"></span>Exceeded</span>' : '' ?></div>
                    <div class="stat-num">Rs: <?= number_format((float)$data['pending_reimbursements'], 2) ?></div>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-top: 3px;">
                        <div style="font-size: 11px; color: var(--t-tertiary);">Threshold: Rs: <?= number_format((float)$threshold, 2) ?></div>
                        <?php if ($data['pending_reimbursements'] > 0): ?>
                            <button class="sf-btn primary" style="padding: 3px 8px; font-size: 11px;" onclick="window.openModal('reimburseModal')">Reimburse</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php $activeTab = $data['active_tab'] ?? 'transactionsTab'; ?>

        <!-- Tabs Container -->
        <div style="background: var(--c-surface); border-radius: var(--r-xl); border: 0.5px solid var(--c-separator); box-shadow: var(--shadow-sm); overflow: hidden;">
            <div style="display: flex; border-bottom: 0.5px solid var(--c-separator); background: var(--c-surface2); padding: 8px 14px; gap: 8px;">
                <button class="sf-btn neutral tab-btn <?= $activeTab === 'transactionsTab' ? 'active' : '' ?>" onclick="window.switchTab(event, 'transactionsTab')" style="border-radius: var(--r-pill); font-size: 13px; padding: 7px 16px;"><i class="fa-solid fa-list"></i> Transactions</button>
                <button class="sf-btn neutral tab-btn <?= $activeTab === 'reimbursementsTab' ? 'active' : '' ?>" onclick="window.switchTab(event, 'reimbursementsTab')" style="border-radius: var(--r-pill); font-size: 13px; padding: 7px 16px;"><i class="fa-solid fa-arrows-rotate"></i> Reimbursements (<?= count($data['reimbursements'] ?? []) ?>)</button>
                <button class="sf-btn neutral tab-btn <?= $activeTab === 'pendingExpensesTab' ? 'active' : '' ?>" onclick="window.switchTab(event, 'pendingExpensesTab')" style="border-radius: var(--r-pill); font-size: 13px; padding: 7px 16px;"><i class="fa-solid fa-clock"></i> Unreimbursed Expenses (<?= count($data['pending_expenses']) ?>)</button>
            </div>

            <!-- Tab 1: Transactions -->
            <div id="transactionsTab" class="tab-content <?= $activeTab === 'transactionsTab' ? 'active' : '' ?>">
                <form method="GET" action="<?= APP_URL ?>/pettycash" class="filter-card">
                    <input type="hidden" name="limit" value="<?= htmlspecialchars((string)($data['limit'] ?? 20)) ?>">
                    <div class="filter-field">
                        <label class="filter-label" for="voucher_number">Voucher No</label>
                        <input type="text" name="voucher_number" id="voucher_number" value="<?= htmlspecialchars((string)$data['filters']['voucher_number']) ?>" placeholder="PCV-xxxxx" class="filter-control">
                    </div>
                    <div class="filter-field">
                        <label class="filter-label" for="paid_to">Paid To / Payee</label>
                        <input type="text" name="paid_to" id="paid_to" value="<?= htmlspecialchars((string)$data['filters']['paid_to']) ?>" placeholder="Payee name..." class="filter-control">
                    </div>
                    <div class="filter-field">
                        <label class="filter-label" for="type">Type</label>
                        <select name="type" id="type" class="filter-control">
                            <option value="">All Types</option>
                            <option value="allocation" <?= $data['filters']['type'] === 'allocation' ? 'selected' : '' ?>>Allocation</option>
                            <option value="expense" <?= $data['filters']['type'] === 'expense' ? 'selected' : '' ?>>Expense</option>
                            <option value="reimbursement" <?= $data['filters']['type'] === 'reimbursement' ? 'selected' : '' ?>>Reimbursement</option>
                        </select>
                    </div>
                    <div class="filter-field">
                        <label class="filter-label" for="status">Status</label>
                        <select name="status" id="status" class="filter-control">
                            <option value="">All Statuses</option>
                            <option value="Pending" <?= $data['filters']['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="Approved" <?= $data['filters']['status'] === 'Approved' ? 'selected' : '' ?>>Approved</option>
                            <option value="Rejected" <?= $data['filters']['status'] === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                    </div>
                    <div class="filter-field">
                        <label class="filter-label" for="rep_route_id">Route</label>
                        <select name="rep_route_id" id="rep_route_id" class="filter-control">
                            <option value="">All Routes</option>
                            <?php foreach ($data['routes'] as $r): ?>
                                <option value="<?= $r->id ?>" <?= $data['filters']['rep_route_id'] == $r->id ? 'selected' : '' ?>><?= htmlspecialchars((string)$r->route_name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-field">
                        <label class="filter-label" for="rep_user_id">Rep</label>
                        <select name="rep_user_id" id="rep_user_id" class="filter-control">
                            <option value="">All Reps</option>
                            <?php foreach ($data['users'] as $u): ?>
                                <option value="<?= $u->id ?>" <?= $data['filters']['rep_user_id'] == $u->id ? 'selected' : '' ?>><?= htmlspecialchars((string)$u->username) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-field" style="max-width: 140px;">
                        <label class="filter-label" for="date_from">From Date</label>
                        <input type="date" name="date_from" id="date_from" value="<?= htmlspecialchars((string)$data['filters']['date_from']) ?>" class="filter-control">
                    </div>
                    <div class="filter-field" style="max-width: 140px;">
                        <label class="filter-label" for="date_to">To Date</label>
                        <input type="date" name="date_to" id="date_to" value="<?= htmlspecialchars((string)$data['filters']['date_to']) ?>" class="filter-control">
                    </div>
                    <div class="filter-field">
                        <label class="filter-label" for="search">Keyword</label>
                        <input type="text" name="search" id="search" value="<?= htmlspecialchars((string)$data['filters']['search']) ?>" placeholder="Desc, Ref..." class="filter-control">
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="sf-btn primary" style="height: 36px; padding: 0 16px;"><i class="fa-solid fa-filter"></i> Filter</button>
                        <a href="<?= APP_URL ?>/pettycash" class="sf-btn neutral" style="height: 36px; padding: 0 14px;"><i class="fa-solid fa-rotate-left"></i> Reset</a>
                        <button type="button" onclick="window.exportTransactionsExcel()" class="sf-btn neutral" style="height: 36px; padding: 0 14px; color: #166534; border-color: #bbf7d0; background: #f0fdf4;" title="Export to Excel">
                            <i class="fa-solid fa-file-excel"></i> Export Excel
                        </button>
                    </div>
                </form>

                <div class="table-panel">
                    <table class="inv-table">
                        <thead>
                            <tr>
                                <th>Voucher No</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Category</th>
                                <th>Paid To / Payee</th>
                                <th>Route / Rep</th>
                                <th style="text-align: right;">Amount</th>
                                <th>Status</th>
                                <th>Created By</th>
                                <th style="text-align: center;">Receipt</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($data['transactions'])): ?>
                                <tr>
                                    <td colspan="11">
                                        <div class="tbl-empty">
                                            <div class="tbl-empty-icon"><i class="fa-solid fa-receipt"></i></div>
                                            <div class="tbl-empty-title">No transactions found</div>
                                            <div class="tbl-empty-sub">Try resetting filters or adjusting search keywords.</div>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: foreach ($data['transactions'] as $tx): ?>
                                <tr>
                                    <td>
                                        <span class="voucher-code">
                                            <?= htmlspecialchars((string)($tx->voucher_number ?: 'PCV-PENDING')) ?>
                                        </span>
                                    </td>
                                    <td><span style="font-family: var(--f-mono); font-size: 13px; font-weight: 500;"><?= date('d-M-Y', strtotime($tx->transaction_date)) ?></span></td>
                                    <td>
                                        <?php if ($tx->type === 'allocation'): ?>
                                            <span class="sf-badge badge-blue"><span class="dot"></span>Allocation</span>
                                        <?php elseif ($tx->type === 'reimbursement'): ?>
                                            <span class="sf-badge badge-teal"><span class="dot"></span>Reimburse</span>
                                        <?php else: ?>
                                            <span class="sf-badge badge-orange"><span class="dot"></span>Expense</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($tx->type === 'allocation'): ?>
                                            <span style="color: var(--t-tertiary); font-size: 12px;">Allocation</span>
                                        <?php elseif ($tx->type === 'reimbursement'): ?>
                                            <span style="color: var(--t-tertiary); font-size: 12px;">Reimbursement</span>
                                        <?php else: ?>
                                            <span style="font-weight: 500; font-size: 13px;"><?= htmlspecialchars((string)($tx->offset_account_name ?: 'Petty Cash Control')) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span style="font-weight: 500;"><?= htmlspecialchars((string)($tx->paid_to ?: 'N/A')) ?></span></td>
                                    <td>
                                        <?php if (!empty($tx->route_name)): ?>
                                            <span style="font-weight: 600; font-size: 12px; color: var(--c-blue);"><?= htmlspecialchars((string)$tx->route_name) ?></span>
                                            <br><span style="font-size: 11px; color: var(--t-tertiary);">Rep: <?= htmlspecialchars((string)($tx->rep_first_name . ' ' . $tx->rep_last_name)) ?></span>
                                        <?php else: ?>
                                            <span style="color: var(--t-tertiary); font-size: 12px;">None</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: right;">
                                        <span class="<?= $tx->type === 'expense' ? 'txt-red' : 'txt-green' ?>">
                                            <?= $tx->type === 'expense' ? '-' : '+' ?>Rs: <?= number_format((float)$tx->amount, 2) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($tx->status === 'Approved'): ?>
                                            <span class="sf-badge badge-active"><span class="dot"></span>Approved</span>
                                        <?php elseif ($tx->status === 'Pending'): ?>
                                            <span class="sf-badge badge-low"><span class="dot"></span>Pending</span>
                                        <?php else: ?>
                                            <span class="sf-badge badge-out"><span class="dot"></span>Rejected</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span style="font-size: 12px; color: var(--t-secondary);"><?= htmlspecialchars((string)($tx->creator_name ?: 'System')) ?></span></td>
                                    <td style="text-align: center;">
                                        <?php if ($tx->attachment_path): ?>
                                            <a href="<?= APP_URL ?>/<?= $tx->attachment_path ?>" target="_blank" class="act-pill pdf" title="View Attachment"><i class="fa-solid fa-file-pdf"></i> View</a>
                                        <?php else: ?>
                                            <span style="color: var(--t-tertiary); font-size: 12px;">None</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: right; white-space: nowrap;">
                                        <div style="display: inline-flex; gap: 4px; justify-content: flex-end;">
                                            <a href="<?= APP_URL ?>/pettycash/print_voucher/<?= $tx->id ?>" target="_blank" class="act-pill print" title="Print Voucher"><i class="fa-solid fa-print"></i> Print</a>
                                            <a href="<?= APP_URL ?>/pettycash/download_pdf/<?= $tx->id ?>" class="act-pill pdf" title="Download PDF"><i class="fa-solid fa-file-pdf"></i> PDF</a>
                                            <?php if ($tx->status === 'Pending' && $tx->type === 'expense'): ?>
                                                <a href="<?= APP_URL ?>/pettycash/approve_expense/<?= $tx->id ?>" class="act-pill approve"><i class="fa-solid fa-check"></i> Approve</a>
                                                <a href="<?= APP_URL ?>/pettycash/reject_expense/<?= $tx->id ?>" class="act-pill reject" onclick="return confirm('Reject this expense?')"><i class="fa-solid fa-xmark"></i> Reject</a>
                                            <?php endif; ?>
                                            <?php 
                                            require_once __DIR__ . '/../../../core/RbacService.php';
                                            if (RbacService::getInstance()->check($_SESSION['user_id'], 'petty_cash', 'delete') && empty($tx->reimbursement_id) && $tx->type !== 'reimbursement'): 
                                            ?>
                                                <a href="<?= APP_URL ?>/pettycash/delete/<?= $tx->id ?>" class="act-pill danger" onclick="return confirm('Are you sure you want to delete this transaction? This will completely undo the transaction and reverse its journal entries.')" title="Delete"><i class="fa-solid fa-trash"></i></a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Block -->
                <div class="sf-pagination">
                    <div class="pg-info">
                        <?php 
                        $totalTx = $data['total_transactions'] ?? 0;
                        $curPage = $data['page'] ?? 1;
                        $perPage = $data['limit'] ?? 20;
                        $start = max(1, ($curPage - 1) * $perPage + 1);
                        $end = min($totalTx, $curPage * $perPage);
                        ?>
                        Showing <strong><?= $totalTx > 0 ? $start : 0 ?></strong> – <strong><?= $end ?></strong> of <strong><?= $totalTx ?></strong> transactions
                    </div>
                    <div class="pg-right">
                        <div class="pg-size-wrap">
                            <span class="pg-size-lbl">Per page</span>
                            <select class="pg-size-sel" onchange="window.location.href='<?= buildPettyCashUrl(['page' => 1]) ?>&limit=' + this.value">
                                <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10</option>
                                <option value="20" <?= $perPage == 20 ? 'selected' : '' ?>>20</option>
                                <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50</option>
                                <option value="100" <?= $perPage == 100 ? 'selected' : '' ?>>100</option>
                            </select>
                        </div>
                        <div class="pg-nav">
                            <?php if ($curPage > 1): ?>
                                <a href="<?= buildPettyCashUrl(['page' => $curPage - 1]) ?>" class="pg-btn" title="Previous Page"><i class="fa-solid fa-chevron-left"></i></a>
                            <?php else: ?>
                                <button type="button" class="pg-btn" disabled><i class="fa-solid fa-chevron-left"></i></button>
                            <?php endif; ?>

                            <div class="pg-current"><?= $curPage ?> / <?= $data['total_pages'] ?? 1 ?></div>

                            <?php if ($curPage < ($data['total_pages'] ?? 1)): ?>
                                <a href="<?= buildPettyCashUrl(['page' => $curPage + 1]) ?>" class="pg-btn" title="Next Page"><i class="fa-solid fa-chevron-right"></i></a>
                            <?php else: ?>
                                <button type="button" class="pg-btn" disabled><i class="fa-solid fa-chevron-right"></i></button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Reimbursements -->
            <div id="reimbursementsTab" class="tab-content <?= $activeTab === 'reimbursementsTab' ? 'active' : '' ?>">

                <div class="table-panel">
                    <table class="inv-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Reimbursement ID</th>
                                <th>Method</th>
                                <th>Remarks / Description</th>
                                <th>Source Account / Bank</th>
                                <th style="text-align: right;">Replenished Amount</th>
                                <th>Status</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($data['reimbursements'])): ?>
                                <tr>
                                    <td colspan="8">
                                        <div class="tbl-empty">
                                            <div class="tbl-empty-icon"><i class="fa-solid fa-arrows-rotate"></i></div>
                                            <div class="tbl-empty-title">No reimbursement requests</div>
                                            <div class="tbl-empty-sub">No reimbursements have been recorded yet.</div>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: foreach ($data['reimbursements'] as $reim): ?>
                                <tr>
                                    <td><span style="font-family: var(--f-mono); font-size: 13px;"><?= date('d-M-Y', strtotime($reim->reimbursement_date)) ?></span></td>
                                    <td><span class="voucher-code">REIM-<?= $reim->id ?></span></td>
                                    <td>
                                        <?php 
                                        $pm = !empty($reim->payment_method) ? $reim->payment_method : 'Bank Transfer';
                                        ?>
                                        <span class="sf-badge badge-blue">
                                            <?= htmlspecialchars((string)$pm) ?>
                                        </span>
                                    </td>
                                    <td><span style="font-size: 13px; color: var(--t-primary);"><?= htmlspecialchars((string)$reim->description) ?></span></td>
                                    <td>
                                        <?php if ($reim->payment_method === 'Cash'): ?>
                                            <span style="color: var(--c-green); font-weight: 600;">Cash in Hand</span>
                                        <?php else: ?>
                                            <span style="font-weight: 500; font-size: 13px;"><?= $reim->bank_account_code ?> - <?= htmlspecialchars((string)$reim->bank_account_name) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: right;"><span class="txt-green">Rs: <?= number_format((float)$reim->amount, 2) ?></span></td>
                                    <td>
                                        <?php if ($reim->status === 'Approved'): ?>
                                            <span class="sf-badge badge-active"><span class="dot"></span>Disbursed</span>
                                        <?php elseif ($reim->status === 'Pending'): ?>
                                            <span class="sf-badge badge-low"><span class="dot"></span>Pending</span>
                                        <?php else: ?>
                                            <span class="sf-badge badge-out"><span class="dot"></span>Rejected</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: right; white-space: nowrap;">
                                        <div style="display: inline-flex; gap: 4px; justify-content: flex-end;">
                                            <a href="<?= APP_URL ?>/pettycash/print_reimbursement/<?= $reim->id ?>" target="_blank" class="act-pill print" title="Print Reimbursement"><i class="fa-solid fa-print"></i> Print</a>
                                            <a href="<?= APP_URL ?>/pettycash/download_reimbursement_pdf/<?= $reim->id ?>" class="act-pill pdf" title="Download PDF"><i class="fa-solid fa-file-pdf"></i> PDF</a>
                                            <?php if ($reim->status === 'Pending'): ?>
                                                <a href="<?= APP_URL ?>/pettycash/approve_reimbursement/<?= $reim->id ?>" class="act-pill approve"><i class="fa-solid fa-hand-holding-dollar"></i> Disburse</a>
                                                <a href="<?= APP_URL ?>/pettycash/reject_reimbursement/<?= $reim->id ?>" class="act-pill reject" onclick="return confirm('Reject reimbursement? Linked expenses will be unlocked.')"><i class="fa-solid fa-xmark"></i> Reject</a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 3: Pending Expenses -->
            <div id="pendingExpensesTab" class="tab-content <?= $activeTab === 'pendingExpensesTab' ? 'active' : '' ?>">
                <?php if (empty($data['pending_expenses'])): ?>
                    <div class="tbl-empty">
                        <div class="tbl-empty-icon"><i class="fa-solid fa-circle-check" style="color: var(--c-green);"></i></div>
                        <div class="tbl-empty-title">All Approved Expenses Reimbursed</div>
                        <div class="tbl-empty-sub">Petty Cash balance matches physical float.</div>
                    </div>
                <?php else: ?>
                    <div style="padding: 16px 20px; background: var(--c-surface2); border-bottom: 0.5px solid var(--c-separator); display: flex; justify-content: space-between; align-items: center;">
                        <p style="margin:0; font-size: 13px; color: var(--t-secondary);">
                            Approved expenses awaiting bank/cash replenishment.
                        </p>
                        <button class="sf-btn primary" onclick="window.openModal('reimburseModal')">
                            <i class="fa-solid fa-arrows-rotate"></i> Generate Reimbursement Request (Rs: <?= number_format((float)$data['pending_reimbursements'], 2) ?>)
                        </button>
                    </div>
                    <div class="table-panel">
                        <table class="inv-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Voucher #</th>
                                    <th>Expense Account</th>
                                    <th>Paid To</th>
                                    <th>Description</th>
                                    <th style="text-align: right;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['pending_expenses'] as $pe): ?>
                                    <tr>
                                        <td><span style="font-family: var(--f-mono); font-size: 13px;"><?= date('d-M-Y', strtotime($pe->transaction_date)) ?></span></td>
                                        <td><span class="voucher-code"><?= htmlspecialchars((string)$pe->voucher_number ?: 'PCV-' . str_pad((string)$pe->id, 5, '0', STR_PAD_LEFT)) ?></span></td>
                                        <td><span style="font-weight: 500; font-size: 13px;"><?= htmlspecialchars((string)$pe->offset_account_code . ' - ' . $pe->offset_account_name) ?></span></td>
                                        <td><span style="font-weight: 500;"><?= htmlspecialchars((string)$pe->paid_to ?: 'N/A') ?></span></td>
                                        <td><span style="font-size: 13px; color: var(--t-primary);"><?= htmlspecialchars((string)$pe->description) ?></span></td>
                                        <td style="text-align: right;"><span class="txt-red">Rs: <?= number_format((float)$pe->amount, 2) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- MODAL 1: SETTINGS -->
<div id="settingsModal" class="modal modal-veil hidden">
    <div class="sf-modal">
        <form method="POST" action="<?= APP_URL ?>/pettycash/settings">
            <input type="hidden" name="csrf_token" value="<?= $data['csrf_token'] ?>">
            <div class="modal-head">
                <h3><i class="fa-solid fa-gear"></i> Petty Cash Configuration</h3>
                <button type="button" class="modal-close" onclick="window.closeModal('settingsModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="sf-group">
                    <label for="limit_amount">Petty Cash Limit (Float Amount) *</label>
                    <input type="number" step="0.01" name="limit_amount" id="limit_amount" value="<?= $data['config_limit'] ?>" class="sf-input" required>
                </div>
                <div class="sf-group">
                    <label for="custodian_id">Petty Cash Custodian *</label>
                    <select name="custodian_id" id="custodian_id" class="sf-input" required>
                        <?php foreach ($data['users'] as $u): ?>
                            <option value="<?= $u->id ?>" <?= ($data['config'] && isset($data['config']->custodian_id) && (int)$data['config']->custodian_id === (int)$u->id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars((string)$u->username) ?> (<?= htmlspecialchars((string)$u->email) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="sf-group">
                    <label for="default_funding_account_id">Default Funding Account *</label>
                    <select name="default_funding_account_id" id="default_funding_account_id" class="sf-input" required>
                        <option value="">-- Select Funding Account --</option>
                        <?php foreach ($data['funding_accounts'] as $fa): ?>
                            <option value="<?= $fa->id ?>" <?= ($data['config'] && isset($data['config']->default_funding_account_id) && (int)$data['config']->default_funding_account_id === (int)$fa->id) ? 'selected' : '' ?>>
                                <?= $fa->account_code ?> - <?= htmlspecialchars((string)$fa->account_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="sf-btn neutral" onclick="window.closeModal('settingsModal')">Cancel</button>
                <button type="submit" class="sf-btn primary">Save Settings</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 2: FUND FLOAT (ALLOCATION) -->
<div id="allocateModal" class="modal modal-veil hidden">
    <div class="sf-modal">
        <form method="POST" action="<?= APP_URL ?>/pettycash/allocate">
            <input type="hidden" name="csrf_token" value="<?= $data['csrf_token'] ?>">
            <div class="modal-head">
                <h3><i class="fa-solid fa-building-columns"></i> Fund Petty Cash Float</h3>
                <button type="button" class="modal-close" onclick="window.closeModal('allocateModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="sf-group">
                    <label for="alloc_amount">Allocation Amount (Rs) *</label>
                    <input type="number" step="0.01" name="amount" id="alloc_amount" placeholder="0.00" class="sf-input" required>
                </div>
                <div class="sf-group">
                    <label for="alloc_payment_method">Payment Method *</label>
                    <select name="payment_method" id="alloc_payment_method" class="sf-input" required onchange="toggleAllocFields(this.value)">
                        <option value="Bank Transfer" selected>Bank Transfer</option>
                        <option value="Cash">Cash in Hand</option>
                        <option value="Cheque">Cheque</option>
                    </select>
                </div>
                <div class="sf-group" id="alloc_bank_group">
                    <label for="alloc_bank_account_id">Source Bank Account *</label>
                    <select name="bank_account_id" id="alloc_bank_account_id" class="sf-input" required>
                        <option value="">-- Select Source Bank Account --</option>
                        <?php foreach (($data['bank_accounts'] ?? $data['funding_accounts']) as $ba): ?>
                            <option value="<?= $ba->id ?>" <?= ($data['config'] && isset($data['config']->default_funding_account_id) && (int)$data['config']->default_funding_account_id === (int)$ba->id) ? 'selected' : '' ?>>
                                <?= $ba->account_code ?> - <?= htmlspecialchars((string)$ba->account_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="sf-group hidden" id="alloc_cash_info" style="background: var(--c-green-light); padding: 12px 14px; border-radius: var(--r-md); border: 0.5px solid rgba(52,199,89,0.3);">
                    <p style="margin: 0; font-size: 13px; color: var(--c-green); font-weight: 500;">
                        <i class="fa-solid fa-money-bill-wave" style="margin-right: 4px;"></i> Funds will be transferred directly from the <strong>Cash in Hand Account (COA 1000)</strong>.
                    </p>
                </div>
                <div id="alloc_cheque_group" class="hidden" style="background: var(--c-surface2); padding: 14px; border-radius: var(--r-md); border: 0.5px solid var(--c-separator); margin-bottom: 16px;">
                    <div class="sf-group">
                        <label for="alloc_cheque_bank_id">Issuing Bank Account *</label>
                        <select name="cheque_bank_id" id="alloc_cheque_bank_id" class="sf-input">
                            <option value="">-- Select Issuing Bank --</option>
                            <?php foreach (($data['bank_accounts'] ?? $data['funding_accounts']) as $ba): ?>
                                <option value="<?= $ba->id ?>"><?= $ba->account_code ?> - <?= htmlspecialchars((string)$ba->account_name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="sf-group">
                        <label for="alloc_issuing_bank">Bank Name (If not in list)</label>
                        <input type="text" name="issuing_bank" id="alloc_issuing_bank" placeholder="e.g. Commercial Bank / BOC / HNB" class="sf-input">
                    </div>
                    <div class="sf-group">
                        <label for="alloc_cheque_number">Cheque Number *</label>
                        <input type="text" name="cheque_number" id="alloc_cheque_number" placeholder="e.g. 331405" class="sf-input">
                    </div>
                    <div class="sf-group">
                        <label for="alloc_cheque_date">Cheque Date / Banking Date *</label>
                        <input type="date" name="cheque_date" id="alloc_cheque_date" value="<?= date('Y-m-d') ?>" class="sf-input">
                    </div>
                    <div class="sf-group">
                        <label for="alloc_payee_name">Payee Name</label>
                        <input type="text" name="payee_name" id="alloc_payee_name" value="Petty Cash Float" placeholder="e.g. Petty Cash Float" class="sf-input">
                    </div>
                    <p style="margin: 6px 0 0 0; font-size: 12px; color: var(--c-blue); font-weight: 500;">
                        <i class="fa-solid fa-circle-info" style="margin-right: 3px;"></i> Cheque will automatically register in <strong>Cheque Management</strong> and credit <strong>Cheque in Hand Account</strong>.
                    </p>
                </div>
                <div class="sf-group">
                    <label for="alloc_date">Transaction Date *</label>
                    <input type="date" name="transaction_date" id="alloc_date" value="<?= date('Y-m-d') ?>" class="sf-input" required>
                </div>
                <div class="sf-group">
                    <label for="alloc_ref">Reference / Voucher No</label>
                    <input type="text" name="reference" id="alloc_ref" placeholder="e.g. Ref #10045" class="sf-input">
                </div>
                <div class="sf-group">
                    <label for="alloc_desc">Description</label>
                    <textarea name="description" id="alloc_desc" rows="3" placeholder="Establish/top-up petty cash float" class="sf-input"></textarea>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="sf-btn neutral" onclick="window.closeModal('allocateModal')">Cancel</button>
                <button type="submit" class="sf-btn primary">Allocate Funds</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 3: RECORD EXPENSE -->
<div id="expenseModal" class="modal modal-veil hidden">
    <div class="sf-modal">
        <form method="POST" action="<?= APP_URL ?>/pettycash/expense" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $data['csrf_token'] ?>">
            <div class="modal-head">
                <h3><i class="fa-solid fa-plus"></i> Record Petty Cash Expense</h3>
                <button type="button" class="modal-close" onclick="window.closeModal('expenseModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="sf-group">
                    <label for="exp_amount">Expense Amount (Rs) *</label>
                    <input type="number" step="0.01" name="amount" id="exp_amount" placeholder="0.00" class="sf-input" required>
                </div>
                <div class="sf-group">
                    <label for="exp_account_id">Expense Category Account *</label>
                    <select name="account_id" id="exp_account_id" class="sf-input" required>
                        <option value="">-- Select Expense Category --</option>
                        <?php foreach ($data['expense_accounts'] as $ea): ?>
                            <option value="<?= $ea->id ?>"><?= $ea->account_code ?> - <?= htmlspecialchars((string)$ea->account_name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="sf-group">
                    <label for="exp_paid_to">Paid To (Payee)</label>
                    <input type="text" name="paid_to" id="exp_paid_to" placeholder="e.g. City Hardware / Staff Member" class="sf-input">
                </div>
                <div class="sf-group">
                    <label for="exp_date">Transaction Date *</label>
                    <input type="date" name="transaction_date" id="exp_date" value="<?= date('Y-m-d') ?>" class="sf-input" required>
                </div>
                <div class="sf-group">
                    <label for="exp_ref">Voucher / Receipt Reference</label>
                    <input type="text" name="reference" id="exp_ref" placeholder="e.g. Bill #1234" class="sf-input">
                </div>
                <div class="sf-group">
                    <label for="exp_desc">Description *</label>
                    <textarea name="description" id="exp_desc" rows="3" placeholder="Details of the expense" class="sf-input" required></textarea>
                </div>
                <div class="sf-group">
                    <label for="exp_attachment">Receipt Attachment (PDF / Image)</label>
                    <input type="file" name="attachment" id="exp_attachment" accept=".pdf,.png,.jpg,.jpeg" class="sf-input">
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="sf-btn neutral" onclick="window.closeModal('expenseModal')">Cancel</button>
                <button type="submit" class="sf-btn primary">Record Expense</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 4: REQUEST REIMBURSEMENT -->
<div id="reimburseModal" class="modal modal-veil hidden">
    <div class="sf-modal">
        <form id="reimbursementForm" method="POST" action="<?= APP_URL ?>/pettycash/reimburse">
            <input type="hidden" name="csrf_token" value="<?= $data['csrf_token'] ?>">
            <div class="modal-head">
                <h3><i class="fa-solid fa-arrows-rotate"></i> Generate Reimbursement Request</h3>
                <button type="button" class="modal-close" onclick="window.closeModal('reimburseModal')">&times;</button>
            </div>
            <div class="modal-body">
                <?php 
                $reimAmount = 0.0;
                foreach ($data['pending_expenses'] as $pe) {
                    $reimAmount += floatval($pe->amount);
                }
                ?>
                <div style="background: var(--c-blue-light); padding: 14px; border-radius: var(--r-md); margin-bottom: 18px; border: 0.5px solid var(--c-separator);">
                    <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--t-label);">Total Reimbursement Value</div>
                    <div style="font-size: 22px; font-weight: 700; color: var(--c-blue); font-family: var(--f-mono); margin-top: 2px;">Rs: <?= number_format((float)$reimAmount, 2) ?></div>
                    <div style="font-size: 12px; color: var(--t-secondary); margin-top: 4px;">Includes <strong><?= count($data['pending_expenses']) ?></strong> approved expense transactions.</div>
                </div>

                <div class="sf-group">
                    <label for="reim_payment_method">Reimbursement Method *</label>
                    <select name="payment_method" id="reim_payment_method" class="sf-input" required onchange="toggleReimbursementFields(this.value)">
                        <option value="Bank Transfer" selected>Bank Transfer</option>
                        <option value="Cash">Cash in Hand</option>
                        <option value="Cheque">Cheque</option>
                    </select>
                </div>

                <div class="sf-group" id="reim_bank_group">
                    <label for="reim_bank_account_id">Source Bank Account *</label>
                    <select name="bank_account_id" id="reim_bank_account_id" class="sf-input" required>
                        <option value="">-- Select Source Bank --</option>
                        <?php foreach ($data['funding_accounts'] as $fa): ?>
                            <option value="<?= $fa->id ?>" <?= ($data['config'] && isset($data['config']->default_funding_account_id) && (int)$data['config']->default_funding_account_id === (int)$fa->id) ? 'selected' : '' ?>>
                                <?= $fa->account_code ?> - <?= htmlspecialchars((string)$fa->account_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="sf-group hidden" id="reim_cash_info" style="background: var(--c-green-light); padding: 12px 14px; border-radius: var(--r-md); border: 0.5px solid rgba(52,199,89,0.3);">
                    <p style="margin: 0; font-size: 13px; color: var(--c-green); font-weight: 500;">
                        <i class="fa-solid fa-money-bill-wave" style="margin-right: 4px;"></i> Funds will be automatically deducted from the <strong>Cash in Hand Account</strong> upon approval.
                    </p>
                </div>

                <div id="reim_cheque_group" class="hidden" style="background: var(--c-surface2); padding: 14px; border-radius: var(--r-md); border: 0.5px solid var(--c-separator); margin-bottom: 16px;">
                    <div class="sf-group">
                        <label for="reim_cheque_number">Cheque Number *</label>
                        <input type="text" name="cheque_number" id="reim_cheque_number" placeholder="e.g. 331305" class="sf-input">
                    </div>
                    <div class="sf-group">
                        <label for="reim_cheque_date">Cheque Date / Banking Date *</label>
                        <input type="date" name="cheque_date" id="reim_cheque_date" value="<?= date('Y-m-d') ?>" class="sf-input">
                    </div>
                    <div class="sf-group">
                        <label for="reim_payee_name">Payee Name (Optional)</label>
                        <input type="text" name="payee_name" id="reim_payee_name" placeholder="e.g. Petty Cash Custodian" class="sf-input">
                    </div>
                </div>

                <div class="sf-group">
                    <label for="reim_date">Reimbursement Date *</label>
                    <input type="date" name="reimbursement_date" id="reim_date" value="<?= date('Y-m-d') ?>" class="sf-input" required>
                </div>
                <div class="sf-group">
                    <label for="reim_desc">Remarks / Description</label>
                    <textarea name="description" id="reim_desc" rows="3" placeholder="Reimbursement of office expenses" class="sf-input"><?= 'Reimbursement request for ' . count($data['pending_expenses']) . ' office expenses' ?></textarea>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="sf-btn neutral" onclick="window.closeModal('reimburseModal')">Cancel</button>
                <button type="submit" class="sf-btn primary">Generate Request</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleAllocFields(method) {
    const bankGroup = document.getElementById('alloc_bank_group');
    const cashInfo = document.getElementById('alloc_cash_info');
    const chequeGroup = document.getElementById('alloc_cheque_group');
    const bankSelect = document.getElementById('alloc_bank_account_id');
    const chequeBankSelect = document.getElementById('alloc_cheque_bank_id');
    const chequeInput = document.getElementById('alloc_cheque_number');

    if (!bankGroup || !cashInfo || !chequeGroup) return;

    if (method === 'Cash') {
        bankGroup.classList.add('hidden');
        cashInfo.classList.remove('hidden');
        chequeGroup.classList.add('hidden');
        if (bankSelect) bankSelect.removeAttribute('required');
        if (chequeBankSelect) chequeBankSelect.removeAttribute('required');
        if (chequeInput) chequeInput.removeAttribute('required');
    } else if (method === 'Cheque') {
        bankGroup.classList.add('hidden');
        cashInfo.classList.add('hidden');
        chequeGroup.classList.remove('hidden');
        if (bankSelect) bankSelect.removeAttribute('required');
        if (chequeBankSelect) chequeBankSelect.setAttribute('required', 'required');
        if (chequeInput) chequeInput.setAttribute('required', 'required');
    } else { // Bank Transfer
        bankGroup.classList.remove('hidden');
        cashInfo.classList.add('hidden');
        chequeGroup.classList.add('hidden');
        if (bankSelect) bankSelect.setAttribute('required', 'required');
        if (chequeBankSelect) chequeBankSelect.removeAttribute('required');
        if (chequeInput) chequeInput.removeAttribute('required');
    }
}

function toggleReimbursementFields(method) {
    const bankGroup = document.getElementById('reim_bank_group');
    const cashInfo = document.getElementById('reim_cash_info');
    const chequeGroup = document.getElementById('reim_cheque_group');
    const bankSelect = document.getElementById('reim_bank_account_id');
    const chequeInput = document.getElementById('reim_cheque_number');

    if (!bankGroup || !cashInfo || !chequeGroup) return;

    if (method === 'Cash') {
        bankGroup.classList.add('hidden');
        cashInfo.classList.remove('hidden');
        chequeGroup.classList.add('hidden');
        if (bankSelect) bankSelect.removeAttribute('required');
        if (chequeInput) chequeInput.removeAttribute('required');
    } else if (method === 'Cheque') {
        bankGroup.classList.remove('hidden');
        cashInfo.classList.add('hidden');
        chequeGroup.classList.remove('hidden');
        if (bankSelect) bankSelect.setAttribute('required', 'required');
        if (chequeInput) chequeInput.setAttribute('required', 'required');
    } else { // Bank Transfer
        bankGroup.classList.remove('hidden');
        cashInfo.classList.add('hidden');
        chequeGroup.classList.add('hidden');
        if (bankSelect) bankSelect.setAttribute('required', 'required');
        if (chequeInput) chequeInput.removeAttribute('required');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const reimForm = document.getElementById('reimbursementForm');
    if (reimForm) {
        reimForm.addEventListener('submit', function(e) {
            console.group('--- Petty Cash Reimbursement Form Submission ---');
            console.log('Timestamp:', new Date().toISOString());
            console.log('Target Action:', reimForm.action);
            console.log('HTTP Method:', reimForm.method);
            const formData = new FormData(reimForm);
            for (let [key, val] of formData.entries()) {
                console.log(`  ${key} =`, val);
            }
            console.groupEnd();
        });
    }

    window.addEventListener('error', function(e) {
        console.error('[Petty Cash Page Error]:', e.message, 'at', e.filename, 'line', e.lineno, e.error);
    });

    window.addEventListener('unhandledrejection', function(e) {
        console.error('[Petty Cash Unhandled Rejection]:', e.reason);
    });
});
</script>