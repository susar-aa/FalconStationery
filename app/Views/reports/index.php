<?php
$categories = $data['categories'] ?? [];
$groupedReports = $data['grouped_reports'] ?? [];
$allReports = $data['reports'] ?? [];
$warehouses = $data['warehouses'] ?? [];

$totalReportsCount = 0;
foreach ($groupedReports as $list) {
    $totalReportsCount += count($list);
}

// Icon mappings for the 12 canonical categories
$categoryIcons = [
    'overview'    => 'ph ph-activity',
    'sales'       => 'ph ph-currency-dollar',
    'inventory'   => 'ph ph-package',
    'procurement' => 'ph ph-shopping-bag',
    'supplier'    => 'ph ph-users',
    'customer'    => 'ph ph-user-check',
    'collection'  => 'ph ph-credit-card',
    'banking'     => 'ph ph-bank',
    'expenses'    => 'ph ph-receipt',
    'finance'     => 'ph ph-book-open',
    'cashier'     => 'ph ph-medal',
    'expiry'      => 'ph ph-calendar-x'
];
?>

<!-- Phosphor Icons Suite -->
<script src="https://unpkg.com/@phosphor-icons/web"></script>

<style>
/* ============================================================
   ERP PROFESSIONAL DESIGN STANDARDS — REPORTS HUB (100VH LOCKED)
   ============================================================ */
@font-face {
    font-family: 'SF Pro Display';
    src: url('<?= APP_URL ?>/public/assets/fonts/sf_pro_display_regular.ttf') format('truetype');
    font-weight: 400;
    font-style: normal;
}
@font-face {
    font-family: 'SF Pro Display';
    src: url('<?= APP_URL ?>/public/assets/fonts/sf_pro_display_medium.ttf') format('truetype');
    font-weight: 500;
    font-style: normal;
}
@font-face {
    font-family: 'SF Pro Display';
    src: url('<?= APP_URL ?>/public/assets/fonts/sf_pro_display_semibold.ttf') format('truetype');
    font-weight: 600;
    font-style: normal;
}
@font-face {
    font-family: 'SF Pro Display';
    src: url('<?= APP_URL ?>/public/assets/fonts/sf_pro_display_bold.ttf') format('truetype');
    font-weight: 700;
    font-style: normal;
}

:root {
    --f-main: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    --f-mono: 'SF Mono', ui-monospace, Menlo, Monaco, Consolas, monospace;
    --c-bg: #eef2f6;
    --c-surface: #ffffff;
    --c-border: #e2e8f0;
    --c-text-main: #0f172a;
    --c-text-muted: #64748b;
    --c-navy-dark: #0f172a;
    --c-navy-hover: #1e293b;
    --c-blue-accent: #2563eb;
    --c-blue-light: #eff6ff;
}

body, html {
    margin: 0;
    padding: 0;
    height: 100vh;
    overflow: hidden !important;
    font-family: var(--f-main);
    background-color: var(--c-bg);
}

.report-hub-viewport {
    height: calc(100vh - 60px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    position: relative;
    padding: 12px 18px 0;
    box-sizing: border-box;
}

/* TOP WORKSPACE NAVIGATION STRIP */
.report-top-shelf {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
    flex-shrink: 0;
}

.shelf-left {
    display: flex;
    align-items: center;
    gap: 10px;
}

.btn-circle-back {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #ffffff;
    border: 1px solid var(--c-border);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #334155;
    text-decoration: none;
    font-size: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    transition: all 0.15s ease;
}
.btn-circle-back:hover {
    background: #f8fafc;
    color: var(--c-text-main);
    border-color: #cbd5e1;
}

.hub-title-pill {
    background: #ffffff;
    border: 1px solid var(--c-border);
    border-radius: 999px;
    padding: 7px 18px;
    font-size: 13.5px;
    font-weight: 700;
    color: var(--c-text-main);
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    display: flex;
    align-items: center;
    gap: 8px;
}

.branch-select-pill {
    background: #ffffff;
    border: 1px solid var(--c-border);
    border-radius: 999px;
    padding: 6px 14px;
    font-size: 12.5px;
    font-weight: 600;
    color: #334155;
    display: flex;
    align-items: center;
    gap: 7px;
    cursor: pointer;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}
.branch-select-pill select {
    border: none;
    background: transparent;
    font-family: var(--f-main);
    font-size: 12.5px;
    font-weight: 600;
    color: #334155;
    outline: none;
    cursor: pointer;
}

/* MAIN TWO-COLUMN CONTAINER */
.hub-content-grid {
    flex: 1;
    display: flex;
    background: transparent;
    gap: 16px;
    min-height: 0;
    overflow: hidden;
}

/* LEFT SIDEBAR: CATEGORIES */
.hub-sidebar {
    width: 250px;
    flex-shrink: 0;
    background: #ffffff;
    border: 1px solid var(--c-border);
    border-radius: 14px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
}

.sidebar-header-label {
    padding: 16px 18px 8px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    color: var(--c-text-muted);
    text-transform: uppercase;
}

.sidebar-menu-list {
    flex: 1;
    overflow-y: auto;
    padding: 4px 10px 14px;
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.category-tab-btn {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 9px 12px;
    border-radius: 9px;
    color: #334155;
    text-decoration: none;
    font-size: 13.5px;
    font-weight: 500;
    transition: all 0.15s ease;
    cursor: pointer;
    border: none;
    background: transparent;
    width: 100%;
    text-align: left;
    box-sizing: border-box;
}

.category-tab-btn:hover {
    background: #f1f5f9;
    color: var(--c-text-main);
}

.category-tab-btn.active {
    background: var(--c-navy-dark) !important;
    color: #ffffff !important;
    font-weight: 600;
}

.cat-label-wrap {
    display: flex;
    align-items: center;
    gap: 10px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}

.cat-label-wrap i {
    font-size: 17px;
    color: #64748b;
    transition: color 0.15s;
    flex-shrink: 0;
}

.category-tab-btn.active .cat-label-wrap i {
    color: #ffffff;
}

.cat-count-badge {
    font-size: 11px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 999px;
    background: #e2e8f0;
    color: #475569;
    flex-shrink: 0;
}

.category-tab-btn.active .cat-count-badge {
    background: rgba(255,255,255,0.2);
    color: #ffffff;
}

/* RIGHT PANE: REPORT LIST */
.hub-main-pane {
    flex: 1;
    background: #ffffff;
    border: 1px solid var(--c-border);
    border-radius: 14px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
}

.pane-header-strip {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 22px;
    border-bottom: 1px solid #f1f5f9;
    flex-shrink: 0;
}

.active-cat-title-wrap {
    display: flex;
    align-items: center;
    gap: 10px;
}

.active-cat-title {
    font-size: 17px;
    font-weight: 700;
    color: var(--c-text-main);
}

.active-cat-badge {
    background: var(--c-blue-light);
    color: var(--c-blue-accent);
    font-size: 12px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 999px;
}

.report-search-box {
    position: relative;
    width: 270px;
}
.report-search-box i {
    position: absolute;
    left: 11px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 15px;
}
.report-search-box input {
    width: 100%;
    padding: 7px 12px 7px 34px;
    border: 1px solid var(--c-border);
    border-radius: 8px;
    font-family: var(--f-main);
    font-size: 13px;
    color: var(--c-text-main);
    background: #f8fafc;
    box-sizing: border-box;
    outline: none;
    transition: all 0.15s ease;
}
.report-search-box input:focus {
    background: #ffffff;
    border-color: var(--c-blue-accent);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
}

/* REPORT CARDS CONTAINER (SCROLLABLE ONLY) */
.reports-scroll-container {
    flex: 1;
    overflow-y: auto;
    padding: 18px 22px 70px;
    box-sizing: border-box;
}

.report-card-item {
    background: #ffffff;
    border: 1px solid var(--c-border);
    border-radius: 12px;
    padding: 14px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
    text-decoration: none;
    color: inherit;
    transition: all 0.15s ease;
    cursor: pointer;
}

.report-card-item:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
    transform: translateY(-1px);
}

.card-left-section {
    display: flex;
    align-items: center;
    gap: 16px;
    flex: 1;
    min-width: 0;
}

.card-icon-box {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: #f8fafc;
    border: 1px solid var(--c-border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: #334155;
    flex-shrink: 0;
}

.card-text-body {
    display: flex;
    flex-direction: column;
    gap: 2px;
    overflow: hidden;
}

.card-report-title {
    font-size: 14.5px;
    font-weight: 700;
    color: var(--c-text-main);
}

.card-report-desc {
    font-size: 12.5px;
    color: var(--c-text-muted);
    line-height: 1.35;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.card-right-section {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-shrink: 0;
    margin-left: 16px;
}

.card-cat-pill {
    font-size: 11.5px;
    font-weight: 600;
    color: #475569;
    background: #f1f5f9;
    padding: 4px 10px;
    border-radius: 999px;
}

.card-arrow-icon {
    color: #94a3b8;
    font-size: 14px;
}

/* EMPTY STATE */
.reports-empty-state {
    display: none;
    text-align: center;
    padding: 60px 20px;
    color: #94a3b8;
}
.reports-empty-state i {
    font-size: 40px;
    margin-bottom: 12px;
}
.reports-empty-state p {
    font-size: 14px;
    margin: 0;
}

/* BOTTOM COMMAND BAR DOCK */
.report-bottom-dock {
    position: fixed;
    bottom: 16px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(255, 255, 255, 0.94);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(226, 232, 240, 0.85);
    border-radius: 999px;
    padding: 5px 8px;
    display: flex;
    align-items: center;
    gap: 4px;
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.06);
    z-index: 100;
}

.dock-item-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4px 14px;
    border-radius: 999px;
    color: #64748b;
    text-decoration: none;
    font-size: 11px;
    font-weight: 600;
    transition: all 0.15s ease;
    gap: 2px;
}
.dock-item-btn i {
    font-size: 16px;
}
.dock-item-btn:hover, .dock-item-btn.active {
    color: var(--c-text-main);
    background: #f1f5f9;
}
.dock-badge-pill {
    background: #ef4444;
    color: #fff;
    font-size: 9px;
    font-weight: 700;
    padding: 1px 5px;
    border-radius: 999px;
    margin-left: 2px;
}
</style>

<div class="report-hub-viewport erp-viewport-view">
    <!-- TOP WORKSPACE NAVIGATION STRIP -->
    <div class="report-top-shelf">
        <div class="shelf-left">
            <a href="<?= APP_URL ?>/home" class="btn-circle-back" title="Go Back">
                <i class="ph ph-arrow-left"></i>
            </a>
            <div class="hub-title-pill">
                <span>Central Reports & Financial Statements</span>
            </div>
            <div class="branch-select-pill">
                <i class="ph ph-storefront"></i>
                <select id="branchSelector">
                    <?php if (!empty($warehouses)): ?>
                        <?php foreach ($warehouses as $wh): ?>
                            <option value="<?= $wh->id ?>"><?= htmlspecialchars($wh->name) ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="1">Kandy Branch (KKK)</option>
                        <option value="2">Colombo Main Hub</option>
                    <?php endif; ?>
                </select>
                <i class="ph ph-caret-down" style="font-size: 12px; color: #94a3b8;"></i>
            </div>
        </div>
    </div>

    <!-- MAIN TWO-COLUMN CONTAINER -->
    <div class="hub-content-grid">
        <!-- LEFT SIDEBAR: CATEGORIES -->
        <div class="hub-sidebar">
            <div class="sidebar-header-label">CATEGORIES</div>
            <div class="sidebar-menu-list">
                <!-- ALL REPORTS -->
                <button type="button" class="category-tab-btn active" data-category="all">
                    <span class="cat-label-wrap">
                        <i class="ph ph-squares-four"></i>
                        <span>All Reports</span>
                    </span>
                    <span class="cat-count-badge" id="totalCountBadge"><?= $totalReportsCount ?></span>
                </button>

                <!-- 12 CANONICAL CATEGORIES -->
                <?php foreach ($categories as $catKey => $catTitle): 
                    $catCount = isset($groupedReports[$catKey]) ? count($groupedReports[$catKey]) : 0;
                    $iconClass = $categoryIcons[$catKey] ?? 'ph ph-chart-line';
                ?>
                    <button type="button" class="category-tab-btn" data-category="<?= $catKey ?>">
                        <span class="cat-label-wrap">
                            <i class="<?= $iconClass ?>"></i>
                            <span><?= htmlspecialchars($catTitle) ?></span>
                        </span>
                        <span class="cat-count-badge"><?= $catCount ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- RIGHT PANE: REPORT LIST -->
        <div class="hub-main-pane">
            <div class="pane-header-strip">
                <div class="active-cat-title-wrap">
                    <span class="active-cat-title" id="activeCategoryHeader">All Reports</span>
                    <span class="active-cat-badge" id="activeCategoryBadge"><?= $totalReportsCount ?> Reports</span>
                </div>
                <div class="report-search-box">
                    <i class="ph ph-magnifying-glass"></i>
                    <input type="text" id="reportSearchInput" placeholder="Quick search reports..." autocomplete="off">
                </div>
            </div>

            <!-- SCROLLABLE REPORT CARDS CONTAINER -->
            <div class="reports-scroll-container" id="reportsCardsList">
                <?php foreach ($allReports as $repKey => $rep): 
                    $catKey = $rep['category'] ?? 'overview';
                    $catTitle = $categories[$catKey] ?? ucfirst($catKey);
                    $icon = $rep['icon'] ?? 'ph-chart-line';
                    $url = APP_URL . '/report/viewer/' . $repKey;
                ?>
                    <a href="<?= $url ?>" 
                       class="report-card-item" 
                       data-category="<?= $catKey ?>"
                       data-title="<?= strtolower(htmlspecialchars($rep['title'])) ?>"
                       data-desc="<?= strtolower(htmlspecialchars($rep['description'] ?? '')) ?>">
                        <div class="card-left-section">
                            <div class="card-icon-box">
                                <i class="ph <?= $icon ?>"></i>
                            </div>
                            <div class="card-text-body">
                                <span class="card-report-title"><?= htmlspecialchars($rep['title']) ?></span>
                                <span class="card-report-desc"><?= htmlspecialchars($rep['description'] ?? 'High-fidelity analytical report with real-time GL reconciliation.') ?></span>
                            </div>
                        </div>
                        <div class="card-right-section">
                            <span class="card-cat-pill"><?= htmlspecialchars($catTitle) ?></span>
                            <i class="ph ph-caret-right card-arrow-icon"></i>
                        </div>
                    </a>
                <?php endforeach; ?>

                <div class="reports-empty-state" id="reportsEmptyState">
                    <i class="ph ph-file-dashed"></i>
                    <p>No matching reports found.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- BOTTOM COMMAND DOCK -->
    <div class="report-bottom-dock">
        <a href="<?= APP_URL ?>/home" class="dock-item-btn">
            <i class="ph ph-house"></i>
            <span>Home</span>
        </a>
        <a href="<?= APP_URL ?>/pos" class="dock-item-btn">
            <i class="ph ph-cash-register"></i>
            <span>POS</span>
        </a>
        <a href="<?= APP_URL ?>/alerts" class="dock-item-btn">
            <i class="ph ph-bell"></i>
            <span>Alerts <span class="dock-badge-pill">3</span></span>
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.body.classList.add('erp-locked');
    const tabButtons = document.querySelectorAll('.category-tab-btn');
    const reportCards = document.querySelectorAll('.report-card-item');
    const searchInput = document.getElementById('reportSearchInput');
    const categoryHeader = document.getElementById('activeCategoryHeader');
    const categoryBadge = document.getElementById('activeCategoryBadge');
    const emptyState = document.getElementById('reportsEmptyState');

    let currentCategory = 'all';

    function filterReports() {
        const query = searchInput.value.trim().toLowerCase();
        let visibleCount = 0;

        reportCards.forEach(card => {
            const cardCat = card.getAttribute('data-category');
            const cardTitle = card.getAttribute('data-title');
            const cardDesc = card.getAttribute('data-desc');

            const matchesCategory = (currentCategory === 'all' || cardCat === currentCategory);
            const matchesQuery = !query || cardTitle.includes(query) || cardDesc.includes(query);

            if (matchesCategory && matchesQuery) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (visibleCount === 0) {
            emptyState.style.display = 'block';
        } else {
            emptyState.style.display = 'none';
        }

        categoryBadge.textContent = visibleCount + ' Reports';
    }

    tabButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            tabButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            currentCategory = this.getAttribute('data-category');
            const catName = this.querySelector('.cat-label-wrap span').textContent;
            categoryHeader.textContent = catName;

            filterReports();
        });
    });

    searchInput.addEventListener('input', function() {
        filterReports();
    });
});
</script>
