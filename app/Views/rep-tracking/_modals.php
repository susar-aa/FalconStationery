<!-- Centered Invoice Popup Modal -->
    <!-- Global Context Menu Backdrop Blur -->
    <div id="menuBackdrop" onclick="closeAllDotsMenus()" style="display: none; position: fixed; inset: 0; z-index: 999; background: rgba(0, 0, 0, 0.08); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); transition: all 0.2s ease;"></div>

    <div class="modal-backdrop" id="invoiceSliderBackdrop" style="display: none; z-index: 2000;">
        <div class="modal-panel" style="max-width: 950px; width: 90%; height: 85vh; display: flex; flex-direction: column; position: relative;">
            <button onclick="closeInvoiceSlider()" style="position: absolute; top: 12px; right: 16px; background: var(--c-fill); border: none; font-size: 16px; font-weight: bold; color: var(--t-secondary); cursor: pointer; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 10;">✕</button>
            <iframe id="invoiceIframe" src="about:blank" style="width: 100%; flex: 1; border: none; border-radius: var(--r-lg); background: #fff;"></iframe>
        </div>
    </div>

    <!-- Secure Delete Confirmation Modal -->
    <div class="modal-backdrop" id="deleteConfirmModal" style="display: none; z-index: 2005;">
        <div class="modal-panel">
            <div class="modal-header" style="background: var(--c-red-light); color: var(--c-red); border-bottom: 0.5px solid var(--c-separator); padding: 16px 20px;">
                <h3 style="margin:0; font-size:16px; font-weight:700;"><i class="ph ph-warning-octagon"></i> Secure Deletion</h3>
            </div>
            <div style="padding: 20px; display: flex; flex-direction: column; gap: 15px;">
                <p style="margin:0; font-size:13px; color:var(--t-secondary);">
                    You are about to delete Sales Order <strong id="deleteTargetInvNum" style="color:var(--t-primary);"></strong>. This will permanently reverse general ledger entries and restore stock to inventory.
                </p>
                <div>
                    <label style="font-weight:bold; font-size:11px; text-transform:uppercase; color:var(--t-secondary); display:block; margin-bottom:4px;">Administrator Password *</label>
                    <input type="password" id="deleteConfirmPassword" placeholder="Enter password" style="width:100%; padding:10px; border:1px solid var(--c-separator); border-radius:var(--r-md); background:var(--c-bg); color:var(--t-primary); font-size:13px;" required>
                </div>
                <div>
                    <label style="font-weight:bold; font-size:11px; text-transform:uppercase; color:var(--t-secondary); display:block; margin-bottom:4px;">Reason for Deletion *</label>
                    <textarea id="deleteConfirmReason" placeholder="Reason (e.g. Cancelled by customer)" style="width:100%; height:70px; padding:10px; border:1px solid var(--c-separator); border-radius:var(--r-md); background:var(--c-bg); color:var(--t-primary); font-size:13px; resize:none;" required></textarea>
                </div>
            </div>
            <div style="padding: 12px 20px; background: var(--c-surface2); border-top: 0.5px solid var(--c-separator); display:flex; justify-content:flex-end; gap:10px;">
                <button onclick="closeDeleteConfirmModal()" style="padding:8px 16px; background:var(--c-fill); border:1px solid var(--c-separator); color:var(--t-secondary); border-radius:var(--r-sm); font-size:12px; font-weight:bold; cursor:pointer;">Cancel</button>
                <button onclick="submitDeleteSalesOrder()" style="padding:8px 16px; background:var(--c-red); border:none; color:#fff; border-radius:var(--r-sm); font-size:12px; font-weight:bold; cursor:pointer;">Permanently Delete</button>
            </div>
        </div>
    </div>

    <!-- Secure Delete Route Confirmation Modal -->
    <div class="modal-backdrop" id="deleteRouteModal" style="display: none; z-index: 2005;">
        <div class="modal-panel" style="max-width: 500px; width: 90%;">
            <div class="modal-header" style="background: var(--c-red-light); color: var(--c-red); border-bottom: 0.5px solid var(--c-separator); padding: 16px 20px;">
                <h3 style="margin:0; font-size:16px; font-weight:700;"><i class="ph ph-warning-octagon"></i> Secure Route Deletion</h3>
            </div>
            <div style="padding: 20px; display: flex; flex-direction: column; gap: 15px;">
                <p style="margin:0; font-size:13px; color:var(--t-secondary);">
                    You are about to delete Daily Route <strong id="deleteRouteTargetNum" style="color:var(--t-primary);"></strong>. Please choose how you want to handle the associated daily transactions:
                </p>
                <div>
                    <label style="font-weight:bold; font-size:11px; text-transform:uppercase; color:var(--t-secondary); display:block; margin-bottom:8px;">Deletion Mode *</label>
                    <div style="display:flex; flex-direction:column; gap:10px; font-size:13px;">
                        <label style="display:flex; align-items:flex-start; gap:8px; cursor:pointer;">
                            <input type="radio" name="deleteRouteMode" value="detach" style="margin-top:3px;" checked>
                            <div>
                                <strong>1. Delete Only Route (Preserve Sales Orders)</strong>
                                <div style="font-size:11px; color:#64748b; margin-top:2px;">
                                    The route will be deleted, but all invoices/sales orders, payments, cheques, and deliveries will be preserved by detaching them from the route.
                                </div>
                            </div>
                        </label>
                        <label style="display:flex; align-items:flex-start; gap:8px; cursor:pointer;">
                            <input type="radio" name="deleteRouteMode" value="delete_with_so" style="margin-top:3px;">
                            <div>
                                <strong>2. Delete Route and Delete/Void Sales Orders</strong>
                                <div style="font-size:11px; color:#64748b; margin-top:2px;">
                                    The route will be deleted, and all associated Sales Orders/Invoices will be permanently deleted (reversing stock and ledger postings). Payments/cheques/deliveries will be detached.
                                </div>
                            </div>
                        </label>
                        <label style="display:flex; align-items:flex-start; gap:8px; cursor:pointer;">
                            <input type="radio" name="deleteRouteMode" value="force_delete_all" style="margin-top:3px;">
                            <div>
                                <strong>3. Force Delete Route & All Associated Records</strong>
                                <div style="font-size:11px; color:#64748b; margin-top:2px;">
                                    The route, associated invoices, payments/cheques, deliveries, and collections will be permanently deleted from the database.
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
                <div>
                    <label style="font-weight:bold; font-size:11px; text-transform:uppercase; color:var(--t-secondary); display:block; margin-bottom:4px;">Administrator Password *</label>
                    <input type="password" id="deleteRoutePassword" placeholder="Enter password" style="width:100%; padding:10px; border:1px solid var(--c-separator); border-radius:var(--r-md); background:var(--c-bg); color:var(--t-primary); font-size:13px;" required>
                </div>
                <div>
                    <label style="font-weight:bold; font-size:11px; text-transform:uppercase; color:var(--t-secondary); display:block; margin-bottom:4px;">Security CAPTCHA *</label>
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:4px;">
                        <span id="deleteRouteCaptchaQuestion" style="font-weight:bold; font-size:13px; color:var(--t-primary); background:var(--c-surface2); padding:8px 12px; border:1px dashed var(--c-separator); border-radius:var(--r-sm); flex-grow:1; text-align:center;">Loading CAPTCHA...</span>
                        <button type="button" onclick="refreshDeleteRouteCaptcha()" style="background:none; border:none; color:var(--c-blue); font-size:20px; cursor:pointer; display:flex; align-items:center; justify-content:center;" title="Refresh CAPTCHA"><i class="ph ph-arrows-clockwise"></i></button>
                    </div>
                    <input type="text" id="deleteRouteCaptchaAnswer" placeholder="Enter CAPTCHA answer" style="width:100%; padding:10px; border:1px solid var(--c-separator); border-radius:var(--r-md); background:var(--c-bg); color:var(--t-primary); font-size:13px;" required>
                </div>
                <div>
                    <label style="font-weight:bold; font-size:11px; text-transform:uppercase; color:var(--t-secondary); display:block; margin-bottom:4px;">Reason for Deletion *</label>
                    <textarea id="deleteRouteReason" placeholder="Reason (e.g. Route created by mistake)" style="width:100%; height:70px; padding:10px; border:1px solid var(--c-separator); border-radius:var(--r-md); background:var(--c-bg); color:var(--t-primary); font-size:13px; resize:none;" required></textarea>
                </div>
            </div>
            <div style="padding: 12px 20px; background: var(--c-surface2); border-top: 0.5px solid var(--c-separator); display:flex; justify-content:flex-end; gap:10px;">
                <button onclick="closeDeleteRouteModal()" style="padding:8px 16px; background:var(--c-fill); border:1px solid var(--c-separator); color:var(--t-secondary); border-radius:var(--r-sm); font-size:12px; font-weight:bold; cursor:pointer;">Cancel</button>
                <button onclick="submitDeleteRoute()" style="padding:8px 16px; background:var(--c-red); border:none; color:#fff; border-radius:var(--r-sm); font-size:12px; font-weight:bold; cursor:pointer;">Permanently Delete Route</button>
            </div>
        </div>
    </div>

    <!-- Move Sales Order Modal -->
    <div class="modal-backdrop" id="moveInvoiceModal" style="display: none; z-index: 2005;">
        <div class="modal-panel">
            <div class="modal-header" style="background: var(--c-blue-light); color: var(--c-blue); border-bottom: 0.5px solid var(--c-separator); padding: 16px 20px;">
                <h3 style="margin:0; font-size:16px; font-weight:700;"><i class="ph ph-arrow-square-out"></i> Move Sales Order</h3>
            </div>
            <div style="padding: 20px; display: flex; flex-direction: column; gap: 15px;">
                <p style="margin:0; font-size:13px; color:var(--t-secondary);">
                    Select the destination route to move Sales Order <strong id="moveTargetInvNum" style="color:var(--t-primary);"></strong> to.
                </p>
                <div>
                    <label style="font-weight:bold; font-size:11px; text-transform:uppercase; color:var(--t-secondary); display:block; margin-bottom:4px;">Destination Route *</label>
                    <select id="moveDestinationRouteSelect" style="width:100%; padding:10px; border:1px solid var(--c-separator); border-radius:var(--r-md); background:var(--c-bg); color:var(--t-primary); font-size:13px;" required>
                        <option value="">-- Select Destination Route --</option>
                    </select>
                </div>
            </div>
            <div style="padding: 12px 20px; background: var(--c-surface2); border-top: 0.5px solid var(--c-separator); display:flex; justify-content:flex-end; gap:10px;">
                <button onclick="closeMoveInvoiceModal()" style="padding:8px 16px; background:var(--c-fill); border:1px solid var(--c-separator); color:var(--t-secondary); border-radius:var(--r-sm); font-size:12px; font-weight:bold; cursor:pointer;">Cancel</button>
                <button onclick="submitMoveSalesOrder()" style="padding:8px 16px; background:var(--c-blue); border:none; color:#fff; border-radius:var(--r-sm); font-size:12px; font-weight:bold; cursor:pointer;">Move Sales Order</button>
            </div>
        </div>
    </div>
</div>

<!-- Dynamic GPS Map Modal -->
<div class="modal-backdrop" id="mapModalBackdrop">
    <div class="modal-panel" style="max-width: 950px; width: 90%; height: 80vh; display: flex; flex-direction: column;">
        <div style="background: #3f51b5; color: #fff; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; font-weight: bold; font-size: 15px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span><i class="ph ph-map-pin"></i> GPS Route Path Tracking</span>
                <span id="modalRouteName" style="background: rgba(255,255,255,0.2); padding: 2px 8px; border-radius: 4px; font-size: 11px;"></span>
                <span id="pathPointCount" style="font-weight: normal; font-size: 11px;"></span>
            </div>
            <button class="close-slider" onclick="closeMapModal()">✕</button>
        </div>
        <div style="flex: 1; display: flex; flex-direction: column; position: relative; background: #eef1f4;">
            <div style="flex: 1; position: relative; min-height: 350px;">
                <div id="mapEmptyOverlay" class="map-empty-overlay">Loading...</div>
                <div id="routePathMap" style="height: 100%; width: 100%;"></div>
            </div>
            <div class="path-step-list" id="pathStepList" style="display: none;">
                <ol id="pathStepOl"></ol>
            </div>
        </div>
    </div>
</div>

<!-- Create Manual Route Modal -->
<div class="modal-backdrop" id="createManualRouteModal">
    <div class="modal-panel" style="max-width: 480px; width: 95%;">
        <div class="modal-header" style="background: #2e7d32;">
            <span><i class="ph ph-plus-circle"></i> Create Route Manually</span>
            <button onclick="closeCreateRouteModal()" style="background:transparent; border:none; color:#fff; font-size:18px; cursor:pointer; font-weight:bold;">✕</button>
        </div>
        <form action="<?= APP_URL ?>/RepTracking/create_route_manual" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <div class="modal-body">
                <div>
                    <label for="mrRep">Select Representative *</label>
                    <select name="user_id" id="mrRep" style="width: 100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 13px; background: white;" required>
                        <option value="">-- Select Rep --</option>
                        <?php foreach($data['reps'] as $rep): ?>
                            <option value="<?= $rep->id ?>"><?= htmlspecialchars(($rep->first_name ? $rep->first_name . ' ' . $rep->last_name : $rep->username)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="mrRouteName">Route Name (Territory/Area) *</label>
                    <input list="mca_areas_list" name="route_name" id="mrRouteName" placeholder="Select or type route name..." style="width: 100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 13px; background: white;" required autocomplete="off">
                    <datalist id="mca_areas_list">
                        <?php foreach($data['mca_areas'] as $area): ?>
                            <option value="<?= htmlspecialchars($area->name) ?>"></option>
                        <?php endforeach; ?>
                    </datalist>
                </div>
                <div>
                    <label for="mrStartMeter">Starting Odometer / Meter *</label>
                    <input type="number" step="0.1" name="start_meter" id="mrStartMeter" value="0.0" min="0" max="999999" oninput="if(this.value.length > 6) this.value = this.value.slice(0,6);" style="width: 100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 13px; background: white;" required>
                </div>
                <div>
                    <label for="mrStartTime">Start Date & Time *</label>
                    <input type="datetime-local" name="start_time" id="mrStartTime" value="<?= date('Y-m-d\TH:i') ?>" style="width: 100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 13px; background: white;" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="qb-btn" onclick="closeCreateRouteModal()" style="border:1px solid #ccc; padding:8px 18px; border-radius:4px; font-size:12px; cursor:pointer;">Cancel</button>
                <button type="submit" class="qb-btn" style="background:#2e7d32; color:#fff; border:none; padding:8px 18px; border-radius:4px; font-size:12px; cursor:pointer; font-weight: bold;"><i class="ph ph-lightning"></i> Create Route</button>
            </div>
        </form>
    </div>
</div>

<!-- Route Multi-Binding Modal -->
<div class="modal-backdrop" id="routeBindingModal" style="display:none; align-items:center; justify-content:center; z-index:10000; background:rgba(15,23,42,0.45); backdrop-filter:blur(8px);">
    <div class="modal-panel" style="max-width: 860px; width: 92%; border-radius: 20px; background: #ffffff; overflow: hidden; box-shadow: 0 24px 60px rgba(15, 23, 42, 0.22); border: 1px solid rgba(226, 232, 240, 0.9); font-family: var(--f-system); display: flex; flex-direction: column; max-height: 88vh;">
        
        <!-- Modal Header -->
        <div style="padding: 20px 24px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="font-size: 16px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                    <i class="ph ph-link-simple-horizontal" style="color: #007aff; font-size: 22px;"></i> Rep Route Multi-Binding Panel
                </div>
                <div style="font-size: 12px; color: #64748b; margin-top: 3px;">Combine multiple representative routes into a single consolidated binding group</div>
            </div>
            <button type="button" onclick="closeRouteBindingModal()" style="width: 32px; height: 32px; border-radius: 50%; background: #ffffff; border: 1px solid #cbd5e1; color: #64748b; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.15s;" onmouseover="this.style.background='#f1f5f9'; this.style.color='#0f172a'" onmouseout="this.style.background='#ffffff'; this.style.color='#64748b'">✕</button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body" style="padding: 24px; overflow-y: auto; flex: 1; display: flex; flex-direction: column; gap: 20px;">
            <!-- Bound Group Name Input -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
                <label for="rbBoundName" style="font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.06em; display: flex; align-items: center; gap: 6px; margin-bottom: 8px;">
                    <i class="ph ph-tag" style="color: #007aff; font-size: 14px;"></i> Custom Name for Bound Group *
                </label>
                <div style="position: relative;">
                    <input type="text" id="rbBoundName" placeholder="e.g. Western Route Combined - June 15" style="width: 100%; padding: 11px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13.5px; background: #ffffff; color: #0f172a; font-weight: 500; outline: none; transition: all 0.15s;" onfocus="this.style.borderColor='#007aff'; this.style.boxShadow='0 0 0 3px rgba(0,122,255,0.12)'" onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none'">
                </div>
            </div>

            <!-- Route Slots Section -->
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                    <span style="font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.06em; display: flex; align-items: center; gap: 6px;">
                        <i class="ph ph-path" style="color: #007aff; font-size: 14px;"></i> Route Slots
                    </span>
                    <span style="font-size: 11px; color: #64748b; font-weight: 500;">Select 2 or more routes to bind</span>
                </div>
                
                <div id="rbSlotsContainer" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; margin-bottom: 16px;"></div>

                <button type="button" onclick="addBindingSlot()" style="width: 100%; background: #f0f7ff; color: #007aff; border: 1.5px dashed #bfe0ff; padding: 12px; border-radius: 12px; font-weight: 700; cursor: pointer; font-size: 13px; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.15s;" onmouseover="this.style.background='#e3f2fd'; this.style.borderColor='#007aff';" onmouseout="this.style.background='#f0f7ff'; this.style.borderColor='#bfe0ff';">
                    <i class="ph ph-plus-circle" style="font-size: 17px;"></i> Add Route Slot
                </button>
            </div>
        </div>

        <!-- Modal Footer -->
        <div style="padding: 16px 24px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 12px; align-items: center;">
            <button type="button" onclick="closeRouteBindingModal()" style="padding: 10px 20px; background: #ffffff; border: 1px solid #cbd5e1; color: #475569; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.15s;" onmouseover="this.style.background='#f1f5f9'; this.style.color='#0f172a'" onmouseout="this.style.background='#ffffff'; this.style.color='#475569'">
                Cancel
            </button>
            <button type="button" onclick="submitRouteBinding()" style="padding: 10px 22px; background: linear-gradient(135deg, #16a34a, #15803d); border: none; color: #ffffff; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 2px 8px rgba(22, 163, 74, 0.25); transition: all 0.15s;" onmouseover="this.style.opacity='0.92';" onmouseout="this.style.opacity='1.0';">
                <i class="ph ph-lightning" style="font-size: 16px;"></i> Confirm & Create Route Binding
            </button>
        </div>
    </div>
</div>

<!-- Attach Sales Order Modal (iOS 2-Panel Split Layout) -->
<div class="modal-backdrop" id="attachInvoiceModal" style="display:none; align-items:center; justify-content:center; z-index:10000; background:rgba(15,23,42,0.4); backdrop-filter:blur(8px);">
    <div class="modal-panel" style="max-width: 920px; width: 94%; border-radius: 24px; background: #ffffff; overflow: hidden; box-shadow: 0 24px 60px rgba(15, 23, 42, 0.22); border: 1px solid rgba(226, 232, 240, 0.8); font-family: var(--f-system); display: flex; flex-direction: column;">
        
        <!-- Modal Header -->
        <div style="padding: 18px 24px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="font-size: 16px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                    <i class="ph ph-link-simple" style="color: #007aff; font-size: 20px;"></i> Attach Sales Orders to Route
                </div>
                <div style="font-size: 12px; color: #64748b; margin-top: 2px;">Filter and attach unassigned pending sales orders to this route</div>
            </div>
            <button type="button" onclick="closeAttachInvoiceModal()" style="width: 32px; height: 32px; border-radius: 50%; background: #ffffff; border: 1px solid #cbd5e1; color: #64748b; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.15s;" onmouseover="this.style.background='#f1f5f9'; this.style.color='#0f172a'" onmouseout="this.style.background='#ffffff'; this.style.color='#64748b'">✕</button>
        </div>

        <!-- Modal Body (2-Panel Split View) -->
        <div style="display: flex; min-height: 440px; max-height: 65vh; overflow: hidden;">
            
            <!-- LEFT PANEL: Filters & Realtime Search (310px) -->
            <div style="width: 310px; flex-shrink: 0; background: #f8fafc; border-right: 1px solid #e2e8f0; padding: 20px; display: flex; flex-direction: column; gap: 16px;">
                
                <div style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.08em; display: flex; align-items: center; gap: 6px;">
                    <i class="ph ph-funnel" style="font-size: 14px; color: #007aff;"></i> Search & Filters
                </div>

                <!-- Realtime Search -->
                <div>
                    <label style="font-size: 11px; font-weight: 700; color: #334155; margin-bottom: 6px; display: block;">Search Keywords</label>
                    <div style="position: relative;">
                        <i class="ph ph-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 16px; color: #94a3b8;"></i>
                        <input type="text" id="invoiceSearchInput" oninput="searchUnattachedInvoices()" placeholder="SO number or customer..." style="width: 100%; padding: 10px 12px 10px 36px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; background: #ffffff; outline: none; transition: all 0.15s;" onfocus="this.style.borderColor='#007aff'; this.style.boxShadow='0 0 0 3px rgba(0,122,255,0.15)'" onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none'">
                    </div>
                </div>

                <!-- Date Range -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div>
                        <label style="font-size: 11px; font-weight: 700; color: #334155; margin-bottom: 6px; display: block;">From Date</label>
                        <input type="date" id="soFilterStartDate" onchange="searchUnattachedInvoices()" style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 12px; background: #ffffff;">
                    </div>
                    <div>
                        <label style="font-size: 11px; font-weight: 700; color: #334155; margin-bottom: 6px; display: block;">To Date</label>
                        <input type="date" id="soFilterEndDate" onchange="searchUnattachedInvoices()" style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 12px; background: #ffffff;">
                    </div>
                </div>

                <!-- Sales Rep Filter -->
                <div>
                    <label style="font-size: 11px; font-weight: 700; color: #334155; margin-bottom: 6px; display: block;">Filter by Representative</label>
                    <select id="soFilterRepSelect" onchange="searchUnattachedInvoices()" style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; background: #ffffff;">
                        <option value="">All Representatives</option>
                        <?php if (!empty($data['reps'])): ?>
                            <?php foreach ($data['reps'] as $rep): ?>
                                <option value="<?= $rep->id ?>"><?= htmlspecialchars($rep->name ?? ($rep->first_name . ' ' . $rep->last_name)) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Route Filter -->
                <div>
                    <label style="font-size: 11px; font-weight: 700; color: #334155; margin-bottom: 6px; display: block;">Filter by Route / Territory</label>
                    <select id="soFilterRouteSelect" onchange="searchUnattachedInvoices()" style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; background: #ffffff;">
                        <option value="">All Routes</option>
                        <?php if (!empty($data['routes_list'])): ?>
                            <?php foreach ($data['routes_list'] as $rt): ?>
                                <option value="<?= $rt->id ?>"><?= htmlspecialchars($rt->name ?? $rt->route_name) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Reset Filters Button -->
                <button type="button" onclick="resetSalesOrderFilters()" style="margin-top: auto; padding: 10px 14px; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 12px; font-weight: 700; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.15s;" onmouseover="this.style.background='#f1f5f9'; this.style.color='#0f172a'" onmouseout="this.style.background='#ffffff'; this.style.color='#64748b'">
                    <i class="ph ph-arrow-counter-clockwise" style="font-size: 14px;"></i> Reset All Filters
                </button>
            </div>

            <!-- RIGHT PANEL: Sales Orders Cards List (Flex: 1) -->
            <div style="flex: 1; display: flex; flex-direction: column; background: #ffffff; padding: 20px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9;">
                    <div style="font-size: 12px; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 0.05em;">
                        Available Orders (<span id="unattachedCount">0</span>)
                    </div>
                    <button type="button" onclick="toggleSelectAllUnattached()" style="font-size: 12px; font-weight: 700; color: #007aff; background: none; border: none; cursor: pointer;">
                        Select All
                    </button>
                </div>

                <div id="unattachedInvoicesContainer" style="flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; padding-right: 4px;">
                    <p style="text-align: center; color: #94a3b8; margin: 60px 0; font-size: 13px;">Type search text or modify filters to query unattached sales orders...</p>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div style="padding: 16px 24px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
            <div style="font-size: 12px; color: #64748b; font-weight: 600;">
                Selected: <strong style="color: #007aff;" id="attachSelectedCount">0</strong> sales order(s)
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="button" onclick="closeAttachInvoiceModal()" style="padding: 10px 20px; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 100px; font-size: 13px; font-weight: 700; color: #334155; cursor: pointer; transition: all 0.15s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#ffffff'">Cancel</button>
                <button type="button" onclick="confirmAttachInvoices()" style="padding: 10px 24px; background: #007aff; border: none; border-radius: 100px; font-size: 13px; font-weight: 700; color: #ffffff; cursor: pointer; box-shadow: 0 3px 12px rgba(0,122,255,0.3); transition: all 0.15s;" onmouseover="this.style.transform='scale(1.03)'; this.style.background='#0066cc'" onmouseout="this.style.transform='scale(1)'; this.style.background='#007aff'">
                    <i class="ph ph-link" style="font-size: 16px; margin-right: 4px;"></i> Attach Selected
                </button>
            </div>
        </div>

    </div>
</div>

<!-- Route Switcher Modal -->
<div class="modal-backdrop" id="routeSwitcherModalBackdrop">
    <div class="modal-panel" style="max-width: 550px; width: 90%; max-height: 80vh; display: flex; flex-direction: column;">
        <div class="modal-header" style="background: #3f51b5; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; color: #fff; font-weight: bold;">
            <span><i class="ph ph-swap"></i> Switch Route</span>
            <button onclick="closeRouteSwitcherModal()" style="background:transparent; border:none; color:#fff; font-size:18px; cursor:pointer; font-weight:bold;">✕</button>
        </div>
        <div style="padding: 15px; border-bottom: 1px solid #e2e8f0; background: #fff;">
            <input type="text" id="routeSwitcherSearchInput" placeholder="Search routes by name, rep..." style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;" oninput="searchRouteSwitcherList()" />
        </div>
        <div class="modal-body" style="overflow-y: auto; flex: 1; padding: 15px; display: flex; flex-direction: column; gap: 10px; background: #fafafa;" id="routeSwitcherItemsContainer">
            <?php foreach($data['routes'] as $route): ?>
                <div class="switcher-route-item" onclick="selectRouteFromSwitcher(<?= $route->id ?>)" style="padding: 12px; border: 1px solid #e2e8f0; border-radius: 6px; background: #fff; cursor: pointer; transition: 0.2s;" data-rname="<?= htmlspecialchars($route->route_name) ?>" data-rep="<?= htmlspecialchars($route->first_name . ' ' . $route->last_name) ?>">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                        <strong style="color: #3f51b5; font-size: 12px; font-family: monospace;">#RT-<?= str_pad($route->id, 5, '0', STR_PAD_LEFT) ?></strong>
                        <span style="font-size: 10px; padding: 2px 6px; border-radius: 4px; background: #e2e8f0; font-weight: bold; color: #555;"><?= htmlspecialchars($route->status) ?></span>
                    </div>
                    <div style="font-weight: bold; font-size: 13px; color: #333;"><?= htmlspecialchars($route->route_name) ?></div>
                    <div style="font-size: 11px; color: #666;">Rep: <?= htmlspecialchars($route->first_name . ' ' . $route->last_name) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Server-side Delivery Process Modal -->
<div class="modal-backdrop" id="serverDeliveryProcessModal" style="display: none; align-items: center; justify-content: center; backdrop-filter: blur(8px); background: rgba(15, 23, 42, 0.6); z-index: 2000;">
    <div class="modal-panel" style="max-width: 1140px; width: 95%; height: 88vh; max-height: 88vh; display: flex; flex-direction: column; border-radius: 20px; overflow: hidden; border: 1px solid rgba(255, 255, 255, 0.2); box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.35); background: #ffffff;">
        
        <!-- Header Bar -->
        <div class="modal-header" id="sdpModalHeader" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important; padding: 18px 24px !important; display: flex !important; justify-content: space-between !important; align-items: center !important; color: #ffffff !important; flex-shrink: 0; border-bottom: 1px solid rgba(255, 255, 255, 0.15) !important;">
            
            <!-- Left Side: Truck Icon + Subtitle/Invoice Ref + Shop Name + Arrears Below -->
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(56, 189, 248, 0.15); border: 1px solid rgba(56, 189, 248, 0.3); color: #38bdf8; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;">
                    <i class="ph ph-truck"></i>
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 2px;">
                        <span style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; font-weight: 700;">Process Customer Visit</span>
                        <span style="color: rgba(255,255,255,0.3);">•</span>
                        <span style="font-size: 11px; color: #38bdf8; font-weight: 700; font-family: monospace;">Invoice Ref: <strong id="sdpInvoiceNumber" style="color: #38bdf8; font-size: 12px;">--</strong></span>
                    </div>
                    <div style="font-size: 17px; font-weight: 700; color: #ffffff; line-height: 1.2;" id="sdpCustomerName">--</div>
                    
                    <!-- Customer Arrears Below Shop Name -->
                    <div id="sdpArrearsInfoBox" style="margin-top: 5px; display: inline-flex; align-items: center; gap: 6px; background: rgba(245, 158, 11, 0.2); border: 1px solid rgba(245, 158, 11, 0.4); padding: 3px 12px; border-radius: 100px; font-size: 11px; color: #fef08a; font-weight: 600;">
                        <i class="ph ph-warning" style="color: #fbbf24; font-size: 13px;"></i> Customer Arrears: <strong id="sdpOutstandingArrears" style="color: #ffffff; font-weight: 700; margin-left: 2px;">Rs 0.00</strong>
                    </div>
                </div>
            </div>

            <!-- Right Side: Delivery Result Dropdown + Close Button -->
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 3px;">
                    <span style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; font-weight: 700;">Delivery Result</span>
                    <select id="sdpDeliveryStatus" style="padding: 7px 14px; border-radius: 100px; border: 1px solid rgba(255, 255, 255, 0.25); font-weight: 700; font-size: 12px; background: rgba(255, 255, 255, 0.12); color: #ffffff; outline: none; cursor: pointer; transition: all 0.18s; backdrop-filter: blur(10px);">
                        <option value="Pending" style="background:#1e293b; color:#ffffff;">Pending</option>
                        <option value="Delivered" style="background:#1e293b; color:#4ade80;">Delivered</option>
                        <option value="Cancelled" style="background:#1e293b; color:#f87171;">Cancelled</option>
                        <option value="Postponed" style="background:#1e293b; color:#cbd5e1;">Postponed</option>
                    </select>
                </div>

                <button type="button" onclick="closeServerDeliveryProcessModal()" style="background: rgba(255, 255, 255, 0.12) !important; border: 1px solid rgba(255, 255, 255, 0.2) !important; color: #ffffff !important; width: 34px !important; height: 34px !important; border-radius: 50% !important; font-size: 14px !important; cursor: pointer !important; display: flex !important; align-items: center !important; justify-content: center !important; transition: all 0.18s !important; margin-left: 4px;" onmouseover="this.style.background='rgba(239,68,68,0.85)'" onmouseout="this.style.background='rgba(255,255,255,0.12)'">✕</button>
            </div>
        </div>

        <!-- Body Section -->
        <div class="modal-body" style="flex: 1; padding: 20px; display: flex !important; flex-direction: row !important; gap: 20px; background: #f8fafc; overflow: hidden !important; min-height: 0;">
            
            <!-- Left Column: Adjust Invoice Items -->
            <div style="flex: 1.25; display: flex; flex-direction: column; background: #ffffff; padding: 20px; border-radius: 14px; border: 1px solid #e2e8f0; box-shadow: 0 2px 10px rgba(15,23,42,0.03); height: 100%; min-height: 0;">
                
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; flex-shrink: 0;">
                    <div style="font-size: 14px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                        <i class="ph ph-package" style="color: #007aff; font-size: 18px;"></i> Delivered Quantities
                    </div>
                    <span style="font-size: 11px; color: #64748b; font-weight: 500;">Adjust quantities if items rejected</span>
                </div>
                
                <!-- Item Search Input -->
                <div style="margin-bottom: 12px; flex-shrink: 0;">
                    <div style="position: relative;">
                        <i class="ph ph-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 15px;"></i>
                        <input type="text" id="sdpItemSearch" placeholder="Search item description..." oninput="filterSdpItems()"
                               style="width: 100%; padding: 9px 12px 9px 36px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 12px; outline: none; background: #f8fafc; transition: all 0.18s; font-family: inherit;" onfocus="this.style.borderColor='#007aff'; this.style.background='#ffffff'; this.style.boxShadow='0 0 0 3px rgba(0,122,255,0.12)'" onblur="this.style.borderColor='#cbd5e1'; this.style.background='#f8fafc'; this.style.boxShadow='none'" />
                    </div>
                </div>
                
                <!-- Items Table Container -->
                <div style="border: 1px solid #e2e8f0; border-radius: 10px; flex: 1; overflow-y: auto; min-height: 0; background: #ffffff;">
                    <table style="width: 100%; border-collapse: separate; border-spacing: 0; font-family: inherit; font-size: 12px;">
                        <thead style="position: sticky; top: 0; background: #f8fafc; z-index: 5;">
                            <tr style="color: #475569; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;">
                                <th style="padding: 10px 14px; text-align: left; border-bottom: 1px solid #e2e8f0;">Item Description</th>
                                <th style="padding: 10px 14px; text-align: right; border-bottom: 1px solid #e2e8f0; width: 70px;">Loaded</th>
                                <th style="padding: 10px 14px; text-align: right; border-bottom: 1px solid #e2e8f0; width: 110px;">Delivered Qty</th>
                            </tr>
                        </thead>
                        <tbody id="sdpItemsTbody">
                            <!-- Dynamic zebra rows populated in JS -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right Column: Record Payments & Collections -->
            <div style="flex: 1; display: flex; flex-direction: column; background: #ffffff; padding: 20px; border-radius: 14px; border: 1px solid #e2e8f0; box-shadow: 0 2px 10px rgba(15,23,42,0.03); gap: 14px; height: 100%; min-height: 0; overflow-y: auto;">
                
                <!-- Hidden inputs -->
                <input type="hidden" id="sdpInvoiceId" />
                <input type="hidden" id="sdpCustomerId" />

                <!-- Collections Section -->
                <div style="flex: 1; display: flex; flex-direction: column; min-height: 0;">
                    <div style="font-size: 14px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; margin-bottom: 14px; flex-shrink: 0;">
                        <i class="ph ph-coins" style="color: #16a34a; font-size: 18px;"></i> Record Payments & Collections
                    </div>
                    
                    <!-- Balance Summary Pills -->
                    <div style="background: #f8fafc; padding: 14px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 16px; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; text-align: center; flex-shrink: 0;">
                        <div>
                            <div style="color: #64748b; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;">Invoice Total</div>
                            <strong id="sdpInvoiceTotal" style="font-size: 14px; color: #0f172a; font-family: monospace; display: block; margin-top: 3px;">Rs 0.00</strong>
                        </div>
                        <div>
                            <div style="color: #64748b; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;">Collected</div>
                            <strong id="sdpTotalCollected" style="font-size: 14px; color: #16a34a; font-family: monospace; display: block; margin-top: 3px;">Rs 0.00</strong>
                        </div>
                        <div>
                            <div style="color: #64748b; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;">Remaining</div>
                            <strong id="sdpRemainingBalance" style="font-size: 14px; color: #dc2626; font-family: monospace; display: block; margin-top: 3px;">Rs 0.00</strong>
                        </div>
                    </div>
                    
                    <!-- Cash & Bank inputs -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px; flex-shrink: 0;">
                        <div>
                            <label style="font-size: 11px; font-weight: 700; color: #334155; display: block; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.03em;">Cash Amount (Rs)</label>
                            <input type="number" step="0.01" min="0" id="sdpCashAmount" oninput="updateSdpBalance()" style="width: 100%; height: 40px; padding: 0 12px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; font-weight: 700; font-family: monospace; outline: none; transition: all 0.18s; background: #ffffff;" value="0.00" onfocus="this.style.borderColor='#007aff'; this.style.boxShadow='0 0 0 3px rgba(0,122,255,0.12)'" onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none'">
                        </div>
                        <div>
                            <label style="font-size: 11px; font-weight: 700; color: #334155; display: block; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.03em;">Bank Transfer (Rs)</label>
                            <input type="number" step="0.01" min="0" id="sdpBankAmount" oninput="updateSdpBalance()" style="width: 100%; height: 40px; padding: 0 12px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; font-weight: 700; font-family: monospace; outline: none; transition: all 0.18s; background: #ffffff;" value="0.00" onfocus="this.style.borderColor='#007aff'; this.style.boxShadow='0 0 0 3px rgba(0,122,255,0.12)'" onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none'">
                        </div>
                    </div>

                    <!-- Cheques Container -->
                    <div style="border-top: 1px dashed #cbd5e1; padding-top: 14px; flex: 1; overflow-y: auto; min-height: 0;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; flex-shrink: 0;">
                            <strong style="font-size: 12px; color: #334155; font-weight: 700;">Cheque Collections</strong>
                            <button type="button" onclick="addSdpChequeRow()" style="padding: 6px 12px; background: #eff6ff; border: 1px solid #bfdbfe; color: #0284c7; border-radius: 100px; font-size: 11px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 4px; transition: all 0.18s;" onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                                <i class="ph ph-plus" style="font-size: 13px;"></i> Add Cheque
                            </button>
                        </div>
                        <div id="sdpChequesContainer" style="display: flex; flex-direction: column; gap: 8px;">
                            <!-- Cheque rows go here -->
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer Bar -->
        <div class="modal-footer" style="background: #ffffff; border-top: 1px solid #e2e8f0; padding: 14px 24px; display: flex; justify-content: flex-end; align-items: center; gap: 12px; flex-shrink: 0;">
            <button type="button" onclick="closeServerDeliveryProcessModal()" style="padding: 10px 22px; background: #ffffff; border: 1px solid #cbd5e1; color: #475569; border-radius: 100px; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.18s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#ffffff'">Cancel</button>
            <button type="button" onclick="submitServerDeliveryProcess()" style="padding: 10px 24px; background: #16a34a; border: none; color: #ffffff; border-radius: 100px; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(22,163,74,0.35); transition: all 0.18s;" onmouseover="this.style.transform='scale(1.03)'; this.style.background='#15803d'" onmouseout="this.style.transform='scale(1)'; this.style.background='#16a34a'">
                <i class="ph ph-check-circle" style="font-size: 16px;"></i> Save & Process Visit
            </button>
        </div>
    </div>
</div>

<!-- Trip Settle Balancing & Finalized Audit Summary Modal -->
<div class="modal-backdrop" id="balancingReportModal" style="display: none; z-index: 2010;">
    <div class="modal-panel" style="max-width: 1100px; width: 95%; height: 90vh; display: flex; flex-direction: column; position: relative; border-radius: var(--r-lg); overflow: hidden; background: #fff; box-shadow: var(--shadow-xl);">
        <div style="background: #1e293b; color: #fff; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; font-weight: bold; font-size: 15px; border-bottom: 1px solid #334155; flex-shrink: 0;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="display: flex; align-items: center; gap: 6px;"><i class="ph ph-file-text"></i> Trip Settlement Balancing & Audit Report</span>
            </div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <button onclick="printBalancingReportFromModal()" style="background: #2563eb; color: #fff; border: none; padding: 6px 14px; border-radius: var(--r-sm); font-size: 12px; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: var(--shadow-sm); transition: 0.2s;"><i class="ph ph-printer"></i> Print Summary</button>
                <button onclick="closeBalancingReportModal()" style="background: #ef4444; color: #fff; border: none; padding: 6px 14px; border-radius: var(--r-sm); font-size: 12px; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 4px; box-shadow: var(--shadow-sm); transition: 0.2s;">✕ Close & Reload</button>
            </div>
        </div>
        <iframe id="balancingReportIframe" src="about:blank" style="width: 100%; flex: 1; border: none; background: #fff;"></iframe>
    </div>
</div>

<!-- Product Replacement / Substitution Modal for Loading Stage -->
<div class="modal-backdrop" id="replaceProductModal" style="display: none; z-index: 2020;">
    <div class="modal-panel" style="max-width: 550px; width: 90%;">
        <div class="modal-header" style="background: #673ab7; color: #fff; border-bottom: 0.5px solid var(--c-separator); padding: 16px 20px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin:0; font-size:16px; font-weight:700; display:flex; align-items:center; gap:8px;"><i class="ph ph-swap"></i> Substitute Product</h3>
            <button onclick="closeReplaceProductModal()" style="background:none; border:none; color:#fff; font-size:18px; cursor:pointer; font-weight:bold;">✕</button>
        </div>
        <div style="padding: 20px; display: flex; flex-direction: column; gap: 15px;">
            <input type="hidden" id="replaceDeliveryId">
            <input type="hidden" id="replaceOrigItemId">
            <input type="hidden" id="replaceOrigVarOptId">
            <input type="hidden" id="selectedReplItemId">
            <input type="hidden" id="selectedReplVarOptId">

            <div style="font-size: 13px; background: #f3e8ff; border: 1px solid #d8b4fe; padding: 10px 12px; border-radius: 6px; color: #581c87;">
                Replacing: <strong id="replaceOrigName" style="color: #3b0764;">[Product Name]</strong> 
                (Required Qty: <strong id="replaceOrigQty">0</strong>)
            </div>

            <div>
                <label style="font-weight:bold; font-size:11px; text-transform:uppercase; color:var(--t-secondary); display:block; margin-bottom:4px;">Search Replacement Product *</label>
                <input type="text" id="replacementSearchInput" oninput="handleReplacementSearchInput(this.value)" placeholder="Type product name, SKU or code..." style="width:100%; padding:10px; border:1px solid var(--c-separator); border-radius:var(--r-md); background:var(--c-bg); color:var(--t-primary); font-size:13px;" autocomplete="off">
                
                <div id="replacementSearchResults" style="max-height: 180px; overflow-y: auto; border: 1px solid var(--c-separator); border-radius: var(--r-md); background: #fff; margin-top: 5px; display: none; box-shadow: var(--shadow-md);">
                    <!-- Dynamic search items -->
                </div>
            </div>

            <div id="selectedReplacementBox" style="display: none; padding: 10px 12px; background: #dcfce7; border: 1px solid #86efac; border-radius: 6px; font-size: 13px; color: #166534;">
                Selected Substitution: <strong id="selectedReplName" style="color: #14532d;">[Product]</strong>
            </div>

            <div>
                <label style="font-weight:bold; font-size:11px; text-transform:uppercase; color:var(--t-secondary); display:block; margin-bottom:4px;">Replacement Quantity *</label>
                <input type="number" id="replacementQtyInput" min="0.01" step="1" value="1" style="width:100%; padding:10px; border:1px solid var(--c-separator); border-radius:var(--r-md); background:var(--c-bg); color:var(--t-primary); font-size:13px; font-weight:bold;">
            </div>
        </div>
        <div style="padding: 12px 20px; background: var(--c-surface2); border-top: 0.5px solid var(--c-separator); display:flex; justify-content:flex-end; gap:10px;">
            <button onclick="closeReplaceProductModal()" style="padding:8px 16px; background:var(--c-fill); border:1px solid var(--c-separator); color:var(--t-secondary); border-radius:var(--r-sm); font-size:12px; font-weight:bold; cursor:pointer;">Cancel</button>
            <button onclick="submitProductSubstitution()" id="btnSaveReplacement" style="padding:8px 16px; background:#673ab7; border:none; color:#fff; border-radius:var(--r-sm); font-size:12px; font-weight:bold; cursor:pointer; display:flex; align-items:center; gap:6px;">
                <i class="ph ph-floppy-disk"></i> Save Product Substitution
            </button>
        </div>
    </div>
</div>

