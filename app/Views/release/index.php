<!-- Inter Font & FontAwesome Icons -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
/* ============================================================
   SF PRO + APPLE DESIGN LANGUAGE — APP RELEASE MANAGEMENT
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
    --c-green:        #34c759;
    --c-green-light:  #e6f9ec;
    --c-orange:       #ff9500;
    --c-orange-light: #fff4e5;
    --c-red:          #ff3b30;
    --c-red-light:    #fff0ef;
    --c-purple:       #af52de;
    --c-purple-light: #f5e8ff;

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

.rel-root {
    font-family: var(--f-system);
    font-size: 14px;
    color: var(--t-primary);
    -webkit-font-smoothing: antialiased;
}

.sf-page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}
.sf-page-title h1 {
    font-size: 28px;
    font-weight: 800;
    margin: 0 0 4px 0;
    letter-spacing: -0.02em;
}
.sf-page-title p {
    margin: 0;
    color: var(--t-secondary);
    font-size: 14px;
}

/* 2 Column Layout (Main Table + Timeline) */
.rel-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 20px;
}

@media (max-width: 1100px) {
    .rel-grid {
        grid-template-columns: 1fr;
    }
}

.sf-panel {
    background: var(--c-surface);
    border-radius: var(--r-lg);
    border: 0.5px solid var(--c-separator);
    box-shadow: var(--shadow-sm);
    padding: 24px;
}
.sf-panel-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--t-primary);
    margin: 0 0 20px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Forms */
.form-group {
    margin-bottom: 18px;
}
.form-group label {
    display: block;
    font-size: 12px;
    font-weight: 700;
    color: var(--t-secondary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 6px;
}
.form-control-sf {
    width: 100%;
    padding: 10px 14px;
    font-size: 14px;
    font-family: var(--f-system);
    background: var(--c-surface2);
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-sm);
    color: var(--t-primary);
    outline: none;
    transition: border-color var(--dur-fast) ease;
    box-sizing: border-box;
}
.form-control-sf:focus {
    border-color: var(--c-blue);
    background: var(--c-surface);
}

.checkbox-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 20px;
}
.checkbox-label {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13.5px;
    font-weight: 600;
    cursor: pointer;
    color: var(--t-primary);
}
.checkbox-label input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: var(--c-blue);
    cursor: pointer;
}

/* Buttons */
.btn-sf {
    padding: 9px 18px;
    font-size: 13px;
    font-weight: 600;
    border-radius: var(--r-pill);
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: all var(--dur-fast) ease;
    font-family: var(--f-system);
}
.btn-sf-primary { background: var(--c-blue); color: #fff; }
.btn-sf-primary:hover { opacity: 0.9; transform: translateY(-1px); color: #fff; }
.btn-sf-secondary { background: var(--c-fill); color: var(--t-primary); }
.btn-sf-secondary:hover { background: var(--c-fill2); }
.btn-sf-danger { background: var(--c-red-light); color: var(--c-red); }
.btn-sf-danger:hover { background: var(--c-red); color: #fff; }

/* Table */
.table-panel {
    background: var(--c-surface);
    border-radius: var(--r-lg);
    border: 0.5px solid var(--c-separator);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}
.sf-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
    font-size: 13.5px;
}
.sf-table th {
    background: var(--c-surface2);
    padding: 14px 18px;
    font-size: 11px;
    font-weight: 700;
    color: var(--t-secondary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 0.5px solid var(--c-separator);
}
.sf-table td {
    padding: 14px 18px;
    border-bottom: 0.5px solid var(--c-separator);
    vertical-align: middle;
}
.sf-table tr:last-child td { border-bottom: none; }
.sf-table tr:hover td { background: var(--c-fill); }

/* Badges */
.sf-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: var(--r-pill);
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
}
.sf-badge .dot { width: 6px; height: 6px; border-radius: 50%; }
.badge-latest { background: var(--c-blue-light); color: var(--c-blue); }
.badge-latest .dot { background: var(--c-blue); }
.badge-force { background: var(--c-red-light); color: var(--c-red); }
.badge-force .dot { background: var(--c-red); }
.badge-optional { background: var(--c-fill2); color: var(--t-secondary); }

/* Timeline */
.timeline {
    list-style: none;
    padding: 0 0 0 20px;
    margin: 0;
    position: relative;
}
.timeline::before {
    content: '';
    position: absolute;
    top: 6px;
    bottom: 6px;
    left: 7px;
    width: 2px;
    background: var(--c-separator);
}
.timeline-item {
    position: relative;
    margin-bottom: 20px;
}
.timeline-item:last-child { margin-bottom: 0; }
.timeline-marker {
    position: absolute;
    left: -20px;
    top: 5px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: var(--t-tertiary);
    border: 2px solid var(--c-surface);
}
.timeline-item.is-latest .timeline-marker {
    background: var(--c-blue);
    box-shadow: 0 0 0 3px var(--c-blue-light);
}
.timeline-item.is-forced .timeline-marker {
    background: var(--c-red);
}
.timeline-box {
    background: var(--c-surface2);
    border: 0.5px solid var(--c-separator);
    border-radius: var(--r-md);
    padding: 12px 14px;
}
.timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
}
.timeline-version {
    font-weight: 700;
    font-size: 13.5px;
    color: var(--t-primary);
}
.timeline-date {
    font-size: 11px;
    color: var(--t-label);
}
.timeline-notes {
    font-size: 12px;
    color: var(--t-secondary);
    line-height: 1.4;
    white-space: pre-wrap;
}

/* Popup Modal */
.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9999;
    background: rgba(0,0,0,0.45);
    backdrop-filter: blur(10px);
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity var(--dur-fast) ease;
}
.modal-overlay.open {
    display: flex;
    opacity: 1;
}
.modal-box {
    background: var(--c-surface);
    width: 520px;
    max-width: 90vw;
    border-radius: var(--r-xl);
    padding: 28px;
    border: 0.5px solid var(--c-separator);
    box-shadow: var(--shadow-xl);
    transform: scale(0.95);
    transition: transform var(--dur-fast) var(--ease-spring);
    max-height: 90vh;
    overflow-y: auto;
}
.modal-overlay.open .modal-box {
    transform: scale(1);
}
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.modal-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 700;
    color: var(--t-primary);
}
.modal-close-btn {
    background: var(--c-fill);
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    color: var(--t-secondary);
    font-size: 16px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background var(--dur-fast) ease;
}
.modal-close-btn:hover {
    background: var(--c-fill2);
    color: var(--t-primary);
}

/* Alerts */
.sf-alert {
    padding: 12px 16px;
    border-radius: var(--r-md);
    margin-bottom: 20px;
    font-size: 13.5px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}
.sf-alert-success { background: var(--c-green-light); color: #166534; border: 0.5px solid rgba(52,199,89,0.3); }
.sf-alert-error { background: var(--c-red-light); color: #991b1b; border: 0.5px solid rgba(255,59,48,0.3); }
</style>

<div class="rel-root">
    <!-- Header with Action Button to Open Upload Modal -->
    <div class="sf-page-header">
        <div class="sf-page-title">
            <h1>App Release Management</h1>
            <p>Manage client-side updates, APK distributions, and version rollout strategies.</p>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <button type="button" class="btn-sf btn-sf-primary" onclick="openUploadModal()">
                <i class="fa-solid fa-cloud-arrow-up"></i> Deploy New Release
            </button>
            <a href="<?= APP_URL ?>/release/api_latest_version" target="_blank" class="btn-sf btn-sf-secondary">
                <i class="fa-solid fa-code"></i> View API JSON
            </a>
        </div>
    </div>

    <?php if(!empty($data['success'])): ?>
        <div class="sf-alert sf-alert-success" id="syncSuccessAlert">
            <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($data['success']) ?>
        </div>
    <?php endif; ?>
    <?php if(!empty($data['error'])): ?>
        <div class="sf-alert sf-alert-error" id="syncErrorAlert">
            <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($data['error']) ?>
        </div>
    <?php endif; ?>

    <div class="sf-alert sf-alert-error" id="ajaxErrorAlert" style="display:none;">
        <i class="fa-solid fa-triangle-exclamation"></i> <span id="ajaxErrorMsg"></span>
    </div>

    <div class="rel-grid">
        <!-- 1. Deployment History Table Panel (Main Area) -->
        <div class="table-panel">
            <div style="padding: 20px 24px; border-bottom: 0.5px solid var(--c-separator); display: flex; justify-content: space-between; align-items: center;">
                <h3 class="sf-panel-title" style="margin: 0;">
                    <i class="fa-solid fa-clock-rotate-left" style="color: var(--c-blue);"></i> Deployment History
                </h3>
                <button type="button" class="btn-sf btn-sf-primary" style="font-size: 12px; padding: 6px 14px;" onclick="openUploadModal()">
                    <i class="fa-solid fa-plus"></i> New Release
                </button>
            </div>

            <?php if (empty($data['releases'])): ?>
                <div style="text-align: center; padding: 60px 20px; color: var(--t-secondary);">
                    <i class="fa-solid fa-cloud-slash" style="font-size: 42px; opacity: 0.3; margin-bottom: 14px; display: block;"></i>
                    <div style="font-size: 16px; font-weight: 700; color: var(--t-primary); margin-bottom: 4px;">No Releases Deployed Yet</div>
                    <div style="font-size: 13px; margin-bottom: 16px;">Upload your first APK package to initialize the update delivery system.</div>
                    <button type="button" class="btn-sf btn-sf-primary" onclick="openUploadModal()">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Upload First Release
                    </button>
                </div>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table class="sf-table">
                        <thead>
                            <tr>
                                <th style="padding-left: 24px;">Version & Package</th>
                                <th>Status</th>
                                <th>Release Notes</th>
                                <th>Upload Date</th>
                                <th>Downloads</th>
                                <th style="text-align: right; padding-right: 24px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['releases'] as $r): ?>
                                <tr>
                                    <td style="padding-left: 24px; font-weight: 700; color: var(--t-primary);">
                                        v<?= htmlspecialchars($r->version) ?>
                                        <?php if (!empty($r->build_version)): ?>
                                            <span style="font-weight: 500; color: var(--t-secondary); font-size: 12px;">(Build <?= $r->build_version ?>)</span>
                                        <?php endif; ?>
                                        <?php if (!empty($r->package_name)): ?>
                                            <div style="font-size: 11px; color: var(--t-label); font-weight: 500; margin-top: 2px;"><?= htmlspecialchars($r->package_name) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="display: flex; flex-direction: column; gap: 4px; align-items: flex-start;">
                                            <?php if ($r->is_latest == 1): ?>
                                                <span class="sf-badge badge-latest"><span class="dot"></span> Latest Active</span>
                                            <?php endif; ?>
                                            <?php if ($r->force_update == 1): ?>
                                                <span class="sf-badge badge-force"><span class="dot"></span> Force Update</span>
                                            <?php else: ?>
                                                <span class="sf-badge badge-optional">Optional</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 13px; color: var(--t-secondary); max-width: 320px; white-space: pre-wrap; line-height: 1.4;"><?= htmlspecialchars($r->release_notes) ?></div>
                                    </td>
                                    <td style="color: var(--t-secondary); font-size: 13px;">
                                        <?= date('M d, Y H:i', strtotime($r->created_at)) ?>
                                    </td>
                                    <td>
                                        <a href="<?= APP_URL ?>/releases/app-v<?= htmlspecialchars($r->version) ?>.apk" class="btn-sf btn-sf-secondary" style="padding: 5px 12px; font-size: 12px;">
                                            <i class="fa-solid fa-download"></i> Download APK
                                        </a>
                                    </td>
                                    <td style="text-align: right; padding-right: 24px;">
                                        <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                            <?php if ($r->is_latest == 0): ?>
                                                <a href="<?= APP_URL ?>/release/set_latest/<?= $r->id ?>" class="btn-sf btn-sf-secondary" style="padding: 5px 12px; font-size: 12px;" title="Set as Latest Active version">
                                                    <i class="fa-solid fa-star"></i> Activate
                                                </a>
                                            <?php endif; ?>
                                            <a href="<?= APP_URL ?>/release/delete/<?= $r->id ?>" class="btn-sf btn-sf-danger" style="padding: 5px 12px; font-size: 12px;" onclick="return confirm('Are you sure you want to permanently delete and remove release v<?= htmlspecialchars($r->version) ?>? This cannot be undone.');">
                                                <i class="fa-solid fa-trash"></i> Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- 2. Release Timeline Panel (Right Sidebar) -->
        <div class="sf-panel timeline-col" style="height: fit-content;">
            <h3 class="sf-panel-title">
                <i class="fa-solid fa-code-branch" style="color: var(--c-blue);"></i> Release Timeline
            </h3>

            <?php if (empty($data['releases'])): ?>
                <div style="text-align: center; padding: 20px 0; color: var(--t-secondary); font-size: 13px;">
                    No milestone timeline available.
                </div>
            <?php else: ?>
                <ul class="timeline">
                    <?php foreach($data['releases'] as $r): ?>
                        <?php 
                            $class = '';
                            if ($r->is_latest == 1) $class .= ' is-latest';
                            if ($r->force_update == 1) $class .= ' is-forced';
                        ?>
                        <li class="timeline-item<?= $class ?>">
                            <div class="timeline-marker"></div>
                            <div class="timeline-box">
                                <div class="timeline-header">
                                    <span class="timeline-version">v<?= htmlspecialchars($r->version) ?> <?= !empty($r->build_version) ? '(Build ' . $r->build_version . ')' : '' ?></span>
                                    <span class="timeline-date"><?= date('M d, Y', strtotime($r->created_at)) ?></span>
                                </div>
                                <div class="timeline-notes"><?= htmlspecialchars($r->release_notes ?: 'Initial system release/upload.') ?></div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Upload Release Popup Modal Panel -->
<div class="modal-overlay" id="uploadReleaseModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fa-solid fa-cloud-arrow-up" style="color: var(--c-blue); margin-right: 8px;"></i> Deploy New Release</h3>
            <button type="button" class="modal-close-btn" onclick="closeUploadModal()">&times;</button>
        </div>
        
        <form action="<?= APP_URL ?>/release/upload" method="POST" enctype="multipart/form-data" id="uploadReleaseForm">
            <div class="form-group">
                <label for="apk">APK Binary File <span style="color: var(--c-red);">*</span></label>
                <input type="file" name="apk" id="apk" class="form-control-sf" accept=".apk" required style="padding: 8px;">
                <div style="font-size: 11px; color: var(--t-label); margin-top: 4px;">Upload the compiled Android app binary package.</div>
                <div style="font-size: 11.5px; color: #166534; background: var(--c-green-light); border: 0.5px solid rgba(52,199,89,0.3); padding: 10px; border-radius: var(--r-sm); margin-top: 8px; line-height: 1.4;">
                    <i class="fa-solid fa-circle-info"></i> APK metadata (Package Name, Version Name, Code) will be extracted automatically from the binary.
                </div>
            </div>

            <div class="form-group">
                <label for="release_notes">Release Notes & Changelog</label>
                <textarea name="release_notes" id="release_notes" class="form-control-sf" rows="5" placeholder="• Fixed crash on Billing POS screen&#10;• Added Sample Code search indexing&#10;• General performance optimizations"></textarea>
            </div>

            <div class="checkbox-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_latest" value="1" checked>
                    <span>Mark as Active/Latest Release</span>
                </label>
            </div>

            <!-- Upload Progress Bar (AJAX Mode) -->
            <div id="uploadProgressContainer" style="display: none; margin-bottom: 18px;">
                <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 6px; font-weight: 600;">
                    <span style="color: var(--t-secondary);">Uploading APK File...</span>
                    <span id="uploadPercent" style="color: var(--c-blue);">0%</span>
                </div>
                <div style="width: 100%; height: 6px; background: var(--c-fill2); border-radius: var(--r-pill); overflow: hidden;">
                    <div id="uploadProgressBar" style="width: 0%; height: 100%; background: var(--c-blue); transition: width 0.1s ease;"></div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 0.5px solid var(--c-separator);">
                <button type="button" class="btn-sf btn-sf-secondary" onclick="closeUploadModal()">Cancel</button>
                <button type="submit" class="btn-sf btn-sf-primary" id="btnUploadSubmit">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Upload and Deploy Release
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openUploadModal() {
    document.getElementById('uploadReleaseModal').classList.add('open');
}
function closeUploadModal() {
    document.getElementById('uploadReleaseModal').classList.remove('open');
}

document.addEventListener("DOMContentLoaded", function() {
    const uploadForm = document.getElementById("uploadReleaseForm");
    
    if (uploadForm) {
        uploadForm.addEventListener("submit", function(e) {
            e.preventDefault();
            
            const fileInput = document.getElementById("apk");
            const releaseNotesInput = document.getElementById("release_notes");
            const forceUpdateInput = uploadForm.querySelector('input[name="force_update"]');
            const isLatestInput = uploadForm.querySelector('input[name="is_latest"]');
            
            if (!fileInput.files || fileInput.files.length === 0) {
                alert("Please select an APK file to upload.");
                return;
            }

            const file = fileInput.files[0];
            const releaseNotes = releaseNotesInput.value;
            const forceUpdate = "1"; // Always force update
            const isLatest = isLatestInput.checked ? "1" : "0";

            const tempToken = "temp_" + Math.random().toString(36).substring(2, 15) + "_" + Date.now();

            const progressContainer = document.getElementById("uploadProgressContainer");
            const progressBar = document.getElementById("uploadProgressBar");
            const percentText = document.getElementById("uploadPercent");
            const submitBtn = document.getElementById("btnUploadSubmit");
            const errorAlert = document.getElementById("ajaxErrorAlert");
            const errorMsgSpan = document.getElementById("ajaxErrorMsg");

            const syncSuccessAlert = document.getElementById("syncSuccessAlert");
            const syncErrorAlert = document.getElementById("syncErrorAlert");
            if (syncSuccessAlert) syncSuccessAlert.style.display = 'none';
            if (syncErrorAlert) syncErrorAlert.style.display = 'none';
            errorAlert.style.display = 'none';

            progressContainer.style.display = 'block';
            progressBar.style.width = '0%';
            percentText.innerText = '0%';
            submitBtn.disabled = true;

            const CHUNK_SIZE = 1024 * 1024;
            const totalChunks = Math.ceil(file.size / CHUNK_SIZE);
            let chunkIndex = 0;

            function uploadNextChunk() {
                const start = chunkIndex * CHUNK_SIZE;
                const end = Math.min(start + CHUNK_SIZE, file.size);
                const chunk = file.slice(start, end);

                const chunkData = new FormData();
                chunkData.append("temp_token", tempToken);
                chunkData.append("chunk_index", chunkIndex);
                chunkData.append("chunk", chunk);

                const xhr = new XMLHttpRequest();
                xhr.open("POST", "<?= APP_URL ?>/release/upload_chunk", true);
                xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        const overallLoaded = (chunkIndex * CHUNK_SIZE) + evt.loaded;
                        const overallPercent = Math.round((overallLoaded / file.size) * 100);
                        progressBar.style.width = overallPercent + '%';
                        percentText.innerText = overallPercent + '%';
                    }
                });

                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.success) {
                                    chunkIndex++;
                                    if (chunkIndex < totalChunks) {
                                        uploadNextChunk();
                                    } else {
                                        assembleUploadedChunks();
                                    }
                                } else {
                                    showError("Chunk upload error: " + response.error);
                                }
                            } catch (e) {
                                showError("Failed to parse server chunk response: " + xhr.responseText);
                            }
                        } else {
                            showError("Chunk upload request failed with status: " + xhr.status);
                        }
                    }
                };

                xhr.send(chunkData);
            }

            function assembleUploadedChunks() {
                percentText.innerText = "Assembling & Parsing metadata...";
                progressBar.style.width = '100%';

                const assembleData = new FormData();
                assembleData.append("temp_token", tempToken);
                assembleData.append("release_notes", releaseNotes);
                assembleData.append("force_update", forceUpdate);
                assembleData.append("is_latest", isLatest);
                assembleData.append("total_chunks", totalChunks);

                const xhr = new XMLHttpRequest();
                xhr.open("POST", "<?= APP_URL ?>/release/assemble_chunks", true);
                xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        submitBtn.disabled = false;
                        progressContainer.style.display = 'none';

                        if (xhr.status === 200) {
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.success) {
                                    alert(response.message || "Upload and assembly completed successfully!");
                                    closeUploadModal();
                                    window.location.reload();
                                } else {
                                    showError("Assembly failed: " + response.error);
                                }
                            } catch (e) {
                                showError("Failed to parse assembly response: " + xhr.responseText);
                            }
                        } else {
                            showError("Assemble request failed with status: " + xhr.status);
                        }
                    }
                };

                xhr.send(assembleData);
            }

            function showError(msg) {
                console.error(msg);
                submitBtn.disabled = false;
                progressContainer.style.display = 'none';
                errorMsgSpan.innerText = msg;
                errorAlert.style.display = 'flex';
                errorAlert.scrollIntoView({ behavior: 'smooth' });
            }

            uploadNextChunk();
        });
    }
});
</script>
