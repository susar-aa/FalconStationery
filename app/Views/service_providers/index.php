<?php
$totalProviders = count($data['service_providers'] ?? []);
$totalOutstanding = 0;
$owedProvidersCount = 0;
foreach ($data['service_providers'] ?? [] as $s) {
    $bal = floatval($s->outstanding_balance ?? 0);
    $totalOutstanding += $bal;
    if ($bal > 0) {
        $owedProvidersCount++;
    }
}
?>

<!-- Inter Font & FontAwesome Icons -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
/* ============================================================
   SF PRO + APPLE DESIGN LANGUAGE - SERVICE PROVIDER CENTER
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

.sp-root {
    font-family: var(--f-system);
    font-size: 15px;
    color: var(--t-primary);
    background: var(--c-bg);
    -webkit-font-smoothing: antialiased;
}

.sp-wrap { max-width: 1420px; margin: 0 auto; padding: 16px 24px 100px; }

.stat-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 24px; margin-top: 10px;}
@media (max-width: 768px) { .stat-row { grid-template-columns: 1fr; } }
.stat-card { background: var(--c-surface); border-radius: var(--r-xl); padding: 16px 20px; box-shadow: var(--shadow-sm); border: 0.5px solid var(--c-separator); transition: transform var(--dur-fast), box-shadow var(--dur-fast); position: relative; overflow: hidden; display: flex; align-items: center; gap: 16px; }
.stat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2.5px; border-radius: var(--r-xl) var(--r-xl) 0 0; }
.stat-card.blue::before  { background: var(--c-blue); }
.stat-card.orange::before { background: var(--c-orange); }
.stat-card.red::before   { background: var(--c-red); }
.stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
.stat-icon { width: 46px; height: 46px; border-radius: var(--r-sm); display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
.stat-card.blue  .stat-icon { background: var(--c-blue-light); color: var(--c-blue); }
.stat-card.orange .stat-icon { background: var(--c-orange-light); color: var(--c-orange); }
.stat-card.red   .stat-icon { background: var(--c-red-light); color: var(--c-red); }
.stat-num { font-size: 22px; font-weight: 700; color: var(--t-primary); line-height: 1.1; margin-bottom: 2px; }
.stat-lbl { font-size: 11px; font-weight: 600; text-transform: uppercase; color: var(--t-label); letter-spacing: 0.05em; }

.filter-shelf { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; margin-bottom: 20px; }
.filter-chip { display: inline-flex; align-items: center; gap: 6px; background: var(--c-surface); border: 0.5px solid var(--c-separator); border-radius: var(--r-pill); padding: 7px 14px; font-size: 13px; font-weight: 500; color: var(--t-secondary); box-shadow: var(--shadow-xs); transition: border-color var(--dur-fast); }
.filter-chip:focus-within { border-color: var(--c-blue); box-shadow: 0 0 0 3px rgba(0,122,255,0.12); }
.filter-chip-label { font-size: 11px; font-weight: 700; color: var(--t-label); text-transform: uppercase; }
.filter-reset { background: transparent; border: 0.5px solid var(--c-separator); border-radius: var(--r-pill); padding: 7px 14px; font-size: 13px; font-weight: 600; color: var(--t-secondary); cursor: pointer; transition: background var(--dur-fast); }
.filter-reset:hover { background: var(--c-fill); color: var(--t-primary); }
.filter-count { margin-left: auto; font-size: 13px; color: var(--t-secondary); font-weight: 500; }
.filter-count strong { color: var(--t-primary); font-weight: 700; }

.sf-dropdown { position: relative; outline: none; cursor: pointer; }
.sf-dropdown-val { display: flex; align-items: center; gap: 5px; font-size: 13.5px; font-weight: 600; color: var(--t-primary); }
.sf-dropdown-val::after { content: ''; display: inline-block; width: 12px; height: 12px; background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%238e8e93' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") center/contain no-repeat; }
.sf-dropdown-menu { position: absolute; top: calc(100% + 10px); left: 0; z-index: 200; background: var(--c-surface); border-radius: var(--r-md); border: 0.5px solid var(--c-separator); box-shadow: var(--shadow-xl); min-width: 200px; max-height: 280px; overflow-y: auto; opacity: 0; visibility: hidden; transform: translateY(-6px) scale(0.98); transition: opacity var(--dur-mid), transform var(--dur-mid), visibility var(--dur-mid); padding: 6px; }
.sf-dropdown:focus-within .sf-dropdown-menu { opacity: 1; visibility: visible; transform: translateY(0) scale(1); }
.sf-dropdown-item { padding: 9px 12px; font-size: 13.5px; font-weight: 500; color: var(--t-primary); border-radius: var(--r-sm); transition: background var(--dur-fast); cursor: pointer; }
.sf-dropdown-item:hover { background: var(--c-fill); }
.sf-dropdown-item.active { background: var(--c-blue-light); color: var(--c-blue); font-weight: 600; }

.table-panel { background: var(--c-surface); border-radius: var(--r-xl); border: 0.5px solid var(--c-separator); box-shadow: var(--shadow-sm); overflow: hidden; }
.sp-table { width: 100%; border-collapse: collapse; }
.sp-table thead th { padding: 13px 18px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--t-label); background: var(--c-surface2); border-bottom: 0.5px solid var(--c-separator); text-align: left; }
.sp-table tbody tr { transition: background var(--dur-fast); border-bottom: 0.5px solid var(--c-separator2); cursor: pointer; }
.sp-table tbody tr:hover { background: var(--c-fill2); }
.sp-table td { padding: 14px 18px; font-size: 14px; color: var(--t-primary); vertical-align: middle; }
.txt-right { text-align: right !important; }
.txt-center { text-align: center !important; }

.sf-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 8px; border-radius: var(--r-xs); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.02em; }
.badge-active { background: var(--c-green-light); color: var(--c-green); }
.badge-owed   { background: var(--c-red-light); color: var(--c-red); }
.badge-inactive { background: var(--c-fill2); color: var(--t-secondary); }
.badge-Unpaid { background: var(--c-red-light); color: var(--c-red); }
.badge-Paid { background: var(--c-green-light); color: var(--c-green); }
.badge-PartiallyPaid { background: var(--c-orange-light); color: var(--c-orange); }
.sf-badge .dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
.avatar-circle { width: 36px; height: 36px; background: var(--c-blue-light); color: var(--c-blue); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; flex-shrink: 0; }

.sf-alert { display: flex; align-items: flex-start; gap: 12px; background: var(--c-surface); border-radius: var(--r-md); padding: 14px 16px; margin-bottom: 20px; box-shadow: var(--shadow-xs); border: 0.5px solid var(--c-separator); border-left-width: 3.5px; font-size: 14px; }
.sf-alert.success { border-left-color: var(--c-green); }
.sf-alert.error   { border-left-color: var(--c-red); }
.sf-alert-icon { font-size: 18px; flex-shrink: 0; }
.sf-alert.success .sf-alert-icon { color: var(--c-green); }
.sf-alert.error   .sf-alert-icon { color: var(--c-red); }
.sf-alert-title { font-weight: 700; color: var(--t-primary); margin-bottom: 2px; }
.sf-alert-msg   { color: var(--t-secondary); font-size: 13px; }
.sf-alert-close { margin-left: auto; flex-shrink: 0; background: none; border: none; color: var(--t-tertiary); cursor: pointer; font-size: 15px; padding: 2px; }

.sf-btn { padding: 8px 14px; border-radius: var(--r-md); font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; border: 0.5px solid transparent; cursor: pointer; transition: transform var(--dur-fast); text-decoration: none; font-family: var(--f-system); }
.sf-btn:active { transform: scale(0.97); }
.sf-btn.primary { background: var(--c-blue); color: #fff; }
.sf-btn.danger { background: var(--c-red); color: #fff; }
.sf-btn.success { background: var(--c-green); color: #fff; }
.sf-btn.neutral { background: var(--c-surface); border-color: var(--c-separator); color: var(--t-primary); box-shadow: var(--shadow-xs); }
.sf-btn.neutral:hover { background: var(--c-surface2); }
.act-btn { width: 28px; height: 28px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; border: none; background: transparent; cursor: pointer; font-size: 13.5px; transition: background var(--dur-fast); }
.act-btn.view  { color: var(--c-blue); }
.act-btn.view:hover { background: var(--c-blue-light); }
.act-btn.edit  { color: var(--t-secondary); }
.act-btn.edit:hover { background: var(--c-fill); }

.cmd-bar { position: fixed; bottom: 28px; left: 50%; transform: translateX(-50%); background: rgba(28, 28, 30, 0.92); backdrop-filter: saturate(180%) blur(28px); border: 0.5px solid rgba(255,255,255,0.12); border-radius: var(--r-pill); padding: 7px 10px; display: flex; align-items: center; gap: 4px; box-shadow: var(--shadow-xl), 0 0 0 0.5px rgba(0,0,0,0.3); z-index: 100; }
.cmd-search { display: flex; align-items: center; gap: 9px; background: rgba(255,255,255,0.1); border-radius: var(--r-pill); padding: 8px 14px; width: 220px; transition: width var(--dur-slow) var(--ease-ios); }
.cmd-search:focus-within { width: 340px; background: rgba(255,255,255,0.18); }
.cmd-search i { color: rgba(255,255,255,0.55); font-size: 14px; flex-shrink: 0; }
.cmd-search input { background: transparent; border: none; outline: none; color: #fff; font-size: 14px; font-weight: 500; font-family: var(--f-system); width: 100%; }
.cmd-search input::placeholder { color: rgba(255,255,255,0.45); }
.cmd-divider { width: 0.5px; height: 22px; background: rgba(255,255,255,0.15); margin: 0 3px; }
.cmd-cta { display: flex; align-items: center; gap: 7px; background: #fff; color: #1c1c1e; border: none; border-radius: var(--r-pill); padding: 0 18px; height: 38px; font-size: 14px; font-weight: 700; font-family: var(--f-system); cursor: pointer; transition: transform var(--dur-fast); margin-left: 2px; }
.cmd-cta:hover { background: #e5e5ea; transform: scale(0.97); }

.sf-pagination { display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; background: var(--c-surface2); border-top: 0.5px solid var(--c-separator); }
.pg-info { font-size: 13px; color: var(--t-secondary); }
.pg-right { display: flex; align-items: center; gap: 20px; }
.pg-size-wrap { display: flex; align-items: center; gap: 8px; }
.pg-size-lbl { font-size: 12px; font-weight: 600; color: var(--t-label); text-transform: uppercase; }
.pg-size-sel { font-family: var(--f-system); font-size: 13px; font-weight: 600; color: var(--t-primary); background: var(--c-fill); border: 0.5px solid var(--c-separator); border-radius: var(--r-sm); padding: 5px 9px; outline: none; cursor: pointer; }
.pg-nav { display: flex; border: 0.5px solid var(--c-separator); border-radius: var(--r-sm); overflow: hidden; }
.pg-btn { width: 34px; height: 32px; display: flex; align-items: center; justify-content: center; background: var(--c-surface); border: none; cursor: pointer; color: var(--t-primary); font-size: 12px; transition: background var(--dur-fast); }
.pg-btn:hover:not(:disabled) { background: var(--c-fill); }
.pg-btn:disabled { opacity: 0.35; cursor: not-allowed; }
.pg-btn + .pg-btn { border-left: 0.5px solid var(--c-separator); }
.pg-current { padding: 0 14px; display: flex; align-items: center; font-size: 13px; font-weight: 600; color: var(--t-primary); background: var(--c-surface); border-left: 0.5px solid var(--c-separator); border-right: 0.5px solid var(--c-separator); }

.modal-veil { position: fixed; inset: 0; z-index: 2000; background: rgba(0, 0, 0, 0.4); backdrop-filter: blur(10px); display: flex; align-items: center; justify-content: center; transition: opacity var(--dur-mid); }
.modal-veil.hidden { display: none; }
.sf-modal { background: var(--c-surface); border: 0.5px solid var(--c-separator); border-radius: var(--r-xl); box-shadow: var(--shadow-xl); width: 520px; max-width: 95vw; animation: sfModalSlide var(--dur-mid) var(--ease-spring); overflow: hidden; }
@keyframes sfModalSlide { from { transform: translateY(20px) scale(0.97); opacity: 0; } to { transform: translateY(0) scale(1); opacity: 1; } }
.modal-head { display: flex; justify-content: space-between; align-items: center; padding: 18px 24px; border-bottom: 0.5px solid var(--c-separator); }
.modal-title { font-size: 16px; font-weight: 700; margin: 0; }
.modal-close { background: var(--c-fill); border: none; width: 26px; height: 26px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--t-label); cursor: pointer; }
.modal-close:hover { background: var(--c-fill2); color: var(--t-secondary); }
.modal-body { padding: 24px; }
.modal-foot { display: flex; justify-content: flex-end; gap: 10px; padding: 16px 24px; background: var(--c-surface2); border-top: 0.5px solid var(--c-separator); }

.sf-group { margin-bottom: 16px; }
.sf-group label { display: block; margin-bottom: 6px; font-size: 12px; font-weight: 600; color: var(--t-secondary); text-transform: uppercase; }
.sf-input { width: 100%; padding: 10px 14px; border-radius: var(--r-sm); border: 0.5px solid var(--c-separator); background: var(--c-surface2); color: var(--t-primary); font-size: 14px; outline: none; box-sizing: border-box; }
.sf-input:focus { border-color: var(--c-blue); background: var(--c-surface); }
.grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; }

.tabs { display: flex; border-bottom: 0.5px solid var(--c-separator); background: var(--c-surface2); padding: 0 24px; }
.tab-btn { padding: 12px 18px; border: none; background: transparent; cursor: pointer; font-size: 13px; font-weight: 600; color: var(--t-secondary); border-bottom: 2.5px solid transparent; transition: 0.18s; }
.tab-btn:hover { color: var(--c-blue); }
.tab-btn.active { color: var(--c-blue); border-bottom-color: var(--c-blue); }
.tab-content { display: none; }
.tab-content.active { display: block; }
.data-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
.data-table th, .data-table td { padding: 10px 12px; text-align: left; border-bottom: 0.5px solid var(--c-separator2); font-size: 13px; }
.data-table th { color: var(--t-label); font-weight: 600; font-size: 10.5px; text-transform: uppercase; background: var(--c-surface2); }
.num-col { text-align: right !important; }

@keyframes spin { to { transform: rotate(360deg); } }
.spin { animation: spin 0.7s linear infinite; display: inline-block; }
.hidden { display: none !important; }
</style>

<div class="sp-root">
    <div class="sp-wrap">

        <!-- Top KPI Stats Row -->
        <div class="stat-row">
            <div class="stat-card blue">
                <div class="stat-icon"><i class="fa-solid fa-bolt"></i></div>
                <div class="stat-info">
                    <div class="stat-num"><?= number_format($totalProviders) ?></div>
                    <div class="stat-lbl">Total Service Providers</div>
                </div>
            </div>
            <div class="stat-card orange">
                <div class="stat-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                <div class="stat-info">
                    <div class="stat-num">Rs. <?= number_format($totalOutstanding, 2) ?></div>
                    <div class="stat-lbl">Total Payable Outstanding</div>
                </div>
            </div>
            <div class="stat-card red">
                <div class="stat-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div class="stat-info">
                    <div class="stat-num"><?= number_format($owedProvidersCount) ?></div>
                    <div class="stat-lbl">Accounts to Settle</div>
                </div>
            </div>
        </div>

        <!-- Feedback Alerts -->
        <?php if (!empty($data['error'])): ?>
            <div class="sf-alert error" id="error-alert">
                <i class="fa-solid fa-circle-exclamation sf-alert-icon"></i>
                <div>
                    <div class="sf-alert-title">Error</div>
                    <div class="sf-alert-msg"><?= htmlspecialchars($data['error']) ?></div>
                </div>
                <button type="button" class="sf-alert-close" onclick="document.getElementById('error-alert').style.display='none'"><i class="fa-solid fa-xmark"></i></button>
            </div>
        <?php endif; ?>
        <?php if (!empty($data['success'])): ?>
            <div class="sf-alert success" id="success-alert">
                <i class="fa-solid fa-circle-check sf-alert-icon"></i>
                <div>
                    <div class="sf-alert-title">Success</div>
                    <div class="sf-alert-msg"><?= htmlspecialchars($data['success']) ?></div>
                </div>
                <button type="button" class="sf-alert-close" onclick="document.getElementById('success-alert').style.display='none'"><i class="fa-solid fa-xmark"></i></button>
            </div>
        <?php endif; ?>

        <!-- Filter Shelf -->
        <div class="filter-shelf">
            <!-- Payment Status Dropdown -->
            <div class="filter-chip">
                <span class="filter-chip-label">Ledger</span>
                <div class="sf-dropdown" tabindex="0">
                    <div class="sf-dropdown-val" id="status-dropdown-val">All Accounts</div>
                    <div class="sf-dropdown-menu">
                        <div class="sf-dropdown-item active" data-val="" onclick="selectStatus('', 'All Accounts')">All Accounts</div>
                        <div class="sf-dropdown-item" data-val="owed" onclick="selectStatus('owed', 'Has Unpaid Balance')">Has Unpaid Balance</div>
                        <div class="sf-dropdown-item" data-val="cleared" onclick="selectStatus('cleared', 'Zero Balance')">Zero Balance</div>
                    </div>
                    <input type="hidden" id="filterStatus" value="">
                </div>
            </div>

            <!-- Category Filter Dropdown -->
            <div class="filter-chip">
                <span class="filter-chip-label">Category</span>
                <div class="sf-dropdown" tabindex="0">
                    <div class="sf-dropdown-val" id="category-dropdown-val">All Categories</div>
                    <div class="sf-dropdown-menu">
                        <div class="sf-dropdown-item active" data-val="" onclick="selectCategory('', 'All Categories')">All Categories</div>
                        <?php
                        $categories = [
                            'Telephone', 'Internet', 'Electricity', 'Water', 'Mobile',
                            'Insurance', 'Courier', 'Cloud Services', 'Software Subscription',
                            'Maintenance', 'Security', 'Rent', 'Other'
                        ];
                        foreach($categories as $cat):
                        ?>
                            <div class="sf-dropdown-item" data-val="<?= htmlspecialchars($cat) ?>" onclick="selectCategory('<?= htmlspecialchars($cat) ?>', '<?= htmlspecialchars($cat) ?>')"><?= htmlspecialchars($cat) ?></div>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" id="filterCategory" value="">
                </div>
            </div>

            <!-- Reset Button -->
            <button type="button" onclick="clearAllFilters()" class="filter-reset">Reset</button>

            <!-- Counter -->
            <div class="filter-count">
                <strong id="matching-count"><?= count($data['service_providers']) ?></strong> service providers
            </div>
        </div>

        <!-- Service Providers Master Table -->
        <div class="table-panel">
            <table class="sp-table">
                <thead>
                    <tr>
                        <th>Service Provider / Company</th>
                        <th>Category & Contact</th>
                        <th class="txt-right">Outstanding Balance</th>
                        <th>Status</th>
                        <th style="width:120px;" class="txt-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="spList">
                    <?php if (empty($data['service_providers'])): ?>
                        <tr>
                            <td colspan="5" style="text-align:center; padding:40px; color:var(--t-secondary);">
                                <i class="fa-solid fa-bolt" style="font-size:28px; margin-bottom:8px; color:var(--t-tertiary);"></i><br>
                                No service providers registered yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($data['service_providers'] as $s): ?>
                            <?php 
                            $bal = floatval($s->outstanding_balance); 
                            $badgeCls = $bal > 0 ? 'badge-owed' : ($s->status === 'Inactive' ? 'badge-inactive' : 'badge-active');
                            $badgeTxt = $bal > 0 ? 'Owed' : ($s->status === 'Inactive' ? 'Inactive' : 'Active');
                            ?>
                            <tr class="provider-row" 
                                onclick="openServiceProviderModalPopup(<?= $s->id ?>)"
                                data-id="<?= $s->id ?>"
                                data-name="<?= htmlspecialchars(strtolower($s->name ?? '')) ?>"
                                data-category="<?= htmlspecialchars(strtolower($s->service_category ?? 'general')) ?>"
                                data-phone="<?= htmlspecialchars(strtolower($s->phone ?? '')) ?>"
                                data-email="<?= htmlspecialchars(strtolower($s->email ?? '')) ?>"
                                data-outstanding="<?= $bal ?>">
                                
                                <td>
                                    <div style="display:flex; align-items:center; gap:12px;">
                                        <div class="avatar-circle"><?= strtoupper(substr($s->name ?? '', 0, 2)) ?></div>
                                        <div>
                                            <strong style="font-size:14.5px; font-weight:600; color:var(--t-primary);"><?= htmlspecialchars($s->name ?? '') ?></strong>
                                            <span style="font-size:11px; color:var(--t-secondary); display:block; margin-top:2px;">
                                                🏠 <?= !empty($s->address) ? htmlspecialchars($s->address) : '<span style="color:var(--c-red); font-weight:600;">Missing Address</span>' ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size:12px; font-weight:600; color:var(--c-purple); margin-bottom:2px;">
                                        ⚡ <?= htmlspecialchars($s->service_category ?: 'General Service') ?>
                                    </div>
                                    <span style="font-size:13px; font-weight:500; display:block;">
                                        📞 <?= !empty($s->phone) ? htmlspecialchars($s->phone) : '<span style="color:var(--c-red); font-weight:600;">Missing Phone</span>' ?>
                                    </span>
                                    <?php if (!empty($s->email)): ?>
                                        <span style="font-size:11px; color:var(--t-secondary); display:block; margin-top:2px;">
                                            ✉️ <?= htmlspecialchars($s->email) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="txt-right" style="font-weight:700; font-family:var(--f-mono); font-size:14px; color: <?= $bal > 0 ? 'var(--c-red)' : 'var(--c-green)' ?>;">
                                    Rs: <?= number_format($bal, 2) ?>
                                </td>
                                <td>
                                    <div style="display:flex; flex-direction:column; gap:4px; align-items:flex-start;">
                                        <span class="sf-badge <?= $badgeCls ?>">
                                            <span class="dot"></span><?= $badgeTxt ?>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div style="display:flex; justify-content:center; gap:6px;" onclick="event.stopPropagation()">
                                        <button type="button" class="act-btn view" onclick="openServiceProviderModalPopup(<?= $s->id ?>)" title="View profile & activity ledger">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination Bar -->
            <div class="sf-pagination">
                <div class="pg-info" id="pg-info-text">
                    Showing <strong>1</strong> – <strong>15</strong> of <strong><?= count($data['service_providers']) ?></strong>
                </div>
                <div class="pg-right">
                    <div class="pg-size-wrap">
                        <span class="pg-size-lbl">Per page</span>
                        <select class="pg-size-sel native-select" onchange="updatePageSize(this.value)">
                            <option value="15" selected>15</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="1000">All</option>
                        </select>
                    </div>
                    <div class="pg-nav" id="pg-nav-container">
                        <button type="button" class="pg-btn" id="pg-prev-btn" onclick="navigatePage(currentPage - 1)">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        <div class="pg-current" id="pg-current-text">1 / 1</div>
                        <button type="button" class="pg-btn" id="pg-next-btn" onclick="navigatePage(currentPage + 1)">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Dynamic Command / Search Floating Bar -->
<div class="cmd-bar">
    <div class="cmd-search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="searchInput" placeholder="Search service providers..." onkeyup="filterList()">
    </div>
    <div class="cmd-divider"></div>
    <button type="button" class="cmd-cta" onclick="openModal('addProviderModal')">
        <i class="fa-solid fa-plus" style="font-size:13px;"></i> New
    </button>
</div>

<!-- ============================================================
     POPUP MODAL: SERVICE PROVIDER PROFILE, LEDGER & BILLS
     ============================================================ -->
<div id="serviceProviderProfileModal" class="modal-veil hidden" onclick="if(event.target === this) closeServiceProviderProfile()">
    <div class="sf-modal" style="width: 88%; max-width: 1060px; height: 88vh; display: flex; flex-direction: column; padding: 0; overflow: hidden; border-radius: var(--r-lg);">
        
        <div class="modal-head" id="modal-header-container" style="padding: 18px 24px; border-bottom: 0.5px solid var(--c-separator); background: var(--c-surface2);">
            <div style="display: flex; gap: 15px; align-items: center;">
                <div class="avatar-circle" style="width: 40px; height: 40px; font-size: 16px;">SP</div>
                <div>
                    <h3 class="modal-title" style="font-size: 18px; font-weight: 700;">Service Provider Details</h3>
                </div>
            </div>
            <button type="button" onclick="closeServiceProviderProfile()" class="modal-close" style="width:30px; height:30px;"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <div id="modal-loader" style="display:none; flex:1; align-items:center; justify-content:center; flex-direction:column; gap:12px; background:var(--c-surface);">
            <i class="fa-solid fa-spinner spin" style="font-size:32px; color:var(--c-blue);"></i>
            <span style="font-size:14px; color:var(--t-secondary); font-weight:500;">Loading provider profile & ledger...</span>
        </div>
        
        <div id="modal-profile-content" style="flex:1; display:flex; flex-direction:column; overflow:hidden;">
            <!-- Content will load dynamically here -->
        </div>
    </div>
</div>

<!-- ============================================================
     HIDDEN TEMPLATE SOURCES (EXTRACTED VIA DOM PARSER)
     ============================================================ -->
<?php if ($data['selected_service_provider']): ?>
    <?php $sup = $data['selected_service_provider']; $stats = $data['stats']; ?>
    
    <div id="modal-header-source" class="hidden">
        <div style="display: flex; gap: 15px; align-items: center; min-width: 0; flex: 1;">
            <div class="avatar-circle" style="width: 40px; height: 40px; font-size: 16px; background: var(--c-blue-light); color: var(--c-blue); flex-shrink: 0;">
                <?= strtoupper(substr($sup->name ?? '', 0, 2)) ?>
            </div>
            <div style="min-width: 0; max-width: 280px;">
                <h3 class="modal-title" style="font-size: 16.5px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0;" title="<?= htmlspecialchars($sup->name ?? '') ?>">
                    <?= htmlspecialchars($sup->name ?? '') ?>
                </h3>
                <div style="font-size: 11px; color: var(--t-secondary); display: flex; gap: 10px; margin-top: 2px; white-space: nowrap; overflow: hidden; align-items: center;">
                    <span>📞 <?= !empty($sup->phone) ? htmlspecialchars($sup->phone) : '<span style="color:var(--c-red); font-weight:600;">Missing</span>' ?></span>
                    <span>✉️ <?= !empty($sup->email) ? htmlspecialchars($sup->email) : '<span style="color:var(--c-red); font-weight:600;">Missing</span>' ?></span>
                    <span style="color:var(--c-purple); font-weight:600;">⚡ <?= htmlspecialchars($sup->service_category ?: 'General') ?></span>
                </div>
            </div>
        </div>
        
        <!-- Header Statistics Cards -->
        <div style="display: flex; gap: 8px; margin-right: 18px; flex-shrink: 0; align-items: center;">
            <div style="background: var(--c-fill); padding: 5px 10px; border-radius: var(--r-sm); text-align: center; min-width: 95px;">
                <div style="font-size: 8.5px; color: var(--t-label); text-transform: uppercase; font-weight: 700; letter-spacing: 0.04em;">Total Billed</div>
                <div style="font-size: 13px; font-weight: bold; color: var(--t-primary); margin-top: 1px; font-family: var(--f-mono);">Rs: <?= number_format($stats->total_billed, 2) ?></div>
            </div>
            <div style="background: var(--c-green-light); padding: 5px 10px; border-radius: var(--r-sm); border: 0.5px solid rgba(52,199,89,0.2); text-align: center; min-width: 95px;">
                <div style="font-size: 8.5px; color: var(--c-green); text-transform: uppercase; font-weight: 700; letter-spacing: 0.04em;">Total Paid</div>
                <div style="font-size: 13px; font-weight: bold; color: var(--c-green); margin-top: 1px; font-family: var(--f-mono);">Rs: <?= number_format($stats->total_paid, 2) ?></div>
            </div>
            <div style="background: <?= $stats->outstanding > 0 ? 'var(--c-red-light)' : 'var(--c-green-light)' ?>; padding: 5px 10px; border-radius: var(--r-sm); border: 0.5px solid <?= $stats->outstanding > 0 ? 'rgba(255,59,48,0.2)' : 'rgba(52,199,89,0.2)' ?>; text-align: center; min-width: 95px;">
                <div style="font-size: 8.5px; color: <?= $stats->outstanding > 0 ? 'var(--c-red)' : 'var(--c-green)' ?>; text-transform: uppercase; font-weight: 700; letter-spacing: 0.04em;">Outstanding</div>
                <div style="font-size: 13px; font-weight: bold; color: <?= $stats->outstanding > 0 ? 'var(--c-red)' : 'var(--c-green)' ?>; margin-top: 1px; font-family: var(--f-mono);">Rs: <?= number_format($stats->outstanding, 2) ?></div>
            </div>
        </div>

        <button type="button" onclick="closeServiceProviderProfile()" class="modal-close" style="width:30px; height:30px; flex-shrink: 0;"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div id="modal-profile-content-source" class="hidden">
        <!-- Tab Navigation -->
        <div class="tabs">
            <button class="tab-btn active" onclick="switchModalTab('ledger')" id="mbtn_ledger">Activity Ledger</button>
            <button class="tab-btn" onclick="switchModalTab('bills')" id="mbtn_bills">Service Bills</button>
            <button class="tab-btn" onclick="switchModalTab('profile')" id="mbtn_profile">Profile & Settings</button>
        </div>

        <!-- TAB 1: Ledger -->
        <div id="mtab_ledger" class="tab-content active" style="padding: 22px; overflow-y: auto; flex: 1;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Transaction Type</th>
                        <th>Reference / Description</th>
                        <th class="num-col">Dr (Paid/Returned)</th>
                        <th class="num-col">Cr (Billed/Services)</th>
                        <th class="num-col">Payable Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($data['ledger'])): ?>
                        <tr><td colspan="6" style="text-align: center; color: var(--t-secondary); padding: 25px;">No financial activity recorded for this service provider.</td></tr>
                    <?php else: foreach($data['ledger'] as $l): ?>
                        <tr>
                            <td style="color:var(--t-secondary); font-size:12px;"><?= date('M d, Y', strtotime($l->date)) ?></td>
                            <td><strong><?= htmlspecialchars($l->type) ?></strong></td>
                            <td>
                                <?php if($l->type == 'GRN' || $l->type == 'Service Bill'): ?>
                                    <a href="<?= APP_URL ?>/serviceprovider/bill/<?= $l->id ?>" target="_blank" style="color:var(--c-blue); font-weight:bold; text-decoration:none;">
                                        <?= htmlspecialchars($l->ref) ?> ↗
                                    </a>
                                <?php elseif($l->type == 'Supplier Return'): ?>
                                    <a href="<?= APP_URL ?>/supplier-return/show/<?= $l->id ?>" target="_blank" style="color:var(--c-red); font-weight:bold; text-decoration:none;">
                                        <?= htmlspecialchars($l->ref) ?> ↗
                                    </a>
                                <?php elseif($l->type == 'Payment Reversal'): ?>
                                    <span style="color: var(--c-orange); font-size: 12px; font-weight:600; background: var(--c-orange-light); padding: 2px 6px; border-radius: 4px;"><?= htmlspecialchars($l->ref) ?></span>
                                <?php else: ?>
                                    <span style="color:var(--t-primary);"><?= htmlspecialchars($l->ref) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="num-col" style="color:var(--c-green); font-weight:500; font-family:var(--f-mono);"><?= $l->debit > 0 ? 'Rs: ' . number_format($l->debit, 2) : '-' ?></td>
                            <td class="num-col" style="font-weight:500; font-family:var(--f-mono);"><?= $l->credit > 0 ? 'Rs: ' . number_format($l->credit, 2) : '-' ?></td>
                            <td class="num-col" style="font-weight:bold; font-family:var(--f-mono); color: <?= $l->balance > 0 ? 'var(--c-red)' : 'var(--c-green)' ?>;">Rs: <?= number_format($l->balance, 2) ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <!-- TAB 2: Service Bills -->
        <div id="mtab_bills" class="tab-content" style="padding: 22px; overflow-y: auto; flex: 1;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                <h4 style="margin:0; font-size:15px; font-weight:700; text-transform:uppercase; color:var(--t-secondary);">Service Bills History</h4>
                <button type="button" class="sf-btn success" onclick="openModal('addBillModal')">
                    <i class="fa-solid fa-plus"></i> Record Service Bill
                </button>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Bill Date</th>
                        <th>Bill Number</th>
                        <th>Service Period</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th class="num-col">Total Amount</th>
                        <th class="num-col">Balance Due</th>
                        <th style="width: 100px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($data['bills'])): ?>
                        <tr><td colspan="8" style="text-align: center; color: var(--t-secondary); padding: 25px;">No service bills recorded.</td></tr>
                    <?php else: foreach($data['bills'] as $b): ?>
                        <tr>
                            <td style="color:var(--t-secondary); font-size:12px;"><?= date('M d, Y', strtotime($b->grn_date)) ?></td>
                            <td><strong style="color: var(--c-blue); font-family: var(--f-mono);"><?= htmlspecialchars($b->grn_number) ?></strong></td>
                            <td><?= htmlspecialchars($b->service_period ?: 'N/A') ?></td>
                            <td style="color:var(--c-red); font-size:12px; font-weight:500;"><?= $b->due_date ? date('M d, Y', strtotime($b->due_date)) : 'N/A' ?></td>
                            <td>
                                <span class="sf-badge badge-<?= str_replace(' ', '', $b->status ?: 'Unpaid') ?>">
                                    <span class="dot"></span><?= htmlspecialchars($b->status ?: 'Unpaid') ?>
                                </span>
                            </td>
                            <td class="num-col" style="font-weight:700; font-family:var(--f-mono);">Rs: <?= number_format($b->total_amount, 2) ?></td>
                            <td class="num-col" style="font-weight:700; font-family:var(--f-mono); color: <?= $b->balance_due > 0 ? 'var(--c-red)' : 'var(--c-green)' ?>;">Rs: <?= number_format($b->balance_due, 2) ?></td>
                            <td style="text-align: center;">
                                <a href="<?= APP_URL ?>/serviceprovider/bill/<?= $b->id ?>" target="_blank" class="sf-btn neutral sf-btn-small" style="padding: 4px 10px; font-size: 11px;">
                                    View Details ↗
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <!-- TAB 3: Profile & Settings -->
        <div id="mtab_profile" class="tab-content" style="padding: 22px; overflow-y: auto; flex: 1;">
            <h4 style="margin:0 0 16px 0; border-bottom: 0.5px solid var(--c-separator); padding-bottom: 8px; font-size:14px; font-weight:700; text-transform:uppercase; color:var(--t-secondary);">
                Update Service Provider Details
            </h4>
            
            <form action="<?= APP_URL ?>/serviceprovider/index/<?= $sup->id ?>" method="POST" style="max-width: 650px;">
                <input type="hidden" name="action" value="update_service_provider">
                <input type="hidden" name="service_provider_id" value="<?= $sup->id ?>">
                
                <div class="grid-2">
                    <div class="sf-group">
                        <label>Provider / Company Name *</label>
                        <input type="text" name="name" class="sf-input" value="<?= htmlspecialchars($sup->name) ?>" required>
                    </div>
                    <div class="sf-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="sf-input" value="<?= htmlspecialchars($sup->email) ?>">
                    </div>
                </div>

                <div class="grid-2">
                    <div class="sf-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" class="sf-input" value="<?= htmlspecialchars($sup->phone) ?>">
                    </div>
                    <div class="sf-group">
                        <label>Service Category</label>
                        <?php
                        $categories = [
                            'Telephone', 'Internet', 'Electricity', 'Water', 'Mobile',
                            'Insurance', 'Courier', 'Cloud Services', 'Software Subscription',
                            'Maintenance', 'Security', 'Rent'
                        ];
                        $currentCat = $sup->service_category;
                        $isOther = !empty($currentCat) && !in_array($currentCat, $categories);
                        ?>
                        <select name="service_category_select" class="sf-input" onchange="toggleCustomCategory(this, 'modalEditCustomCategoryContainer')">
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat ?>" <?= $currentCat == $cat ? 'selected' : '' ?>><?= $cat ?></option>
                            <?php endforeach; ?>
                            <option value="Other" <?= $isOther ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>
                </div>

                <div class="sf-group" id="modalEditCustomCategoryContainer" style="display: <?= $isOther ? 'block' : 'none' ?>;">
                    <label>Custom Category Name *</label>
                    <input type="text" name="service_category_custom" class="sf-input" value="<?= $isOther ? htmlspecialchars($currentCat) : '' ?>">
                </div>

                <div class="grid-2">
                    <div class="sf-group">
                        <label>Status</label>
                        <select name="status" class="sf-input">
                            <option value="Active" <?= $sup->status == 'Active' ? 'selected' : '' ?>>Active</option>
                            <option value="Inactive" <?= $sup->status == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="sf-group">
                        <label>Physical Address</label>
                        <textarea name="address" class="sf-input" rows="2" style="resize:vertical; min-height:50px;"><?= htmlspecialchars($sup->address) ?></textarea>
                    </div>
                </div>
                
                <div style="text-align: right; margin-top: 14px;">
                    <button type="submit" class="sf-btn primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<!-- ============================================================
     ADD SERVICE PROVIDER MODAL
     ============================================================ -->
<div class="modal-veil hidden" id="addProviderModal" onclick="if(event.target === this) closeModal('addProviderModal')">
    <div class="sf-modal" style="width: 500px;">
        <div class="modal-head">
            <h3 class="modal-title">Add New Service Provider</h3>
            <button type="button" class="modal-close" onclick="closeModal('addProviderModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="<?= APP_URL ?>/serviceprovider" method="POST">
            <div class="modal-body">
                <input type="hidden" name="action" value="add_service_provider">
                
                <div class="sf-group">
                    <label>Provider / Company Name *</label>
                    <input type="text" name="name" class="sf-input" required placeholder="e.g. Ceylon Electricity Board">
                </div>
                
                <div class="grid-2">
                    <div class="sf-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="sf-input" placeholder="billing@provider.lk">
                    </div>
                    <div class="sf-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" class="sf-input" placeholder="0112345678">
                    </div>
                </div>
                
                <div class="sf-group">
                    <label>Service Category</label>
                    <select name="service_category_select" class="sf-input" onchange="toggleCustomCategory(this, 'addCustomCategoryContainer')">
                        <option value="">Select Category</option>
                        <option value="Telephone">Telephone</option>
                        <option value="Internet">Internet</option>
                        <option value="Electricity">Electricity</option>
                        <option value="Water">Water</option>
                        <option value="Mobile">Mobile</option>
                        <option value="Insurance">Insurance</option>
                        <option value="Courier">Courier</option>
                        <option value="Cloud Services">Cloud Services</option>
                        <option value="Software Subscription">Software Subscription</option>
                        <option value="Maintenance">Maintenance</option>
                        <option value="Security">Security</option>
                        <option value="Rent">Rent</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="sf-group" id="addCustomCategoryContainer" style="display: none;">
                    <label>Custom Category Name *</label>
                    <input type="text" name="service_category_custom" class="sf-input" placeholder="Enter category...">
                </div>

                <div class="sf-group">
                    <label>Status</label>
                    <select name="status" class="sf-input">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
                
                <div class="sf-group">
                    <label>Address</label>
                    <textarea name="address" class="sf-input" rows="2" style="resize:vertical;" placeholder="Physical address..."></textarea>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="sf-btn neutral" onclick="closeModal('addProviderModal')">Cancel</button>
                <button type="submit" class="sf-btn primary">Register Provider</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================
     ADD SERVICE BILL MODAL
     ============================================================ -->
<?php if ($data['selected_service_provider']): ?>
<div class="modal-veil hidden" id="addBillModal" onclick="if(event.target === this) closeModal('addBillModal')">
    <div class="sf-modal" style="width: 560px;">
        <div class="modal-head">
            <h3 class="modal-title">Record Service Bill</h3>
            <button type="button" class="modal-close" onclick="closeModal('addBillModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="<?= APP_URL ?>/serviceprovider/index/<?= $data['selected_service_provider']->id ?>" method="POST" enctype="multipart/form-data">
            <div class="modal-body" style="max-height: 75vh; overflow-y: auto;">
                <input type="hidden" name="action" value="add_service_bill">
                <input type="hidden" name="service_provider_id" value="<?= $data['selected_service_provider']->id ?>">
                <input type="hidden" name="ap_account_id" value="<?= $data['ap_account'] ? $data['ap_account']->id : '' ?>">
                
                <div class="grid-2">
                    <div class="sf-group">
                        <label>Bill Number (System Generated) *</label>
                        <input type="text" name="bill_number" class="sf-input" value="<?= htmlspecialchars($data['next_bill_number']) ?>" readonly style="background:var(--c-fill2); font-weight:700;">
                    </div>
                    <div class="sf-group">
                        <label>Supplier Invoice/Receipt #</label>
                        <input type="text" name="receipt_number" class="sf-input" placeholder="e.g. INV-12345">
                    </div>
                </div>
                
                <div class="sf-group">
                    <label>Service Period</label>
                    <input type="text" name="service_period" class="sf-input" placeholder="e.g. July 2026">
                </div>
                
                <div class="grid-2">
                    <div class="sf-group">
                        <label>Bill Date *</label>
                        <input type="date" name="bill_date" class="sf-input" required value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="sf-group">
                        <label>Due Date *</label>
                        <input type="date" name="due_date" class="sf-input" required value="<?= date('Y-m-d', strtotime('+14 days')) ?>">
                    </div>
                </div>
                
                <div class="grid-3">
                    <div class="sf-group">
                        <label>Subtotal (Rs) *</label>
                        <input type="number" step="0.01" id="bill_amount" name="amount" class="sf-input" required oninput="calculateBillTotal()" placeholder="0.00">
                    </div>
                    <div class="sf-group">
                        <label>Tax (Rs)</label>
                        <input type="number" step="0.01" id="bill_tax" name="tax" class="sf-input" oninput="calculateBillTotal()" placeholder="0.00" value="0.00">
                    </div>
                    <div class="sf-group">
                        <label>Total Amount</label>
                        <input type="number" step="0.01" id="bill_total" name="total_amount" class="sf-input" readonly placeholder="0.00" style="background:var(--c-fill2); font-weight:700; color:var(--c-blue);">
                    </div>
                </div>
                
                <div class="grid-2">
                    <div class="sf-group">
                        <label>Expense Account *</label>
                        <select name="expense_account_id" class="sf-input" required>
                            <option value="">-- Select Expense --</option>
                            <?php foreach($data['expenses'] as $acc): ?>
                                <option value="<?= $acc->id ?>" <?= (!empty($data['selected_service_provider']->expense_account_id) && $data['selected_service_provider']->expense_account_id == $acc->id) ? 'selected' : '' ?>><?= htmlspecialchars($acc->account_code . ' - ' . $acc->account_name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="sf-group">
                        <label>Accounts Payable *</label>
                        <input type="text" class="sf-input" readonly value="<?= $data['ap_account'] ? htmlspecialchars($data['ap_account']->account_code . ' - ' . $data['ap_account']->account_name) : 'Accounts Payable Not Configured' ?>" style="background:var(--c-fill2);">
                    </div>
                </div>
                
                <div class="sf-group">
                    <label>Upload Bill / Invoice (Optional)</label>
                    <input type="file" name="attachment" class="sf-input">
                </div>
                
                <div class="sf-group">
                    <label>Memo / Notes</label>
                    <textarea name="notes" class="sf-input" rows="2" placeholder="Enter notes..."></textarea>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="sf-btn neutral" onclick="closeModal('addBillModal')">Cancel</button>
                <button type="submit" class="sf-btn success">Record Bill</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script>
console.log("--- SERVICE PROVIDER SCRIPT INITIALIZING ---");
try {
    window.allProviders = [];
    window.filteredProviders = [];
    window.currentPage = 1;
    window.pageSize = 15;

    document.addEventListener("DOMContentLoaded", function() {
        const rows = document.querySelectorAll('#spList .provider-row');
        rows.forEach(row => {
            window.allProviders.push({
                element: row,
                name: row.getAttribute('data-name'),
                category: row.getAttribute('data-category'),
                phone: row.getAttribute('data-phone'),
                email: row.getAttribute('data-email'),
                outstanding: parseFloat(row.getAttribute('data-outstanding'))
            });
        });
        window.filteredProviders = [...window.allProviders];
        window.renderPagination();
        
        const pathParts = window.location.pathname.split('/');
        const idIndex = pathParts.indexOf('index');
        let autoLoadId = null;
        if (idIndex !== -1 && pathParts.length > idIndex + 1) {
            autoLoadId = pathParts[idIndex + 1];
        } else {
            const urlParams = new URLSearchParams(window.location.search);
            autoLoadId = urlParams.get('service_provider_id');
        }
        
        if (autoLoadId && !isNaN(autoLoadId)) {
            const tab = new URLSearchParams(window.location.search).get('tab');
            window.openServiceProviderModalPopup(autoLoadId, tab);
        }
    });

    window.filterStatusValue = '';
    window.filterCategoryValue = '';
    
    window.filterList = function() {
        const query = document.getElementById('searchInput').value.toLowerCase();
        
        window.filteredProviders = window.allProviders.filter(c => {
            const matchQuery = c.name.includes(query) || c.category.includes(query) || c.phone.includes(query) || c.email.includes(query);
            let matchStatus = true;
            let matchCat = true;
            
            if (window.filterStatusValue === 'owed') matchStatus = (c.outstanding > 0);
            else if (window.filterStatusValue === 'cleared') matchStatus = (c.outstanding <= 0);
            
            if (window.filterCategoryValue !== '') {
                matchCat = c.category.includes(window.filterCategoryValue.toLowerCase());
            }

            return matchQuery && matchStatus && matchCat;
        });
        
        window.currentPage = 1;
        window.renderPagination();
        document.getElementById('matching-count').innerText = window.filteredProviders.length;
    }

    window.selectStatus = function(val, label) {
        document.getElementById('filterStatus').value = val;
        document.getElementById('status-dropdown-val').innerText = label;
        window.filterStatusValue = val;
        window.filterList();
        document.activeElement.blur();
    }

    window.selectCategory = function(val, label) {
        document.getElementById('filterCategory').value = val;
        document.getElementById('category-dropdown-val').innerText = label;
        window.filterCategoryValue = val;
        window.filterList();
        document.activeElement.blur();
    }

    window.clearAllFilters = function() {
        document.getElementById('searchInput').value = '';
        window.selectStatus('', 'All Accounts');
        window.selectCategory('', 'All Categories');
    }

    window.renderPagination = function() {
        window.allProviders.forEach(c => c.element.style.display = 'none');
        
        if (window.filteredProviders.length === 0) {
            document.getElementById('pg-info-text').innerHTML = "No matching service providers";
            document.getElementById('pg-current-text').innerText = "0 / 0";
            document.getElementById('pg-prev-btn').disabled = true;
            document.getElementById('pg-next-btn').disabled = true;
            return;
        }
        
        let totalPages = Math.ceil(window.filteredProviders.length / window.pageSize);
        if (window.currentPage > totalPages) window.currentPage = totalPages;
        if (window.currentPage < 1) window.currentPage = 1;
        
        let startIdx = (window.currentPage - 1) * window.pageSize;
        let endIdx = startIdx + window.pageSize;
        
        for (let i = startIdx; i < endIdx && i < window.filteredProviders.length; i++) {
            window.filteredProviders[i].element.style.display = 'table-row';
        }
        
        let showingEnd = Math.min(endIdx, window.filteredProviders.length);
        document.getElementById('pg-info-text').innerHTML = `Showing <strong>${startIdx + 1}</strong> – <strong>${showingEnd}</strong> of <strong>${window.filteredProviders.length}</strong>`;
        document.getElementById('pg-current-text').innerText = `${window.currentPage} / ${totalPages}`;
        
        document.getElementById('pg-prev-btn').disabled = (window.currentPage === 1);
        document.getElementById('pg-next-btn').disabled = (window.currentPage === totalPages);
    }
    
    window.navigatePage = function(page) {
        let totalPages = Math.ceil(window.filteredProviders.length / window.pageSize);
        if (page >= 1 && page <= totalPages) {
            window.currentPage = page;
            window.renderPagination();
        }
    }
    
    window.updatePageSize = function(size) {
        if (size === '1000') { window.pageSize = 999999; } else { window.pageSize = parseInt(size); }
        window.currentPage = 1;
        window.renderPagination();
    }

    window.openModal = function(id) { 
        const m = document.getElementById(id);
        if (m) m.classList.remove('hidden'); 
    }
    window.closeModal = function(id) { 
        const m = document.getElementById(id);
        if (m) m.classList.add('hidden'); 
    }

    window.openServiceProviderModalPopup = function(id, tab = null) {
        const modal = document.getElementById('serviceProviderProfileModal');
        const loader = document.getElementById('modal-loader');
        const content = document.getElementById('modal-profile-content');
        
        modal.classList.remove('hidden');
        loader.style.display = 'flex';
        content.style.display = 'none';
        
        let targetUrl = '<?= APP_URL ?>/serviceprovider/index/' + id;
        if (tab) { targetUrl += '?tab=' + tab; }
        window.history.pushState({ path: targetUrl }, '', targetUrl);
        
        fetch(targetUrl)
            .then(response => {
                if (!response.ok) throw new Error('Failed to load provider profile');
                return response.text();
            })
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                const newContent = doc.getElementById('modal-profile-content-source');
                const newHeader = doc.getElementById('modal-header-source');
                
                if (newContent && newHeader) {
                    content.innerHTML = newContent.innerHTML;
                    document.getElementById('modal-header-container').innerHTML = newHeader.innerHTML;
                    loader.style.display = 'none';
                    content.style.display = 'flex';
                    
                    if (tab) { window.switchModalTab(tab); } 
                    else { window.switchModalTab('ledger'); }
                } else {
                    throw new Error('Profile layout mismatch');
                }
            })
            .catch(err => {
                console.error(err);
                loader.style.display = 'none';
                content.innerHTML = `<div style="padding:40px; text-align:center; color:var(--c-red); font-weight:600;"><i class="fa-solid fa-triangle-exclamation" style="font-size:24px; margin-bottom:10px; display:block;"></i>Failed to load provider profile data.</div>`;
                content.style.display = 'block';
            });
    }

    window.closeServiceProviderProfile = function() {
        document.getElementById('serviceProviderProfileModal').classList.add('hidden');
        window.history.pushState({ path: '<?= APP_URL ?>/serviceprovider' }, '', '<?= APP_URL ?>/serviceprovider');
    }

    window.switchModalTab = function(tabName) {
        const content = document.getElementById('modal-profile-content');
        content.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
        content.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        
        const tabEl = content.querySelector('#mtab_' + tabName);
        const btnEl = content.querySelector('#mbtn_' + tabName);
        
        if (tabEl) tabEl.style.display = 'block';
        if (btnEl) btnEl.classList.add('active');
        
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('tab', tabName);
        const newUrl = window.location.pathname + '?' + urlParams.toString();
        window.history.replaceState({ path: newUrl }, '', newUrl);
    }

    window.toggleCustomCategory = function(selectEl, containerId) {
        const container = document.getElementById(containerId);
        if (container) {
            if (selectEl.value === 'Other') {
                container.style.display = 'block';
                const inp = container.querySelector('input');
                if (inp) inp.setAttribute('required', 'required');
            } else {
                container.style.display = 'none';
                const inp = container.querySelector('input');
                if (inp) {
                    inp.removeAttribute('required');
                    inp.value = '';
                }
            }
        }
    }

    window.calculateBillTotal = function() {
        const amt = parseFloat(document.getElementById('bill_amount').value) || 0;
        const tax = parseFloat(document.getElementById('bill_tax').value) || 0;
        const totEl = document.getElementById('bill_total');
        if (totEl) totEl.value = (amt + tax).toFixed(2);
    }

} catch(e) {
    console.error("--- SERVICE PROVIDER SCRIPT FAILED ---", e);
}
</script>
