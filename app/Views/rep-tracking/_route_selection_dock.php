<?php
/**
 * Route Selection Panel - Floating Command Dock
 * Search Input, Route History/Active Routes toggle, Create Route, Binding Panel, Refresh
 */
$isHistory = $isHistory ?? ($data['is_history'] ?? false);
?>
<div class="cmd-bar">
    <div id="auto-evt-div-27" class="cmd-search">
        <i class="ph ph-magnifying-glass"></i>
        <input type="text" id="floatingSearchInput" name="search_route_query_no_autofill" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" placeholder="Search routes...">
    </div>
    <div class="cmd-divider"></div>
    
    <?php if ($isHistory): ?>
        <a href="<?= APP_URL ?>/RepTracking/index" class="cmd-btn" title="Active Routes">
            <i class="ph-bold ph-arrow-left"></i>
            <span>Active Routes</span>
        </a>
    <?php else: ?>
        <a href="<?= APP_URL ?>/RepTracking/history" class="cmd-btn" title="Route History">
            <i class="ph-bold ph-clock-counter-clockwise"></i>
            <span>Route History</span>
        </a>
        <button id="auto-evt-button-28" type="button" class="cmd-btn" title="Create Route Manually">
            <i class="ph-bold ph-plus-circle"></i>
            <span>Create Route</span>
        </button>
    <?php endif; ?>
    
    <button type="button" id="btnOpenRouteBinding" class="cmd-btn" title="Route Binding Panel">
        <i class="ph-bold ph-link"></i>
        <span>Binding Panel</span>
    </button>
    <div class="cmd-divider"></div>
    
    <button id="auto-evt-button-29" type="button" class="cmd-icon" title="Refresh page">
        <i class="ph ph-arrows-clockwise"></i>
    </button>
</div>
