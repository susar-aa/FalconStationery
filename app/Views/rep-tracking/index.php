<?php
/**
 * Rep Tracking Master View
 * Split into modular panels:
 * 1. Route Selection Panel (_route_selection_filters.php, _route_selection_panel.php, _route_selection_dock.php)
 * 2. Route Workflow Panel (_route_workflow_panel.php)
 */
include __DIR__ . '/index.css.php';
?>

<div class="header-actions" style="margin-bottom: 15px; display: flex; justify-content: <?= $isHistory ? 'flex-start' : 'flex-end' ?>; align-items: center; flex-wrap: wrap; gap: 10px;">
    <?php if ($isHistory): ?>
        <div>
            <a href="<?= APP_URL ?>/RepTracking/index" style="padding: 10px 18px; border: none; background: #64748b; color: #fff; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 13px; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px rgba(100, 116, 139, 0.2); transition: all 0.2s ease; text-decoration: none;">
                <i class="ph-bold ph-arrow-left"></i> Back to Control Panel
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- ROUTE SELECTION PANEL: Global Filter Bar -->
<?php include __DIR__ . '/_route_selection_filters.php'; ?>

<!-- APP MAIN WORKSPACE -->
<div class="app-workspace">
    <!-- 1. ROUTE SELECTION PANEL (Left Pane: Master Route Cards & Pagination) -->
    <?php include __DIR__ . '/_route_selection_panel.php'; ?>

    <!-- 2. ROUTE WORKFLOW PANEL (Middle Pane: Workspace Header, Collapsible Sidebar, Tabs 1-12) -->
    <?php include __DIR__ . '/_route_workflow_panel.php'; ?>
</div>

<!-- ROUTE SELECTION PANEL: Floating Command Dock Bar -->
<?php include __DIR__ . '/_route_selection_dock.php'; ?>

<!-- MODAL DIALOGS -->
<?php include __DIR__ . '/_modals.php'; ?>

<!-- CLIENT-SIDE EVENT HANDLERS & LOGIC -->
<?php include __DIR__ . '/index.js.php'; ?>
