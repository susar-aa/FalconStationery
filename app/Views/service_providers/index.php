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
$csrfToken = $data['csrf_token'] ?? ($_SESSION['csrf_token'] ?? '');
?>

<script>
document.body.classList.add('erp-locked');
</script>

<!-- FontAwesome 6 Icons Guarantee -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
/* ============================================================
   FALCON STATIONERY ERP: SERVICE PROVIDER CENTER ARCHITECTURE
   SF PRO DISPLAY + VIEWPORT-LOCKED ERP STANDARDS (RULES 1, 2, 3)
   ============================================================ */

@font-face {
    font-family: 'SF Pro Display';
    src: url('<?= APP_URL ?>/public/assets/fonts/sf_pro_display_regular.ttf') format('truetype');
    font-weight: 400;
}
@font-face {
    font-family: 'SF Pro Display';
    src: url('<?= APP_URL ?>/public/assets/fonts/sf_pro_display_medium.ttf') format('truetype');
    font-weight: 500;
}
@font-face {
    font-family: 'SF Pro Display';
    src: url('<?= APP_URL ?>/public/assets/fonts/sf_pro_display_semibold.ttf') format('truetype');
    font-weight: 600;
}
@font-face {
    font-family: 'SF Pro Display';
    src: url('<?= APP_URL ?>/public/assets/fonts/sf_pro_display_bold.ttf') format('truetype');
    font-weight: 700;
}

:root {
    --erp-bg: #f8fafc;
    --erp-card: #ffffff;
    --erp-border: #e2e8f0;
    --erp-text: #0f172a;
    --erp-muted: #64748b;
    --erp-font: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    --erp-mono: 'SF Mono', ui-monospace, Menlo, Monaco, Consolas, monospace;
}

html, body {
    height: 100vh !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: hidden !important;
    background: var(--erp-bg, #f8fafc);
    font-family: var(--erp-font);
    -webkit-font-smoothing: antialiased;
}

body.erp-locked .app-container,
body:has(.erp-viewport-view) .app-container,
.app-container {
    margin-top: 70px !important;
    height: calc(100vh - 70px) !important;
    overflow: hidden !important;
    display: flex !important;
    flex-direction: column !important;
}

body.erp-locked .main-content,
body:has(.erp-viewport-view) .main-content,
.main-content {
    padding: 8px 16px 8px 16px !important;
    overflow: hidden !important;
    display: flex !important;
    flex-direction: column !important;
    height: 100% !important;
    min-height: 0 !important;
    flex: 1 !important;
}

.erp-viewport-view.sp-erp-workspace {
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 0;
    max-height: 100%;
    flex: 1;
    overflow: hidden;
    gap: 6px;
    box-sizing: border-box;
    position: relative;
    padding: 0;
}

/* 1. Sleek Compact KPI Stats Strip */
.erp-stats-strip {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    flex-shrink: 0;
}

.stat-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 9999px;
    padding: 3px 12px;
    font-size: 12px;
    color: #475569;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
}
.stat-pill strong {
    color: #0f172a;
    font-weight: 700;
    font-family: var(--erp-mono);
}
.badge-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    flex-shrink: 0;
}

/* 2. Top-Right Clean Filter Bar Above Table (RULE 2) */
.erp-filter-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 5px 14px;
    flex-shrink: 0;
    gap: 12px;
}

.erp-filter-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

.erp-results-badge {
    font-size: 12px;
    color: #64748b;
    white-space: nowrap;
    font-weight: 500;
}
.erp-results-badge strong {
    color: #0f172a;
    font-family: var(--erp-font);
    font-weight: 700;
}

.erp-filter-right {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: nowrap;
    justify-content: flex-end;
}

.erp-filter-control {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
}

.erp-filter-label {
    font-size: 10.5px;
    font-weight: 700;
    color: #475569;
    white-space: nowrap;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.erp-select {
    height: 28px;
    max-width: 165px;
    padding: 0 24px 0 10px;
    font-size: 12px;
    font-weight: 500;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    background: #ffffff;
    color: #1e293b;
    outline: none;
    font-family: var(--erp-font);
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 7px center;
    background-size: 10px;
    transition: border-color 0.15s, box-shadow 0.15s;
    text-overflow: ellipsis;
}
.erp-select:hover {
    border-color: #94a3b8;
}
.erp-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.12);
}

.erp-btn-reset {
    height: 28px;
    padding: 0 11px;
    font-size: 12px;
    font-weight: 600;
    color: #475569;
    background: #ffffff;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.15s;
}
.erp-btn-reset:hover {
    background: #f8fafc;
    color: #0f172a;
    border-color: #94a3b8;
}

/* 3. Table Card with Dedicated Independent Scroll (RULE 1 & 3) */
.erp-card-table {
    flex: 1;
    min-height: 0;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    position: relative;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

.erp-table-scroll {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: auto;
    position: relative;
    padding-bottom: 54px; /* Clearance inside table scroll so bottom rows clear the floating island */
}

.erp-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12.5px;
    text-align: left;
}

.erp-table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    background: #f8fafc;
    color: #475569;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    padding: 8px 14px;
    border-bottom: 1px solid #e2e8f0;
    white-space: nowrap;
}

.erp-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background-color 0.12s ease;
    cursor: pointer;
}

/* MANDATORY ZEBRA STRIPING (RULE 3) */
.erp-table tbody tr:nth-child(even) {
    background-color: #f8fafc;
}
.erp-table tbody tr:hover {
    background-color: #f1f5f9;
}

.erp-table td {
    padding: 8px 14px;
    vertical-align: middle;
    color: #1e293b;
}

.txt-right { text-align: right !important; }
.txt-center { text-align: center !important; }

/* Status Badges */
.sf-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 7px;
    border-radius: 4px;
    font-size: 10.5px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}
.badge-active { background: #dcfce7; color: #15803d; }
.badge-owed   { background: #fee2e2; color: #b91c1c; }
.badge-inactive { background: #f1f5f9; color: #64748b; }
.badge-dot-sm { width: 5px; height: 5px; border-radius: 50%; background: currentColor; }

.avatar-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #e0f2fe;
    color: #0284c7;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 700;
    flex-shrink: 0;
}

/* Interactive iOS Toggle Switch (.erp-switch) */
.erp-switch {
    position: relative;
    display: inline-block;
    width: 34px;
    height: 19px;
    vertical-align: middle;
    margin-right: 6px;
    cursor: pointer;
}
.erp-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
.erp-slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #cbd5e1;
    transition: .25s ease;
    border-radius: 20px;
}
.erp-slider:before {
    position: absolute;
    content: "";
    height: 15px;
    width: 15px;
    left: 2px;
    bottom: 2px;
    background-color: white;
    transition: .25s ease;
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}
.erp-switch input:checked + .erp-slider {
    background-color: #10b981;
}
.erp-switch input:checked + .erp-slider:before {
    transform: translateX(15px);
}
.status-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}
.status-active { color: #15803d; }
.status-inactive { color: #64748b; }

/* Action Buttons */
.act-btn {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    cursor: pointer;
    font-size: 12px;
    color: #3b82f6;
    transition: all 0.15s;
}
.act-btn:hover {
    background: #eff6ff;
    border-color: #bfdbfe;
    color: #1d4ed8;
}

/* Pinned Table Pagination Footer (RULE 1) */
.erp-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 7px 16px;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    flex-shrink: 0;
    font-size: 12px;
    color: #64748b;
    z-index: 10;
}
.pg-info strong {
    color: #0f172a;
    font-weight: 700;
}
.pg-right {
    display: flex;
    align-items: center;
    gap: 14px;
}
.pg-size-wrap {
    display: flex;
    align-items: center;
    gap: 6px;
}
.pg-size-lbl {
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
}
.pg-size-sel {
    font-family: var(--erp-font);
    font-size: 11.5px;
    font-weight: 600;
    color: #1e293b;
    background: #f8fafc;
    border: 1px solid #d1d5db;
    border-radius: 5px;
    padding: 3px 6px;
    outline: none;
    cursor: pointer;
}
.pg-nav {
    display: flex;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    overflow: hidden;
}
.pg-btn {
    width: 28px;
    height: 26px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #ffffff;
    border: none;
    cursor: pointer;
    color: #475569;
    font-size: 11px;
    transition: background 0.12s;
}
.pg-btn:hover:not(:disabled) {
    background: #f1f5f9;
    color: #0f172a;
}
.pg-btn:disabled {
    opacity: 0.35;
    cursor: not-allowed;
}
.pg-btn + .pg-btn {
    border-left: 1px solid #e2e8f0;
}
.pg-current {
    padding: 0 10px;
    display: flex;
    align-items: center;
    font-size: 11.5px;
    font-weight: 600;
    color: #0f172a;
    background: #ffffff;
    border-left: 1px solid #e2e8f0;
    border-right: 1px solid #e2e8f0;
}

/* 4. Sleek Floating Island Command Bar Pill (RULE 3) */
.cmd-bar {
    position: fixed;
    bottom: 24px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(15, 23, 42, 0.95);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 9999px;
    padding: 6px 8px;
    display: flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.35);
    z-index: 100;
}

.cmd-search {
    display: flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.08);
    border-radius: 9999px;
    padding: 6px 14px;
    width: 320px;
}
.cmd-search i {
    color: rgba(255, 255, 255, 0.5);
    font-size: 13px;
    flex-shrink: 0;
}
.cmd-search input {
    background: transparent;
    border: none;
    outline: none;
    color: #ffffff;
    font-size: 13px;
    font-weight: 500;
    font-family: var(--erp-font);
    width: 100%;
}
.cmd-search input::placeholder {
    color: rgba(255, 255, 255, 0.45);
}

.cmd-divider {
    width: 1px;
    height: 20px;
    background: rgba(255, 255, 255, 0.15);
    margin: 0 2px;
}

.cmd-cta {
    display: flex;
    align-items: center;
    gap: 6px;
    background: #ffffff;
    color: #0f172a;
    border: none;
    border-radius: 9999px;
    padding: 0 16px;
    height: 32px;
    font-size: 13px;
    font-weight: 700;
    font-family: var(--erp-font);
    cursor: pointer;
    transition: all 0.15s;
    white-space: nowrap;
}
.cmd-cta:hover {
    background: #f1f5f9;
    transform: translateY(-1px);
}

/* Modals & Dialogs */
.modal-veil {
    position: fixed;
    inset: 0;
    z-index: 2000;
    background: rgba(15, 23, 42, 0.5);
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: opacity 0.2s ease;
}
.modal-veil.hidden { display: none !important; }

.sf-modal {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.18);
    width: 520px;
    max-width: 95vw;
    animation: sfModalSlide 0.22s cubic-bezier(0.16, 1, 0.3, 1);
    overflow: hidden;
}
@keyframes sfModalSlide {
    from { transform: translateY(12px) scale(0.98); opacity: 0; }
    to { transform: translateY(0) scale(1); opacity: 1; }
}

.modal-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 20px;
    border-bottom: 1px solid #e2e8f0;
    background: #f8fafc;
}
.modal-title {
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}
.modal-close {
    background: #e2e8f0;
    border: none;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    cursor: pointer;
    transition: all 0.15s;
}
.modal-close:hover {
    background: #cbd5e1;
    color: #0f172a;
}
.modal-body { padding: 20px; }
.modal-foot {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    padding: 12px 20px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
}

.sf-group { margin-bottom: 14px; }
.sf-group label {
    display: block;
    margin-bottom: 5px;
    font-size: 11.5px;
    font-weight: 600;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}
.sf-input {
    width: 100%;
    padding: 8px 12px;
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #0f172a;
    font-size: 13px;
    outline: none;
    box-sizing: border-box;
    font-family: var(--erp-font);
}
.sf-input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15);
}
.grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; }

/* Tabs inside Modal */
.tabs {
    display: flex;
    border-bottom: 1px solid #e2e8f0;
    background: #f8fafc;
    padding: 0 20px;
}
.tab-btn {
    padding: 10px 16px;
    border: none;
    background: transparent;
    cursor: pointer;
    font-size: 12.5px;
    font-weight: 600;
    color: #64748b;
    border-bottom: 2px solid transparent;
    transition: all 0.15s;
    font-family: var(--erp-font);
}
.tab-btn:hover { color: #3b82f6; }
.tab-btn.active { color: #3b82f6; border-bottom-color: #3b82f6; }
.tab-content { display: none; }
.tab-content.active { display: block; }

/* MANDATORY ZEBRA STRIPING IN MODAL TABLES (RULE 3) */
.data-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 6px;
    font-size: 12px;
}
.data-table thead th {
    position: sticky;
    top: 0;
    z-index: 5;
    background: #f8fafc;
    padding: 8px 12px;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
    color: #475569;
    font-weight: 700;
    font-size: 10.5px;
    text-transform: uppercase;
}
.data-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
}
.data-table tbody tr:nth-child(even) {
    background-color: #f8fafc;
}
.data-table tbody tr:hover {
    background-color: #f1f5f9;
}
.data-table td {
    padding: 8px 12px;
    vertical-align: middle;
}
.num-col { text-align: right !important; }

/* Buttons */
.sf-btn {
    padding: 7px 14px;
    border-radius: 6px;
    font-size: 12.5px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.15s;
    text-decoration: none;
    font-family: var(--erp-font);
}
.sf-btn.primary { background: #3b82f6; color: #ffffff; }
.sf-btn.primary:hover { background: #2563eb; }
.sf-btn.success { background: #10b981; color: #ffffff; }
.sf-btn.success:hover { background: #059669; }
.sf-btn.neutral { background: #ffffff; border-color: #cbd5e1; color: #334155; }
.sf-btn.neutral:hover { background: #f8fafc; color: #0f172a; }

/* Alerts */
.sf-alert {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    background: #ffffff;
    border-radius: 6px;
    padding: 10px 14px;
    margin-bottom: 8px;
    border: 1px solid #e2e8f0;
    border-left-width: 4px;
    font-size: 12.5px;
}
.sf-alert.success { border-left-color: #10b981; }
.sf-alert.error   { border-left-color: #ef4444; }
.sf-alert-icon { font-size: 15px; flex-shrink: 0; margin-top: 2px; }
.sf-alert.success .sf-alert-icon { color: #10b981; }
.sf-alert.error   .sf-alert-icon { color: #ef4444; }
.sf-alert-title { font-weight: 700; color: #0f172a; margin-bottom: 2px; }
.sf-alert-msg   { color: #475569; font-size: 12px; }
.sf-alert-close {
    margin-left: auto;
    flex-shrink: 0;
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    font-size: 13px;
    padding: 2px;
}
.sf-alert-close:hover { color: #475569; }

@keyframes spin { to { transform: rotate(360deg); } }
.spin { animation: spin 0.7s linear infinite; display: inline-block; }
.hidden { display: none !important; }
</style>

<div class="erp-viewport-view sp-erp-workspace">

    <!-- 1. Sleek Compact KPI Stats Strip -->
    <div class="erp-stats-strip">
        <div class="stat-pill">
            <span class="badge-dot" style="background:#3b82f6;"></span>
            Total Service Providers: <strong><?= number_format($totalProviders) ?></strong>
        </div>
        <div class="stat-pill">
            <span class="badge-dot" style="background:#f59e0b;"></span>
            Total Outstanding: <strong>Rs. <?= number_format($totalOutstanding, 2) ?></strong>
        </div>
        <div class="stat-pill">
            <span class="badge-dot" style="background:#ef4444;"></span>
            Accounts to Settle: <strong><?= number_format($owedProvidersCount) ?></strong>
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

    <!-- 2. Top-Right Clean Filter Bar Above Table (RULE 2) -->
    <div class="erp-filter-bar">
        <div class="erp-filter-left">
            <span class="erp-results-badge">Showing <strong id="matching-count"><?= count($data['service_providers'] ?? []) ?></strong> service providers</span>
        </div>
        <div class="erp-filter-right">
            <div class="erp-filter-control">
                <span class="erp-filter-label">Category:</span>
                <select id="filterCategory" class="erp-select" onchange="filterList()">
                    <option value="">All Categories</option>
                    <?php
                    $categories = [
                        'Telephone', 'Internet', 'Electricity', 'Water', 'Mobile',
                        'Insurance', 'Courier', 'Cloud Services', 'Software Subscription',
                        'Maintenance', 'Security', 'Rent', 'Other'
                    ];
                    foreach($categories as $cat):
                    ?>
                        <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="erp-filter-control">
                <span class="erp-filter-label">Ledger:</span>
                <select id="filterStatus" class="erp-select" onchange="filterList()">
                    <option value="">All Accounts</option>
                    <option value="owed">Has Unpaid Balance</option>
                    <option value="cleared">Zero Balance</option>
                </select>
            </div>
            <div class="erp-filter-control">
                <span class="erp-filter-label">Status:</span>
                <select id="filterAccountStatus" class="erp-select" onchange="filterList()">
                    <option value="">All Statuses</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <button type="button" onclick="clearAllFilters()" class="erp-btn-reset">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </button>
        </div>
    </div>

    <!-- 3. Table Card with Dedicated Independent Scroll (RULE 1 & 3) -->
    <div class="erp-card-table">
        <div class="erp-table-scroll">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th>Service Provider / Company</th>
                        <th>Category & Contact</th>
                        <th class="txt-right">Outstanding Balance</th>
                        <th>Ledger Status</th>
                        <th>Account Status</th>
                        <th style="width:100px;" class="txt-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="spList">
                    <?php if (empty($data['service_providers'])): ?>
                        <tr>
                            <td colspan="6" style="text-align:center; padding:40px; color:#64748b;">
                                <i class="fa-solid fa-bolt" style="font-size:28px; margin-bottom:8px; color:#cbd5e1;"></i><br>
                                No service providers registered yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($data['service_providers'] as $s): ?>
                            <?php 
                            $bal = floatval($s->outstanding_balance); 
                            $badgeCls = $bal > 0 ? 'badge-owed' : 'badge-active';
                            $badgeTxt = $bal > 0 ? 'Owed' : 'Cleared';
                            $accountStatus = $s->status ?? 'Active';
                            ?>
                            <tr class="provider-row" 
                                onclick="openServiceProviderModalPopup(<?= $s->id ?>)"
                                data-id="<?= $s->id ?>"
                                data-name="<?= htmlspecialchars(strtolower($s->name ?? '')) ?>"
                                data-category="<?= htmlspecialchars(strtolower($s->service_category ?? 'general')) ?>"
                                data-phone="<?= htmlspecialchars(strtolower($s->phone ?? '')) ?>"
                                data-email="<?= htmlspecialchars(strtolower($s->email ?? '')) ?>"
                                data-status="<?= htmlspecialchars($accountStatus) ?>"
                                data-outstanding="<?= $bal ?>">
                                
                                <td>
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <div class="avatar-circle"><?= strtoupper(substr($s->name ?? '', 0, 2)) ?></div>
                                        <div>
                                            <strong style="font-size:13px; font-weight:600; color:#0f172a;"><?= htmlspecialchars($s->name ?? '') ?></strong>
                                            <span style="font-size:11px; color:#64748b; display:block; margin-top:1px;">
                                                <?= !empty($s->address) ? htmlspecialchars($s->address) : '<span style="color:#ef4444; font-weight:600;">Missing Address</span>' ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size:11.5px; font-weight:600; color:#8b5cf6; margin-bottom:1px;">
                                        ⚡ <?= htmlspecialchars($s->service_category ?: 'General Service') ?>
                                    </div>
                                    <span style="font-size:12px; font-weight:500; display:block;">
                                        📞 <?= !empty($s->phone) ? htmlspecialchars($s->phone) : '<span style="color:#ef4444; font-weight:600;">Missing Phone</span>' ?>
                                    </span>
                                    <?php if (!empty($s->email)): ?>
                                        <span style="font-size:11px; color:#64748b; display:block; margin-top:1px;">
                                            ✉️ <?= htmlspecialchars($s->email) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="txt-right" style="font-weight:700; font-family:var(--erp-mono); font-size:13px; color: <?= $bal > 0 ? '#ef4444' : '#10b981' ?>;">
                                    Rs: <?= number_format($bal, 2) ?>
                                </td>
                                <td>
                                    <span class="sf-badge <?= $badgeCls ?>">
                                        <span class="badge-dot-sm"></span><?= $badgeTxt ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="display:flex; align-items:center;" onclick="event.stopPropagation();">
                                        <label class="erp-switch" title="Toggle active/inactive status">
                                            <input type="checkbox" <?= $accountStatus === 'Active' ? 'checked' : '' ?> onchange="toggleProviderStatus(<?= $s->id ?>, this)">
                                            <span class="erp-slider"></span>
                                        </label>
                                        <span class="status-label <?= $accountStatus === 'Active' ? 'status-active' : 'status-inactive' ?>" id="status-text-<?= $s->id ?>"><?= $accountStatus ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div style="display:flex; justify-content:center; gap:6px;" onclick="event.stopPropagation()">
                                        <button type="button" class="act-btn" onclick="openServiceProviderModalPopup(<?= $s->id ?>)" title="View profile & activity ledger">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pinned Pagination Footer (RULE 1) -->
        <div class="erp-pagination">
            <div class="pg-info" id="pg-info-text">
                Showing <strong>1</strong> – <strong>15</strong> of <strong><?= count($data['service_providers'] ?? []) ?></strong>
            </div>
            <div class="pg-right">
                <div class="pg-size-wrap">
                    <span class="pg-size-lbl">SHOW:</span>
                    <select class="pg-size-sel" onchange="updatePageSize(this.value)">
                        <option value="15" selected>15</option>
                        <option value="25">25</option>
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

<!-- 4. Sleek Floating Island Command Bar Pill (RULE 3) -->
<div class="cmd-bar">
    <div class="cmd-search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="searchInput" placeholder="Search service providers, category, phone..." onkeyup="filterList()">
    </div>
    <div class="cmd-divider"></div>
    <button type="button" class="cmd-cta" onclick="openModal('addProviderModal')">
        <i class="fa-solid fa-plus"></i> New Service Provider
    </button>
</div>

<!-- ============================================================
     POPUP MODAL: SERVICE PROVIDER PROFILE, LEDGER & BILLS
     ============================================================ -->
<div id="serviceProviderProfileModal" class="modal-veil hidden" onclick="if(event.target === this) closeServiceProviderProfile()">
    <div class="sf-modal" style="width: 88%; max-width: 1060px; height: 88vh; display: flex; flex-direction: column; padding: 0; overflow: hidden;">
        
        <div class="modal-head" id="modal-header-container" style="padding: 14px 20px;">
            <div style="display: flex; gap: 12px; align-items: center;">
                <div class="avatar-circle" style="width: 36px; height: 36px; font-size: 14px;">SP</div>
                <div>
                    <h3 class="modal-title" style="font-size: 16px; font-weight: 700;">Service Provider Details</h3>
                </div>
            </div>
            <button type="button" onclick="closeServiceProviderProfile()" class="modal-close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <div id="modal-loader" style="display:none; flex:1; align-items:center; justify-content:center; flex-direction:column; gap:12px; background:#ffffff;">
            <i class="fa-solid fa-spinner spin" style="font-size:32px; color:#3b82f6;"></i>
            <span style="font-size:13px; color:#64748b; font-weight:500;">Loading provider profile & ledger...</span>
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
        <div style="display: flex; gap: 12px; align-items: center; min-width: 0; flex: 1; margin-right: 15px;">
            <div class="avatar-circle" style="width: 38px; height: 38px; font-size: 14px; flex-shrink: 0;">
                <?= strtoupper(substr($sup->name ?? '', 0, 2)) ?>
            </div>
            <div style="min-width: 0; flex: 1;">
                <h3 class="modal-title" style="font-size: 16px; font-weight: 700; word-break: break-word; line-height: 1.2; margin: 0 0 2px 0;" title="<?= htmlspecialchars($sup->name ?? '') ?>">
                    <?= htmlspecialchars($sup->name ?? '') ?>
                </h3>
                <div style="font-size: 11px; color: #64748b; display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                    <span>📞 <?= !empty($sup->phone) ? htmlspecialchars($sup->phone) : '<span style="color:#ef4444; font-weight:600;">Missing</span>' ?></span>
                    <span>✉️ <?= !empty($sup->email) ? htmlspecialchars($sup->email) : '<span style="color:#ef4444; font-weight:600;">Missing</span>' ?></span>
                    <span style="color:#8b5cf6; font-weight:600;">⚡ <?= htmlspecialchars($sup->service_category ?: 'General') ?></span>
                </div>
            </div>
        </div>
        
        <!-- Header Statistics Cards -->
        <div style="display: flex; gap: 8px; margin-right: 12px; flex-shrink: 0; align-items: center;">
            <div style="background: #f1f5f9; padding: 4px 10px; border-radius: 6px; text-align: center; min-width: 90px;">
                <div style="font-size: 8.5px; color: #64748b; text-transform: uppercase; font-weight: 700; letter-spacing: 0.04em;">Total Billed</div>
                <div style="font-size: 12.5px; font-weight: bold; color: #0f172a; margin-top: 1px; font-family: var(--erp-mono);">Rs: <?= number_format($stats->total_billed, 2) ?></div>
            </div>
            <div style="background: #dcfce7; padding: 4px 10px; border-radius: 6px; border: 1px solid rgba(52,199,89,0.2); text-align: center; min-width: 90px;">
                <div style="font-size: 8.5px; color: #15803d; text-transform: uppercase; font-weight: 700; letter-spacing: 0.04em;">Total Paid</div>
                <div style="font-size: 12.5px; font-weight: bold; color: #15803d; margin-top: 1px; font-family: var(--erp-mono);">Rs: <?= number_format($stats->total_paid, 2) ?></div>
            </div>
            <div style="background: <?= $stats->outstanding > 0 ? '#fee2e2' : '#dcfce7' ?>; padding: 4px 10px; border-radius: 6px; border: 1px solid <?= $stats->outstanding > 0 ? 'rgba(255,59,48,0.2)' : 'rgba(52,199,89,0.2)' ?>; text-align: center; min-width: 90px;">
                <div style="font-size: 8.5px; color: <?= $stats->outstanding > 0 ? '#b91c1c' : '#15803d' ?>; text-transform: uppercase; font-weight: 700; letter-spacing: 0.04em;">Outstanding</div>
                <div style="font-size: 12.5px; font-weight: bold; color: <?= $stats->outstanding > 0 ? '#b91c1c' : '#15803d' ?>; margin-top: 1px; font-family: var(--erp-mono);">Rs: <?= number_format($stats->outstanding, 2) ?></div>
            </div>
        </div>

        <button type="button" onclick="closeServiceProviderProfile()" class="modal-close" style="flex-shrink: 0;"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div id="modal-profile-content-source" class="hidden">
        <!-- Tab Navigation -->
        <div class="tabs">
            <button class="tab-btn active" onclick="switchModalTab('ledger')" id="mbtn_ledger">Activity Ledger</button>
            <button class="tab-btn" onclick="switchModalTab('bills')" id="mbtn_bills">Service Bills (SRN)</button>
            <button class="tab-btn" onclick="switchModalTab('profile')" id="mbtn_profile">Profile & Settings</button>
        </div>

        <!-- TAB 1: Ledger -->
        <div id="mtab_ledger" class="tab-content active" style="padding: 16px 20px; overflow-y: auto; flex: 1;">
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
                        <tr><td colspan="6" style="text-align: center; color: #64748b; padding: 25px;">No financial activity recorded for this service provider.</td></tr>
                    <?php else: foreach($data['ledger'] as $l): ?>
                        <tr>
                            <td style="color:#64748b; font-size:11.5px;"><?= date('M d, Y', strtotime($l->date)) ?></td>
                            <td>
                                <span class="sf-badge <?= ($l->type == 'SRN' || $l->type == 'GRN' || $l->type == 'Service Bill') ? 'badge-inactive' : ($l->type == 'Payment' ? 'badge-active' : 'badge-owed') ?>">
                                    <?= ($l->type == 'GRN' || $l->type == 'Service Bill') ? 'SRN' : htmlspecialchars($l->type) ?>
                                </span>
                            </td>
                            <td>
                                <?php if($l->type == 'SRN' || $l->type == 'GRN' || $l->type == 'Service Bill'): ?>
                                    <a href="<?= APP_URL ?>/serviceprovider/bill/<?= $l->id ?>" target="_blank" style="color:#3b82f6; font-weight:bold; text-decoration:none; font-family:var(--erp-mono);">
                                        <?= (strpos($l->ref, 'SRN') === 0) ? htmlspecialchars($l->ref) : 'SRN-' . htmlspecialchars($l->ref) ?> ↗
                                    </a>
                                <?php elseif($l->type == 'Supplier Return'): ?>
                                    <a href="<?= APP_URL ?>/supplier-return/show/<?= $l->id ?>" target="_blank" style="color:#ef4444; font-weight:bold; text-decoration:none;">
                                        <?= htmlspecialchars($l->ref) ?> ↗
                                    </a>
                                <?php elseif($l->type == 'Payment Reversal'): ?>
                                    <span style="color: #f59e0b; font-size: 11.5px; font-weight:600; background: #fef3c7; padding: 2px 6px; border-radius: 4px;"><?= htmlspecialchars($l->ref) ?></span>
                                <?php else: ?>
                                    <span style="color:#0f172a;"><?= htmlspecialchars($l->ref) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="num-col" style="color:#10b981; font-weight:500; font-family:var(--erp-mono);"><?= $l->debit > 0 ? 'Rs: ' . number_format($l->debit, 2) : '-' ?></td>
                            <td class="num-col" style="font-weight:500; font-family:var(--erp-mono);"><?= $l->credit > 0 ? 'Rs: ' . number_format($l->credit, 2) : '-' ?></td>
                            <td class="num-col" style="font-weight:bold; font-family:var(--erp-mono); color: <?= $l->balance > 0 ? '#ef4444' : '#10b981' ?>;">Rs: <?= number_format($l->balance, 2) ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <!-- TAB 2: Service Bills -->
        <div id="mtab_bills" class="tab-content" style="padding: 16px 20px; overflow-y: auto; flex: 1;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <h4 style="margin:0; font-size:13px; font-weight:700; text-transform:uppercase; color:#475569;">Service Bills (SRN) History</h4>
                <button type="button" class="sf-btn success" onclick="openModal('addBillModal')">
                    <i class="fa-solid fa-plus"></i> Record Service Bill
                </button>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Bill Date</th>
                        <th>SRN Number</th>
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
                        <tr><td colspan="8" style="text-align: center; color: #64748b; padding: 25px;">No service bills recorded.</td></tr>
                    <?php else: foreach($data['bills'] as $b): ?>
                        <tr>
                            <td style="color:#64748b; font-size:11.5px;"><?= date('M d, Y', strtotime($b->grn_date)) ?></td>
                            <td><strong style="color: #3b82f6; font-family: var(--erp-mono);">SRN-<?= htmlspecialchars($b->grn_number) ?></strong></td>
                            <td><?= htmlspecialchars($b->service_period ?: 'N/A') ?></td>
                            <td style="color:#ef4444; font-size:11.5px; font-weight:500;"><?= $b->due_date ? date('M d, Y', strtotime($b->due_date)) : 'N/A' ?></td>
                            <td>
                                <span class="sf-badge <?= ($b->status === 'Paid') ? 'badge-active' : (($b->status === 'Partially Paid') ? 'badge-inactive' : 'badge-owed') ?>">
                                    <span class="badge-dot-sm"></span><?= htmlspecialchars($b->status ?: 'Unpaid') ?>
                                </span>
                            </td>
                            <td class="num-col" style="font-weight:700; font-family:var(--erp-mono);">Rs: <?= number_format($b->total_amount, 2) ?></td>
                            <td class="num-col" style="font-weight:700; font-family:var(--erp-mono); color: <?= $b->balance_due > 0 ? '#ef4444' : '#10b981' ?>;">Rs: <?= number_format($b->balance_due, 2) ?></td>
                            <td style="text-align: center;">
                                <a href="<?= APP_URL ?>/serviceprovider/bill/<?= $b->id ?>" target="_blank" class="sf-btn neutral" style="padding: 3px 8px; font-size: 11px;">
                                    View Details ↗
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <!-- TAB 3: Profile & Settings -->
        <div id="mtab_profile" class="tab-content" style="padding: 20px; overflow-y: auto; flex: 1;">
            <h4 style="margin:0 0 16px 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; font-size:12px; font-weight:700; text-transform:uppercase; color:#475569; letter-spacing:0.04em;">
                Update Service Provider Details
            </h4>
            
            <form action="<?= APP_URL ?>/serviceprovider/index/<?= $sup->id ?>" method="POST" style="width: 100%;">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="action" value="update_service_provider">
                <input type="hidden" name="service_provider_id" value="<?= $sup->id ?>">
                
                <div class="grid-2">
                    <div class="sf-group">
                        <label>Provider / Company Name *</label>
                        <input type="text" name="name" class="sf-input" value="<?= htmlspecialchars($sup->name) ?>" required>
                    </div>
                    <div class="sf-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="sf-input" value="<?= htmlspecialchars($sup->email) ?>" placeholder="e.g. info@company.lk">
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
                        <textarea name="address" class="sf-input" rows="3" style="resize:vertical; min-height:42px;"><?= htmlspecialchars($sup->address) ?></textarea>
                    </div>
                </div>
                
                <div style="text-align: center; margin-top: 20px; padding-top: 14px; border-top: 1px solid #e2e8f0;">
                    <button type="submit" class="sf-btn primary" style="padding: 8px 28px; font-size: 13px; border-radius: 9999px;">
                        <i class="fa-solid fa-floppy-disk"></i> Save Changes
                    </button>
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
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
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
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <div class="modal-body" style="max-height: 75vh; overflow-y: auto;">
                <input type="hidden" name="action" value="add_service_bill">
                <input type="hidden" name="service_provider_id" value="<?= $data['selected_service_provider']->id ?>">
                <input type="hidden" name="ap_account_id" value="<?= $data['ap_account'] ? $data['ap_account']->id : '' ?>">
                
                <div class="grid-2">
                    <div class="sf-group">
                        <label>Bill Number (System Generated) *</label>
                        <input type="text" name="bill_number" class="sf-input" value="<?= htmlspecialchars($data['next_bill_number']) ?>" readonly style="background:#f1f5f9; font-weight:700;">
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
                        <input type="number" step="0.01" id="bill_total" name="total_amount" class="sf-input" readonly placeholder="0.00" style="background:#f1f5f9; font-weight:700; color:#3b82f6;">
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
                        <input type="text" class="sf-input" readonly value="<?= $data['ap_account'] ? htmlspecialchars($data['ap_account']->account_code . ' - ' . $data['ap_account']->account_name) : 'Accounts Payable Not Configured' ?>" style="background:#f1f5f9;">
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
    window.csrfToken = '<?= $csrfToken ?>';

    document.addEventListener("DOMContentLoaded", function() {
        const rows = document.querySelectorAll('#spList .provider-row');
        rows.forEach(row => {
            window.allProviders.push({
                element: row,
                id: row.getAttribute('data-id'),
                name: row.getAttribute('data-name'),
                category: row.getAttribute('data-category'),
                phone: row.getAttribute('data-phone'),
                email: row.getAttribute('data-email'),
                status: row.getAttribute('data-status'),
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

    window.filterList = function() {
        const query = (document.getElementById('searchInput').value || '').toLowerCase().trim();
        const catFilter = (document.getElementById('filterCategory').value || '').toLowerCase();
        const ledgerFilter = document.getElementById('filterStatus').value;
        const statusFilter = document.getElementById('filterAccountStatus').value;
        
        window.filteredProviders = window.allProviders.filter(c => {
            const matchQuery = !query || c.name.includes(query) || c.category.includes(query) || c.phone.includes(query) || c.email.includes(query);
            let matchLedger = true;
            let matchCat = true;
            let matchStatus = true;
            
            if (ledgerFilter === 'owed') matchLedger = (c.outstanding > 0);
            else if (ledgerFilter === 'cleared') matchLedger = (c.outstanding <= 0);
            
            if (catFilter) {
                matchCat = c.category.includes(catFilter);
            }

            if (statusFilter) {
                matchStatus = (c.status === statusFilter);
            }

            return matchQuery && matchLedger && matchCat && matchStatus;
        });
        
        window.currentPage = 1;
        window.renderPagination();
        document.getElementById('matching-count').innerText = window.filteredProviders.length;
    };

    window.clearAllFilters = function() {
        document.getElementById('searchInput').value = '';
        document.getElementById('filterCategory').value = '';
        document.getElementById('filterStatus').value = '';
        document.getElementById('filterAccountStatus').value = '';
        window.filterList();
    };

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
    };
    
    window.navigatePage = function(page) {
        let totalPages = Math.ceil(window.filteredProviders.length / window.pageSize);
        if (page >= 1 && page <= totalPages) {
            window.currentPage = page;
            window.renderPagination();
        }
    };
    
    window.updatePageSize = function(size) {
        if (size === '1000') { window.pageSize = 999999; } else { window.pageSize = parseInt(size); }
        window.currentPage = 1;
        window.renderPagination();
    };

    window.toggleProviderStatus = function(id, checkboxEl) {
        const originalChecked = !checkboxEl.checked;
        const newStatus = checkboxEl.checked ? 'Active' : 'Inactive';
        const labelEl = document.getElementById('status-text-' + id);
        
        if (labelEl) {
            labelEl.textContent = newStatus;
            labelEl.className = 'status-label ' + (checkboxEl.checked ? 'status-active' : 'status-inactive');
        }

        const formData = new FormData();
        formData.append('csrf_token', window.csrfToken);

        fetch('<?= APP_URL ?>/serviceprovider/toggle_status/' + id, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.csrfToken
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Server returned HTTP ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (!data.success) {
                throw new Error(data.message || 'Status toggle failed');
            }
            // Update in-memory record
            const item = window.allProviders.find(p => p.id == id);
            if (item) {
                item.status = data.new_status;
                item.element.setAttribute('data-status', data.new_status);
            }
        })
        .catch(err => {
            console.error('[toggleProviderStatus Error]:', err);
            // Rollback UI
            checkboxEl.checked = originalChecked;
            const rolledStatus = originalChecked ? 'Active' : 'Inactive';
            if (labelEl) {
                labelEl.textContent = rolledStatus;
                labelEl.className = 'status-label ' + (originalChecked ? 'status-active' : 'status-inactive');
            }
            alert('Failed to update status: ' + (err.message || 'Network or CSRF error'));
        });
    };

    window.openModal = function(id) { 
        const m = document.getElementById(id);
        if (m) {
            m.classList.remove('hidden');
        } else {
            console.error('[openModal Error] Target modal ID not found in DOM:', id);
            alert('Unable to open modal: #' + id + ' is missing.');
        }
    };

    window.closeModal = function(id) { 
        const m = document.getElementById(id);
        if (m) {
            m.classList.add('hidden');
        } else {
            console.error('[closeModal Error] Target modal ID not found in DOM:', id);
        }
    };

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
                if (!response.ok) throw new Error('Failed to load provider profile (HTTP ' + response.status + ')');
                return response.text();
            })
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                const newContent = doc.getElementById('modal-profile-content-source');
                const newHeader = doc.getElementById('modal-header-source');
                const newBillModal = doc.getElementById('addBillModal');
                
                if (newContent && newHeader) {
                    content.innerHTML = newContent.innerHTML;
                    document.getElementById('modal-header-container').innerHTML = newHeader.innerHTML;
                    
                    // Inject or update the dynamic addBillModal in main DOM
                    let billModalEl = document.getElementById('addBillModal');
                    if (newBillModal) {
                        if (billModalEl) {
                            billModalEl.outerHTML = newBillModal.outerHTML;
                        } else {
                            document.body.insertAdjacentHTML('beforeend', newBillModal.outerHTML);
                        }
                    }

                    loader.style.display = 'none';
                    content.style.display = 'flex';
                    
                    if (tab) { window.switchModalTab(tab); } 
                    else { window.switchModalTab('ledger'); }
                } else {
                    throw new Error('Profile layout mismatch: missing modal-profile-content-source or modal-header-source');
                }
            })
            .catch(err => {
                console.error('[openServiceProviderModalPopup Error]:', err);
                loader.style.display = 'none';
                content.innerHTML = `<div style="padding:40px; text-align:center; color:#ef4444; font-weight:600;"><i class="fa-solid fa-triangle-exclamation" style="font-size:24px; margin-bottom:10px; display:block;"></i>Failed to load provider profile data: ${err.message || err}</div>`;
                content.style.display = 'block';
            });
    };

    window.closeServiceProviderProfile = function() {
        document.getElementById('serviceProviderProfileModal').classList.add('hidden');
        window.history.pushState({ path: '<?= APP_URL ?>/serviceprovider' }, '', '<?= APP_URL ?>/serviceprovider');
    };

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
    };

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
    };

    window.calculateBillTotal = function() {
        const amt = parseFloat(document.getElementById('bill_amount').value) || 0;
        const tax = parseFloat(document.getElementById('bill_tax').value) || 0;
        const totEl = document.getElementById('bill_total');
        if (totEl) totEl.value = (amt + tax).toFixed(2);
    };

} catch(e) {
    console.error("--- SERVICE PROVIDER SCRIPT FAILED ---", e);
}
</script>
