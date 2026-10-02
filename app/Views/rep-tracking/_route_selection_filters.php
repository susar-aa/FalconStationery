<?php
/**
 * Route Selection Panel - Filters Bar
 * Representative, Route, Date, Territory Filters & Clear Button
 */
?>
<div class="global-status-filter-bar" style="display: flex; gap: 12px; background: var(--c-surface); border: 0.5px solid var(--c-separator); border-radius: var(--r-md); padding: 12px 20px; margin-bottom: 20px; box-shadow: var(--shadow-sm); align-items: center; justify-content: space-between; flex-wrap: wrap;">
    <div style="display: flex; gap: 16px; align-items: center; flex-wrap: wrap; flex: 1;">
        <!-- Rep-wise filter -->
        <div style="display: flex; flex-direction: column; gap: 4px;">
            <label style="font-size: 10px; font-weight: 700; color: var(--t-label); text-transform: uppercase; letter-spacing: 0.05em;">Representative</label>
            <select id="filterRepSelect" style="padding: 6px 12px; border: 0.5px solid var(--c-separator); border-radius: var(--r-xs); font-size: 13px; background: var(--c-surface2); color: var(--t-primary); min-width: 160px; outline: none;">
                <option value="">All Representatives</option>
                <?php
                $db = new Database();
                $db->query("
                    SELECT DISTINCT COALESCE(e.first_name, u.username) as first_name, COALESCE(e.last_name, '') as last_name
                    FROM rep_daily_routes r
                    LEFT JOIN users u ON r.user_id = u.id
                    LEFT JOIN employees e ON u.email = e.email
                    UNION
                    SELECT DISTINCT COALESCE(e.first_name, u.username) as first_name, COALESCE(e.last_name, '') as last_name
                    FROM users u
                    LEFT JOIN employees e ON u.employee_id = e.id
                    WHERE u.role = 'rep' AND (u.status IS NULL OR u.status = 'Active')
                ");
                $dbReps = $db->resultSet() ?: [];
                $repNames = [];
                foreach ($dbReps as $r) {
                    $fullName = trim($r->first_name . ' ' . $r->last_name);
                    if ($fullName !== '' && !in_array($fullName, $repNames)) {
                        $repNames[] = $fullName;
                    }
                }
                sort($repNames);
                foreach ($repNames as $name): ?>
                    <option value="<?= htmlspecialchars($name) ?>"><?= htmlspecialchars($name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- Route-wise filter -->
        <div style="display: flex; flex-direction: column; gap: 4px;">
            <label style="font-size: 10px; font-weight: 700; color: var(--t-label); text-transform: uppercase; letter-spacing: 0.05em;">Route</label>
            <select id="filterRouteSelect" style="padding: 6px 12px; border: 0.5px solid var(--c-separator); border-radius: var(--r-xs); font-size: 13px; background: var(--c-surface2); color: var(--t-primary); min-width: 160px; outline: none;">
                <option value="">All Routes</option>
                <?php 
                $uniqueRouteNames = [];
                foreach ($data['routes'] as $r) {
                    if (!in_array($r->route_name, $uniqueRouteNames)) {
                        $uniqueRouteNames[] = $r->route_name;
                    }
                }
                sort($uniqueRouteNames);
                foreach ($uniqueRouteNames as $name): ?>
                    <option value="<?= htmlspecialchars($name) ?>"><?= htmlspecialchars($name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- Date-wise filter -->
        <div style="display: flex; flex-direction: column; gap: 4px;">
            <label style="font-size: 10px; font-weight: 700; color: var(--t-label); text-transform: uppercase; letter-spacing: 0.05em;">Date</label>
            <input type="date" id="filterDateInput" style="padding: 5px 12px; border: 0.5px solid var(--c-separator); border-radius: var(--r-xs); font-size: 13px; background: var(--c-surface2); color: var(--t-primary); min-width: 140px; outline: none;">
        </div>
        <!-- Territory-wise filter -->
        <div style="display: flex; flex-direction: column; gap: 4px;">
            <label style="font-size: 10px; font-weight: 700; color: var(--t-label); text-transform: uppercase; letter-spacing: 0.05em;">Territory</label>
            <select id="filterTerritorySelect" style="padding: 6px 12px; border: 0.5px solid var(--c-separator); border-radius: var(--r-xs); font-size: 13px; background: var(--c-surface2); color: var(--t-primary); min-width: 160px; outline: none;">
                <option value="">All Territories</option>
                <?php foreach ($data['mca_areas'] as $area): ?>
                    <option value="<?= htmlspecialchars($area->name) ?>"><?= htmlspecialchars($area->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div style="display: flex; align-items: flex-end; align-self: flex-end;">
        <button id="auto-evt-button-1" type="button" style="padding: 7px 14px; border: 0.5px solid var(--c-separator); background: var(--c-surface2); color: var(--t-secondary); border-radius: var(--r-xs); font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: all var(--dur-fast);">
            <i class="ph ph-arrow-counter-clockwise"></i> Clear Filters
        </button>
    </div>
</div>
