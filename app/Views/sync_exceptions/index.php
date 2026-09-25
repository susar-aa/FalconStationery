<div class="content-wrapper">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title text-danger"><i class="fas fa-inbox me-2"></i>Sync Exceptions Inbox</h1>
            <p class="text-muted">Review, resolve, and replay operational sync failures across products, routes, customers, and accounting rules.</p>
        </div>
        <div>
            <a href="<?= APP_URL ?>/reptracking" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back to Tracking</a>
        </div>
    </div>

    <!-- Category Filter Cards -->
    <div class="row g-3 mb-4">
        <?php 
        $categories = [
            'total' => ['label' => 'All Pending', 'icon' => 'fa-layer-group', 'color' => 'primary'],
            'unmapped_product' => ['label' => 'Unmapped Products', 'icon' => 'fa-box-open', 'color' => 'warning'],
            'unknown_customer' => ['label' => 'Unknown Customers', 'icon' => 'fa-user-slash', 'color' => 'danger'],
            'route_not_found' => ['label' => 'Route Not Found', 'icon' => 'fa-route', 'color' => 'info'],
            'duplicate_event' => ['label' => 'Duplicate Events', 'icon' => 'fa-copy', 'color' => 'secondary'],
            'coa_not_configured' => ['label' => 'COA Missing', 'icon' => 'fa-file-invoice-dollar', 'color' => 'dark'],
            'stock_conflict' => ['label' => 'Stock Conflicts', 'icon' => 'fa-exclamation-triangle', 'color' => 'warning']
        ];
        foreach ($categories as $catKey => $catInfo):
            $catCount = $data['counts'][$catKey] ?? 0;
            $isActive = ($data['selected_category'] === $catKey || ($catKey === 'total' && empty($data['selected_category'])));
        ?>
        <div class="col-md-3 col-sm-6">
            <a href="<?= APP_URL ?>/syncexceptions<?= $catKey !== 'total' ? '?category=' . $catKey : '' ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-3 <?= $isActive ? 'border-start border-4 border-' . $catInfo['color'] : '' ?>">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small d-block"><?= $catInfo['label'] ?></span>
                            <h3 class="mb-0 fw-bold text-<?= $catInfo['color'] ?>"><?= number_format($catCount) ?></h3>
                        </div>
                        <div class="bg-light p-3 rounded-circle text-<?= $catInfo['color'] ?>">
                            <i class="fas <?= $catInfo['icon'] ?> fa-lg"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Exceptions Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0 text-secondary">
                <i class="fas fa-list me-2"></i>Pending Exception Queue
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Category</th>
                            <th>Entity</th>
                            <th>Rep / User</th>
                            <th>Error Details</th>
                            <th>Timestamp</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data['exceptions'])): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-check-circle text-success fa-3x mb-3 d-block"></i>
                                No pending sync exceptions found. All operational events are synchronized cleanly!
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($data['exceptions'] as $exc): ?>
                            <tr>
                                <td><span class="badge bg-secondary">#<?= $exc->id ?></span></td>
                                <td>
                                    <span class="badge bg-danger text-uppercase p-2">
                                        <?= str_replace('_', ' ', $exc->category) ?>
                                    </span>
                                </td>
                                <td><strong class="text-capitalize"><?= htmlspecialchars($exc->entity_type) ?></strong></td>
                                <td><i class="fas fa-user-circle me-1 text-muted"></i><?= htmlspecialchars($exc->rep_username ?? 'User #' . $exc->user_id) ?></td>
                                <td style="max-width:350px;">
                                    <div class="text-danger small fw-semibold"><?= htmlspecialchars($exc->error_message) ?></div>
                                    <?php if (!empty($exc->uuid)): ?>
                                        <code class="small text-muted d-block mt-1">UUID: <?= htmlspecialchars($exc->uuid) ?></code>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted"><?= date('Y-m-d H:i', strtotime($exc->created_at)) ?></td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-success me-1 btn-replay" data-id="<?= $exc->id ?>" onclick="replayException(<?= $exc->id ?>)">
                                        <i class="fas fa-play me-1"></i> Replay
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary btn-ignore" data-id="<?= $exc->id ?>" onclick="ignoreException(<?= $exc->id ?>)">
                                        <i class="fas fa-ban me-1"></i> Ignore
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function replayException(id) {
    if (!confirm('Replay this exception event now? Ensure master data configuration is set up.')) return;
    
    fetch('<?= APP_URL ?>/syncexceptions/replay', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': '<?= $_SESSION['csrf_token'] ?? '' ?>'
        },
        body: 'id=' + id + '&csrf_token=<?= $_SESSION['csrf_token'] ?? '' ?>'
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message);
            location.reload();
        } else {
            alert('Replay Error: ' + data.message);
        }
    })
    .catch(err => alert('Network or server error during replay.'));
}

function ignoreException(id) {
    if (!confirm('Mark this exception as ignored?')) return;
    
    fetch('<?= APP_URL ?>/syncexceptions/ignore', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': '<?= $_SESSION['csrf_token'] ?? '' ?>'
        },
        body: 'id=' + id + '&csrf_token=<?= $_SESSION['csrf_token'] ?? '' ?>'
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
}
</script>
