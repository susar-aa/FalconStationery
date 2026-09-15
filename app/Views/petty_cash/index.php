<?php
declare(strict_types=1);
?>

<script>
    // Tab switching logic
    window.switchTab = function(evt, tabId) {
        console.log('switchTab called for ID:', tabId);
        // Hide all tab contents
        var tabcontents = document.getElementsByClassName("tab-content");
        for (var i = 0; i < tabcontents.length; i++) {
            tabcontents[i].classList.remove("active");
        }

        // Deactivate all tab buttons
        var tablinks = document.getElementsByClassName("tab-btn");
        for (var i = 0; i < tablinks.length; i++) {
            tablinks[i].classList.remove("active");
        }

        // Show the current tab and activate the button
        var tabElement = document.getElementById(tabId);
        if (tabElement) {
            tabElement.classList.add("active");
            evt.currentTarget.classList.add("active");
            console.log('Successfully activated tab:', tabId);
        } else {
            console.error('Tab element not found for ID:', tabId);
        }
    };

    // Modal control logic
    window.openModal = function(modalId) {
        console.log('Attempting to open modal:', modalId);
        var modal = document.getElementById(modalId);
        if (!modal) {
            modal = document.querySelector('[id="' + modalId + '"]');
        }
        if (modal) {
            document.querySelectorAll('.modal [required]').forEach(el => el.removeAttribute('required'));
            modal.querySelectorAll('input[data-required="true"], select[data-required="true"], textarea[data-required="true"]').forEach(el => el.setAttribute('required', 'required'));
            modal.style.display = 'flex';
            void modal.offsetWidth; 
            modal.classList.remove('hidden'); modal.classList.add('show');
            
            // Focus first input if any
            var firstInput = modal.querySelector('input:not([type="hidden"]), select, textarea');
            if (firstInput) {
                setTimeout(() => firstInput.focus(), 100);
            }
        } else {
            console.error('Modal element not found for ID:', modalId);
            alert("Error: Modal '" + modalId + "' could not be loaded. Please refresh the page (Ctrl+F5) and try again.");
        }
    };

    window.closeModal = function(modalId) {
        console.log('closeModal called for ID:', modalId);
        var modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden'); modal.classList.remove('show');
            setTimeout(function() {
                modal.style.display = 'none';
                console.log('Hidden modal style.display');
            }, 250);
        } else {
            console.error('Modal element not found for ID:', modalId);
        }
    };

    // Close modal when clicking outside the dialog content
    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal')) {
            window.closeModal(event.target.id);
        }
    });
</script>

<style>
/* ============================================================
   SF PRO + APPLE DESIGN LANGUAGE — INVENTORY CATALOG
   ============================================================ */

:root {
    /* True iOS system palette */
    --c-bg:           #f2f2f7;
    --c-surface:      #ffffff;
    --c-surface2:     #f9f9fb;
    --c-fill:         rgba(120,120,128,0.12);
    --c-fill2:        rgba(120,120,128,0.16);
    --c-separator:    rgba(60,60,67,0.12);
    --c-separator2:   rgba(60,60,67,0.06);

    /* iOS system colors */
    --c-blue:         #007aff;
    --c-blue-light:   #e5f2ff;
    --c-blue-mid:     #b3d6ff;
    --c-green:        #34c759;
    --c-green-light:  #e6f9ec;
    --c-orange:       #ff9500;
    --c-orange-light: #fff4e5;
    --c-red:          #ff3b30;
    --c-red-light:    #fff0ef;
    --c-purple:       #af52de;
    --c-purple-light: #f5eeff;
    --c-teal:         #5ac8fa;
    --c-teal-light:   #eaf8ff;

    /* Typography */
    --f-system: -apple-system, 'SF Pro Display', 'SF Pro Text', 'Inter', 'Helvetica Neue', sans-serif;
    --f-mono:   ui-monospace, 'SF Mono', 'Menlo', 'Monaco', monospace;

    /* Text hierarchy */
    --t-primary:   #1c1c1e;
    --t-secondary: #636366;
    --t-tertiary:  #aeaeb2;
    --t-label:     #8e8e93;

    /* Elevation */
    --shadow-xs:  0 1px 2px rgba(0,0,0,0.04);
    --shadow-sm:  0 2px 8px rgba(0,0,0,0.06), 0 1px 3px rgba(0,0,0,0.04);
    --shadow-md:  0 8px 24px rgba(0,0,0,0.08), 0 2px 6px rgba(0,0,0,0.04);
    --shadow-xl:  0 24px 48px rgba(0,0,0,0.14), 0 4px 12px rgba(0,0,0,0.06);

    /* Geometry */
    --r-xs: 6px;
    --r-sm: 10px;
    --r-md: 14px;
    --r-lg: 20px;
    --r-xl: 26px;
    --r-pill: 999px;

    /* Motion */
    --ease-spring: cubic-bezier(0.34, 1.56, 0.64, 1);
    --ease-ios:    cubic-bezier(0.25, 0.1, 0.25, 1);
    --dur-fast:    0.18s;
    --dur-mid:     0.28s;
    --dur-slow:    0.42s;
}

/* ---- Reset & Base ---- */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.inv-root {
    font-family: var(--f-system);
    font-size: 15px;
    color: var(--t-primary);
    background: var(--c-bg);
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* ---- Layout ---- */
.inv-wrap {
    max-width: 1420px;
    margin: 0 auto;
    padding: 0px 24px 160px;
}
.inv-split {
    display: flex;
    gap: 18px;
    align-items: flex-start;
}
.inv-main { flex: 1; min-width: 0; }

/* ---- Page Header ---- */
.inv-header {
    margin-bottom: 28px;
}
.inv-eyebrow {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--c-blue);
    margin-bottom: 6px;
}
.inv-title {
    font-size: 32px;
    font-weight: 700;
    letter-spacing: -0.03em;
    line-height: 1.1;
    color: var(--t-primary);
    margin-bottom: 24px;
}

/* ---- Stat Cards (SF Widgets style) ---- */
.stat-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
    margin-bottom: 28px;
}
.stat-card {
    background: var(--c-surface);
    border-radius: var(--r-xl);
    padding: 16px 20px;
    box-shadow: var(--shadow-sm);
    border: 0.5px solid var(--c-separator);
    transition: transform var(--dur-fast) var(--ease-ios),
                box-shadow var(--dur-fast) var(--ease-ios);
    cursor: default;
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
    height: 2px;
    border-radius: var(--r-xl) var(--r-xl) 0 0;
}
.stat-card.blue::before  { background: var(--c-blue); }
.stat-card.orange::before { background: var(--c-orange); }
.stat-card.red::before   { background: var(--c-red); }
.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}
.stat-icon {
    width: 48px; height: 48px;
    border-radius: var(--r-sm);
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}
.stat-card.blue  .stat-icon { background: var(--c-blue-light);   color: var(--c-blue); }
.stat-card.orange .stat-icon { background: var(--c-orange-light); color: var(--c-orange); }
.stat-card.red   .stat-icon { background: var(--c-red-light);    color: var(--c-red); }
.stat-info {
    display: flex; flex-direction: column; justify-content: center;
}
.stat-num {
    font-size: 24px;
    font-weight: 700;
    letter-spacing: -0.04em;
    font-family: var(--f-mono);
    line-height: 1;
    margin-bottom: 2px;
}
.stat-card.blue   .stat-num { color: var(--t-primary); }
.stat-card.orange .stat-num { color: var(--c-orange); }
.stat-card.red    .stat-num { color: var(--c-red); }
.stat-lbl {
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: var(--t-label);
}

/* ---- Alerts ---- */
.sf-alert {
    display: flex; align-items: flex-start; gap: 12px;
    background: var(--c-surface);
    border-radius: var(--r-md);
    padding: 14px 16px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-xs);
    border: 0.5px solid var(--c-separator);
    border-left-width: 3px;
    font-size: 14px;
}
.sf-alert.success { border-left-color: var(--c-green); }
.sf-alert.error   { border-left-color: var(--c-red); }
.sf-alert-icon { font-size: 18px; flex-shrink: 0; padding-top: 1px; }
.sf-alert.success .sf-alert-icon { color: var(--c-green); }
.sf-alert.error   .sf-alert-icon { color: var(--c-red); }
.sf-alert-title { font-weight: 600; color: var(--t-primary); margin-bottom: 2px; }
.sf-alert-msg   { color: var(--t-secondary); font-size: 13px; }
.sf-alert-close {
    margin-left: auto; flex-shrink: 0; background: none; border: none;
    color: var(--t-tertiary); cursor: pointer; font-size: 15px; padding: 2px;
    transition: color var(--dur-fast);
}
.sf-alert-close:hover { color: var(--t-secondary); }

/* ---- Filter Shelf ---- */
.filter-shelf {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
    margin-bottom: 18px;
}
.filter-chip {
    display: inline-flex; align-items: center; gap: 6px;
    background: var(--c-surface);
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-pill);
    padding: 7px 14px;
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
.filter-chip-label {
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.02em;
    color: var(--t-label);
    text-transform: uppercase;
}
.filter-chip input[type="number"] {
    border: none; outline: none; background: transparent;
    font-size: 14px; font-weight: 600; font-family: var(--f-system);
    color: var(--t-primary); width: 72px; padding: 0;
}
.filter-chip input[type="number"]::-webkit-inner-spin-button,
.filter-chip input[type="number"]::-webkit-outer-spin-button { -webkit-appearance: none; }
.filter-reset {
    background: transparent;
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-pill);
    padding: 7px 14px;
    font-size: 13px;
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

/* ---- Custom Dropdown ---- */
.sf-dropdown { position: relative; outline: none; cursor: pointer; }
.sf-dropdown-val {
    display: flex; align-items: center; gap: 5px;
    font-size: 14px; font-weight: 600; color: var(--t-primary);
}
.sf-dropdown-val::after {
    content: '';
    display: inline-block; width: 12px; height: 12px;
    background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%238e8e93' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") center/contain no-repeat;
}
.sf-dropdown-menu {
    position: absolute; top: calc(100% + 10px); left: 0; z-index: 200;
    background: var(--c-surface);
    border-radius: var(--r-md);
    border: 0.5px solid var(--c-separator);
    box-shadow: var(--shadow-xl);
    min-width: 196px;
    max-height: 280px; overflow-y: auto;
    opacity: 0; visibility: hidden;
    transform: translateY(-6px) scale(0.98);
    transform-origin: top left;
    transition: opacity var(--dur-mid) var(--ease-ios),
                transform var(--dur-mid) var(--ease-ios),
                visibility var(--dur-mid);
    padding: 6px;
}
.sf-dropdown:focus-within .sf-dropdown-menu {
    opacity: 1; visibility: visible; transform: translateY(0) scale(1);
}
.sf-dropdown-item {
    padding: 9px 12px;
    font-size: 14px;
    font-weight: 500;
    color: var(--t-primary);
    border-radius: var(--r-sm);
    transition: background var(--dur-fast);
    cursor: pointer;
}
.sf-dropdown-item:hover { background: var(--c-fill); }
.sf-dropdown-item.active { background: var(--c-blue-light); color: var(--c-blue); font-weight: 600; }

/* ---- Table Panel ---- */
.table-panel {
    background: var(--c-surface);
    border-radius: var(--r-xl);
    border: 0.5px solid var(--c-separator);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    position: relative;
}
#table-loader {
    position: absolute; inset: 0; z-index: 10;
    background: rgba(255,255,255,0.75);
    backdrop-filter: blur(4px);
    display: flex; align-items: center; justify-content: center;
    opacity: 0; pointer-events: none;
    transition: opacity var(--dur-mid);
}
#table-loader.active { opacity: 1; pointer-events: auto; }

.inv-table { width: 100%; border-collapse: collapse; }
.inv-table thead th {
    padding: 13px 18px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--t-label);
    background: var(--c-surface2);
    border-bottom: 0.5px solid var(--c-separator);
    white-space: nowrap;
}
.inv-table thead th:first-child { border-radius: var(--r-xl) 0 0 0; }
.inv-table thead th:last-child  { border-radius: 0 var(--r-xl) 0 0; }
.inv-table tbody tr {
    transition: background var(--dur-fast);
    border-bottom: 0.5px solid var(--c-separator2);
}
.inv-table tbody tr:last-child { border-bottom: none; }
.inv-table tbody tr:hover { background: var(--c-fill2); }
.inv-table td {
    padding: 14px 18px;
    font-size: 14px;
    color: var(--t-primary);
    vertical-align: middle;
}

/* ---- iOS Checkbox ---- */
.sf-check {
    appearance: none;
    width: 21px; height: 21px;
    border-radius: 50%;
    border: 1.5px solid var(--c-separator);
    background: var(--c-fill);
    cursor: pointer;
    position: relative;
    flex-shrink: 0;
    transition: all var(--dur-fast) var(--ease-spring);
    display: block;
}
.sf-check:checked {
    background: var(--c-blue);
    border-color: var(--c-blue);
    box-shadow: 0 2px 8px rgba(0,122,255,0.35);
}
.sf-check:checked::after {
    content: '';
    position: absolute;
    left: 6px; top: 4px;
    width: 5px; height: 9px;
    border: 2px solid #fff; border-top: none; border-left: none;
    transform: rotate(45deg);
}

/* ---- Product Cell ---- */
.prod-thumb {
    width: 42px; height: 42px;
    border-radius: var(--r-sm);
    object-fit: cover;
    background: var(--c-fill);
    border: 0.5px solid var(--c-separator);
    flex-shrink: 0;
}
.prod-name {
    font-size: 14px; font-weight: 600; color: var(--t-primary);
    margin-bottom: 2px; line-height: 1.3;
}
.prod-meta {
    font-size: 11px; font-weight: 500;
    color: var(--t-tertiary);
    font-family: var(--f-mono);
    letter-spacing: 0.02em;
}
.d-flex-row { display: flex; align-items: center; gap: 10px; }

/* ---- Numbers ---- */
.num { font-family: var(--f-mono); font-size: 13px; font-weight: 600; }
.num-muted { color: var(--t-label); }

/* ---- Status Badges ---- */
.sf-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px;
    border-radius: var(--r-pill);
    font-size: 12px; font-weight: 700;
    letter-spacing: 0.01em;
    white-space: nowrap;
}
.sf-badge .dot {
    width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0;
}
.badge-active  { background: var(--c-green-light);  color: #1a7f3c; }
.badge-active  .dot { background: var(--c-green); }
.badge-low     { background: var(--c-orange-light); color: #c05d00; }
.badge-low     .dot { background: var(--c-orange); }
.badge-out     { background: var(--c-red-light);    color: #c0291f; }
.badge-out     .dot { background: var(--c-red); }

/* ---- Row Actions ---- */
.row-acts { display: flex; gap: 6px; justify-content: flex-end; }
.act-btn {
    width: 32px; height: 32px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    background: var(--c-fill);
    color: var(--t-label);
    border: none; cursor: pointer; text-decoration: none;
    font-size: 13px;
    transition: all var(--dur-fast) var(--ease-spring);
}
.act-btn:hover { transform: scale(1.12); }
.act-btn.view:hover   { background: var(--c-blue-light);   color: var(--c-blue); }
.act-btn.edit:hover   { background: var(--c-purple-light); color: var(--c-purple); }
.act-btn.trash:hover  { background: var(--c-red-light);    color: var(--c-red); }

/* ---- Pagination ---- */
.sf-pagination {
    display: flex; justify-content: space-between; align-items: center;
    padding: 14px 18px;
    background: var(--c-surface2);
    border-top: 0.5px solid var(--c-separator);
}
.pg-info { font-size: 13px; color: var(--t-secondary); font-weight: 500; }
.pg-info strong { color: var(--t-primary); font-weight: 700; }
.pg-right { display: flex; align-items: center; gap: 14px; }
.pg-size-wrap { display: flex; align-items: center; gap: 7px; }
.pg-size-lbl { font-size: 12px; color: var(--t-label); font-weight: 500; }
.pg-size-sel {
    font-family: var(--f-system); font-size: 13px; font-weight: 600;
    color: var(--t-primary);
    background: var(--c-fill);
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-sm);
    padding: 5px 9px;
    outline: none; cursor: pointer;
    transition: border-color var(--dur-fast);
}
.pg-size-sel:hover { border-color: var(--c-blue); }
.pg-nav { display: flex; border: 0.5px solid var(--c-separator); border-radius: var(--r-sm); overflow: hidden; }
.pg-btn {
    width: 34px; height: 32px;
    display: flex; align-items: center; justify-content: center;
    background: var(--c-surface);
    border: none; cursor: pointer; color: var(--t-primary); font-size: 12px;
    transition: background var(--dur-fast);
}
.pg-btn:hover:not(:disabled) { background: var(--c-fill); }
.pg-btn:disabled { opacity: 0.35; cursor: not-allowed; }
.pg-btn + .pg-btn { border-left: 0.5px solid var(--c-separator); }
.pg-current {
    padding: 0 14px;
    display: flex; align-items: center;
    font-size: 13px; font-weight: 600; color: var(--t-primary);
    background: var(--c-surface);
    border-left: 0.5px solid var(--c-separator);
    border-right: 0.5px solid var(--c-separator);
}

/* ---- Command Bar (Dynamic Island style) ---- */
.cmd-bar {
    position: fixed;
    bottom: 28px; left: 50%;
    transform: translateX(-50%);
    background: rgba(28, 28, 30, 0.92);
    backdrop-filter: saturate(180%) blur(28px);
    -webkit-backdrop-filter: saturate(180%) blur(28px);
    border: 0.5px solid rgba(255,255,255,0.12);
    border-radius: var(--r-pill);
    padding: 7px 10px;
    display: flex; align-items: center; gap: 4px;
    box-shadow: var(--shadow-xl), 0 0 0 0.5px rgba(0,0,0,0.3);
    z-index: 100;
}
.cmd-search {
    display: flex; align-items: center; gap: 9px;
    background: rgba(255,255,255,0.1);
    border-radius: var(--r-pill);
    padding: 8px 14px;
    width: 196px;
    transition: width var(--dur-slow) var(--ease-ios),
                background var(--dur-mid);
}
.cmd-search:focus-within {
    width: 300px;
    background: rgba(255,255,255,0.18);
}
.cmd-search i { color: rgba(255,255,255,0.55); font-size: 14px; flex-shrink: 0; }
.cmd-search input {
    background: transparent; border: none; outline: none;
    color: #fff; font-size: 14px; font-weight: 500;
    font-family: var(--f-system); width: 100%;
}
.cmd-search input::placeholder { color: rgba(255,255,255,0.45); }
.cmd-divider { width: 0.5px; height: 22px; background: rgba(255,255,255,0.15); margin: 0 3px; }
.cmd-icon {
    width: 38px; height: 38px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: rgba(255,255,255,0.8); font-size: 15px;
    background: transparent; border: none; cursor: pointer; text-decoration: none;
    transition: background var(--dur-fast);
}
.cmd-icon:hover { background: rgba(255,255,255,0.12); color: #fff; }
.cmd-cta {
    display: flex; align-items: center; gap: 7px;
    background: #fff; color: #1c1c1e;
    border: none; border-radius: var(--r-pill);
    padding: 0 18px; height: 38px;
    font-size: 14px; font-weight: 700;
    font-family: var(--f-system);
    cursor: pointer; text-decoration: none;
    transition: transform var(--dur-fast) var(--ease-spring),
                background var(--dur-fast);
    margin-left: 2px;
}
.cmd-cta:hover { background: #e5e5ea; transform: scale(0.97); }

/* ---- Bulk Toolbar ---- */
.bulk-bar {
    position: fixed;
    bottom: 100px; left: 50%;
    transform: translateX(-50%) translateY(12px);
    background: var(--c-blue);
    border-radius: var(--r-pill);
    padding: 10px 22px;
    display: flex; align-items: center; gap: 16px;
    box-shadow: var(--shadow-xl), 0 4px 16px rgba(0,122,255,0.35);
    z-index: 90;
    opacity: 0; pointer-events: none;
    transition: opacity var(--dur-mid) var(--ease-ios),
                transform var(--dur-mid) var(--ease-ios);
}
.bulk-bar:not(.hidden) {
    opacity: 1; pointer-events: auto;
    transform: translateX(-50%) translateY(0);
}
.bulk-count {
    background: rgba(255,255,255,0.22);
    color: #fff; font-size: 13px; font-weight: 700;
    padding: 3px 10px; border-radius: var(--r-pill);
}
.bulk-lbl { font-size: 14px; font-weight: 600; color: rgba(255,255,255,0.85); }
.bulk-divider { width: 0.5px; height: 20px; background: rgba(255,255,255,0.25); }
.bulk-action {
    background: rgba(255,255,255,0.2);
    color: #fff; border: none; border-radius: var(--r-pill);
    padding: 7px 16px; font-size: 13px; font-weight: 700;
    font-family: var(--f-system); cursor: pointer;
    display: flex; align-items: center; gap: 6px;
    transition: background var(--dur-fast);
}
.bulk-action:hover { background: rgba(255,255,255,0.3); }
.bulk-cancel {
    background: transparent; border: none; color: rgba(255,255,255,0.65);
    font-size: 13px; font-weight: 600; font-family: var(--f-system);
    cursor: pointer; padding: 6px;
    transition: color var(--dur-fast);
}
.bulk-cancel:hover { color: #fff; }

/* ---- Modals ---- */
.modal-veil {
    position: fixed; inset: 0; z-index: 500;
    background: rgba(0,0,0,0.4);
    backdrop-filter: saturate(180%) blur(14px);
    -webkit-backdrop-filter: saturate(180%) blur(14px);
    display: flex; align-items: center; justify-content: center;
    padding: 20px;
    opacity: 0; pointer-events: none;
    transition: opacity var(--dur-mid) var(--ease-ios);
}
.modal-veil:not(.hidden) { opacity: 1; pointer-events: auto; }
.sf-modal {
    background: var(--c-surface);
    border-radius: var(--r-xl);
    border: 0.5px solid var(--c-separator);
    box-shadow: var(--shadow-xl);
    width: 100%; max-width: 420px;
    overflow: hidden;
    transform: translateY(16px) scale(0.97);
    transition: transform var(--dur-slow) var(--ease-spring);
}
.modal-veil:not(.hidden) .sf-modal { transform: translateY(0) scale(1); }
.modal-head {
    padding: 20px 22px 18px;
    text-align: center;
    border-bottom: 0.5px solid var(--c-separator);
    position: relative;
}
.modal-title { font-size: 17px; font-weight: 700; color: var(--t-primary); }
.modal-close {
    position: absolute; right: 14px; top: 14px;
    width: 28px; height: 28px; border-radius: 50%;
    background: var(--c-fill); border: none; cursor: pointer;
    color: var(--t-label); font-size: 13px;
    display: flex; align-items: center; justify-content: center;
    transition: background var(--dur-fast);
}
.modal-close:hover { background: var(--c-fill2); }
.modal-body { padding: 22px; max-height: 60vh; overflow-y: auto; }
.modal-foot {
    padding: 14px 22px;
    background: var(--c-surface2);
    border-top: 0.5px solid var(--c-separator);
    display: flex; gap: 10px;
}
.sf-btn {
    flex: 1; padding: 12px;
    border-radius: var(--r-md);
    font-size: 15px; font-weight: 700;
    font-family: var(--f-system); text-align: center;
    cursor: pointer; border: none;
    transition: transform var(--dur-fast) var(--ease-spring), filter var(--dur-fast);
    text-decoration: none; display: flex; align-items: center; justify-content: center;
}
.sf-btn:hover { filter: brightness(0.94); }
.sf-btn:active { transform: scale(0.97); }
.sf-btn.neutral { background: var(--c-fill); color: var(--t-primary); }
.sf-btn.primary { background: var(--t-primary); color: #fff; }
.sf-btn.danger  { background: var(--c-red);    color: #fff; }

/* ---- Inputs ---- */
.sf-input {
    width: 100%; padding: 12px 14px;
    background: var(--c-fill);
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-md);
    font-size: 15px; font-weight: 500; font-family: var(--f-system);
    color: var(--t-primary); outline: none;
    transition: border-color var(--dur-fast), box-shadow var(--dur-fast), background var(--dur-fast);
    box-sizing: border-box;
}
.sf-input:focus {
    background: var(--c-surface);
    border-color: var(--c-blue);
    box-shadow: 0 0 0 3.5px rgba(0,122,255,0.14);
}
.sf-input:disabled { opacity: 0.45; cursor: not-allowed; }
.sf-input-group { display: flex; flex-direction: column; gap: 14px; }

/* ---- Bulk section ---- */
.bulk-field {
    background: var(--c-fill);
    border-radius: var(--r-md);
    padding: 14px 16px;
}
.bulk-field-head {
    display: flex; align-items: center; gap: 10px;
    cursor: pointer; margin-bottom: 10px;
}
.bulk-field-label { font-size: 14px; font-weight: 600; color: var(--t-primary); }
.bulk-row { display: flex; gap: 8px; }

/* ---- Modal error ---- */
.modal-err {
    padding: 11px 14px;
    background: var(--c-red-light);
    border-radius: var(--r-md);
    font-size: 13px; font-weight: 600;
    color: #c0291f; text-align: center;
    margin-bottom: 18px;
}

/* ---- CSV drop zone ---- */
.drop-zone {
    border: 1.5px dashed var(--c-separator);
    border-radius: var(--r-lg);
    padding: 32px 20px;
    text-align: center;
    background: var(--c-fill);
    cursor: pointer; position: relative;
    margin-bottom: 18px;
    transition: border-color var(--dur-fast), background var(--dur-fast);
}
.drop-zone:hover { border-color: var(--c-blue); background: var(--c-blue-light); }
.drop-zone input[type="file"] {
    position: absolute; inset: 0; opacity: 0; cursor: pointer;
}
.drop-zone-icon { font-size: 30px; color: var(--c-blue); margin-bottom: 10px; }
.drop-zone-title { font-size: 15px; font-weight: 600; color: var(--t-primary); }
.drop-zone-sub   { font-size: 13px; color: var(--t-secondary); margin-top: 4px; }

/* ---- Import hint ---- */
.import-hint {
    background: var(--c-surface2);
    border-radius: var(--r-md);
    padding: 14px 16px;
    border: 0.5px solid var(--c-separator);
    display: flex; align-items: flex-start; gap: 10px;
}
.import-hint-title { font-size: 13px; font-weight: 700; color: var(--t-primary); margin-bottom: 4px; display: flex; gap: 6px; align-items: center; }
.import-hint-body  { font-size: 12px; color: var(--t-secondary); line-height: 1.5; }

/* ---- Quick View Panel ---- */
.qv-panel {
    width: 0; opacity: 0; overflow: hidden;
    background: var(--c-surface);
    border-radius: var(--r-xl);
    border: 0.5px solid var(--c-separator);
    box-shadow: var(--shadow-md);
    flex-shrink: 0;
    position: sticky; top: 20px;
    height: fit-content;
    max-height: calc(100vh - 40px);
    overflow-y: auto;
    transition: width var(--dur-slow) var(--ease-ios),
                opacity var(--dur-slow) var(--ease-ios);
}
.qv-panel.open { width: 320px; opacity: 1; overflow-y: auto; }
.qv-inner { padding: 22px; min-width: 320px; }
.qv-close {
    position: absolute; top: 14px; right: 14px;
    width: 28px; height: 28px; border-radius: 50%;
    background: var(--c-fill); border: none; cursor: pointer;
    color: var(--t-label); font-size: 13px;
    display: flex; align-items: center; justify-content: center;
    transition: background var(--dur-fast);
}
.qv-close:hover { background: var(--c-fill2); }
.qv-carousel-container {
    position: relative;
    width: 100%;
    aspect-ratio: 1 / 1;
    border-radius: var(--r-lg);
    overflow: hidden;
    margin-bottom: 14px;
    border: 0.5px solid var(--c-separator);
    background: var(--c-surface2);
}
.qv-carousel-track {
    display: flex;
    width: 100%;
    height: 100%;
    transition: transform var(--dur-mid) var(--ease-ios);
}
.qv-carousel-slide {
    width: 100%;
    height: 100%;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}
.qv-carousel-slide img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.qv-carousel-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: rgba(0,0,0,0.2);
    border: none;
    padding: 0;
    transition: background var(--dur-fast), transform var(--dur-fast);
    cursor: pointer;
}
.qv-carousel-dot.active {
    background: var(--c-blue);
    transform: scale(1.2);
}
.qv-name { font-size: 17px; font-weight: 700; color: var(--t-primary); text-align: center; margin-bottom: 3px; }
.qv-sku  { font-size: 11px; font-family: var(--f-mono); color: var(--t-label); text-align: center; margin-bottom: 8px; }
.qv-tags { display: flex; justify-content: center; gap: 8px; margin-bottom: 18px; flex-wrap: wrap; }
.qv-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px; }
.qv-tile {
    background: var(--c-fill);
    border-radius: var(--r-md);
    padding: 14px;
    text-align: center;
}
.qv-tile-lbl { font-size: 10px; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; color: var(--t-label); margin-bottom: 6px; }
.qv-tile-val { font-size: 22px; font-weight: 700; font-family: var(--f-mono); color: var(--t-primary); }
.qv-prices {
    background: var(--c-fill);
    border-radius: var(--r-md);
    padding: 14px 16px;
    margin-bottom: 12px;
}
.qv-price-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 7px 0;
    border-bottom: 0.5px solid var(--c-separator2);
}
.qv-price-row:last-child { border-bottom: none; padding-bottom: 0; }
.qv-price-lbl { font-size: 13px; color: var(--t-secondary); font-weight: 500; }
.qv-price-val { font-size: 14px; font-weight: 700; font-family: var(--f-mono); color: var(--t-primary); }
.qv-desc {
    font-size: 13px; color: var(--t-secondary); line-height: 1.55;
    margin-bottom: 18px;
    max-height: 80px; overflow-y: auto;
}
.qv-actions { display: flex; gap: 8px; }
.qv-btn {
    flex: 1; padding: 10px 14px;
    border-radius: var(--r-md);
    font-size: 13px; font-weight: 700;
    font-family: var(--f-system);
    display: flex; align-items: center; justify-content: center; gap: 6px;
    text-decoration: none; border: none; cursor: pointer;
    transition: transform var(--dur-fast) var(--ease-spring), filter var(--dur-fast);
}
.qv-btn:hover { filter: brightness(0.92); }
.qv-btn:active { transform: scale(0.97); }
.qv-btn.primary { background: var(--t-primary); color: #fff; }
.qv-btn.muted   { background: var(--c-fill); color: var(--t-primary); }

/* ---- Empty state ---- */
.tbl-empty { text-align: center; padding: 64px 20px; }
.tbl-empty-icon { font-size: 36px; color: var(--t-tertiary); margin-bottom: 12px; }
.tbl-empty-title { font-size: 16px; font-weight: 600; color: var(--t-secondary); margin-bottom: 6px; }
.tbl-empty-sub   { font-size: 13px; color: var(--t-tertiary); }

/* ---- Spinner ---- */
@keyframes spin { to { transform: rotate(360deg); } }
.spin { animation: spin 0.7s linear infinite; display: inline-block; }

/* ---- Helpers ---- */
.hidden { display: none !important; }
.txt-right { text-align: right; }
.txt-center { text-align: center; }
.gap-2 { gap: 8px; }

/* ---- Custom Heading Styles matching Category Page ---- */
.inv-header {
    padding-top: 16px !important;
    margin-bottom: 28px !important;
}
.inv-header-title {
    font-family: 'Inter', sans-serif !important;
    font-size: 30px !important;
    font-weight: 800 !important;
    letter-spacing: -0.025em !important;
    color: #0f172a !important;
    margin: 0 0 4px 0 !important;
    line-height: 2.25rem !important;
}
.inv-header-desc {
    font-family: 'Inter', sans-serif !important;
    font-size: 14px !important;
    color: #64748b !important;
    margin: 4px 0 24px 0 !important;
    line-height: 1.25rem !important;
    font-weight: 400 !important;
}

/* ---- Variations Expandable Styles ---- */
.variation-item-row.hidden {
    display: none !important;
}
.toggle-var-btn {
    border: none;
    background: transparent;
    cursor: pointer;
    color: var(--t-secondary);
    width: 24px;
    height: 24px;
    border-radius: var(--r-sm);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: background var(--dur-fast), color var(--dur-fast);
}
.toggle-var-btn:hover {
    background: var(--c-fill2);
    color: var(--t-primary);
}
.has-variations-parent td {
    background-color: rgba(0, 122, 255, 0.07) !important;
}
.has-variations-parent td:first-child {
    border-left: 4px solid var(--c-blue) !important;
}
.variation-item-row td {
    background-color: rgba(0, 122, 255, 0.02) !important;
}
.variation-item-row td:first-child {
    border-left: 4px solid var(--c-blue) !important;
}
</style>

<div class="inv-root">
    <div class="inv-wrap" style="padding-top: 24px;">
        <div class="inv-split">
            <div class="inv-main">
                <!-- Header -->
                <div class="inv-header" style="display: flex; justify-content: space-between; align-items: flex-end;">
                    <div>
                        <div class="inv-eyebrow">Finance / Administration</div>
                        <h1 class="inv-title">Petty Cash Dashboard</h1>
                    </div>
                    <div style="display: flex; gap: 10px; padding-bottom: 24px;">
                        <button class="sf-btn neutral" style="padding: 10px 16px;" onclick="window.openModal('settingsModal')"><i class="fa fa-cog"></i></button>
                        <button class="sf-btn neutral" style="padding: 10px 16px;" onclick="window.openModal('allocateModal')"><i class="fa fa-university"></i> Fund Float</button>
                        <button class="sf-btn primary" style="padding: 10px 16px;" onclick="window.openModal('expenseModal')"><i class="fa fa-plus"></i> Record Expense</button>
                    </div>
                </div>

    <!-- Flash messages -->
    <?php if (!empty($data['success'])): ?>
        <div class="alert alert-success">
            <i class="ph ph-check-circle"></i>
            <span><?= htmlspecialchars($data['success']) ?></span>
        </div>
    <?php endif; ?>
    <?php if (!empty($data['error'])): ?>
        <div class="alert alert-danger">
            <i class="ph ph-warning-circle"></i>
            <span><?= htmlspecialchars($data['error']) ?></span>
        </div>
    <?php endif; ?>

    <!-- KPI Grid -->
    <div class="stat-row" style="grid-template-columns: repeat(4, 1fr);">
        <!-- KPI 1: Ledger Balance -->
        <div class="stat-card blue">
            <div class="stat-icon"><i class="fa fa-wallet"></i></div>
            <div class="stat-info" style="flex: 1; width: 100%;">
                <div class="stat-lbl" style="display:flex; justify-content:space-between">Petty Cash Balance <span class="sf-badge badge-active"><span class="dot"></span>Ledger 1020</span></div>
                <div class="stat-num">Rs: <?= number_format((float)$data['ledger_balance'], 2) ?></div>
                <?php $pct = $data['config_limit'] > 0 ? ($data['ledger_balance'] / $data['config_limit']) * 100 : 0; ?>
                <div style="font-size: 11px; color: var(--t-tertiary); margin-top: 4px;"><?= round($pct) ?>% of limit</div>
            </div>
        </div>

        <!-- KPI 2: Configured Limit -->
        <div class="stat-card orange">
            <div class="stat-icon"><i class="fa fa-sliders-h"></i></div>
            <div class="stat-info">
                <div class="stat-lbl">Petty Cash Limit</div>
                <div class="stat-num">Rs: <?= number_format((float)$data['config_limit'], 2) ?></div>
                <div style="font-size: 11px; color: var(--t-tertiary); margin-top: 4px;">Custodian: <?= ($data['config'] && isset($data['config']->custodian_name)) ? htmlspecialchars($data['config']->custodian_name) : 'Not Assigned' ?></div>
            </div>
        </div>

        <!-- KPI 3: Available Balance -->
        <div class="stat-card" style="border-top: 2px solid var(--c-green);">
            <div class="stat-icon" style="background:var(--c-green-light); color:var(--c-green);"><i class="fa fa-hand-holding-usd"></i></div>
            <div class="stat-info">
                <div class="stat-lbl">Available to Spend</div>
                <div class="stat-num" style="color: <?= $data['available_balance'] <= 0 ? 'var(--c-red)' : 'var(--t-primary)' ?>;">Rs: <?= number_format((float)$data['available_balance'], 2) ?></div>
                <div style="font-size: 11px; color: var(--t-tertiary); margin-top: 4px;">Excl. pending approvals</div>
            </div>
        </div>

        <!-- KPI 4: Pending Reimbursements -->
        <div class="stat-card red">
            <div class="stat-icon"><i class="fa fa-receipt"></i></div>
            <div class="stat-info" style="flex: 1; width: 100%;">
                <?php 
                $threshold = ($data['config'] && isset($data['config']->reimbursement_threshold)) ? floatval($data['config']->reimbursement_threshold) : 10000.00;
                $exceeded = $data['pending_reimbursements'] >= $threshold;
                ?>
                <div class="stat-lbl" style="display:flex; justify-content:space-between">Pending Reimb. <?= $exceeded ? '<span class="sf-badge badge-out"><span class="dot"></span>Exceeded</span>' : '' ?></div>
                <div class="stat-num">Rs: <?= number_format((float)$data['pending_reimbursements'], 2) ?></div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top: 4px;">
                    <div style="font-size: 11px; color: var(--t-tertiary);">Threshold: Rs: <?= number_format((float)$threshold, 2) ?></div>
                    <?php if ($data['pending_reimbursements'] > 0): ?>
                        <button class="sf-btn primary" style="padding: 4px 8px; font-size: 11px;" onclick="window.openModal('reimburseModal')">Reimburse</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Wrapper -->
    <div style="background: var(--c-surface); border-radius: var(--r-xl); border: 0.5px solid var(--c-separator); box-shadow: var(--shadow-sm); overflow: hidden; display: flex; flex-direction: column; flex: 1; margin-top: 24px;">
        <div style="display: flex; border-bottom: 0.5px solid var(--c-separator); background: var(--c-surface2); padding: 8px 14px; gap: 8px;">
            <button class="sf-btn neutral tab-btn active" onclick="window.switchTab(event, 'transactionsTab')" style="border-radius: var(--r-pill); font-size: 13px; padding: 8px 16px;"><i class="fa fa-list"></i> Transactions</button>
            <button class="sf-btn neutral tab-btn" onclick="window.switchTab(event, 'reimbursementsTab')" style="border-radius: var(--r-pill); font-size: 13px; padding: 8px 16px;"><i class="fa fa-sync"></i> Reimbursements</button>
            <button class="sf-btn neutral tab-btn" onclick="window.switchTab(event, 'pendingExpensesTab')" style="border-radius: var(--r-pill); font-size: 13px; padding: 8px 16px;"><i class="fa fa-clock"></i> Unreimbursed Expenses (<?= count($data['pending_expenses']) ?>)</button>
        </div>

        <!-- Tab 1: Transactions -->
        <div id="transactionsTab" class="tab-content active">
            <!-- Filter Form -->
            <form method="GET" action="<?= APP_URL ?>/pettycash" class="filters-bar" style="display: flex; flex-wrap: wrap; gap: 15px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                <div class="filter-group" style="flex: 1 1 180px;">
                    <label for="voucher_number">Voucher No</label>
                    <input type="text" name="voucher_number" id="voucher_number" value="<?= htmlspecialchars($data['filters']['voucher_number']) ?>" placeholder="PCV-xxxxx" class="filter-input">
                </div>
                <div class="filter-group" style="flex: 1 1 180px;">
                    <label for="paid_to">Paid To / Payee</label>
                    <input type="text" name="paid_to" id="paid_to" value="<?= htmlspecialchars($data['filters']['paid_to']) ?>" placeholder="Payee name..." class="filter-input">
                </div>
                <div class="filter-group" style="flex: 1 1 150px;">
                    <label for="type">Type</label>
                    <select name="type" id="type" class="filter-input">
                        <option value="">All Types</option>
                        <option value="allocation" <?= $data['filters']['type'] === 'allocation' ? 'selected' : '' ?>>Allocation</option>
                        <option value="expense" <?= $data['filters']['type'] === 'expense' ? 'selected' : '' ?>>Expense</option>
                        <option value="reimbursement" <?= $data['filters']['type'] === 'reimbursement' ? 'selected' : '' ?>>Reimbursement</option>
                    </select>
                </div>
                <div class="filter-group" style="flex: 1 1 150px;">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="filter-input">
                        <option value="">All Statuses</option>
                        <option value="Pending" <?= $data['filters']['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Approved" <?= $data['filters']['status'] === 'Approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="Rejected" <?= $data['filters']['status'] === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                    </select>
                </div>
                <div class="filter-group" style="flex: 1 1 180px;">
                    <label for="rep_route_id">Related Route</label>
                    <select name="rep_route_id" id="rep_route_id" class="filter-input">
                        <option value="">All Routes</option>
                        <?php foreach ($data['routes'] as $r): ?>
                            <option value="<?= $r->id ?>" <?= $data['filters']['rep_route_id'] == $r->id ? 'selected' : '' ?>><?= htmlspecialchars($r->route_name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group" style="flex: 1 1 180px;">
                    <label for="rep_user_id">Related Rep</label>
                    <select name="rep_user_id" id="rep_user_id" class="filter-input">
                        <option value="">All Reps</option>
                        <?php foreach ($data['users'] as $u): ?>
                            <option value="<?= $u->id ?>" <?= $data['filters']['rep_user_id'] == $u->id ? 'selected' : '' ?>><?= htmlspecialchars($u->username) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group" style="flex: 1 1 150px;">
                    <label for="date_from">From Date</label>
                    <input type="date" name="date_from" id="date_from" value="<?= htmlspecialchars($data['filters']['date_from']) ?>" class="filter-input">
                </div>
                <div class="filter-group" style="flex: 1 1 150px;">
                    <label for="date_to">To Date</label>
                    <input type="date" name="date_to" id="date_to" value="<?= htmlspecialchars($data['filters']['date_to']) ?>" class="filter-input">
                </div>
                <div class="filter-group" style="flex: 1 1 180px;">
                    <label for="search">Keyword</label>
                    <input type="text" name="search" id="search" value="<?= htmlspecialchars($data['filters']['search']) ?>" placeholder="Desc, Ref..." class="filter-input">
                </div>
                <div class="filter-group" style="flex: 0 0 auto; display: flex; align-items: flex-end; gap: 8px;">
                    <button type="submit" class="btn btn-secondary" style="height: 38px; display: inline-flex; align-items: center; justify-content: center;"><i class="ph ph-funnel" style="margin-right: 4px;"></i> Filter</button>
                    <a href="<?= APP_URL ?>/pettycash" class="btn btn-outline" style="height: 38px; display: inline-flex; align-items: center; justify-content: center; padding: 0 15px; border: 1px solid #ccc; background: #fff; text-decoration: none; box-sizing: border-box; border-radius: 4px; color: #333;"><i class="ph ph-arrow-counter-clockwise" style="margin-right: 4px;"></i> Reset</a>
                </div>
            </form>

            <table class="pc-table">
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
                                <div class="empty-state">
                                    <i class="ph ph-receipt"></i>
                                    <p>No transactions found matching the filters.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: foreach ($data['transactions'] as $tx): ?>
                        <tr>
                            <td>
                                <strong style="font-family: monospace; font-size: 13px; color: #1b5e20;">
                                    <?= htmlspecialchars($tx->voucher_number ?: 'PCV-PENDING') ?>
                                </strong>
                            </td>
                            <td><?= date('d-M-Y', strtotime($tx->transaction_date)) ?></td>
                            <td>
                                <?php if ($tx->type === 'allocation'): ?>
                                    <span class="badge badge-info">Allocation</span>
                                <?php elseif ($tx->type === 'reimbursement'): ?>
                                    <span class="badge badge-success">Reimburse</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Expense</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($tx->type === 'allocation'): ?>
                                    <span style="color: var(--text-muted); font-size: 11px;">Allocation</span>
                                <?php elseif ($tx->type === 'reimbursement'): ?>
                                    <span style="color: var(--text-muted); font-size: 11px;">Reimbursement</span>
                                <?php else: ?>
                                    <?= htmlspecialchars($tx->offset_account_name ?: 'Petty Cash Control') ?>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($tx->paid_to ?: 'N/A') ?></td>
                            <td>
                                <?php if (!empty($tx->route_name)): ?>
                                    <span style="font-weight: 500; font-size: 12px; color: #0284c7;"><?= htmlspecialchars($tx->route_name) ?></span>
                                    <br><span style="font-size: 11px; color: var(--text-muted);">Rep: <?= htmlspecialchars($tx->rep_first_name . ' ' . $tx->rep_last_name) ?></span>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 11px;">None</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right; font-weight: bold; color: <?= $tx->type === 'expense' ? '#ef6868' : '#10b981' ?>;">
                                <?= $tx->type === 'expense' ? '-' : '+' ?>Rs: <?= number_format((float)$tx->amount, 2) ?>
                            </td>
                            <td>
                                <?php if ($tx->status === 'Approved'): ?>
                                    <span class="badge badge-success">Approved</span>
                                <?php elseif ($tx->status === 'Pending'): ?>
                                    <span class="badge badge-warning">Pending</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Rejected</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($tx->creator_name ?: 'System') ?></td>
                            <td style="text-align: center;">
                                <?php if ($tx->attachment_path): ?>
                                    <a href="<?= APP_URL ?>/<?= $tx->attachment_path ?>" target="_blank" class="btn btn-outline btn-sm" style="padding: 4px 8px;"><i class="ph ph-file-pdf"></i> View</a>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 11px;">None</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="<?= APP_URL ?>/pettycash/print_voucher/<?= $tx->id ?>" target="_blank" class="btn btn-outline btn-sm" style="padding: 4px 8px; border: 1px solid #1b5e20; color: #1b5e20;" title="Print Voucher"><i class="ph ph-printer"></i> Print</a>
                                <a href="<?= APP_URL ?>/pettycash/download_pdf/<?= $tx->id ?>" class="btn btn-outline btn-sm" style="padding: 4px 8px; border: 1px solid #d32f2f; color: #d32f2f;" title="Download PDF"><i class="ph ph-file-pdf"></i> PDF</a>
                                <?php if ($tx->status === 'Pending' && $tx->type === 'expense'): ?>
                                    <a href="<?= APP_URL ?>/pettycash/approve_expense/<?= $tx->id ?>" class="btn btn-primary btn-sm"><i class="ph ph-check"></i> Approve</a>
                                    <a href="<?= APP_URL ?>/pettycash/reject_expense/<?= $tx->id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Reject this expense?')"><i class="ph ph-x"></i> Reject</a>
                                <?php endif; ?>
                                <?php 
                                require_once __DIR__ . '/../../../core/RbacService.php';
                                if (RbacService::getInstance()->check($_SESSION['user_id'], 'petty_cash', 'delete') && empty($tx->reimbursement_id) && $tx->type !== 'reimbursement'): 
                                ?>
                                    <a href="<?= APP_URL ?>/pettycash/delete/<?= $tx->id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this transaction? This will completely undo the transaction and reverse its journal entries.')" style="margin-left: 4px;"><i class="ph ph-trash"></i> Delete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <?php if ($data['total_pages'] > 1): ?>
                <div class="pagination">
                    <span style="font-size: 13px; color: var(--text-muted);">Page <?= $data['page'] ?> of <?= $data['total_pages'] ?></span>
                    <div style="display: flex; gap: 6px;">
                        <?php if ($data['page'] > 1): ?>
                            <a href="?page=<?= $data['page'] - 1 ?>&type=<?= $data['filters']['type'] ?>&status=<?= $data['filters']['status'] ?>&date_from=<?= $data['filters']['date_from'] ?>&date_to=<?= $data['filters']['date_to'] ?>&search=<?= urlencode($data['filters']['search']) ?>" class="btn btn-secondary btn-sm">Prev</a>
                        <?php endif; ?>
                        <?php if ($data['page'] < $data['total_pages']): ?>
                            <a href="?page=<?= $data['page'] + 1 ?>&type=<?= $data['filters']['type'] ?>&status=<?= $data['filters']['status'] ?>&date_from=<?= $data['filters']['date_from'] ?>&date_to=<?= $data['filters']['date_to'] ?>&search=<?= urlencode($data['filters']['search']) ?>" class="btn btn-secondary btn-sm">Next</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Tab 2: Reimbursements -->
        <div id="reimbursementsTab" class="tab-content">
            <table class="pc-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Reimbursement ID</th>
                        <th>Method</th>
                        <th>Remarks / Description</th>
                        <th>Source Account / Bank</th>
                        <th>Cheque Details</th>
                        <th style="text-align: right;">Replenished Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['reimbursements'])): ?>
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="ph ph-arrows-clockwise"></i>
                                    <p>No reimbursement requests have been created yet.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: foreach ($data['reimbursements'] as $reim): ?>
                        <tr>
                            <td><?= date('d-M-Y', strtotime($reim->reimbursement_date)) ?></td>
                            <td><strong>REIM-<?= $reim->id ?></strong></td>
                            <td>
                                <?php 
                                $pm = !empty($reim->payment_method) ? $reim->payment_method : 'Bank Transfer';
                                $badgeStyle = $pm === 'Cash' ? 'background:#e0e7ff; color:#3730a3;' : ($pm === 'Cheque' ? 'background:#fef3c7; color:#92400e;' : 'background:#e0f2fe; color:#0369a1;');
                                ?>
                                <span class="badge" style="<?= $badgeStyle ?> font-weight:600; padding:3px 8px; border-radius:4px; font-size:11px;">
                                    <?= htmlspecialchars($pm) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($reim->description) ?></td>
                            <td>
                                <?php if ($reim->payment_method === 'Cash'): ?>
                                    <span style="color:#059669; font-weight:500;">Cash in Hand</span>
                                <?php else: ?>
                                    <?= $reim->bank_account_code ?> - <?= htmlspecialchars($reim->bank_account_name) ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($reim->payment_method === 'Cheque' || !empty($reim->cheque_number)): ?>
                                    <div style="font-size:12px;">
                                        <strong>#<?= htmlspecialchars($reim->cheque_number) ?></strong>
                                        <?php if (!empty($reim->cheque_date)): ?>
                                            <br><span style="color:#64748b; font-size:11px;"><?= date('d-M-Y', strtotime($reim->cheque_date)) ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span style="color:#94a3b8;">-</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right; font-weight: bold; color: #10b981;">Rs: <?= number_format((float)$reim->amount, 2) ?></td>
                            <td>
                                <?php if ($reim->status === 'Approved'): ?>
                                    <span class="badge badge-success">Disbursed</span>
                                <?php elseif ($reim->status === 'Pending'): ?>
                                    <span class="badge badge-warning">Pending Approval</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Rejected</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= APP_URL ?>/pettycash/print_reimbursement/<?= $reim->id ?>" target="_blank" class="btn btn-outline btn-sm" style="padding: 4px 8px; border: 1px solid #1b5e20; color: #1b5e20;" title="Print Reimbursement"><i class="ph ph-printer"></i> Print</a>
                                <a href="<?= APP_URL ?>/pettycash/download_reimbursement_pdf/<?= $reim->id ?>" class="btn btn-outline btn-sm" style="padding: 4px 8px; border: 1px solid #d32f2f; color: #d32f2f;" title="Download PDF"><i class="ph ph-file-pdf"></i> PDF</a>
                                <?php if ($reim->status === 'Pending'): ?>
                                    <a href="<?= APP_URL ?>/pettycash/approve_reimbursement/<?= $reim->id ?>" class="btn btn-primary btn-sm" style="margin-left: 4px;"><i class="ph ph-hand-coins"></i> Disburse (Replenish)</a>
                                    <a href="<?= APP_URL ?>/pettycash/reject_reimbursement/<?= $reim->id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Reject reimbursement? Linked expenses will be unlocked.')" style="margin-left: 4px;"><i class="ph ph-x"></i> Reject</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Tab 3: Pending Expenses (ready to be reimbursed) -->
        <div id="pendingExpensesTab" class="tab-content">
            <?php if (empty($data['pending_expenses'])): ?>
                <div class="empty-state">
                    <i class="ph ph-check-circle" style="color: #10b981;"></i>
                    <h3>All Approved Expenses Reimbursed</h3>
                    <p>All approved expenses have been reimbursed. Petty Cash balance matches the physical float!</p>
                </div>
            <?php else: ?>
                <div style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                    <p style="margin:0; font-size: 13.5px; color: var(--text-muted);">
                        These approved expenses are paid out of Petty Cash and await replenishment from a bank account.
                    </p>
                    <button class="btn btn-primary" onclick="window.openModal('reimburseModal')">
                        <i class="ph ph-arrows-clockwise"></i> Generate Reimbursement Request (Rs: <?= number_format((float)$data['pending_reimbursements'], 2) ?>)
                    </button>
                </div>
                <table class="pc-table">
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
                                <td><?= date('d-M-Y', strtotime($pe->transaction_date)) ?></td>
                                <td><strong><?= htmlspecialchars($pe->voucher_number ?: 'PCV-' . str_pad((string)$pe->id, 5, '0', STR_PAD_LEFT)) ?></strong></td>
                                <td><?= htmlspecialchars($pe->offset_account_code . ' - ' . $pe->offset_account_name) ?></td>
                                <td><?= htmlspecialchars($pe->paid_to ?: 'N/A') ?></td>
                                <td><?= htmlspecialchars($pe->description) ?></td>
                                <td style="text-align: right; font-weight: bold; color: #ef4444;">Rs: <?= number_format((float)$pe->amount, 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

            </div> <!-- end inv-main -->
        </div> <!-- end inv-split -->
    </div> <!-- end inv-wrap -->
</div> <!-- end inv-root -->

<!-- MODAL 1: SETTINGS -->
<?php try { ?>
<div id="settingsModal" class="modal modal-veil hidden">
    <div class="sf-modal">
        <form method="POST" action="<?= APP_URL ?>/pettycash/settings">
            <input type="hidden" name="csrf_token" value="<?= $data['csrf_token'] ?>">
            <div class="modal-head">
                <h3><i class="ph ph-gear"></i> Petty Cash Configuration</h3>
                <button type="button" class="modal-close" onclick="window.closeModal('settingsModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="limit_amount">Petty Cash Limit (Float Amount) *</label>
                    <input type="number" step="0.01" name="limit_amount" id="limit_amount" value="<?= $data['config_limit'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="custodian_id">Petty Cash Custodian *</label>
                    <select name="custodian_id" id="custodian_id" required>
                        <?php foreach ($data['users'] as $u): ?>
                            <option value="<?= $u->id ?>" <?= ($data['config'] && isset($data['config']->custodian_id) && (int)$data['config']->custodian_id === (int)$u->id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($u->username) ?> (<?= htmlspecialchars($u->email) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="default_funding_account_id">Default Funding Account *</label>
                    <select name="default_funding_account_id" id="default_funding_account_id" required>
                        <option value="">-- Select Funding Account --</option>
                        <?php foreach ($data['funding_accounts'] as $fa): ?>
                            <option value="<?= $fa->id ?>" <?= ($data['config'] && isset($data['config']->default_funding_account_id) && (int)$data['config']->default_funding_account_id === (int)$fa->id) ? 'selected' : '' ?>>
                                <?= $fa->account_code ?> - <?= htmlspecialchars($fa->account_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn btn-secondary" onclick="window.closeModal('settingsModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Settings</button>
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
                <h3><i class="ph ph-bank"></i> Fund Petty Cash Float</h3>
                <button type="button" class="modal-close" onclick="window.closeModal('allocateModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="alloc_amount">Allocation Amount (Rs) *</label>
                    <input type="number" step="0.01" name="amount" id="alloc_amount" placeholder="0.00" required>
                </div>
                <div class="form-group">
                    <label for="alloc_bank_account_id">Source Bank/Cash Account *</label>
                    <select name="bank_account_id" id="alloc_bank_account_id" required>
                        <option value="">-- Select Source Account --</option>
                        <?php foreach ($data['funding_accounts'] as $fa): ?>
                            <option value="<?= $fa->id ?>" <?= ($data['config'] && isset($data['config']->default_funding_account_id) && (int)$data['config']->default_funding_account_id === (int)$fa->id) ? 'selected' : '' ?>>
                                <?= $fa->account_code ?> - <?= htmlspecialchars($fa->account_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="alloc_date">Transaction Date *</label>
                    <input type="date" name="transaction_date" id="alloc_date" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="form-group">
                    <label for="alloc_ref">Reference (Cheque / Ref No)</label>
                    <input type="text" name="reference" id="alloc_ref" placeholder="e.g. CHQ-10045">
                </div>
                <div class="form-group">
                    <label for="alloc_desc">Description</label>
                    <textarea name="description" id="alloc_desc" rows="3" placeholder="Establish/top-up petty cash float"></textarea>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn btn-secondary" onclick="window.closeModal('allocateModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Allocate Funds</button>
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
                <h3><i class="ph ph-plus"></i> Record Petty Cash Expense</h3>
                <button type="button" class="modal-close" onclick="window.closeModal('expenseModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="exp_amount">Expense Amount (Rs) *</label>
                    <input type="number" step="0.01" name="amount" id="exp_amount" placeholder="0.00" required>
                </div>
                <div class="form-group">
                    <label for="exp_account_id">Expense Category Account *</label>
                    <select name="account_id" id="exp_account_id" required>
                        <option value="">-- Select Expense Category --</option>
                        <?php foreach ($data['expense_accounts'] as $ea): ?>
                            <option value="<?= $ea->id ?>"><?= $ea->account_code ?> - <?= htmlspecialchars($ea->account_name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="exp_paid_to">Paid To (Payee)</label>
                    <input type="text" name="paid_to" id="exp_paid_to" placeholder="e.g. City Hardware / Staff Member">
                </div>
                <div class="form-group">
                    <label for="exp_date">Transaction Date *</label>
                    <input type="date" name="transaction_date" id="exp_date" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="form-group">
                    <label for="exp_ref">Voucher / Receipt Reference</label>
                    <input type="text" name="reference" id="exp_ref" placeholder="e.g. Bill #1234">
                </div>
                <div class="form-group">
                    <label for="exp_desc">Description *</label>
                    <textarea name="description" id="exp_desc" rows="3" placeholder="Details of the expense" required></textarea>
                </div>
                <div class="form-group">
                    <label for="exp_attachment">Receipt Attachment (PDF / Image)</label>
                    <input type="file" name="attachment" id="exp_attachment" accept=".pdf,.png,.jpg,.jpeg">
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn btn-secondary" onclick="window.closeModal('expenseModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Record Expense</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 4: REQUEST REIMBURSEMENT -->
<div id="reimburseModal" class="modal modal-veil hidden">
    <div class="sf-modal">
        <form method="POST" action="<?= APP_URL ?>/pettycash/reimburse">
            <input type="hidden" name="csrf_token" value="<?= $data['csrf_token'] ?>">
            <div class="modal-head">
                <h3><i class="ph ph-arrows-clockwise"></i> Generate Reimbursement Request</h3>
                <button type="button" class="modal-close" onclick="window.closeModal('reimburseModal')">&times;</button>
            </div>
            <div class="modal-body">
                <?php 
                $reimAmount = 0.0;
                foreach ($data['pending_expenses'] as $pe) {
                    $reimAmount += floatval($pe->amount);
                }
                ?>
                <div style="background: rgba(79, 70, 229, 0.06); padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid rgba(79, 70, 229, 0.1);">
                    <p style="font-size: 13px; color: var(--text-muted);">Total Reimbursement Value:</p>
                    <p style="font-size: 24px; font-weight: 700; color: var(--text-main); margin-top: 4px;">Rs: <?= number_format((float)$reimAmount, 2) ?></p>
                    <p style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">Includes <strong><?= count($data['pending_expenses']) ?></strong> approved expense transactions.</p>
                </div>

                <div class="form-group">
                    <label for="reim_payment_method">Reimbursement Method *</label>
                    <select name="payment_method" id="reim_payment_method" required onchange="toggleReimbursementFields(this.value)">
                        <option value="Bank Transfer" selected>Bank Transfer</option>
                        <option value="Cash">Cash in Hand</option>
                        <option value="Cheque">Cheque</option>
                    </select>
                </div>

                <div class="form-group" id="reim_bank_group">
                    <label for="reim_bank_account_id">Source Bank Account *</label>
                    <select name="bank_account_id" id="reim_bank_account_id" required>
                        <option value="">-- Select Source Bank --</option>
                        <?php foreach ($data['funding_accounts'] as $fa): ?>
                            <option value="<?= $fa->id ?>" <?= ($data['config'] && isset($data['config']->default_funding_account_id) && (int)$data['config']->default_funding_account_id === (int)$fa->id) ? 'selected' : '' ?>>
                                <?= $fa->account_code ?> - <?= htmlspecialchars($fa->account_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group hidden" id="reim_cash_info" style="background: rgba(16, 185, 129, 0.08); padding: 12px 15px; border-radius: 8px; border: 1px solid rgba(16, 185, 129, 0.2); margin-bottom: 15px;">
                    <p style="margin: 0; font-size: 13px; color: #047857; font-weight: 500;">
                        <i class="ph ph-money" style="margin-right: 4px;"></i> Funds will be automatically deducted from the <strong>Cash in Hand Account</strong> upon approval.
                    </p>
                </div>

                <div id="reim_cheque_group" class="hidden" style="background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 15px;">
                    <div class="form-group">
                        <label for="reim_cheque_number">Cheque Number *</label>
                        <input type="text" name="cheque_number" id="reim_cheque_number" placeholder="e.g. 331305">
                    </div>
                    <div class="form-group" style="margin-top: 10px;">
                        <label for="reim_cheque_date">Cheque Date / Banking Date *</label>
                        <input type="date" name="cheque_date" id="reim_cheque_date" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group" style="margin-top: 10px;">
                        <label for="reim_payee_name">Payee Name (Optional)</label>
                        <input type="text" name="payee_name" id="reim_payee_name" placeholder="e.g. Petty Cash Custodian">
                    </div>
                </div>

                <div class="form-group">
                    <label for="reim_date">Reimbursement Date *</label>
                    <input type="date" name="reimbursement_date" id="reim_date" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="form-group">
                    <label for="reim_desc">Remarks / Description</label>
                    <textarea name="description" id="reim_desc" rows="3" placeholder="Reimbursement of office expenses"><?= 'Reimbursement request for ' . count($data['pending_expenses']) . ' office expenses' ?></textarea>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn btn-secondary" onclick="window.closeModal('reimburseModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Generate Request</button>
            </div>
        </form>
    </div>
</div>

<script>
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
</script>
<?php } catch (\Throwable $e) { echo "<script>alert('PHP View Error: " . addslashes($e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine()) . "');</script>"; } ?>


