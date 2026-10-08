<?php
/**
 * Route Selection Panel
 * Master Route Cards List Grid and Pagination Container (.pane-left)
 */
?>
<div class="pane-left">
    <div id="routeListItemsContainer">
        <?php 
        $routes = $data['routes'];
        include __DIR__ . '/_route_list_items.php'; 
        ?>
    </div>
    <div id="routePaginationContainer" style="width:100%; flex-shrink:0;">
        <?php 
        $pagination = $data['pagination'] ?? null;
        include __DIR__ . '/_pagination.php'; 
        ?>
    </div>
</div>
