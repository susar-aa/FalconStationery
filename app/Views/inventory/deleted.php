<?php
// ==========================================
// DELETED PRODUCTS MODULE (SF PRO DESIGN)
// ==========================================

$items = $data['items'] ?? [];
$search = $data['search'] ?? '';

$pagination = $data['pagination'] ?? [
    'current_page' => 1,
    'per_page' => 15,
    'total_items' => count($items),
    'total_pages' => 1
];

$currentPage = (int)$pagination['current_page'];
$perPage = (int)$pagination['per_page'];
$totalItems = (int)$pagination['total_items'];
$totalPages = (int)$pagination['total_pages'];

$flashSuccess = $_SESSION['flash_success'] ?? null;
if ($flashSuccess) unset($_SESSION['flash_success']);
$flashError = $_SESSION['flash_error'] ?? null;
if ($flashError) unset($_SESSION['flash_error']);
?>

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
    --c-red:          #ff3b30;
    --c-red-light:    #fff0ef;
    --c-orange:       #ff9500;
    --f-system: -apple-system, 'SF Pro Display', 'SF Pro Text', 'Inter', sans-serif;
    --f-mono:   ui-monospace, 'SF Mono', 'Menlo', monospace;
    --t-primary:   #1c1c1e;
    --t-secondary: #636366;
    --t-tertiary:  #aeaeb2;
    --t-label:     #8e8e93;
    --shadow-xs:  0 1px 2px rgba(0,0,0,0.04);
    --shadow-sm:  0 2px 8px rgba(0,0,0,0.06);
    --shadow-md:  0 8px 24px rgba(0,0,0,0.08);
    --r-sm: 10px;
    --r-md: 14px;
    --r-lg: 20px;
    --r-xl: 26px;
    --r-pill: 999px;
    --ease-ios: cubic-bezier(0.25, 0.1, 0.25, 1);
    --dur-fast: 0.18s;
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.del-root {
    font-family: var(--f-system);
    font-size: 15px;
    color: var(--t-primary);
    background: var(--c-bg);
    -webkit-font-smoothing: antialiased;
}

.del-wrap {
    max-width: 1420px;
    margin: 0 auto;
    padding: 24px 24px 120px;
}

.del-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
}

.del-title {
    font-size: 28px;
    font-weight: 800;
    letter-spacing: -0.025em;
    color: var(--t-primary);
    margin-bottom: 4px;
}

.del-sub {
    font-size: 14px;
    color: var(--t-secondary);
}

.sf-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: var(--r-pill);
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all var(--dur-fast) var(--ease-ios);
}

.sf-btn.primary { background: var(--c-blue); color: #fff; }
.sf-btn.primary:hover { background: #0066d6; }
.sf-btn.neutral { background: var(--c-surface); color: var(--t-primary); border: 0.5px solid var(--c-separator); box-shadow: var(--shadow-xs); }
.sf-btn.neutral:hover { background: var(--c-surface2); }
.sf-btn.success { background: var(--c-green); color: #fff; }
.sf-btn.success:hover { background: #2fb34f; }
.sf-btn.danger { background: var(--c-red); color: #fff; }
.sf-btn.danger:hover { background: #e03227; }

/* Stat Cards */
.stat-card {
    background: var(--c-surface);
    border-radius: var(--r-xl);
    padding: 16px 20px;
    box-shadow: var(--shadow-sm);
    border: 0.5px solid var(--c-separator);
    display: inline-flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 24px;
}

.stat-icon {
    width: 44px; height: 44px;
    border-radius: var(--r-sm);
    background: var(--c-red-light);
    color: var(--c-red);
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
}

.stat-num {
    font-size: 24px;
    font-weight: 800;
    font-family: var(--f-mono);
    color: var(--c-red);
    line-height: 1;
}

.stat-lbl {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--t-label);
    margin-top: 2px;
}

/* Search Shelf */
.search-shelf {
    display: flex;
    gap: 12px;
    align-items: center;
    margin-bottom: 20px;
}

.search-box {
    display: flex;
    align-items: center;
    gap: 10px;
    background: var(--c-surface);
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-pill);
    padding: 8px 16px;
    width: 320px;
    box-shadow: var(--shadow-xs);
}

.search-box i { color: var(--t-tertiary); font-size: 14px; }
.search-box input {
    border: none; outline: none; background: transparent;
    font-size: 14px; font-weight: 500; font-family: var(--f-system);
    color: var(--t-primary); width: 100%;
}

/* Alerts */
.sf-alert {
    display: flex; align-items: flex-start; gap: 12px;
    background: var(--c-surface);
    border-radius: var(--r-md);
    padding: 14px 16px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-xs);
    border: 0.5px solid var(--c-separator);
    border-left: 4px solid var(--c-blue);
    font-size: 14px;
}
.sf-alert.success { border-left-color: var(--c-green); }
.sf-alert.error   { border-left-color: var(--c-red); }

/* Table Container */
.table-panel {
    background: var(--c-surface);
    border-radius: var(--r-xl);
    border: 0.5px solid var(--c-separator);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}

.inv-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
    font-size: 14px;
}

.inv-table th {
    background: var(--c-surface2);
    padding: 12px 18px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--t-label);
    border-bottom: 0.5px solid var(--c-separator);
}

.inv-table td {
    padding: 14px 18px;
    border-bottom: 0.5px solid var(--c-separator);
    vertical-align: middle;
}

.inv-table tr:last-child td { border-bottom: none; }
.inv-table tr:hover td { background: rgba(0,122,255,0.02); }

.prod-thumb {
    width: 40px; height: 40px;
    border-radius: var(--r-sm);
    object-fit: cover;
    border: 0.5px solid var(--c-separator);
    background: var(--c-bg);
}

.prod-name { font-weight: 700; color: var(--t-primary); font-size: 14px; }
.prod-meta { font-size: 12px; color: var(--t-secondary); font-family: var(--f-mono); margin-top: 2px; }

.sf-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; border-radius: var(--r-pill);
    font-size: 12px; font-weight: 700;
}
.badge-deleted { background: var(--c-red-light); color: var(--c-red); }

.act-btns { display: flex; gap: 6px; justify-content: flex-end; }
.act-icon {
    width: 34px; height: 34px; border-radius: var(--r-sm);
    display: flex; align-items: center; justify-content: center;
    border: 0.5px solid var(--c-separator);
    background: var(--c-surface);
    color: var(--t-secondary);
    cursor: pointer; text-decoration: none;
    transition: all var(--dur-fast);
}
.act-icon:hover.restore { background: var(--c-green-light); color: var(--c-green); border-color: var(--c-green); }
.act-icon:hover.purge { background: var(--c-red-light); color: var(--c-red); border-color: var(--c-red); }

.txt-right { text-align: right; }
.txt-center { text-align: center; }
.num { font-family: var(--f-mono); font-weight: 600; }

/* Empty state */
.tbl-empty { text-align: center; padding: 64px 20px; }
.tbl-empty-icon { font-size: 40px; color: var(--t-tertiary); margin-bottom: 12px; }
.tbl-empty-title { font-size: 16px; font-weight: 700; color: var(--t-secondary); margin-bottom: 4px; }
.tbl-empty-sub { font-size: 13px; color: var(--t-tertiary); }

/* Pagination */
.pg-bar {
    display: flex; justify-content: space-between; align-items: center;
    padding: 14px 20px; background: var(--c-surface2);
    border-top: 0.5px solid var(--c-separator);
    font-size: 13px; color: var(--t-secondary);
}
</style>

<div class="del-root">
    <div class="del-wrap">
        
        <!-- Header -->
        <div class="del-header">
            <div>
                <div class="del-title"><i class="fa-solid fa-trash-arrow-up" style="color: var(--c-red); margin-right: 8px;"></i> Deleted Products</div>
                <div class="del-sub">Recycle bin for soft-deleted inventory products. Restore items back to active catalog or permanently purge them.</div>
            </div>
            <a href="<?= APP_URL ?>/inventory" class="sf-btn neutral">
                <i class="fa-solid fa-arrow-left"></i> Back to Inventory Catalog
            </a>
        </div>

        <!-- Alerts -->
        <?php if ($flashSuccess): ?>
        <div class="sf-alert success">
            <i class="fa-solid fa-circle-check" style="color: var(--c-green); font-size: 18px;"></i>
            <div>
                <strong>Success:</strong> <?= htmlspecialchars($flashSuccess) ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($flashError): ?>
        <div class="sf-alert error">
            <i class="fa-solid fa-circle-exclamation" style="color: var(--c-red); font-size: 18px;"></i>
            <div>
                <strong>Error:</strong> <?= htmlspecialchars($flashError) ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Stat Card -->
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-box-archive"></i></div>
            <div>
                <div class="stat-num"><?= number_format($totalItems) ?></div>
                <div class="stat-lbl">Soft-Deleted Products</div>
            </div>
        </div>

        <!-- Search Shelf -->
        <form method="GET" action="<?= APP_URL ?>/inventory/deleted" class="search-shelf">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search deleted product by name or SKU..." onchange="this.form.submit()">
            </div>
            <?php if (!empty($search)): ?>
                <a href="<?= APP_URL ?>/inventory/deleted" class="sf-btn neutral" style="padding: 8px 14px; font-size: 13px;">Clear Search</a>
            <?php endif; ?>
        </form>

        <!-- Main Table Panel -->
        <div class="table-panel">
            <table class="inv-table">
                <thead>
                    <tr>
                        <th style="width: 60px;" class="txt-center">#</th>
                        <th>Product Info</th>
                        <th>Category</th>
                        <th class="txt-right">Retail Price</th>
                        <th class="txt-center">Qty</th>
                        <th>Status</th>
                        <th class="txt-right" style="width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="7">
                            <div class="tbl-empty">
                                <div class="tbl-empty-icon"><i class="fa-solid fa-box-open"></i></div>
                                <div class="tbl-empty-title">No deleted products found</div>
                                <div class="tbl-empty-sub">Deleted products will be stored here for restoration.</div>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($items as $index => $item):
                            $img_src = empty($item->image_path) ? 'https://placehold.co/100x100/f2f2f7/8e8e93?text=' . urlencode(substr($item->name ?? 'P', 0, 1)) : APP_URL . '/uploads/products/' . basename($item->image_path);
                            $sku = !empty($item->item_code) ? $item->item_code : '-';
                            $price = floatval($item->selling_price ?? $item->price ?? 0);
                            $qty = intval($item->qty ?? 0);
                        ?>
                        <tr>
                            <td class="txt-center num" style="color: var(--t-tertiary);"><?= (($currentPage - 1) * $perPage) + $index + 1 ?></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <img src="<?= $img_src ?>" class="prod-thumb" alt="" onerror="this.src='https://placehold.co/100x100/f2f2f7/8e8e93?text=?'">
                                    <div>
                                        <div class="prod-name"><?= htmlspecialchars($item->name ?? 'Unnamed Product') ?></div>
                                        <div class="prod-meta"><?= htmlspecialchars($sku) ?><?= !empty($item->sample_code) ? ' · Code: ' . htmlspecialchars($item->sample_code) : '' ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="font-size: 13px; color: var(--t-secondary); font-weight: 500;">
                                    <?= htmlspecialchars($item->category_name ?? 'Uncategorized') ?>
                                </span>
                            </td>
                            <td class="txt-right num">Rs. <?= number_format($price, 2) ?></td>
                            <td class="txt-center num"><?= $qty ?></td>
                            <td>
                                <span class="sf-badge badge-deleted">
                                    <i class="fa-solid fa-circle" style="font-size: 6px;"></i> Deleted
                                </span>
                            </td>
                            <td class="txt-right">
                                <div class="act-btns">
                                    <!-- Restore Button -->
                                    <form method="POST" action="<?= APP_URL ?>/inventory/restore/<?= $item->id ?>" style="display:inline;" onsubmit="return confirm('Restore \'<?= htmlspecialchars(addslashes($item->name)) ?>\' back to active inventory catalog?');">
                                        <button type="submit" class="act-icon restore" title="Restore Product">
                                            <i class="fa-solid fa-rotate-left"></i>
                                        </button>
                                    </form>

                                    <!-- Permanent Delete Button -->
                                    <form method="POST" action="<?= APP_URL ?>/inventory/permanent_delete/<?= $item->id ?>" style="display:inline;" onsubmit="return confirm('Permanently purge \'<?= htmlspecialchars(addslashes($item->name)) ?>\'? If it contains transaction history, it will remain safely soft-deleted.');">
                                        <button type="submit" class="act-icon purge" title="Permanently Delete">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination Bar -->
            <?php if ($totalPages > 1): ?>
            <div class="pg-bar">
                <div>Showing Page <strong><?= $currentPage ?></strong> of <strong><?= $totalPages ?></strong> (Total <strong><?= $totalItems ?></strong> deleted items)</div>
                <div style="display: flex; gap: 6px;">
                    <?php if ($currentPage > 1): ?>
                        <a href="<?= APP_URL ?>/inventory/deleted?page=<?= $currentPage - 1 ?>&search=<?= urlencode($search) ?>" class="sf-btn neutral" style="padding: 6px 14px; font-size: 13px;">Previous</a>
                    <?php endif; ?>
                    <?php if ($currentPage < $totalPages): ?>
                        <a href="<?= APP_URL ?>/inventory/deleted?page=<?= $currentPage + 1 ?>&search=<?= urlencode($search) ?>" class="sf-btn neutral" style="padding: 6px 14px; font-size: 13px;">Next</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

    </div>
</div>
