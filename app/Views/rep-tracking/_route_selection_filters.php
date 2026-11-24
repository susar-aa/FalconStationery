<?php
/**
 * Route Selection Panel - Filters Bar
 * Representative, Route, Date, Territory Filters & Clear Button
 */
?>
<div class="global-status-filter-bar">
    <div class="filter-controls-group">
        <!-- Rep-wise filter -->
        <div class="filter-control-item">
            <label class="filter-label">
                <i class="ph ph-user"></i> Representative
            </label>
            <div class="filter-select-wrapper">
                <select id="filterRepSelect" class="filter-input-select">
                    <option value="">All Representatives</option>
                    <?php
                    $repNames = [];
                    $filterReps = !empty($data['filter_reps']) ? $data['filter_reps'] : (!empty($data['reps']) ? $data['reps'] : []);
                    foreach ($filterReps as $r) {
                        $fullName = trim(($r->first_name ?? '') . ' ' . ($r->last_name ?? ''));
                        if ($fullName === '' && !empty($r->username)) {
                            $fullName = trim($r->username);
                        }
                        if ($fullName !== '' && !in_array($fullName, $repNames)) {
                            $repNames[] = $fullName;
                        }
                    }
                    sort($repNames);
                    foreach ($repNames as $name): ?>
                        <option value="<?= htmlspecialchars($name) ?>"><?= htmlspecialchars($name) ?></option>
                    <?php endforeach; ?>
                </select>
                <i class="ph ph-caret-down filter-caret"></i>
            </div>
        </div>

        <!-- Route-wise filter -->
        <div class="filter-control-item">
            <label class="filter-label">
                <i class="ph ph-path"></i> Route
            </label>
            <div class="filter-select-wrapper">
                <select id="filterRouteSelect" class="filter-input-select">
                    <option value="">All Routes</option>
                    <?php 
                    $uniqueRouteNames = [];
                    $sourceRoutes = !empty($data['all_routes']) ? $data['all_routes'] : ($data['routes'] ?? []);
                    foreach ($sourceRoutes as $r) {
                        $rName = is_object($r) ? ($r->route_name ?? '') : '';
                        if (!empty($rName) && !in_array($rName, $uniqueRouteNames)) {
                            $uniqueRouteNames[] = $rName;
                        }
                    }
                    sort($uniqueRouteNames);
                    foreach ($uniqueRouteNames as $name): ?>
                        <option value="<?= htmlspecialchars($name) ?>"><?= htmlspecialchars($name) ?></option>
                    <?php endforeach; ?>
                </select>
                <i class="ph ph-caret-down filter-caret"></i>
            </div>
        </div>

        <!-- Date-wise filter -->
        <div class="filter-control-item">
            <label class="filter-label">
                <i class="ph ph-calendar-blank"></i> Date
            </label>
            <div class="filter-select-wrapper">
                <input type="date" id="filterDateInput" class="filter-input-date">
            </div>
        </div>

        <!-- Territory-wise filter -->
        <div class="filter-control-item">
            <label class="filter-label">
                <i class="ph ph-map-pin"></i> Territory
            </label>
            <div class="filter-select-wrapper">
                <select id="filterTerritorySelect" class="filter-input-select">
                    <option value="">All Territories</option>
                    <?php foreach ($data['mca_areas'] as $area): ?>
                        <option value="<?= htmlspecialchars($area->name) ?>"><?= htmlspecialchars($area->name) ?></option>
                    <?php endforeach; ?>
                </select>
                <i class="ph ph-caret-down filter-caret"></i>
            </div>
        </div>
    </div>

    <div class="filter-action-group">
        <button id="auto-evt-button-1" type="button" class="btn-clear-filters">
            <i class="ph ph-arrow-counter-clockwise"></i> Clear Filters
        </button>
    </div>
</div>
