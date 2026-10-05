<script>
(function() {
    console.log('[RepTracking] Initializing Master Route Control Panel...');
    window.addEventListener('error', function(e) {
        console.error('[RepTracking Global Error]', e.message, e.filename, e.lineno, e.error);
    });

    const globalBankAccounts = <?php echo json_encode($data['bank_accounts'] ?? []); ?>;
    const globalAllAccounts = <?php echo json_encode($data['all_accounts'] ?? []); ?>;

    function buildAccountOptions(selectedId, fallbackCode) {
        let html = '<option value="">-- Select Account --</option>';
        let hasSelected = false;
        if (selectedId !== undefined && selectedId !== null && selectedId !== '') {
            hasSelected = globalAllAccounts.some(acc => String(acc.id) === String(selectedId));
        }
        globalAllAccounts.forEach(acc => {
            let isSel = false;
            if (hasSelected) {
                isSel = String(acc.id) === String(selectedId);
            } else {
                isSel = acc.account_code === fallbackCode;
            }
            html += `<option value="${acc.id}" ${isSel ? 'selected' : ''}>${acc.account_code} - ${acc.account_name}</option>`;
        });
        return html;
    }

    // Helper function to resolve ID by account code
    function getAccountIdByCode(code) {
        const acc = globalAllAccounts.find(a => a.account_code === code);
        return acc ? acc.id : null;
    }

    let currentRouteId = null;
    let routeMap = null;
    let routeMapLayers = [];
    let tab1Map = null;
    let tab1MapLayers = [];
    let rbSlotsCount = 2;
    let activeRouteBills = [];
    let currentDeliveryDetails = null;

    let pathGreenIcon, pathRedIcon, pathBlueIcon, pathOrangeIcon;
    function initMapIcons() {
        if (typeof L !== 'undefined' && !pathGreenIcon) {
            try {
                pathGreenIcon = new L.Icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34]
                });
                pathRedIcon = new L.Icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34]
                });
                pathBlueIcon = new L.Icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34]
                });
                pathOrangeIcon = new L.Icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-orange.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34]
                });
            } catch (err) {
                console.warn('[RepTracking] Could not initialize Leaflet icons:', err);
            }
        }
    }
    initMapIcons();

    const activeUsername = <?php echo json_encode($_SESSION['username'] ?? ''); ?>;

    let isFetchingRoutes = false;
    function fetchRoutesList(pageNumber = 1) {
        if (isFetchingRoutes) return;
        isFetchingRoutes = true;

        let query = document.getElementById('floatingSearchInput').value.trim();
        if (query && (query.toLowerCase() === 'susar.aa' || (activeUsername && query.toLowerCase() === activeUsername.toLowerCase()))) {
            document.getElementById('floatingSearchInput').value = '';
            query = '';
        }

        const selectedRep = document.getElementById('filterRepSelect').value;
        const selectedRoute = document.getElementById('filterRouteSelect').value;
        const selectedDate = document.getElementById('filterDateInput').value;
        const selectedTerritory = document.getElementById('filterTerritorySelect').value;

        const params = new URLSearchParams();
        params.set('ajax', '1');
        params.set('page', pageNumber);
        if (query) params.set('search', query);
        if (selectedRep) params.set('rep', selectedRep);
        if (selectedRoute) params.set('route', selectedRoute);
        if (selectedDate) params.set('date', selectedDate);
        if (selectedTerritory) params.set('territory', selectedTerritory);
        
        const url = new URL(window.location);
        url.searchParams.set('page', pageNumber);
        if (query) url.searchParams.set('search', query); else url.searchParams.delete('search');
        if (selectedRep) url.searchParams.set('rep', selectedRep); else url.searchParams.delete('rep');
        if (selectedRoute) url.searchParams.set('route', selectedRoute); else url.searchParams.delete('route');
        if (selectedDate) url.searchParams.set('date', selectedDate); else url.searchParams.delete('date');
        if (selectedTerritory) url.searchParams.set('territory', selectedTerritory); else url.searchParams.delete('territory');
        window.history.replaceState({}, '', url);

        fetch(window.location.pathname + '?' + params.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            isFetchingRoutes = false;
            if (data.status === 'success') {
                const container = document.getElementById('routeListItemsContainer');
                const pagContainer = document.getElementById('routePaginationContainer');
                if (container) container.innerHTML = data.routes_html;
                if (pagContainer) pagContainer.innerHTML = data.pagination_html;
                
                if (currentRouteId) {
                    const activeRouteEl = document.getElementById('route_' + currentRouteId);
                    if (activeRouteEl) {
                        activeRouteEl.classList.add('active');
                    }
                } else {
                    const urlParams = new URLSearchParams(window.location.search);
                    const rId = urlParams.get('route_id');
                    if (rId) {
                        const routeEl = document.getElementById('route_' + rId);
                        if (routeEl) {
                            loadRouteDetails(rId, routeEl);
                            routeEl.scrollIntoView({ block: 'nearest' });
                        }
                    }
                }
            } else {
                console.error('Error fetching routes:', data.message);
            }
        })
        .catch(err => {
            isFetchingRoutes = false;
            console.error('AJAX fetch routes error:', err);
        });
    }

    function changePage(pageNumber) {
        fetchRoutesList(pageNumber);
    }

    window.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const routeId = urlParams.get('route_id');
        const page = urlParams.get('page') || 1;
        
        const searchVal = urlParams.get('search');
        const searchInput = document.getElementById('floatingSearchInput');
        if (searchInput) {
            searchInput.value = searchVal ? searchVal : '';
        }

        const repVal = urlParams.get('rep');
        const repSelect = document.getElementById('filterRepSelect');
        if (repSelect) {
            repSelect.value = repVal ? repVal : '';
        }

        const routeVal = urlParams.get('route');
        const routeSelect = document.getElementById('filterRouteSelect');
        if (routeSelect) {
            routeSelect.value = routeVal ? routeVal : '';
        }

        const dateVal = urlParams.get('date');
        const dateInput = document.getElementById('filterDateInput');
        if (dateInput) {
            dateInput.value = dateVal ? dateVal : '';
        }

        const territoryVal = urlParams.get('territory');
        const territorySelect = document.getElementById('filterTerritorySelect');
        if (territorySelect) {
            territorySelect.value = territoryVal ? territoryVal : '';
        }

        // Failsafe: Purge browser credential autofill on load
        const purgeAutofill = () => {
            const input = document.getElementById('floatingSearchInput');
            if (input && !new URLSearchParams(window.location.search).get('search')) {
                const val = input.value.trim().toLowerCase();
                if (val === 'susar.aa' || (activeUsername && val === activeUsername.toLowerCase())) {
                    input.value = '';
                }
            }
        };
        purgeAutofill();
        setTimeout(purgeAutofill, 50);
        setTimeout(purgeAutofill, 200);
        setTimeout(purgeAutofill, 600);

        fetchRoutesList(page);
    });

    function filterLeftPane(type, btn) {
        // Backwards compatibility stub
    }

    let searchDebounceTimeout = null;
    function searchRouteList() {
        if (searchDebounceTimeout) clearTimeout(searchDebounceTimeout);
        searchDebounceTimeout = setTimeout(() => {
            fetchRoutesList(1);
        }, 300);
    }

    function clearFilters() {
        document.getElementById('filterRepSelect').value = '';
        document.getElementById('filterRouteSelect').value = '';
        document.getElementById('filterDateInput').value = '';
        document.getElementById('filterTerritorySelect').value = '';
        document.getElementById('floatingSearchInput').value = '';
        fetchRoutesList(1);
    }

    function openRouteSwitcherModal() {
        const modal = document.getElementById('routeSwitcherModalBackdrop');
        if (modal) {
            modal.style.display = 'flex';
            document.getElementById('routeSwitcherSearchInput').value = '';
            searchRouteSwitcherList();
            document.getElementById('routeSwitcherSearchInput').focus();
        }
    }

    function closeRouteSwitcherModal() {
        const modal = document.getElementById('routeSwitcherModalBackdrop');
        if (modal) modal.style.display = 'none';
    }

    function openCreateRouteModal() {
        const modal = document.getElementById('createManualRouteModal');
        if (modal) {
            modal.style.display = 'flex';
            // Set current date/time to now
            const now = new Date();
            const offset = now.getTimezoneOffset() * 60000;
            const localISOTime = (new Date(now - offset)).toISOString().slice(0, 16);
            document.getElementById('mrStartTime').value = localISOTime;
        }
    }

    function closeCreateRouteModal() {
        const modal = document.getElementById('createManualRouteModal');
        if (modal) modal.style.display = 'none';
    }

    function searchRouteSwitcherList() {
        const query = document.getElementById('routeSwitcherSearchInput').value.toLowerCase().trim();
        document.querySelectorAll('.switcher-route-item').forEach(item => {
            const rname = item.getAttribute('data-rname').toLowerCase();
            const rep = item.getAttribute('data-rep').toLowerCase();
            if (rname.includes(query) || rep.includes(query)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function selectRouteFromSwitcher(routeId) {
        closeRouteSwitcherModal();
        const routeEl = document.getElementById('route_' + routeId);
        loadRouteDetails(routeId, routeEl);
        
        // Update URL query param to reflect the new route active without reloading
        const url = new URL(window.location);
        url.searchParams.set('route_id', routeId);
        window.history.replaceState({}, '', url);
    }

    function goBackToRoutes() {
        currentRouteId = null;
        document.body.classList.remove('workspace-showing');
        const ws = document.querySelector('.app-workspace');
        if (ws) ws.classList.remove('workspace-active');
        
        document.querySelectorAll('.route-item').forEach(i => i.classList.remove('active'));
        
        // Clear route_id query parameter from the URL
        const url = new URL(window.location);
        url.searchParams.delete('route_id');
        window.history.replaceState({}, '', url);
    }

    // Register custom back handler for top navbar back button
    window.customGoBack = function() {
        const ws = document.querySelector('.app-workspace');
        if (ws && ws.classList.contains('workspace-active')) {
            goBackToRoutes();
            return true; // Back action handled within Rep Tracking module!
        }
        return false; // Not handled, fallback to default navigation
    };

    // Handle native browser / mobile physical back button
    window.addEventListener('popstate', function(e) {
        const ws = document.querySelector('.app-workspace');
        if (ws && ws.classList.contains('workspace-active')) {
            goBackToRoutes();
        }
    });

    const CSRF_TOKEN = '<?= $_SESSION['csrf_token'] ?? '' ?>';
    let currentTabIndex = 1;
    let currentRouteStatus = 'Active';

    // Fetch wrapper to inject CSRF token to headers and bodies
    function fetchSecure(url, options = {}) {
        options.headers = options.headers || {};
        options.headers['X-CSRF-TOKEN'] = CSRF_TOKEN;
        options.headers['X-Requested-With'] = 'XMLHttpRequest';
        options.headers['Accept'] = 'application/json, text/plain, */*';
        
        if (options.body && typeof options.body === 'string') {
            try {
                const parsed = JSON.parse(options.body);
                if (typeof parsed === 'object' && parsed !== null) {
                    parsed.csrf_token = CSRF_TOKEN;
                    options.body = JSON.stringify(parsed);
                }
            } catch (e) {
                // Ignore parsing errors
            }
        }
        return fetch(url, options);
    }

    // Observer trigger to refresh Loading and Variance stages
    function onRouteDataChanged() {
        if (!currentRouteId) return;
        loadLoadingStage(currentRouteId);
        loadVarianceAdjustmentStage(currentRouteId);

        // Fetch fresh delivery details to keep sidebar and local cache updated
        const d = document.getElementById('route_data_' + currentRouteId);
        const delId = d ? d.getAttribute('data-delivery-id') : null;
        if (delId && delId !== '0' && delId !== '') {
            fetchSecure('<?= APP_URL ?>/RepTracking/api_get_delivery_details/' + delId)
                .then(res => res.json())
                .then(data => {
                    if (currentRouteId === data.delivery.rep_route_id || currentRouteId === data.delivery.secondary_rep_route_id) {
                        currentDeliveryDetails = data;
                        updateSidebarProgress();
                    }
                });
        }
    }

    function updateSidebarProgress() {
        const steps = [
            { id: 1, icon: '<i class="ph-bold ph-clipboard-text"></i>', name: 'Route Details', statusKey: 'Active' },
            { id: 3, icon: '<i class="ph-bold ph-file-text"></i>', name: 'Bill Adjustments', statusKey: 'Adjustments' },
            { id: 4, icon: '<i class="ph-bold ph-truck"></i>', name: 'Loading', statusKey: 'Loading' },
            { id: 5, icon: '<i class="ph-bold ph-scales"></i>', name: 'Variance Audit', statusKey: 'Variance Adjustment' },
            { id: 6, icon: '<i class="ph-bold ph-map-trifold"></i>', name: 'Delivery Arrange', statusKey: 'Finalizing' },
            { id: 14, icon: '<i class="ph-bold ph-receipt"></i>', name: 'Credit Bills', statusKey: 'Finalizing' },
            { id: 8, icon: '<i class="ph-bold ph-steering-wheel"></i>', name: 'Delivery Execution', statusKey: 'Finalizing' },
            { id: 12, icon: '<i class="ph-bold ph-receipt"></i>', name: 'Expenses', statusKey: 'Finalizing' },
            { id: 7, icon: '<i class="ph-bold ph-currency-dollar"></i>', name: 'Reconciliation', statusKey: 'Finalizing' },
            { id: 9, icon: '<i class="ph-bold ph-package"></i>', name: 'Return Stock', statusKey: 'Finalizing' },
            { id: 10, icon: '<i class="ph-bold ph-credit-card"></i>', name: 'Payments', statusKey: 'Finalizing' },
            { id: 13, icon: '<i class="ph-bold ph-arrow-counter-clockwise"></i>', name: 'Market Returns', statusKey: 'Finalizing' },
            { id: 11, icon: '<i class="ph-bold ph-flag-checkered"></i>', name: 'Finalize', statusKey: 'Finalizing' }
        ];

        const statusSequence = ['Active', 'Pending GL', 'Adjustments', 'Loading', 'Variance Adjustment', 'Finalizing', 'Completed', 'Finalized'];
        let checkStatus = currentRouteStatus;
        if (checkStatus === 'Delivery Arranged') {
            checkStatus = 'Finalizing';
        }
        const currentRouteStatusIndex = statusSequence.indexOf(checkStatus);

        steps.forEach(step => {
            const el = document.getElementById('sb-step-' + step.id);
            if (!el) return;

            // Remove all states
            el.classList.remove('active', 'completed', 'pending', 'locked');
            
            // Step dot element
            const dot = el.querySelector('.step-dot');
            dot.innerHTML = step.icon; // Default back to step sequence number

            // Determine active tab
            if (step.id === currentTabIndex) {
                el.classList.add('active');
            }

            let stepRequiredStatusIndex = statusSequence.indexOf(step.statusKey);
            let isStepCompleted = false;

            // Mark as completed if the route's current status is past the step's required status
            if (currentRouteStatus === 'Completed' || currentRouteStatus === 'Finalized') {
                isStepCompleted = true;
            } else if (currentRouteStatusIndex > stepRequiredStatusIndex) {
                isStepCompleted = true;
            } else if (currentRouteStatus === 'Finalizing' || currentRouteStatus === 'Delivery Arranged') {
                // Inside Finalizing, sub-stages can have completion heuristics:
                const d = document.getElementById('route_data_' + currentRouteId);
                const delId = d ? d.getAttribute('data-delivery-id') : null;
                let delStatus = d ? d.getAttribute('data-delivery-status') : null;
                let vehicleNum = d ? d.getAttribute('data-vehicle-number') : null;
                if (currentDeliveryDetails && currentDeliveryDetails.delivery) {
                    delStatus = currentDeliveryDetails.delivery.status;
                    vehicleNum = currentDeliveryDetails.delivery.vehicle_number;
                }

                const isArranged = delId && delId !== '0' && delId !== '' && vehicleNum && vehicleNum !== 'Pending Vehicle' && vehicleNum !== '';

                if (step.id === 6) {
                    if (isArranged) {
                        isStepCompleted = true;
                    }
                } else if (step.id === 7) {
                    // Check if reconciliation draft was saved
                    let isReconciled = false;
                    const cashVal = parseFloat(document.getElementById('reconActualCash')?.value || 0);
                    if (cashVal > 0) {
                        isReconciled = true;
                    } else if (currentDeliveryDetails && currentDeliveryDetails.delivery && currentDeliveryDetails.delivery.reconciliation_json !== null && currentDeliveryDetails.delivery.reconciliation_json !== '') {
                        isReconciled = true;
                    }
                    if (isReconciled) {
                        isStepCompleted = true;
                    }
                } else if (step.id === 8 && (delStatus === 'Finalizing' || delStatus === 'Finalized')) {
                    isStepCompleted = true; // Delivery is completed if delivery status is Finalizing (meaning driver ended the route) or Finalized
                } else if (step.id === 9) {
                    // Check if stock verified checkbox is checked OR return stock has been saved in delivery details
                    const verifyStockCheck = document.getElementById('settleVerifyStock');
                    let isVerified = verifyStockCheck ? verifyStockCheck.checked : false;
                    if (!isVerified && currentDeliveryDetails && currentDeliveryDetails.delivery && currentDeliveryDetails.delivery.return_stock_json !== null && currentDeliveryDetails.delivery.return_stock_json !== '') {
                        console.log("[SidebarProgress] Found saved return stock in database. Auto-checking checkbox.");
                        isVerified = true;
                        if (verifyStockCheck) {
                            verifyStockCheck.checked = true;
                        }
                    }
                    if (isVerified) {
                        isStepCompleted = true;
                    }
                } else if (step.id === 10) {
                    let allCollectionsApproved = true;
                    let hasChks = false;
                    const settlePaymentChks = document.querySelectorAll('.settle-payment-chk');
                    if (settlePaymentChks.length > 0) {
                        hasChks = true;
                        settlePaymentChks.forEach(chk => {
                            if (!chk.checked) allCollectionsApproved = false;
                        });
                    } else if (currentDeliveryDetails && currentDeliveryDetails.balancing && currentDeliveryDetails.balancing.payments) {
                        const payments = currentDeliveryDetails.balancing.payments;
                        if (payments.length > 0) {
                            hasChks = true;
                            payments.forEach(p => {
                                if (parseInt(p.is_verified) !== 1) allCollectionsApproved = false;
                            });
                        }
                    }
                    if (hasChks && allCollectionsApproved) {
                        isStepCompleted = true;
                    }
                } else if (step.id === 12) {
                    isStepCompleted = true;
                } else if (step.id === 11) {
                    if (currentRouteStatus === 'Completed' || currentRouteStatus === 'Finalized') {
                        isStepCompleted = true;
                    }
                }
            }

            if (isStepCompleted) {
                el.classList.add('completed');
            } else {
                el.classList.add('pending');
            }
        });
    }

    function updateWizardProgress(status) {
        updateSidebarProgress();
    }

    function loadRouteDetails(routeId, el) {
        currentRouteId = routeId;
        currentDeliveryDetails = null; // Reset to prevent stale values from other routes
        
        document.querySelectorAll('.route-item').forEach(i => i.classList.remove('active'));
        if (el) el.classList.add('active');
        else {
            const sidebarEl = document.getElementById('route_' + routeId);
            if (sidebarEl) sidebarEl.classList.add('active');
        }

        const d = document.getElementById('route_data_' + routeId);
        const routeName = d.getAttribute('data-rname');
        const repName = d.getAttribute('data-rep');
        const status = d.getAttribute('data-status');
        const bindingId = d.getAttribute('data-binding-id');
        const isBound = d.getAttribute('data-bound') === '1';
        const delId = d ? d.getAttribute('data-delivery-id') : null;

        currentRouteStatus = status;

        if (delId && delId !== '0' && delId !== '') {
            fetchSecure('<?= APP_URL ?>/RepTracking/api_get_delivery_details/' + delId)
                .then(res => res.json())
                .then(data => {
                    if (currentRouteId === routeId) {
                        currentDeliveryDetails = data;
                        updateSidebarProgress();
                    }
                });
        }

        if (document.getElementById('mhRouteName')) document.getElementById('mhRouteName').innerText = routeName;
        if (document.getElementById('mhRepName')) document.getElementById('mhRepName').innerText = repName;
        if (document.getElementById('mhStart')) document.getElementById('mhStart').innerText = d.getAttribute('data-start');
        if (document.getElementById('mhEnd')) document.getElementById('mhEnd').innerText = d.getAttribute('data-end');
        if (document.getElementById('mhSales')) document.getElementById('mhSales').innerText = d.getAttribute('data-sales');
        if (document.getElementById('mhBills')) document.getElementById('mhBills').innerText = d.getAttribute('data-bills');

        const formattedRouteNo = '#RT-' + String(routeId).padStart(5, '0');
        if (document.getElementById('mhRouteNumber')) document.getElementById('mhRouteNumber').innerText = 'Route ' + formattedRouteNo;
        
        const statusBadge = document.getElementById('mhRouteStatusBadge');
        if (statusBadge) {
            statusBadge.innerText = status;
            statusBadge.style.background = (status === 'Completed' || status === 'Finalized') ? '#e2f0d9' : '#fff3cd';
            statusBadge.style.color = (status === 'Completed' || status === 'Finalized') ? '#2e7d32' : '#d97706';
            statusBadge.style.borderColor = (status === 'Completed' || status === 'Finalized') ? '#2e7d32' : '#d97706';
        }

        const boundSummary = document.getElementById('boundRouteSummaryContainer');
        if (boundSummary) {
            boundSummary.style.display = 'none';
        }
        const isMerged = d.getAttribute('data-merged') === '1';
        if (isMerged) {
            fetchSecure('<?= APP_URL ?>/RepTracking/api_get_bound_routes_summary/' + routeId)
                .then(res => res.json())
                .then(resData => {
                    if (resData.status === 'success') {
                        const elConstituents = document.getElementById('brsConstituentsList');
                        const elTotalCustomers = document.getElementById('brsTotalCustomers');
                        const elTotalInvoices = document.getElementById('brsTotalInvoices');
                        const elTotalValue = document.getElementById('brsTotalValue');
                        const elTotalProducts = document.getElementById('brsTotalProducts');

                        if (elConstituents) elConstituents.innerText = resData.constituents.map(c => `${c.route_name} (ID: #${c.id})`).join(', ');
                        if (elTotalCustomers) elTotalCustomers.innerText = resData.total_customers;
                        if (elTotalInvoices) elTotalInvoices.innerText = resData.total_invoices;
                        if (elTotalValue) elTotalValue.innerText = 'Rs ' + parseFloat(resData.total_value).toLocaleString('en-IN', {minimumFractionDigits:2});
                        if (elTotalProducts) elTotalProducts.innerText = `${resData.unique_products} unique items (Total Qty: ${resData.total_products_qty})`;
                        
                        if (boundSummary) {
                            boundSummary.style.display = 'block';
                        }
                    }
                });
        }

        const midHeaderEl = document.getElementById('midHeader');
        if (midHeaderEl) {
            midHeaderEl.style.visibility = 'visible';
            midHeaderEl.style.display = 'flex';
        }
        document.getElementById('midEmptyState').style.display = 'none';
        document.getElementById('workspaceLayoutWrapper').style.display = 'flex';
        if (document.getElementById('btnViewMap')) document.getElementById('btnViewMap').style.display = 'inline-flex';

        const btnUnbind = document.getElementById('btnUnbindRoute');
        const btnUnbindTab1 = document.getElementById('btnUnbindRouteTab1');
        const hasBinding = isMerged || isBound || (bindingId && bindingId !== '0' && bindingId !== '');
        
        if (btnUnbind) {
            if (hasBinding) {
                btnUnbind.style.display = 'inline-flex';
                if (bindingId) btnUnbind.setAttribute('data-binding-id', bindingId);
            } else {
                btnUnbind.style.display = 'none';
            }
        }
        if (btnUnbindTab1) {
            if (hasBinding) {
                btnUnbindTab1.style.display = 'inline-flex';
                if (bindingId) btnUnbindTab1.setAttribute('data-binding-id', bindingId);
            } else {
                btnUnbindTab1.style.display = 'none';
            }
        }

        updateWizardProgress(status);

        document.getElementById('routeWorkspaceTabs').style.display = 'flex';
        document.getElementById('stageContentWrapper').style.display = 'block';

        // Toggle completed archive banner
        const archiveBanner = document.getElementById('completedArchiveBanner');
        if (archiveBanner) {
            if (status === 'Completed' || status === 'Finalized') {
                archiveBanner.style.display = 'flex';
            } else {
                archiveBanner.style.display = 'none';
            }
        }

        // Close slider
        closeInvoiceSlider();

        // Auto-advance active tab if entering a route that is in Finalizing/Completed/Finalized status
        if (status === 'Finalizing' || status === 'Completed' || status === 'Finalized') {
            const d = document.getElementById('route_data_' + currentRouteId);
            const delId = d ? d.getAttribute('data-delivery-id') : null;
            let delStatus = d ? d.getAttribute('data-delivery-status') : null;
            let vehicleNum = d ? d.getAttribute('data-vehicle-number') : null;
            if (currentDeliveryDetails && currentDeliveryDetails.delivery) {
                delStatus = currentDeliveryDetails.delivery.status;
                vehicleNum = currentDeliveryDetails.delivery.vehicle_number;
            }
            
            const isArranged = delId && delId !== '0' && delId !== '' && vehicleNum && vehicleNum !== 'Pending Vehicle' && vehicleNum !== '';
            const isDeliveryCompleted = delStatus === 'Finalizing' || delStatus === 'Finalized';
            
            if (status === 'Finalizing') {
                if (!isArranged) {
                    currentTabIndex = 6; // Land on Delivery Arrange
                } else if (!isDeliveryCompleted) {
                    currentTabIndex = 8; // Land on Delivery Execution
                } else if (currentTabIndex < 7) {
                    currentTabIndex = 12; // Land on Expenses
                }
            } else {
                // Completed or Finalized
                if (currentTabIndex < 7) {
                    currentTabIndex = 7; // Land on Reconciliation
                }
            }
        }

        // Switch to the last selected index, default to 1 (Details)
        switchRouteTab(currentTabIndex);

        // Transition views
        document.body.classList.add('workspace-showing');
        document.querySelector('.app-workspace').classList.add('workspace-active');
    }

    function switchRouteTab(tabIndex) {
        currentTabIndex = tabIndex;
        
        // Update tab buttons styling
        const tabBtnMap = {
            1: 'auto-evt-button-4',
            3: 'auto-evt-button-6',
            4: 'auto-evt-button-7',
            5: 'auto-evt-button-8',
            6: 'auto-evt-button-9',
            14: 'btnTabCreditBills',
            7: 'auto-evt-button-10',
            8: 'auto-evt-button-11',
            9: 'auto-evt-button-12',
            10: 'auto-evt-button-13',
            11: 'btnTabFinalize',
            12: 'btnTabExpenses',
            13: 'btnTabMarketReturns'
        };
        document.querySelectorAll('#routeWorkspaceTabs .scroll-tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        const activeBtnId = tabBtnMap[tabIndex];
        if (activeBtnId) {
            document.getElementById(activeBtnId)?.classList.add('active');
        }
        
        // Toggle tab panels display
        document.querySelectorAll('.workspace-tab-panel').forEach(panel => {
            panel.style.display = 'none';
        });
        const activePanel = document.getElementById('tabpanel-' + tabIndex);
        if (activePanel) {
            activePanel.style.display = 'block';
            // If Tab 1 is shown, the map container is now visible — re-init if needed and invalidate size
            if (tabIndex === 1 && tab1Map) {
                setTimeout(() => {
                    tab1Map.invalidateSize();
                    if (window._tab1MapBounds) {
                        try { tab1Map.fitBounds(window._tab1MapBounds, { padding: [60, 60], maxZoom: 15 }); } catch(e) {}
                    }
                }, 200);
            }
        }
        
        updateSidebarProgress();
        if (!currentRouteId) return;
        
        // Dynamically load tab data
        switch (tabIndex) {
            case 1:
                loadTab1Details(currentRouteId);
                break;
            case 3:
                loadAdjustmentsStage(currentRouteId);
                break;
            case 4:
                loadLoadingStage(currentRouteId);
                break;
            case 5:
                loadVarianceAdjustmentStage(currentRouteId);
                break;
            case 6:
            case 14:
                loadDispatchStage(currentRouteId);
                break;
            case 7:
                loadTab8Reconciliation(currentRouteId);
                break;
            case 8:
                loadDeliveryLiveStage(currentRouteId);
                break;
            case 9:
                loadTab9ReturnStock(currentRouteId);
                break;
            case 10:
                loadTab10Accounting(currentRouteId);
                break;
            case 11:
                loadTab11Finalize(currentRouteId);
                break;
            case 12:
                loadTabExpenses(currentRouteId);
                break;
            case 13:
                loadMarketReturnsStage(currentRouteId);
                break;
        }
    }

    function loadTab1Details(routeId) {
        const d = document.getElementById('route_data_' + routeId);
        if (!d) return;

        const formattedRouteNo = '#RT-' + String(routeId).padStart(5, '0');

        document.getElementById('tab1RouteNumber').innerText = formattedRouteNo;
        document.getElementById('tab1RouteName').innerText = d.getAttribute('data-rname') || '';
        document.getElementById('tab1RepName').innerText = d.getAttribute('data-rep') || '';
        
        const status = d.getAttribute('data-status') || '';
        const isFinished = (status === 'Completed' || status === 'Finalized');

        // Top toolbar status chip
        const statusBadge = document.getElementById('tab1Status');
        if (statusBadge) {
            statusBadge.innerText = status || '—';
            statusBadge.style.background = isFinished ? '#dcfce7' : '#fef9c3';
            statusBadge.style.color = isFinished ? '#166534' : '#854d0e';
            statusBadge.style.borderColor = isFinished ? '#bbf7d0' : '#fde68a';
        }
        // Right panel status badge (B&W)
        const statusBadge2 = document.getElementById('tab1StatusBadge');
        if (statusBadge2) {
            statusBadge2.innerText = status || '—';
            statusBadge2.style.background = isFinished ? '#f0fdf4' : '#f8fafc';
            statusBadge2.style.color = isFinished ? '#166534' : '#475569';
            statusBadge2.style.borderColor = isFinished ? '#86efac' : '#e2e8f0';
        }

        const repSales = d.getAttribute('data-rep-sales') || d.getAttribute('data-sales') || '0.00';
        const actualSales = d.getAttribute('data-actual-sales') || d.getAttribute('data-sales') || '0.00';
        document.getElementById('tab1SalesValue').innerText = 'Rs ' + repSales;
        if (document.getElementById('tab1ActualSalesValue')) {
            document.getElementById('tab1ActualSalesValue').innerText = 'Rs ' + actualSales;
        }
        document.getElementById('tab1BillsCount').innerText = d.getAttribute('data-bills') || '0';

        document.getElementById('tab1StartTime').innerText = d.getAttribute('data-start-time') || '';
        document.getElementById('tab1EndTime').innerText = d.getAttribute('data-end-time') || '';
        document.getElementById('tab1StartMeter').innerText = d.getAttribute('data-start') || '';
        document.getElementById('tab1EndMeter').innerText = d.getAttribute('data-end') || '';
        
        const start = parseFloat(d.getAttribute('data-start')) || 0;
        const end = parseFloat(d.getAttribute('data-end')) || 0;
        document.getElementById('tab1Distance').innerText = (end > start) ? (end - start) + ' km' : 'Active';

        const isReadOnly = (currentRouteStatus === 'Completed' || currentRouteStatus === 'Finalized');
        const notesTextarea = document.getElementById('tab1RouteNotes');
        if (notesTextarea) {
            notesTextarea.readOnly = isReadOnly;
            fetchSecure('<?= APP_URL ?>/RepTracking/api_get_route_details/' + routeId)
                .then(res => res.json())
                .then(data => { notesTextarea.value = data.notes || ''; });
        }

        // Update inline map route name labels
        const mapNameEl = document.getElementById('tab1MapRouteName');
        if (mapNameEl) mapNameEl.innerText = document.getElementById('tab1RouteName')?.innerText || 'Route';

        // Load Unproductive Visits for Route Details Tab
        loadTab1UnproductiveVisits(routeId);

        // Load inline GPS map
        loadTab1RoutePath(routeId);
    }

    function loadTab1UnproductiveVisits(routeId) {
        const tbody = document.getElementById('tab1UnproductiveTbody');
        const badge = document.getElementById('tab1UnproductiveBadge');
        const countEl = document.getElementById('tab1UnproductiveCount');

        if (!tbody) return;

        tbody.innerHTML = '<tr><td colspan="4" style="text-align:center; color:#888; padding:15px;"><i class="ph ph-spinner"></i> Loading unproductive visits...</td></tr>';

        fetchSecure('<?= APP_URL ?>/RepTracking/api_get_unproductive_visits/' + routeId)
            .then(res => res.json())
            .then(data => {
                console.log('[Route Details] Unproductive visits loaded:', data);
                const visits = data.visits || [];
                const count = visits.length;

                if (countEl) countEl.innerText = count;
                if (badge) badge.innerText = count + (count === 1 ? ' Visit' : ' Visits');

                if (count === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" style="text-align:center; color:#888; padding:12px;">No unproductive visits recorded for this route.</td></tr>';
                    return;
                }

                let html = '';
                visits.forEach(v => {
                    let reasonDisplay = v.reason || 'Other';
                    if (v.reason === 'Other' && v.custom_reason) {
                        reasonDisplay += ' (' + v.custom_reason + ')';
                    } else if (v.custom_reason) {
                        reasonDisplay += ' - ' + v.custom_reason;
                    }

                    let locationDisplay = 'N/A';
                    let mapBtnHtml = '';
                    if (v.latitude && v.longitude) {
                        locationDisplay = `${parseFloat(v.latitude).toFixed(5)}, ${parseFloat(v.longitude).toFixed(5)}`;
                        mapBtnHtml = `<button type="button" onclick="openMapModal(${routeId})" style="padding:3px 8px; background:#fff7ed; color:#c2410c; border:1px solid #ffedd5; border-radius:4px; font-size:11px; font-weight:bold; cursor:pointer; display:inline-flex; align-items:center; gap:4px;"><i class="ph ph-map-pin"></i> View Map</button>`;
                    }

                    const custName = v.customer_name ? `${v.customer_name} ${v.customer_phone ? '(' + v.customer_phone + ')' : ''}` : 'Unknown Customer';
                    const visitTime = v.visit_time || 'N/A';

                    html += `
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:8px; font-weight:500; font-family:monospace; color:#475569; font-size:11px;">${visitTime}</td>
                            <td style="padding:8px; font-weight:bold; color:#1e293b; font-size:11px;">${custName}</td>
                            <td style="padding:8px;"><span style="background:#fff7ed; color:#c2410c; padding:2px 7px; border-radius:4px; font-weight:600; font-size:10px; border:0.5px solid #fed7aa;">${reasonDisplay}</span></td>
                            <td style="padding:8px; text-align:center;">${mapBtnHtml}</td>
                        </tr>
                    `;
                });
                tbody.innerHTML = html;
            })
            .catch(err => {
                console.error('[Route Details] Error loading unproductive visits:', err);
                tbody.innerHTML = '<tr><td colspan="4" style="text-align:center; color:#dc2626; padding:12px;">Failed to load unproductive visits.</td></tr>';
            });
    }

    function saveRouteNotes() {
        if (!currentRouteId) return;
        const notes = document.getElementById('tab1RouteNotes').value;
        fetchSecure('<?= APP_URL ?>/RepTracking/api_save_route_notes', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ route_id: currentRouteId, notes: notes })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                alert("Route notes saved successfully!");
            } else {
                alert("Error: " + data.message);
            }
        });
    }



    let cachedOutstandingBills = [];
    let selectedCreditBills = [];

    function loadOutstandingBillsChecklist(routeId, delId) {
        const container = document.getElementById('adjDaBillsContainer');
        if (!container) return;

        container.innerHTML = '<p style="text-align:center; color:#888;">Loading credit bills... </p>';
        
        // Reset cached variables
        cachedOutstandingBills = [];
        selectedCreditBills = [];
        
        // Clear search inputs
        const searchInput = document.getElementById('creditBillsSearch');
        if (searchInput) searchInput.value = '';

        const loadBillsData = () => {
            fetchSecure('<?= APP_URL ?>/RepTracking/api_get_outstanding_bills/' + routeId)
                .then(res => res.json())
                .then(data => {
                    if (data.status !== 'success' || !data.bills) {
                        container.innerHTML = '<p style="text-align:center; color:#888; margin:10px 0;">Error loading outstanding credit bills.</p>';
                        return;
                    }
                    cachedOutstandingBills = data.bills;
                    
                    // Populate route filter dropdown
                    const routeFilter = document.getElementById('creditBillsRouteFilter');
                    if (routeFilter) {
                        routeFilter.innerHTML = `
                            <option value="all" style="background:#ffffff; color:#0f172a;">All Routes</option>
                            <option value="none" style="background:#ffffff; color:#0f172a;">No Route / Unassigned</option>
                        `;
                        
                        // Extract unique routes
                        const routeMap = {};
                        cachedOutstandingBills.forEach(cust => {
                            cust.bills.forEach(b => {
                                if (b.rep_route_id && b.route_name) {
                                    routeMap[b.rep_route_id] = b.route_name;
                                }
                            });
                        });
                        
                        // Add options to filter
                        Object.keys(routeMap).forEach(rid => {
                            const opt = document.createElement('option');
                            opt.value = rid;
                            opt.textContent = routeMap[rid];
                            opt.style.cssText = 'background:#ffffff; color:#0f172a;';
                            routeFilter.appendChild(opt);
                        });
                    }

                    // Run initial filter rendering
                    filterCreditBillsList();
                });
        };

        if (delId && delId !== '0' && delId !== '') {
            fetchSecure('<?= APP_URL ?>/RepTracking/api_get_delivery_details/' + delId)
                .then(res => res.json())
                .then(dData => {
                    if (dData.delivery && dData.delivery.selected_credit_invoices) {
                        try {
                            selectedCreditBills = JSON.parse(dData.delivery.selected_credit_invoices).map(id => parseInt(id));
                        } catch (e) {}
                    }
                    loadBillsData();
                });
        } else {
            loadBillsData();
        }
    }

    function filterCreditBillsList() {
        const container = document.getElementById('adjDaBillsContainer');
        if (!container) return;

        const isReadOnly = (currentRouteStatus === 'Completed' || currentRouteStatus === 'Finalized');
        const searchInput = document.getElementById('creditBillsSearch');
        const routeFilter = document.getElementById('creditBillsRouteFilter');
        
        const searchQuery = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const selectedRoute = routeFilter ? routeFilter.value : 'all';

        let filteredBillsCount = 0;
        let totalSelectedAmount = 0;
        let html = '';
        let rowIndex = 0;

        cachedOutstandingBills.forEach(cust => {
            const customerName = cust.customer_name.toLowerCase();
            const customerMatches = customerName.includes(searchQuery);

            const matchedBills = cust.bills.filter(b => {
                const invoiceNumber = b.invoice_number.toLowerCase();
                const invoiceMatches = invoiceNumber.includes(searchQuery);

                if (searchQuery && !customerMatches && !invoiceMatches) {
                    return false;
                }

                if (selectedRoute === 'none') {
                    if (b.rep_route_id) return false;
                } else if (selectedRoute !== 'all') {
                    if (String(b.rep_route_id) !== String(selectedRoute)) return false;
                }

                return true;
            });

            matchedBills.forEach(b => {
                filteredBillsCount++;
                rowIndex++;
                const isChecked = selectedCreditBills.includes(parseInt(b.id));
                const trueVal = parseFloat(b.true_grand_total) || 0;
                if (isChecked) {
                    totalSelectedAmount += trueVal;
                }

                let amtFormatted = trueVal.toLocaleString('en-IN', {minimumFractionDigits:2});
                const routeTag = b.route_name ? `<span style="font-size:10px; background:#e0f2fe; color:#0369a1; padding:2px 6px; border-radius:4px; margin-left:6px; font-weight:600;">${b.route_name}</span>` : `<span style="font-size:10px; background:#f1f5f9; color:#475569; padding:2px 6px; border-radius:4px; margin-left:6px; font-weight:600;">Unassigned</span>`;
                
                const bg = (rowIndex % 2 === 0) ? '#f8fafc' : '#ffffff';
                const rowBg = isChecked ? '#f0fdf4' : bg;

                html += `
                    <tr style="background:${rowBg}; transition:background 0.15s; border-bottom:1px solid #f1f5f9;" onmouseover="if(!this.querySelector('.adj-da-bill-checkbox').checked) this.style.background='#eff6ff'" onmouseout="if(!this.querySelector('.adj-da-bill-checkbox').checked) this.style.background='${bg}'">
                        <td style="padding:12px 16px; text-align:center; vertical-align:middle;">
                            <input type="checkbox" class="adj-da-bill-checkbox" value="${b.id}" style="width:16px; height:16px; cursor:pointer;" ${isReadOnly ? 'disabled' : ''} ${isChecked ? 'checked' : ''} onchange="toggleCreditBillSelection(this)">
                        </td>
                        <td style="padding:12px 16px; vertical-align:middle;">
                            <div style="font-weight:700; font-size:13px; color:#0f172a;">${cust.customer_name}</div>
                            ${cust.mca_name ? `<div style="font-size:11px; color:#64748b; margin-top:2px;"><i class="ph ph-map-pin" style="font-size:11px;"></i> ${cust.mca_name}</div>` : ''}
                        </td>
                        <td style="padding:12px 16px; vertical-align:middle;">
                            <span style="font-weight:700; font-size:13px; color:#1e293b; font-family:monospace;">${b.invoice_number}</span>
                            ${routeTag}
                        </td>
                        <td style="padding:12px 16px; text-align:center; vertical-align:middle; font-size:12px; color:#475569; font-weight:500;">
                            ${b.invoice_date}
                        </td>
                        <td style="padding:12px 16px; text-align:right; vertical-align:middle; font-weight:700; font-family:monospace; color:#059669; font-size:13px;">
                            Rs ${amtFormatted}
                        </td>
                    </tr>
                `;
            });
        });

        if (filteredBillsCount === 0) {
            container.innerHTML = `<tr><td colspan="5" style="text-align:center; padding:32px; color:#94a3b8; font-size:13px;">No credit bills match the search/filter criteria.</td></tr>`;
        } else {
            container.innerHTML = html;
        }

        // Update Dock Counter & Total
        const countText = document.getElementById('creditBillsCountText');
        if (countText) {
            let amtStr = totalSelectedAmount.toLocaleString('en-IN', {minimumFractionDigits:2});
            countText.innerHTML = `<strong style="color:#38bdf8;">${selectedCreditBills.length}</strong> bills <span style="font-weight:normal; color:#cbd5e1; margin:0 4px;">|</span> <strong style="color:#4ade80;">Rs ${amtStr}</strong>`;
        }

        // Update Master Select All checkbox state
        const masterCb = document.getElementById('selectAllCreditBills');
        if (masterCb) {
            const allCbs = document.querySelectorAll('.adj-da-bill-checkbox');
            if (allCbs.length > 0) {
                masterCb.checked = Array.from(allCbs).every(cb => cb.checked);
            } else {
                masterCb.checked = false;
            }
        }
    }

    function toggleSelectAllCreditBills(masterCheckbox) {
        const checkboxes = document.querySelectorAll('.adj-da-bill-checkbox');
        checkboxes.forEach(cb => {
            if (!cb.disabled) {
                const billId = parseInt(cb.value);
                if (masterCheckbox.checked) {
                    if (!selectedCreditBills.includes(billId)) {
                        selectedCreditBills.push(billId);
                    }
                } else {
                    selectedCreditBills = selectedCreditBills.filter(id => id !== billId);
                }
            }
        });
        filterCreditBillsList();
    }

    function toggleCreditBillSelection(checkbox) {
        const billId = parseInt(checkbox.value);
        if (checkbox.checked) {
            if (!selectedCreditBills.includes(billId)) {
                selectedCreditBills.push(billId);
            }
        } else {
            selectedCreditBills = selectedCreditBills.filter(id => id !== billId);
        }
        filterCreditBillsList();
    }

    function loadAdjustmentsStage(routeId) {
        const tbody = document.getElementById('adjustmentsInvoicesTbody');
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Loading Sales Orders... </td></tr>';

        const isReadOnly = (currentRouteStatus === 'Completed' || currentRouteStatus === 'Finalized');

        // Hide/Show operational buttons floating dock in Tab 3
        const opsHeader = document.getElementById('tab3FloatingDock');
        if (opsHeader) {
            opsHeader.style.display = isReadOnly ? 'none' : 'flex';
        }

        // Load outstanding bills and pre-check already selected ones if delivery is already arranged
        const rdata = document.getElementById('route_data_' + routeId);
        const delId = rdata ? rdata.getAttribute('data-delivery-id') : null;
        loadOutstandingBillsChecklist(routeId, delId);

        fetchSecure('<?= APP_URL ?>/RepTracking/api_get_route_details/' + routeId)
            .then(res => res.json())
            .then(data => {
                const bills = data.bills || [];
                tbody.innerHTML = '';
                let totalSum = 0;
                if (bills.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:20px; color:#888;">No sales orders attached to this route.</td></tr>';
                    const totalGrandEl = document.getElementById('adjustmentsTotalGrand');
                    if (totalGrandEl) totalGrandEl.innerText = 'Rs 0.00';
                } else {
                    bills.forEach(bill => {
                        totalSum += parseFloat(bill.true_grand_total || 0);
                        let time = new Date(bill.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                        
                        let dropdownHtml = '';
                        if (!isReadOnly) {
                            dropdownHtml += `
                                <button class="dots-dropdown-item" onclick="event.stopPropagation(); editSalesOrder(${bill.id})">
                                    <i class="ph ph-pencil"></i> Edit
                                </button>
                                <button class="dots-dropdown-item danger" onclick="event.stopPropagation(); confirmDeleteSalesOrder(${bill.id}, '${bill.invoice_number}')">
                                    <i class="ph ph-trash"></i> Delete
                                </button>
                                <button class="dots-dropdown-item" onclick="event.stopPropagation(); detachInvoice(${bill.id})">
                                    <i class="ph ph-link-break"></i> Remove
                                </button>
                                <button class="dots-dropdown-item" onclick="event.stopPropagation(); openMoveInvoiceModal(${bill.id}, '${bill.invoice_number}')">
                                    <i class="ph ph-arrow-square-out"></i> Move
                                </button>
                                <div class="dots-dropdown-divider"></div>
                            `;
                        }
                        dropdownHtml += `
                            <button class="dots-dropdown-item" onclick="event.stopPropagation(); openInvoiceSlider(${bill.id})">
                                <i class="ph ph-eye"></i> View Invoice
                            </button>
                            <button class="dots-dropdown-item" onclick="event.stopPropagation(); printInvoice(${bill.id})">
                                <i class="ph ph-printer"></i> Print
                            </button>
                            <button class="dots-dropdown-item" data-customer-id="${bill.customer_id || 0}" data-customer="${(bill.customer_name || '').replace(/"/g, '&quot;')}" onclick="event.stopPropagation(); viewCustomerProfile(this.getAttribute('data-customer-id'), this.getAttribute('data-customer'))">
                                <i class="ph ph-user"></i> View Customer
                            </button>
                            <button class="dots-dropdown-item" onclick="event.stopPropagation(); downloadInvoicePdf(${bill.id})">
                                <i class="ph ph-file-pdf"></i> Download PDF
                            </button>
                            <button class="dots-dropdown-item" onclick="event.stopPropagation(); exportInvoiceExcel(${bill.id})">
                                <i class="ph ph-file-xls"></i> Export Excel
                            </button>
                        `;

                        let actionBtn = `
                            <div class="dots-menu-container">
                                <button class="dots-btn" onclick="toggleDotsMenu(event, ${bill.id})">
                                    <i class="ph-bold ph-dots-three-vertical"></i>
                                </button>
                                <div class="dots-dropdown" id="dots-dropdown-${bill.id}">
                                    ${dropdownHtml}
                                </div>
                            </div>
                        `;

                        tbody.innerHTML += `
                            <tr onclick="openInvoiceSlider(${bill.id})" style="cursor:pointer;">
                                <td style="font-weight:bold; color:var(--primary);">${bill.invoice_number}</td>
                                <td>${time}</td>
                                <td><strong>${bill.customer_name}</strong></td>
                                <td style="text-align:right; font-family:monospace; font-weight:bold;">${parseFloat(bill.true_grand_total).toLocaleString('en-IN', {minimumFractionDigits:2})}</td>
                                <td style="text-align:center;" onclick="event.stopPropagation();">${actionBtn}</td>
                            </tr>
                        `;
                    });
                    const totalGrandEl = document.getElementById('adjustmentsTotalGrand');
                    if (totalGrandEl) totalGrandEl.innerText = 'Rs ' + totalSum.toLocaleString('en-IN', {minimumFractionDigits:2, maximumFractionDigits:2});
                }
            });
    }

    function detachInvoice(invoiceId) {
        if (!confirm("Are you sure you want to remove/detach this Sales Order from this route?")) return;
        fetchSecure('<?= APP_URL ?>/RepTracking/api_detach_invoice', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ invoice_id: invoiceId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                alert("Sales Order successfully detached from route!");
                onRouteDataChanged();
                loadAdjustmentsStage(currentRouteId);
            } else {
                alert("Error: " + data.message);
            }
        });
    }

    function submitAdjustmentsLogisticsArrange() {
        console.log('[ARRANGE LOGISTICS] submitAdjustmentsLogisticsArrange called', {
            currentRouteId: currentRouteId,
            date: document.getElementById('adjDaDate')?.value,
            vehicle: document.getElementById('adjDaVehicle')?.value,
            driver: document.getElementById('adjDaDriver')?.value,
            partner: document.getElementById('adjDaPartner')?.value
        });
        const date = document.getElementById('adjDaDate').value;
        const vehicle = document.getElementById('adjDaVehicle').value;
        const driver = document.getElementById('adjDaDriver').value;
        const partner = document.getElementById('adjDaPartner').value;

        if (!vehicle) { alert("Please select a Vehicle Number."); return; }
        if (!driver) { alert("Please select a Driver Name."); return; }

        const checkedBills = [...selectedCreditBills];

        const payload = {
            rep_route_id: currentRouteId,
            secondary_rep_route_id: null,
            delivery_date: date,
            vehicle_number: vehicle,
            driver_name: driver,
            partner_name: partner,
            selected_credit_invoices: checkedBills
        };

        console.log('[ARRANGE LOGISTICS] Sending payload:', payload);

        fetchSecure('<?= APP_URL ?>/RepTracking/arrange', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            console.log('[ARRANGE LOGISTICS] Server response:', data);
            if (data.status === 'success') {
                alert("Delivery arranged successfully!");
                const rdata = document.getElementById('route_data_' + currentRouteId);
                if (rdata) {
                    rdata.setAttribute('data-delivery-id', data.delivery_id);
                }
                onRouteDataChanged();
                loadDispatchStage(currentRouteId);
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(err => {
            console.error('[ARRANGE LOGISTICS] Fetch error:', err);
        });
    }

    let currentLoadingStageItems = [];
    let currentLoadingDeliveryId = 0;

    function loadLoadingStage(routeId) {
        const box = document.getElementById('loadingBox');
        if (!box) return;
        box.innerHTML = '<div style="padding:25px; text-align:center; color:#64748b; font-weight:600;"><i class="ph ph-spinner spin"></i> Loading warehouse verification items...</div>';

        fetchSecure('<?= APP_URL ?>/RepTracking/api_get_route_variances/' + routeId)
            .then(res => res.json())
            .then(data => {
                if (data.status !== 'success') {
                    box.innerHTML = `<p style="color:red; text-align:center; padding:10px;">Error loading data: ${data.message || 'Unknown error'}</p>`;
                    return;
                }

                const deliveries = data.deliveries || [];
                if (deliveries.length === 0) {
                    box.innerHTML = '<div style="background:#fff; border:1px solid #cbd5e1; padding:30px; text-align:center; border-radius:8px; color:#64748b; font-size:13px;">No delivery manifest initialized for this route yet.</div>';
                    return;
                }

                const del = deliveries[0];
                currentLoadingDeliveryId = del.delivery_id;
                currentLoadingStageItems = del.items || [];

                renderLoadingStageUI();
            })
            .catch(err => {
                console.error("Error loading route loading details:", err);
                box.innerHTML = `<p style="color:red; text-align:center; padding:10px;">Error loading data: ${err.message}</p>`;
            });
    }

    function filterLoadingProductsBySearch(query) {
        const q = (query || '').toLowerCase().trim();
        document.querySelectorAll('.loading-table-row').forEach(row => {
            const name = (row.getAttribute('data-item-name') || '').toLowerCase();
            row.style.display = (!q || name.includes(q)) ? '' : 'none';
        });
    }

    function renderLoadingStageUI() {
        const box = document.getElementById('loadingBox');
        if (!box) return;

        const isReadOnly = (currentRouteStatus === 'Completed' || currentRouteStatus === 'Finalized');
        const verifiedCount = currentLoadingStageItems.filter(item => item.is_verified === 1).length;
        const totalCount = currentLoadingStageItems.length;

        let listHtml = '';
        if (currentLoadingStageItems.length === 0) {
            listHtml = '<tr><td colspan="5" style="text-align:center; padding:20px; color:#64748b; font-size:13px;">No loading items found.</td></tr>';
        } else {
            currentLoadingStageItems.forEach(item => {
                const req = parseFloat(item.required_qty);
                const loaded = item.final_loaded_qty !== null ? parseFloat(item.final_loaded_qty) : req;
                const diff = loaded - req;

                let rowBg = '';
                let statusBadge = '';
                if (Math.abs(diff) < 0.01) {
                    rowBg = 'background-color: #f0fdf4;';
                    statusBadge = '<span style="padding:3px 10px; border-radius:12px; font-size:11px; font-weight:bold; background:#dcfce7; color:#15803d; display:inline-flex; align-items:center; gap:4px;"><i class="ph ph-check-circle"></i> Match</span>';
                } else if (diff < 0) {
                    rowBg = 'background-color: #fef2f2;';
                    statusBadge = `<span style="padding:3px 10px; border-radius:12px; font-size:11px; font-weight:bold; background:#fee2e2; color:#b91c1c;">Short (${diff.toFixed(1)})</span>`;
                } else {
                    rowBg = 'background-color: #fff7ed;';
                    statusBadge = `<span style="padding:3px 10px; border-radius:12px; font-size:11px; font-weight:bold; background:#ffedd5; color:#c2410c;">Over (+${diff.toFixed(1)})</span>`;
                }

                let nameHtml = `<div style="font-weight:700; color:#0f172a; font-size:13px;">${escapeHtml(item.item_name)}</div>`;
                if (item.category_name) {
                    nameHtml += `<div style="font-size:10px; color:#64748b; font-weight:600; text-transform:uppercase; letter-spacing:0.04em; margin-top:1px;">${escapeHtml(item.category_name)}</div>`;
                }
                if (item.replaced_by_name) {
                    nameHtml += `<div style="font-size:11px; color:#7c3aed; font-weight:bold; margin-top:2px;"><i class="ph ph-swap"></i> Replaced By: ${escapeHtml(item.replaced_by_name)} (Qty: ${item.replacement_qty})</div>`;
                } else if (item.replaces_name) {
                    nameHtml += `<div style="font-size:11px; color:#15803d; font-weight:bold; margin-top:2px;"><i class="ph ph-star"></i> Substituted for: ${escapeHtml(item.replaces_name)}</div>`;
                }

                const isVerified = (item.is_verified === 1);

                listHtml += `
                    <tr class="loading-table-row" data-item-name="${escapeHtml(item.item_name.toLowerCase())}" style="${rowBg}">
                        <td style="padding:12px 18px;">${nameHtml}</td>
                        <td style="padding:12px 18px; text-align:center; font-weight:bold; font-family:monospace; font-size:13px; color:#334155;">${req}</td>
                        <td style="padding:12px 18px; text-align:center;">
                            <div style="display:inline-flex; align-items:center; gap:4px;">
                                <button type="button" onclick="adjustLoadingQty(${item.id}, ${loaded - 1})" ${isReadOnly ? 'disabled' : ''} style="width:28px; height:28px; border:1px solid #cbd5e1; background:#fff; border-radius:6px; font-weight:bold; cursor:pointer; font-size:14px; color:#334155;">-</button>
                                <input type="number" step="1" min="0" value="${loaded}" onchange="adjustLoadingQty(${item.id}, this.value)" ${isReadOnly ? 'disabled' : ''} style="width:65px; text-align:center; padding:4px; border:1px solid #cbd5e1; border-radius:6px; font-weight:bold; font-family:monospace; font-size:13px;">
                                <button type="button" onclick="adjustLoadingQty(${item.id}, ${loaded + 1})" ${isReadOnly ? 'disabled' : ''} style="width:28px; height:28px; border:1px solid #cbd5e1; background:#fff; border-radius:6px; font-weight:bold; cursor:pointer; font-size:14px; color:#334155;">+</button>
                            </div>
                        </td>
                        <td style="padding:12px 18px; text-align:center;">${statusBadge}</td>
                        <td style="padding:12px 18px; text-align:center;">
                            <div style="display:inline-flex; align-items:center; gap:6px;">
                                <button type="button" onclick="toggleItemVerified(${item.id}, ${isVerified ? 0 : 1})" ${isReadOnly ? 'disabled' : ''} style="padding:6px 12px; border:none; border-radius:6px; font-size:11px; font-weight:700; cursor:pointer; background:${isVerified ? '#16a34a' : '#64748b'}; color:#fff; display:inline-flex; align-items:center; gap:4px; transition:0.2s;">
                                    <i class="ph ${isVerified ? 'ph-check-circle' : 'ph-circle'}"></i> ${isVerified ? 'Verified ✓' : 'Verify'}
                                </button>
                                ${(!item.replaced_by_name && !item.replaces_name && req > 0 && !isReadOnly) ? `
                                    <button type="button" onclick="openReplaceProductModal(${item.id})" style="padding:6px 10px; background:#673ab7; color:#fff; border:none; border-radius:6px; font-size:11px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:4px;">
                                        <i class="ph ph-swap"></i> Replace
                                    </button>
                                ` : ''}
                            </div>
                        </td>
                    </tr>
                `;
            });
        }

        const tbody = document.getElementById('loadingChecklistTbody');
        if (tbody) {
            tbody.innerHTML = listHtml;
            const btnVerify = document.getElementById('btnVerifyAllItemsText');
            if (btnVerify) {
                btnVerify.innerHTML = `<i class="ph ph-checks" style="font-size:16px;"></i> Verify All Items (${verifiedCount}/${totalCount})`;
            }
            return;
        }

        box.innerHTML = `
            <div style="background:transparent; margin-bottom:20px;">
                <div style="overflow-x:auto;">
                    <table class="data-table" style="margin:0; width:100%;">
                        <thead>
                            <tr style="background:#f8fafc; border-bottom:2px solid #e2e8f0;">
                                <th style="padding:14px 18px; text-align:left;">Product Name</th>
                                <th style="padding:14px 18px; text-align:center; width:120px;">Required Qty</th>
                                <th style="padding:14px 18px; text-align:center; width:170px;">Verified Loaded Qty</th>
                                <th style="padding:14px 18px; text-align:center; width:140px;">Variance Status</th>
                                <th style="padding:14px 18px; text-align:center; width:190px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="loadingChecklistTbody">${listHtml}</tbody>
                    </table>
                </div>

                <!-- Dark Pill Floating Dock with Search & Labeled Action Buttons -->
                <div id="tab4FloatingDock" style="position:fixed; bottom:24px; left:50%; transform:translateX(-50%); z-index:1000; display:flex; align-items:center; gap:10px; padding:8px 14px; background:rgba(30, 41, 59, 0.96); backdrop-filter:blur(24px) saturate(200%); -webkit-backdrop-filter:blur(24px) saturate(200%); border:1px solid rgba(255, 255, 255, 0.15); border-radius:100px; box-shadow:0 16px 40px rgba(15, 23, 42, 0.35);">
                    
                    <!-- Search Input Pill inside Dark Bar -->
                    <div style="display:flex; align-items:center; background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.18); border-radius:100px; padding:6px 14px; gap:8px; width:220px; transition:all 0.18s;">
                        <i class="ph ph-magnifying-glass" style="color:#94a3b8; font-size:16px;"></i>
                        <input type="text" id="searchLoadingProductsInput" oninput="filterLoadingProductsBySearch(this.value)" placeholder="Search loading items..." style="background:transparent; border:none; outline:none; color:#ffffff; font-size:13px; width:100%; font-family:inherit;" onfocus="this.parentElement.style.borderColor='#007aff'; this.parentElement.style.background='rgba(255,255,255,0.18)'" onblur="this.parentElement.style.borderColor='rgba(255,255,255,0.18)'; this.parentElement.style.background='rgba(255,255,255,0.12)'">
                    </div>

                    <div style="width:1px; height:22px; background:rgba(255,255,255,0.2); margin:0 2px;"></div>

                    <!-- Labeled Buttons with Icons -->
                    <button type="button" onclick="printLoadingSheet('final')" style="padding:9px 16px; background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.2); color:#ffffff; border-radius:100px; font-weight:700; font-size:13px; cursor:pointer; display:inline-flex; align-items:center; gap:8px; white-space:nowrap; transition:all 0.18s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.12)'">
                        <i class="ph ph-printer" style="font-size:16px;"></i> Print Loading Sheet
                    </button>

                    ${!isReadOnly ? `
                        <button type="button" onclick="verifyAllLoadingItems()" id="btnVerifyAllItemsText" style="padding:9px 16px; background:#0284c7; border:none; color:#ffffff; border-radius:100px; font-weight:700; font-size:13px; cursor:pointer; display:inline-flex; align-items:center; gap:8px; white-space:nowrap; box-shadow:0 3px 12px rgba(2,132,199,0.3); transition:all 0.18s;" onmouseover="this.style.transform='scale(1.04)'; this.style.background='#0369a1'" onmouseout="this.style.transform='scale(1)'; this.style.background='#0284c7'">
                            <i class="ph ph-checks" style="font-size:16px;"></i> Verify All Items (${verifiedCount}/${totalCount})
                        </button>
                        <button type="button" onclick="completeLoadingVerification()" style="padding:9px 18px; background:#16a34a; border:none; color:#ffffff; border-radius:100px; font-weight:700; font-size:13px; cursor:pointer; display:inline-flex; align-items:center; gap:8px; white-space:nowrap; box-shadow:0 3px 12px rgba(22,163,74,0.3); transition:all 0.18s;" onmouseover="this.style.transform='scale(1.04)'; this.style.background='#15803d'" onmouseout="this.style.transform='scale(1)'; this.style.background='#16a34a'">
                            <i class="ph ph-check-circle" style="font-size:16px;"></i> Complete Loading
                        </button>
                    ` : ''}
                </div>
            </div>
        `;
    }

    function adjustLoadingQty(itemId, newQty) {
        const item = currentLoadingStageItems.find(i => parseInt(i.id) === parseInt(itemId));
        if (!item) return;

        newQty = Math.max(0, parseFloat(newQty) || 0);
        item.final_loaded_qty = newQty;
        item.variance = newQty - item.required_qty;
        item.is_verified = 0; // Reset verification on qty change

        fetchSecure('<?= APP_URL ?>/Picking/api_update_final_item', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id: item.id,
                final_loaded_qty: newQty,
                is_verified: 0,
                user_id: <?= $_SESSION['user_id'] ?? 1 ?>
            })
        })
        .then(res => res.json())
        .then(data => {
            renderLoadingStageUI();
        })
        .catch(err => console.error("Error updating item qty:", err));
    }

    function toggleItemVerified(itemId, isVerified) {
        const item = currentLoadingStageItems.find(i => parseInt(i.id) === parseInt(itemId));
        if (!item) return;

        item.is_verified = isVerified;
        const finalQty = item.final_loaded_qty !== null ? item.final_loaded_qty : item.required_qty;

        fetchSecure('<?= APP_URL ?>/Picking/api_update_final_item', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id: item.id,
                final_loaded_qty: finalQty,
                is_verified: isVerified,
                user_id: <?= $_SESSION['user_id'] ?? 1 ?>
            })
        })
        .then(res => res.json())
        .then(data => {
            renderLoadingStageUI();
        })
        .catch(err => console.error("Error toggling verification:", err));
    }

    function verifyAllLoadingItems() {
        if (!currentLoadingStageItems.length) return;

        let promises = [];
        currentLoadingStageItems.forEach(item => {
            item.is_verified = 1;
            const finalQty = item.final_loaded_qty !== null ? item.final_loaded_qty : item.required_qty;
            const p = fetchSecure('<?= APP_URL ?>/Picking/api_update_final_item', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id: item.id,
                    final_loaded_qty: finalQty,
                    is_verified: 1,
                    user_id: <?= $_SESSION['user_id'] ?? 1 ?>
                })
            });
            promises.push(p);
        });

        Promise.all(promises).then(() => {
            renderLoadingStageUI();
        });
    }

    function completeLoadingVerification() {
        if (!currentLoadingDeliveryId) {
            alert("No delivery ID found for loading verification.");
            return;
        }

        if (!confirm("Are you sure you want to complete loading verification? This will finalize verified stock and transition the route directly into Variance Audit.")) {
            return;
        }

        fetchSecure('<?= APP_URL ?>/Picking/api_complete_loading', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ delivery_id: currentLoadingDeliveryId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message || "Loading verification completed! Route moved to Variance Audit.");
                currentRouteStatus = 'Variance Adjustment';
                // Transition RepTracking workflow to Step 4 (Variance Audit - tab index 5)
                switchRouteTab(5);
            } else {
                alert(data.error || "Failed to complete loading verification.");
            }
        })
        .catch(err => {
            alert("Error completing loading verification: " + err.message);
        });
    }

    // Modal handlers for Product Replacement / Substitution
    function openReplaceProductModal(itemId) {
        const item = currentLoadingStageItems.find(i => parseInt(i.id) === parseInt(itemId));
        if (!item) return;

        document.getElementById('replaceDeliveryId').value = currentLoadingDeliveryId;
        document.getElementById('replaceOrigItemId').value = item.item_id;
        document.getElementById('replaceOrigVarOptId').value = item.variation_option_id || '';
        document.getElementById('replaceOrigName').innerText = item.item_name;
        document.getElementById('replaceOrigQty').innerText = item.required_qty;
        document.getElementById('replacementSearchInput').value = '';
        document.getElementById('selectedReplItemId').value = '';
        document.getElementById('selectedReplVarOptId').value = '';
        document.getElementById('selectedReplacementBox').style.display = 'none';
        document.getElementById('replacementSearchResults').style.display = 'none';
        document.getElementById('replacementQtyInput').value = Math.max(1, item.required_qty);

        const modal = document.getElementById('replaceProductModal');
        if (modal) modal.style.display = 'flex';
    }

    function closeReplaceProductModal() {
        const modal = document.getElementById('replaceProductModal');
        if (modal) modal.style.display = 'none';
    }

    let searchTimeout = null;
    function handleReplacementSearchInput(q) {
        clearTimeout(searchTimeout);
        const resultsBox = document.getElementById('replacementSearchResults');
        if (!q.trim()) {
            resultsBox.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(() => {
            fetchSecure('<?= APP_URL ?>/Picking/api_search_products?q=' + encodeURIComponent(q.trim()))
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.products && data.products.length > 0) {
                        let html = '';
                        data.products.forEach(p => {
                            const name = p.name + (p.variation_name ? ' (' + p.variation_name + ')' : '');
                            const varOptId = p.variation_option_id || '';
                            html += `
                                <div class="repl-search-row" data-id="${p.id}" data-name="${escapeHtml(name)}" data-var-opt-id="${varOptId}" onclick="onSelectReplacementRow(this)" style="padding:8px 12px; border-bottom:1px solid #f1f5f9; cursor:pointer; font-size:12px;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
                                    <strong>${escapeHtml(name)}</strong>
                                    <div style="font-size:11px; color:#64748b;">Code: ${escapeHtml(p.item_code || '-')} | Price: Rs ${p.selling_price.toFixed(2)}</div>
                                </div>
                            `;
                        });
                        resultsBox.innerHTML = html;
                        resultsBox.style.display = 'block';
                    } else {
                        resultsBox.innerHTML = '<div style="padding:10px; font-size:12px; color:#888; text-align:center;">No matching products found</div>';
                        resultsBox.style.display = 'block';
                    }
                })
                .catch(err => {
                    console.error("Product search error:", err);
                    resultsBox.style.display = 'none';
                });
        }, 250);
    }

    function onSelectReplacementRow(el) {
        const id = el.getAttribute('data-id');
        const name = el.getAttribute('data-name');
        const varOptId = el.getAttribute('data-var-opt-id');
        selectReplacementProduct(id, name, varOptId);
    }

    function selectReplacementProduct(id, name, varOptId) {
        document.getElementById('selectedReplItemId').value = id;
        document.getElementById('selectedReplVarOptId').value = varOptId || '';
        document.getElementById('selectedReplName').innerText = name;
        document.getElementById('selectedReplacementBox').style.display = 'block';
        document.getElementById('replacementSearchResults').style.display = 'none';
    }

    function submitProductSubstitution() {
        const deliveryId = parseInt(document.getElementById('replaceDeliveryId').value);
        const origItemId = parseInt(document.getElementById('replaceOrigItemId').value);
        const origVarOptId = document.getElementById('replaceOrigVarOptId').value;
        const replItemId = parseInt(document.getElementById('selectedReplItemId').value);
        const replVarOptId = document.getElementById('selectedReplVarOptId').value;
        const replQty = parseFloat(document.getElementById('replacementQtyInput').value || 0);

        if (!replItemId) {
            alert("Please search and select a replacement product.");
            return;
        }

        if (replQty <= 0) {
            alert("Replacement quantity must be greater than zero.");
            return;
        }

        const payload = {
            delivery_id: deliveryId,
            original_item_id: origItemId,
            original_variation_option_id: origVarOptId !== '' ? origVarOptId : null,
            replacement_item_id: replItemId,
            replacement_variation_option_id: replVarOptId !== '' ? replVarOptId : null,
            replacement_qty: replQty,
            user_id: <?= $_SESSION['user_id'] ?? 1 ?>
        };

        fetchSecure('<?= APP_URL ?>/Picking/api_substitute_product', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message || "Product substitution recorded!");
                closeReplaceProductModal();
                loadLoadingStage(currentRouteId);
            } else {
                alert(data.error || "Failed to record product substitution.");
            }
        })
        .catch(err => {
            alert("Error submitting substitution: " + err.message);
        });
    }

    let currentVarianceState = {};
    let currentSubstitutions = [];

    function loadVarianceAdjustmentStage(routeId) {
        const box = document.getElementById('varianceAuditBox');
        if (!box) return;
        box.innerHTML = '<div style="padding:20px; text-align:center;">Loading shortages & overages... </div>';
        currentVarianceState = {};
        currentSubstitutions = [];

        fetchSecure('<?= APP_URL ?>/RepTracking/api_get_route_variances/' + routeId)
            .then(res => res.json())
            .then(data => {
                if (data.status !== 'success' || !data.deliveries || data.deliveries.length === 0) {
                    box.innerHTML = '<p style="color:red; padding:10px;">No variance records found.</p>';
                    return;
                }
                const del = data.deliveries[0];
                const items = del.items || [];
                currentSubstitutions = data.substitutions || [];
                
                if (items.length === 0) {
                    box.innerHTML = '<p style="color:green; padding:10px; font-weight:bold;">No products picked on this route.</p>';
                    return;
                }

                let fetchPromises = [];
                items.forEach(item => {
                    const itemId = item.item_id;
                    const varOptId = item.variation_option_id || 0;
                    const stateKey = itemId + '_' + varOptId;
                    currentVarianceState[stateKey] = {
                        item_id: itemId,
                        variation_option_id: item.variation_option_id || null,
                        item_name: item.item_name,
                        required_qty: parseFloat(item.required_qty),
                        pre_loaded_qty: parseFloat(item.pre_loaded_qty),
                        final_loaded_qty: item.final_loaded_qty !== null ? parseFloat(item.final_loaded_qty) : parseFloat(item.required_qty),
                        variance: parseFloat(item.variance),
                        invoices: []
                    };

                    if (parseFloat(item.variance) !== 0) {
                        let url = '<?= APP_URL ?>/RepTracking/api_get_product_invoices?route_id=' + routeId + '&item_id=' + itemId;
                        if (item.variation_option_id) {
                            url += '&variation_option_id=' + item.variation_option_id;
                        }
                        const p = fetchSecure(url)
                            .then(res => res.json())
                            .then(invData => {
                                if (invData.status === 'success') {
                                    currentVarianceState[stateKey].invoices = invData.invoices.map(inv => ({
                                        invoice_id: parseInt(inv.invoice_id),
                                        invoice_number: inv.invoice_number,
                                        customer_name: inv.customer_name,
                                        original_qty: parseFloat(inv.original_qty !== undefined ? inv.original_qty : inv.quantity),
                                        quantity: parseFloat(inv.quantity),
                                        unit_price: parseFloat(inv.unit_price),
                                        remove_completely: 0
                                    }));
                                }
                            });
                        fetchPromises.push(p);
                    }
                });

                Promise.all(fetchPromises).then(() => {
                    renderVarianceReconciliation();
                });
            });
    }

    function filterVarianceBySearch(query) {
        const q = (query || '').toLowerCase().trim();
        document.querySelectorAll('.variance-engine-card').forEach(card => {
            const name = (card.getAttribute('data-item-name') || '').toLowerCase();
            card.style.display = (!q || name.includes(q)) ? '' : 'none';
        });
        document.querySelectorAll('.variance-table-row').forEach(row => {
            const name = (row.getAttribute('data-item-name') || '').toLowerCase();
            row.style.display = (!q || name.includes(q)) ? '' : 'none';
        });
    }

    function renderVarianceReconciliation() {
        const box = document.getElementById('varianceAuditBox');
        if (!box) return;
        let html = '';

        let totalShortages = 0;
        let totalOverages = 0;
        let hasUnbalanced = false;

        let tableRows = '';
        const isReadOnly = (currentRouteStatus === 'Completed' || currentRouteStatus === 'Finalized');

        Object.values(currentVarianceState).forEach(item => {
            const variance = item.variance;
            if (variance < 0) totalShortages += Math.abs(variance);
            if (variance > 0) totalOverages += variance;

            let varColor = '#059669';
            let varText = 'Match (0)';
            if (variance < 0) {
                varColor = '#dc2626';
                varText = `${variance} (Shortage)`;
            } else if (variance > 0) {
                varColor = '#d97706';
                varText = `+${variance} (Overage)`;
            }

            let allocatedSum = 0;
            if (variance === 0) {
                allocatedSum = item.final_loaded_qty;
            } else {
                item.invoices.forEach(inv => {
                    allocatedSum += inv.quantity;
                });
            }

            const isItemBalanced = (item.invoices.length === 0 || Math.abs(allocatedSum - item.final_loaded_qty) < 0.01);
            if (!isItemBalanced) {
                hasUnbalanced = true;
            }

            const statusBadge = isItemBalanced 
                ? `<span style="background:#dcfce7; color:#15803d; padding:3px 10px; border-radius:12px; font-size:11px; font-weight:700; display:inline-flex; align-items:center; gap:4px;"><i class="ph ph-check-circle" style="font-size:13px;"></i> Balanced</span>`
                : `<span style="background:#fee2e2; color:#b91c1c; padding:3px 10px; border-radius:12px; font-size:11px; font-weight:700; display:inline-flex; align-items:center; gap:4px;"><i class="ph ph-warning" style="font-size:13px;"></i> Unbalanced</span>`;

            tableRows += `
                <tr class="variance-table-row" data-item-name="${escapeHtml(item.item_name.toLowerCase())}">
                    <td style="padding:12px 18px; font-weight:700; color:#0f172a;">${escapeHtml(item.item_name)}</td>
                    <td style="padding:12px 18px; text-align:center; font-weight:700; font-family:monospace;">${item.required_qty}</td>
                    <td style="padding:12px 18px; text-align:center; font-family:monospace;">${item.pre_loaded_qty}</td>
                    <td style="padding:12px 18px; text-align:center; font-weight:700; font-family:monospace; background:#f8fafc;">${item.final_loaded_qty}</td>
                    <td style="padding:12px 18px; text-align:center; font-weight:700; color:${varColor}; font-family:monospace;">${varText}</td>
                    <td style="padding:12px 18px; text-align:center;">${statusBadge}</td>
                </tr>
            `;
        });

        // Substitutions Panel
        let substitutionHtml = '';
        if (currentSubstitutions.length > 0) {
            let subRows = '';
            currentSubstitutions.forEach(sub => {
                let actionPart = '';
                if (sub.status === 'Pending Bill Update') {
                    actionPart = `
                        <div style="display:flex; flex-direction:column; gap:8px; align-items:flex-end;">
                            <div>
                                <label style="font-size:11px; font-weight:bold; color:#475569; margin-right:5px;">Pricing Decision:</label>
                                <select id="pricing_choice_${sub.id}" style="padding:4px 8px; font-size:11px; border-radius:6px; border:1px solid #cbd5e1; background:#fff;">
                                    <option value="original">Use Original Product Price</option>
                                    <option value="replacement" selected>Use Replacement Product Price</option>
                                </select>
                            </div>
                            <button onclick="applyProductSubstitution(${sub.id})" style="padding:6px 12px; background:#673ab7; color:#fff; border:none; border-radius:6px; font-size:11px; font-weight:bold; cursor:pointer;">
                                Apply Substitution To Bills
                            </button>
                        </div>
                    `;
                } else {
                    actionPart = `
                        <div style="text-align:right; font-size:11px; color:#64748b; line-height:1.4;">
                            Applied by: <strong>${sub.creator_name || 'System'}</strong><br>
                            Date: <strong>${sub.applied_at}</strong><br>
                            Original Bill Value: <strong>Rs. ${parseFloat(sub.original_bill_value).toFixed(2)}</strong><br>
                            Updated Bill Value: <strong>Rs. ${parseFloat(sub.updated_bill_value).toFixed(2)}</strong>
                        </div>
                    `;
                }

                subRows += `
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:12px; border-bottom:1px solid #e2e8f0; gap:15px;">
                        <div style="line-height:1.5; font-size:12px;">
                            Original Product: <strong style="color:#ef6c00;">${escapeHtml(sub.original_item_name)}</strong> (Required Qty: <strong>${parseFloat(sub.required_qty || 0)}</strong>)<br>
                            Replacement Product: <strong style="color:#2e7d32;">${escapeHtml(sub.replacement_item_name)}</strong> (Loaded Qty: <strong>${parseFloat(sub.loaded_qty || 0)}</strong>)<br>
                            Status: <span style="font-weight:bold; color:${sub.status === 'Applied' ? '#16a34a' : '#e65100'};">${sub.status}</span>
                        </div>
                        ${sub.status === 'Pending Bill Update' ? `
                            <div style="display:flex; flex-direction:column; gap:8px; align-items:flex-end;">
                                <div>
                                    <label style="font-size:11px; font-weight:bold; color:#475569; margin-right:5px;">Pricing Decision:</label>
                                    <select id="pricing_choice_${sub.id}" style="padding:4px 8px; font-size:11px; border-radius:6px; border:1px solid #cbd5e1; background:#fff;">
                                        <option value="original">Use Original Product Price</option>
                                        <option value="replacement" selected>Use Replacement Product Price</option>
                                    </select>
                                </div>
                                <button onclick="applyProductSubstitution(${sub.id})" style="padding:6px 12px; background:#673ab7; color:#fff; border:none; border-radius:6px; font-size:11px; font-weight:bold; cursor:pointer;">
                                    Apply Substitution To Bills
                                </button>
                            </div>
                        ` : `
                            <div style="text-align:right; font-size:11px; color:#64748b; line-height:1.4;">
                                Applied by: <strong>${sub.creator_name || 'System'}</strong><br>
                                Date: <strong>${sub.applied_at || 'N/A'}</strong><br>
                                Original Bill Value: <strong>Rs. ${(parseFloat(sub.original_bill_value) || 0).toFixed(2)}</strong><br>
                                Updated Bill Value: <strong>Rs. ${(parseFloat(sub.updated_bill_value) || 0).toFixed(2)}</strong>
                            </div>
                        `}
                    </div>
                `;
            });

            substitutionHtml = `
                <div style="background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:16px; margin-bottom:20px; box-shadow:0 1px 4px rgba(15,23,42,0.04);">
                    <div style="font-size:12px; font-weight:800; color:#475569; text-transform:uppercase; letter-spacing:0.04em; margin-bottom:10px; display:flex; align-items:center; gap:6px;">
                        <i class="ph ph-swap" style="color:#007aff;"></i> Product Substitutions
                    </div>
                    ${subRows}
                </div>
            `;
        }

        let reconciliationPanels = '';
        let hasAnyVariance = false;

        Object.values(currentVarianceState).forEach(item => {
            if (item.variance === 0) return;
            hasAnyVariance = true;

            const stateKey = item.item_id + '_' + (item.variation_option_id || 0);

            let subBadge = '';
            const subInfo = currentSubstitutions.find(s => parseInt(s.replacement_item_id) === parseInt(item.item_id) || parseInt(s.original_item_id) === parseInt(item.item_id));
            if (subInfo) {
                if (parseInt(subInfo.replacement_item_id) === parseInt(item.item_id)) {
                    subBadge = `<span style="display:inline-flex; align-items:center; gap:4px; margin-top:2px; padding:2px 8px; background:#eff6ff; color:#1d4ed8; border-radius:6px; font-size:10px; font-weight:700;"><i class="ph ph-swap"></i> Replaced "${escapeHtml(subInfo.original_item_name)}"</span>`;
                } else {
                    subBadge = `<span style="display:inline-flex; align-items:center; gap:4px; margin-top:2px; padding:2px 8px; background:#fef2f2; color:#b91c1c; border-radius:6px; font-size:10px; font-weight:700;"><i class="ph ph-swap"></i> Replaced by "${escapeHtml(subInfo.replacement_item_name)}"</span>`;
                }
            }

            let invoiceRows = '';
            let currentTotal = 0;

            item.invoices.forEach(inv => {
                currentTotal += (parseFloat(inv.quantity) || 0);
            });

            const unbalancedVal = item.final_loaded_qty - currentTotal;
            const isBalanced = (item.invoices.length === 0 || Math.abs(unbalancedVal) < 0.01);

            const cardBg = isBalanced ? '#f0fdf4' : '#ffffff';
            const cardBorder = isBalanced ? '1px solid #86efac' : '1px solid #e2e8f0';
            const rowBg = isBalanced ? '#ffffff' : '#f8fafc';
            const rowBorder = isBalanced ? '1px solid #dcfce7' : '1px solid #f1f5f9';
            const footerBg = isBalanced ? '#dcfce7' : '#f8fafc';
            const footerBorder = isBalanced ? '1px solid #86efac' : '1px solid #e2e8f0';
            const cubeIconColor = isBalanced ? '#16a34a' : '#007aff';

            item.invoices.forEach((inv, index) => {
                 let actionSelect = '';
                 if (inv.quantity === 0) {
                     actionSelect = `
                         <div style="margin-top:2px; color:#dc2626; font-size:10px; font-weight:700; display:flex; align-items:center; gap:3px;">
                             <i class="ph ph-warning"></i> Will be removed from invoice
                         </div>
                     `;
                 }

                invoiceRows += `
                    <div style="display:grid; grid-template-columns: 1fr auto auto auto; gap:12px; align-items:center; padding:8px 12px; background:${rowBg}; border-radius:8px; margin-bottom:6px; border:${rowBorder};">
                        <div style="font-size:12px; font-weight:600; color:#0f172a; min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="${escapeHtml(inv.customer_name)}">
                            <span>${escapeHtml(inv.customer_name)}</span>
                            ${actionSelect}
                        </div>
                        <div style="text-align:center; font-family:monospace; font-size:11px; color:#64748b; white-space:nowrap;">
                            Orig: <strong style="color:#0f172a;">${inv.original_qty}</strong>
                        </div>
                        <div style="text-align:center;">
                            <input type="number" step="1" min="0" value="${inv.quantity}" 
                                   oninput="updateInvoiceAllocation('${stateKey}', ${inv.invoice_id}, this.value)" 
                                   ${isReadOnly ? 'disabled' : ''}
                                   style="width:65px; padding:4px 6px; border:1px solid #cbd5e1; border-radius:6px; text-align:center; font-weight:bold; font-family:monospace; font-size:12px; background:#ffffff;" />
                        </div>
                        <div style="text-align:right; min-width:85px;">
                            <span style="font-size:12px; font-weight:700; color:#0f172a; font-family:monospace;">Rs. ${(inv.quantity * inv.unit_price).toLocaleString('en-IN', {minimumFractionDigits:2, maximumFractionDigits:2})}</span>
                        </div>
                    </div>
                `;
            });

            if (item.invoices.length === 0) {
                invoiceRows = `<p style="color:#64748b; font-size:12px; margin:8px 0; font-style:italic;">No invoices contain this product on this route.</p>`;
            }

            const panelStatus = isBalanced
                ? `<span style="background:#dcfce7; color:#15803d; padding:3px 10px; border-radius:100px; font-size:11px; font-weight:700; display:inline-flex; align-items:center; gap:4px; border:1px solid #86efac;"><i class="ph ph-check-circle" style="font-size:13px;"></i> Balanced</span>`
                : `<span style="background:#fee2e2; color:#b91c1c; padding:3px 10px; border-radius:100px; font-size:11px; font-weight:700; display:inline-flex; align-items:center; gap:4px; border:1px solid #fca5a5;"><i class="ph ph-warning" style="font-size:13px;"></i> Unbalanced (${unbalancedVal > 0 ? '+' : ''}${unbalancedVal.toFixed(1)} pcs)</span>`;

            let autoDistBtn = isReadOnly ? '' : `
                <button onclick="autoDistributeVariance('${stateKey}')" style="padding:5px 10px; background:#eff6ff; border:1px solid #bfdbfe; color:#1d4ed8; border-radius:100px; font-size:11px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:4px; transition:all 0.18s;" onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                    <i class="ph ph-lightning" style="font-size:12px;"></i> Auto-Distribute
                </button>
            `;

            let varColor = item.variance > 0 ? '#d97706' : '#dc2626';

            reconciliationPanels += `
                <div class="variance-engine-card" data-item-name="${escapeHtml(item.item_name.toLowerCase())}" style="background:${cardBg}; border:${cardBorder}; border-radius:14px; padding:14px 16px; box-shadow:0 2px 8px rgba(15,23,42,0.04); display:flex; flex-direction:column; justify-content:space-between; transition:all 0.18s ease;">
                    <div>
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; border-bottom:1px solid ${isBalanced ? '#dcfce7' : '#f1f5f9'}; padding-bottom:10px; margin-bottom:10px; gap:10px;">
                            <div>
                                <div style="font-weight:700; font-size:14px; color:#0f172a; display:flex; align-items:center; gap:6px;">
                                    <i class="ph ph-cube" style="color:${cubeIconColor}; font-size:16px;"></i> ${escapeHtml(item.item_name)}
                                </div>
                                ${subBadge ? `<div style="margin-top:2px;">${subBadge}</div>` : ''}
                                <div style="font-size:11px; color:#64748b; margin-top:3px;">
                                    Variance: <strong style="color:${varColor}">${item.variance > 0 ? '+' : ''}${item.variance}</strong> | Req: <strong>${item.required_qty}</strong> | Loaded: <strong>${item.final_loaded_qty}</strong>
                                </div>
                            </div>
                            <div style="display:flex; align-items:center; gap:6px; flex-shrink:0;">
                                ${autoDistBtn}
                                ${panelStatus}
                            </div>
                        </div>
                        <div>${invoiceRows}</div>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:10px; font-size:11px; color:#475569; background:${footerBg}; padding:8px 12px; border-radius:100px; border:${footerBorder};">
                        <span>Allocated: <strong style="font-family:monospace; font-size:12px; color:#0f172a;">${currentTotal.toFixed(1)}</strong></span>
                        <span>Remaining: <strong style="font-family:monospace; font-size:12px; color:${unbalancedVal !== 0 ? '#dc2626' : '#16a34a'};">${unbalancedVal.toFixed(1)}</strong></span>
                    </div>
                </div>
            `;
        });

        if (!hasAnyVariance) {
            reconciliationPanels = `
                <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:14px; padding:20px; text-align:center; color:#166534; font-weight:700; margin-bottom:20px; display:flex; align-items:center; justify-center; gap:8px; box-shadow:0 1px 3px rgba(0,0,0,0.03); grid-column: 1 / -1;">
                    <i class="ph ph-check-circle" style="font-size:18px;"></i> All items are fully balanced! No billing adjustments required.
                </div>
            `;
        }

        // --- CONSTRUCT THE HTML ---
        
        // 1. Top Shortages / Overages Total Metric Cards
        html += `
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px;">
                <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:14px; padding:14px 16px; display:flex; align-items:center; gap:12px; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
                    <div style="width:38px; height:38px; border-radius:10px; background:#fee2e2; color:#ef4444; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0;">
                        <i class="ph ph-trend-down"></i>
                    </div>
                    <div>
                        <div style="font-size:11px; font-weight:700; color:#991b1b; text-transform:uppercase; letter-spacing:0.04em;">Shortages to Reconcile</div>
                        <div style="font-size:18px; font-weight:800; color:#7f1d1d; font-family:monospace; margin-top:2px;">${totalShortages} pcs</div>
                    </div>
                </div>
                <div style="background:#fff7ed; border:1px solid #ffedd5; border-radius:14px; padding:14px 16px; display:flex; align-items:center; gap:12px; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
                    <div style="width:38px; height:38px; border-radius:10px; background:#ffedd5; color:#f97316; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0;">
                        <i class="ph ph-trend-up"></i>
                    </div>
                    <div>
                        <div style="font-size:11px; font-weight:700; color:#c2410c; text-transform:uppercase; letter-spacing:0.04em;">Overages to Reconcile</div>
                        <div style="font-size:18px; font-weight:800; color:#9a3412; font-family:monospace; margin-top:2px;">${totalOverages} pcs</div>
                    </div>
                </div>
            </div>
        `;

        // 2. Bill Reconciliation Engine Cards Section (Grid layout)
        html += `
            <div id="varianceEngineContainer" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(480px, 1fr)); gap:16px; margin-bottom:20px;">
                ${reconciliationPanels}
            </div>
        `;

        // 3. Product Substitutions Section (Below engine cards, above item list)
        if (substitutionHtml) {
            html += substitutionHtml;
        }

        // 3. Product Loading Variances Table Section
        html += `
            <div style="overflow-x:auto; margin-bottom:20px;">
                <table class="data-table" style="margin:0; width:100%;">
                    <thead>
                        <tr style="background:#f8fafc; border-bottom:2px solid #e2e8f0;">
                            <th style="padding:14px 18px; text-align:left;">Product Name</th>
                            <th style="padding:14px 18px; text-align:center;">Required</th>
                            <th style="padding:14px 18px; text-align:center;">Pre-Loaded</th>
                            <th style="padding:14px 18px; text-align:center; background:#f1f5f9;">Final Loaded</th>
                            <th style="padding:14px 18px; text-align:center;">Variance</th>
                            <th style="padding:14px 18px; text-align:center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${tableRows}
                    </tbody>
                </table>
            </div>
        `;

        // 4. Dark Pill Floating Dock with Search & Labeled Action Buttons
        html += `
            <div id="tab5FloatingDock" style="position:fixed; bottom:24px; left:50%; transform:translateX(-50%); z-index:1000; display:flex; align-items:center; gap:10px; padding:8px 14px; background:rgba(30, 41, 59, 0.96); backdrop-filter:blur(24px) saturate(200%); -webkit-backdrop-filter:blur(24px) saturate(200%); border:1px solid rgba(255, 255, 255, 0.15); border-radius:100px; box-shadow:0 16px 40px rgba(15, 23, 42, 0.35);">
                
                <!-- Search Input Pill inside Dark Bar -->
                <div style="display:flex; align-items:center; background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.18); border-radius:100px; padding:6px 14px; gap:8px; width:220px; transition:all 0.18s;">
                    <i class="ph ph-magnifying-glass" style="color:#94a3b8; font-size:16px;"></i>
                    <input type="text" id="searchVarianceProductsInput" oninput="filterVarianceBySearch(this.value)" placeholder="Search items & engine..." style="background:transparent; border:none; outline:none; color:#ffffff; font-size:13px; width:100%; font-family:inherit;" onfocus="this.parentElement.style.borderColor='#007aff'; this.parentElement.style.background='rgba(255,255,255,0.18)'" onblur="this.parentElement.style.borderColor='rgba(255,255,255,0.18)'; this.parentElement.style.background='rgba(255,255,255,0.12)'">
                </div>

                <div style="width:1px; height:22px; background:rgba(255,255,255,0.2); margin:0 2px;"></div>

                <!-- Labeled Buttons with Icons -->
                ${!isReadOnly ? `
                    ${hasUnbalanced ? `
                        <button id="btnForceSubmitVarianceAdjustments" type="button" onclick="submitVarianceAdjustments(true)" style="padding:9px 16px; background:#dc2626; border:none; color:#ffffff; border-radius:100px; font-weight:700; font-size:13px; cursor:pointer; display:inline-flex; align-items:center; gap:8px; white-space:nowrap; box-shadow:0 3px 12px rgba(220,38,38,0.3); transition:all 0.18s;" onmouseover="this.style.transform='scale(1.04)'; this.style.background='#b91c1c'" onmouseout="this.style.transform='scale(1)'; this.style.background='#dc2626'">
                            <i class="ph ph-warning-octagon" style="font-size:16px;"></i> Force Approve (Bypass)
                        </button>
                    ` : ''}
                    <button id="btnSubmitVarianceAdjustments" type="button" onclick="submitVarianceAdjustments(false)" ${hasUnbalanced ? 'disabled' : ''} style="padding:9px 18px; background:#16a34a; border:none; color:#ffffff; border-radius:100px; font-weight:700; font-size:13px; cursor:${hasUnbalanced ? 'not-allowed' : 'pointer'}; opacity:${hasUnbalanced ? 0.5 : 1}; display:inline-flex; align-items:center; gap:8px; white-space:nowrap; box-shadow:0 3px 12px rgba(22,163,74,0.3); transition:all 0.18s;" onmouseover="${hasUnbalanced ? '' : 'this.style.transform=\'scale(1.04)\'; this.style.background=\'#15803d\''}" onmouseout="${hasUnbalanced ? '' : 'this.style.transform=\'scale(1)\'; this.style.background=\'#16a34a\''}">
                        <i class="ph ph-scales" style="font-size:16px;"></i> Approve & Apply Adjustments
                    </button>
                ` : ''}
            </div>
        `;

        box.innerHTML = html;
    }

    function updateRemoveCompletelyChoice(stateKey, invoiceId, value) {
        const item = currentVarianceState[stateKey];
        if (item) {
            const inv = item.invoices.find(i => i.invoice_id === invoiceId);
            if (inv) {
                inv.remove_completely = parseInt(value);
            }
        }
        renderVarianceReconciliation();
    }

    function applyProductSubstitution(subId) {
        const choiceSelect = document.getElementById('pricing_choice_' + subId);
        const choice = choiceSelect ? choiceSelect.value : 'replacement';

        if (!confirm('Are you sure you want to apply this product substitution to the bills? This will modify the invoice items list, adjust stock, and update the pricing.')) {
            return;
        }

        console.log("Applying product substitution:", { substitution_id: subId, pricing_choice: choice });

        fetchSecure('<?= APP_URL ?>/RepTracking/api_apply_substitution', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                substitution_id: subId,
                pricing_choice: choice
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                onRouteDataChanged();
                switchRouteTab(5);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('An unexpected error occurred.');
        });
    }

    function updateInvoiceAllocation(stateKey, invoiceId, value) {
        let qty = parseFloat(value);
        if (isNaN(qty) || qty < 0) {
            qty = 0;
        }
        
        const item = currentVarianceState[stateKey];
        if (item) {
            const inv = item.invoices.find(i => i.invoice_id === invoiceId);
            if (inv) {
                inv.quantity = qty;
                inv.remove_completely = (qty === 0 ? 1 : 0);
            }
        }
        renderVarianceReconciliation();
    }

    function autoDistributeVariance(stateKey) {
        const item = currentVarianceState[stateKey];
        if (!item || item.invoices.length === 0) return;

        let targetTotal = item.final_loaded_qty;
        let originalTotal = item.required_qty;
        let diff = targetTotal - originalTotal;

        if (diff === 0) {
            item.invoices.forEach(inv => {
                inv.quantity = inv.original_qty;
                inv.remove_completely = (inv.quantity === 0 ? 1 : 0);
            });
        } else if (diff < 0) {
            let shortageToDeduct = Math.abs(diff);
            item.invoices.forEach(inv => {
                if (shortageToDeduct <= 0) {
                    inv.remove_completely = (inv.quantity === 0 ? 1 : 0);
                    return;
                }
                if (inv.quantity >= shortageToDeduct) {
                    inv.quantity -= shortageToDeduct;
                    shortageToDeduct = 0;
                } else {
                    shortageToDeduct -= inv.quantity;
                    inv.quantity = 0;
                }
                inv.remove_completely = (inv.quantity === 0 ? 1 : 0);
            });
        } else {
            item.invoices[0].quantity += diff;
            item.invoices[0].remove_completely = (item.invoices[0].quantity === 0 ? 1 : 0);
            // Ensure other invoices also have correct remove_completely
            for (let i = 1; i < item.invoices.length; i++) {
                item.invoices[i].remove_completely = (item.invoices[i].quantity === 0 ? 1 : 0);
            }
        }

        renderVarianceReconciliation();
    }

    function submitVarianceAdjustments(force = false) {
        if (!currentRouteId) return;

        const adjustments = [];
        Object.values(currentVarianceState).forEach(item => {
            if (item.variance === 0) return;
            
            const invoiceAdjustments = item.invoices.map(inv => ({
                invoice_id: inv.invoice_id,
                new_qty: inv.quantity,
                remove_completely: inv.remove_completely ? 1 : 0
            }));

            adjustments.push({
                item_id: item.item_id,
                variation_option_id: item.variation_option_id || null,
                invoice_adjustments: invoiceAdjustments
            });
        });

        const confirmMsg = force 
            ? 'Are you sure you want to FORCE approve these variance adjustments, bypassing the allocation balance validation?' 
            : 'Are you sure you want to approve these variance adjustments and update invoice billing? This action will modify invoice line quantities.';

        if (adjustments.length > 0 && !confirm(confirmMsg)) {
            return;
        }

        const payload = {
            route_id: currentRouteId,
            adjustments: adjustments,
            force: force ? 1 : 0
        };

        console.log("Submitting variance adjustments payload:", payload);

        fetchSecure('<?= APP_URL ?>/RepTracking/api_adjust_variance_billing', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                onRouteDataChanged();
                switchRouteTab(5);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('An unexpected error occurred during submission.');
        });
    }

    function printLoadingSheet(type) {
        if (!currentRouteId) return;
        window.open('<?= APP_URL ?>/RepTracking/print_loading/' + currentRouteId + '?type=' + type, '_blank');
    }

    function makeSelectSearchable(selectId) {
        const sel = document.getElementById(selectId);
        if (!sel || sel.dataset.searchableInit === 'true') return;
        sel.dataset.searchableInit = 'true';

        sel.style.setProperty('display', 'none', 'important');

        const wrapper = document.createElement('div');
        wrapper.className = 'custom-searchable-select';
        wrapper.style.cssText = 'position:relative; width:100%; font-family:inherit;';

        const trigger = document.createElement('div');
        trigger.className = 'custom-select-trigger';
        trigger.style.cssText = 'height:42px; padding:0 14px; border:1px solid #cbd5e1; border-radius:10px; background:#ffffff; color:#0f172a; font-size:13px; font-weight:600; cursor:pointer; display:flex; justify-content:space-between; align-items:center; transition:all 0.18s; user-select:none; box-sizing:border-box;';
        
        const textSpan = document.createElement('span');
        textSpan.style.cssText = 'overflow:hidden; text-overflow:ellipsis; white-space:nowrap;';
        textSpan.textContent = sel.options[sel.selectedIndex] ? sel.options[sel.selectedIndex].text : '-- Select --';
        
        const arrow = document.createElement('i');
        arrow.className = 'ph ph-caret-down';
        arrow.style.cssText = 'color:#64748b; font-size:14px; transition:transform 0.18s; margin-left:8px; flex-shrink:0;';

        trigger.appendChild(textSpan);
        trigger.appendChild(arrow);

        const dropdown = document.createElement('div');
        dropdown.className = 'custom-select-dropdown';
        dropdown.style.cssText = 'display:none; position:absolute; top:calc(100% + 4px); left:0; right:0; background:#ffffff; border:1px solid #cbd5e1; border-radius:12px; box-shadow:0 10px 30px rgba(15,23,42,0.15); z-index:1000; padding:8px;';

        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.placeholder = 'Type to search...';
        searchInput.style.cssText = 'width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; font-size:12px; outline:none; background:#f8fafc; margin-bottom:8px; box-sizing:border-box; font-family:inherit;';

        const optionsList = document.createElement('div');
        optionsList.style.cssText = 'max-height:190px; overflow-y:auto; display:flex; flex-direction:column; gap:2px;';

        const updateOptions = () => {
            optionsList.innerHTML = '';
            const q = searchInput.value.toLowerCase().trim();
            let count = 0;
            Array.from(sel.options).forEach(opt => {
                const txt = opt.text;
                const val = opt.value;
                if (!q || txt.toLowerCase().includes(q)) {
                    count++;
                    const item = document.createElement('div');
                    const isSelected = String(sel.value) === String(val);
                    item.style.cssText = `padding:8px 12px; border-radius:6px; font-size:13px; cursor:pointer; font-weight:${isSelected ? '700' : '500'}; background:${isSelected ? '#eff6ff' : 'transparent'}; color:${isSelected ? '#1d4ed8' : '#0f172a'}; transition:background 0.12s;`;
                    item.textContent = txt;
                    item.onmouseover = () => { if (!isSelected) item.style.background = '#f1f5f9'; };
                    item.onmouseout = () => { if (!isSelected) item.style.background = 'transparent'; };
                    item.onclick = (e) => {
                        e.stopPropagation();
                        sel.value = val;
                        sel.dispatchEvent(new Event('change', { bubbles: true }));
                        closeDropdown();
                    };
                    optionsList.appendChild(item);
                }
            });
            if (count === 0) {
                const noRes = document.createElement('div');
                noRes.style.cssText = 'padding:10px; text-align:center; color:#94a3b8; font-size:12px;';
                noRes.textContent = 'No matching options';
                optionsList.appendChild(noRes);
            }
        };

        dropdown.appendChild(searchInput);
        dropdown.appendChild(optionsList);
        wrapper.appendChild(trigger);
        wrapper.appendChild(dropdown);

        sel.parentNode.insertBefore(wrapper, sel);
        wrapper.appendChild(sel);

        const openDropdown = () => {
            dropdown.style.display = 'block';
            trigger.style.borderColor = '#007aff';
            trigger.style.boxShadow = '0 0 0 3px rgba(0,122,255,0.12)';
            arrow.style.transform = 'rotate(180deg)';
            searchInput.value = '';
            updateOptions();
            setTimeout(() => searchInput.focus(), 50);
        };

        const closeDropdown = () => {
            dropdown.style.display = 'none';
            trigger.style.borderColor = '#cbd5e1';
            trigger.style.boxShadow = 'none';
            arrow.style.transform = 'rotate(0deg)';
        };

        trigger.onclick = (e) => {
            e.stopPropagation();
            if (dropdown.style.display === 'block') closeDropdown(); else openDropdown();
        };

        searchInput.oninput = () => updateOptions();
        searchInput.onclick = (e) => e.stopPropagation();

        document.addEventListener('click', (e) => {
            if (!wrapper.contains(e.target)) closeDropdown();
        });

        sel.addEventListener('change', () => {
            const selectedOpt = sel.options[sel.selectedIndex];
            textSpan.textContent = selectedOpt ? selectedOpt.text : '-- Select --';
        });
    }

    function setSelectValue(selectId, val) {
        const sel = document.getElementById(selectId);
        if (!sel) return;
        
        const cleanVal = (val && val !== 'null' && val !== 'undefined') ? String(val).trim() : '';
        if (!cleanVal) {
            sel.value = '';
            sel.selectedIndex = 0;
            sel.dispatchEvent(new Event('change', { bubbles: true }));
            return;
        }

        // Reset previous selections
        for (let i = 0; i < sel.options.length; i++) {
            sel.options[i].selected = false;
        }

        // 1. Try exact match
        for (let i = 0; i < sel.options.length; i++) {
            if (sel.options[i].value === cleanVal || sel.options[i].text === cleanVal) {
                sel.options[i].selected = true;
                sel.selectedIndex = i;
                sel.value = sel.options[i].value;
                sel.dispatchEvent(new Event('change', { bubbles: true }));
                return;
            }
        }

        // 2. Try case-insensitive / trimmed match
        const lowerVal = cleanVal.toLowerCase();
        for (let i = 0; i < sel.options.length; i++) {
            const optVal = (sel.options[i].value || '').trim().toLowerCase();
            const optTxt = (sel.options[i].text || '').trim().toLowerCase();
            if (optVal === lowerVal || optTxt === lowerVal) {
                sel.options[i].selected = true;
                sel.selectedIndex = i;
                sel.value = sel.options[i].value;
                sel.dispatchEvent(new Event('change', { bubbles: true }));
                return;
            }
        }

        // 3. Dynamically append option if missing from list so selection is NEVER lost
        const opt = document.createElement('option');
        opt.value = cleanVal;
        opt.textContent = cleanVal;
        opt.selected = true;
        sel.appendChild(opt);
        sel.value = cleanVal;
        sel.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function loadDispatchStage(routeId) {
        console.log('[loadDispatchStage] Called for routeId:', routeId);
        const formView = document.getElementById('adjDeliveryFormView');
        if (!formView) return;

        makeSelectSearchable('adjDaVehicle');
        makeSelectSearchable('adjDaDriver');
        makeSelectSearchable('adjDaPartner');

        const isReadOnly = (currentRouteStatus === 'Completed' || currentRouteStatus === 'Finalized');

        // Form is always visible
        formView.style.display = 'block';

        const rdata = document.getElementById('route_data_' + routeId);
        const delId = rdata ? rdata.getAttribute('data-delivery-id') : null;
        console.log('[loadDispatchStage] route_data delId:', delId);

        const statusBanner = document.getElementById('adjDeliveryStatusBanner');
        const statusId = document.getElementById('adjDeliveryStatusId');

        // Load outstanding bills and pre-check
        loadOutstandingBillsChecklist(routeId, delId);

        fetchSecure('<?= APP_URL ?>/RepTracking/api_get_delivery_details/' + (delId || 0) + '?route_id=' + routeId)
            .then(res => res.json())
            .then(dData => {
                console.log('[loadDispatchStage] api_get_delivery_details response:', dData);
                if (dData.status === 'success' && dData.delivery) {
                    if (statusBanner) {
                        statusBanner.style.display = 'flex';
                        statusId.innerText = '#' + dData.delivery.id;
                    }
                    if (rdata) rdata.setAttribute('data-delivery-id', dData.delivery.id);
                    if (document.getElementById('adjDaDate')) document.getElementById('adjDaDate').value = dData.delivery.delivery_date || '';
                    setSelectValue('adjDaVehicle', dData.delivery.vehicle_number);
                    setSelectValue('adjDaDriver', dData.delivery.driver_name);
                    setSelectValue('adjDaPartner', dData.delivery.partner_name);
                } else {
                    if (statusBanner) statusBanner.style.display = 'none';
                    const dateInput = document.getElementById('adjDaDate');
                    if (dateInput && !dateInput.value) {
                        dateInput.value = new Date().toISOString().split('T')[0];
                    }
                    setSelectValue('adjDaVehicle', '');
                    setSelectValue('adjDaDriver', '');
                    setSelectValue('adjDaPartner', '');
                }
            })
            .catch(err => {
                console.error('[loadDispatchStage] fetch error:', err);
            });
    }

    function loadDeliveryLiveStage(routeId) {
        const summaryCards = document.getElementById('deliveryTabSummaryCards');
        const tbody = document.getElementById('deliveryTabInvoicesTbody');
        if (!summaryCards || !tbody) return;

        summaryCards.innerHTML = '<div style="grid-column: span 4; text-align:center; padding:10px;">Loading performance summary... </div>';
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Loading customer dispatches... </td></tr>';

        const rdata = document.getElementById('route_data_' + routeId);
        const delId = rdata ? rdata.getAttribute('data-delivery-id') : null;

        if (!delId || delId === '0' || delId === '') {
            summaryCards.innerHTML = '<div style="grid-column: span 4; text-align:center; padding:10px; color:#888;">Delivery has not been arranged/dispatched yet.</div>';
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; color:#888;">No dispatch data available.</td></tr>';
            return;
        }

        fetchSecure('<?= APP_URL ?>/RepTracking/api_get_delivery_details/' + delId)
            .then(res => res.json())
            .then(data => {
                if (data.status !== 'success' || !data.delivery) {
                    summaryCards.innerHTML = '<div style="grid-column: span 4; text-align:center; color:red;">Error loading delivery.</div>';
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; color:red;">Failed to load details.</td></tr>';
                    return;
                }

                const invoices = data.invoices || [];
                const totalInvoices = invoices.length;
                const delivered = invoices.filter(inv => inv.delivery_status === 'Delivered').length;
                const pending = totalInvoices - delivered;
                const collections = data.balancing ? (parseFloat(data.balancing.total_payments) || (parseFloat(data.balancing.raw_cash_collections || 0) + parseFloat(data.balancing.cheque_collections || 0) + parseFloat(data.balancing.bank_collections || 0))) : 0;

                summaryCards.innerHTML = `
                    <div style="background:#f0f9ff; border:1px solid #bae6fd; padding:12px; border-radius:6px; text-align:center;">
                        <span style="font-size:11px; color:#0369a1; font-weight:bold;">Total Invoices</span><br>
                        <strong style="font-size:15px; color:#0f172a;">${totalInvoices}</strong>
                    </div>
                    <div style="background:#f0fdf4; border:1px solid #bbf7d0; padding:12px; border-radius:6px; text-align:center;">
                        <span style="font-size:11px; color:#166534; font-weight:bold;">Delivered</span><br>
                        <strong style="font-size:15px; color:#166534;">${delivered}</strong>
                    </div>
                    <div style="background:#fff7ed; border:1px solid #ffedd5; padding:12px; border-radius:6px; text-align:center;">
                        <span style="font-size:11px; color:#c2410c; font-weight:bold;">Pending Visit</span><br>
                        <strong style="font-size:15px; color:#c2410c;">${pending}</strong>
                    </div>
                    <div style="background:#f0fdf4; border:1px solid #bbf7d0; padding:12px; border-radius:6px; text-align:center;">
                        <span style="font-size:11px; color:#166534; font-weight:bold;">Collected Amount</span><br>
                        <strong style="font-size:15px; color:#166534;">Rs ${collections.toLocaleString('en-US', {minimumFractionDigits: 2})}</strong>
                    </div>
                `;

                tbody.innerHTML = '';
                if (invoices.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:32px; color:#94a3b8; font-size:13px;">No invoices dispatched.</td></tr>';
                    return;
                }

                let rowIndex = 0;
                invoices.forEach(inv => {
                    rowIndex++;
                    let dColor = '#d05d00';
                    if (inv.delivery_status === 'Delivered') dColor = '#166534';
                    else if (inv.delivery_status === 'Cancelled') dColor = '#dc2626';
                    else if (inv.delivery_status === 'Postponed') dColor = '#475569';

                    let isCancelled = (inv.status === 'Cancelled' || inv.delivery_status === 'Cancelled');
                    let pColor = inv.status === 'Paid' ? '#166534' : (isCancelled ? '#dc2626' : '#c2410c');
                    let pBg = inv.status === 'Paid' ? '#f0fdf4' : (isCancelled ? '#fef2f2' : '#fff7ed');
                    let displayPaymentStatus = isCancelled ? 'Cancelled' : (inv.status || 'Unpaid');
                    
                    let actionHtml = '';
                    if (currentRouteStatus === 'Completed' || currentRouteStatus === 'Finalized') {
                        actionHtml = `<span style="color:#94a3b8; font-size:12px; font-weight:600;">Closed</span>`;
                    } else if (inv.delivery_status !== 'Pending') {
                        actionHtml = `<span style="color:#0284c7; font-size:11px; font-weight:700; background:#e0f2fe; padding:4px 10px; border-radius:100px;">Processed</span>`;
                    } else {
                        actionHtml = `
                            <button type="button" onclick="openServerDeliveryProcessModal(${inv.id}, ${inv.customer_id}, '${inv.invoice_number}', '${inv.customer_name.replace(/'/g, "\\'")}', ${inv.true_grand_total})" 
                                    style="padding:6px 14px; background:#007aff; border:none; color:#ffffff; border-radius:100px; font-weight:700; font-size:12px; cursor:pointer; display:inline-flex; align-items:center; gap:6px; box-shadow:0 3px 10px rgba(0,122,255,0.25); transition:all 0.18s;"
                                    onmouseover="this.style.transform='scale(1.04)'; this.style.background='#0062cc'" onmouseout="this.style.transform='scale(1)'; this.style.background='#007aff'">
                                <i class="ph ph-gear" style="font-size:14px;"></i> Process Visit
                            </button>
                        `;
                    }

                    let statusSelectHtml = '';
                    const isReadOnly = (currentRouteStatus === 'Completed' || currentRouteStatus === 'Finalized');
                    if (isReadOnly) {
                        statusSelectHtml = `<span style="color:${dColor}; font-weight:700;">${inv.delivery_status || 'Pending'}</span>`;
                    } else {
                        statusSelectHtml = `
                            <select onchange="updateSingleInvoiceDeliveryStatus(${inv.id}, ${inv.customer_id}, this.value)" 
                                    style="padding:6px 10px; border:1px solid #cbd5e1; border-radius:8px; font-size:12px; font-weight:700; color:${dColor}; background:#ffffff; outline:none; cursor:pointer; transition:all 0.18s;">
                                <option value="Pending" ${inv.delivery_status === 'Pending' ? 'selected' : ''} style="color:#d05d00; background:#fff;">Pending</option>
                                <option value="Delivered" ${inv.delivery_status === 'Delivered' ? 'selected' : ''} style="color:#166534; background:#fff;">Delivered</option>
                                <option value="Cancelled" ${inv.delivery_status === 'Cancelled' ? 'selected' : ''} style="color:#dc2626; background:#fff;">Cancelled</option>
                                <option value="Postponed" ${inv.delivery_status === 'Postponed' ? 'selected' : ''} style="color:#475569; background:#fff;">Postponed</option>
                            </select>
                        `;
                    }

                    const bg = (rowIndex % 2 === 0) ? '#f8fafc' : '#ffffff';

                    tbody.innerHTML += `
                        <tr style="background:${bg}; border-bottom:1px solid #f1f5f9; transition:background 0.15s;" onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background='${bg}'">
                            <td style="padding:12px 16px; vertical-align:middle;">
                                <strong style="font-size:13px; color:#0f172a;">${inv.customer_name}</strong>
                            </td>
                            <td style="padding:12px 16px; vertical-align:middle; font-weight:700; color:#1e293b; font-family:monospace; font-size:13px;">
                                ${inv.invoice_number}
                            </td>
                            <td style="padding:12px 16px; text-align:right; font-family:monospace; font-weight:700; color:#059669; font-size:13px; vertical-align:middle;">
                                Rs ${parseFloat(inv.true_grand_total).toLocaleString('en-US', {minimumFractionDigits: 2})}
                            </td>
                            <td style="padding:12px 16px; text-align:center; vertical-align:middle;">${statusSelectHtml}</td>
                            <td style="padding:12px 16px; text-align:center; vertical-align:middle;">
                                <span style="font-size:11px; font-weight:700; padding:3px 10px; border-radius:100px; color:${pColor}; background:${pBg}; border:1px solid ${pColor}33;">${displayPaymentStatus}</span>
                            </td>
                            <td style="padding:12px 16px; text-align:center; vertical-align:middle;">${actionHtml}</td>
                        </tr>
                    `;
                });
            });
    }

    function openServerDeliveryProcessModal(invoiceId, customerId, invoiceNumber, customerName, grandTotal) {
        document.getElementById('sdpInvoiceId').value = invoiceId;
        document.getElementById('sdpInvoiceId').setAttribute('data-grand-total', grandTotal);
        document.getElementById('sdpCustomerId').value = customerId;
        document.getElementById('sdpCustomerName').innerText = customerName;
        document.getElementById('sdpInvoiceNumber').innerText = invoiceNumber;
        document.getElementById('sdpCashAmount').value = '0.00';
        document.getElementById('sdpBankAmount').value = '0.00';
        document.getElementById('sdpChequesContainer').innerHTML = '';
        document.getElementById('sdpItemsTbody').innerHTML = '<tr><td colspan="3" style="text-align:center;">Loading items... </td></tr>';
        
        document.getElementById('sdpInvoiceTotal').innerText = 'Rs ' + parseFloat(grandTotal).toLocaleString('en-US', {minimumFractionDigits: 2});
        updateSdpBalance();
        
        if (document.getElementById('sdpItemSearch')) {
            document.getElementById('sdpItemSearch').value = '';
        }
        
        document.getElementById('serverDeliveryProcessModal').style.display = 'flex';
        
        fetchSecure('<?= APP_URL ?>/RepTracking/api_get_invoice_for_delivery/' + invoiceId)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    let statusVal = data.invoice.delivery_status || 'Pending';
                    if (statusVal === 'Pending') statusVal = 'Delivered';
                    document.getElementById('sdpDeliveryStatus').value = statusVal;
                    
                    const arrears = parseFloat(data.arrears || 0);
                    document.getElementById('sdpOutstandingArrears').innerText = 'Rs ' + arrears.toLocaleString('en-US', {minimumFractionDigits: 2});
                    
                    // Calculate tax percentage
                    let taxPercentage = 0;
                    const subTotal = parseFloat(data.invoice.total_amount || 0);
                    const globalDiscVal = parseFloat(data.invoice.global_discount_val || 0);
                    const globalDiscType = data.invoice.global_discount_type || 'Rs';
                    const globalDisc = (globalDiscType === '%') ? (subTotal * globalDiscVal / 100) : globalDiscVal;
                    const netSub = Math.max(0, subTotal - globalDisc);
                    if (netSub > 0) {
                        taxPercentage = parseFloat(data.invoice.tax_amount || 0) / netSub;
                    }
                    
                    const modal = document.getElementById('serverDeliveryProcessModal');
                    modal.setAttribute('data-global-discount-val', globalDiscVal);
                    modal.setAttribute('data-global-discount-type', globalDiscType);
                    modal.setAttribute('data-tax-percentage', taxPercentage);
                    
                    let tbody = document.getElementById('sdpItemsTbody');
                    tbody.innerHTML = '';
                    
                    if (!data.items || data.items.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="3" style="text-align:center; padding:24px; color:#94a3b8; font-size:13px;">No items in this invoice.</td></tr>';
                    } else {
                        let itemIdx = 0;
                        data.items.forEach(item => {
                            itemIdx++;
                            const bg = (itemIdx % 2 === 0) ? '#f8fafc' : '#ffffff';
                            tbody.innerHTML += `
                                <tr data-item-id="${item.id}" style="background:${bg}; border-bottom:1px solid #f1f5f9; transition:background 0.15s;" onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background='${bg}'">
                                    <td style="padding:10px 14px; vertical-align:middle;">
                                        <strong style="font-size:13px; color:#0f172a;">${item.description}</strong>
                                    </td>
                                    <td style="padding:10px 14px; text-align:right; font-family:monospace; font-weight:700; color:#475569; font-size:13px; vertical-align:middle;">
                                        ${parseInt(item.loaded_quantity)}
                                    </td>
                                    <td style="padding:10px 14px; text-align:right; vertical-align:middle;">
                                        <input type="number" step="1" min="0" max="${parseInt(item.loaded_quantity)}" class="sdp-delivered-qty" 
                                               value="${parseInt(item.quantity)}" 
                                               data-price="${parseFloat(item.unit_price || 0)}"
                                               data-discount-type="${item.discount_type || 'Rs'}"
                                               data-discount-val="${parseFloat(item.discount_value || 0)}"
                                               oninput="recalculateSdpInvoiceTotal()"
                                               style="width: 76px; text-align: center; padding: 6px 8px; border: 1px solid #cbd5e1; border-radius: 8px; font-weight: 700; font-family: monospace; font-size: 13px; outline: none; background: #ffffff;"
                                               onfocus="this.style.borderColor='#007aff'; this.style.boxShadow='0 0 0 3px rgba(0,122,255,0.12)'"
                                               onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none'" />
                                    </td>
                                </tr>
                            `;
                        });
                        recalculateSdpInvoiceTotal();
                    }
                } else {
                    alert('Error: ' + data.message);
                    closeServerDeliveryProcessModal();
                }
            })
            .catch(err => {
                console.error(err);
                alert('Failed to load invoice items.');
                closeServerDeliveryProcessModal();
            });
    }

    function filterSdpItems() {
        const query = document.getElementById('sdpItemSearch').value.toLowerCase().trim();
        const rows = document.querySelectorAll('#sdpItemsTbody tr');
        rows.forEach(row => {
            const descTd = row.querySelector('td:first-child');
            if (descTd) {
                const desc = descTd.innerText.toLowerCase();
                if (desc.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    }

    function updateSdpBalance() {
        const grandTotal = parseFloat(document.getElementById('sdpInvoiceId').getAttribute('data-grand-total') || 0);
        const cash = parseFloat(document.getElementById('sdpCashAmount').value) || 0;
        const bank = parseFloat(document.getElementById('sdpBankAmount').value) || 0;
        
        let chequeTotal = 0;
        document.querySelectorAll('.sdp-ch-amount').forEach(input => {
            chequeTotal += parseFloat(input.value) || 0;
        });
        
        const totalCollected = cash + bank + chequeTotal;
        const balance = grandTotal - totalCollected;
        
        document.getElementById('sdpTotalCollected').innerText = 'Rs ' + totalCollected.toLocaleString('en-US', {minimumFractionDigits: 2});
        document.getElementById('sdpRemainingBalance').innerText = 'Rs ' + balance.toLocaleString('en-US', {minimumFractionDigits: 2});
        
        if (balance <= 0) {
            document.getElementById('sdpRemainingBalance').style.color = '#16a34a';
        } else {
            document.getElementById('sdpRemainingBalance').style.color = '#dc2626';
        }
    }

    function recalculateSdpInvoiceTotal() {
        const modal = document.getElementById('serverDeliveryProcessModal');
        const globalDiscVal = parseFloat(modal.getAttribute('data-global-discount-val') || 0);
        const globalDiscType = modal.getAttribute('data-global-discount-type') || 'Rs';
        const taxPercentage = parseFloat(modal.getAttribute('data-tax-percentage') || 0);
        
        let subTotal = 0;
        document.querySelectorAll('#sdpItemsTbody tr').forEach(row => {
            const qtyInput = row.querySelector('.sdp-delivered-qty');
            if (qtyInput) {
                const qty = parseInt(qtyInput.value) || 0;
                const price = parseFloat(qtyInput.getAttribute('data-price') || 0);
                const discVal = parseFloat(qtyInput.getAttribute('data-discount-val') || 0);
                const discType = qtyInput.getAttribute('data-discount-type') || 'Rs';
                
                const rowGross = qty * price;
                const rowDisc = (discType === '%') ? (rowGross * discVal / 100) : discVal;
                const rowTotal = Math.max(0, rowGross - rowDisc);
                subTotal += rowTotal;
            }
        });
        
        const globalDisc = (globalDiscType === '%') ? (subTotal * globalDiscVal / 100) : globalDiscVal;
        const netSub = Math.max(0, subTotal - globalDisc);
        const taxAmount = netSub * taxPercentage;
        const newGrandTotal = netSub + taxAmount;
        
        document.getElementById('sdpInvoiceId').setAttribute('data-grand-total', newGrandTotal);
        document.getElementById('sdpInvoiceTotal').innerText = 'Rs ' + newGrandTotal.toLocaleString('en-US', {minimumFractionDigits: 2});
        
        updateSdpBalance();
    }

    function closeServerDeliveryProcessModal() {
        document.getElementById('serverDeliveryProcessModal').style.display = 'none';
    }

    function addSdpChequeRow() {
        const container = document.getElementById('sdpChequesContainer');
        const row = document.createElement('div');
        row.className = 'sdp-cheque-row';
        row.style.cssText = 'display:grid; grid-template-columns:1.5fr 1fr 1.2fr 1fr 36px; gap:8px; align-items:center; background:#f8fafc; padding:8px 10px; border-radius:10px; border:1px solid #e2e8f0; transition:all 0.18s;';
        
        row.innerHTML = `
            <input type="text" placeholder="Bank Name" class="sdp-ch-bank" style="padding:6px 10px; border:1px solid #cbd5e1; border-radius:8px; font-size:12px; width:100%; outline:none; background:#ffffff;" required />
            <input type="text" placeholder="Cheque #" class="sdp-ch-number" maxlength="6" oninput="this.value = this.value.replace(/[^0-9]/g, '');" style="padding:6px 10px; border:1px solid #cbd5e1; border-radius:8px; font-size:12px; width:100%; outline:none; background:#ffffff;" required />
            <input type="date" class="sdp-ch-date" style="padding:6px 10px; border:1px solid #cbd5e1; border-radius:8px; font-size:12px; width:100%; outline:none; background:#ffffff;" required />
            <input type="number" step="0.01" min="0" placeholder="Amount" class="sdp-ch-amount" oninput="updateSdpBalance()" style="padding:6px 10px; border:1px solid #cbd5e1; border-radius:8px; font-size:12px; font-weight:700; font-family:monospace; width:100%; outline:none; background:#ffffff;" required />
            <button type="button" onclick="this.closest('.sdp-cheque-row').remove(); updateSdpBalance();" style="background:#fef2f2; border:1px solid #fecaca; color:#dc2626; width:32px; height:32px; border-radius:8px; font-size:14px; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all 0.18s;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'"><i class="ph ph-trash"></i></button>
        `;
        container.appendChild(row);
    }

    function submitServerDeliveryProcess() {
        const routeId = currentRouteId;
        const customerId = document.getElementById('sdpCustomerId').value;
        const invoiceId = document.getElementById('sdpInvoiceId').value;
        
        let deliveryStatus = document.getElementById('sdpDeliveryStatus').value;
        if (deliveryStatus === 'Pending') {
            deliveryStatus = 'Delivered';
            document.getElementById('sdpDeliveryStatus').value = 'Delivered';
        }
        
        // 1. Gather invoice items updates and validate range
        const items = [];
        let qtyValidationFailed = false;
        let qtyValidationMsg = '';
        document.querySelectorAll('#sdpItemsTbody tr').forEach(row => {
            const itemId = row.getAttribute('data-item-id');
            const qtyInput = row.querySelector('.sdp-delivered-qty');
            if (itemId && qtyInput) {
                const val = parseInt(qtyInput.value) || 0;
                const maxVal = parseInt(qtyInput.getAttribute('max')) || 0;
                if (val < 0) {
                    qtyValidationFailed = true;
                    qtyValidationMsg = 'Delivered quantity cannot be negative.';
                }
                if (val > maxVal) {
                    qtyValidationFailed = true;
                    qtyValidationMsg = 'Delivered quantity cannot be greater than loaded quantity (' + maxVal + ').';
                }
                items.push({
                    invoice_item_id: parseInt(itemId),
                    delivered_qty: val
                });
            }
        });

        if (qtyValidationFailed) {
            alert(qtyValidationMsg);
            return;
        }
        
        // 2. Gather payments & collections
        const cash = parseFloat(document.getElementById('sdpCashAmount').value) || 0;
        const bank = parseFloat(document.getElementById('sdpBankAmount').value) || 0;
        
        const cheques = [];
        let chequeValidationFailed = false;
        document.querySelectorAll('.sdp-cheque-row').forEach(row => {
            const bankName = row.querySelector('.sdp-ch-bank').value.trim();
            const chNum = row.querySelector('.sdp-ch-number').value.trim();
            const chDate = row.querySelector('.sdp-ch-date').value;
            const chAmt = parseFloat(row.querySelector('.sdp-ch-amount').value) || 0;
            
            if (!bankName || !chNum || !chDate || chAmt <= 0) {
                chequeValidationFailed = true;
            }
            
            cheques.push({
                bank: bankName,
                number: chNum,
                date: chDate,
                amount: chAmt
            });
        });
        
        if (chequeValidationFailed) {
            alert('Please fill out all fields in the added cheque rows with valid values.');
            return;
        }
        
        // Confirm action
        if (!confirm('Are you sure you want to process this visit and save changes? This will modify the delivery status, adjust quantities, and record payments.')) {
            return;
        }
        
        const payload = {
            route_id: parseInt(routeId),
            customer_id: parseInt(customerId),
            deliveries: [
                {
                    invoice_id: parseInt(invoiceId),
                    delivery_status: deliveryStatus,
                    items: items
                }
            ],
            collections: {
                cash: cash,
                bank: bank,
                cheques: cheques
            }
        };
        
        console.log('[submitServerDeliveryProcess] Submitting delivery visit:', {
            url: '<?= APP_URL ?>/RepTracking/api_process_delivery_visit',
            payload: payload
        });
        
        fetchSecure('<?= APP_URL ?>/RepTracking/api_process_delivery_visit', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(async res => {
            const rawText = await res.text();
            console.log('[submitServerDeliveryProcess] HTTP Status:', res.status, res.statusText);
            console.log('[submitServerDeliveryProcess] Server Response Raw:', rawText);
            
            let data = null;
            try {
                data = JSON.parse(rawText);
                console.log('[submitServerDeliveryProcess] Parsed Response Data:', data);
            } catch (jsonErr) {
                console.error('[submitServerDeliveryProcess] Response is not valid JSON:', rawText);
                throw new Error('Server returned unexpected HTML/non-JSON response (HTTP ' + res.status + '). Check console log for details.\nPreview: ' + rawText.substring(0, 200));
            }
            return { res, data };
        })
        .then(({ res, data }) => {
            if (data.status === 'success') {
                alert(data.message || 'Delivery visit processed successfully!');
                closeServerDeliveryProcessModal();
                loadDeliveryLiveStage(routeId);
            } else {
                const fileInfo = data.file ? ` (${data.file}:${data.line})` : '';
                alert('Error processing visit: ' + (data.message || 'Unknown server error') + fileInfo);
            }
        })
        .catch(err => {
            console.error('[submitServerDeliveryProcess] Process visit failed:', err);
            alert('An unexpected error occurred during submission:\n' + err.message);
        });
    }

    function applyDefensiveGuard(delId, guardId, contentId) {
        const guardEl = document.getElementById(guardId);
        const contentEl = document.getElementById(contentId);
        if (!guardEl || !contentEl) return false;

        let isBlocked = false;
        let title = '';
        let desc = '';

        if (!delId || delId === '0' || delId === '') {
            // isBlocked = true;
            // title = 'Delivery Data Incomplete';
            // desc = 'Reconciliation and postings are unavailable because delivery has not been arranged for this route yet.';
        } else if (currentRouteStatus !== 'Finalizing' && currentRouteStatus !== 'Completed' && currentRouteStatus !== 'Finalized') {
            // isBlocked = true;
            // title = 'Preview Not Available';
            // desc = 'Reconciliation and GL postings can only be performed once the route delivery has completed execution.';
        }

        if (isBlocked) {
            guardEl.innerHTML = `
                <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 45px 20px; text-align: center; max-width: 580px; margin: 40px auto; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                    <div style="width: 60px; height: 60px; background: #fffbeb; color: #d97706; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto; font-size: 28px;">
                        <i class="ph ph-warning-circle"></i>
                    </div>
                    <h4 style="margin: 0 0 10px 0; font-size: 15px; font-weight: bold; color: #0f172a;">${title}</h4>
                    <p style="margin: 0; font-size: 12px; color: #64748b; line-height: 1.6;">${desc}</p>
                </div>
            `;
            guardEl.style.display = 'block';
            contentEl.style.display = 'none';
            return true;
        } else {
            guardEl.style.display = 'none';
            contentEl.style.display = 'block';
            return false;
        }
    }

    function applyPaymentsDefensiveGuard(routeId, delId) {
        const guardEl = document.getElementById('tab10GuardContainer');
        const contentEl = document.getElementById('tab10ContentContainer');
        if (!guardEl || !contentEl) return false;

        let isBlocked = false;
        let title = '';
        let desc = '';

        if (currentRouteStatus === 'Active') {
            isBlocked = true;
            title = 'Route Still Active';
            desc = 'Payments collected cannot be verified until the representative has ended the route from their app.';
        }

        if (isBlocked) {
            guardEl.innerHTML = `
                <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 45px 20px; text-align: center; max-width: 580px; margin: 40px auto; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                    <div style="width: 60px; height: 60px; background: #fffbeb; color: #d97706; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto; font-size: 28px;">
                        <i class="ph ph-warning-circle"></i>
                    </div>
                    <h4 style="margin: 0 0 10px 0; font-size: 15px; font-weight: bold; color: #0f172a;">${title}</h4>
                    <p style="margin: 0; font-size: 12px; color: #64748b; line-height: 1.6;">${desc}</p>
                </div>
            `;
            guardEl.style.display = 'block';
            contentEl.style.display = 'none';
            return true;
        } else {
            guardEl.style.display = 'none';
            contentEl.style.display = 'block';
            return false;
        }
    }

    function switchExpenseSubTab(category) {
        const catInput = document.getElementById('expCategory');
        if (catInput) catInput.value = category;

        const btnRep = document.getElementById('btnExpenseSubTabRep');
        const btnDel = document.getElementById('btnExpenseSubTabDelivery');
        const headRep = document.getElementById('subtabRepHeader');
        const headDel = document.getElementById('subtabDeliveryHeader');
        const fieldsRep = document.getElementById('fieldsRepExpenses');
        const fieldsDel = document.getElementById('fieldsDeliveryExpenses');

        if (category === 'Rep') {
            if (btnRep) btnRep.classList.add('active');
            if (btnDel) btnDel.classList.remove('active');
            if (headRep) headRep.style.display = 'block';
            if (headDel) headDel.style.display = 'none';
            if (fieldsRep) fieldsRep.style.display = 'block';
            if (fieldsDel) fieldsDel.style.display = 'none';
        } else {
            if (btnDel) btnDel.classList.add('active');
            if (btnRep) btnRep.classList.remove('active');
            if (headDel) headDel.style.display = 'block';
            if (headRep) headRep.style.display = 'none';
            if (fieldsDel) fieldsDel.style.display = 'block';
            if (fieldsRep) fieldsRep.style.display = 'none';
        }
    }

    function loadTabExpenses(routeId) {
        const rdata = document.getElementById('route_data_' + routeId);
        if (!rdata) return;

        const routeName = rdata.getAttribute('data-rname') || '';
        const repName = rdata.getAttribute('data-rep') || '';
        const formattedRouteNo = '#RT-' + String(routeId).padStart(5, '0');

        // Populate Rep Expenses fields
        if (document.getElementById('expRouteNumber')) document.getElementById('expRouteNumber').value = formattedRouteNo + ' - ' + routeName;
        if (document.getElementById('expRepName')) document.getElementById('expRepName').value = repName;
        if (document.getElementById('expDateTime')) document.getElementById('expDateTime').value = new Date().toLocaleString();

        // Populate Delivery Expenses fields
        const delId = rdata.getAttribute('data-delivery-id');
        if (delId && delId !== '0' && delId !== '') {
            const formattedDelNo = '#DEL-' + String(delId).padStart(5, '0');
            if (document.getElementById('expDelManifest')) document.getElementById('expDelManifest').value = formattedDelNo;
            if (document.getElementById('expDelDateTime')) document.getElementById('expDelDateTime').value = new Date().toLocaleString();

            fetchSecure('<?= APP_URL ?>/RepTracking/api_get_delivery_details/' + delId)
                .then(res => res.json())
                .then(dData => {
                    if (dData.delivery) {
                        const delVeh = dData.delivery.vehicle_number || 'Unassigned';
                        let drvInfo = dData.delivery.driver_name || 'Unassigned Driver';
                        if (dData.delivery.partner_name) drvInfo += ' (Helper: ' + dData.delivery.partner_name + ')';
                        
                        if (document.getElementById('expDelVehicle')) document.getElementById('expDelVehicle').value = delVeh;
                        if (document.getElementById('expDelDriverInfo')) document.getElementById('expDelDriverInfo').value = drvInfo;

                        // Pre-select delivery vehicle in Rep dropdown if none selected yet
                        const vehSelect = document.getElementById('expVehicleNumber');
                        if (vehSelect && !vehSelect.value && delVeh !== 'Unassigned') {
                            vehSelect.value = delVeh;
                        }
                    }
                });
        } else {
            if (document.getElementById('expDelManifest')) document.getElementById('expDelManifest').value = 'No Delivery Arranged';
            if (document.getElementById('expDelVehicle')) document.getElementById('expDelVehicle').value = 'Not Arranged';
            if (document.getElementById('expDelDriverInfo')) document.getElementById('expDelDriverInfo').value = 'Not Arranged';
            if (document.getElementById('expDelDateTime')) document.getElementById('expDelDateTime').value = new Date().toLocaleString();
        }

        // Fetch recorded expenses and available balances
        fetchSecure('<?= APP_URL ?>/RepTracking/api_get_route_expenses/' + routeId)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    // Update balances labels
                    document.getElementById('lblAvailPettyCash').innerText = 'Rs ' + parseFloat(data.available_petty_cash).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    document.getElementById('lblAvailRouteCash').innerText = 'Rs ' + parseFloat(data.available_route_cash).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                    // Render expenses list
                    const listContainer = document.getElementById('listRecordedExpenses');
                    listContainer.innerHTML = '';

                    const isReadOnly = (currentRouteStatus === 'Completed' || currentRouteStatus === 'Finalized');

                    if (data.expenses.length === 0) {
                        listContainer.innerHTML = `
                            <div style="text-align:center; padding: 48px 20px; color:#94a3b8;">
                                <i class="ph ph-receipt" style="font-size:40px; color:#cbd5e1; margin-bottom: 8px;"></i>
                                <p style="margin:0; font-size: 13px; font-weight: 500; color: #64748b;">No expenses recorded for this route.</p>
                            </div>
                        `;
                    } else {
                        let html = `
                            <table style="width:100%; border-collapse:separate; border-spacing:0; font-size:12px;">
                                <thead>
                                    <tr style="background:#f8fafc; color:#64748b; font-weight:700; text-transform:uppercase; font-size:10px; letter-spacing:0.4px;">
                                        <th style="padding:10px 10px; text-align:left; border-bottom:1px solid #e2e8f0; border-top-left-radius:6px;">Category / Type</th>
                                        <th style="padding:10px 10px; text-align:left; border-bottom:1px solid #e2e8f0;">Details / Source</th>
                                        <th style="padding:10px 10px; text-align:right; border-bottom:1px solid #e2e8f0;">Amount</th>
                                        <th style="padding:10px 10px; text-align:center; border-bottom:1px solid #e2e8f0; border-top-right-radius:6px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;
                        data.expenses.forEach((exp, idx) => {
                            const dateStr = new Date(exp.expense_date).toLocaleDateString();
                            const amt = parseFloat(exp.amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            const category = exp.expense_category || 'Rep';
                            const badgeBg = (category === 'Delivery') ? '#fef3c7' : '#e0f2fe';
                            const badgeColor = (category === 'Delivery') ? '#b45309' : '#0369a1';
                            const rowBg = (idx % 2 === 0) ? '#ffffff' : '#f8fafc';
                            
                            let detailsSub = exp.payment_source;
                            if (category === 'Delivery' && exp.driver_name) {
                                detailsSub = exp.driver_name + ' (' + (exp.vehicle_number || 'Veh') + ') • ' + exp.payment_source;
                            } else if (exp.vehicle_number) {
                                detailsSub = 'Veh: ' + exp.vehicle_number + ' • ' + exp.payment_source;
                            }

                            html += `
                                <tr style="background:${rowBg}; border-bottom:1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='${rowBg}'">
                                    <td style="padding:10px 10px; border-bottom:1px solid #f1f5f9;">
                                        <div style="display:flex; align-items:center; gap:6px;">
                                            <span style="padding:2px 6px; border-radius:4px; font-size:9px; font-weight:800; background:${badgeBg}; color:${badgeColor}; text-transform:uppercase;">${category}</span>
                                            <span style="font-weight:600; color:#0f172a;">${exp.expense_type}</span>
                                        </div>
                                        <div style="font-size:10px; color:#64748b; font-family:monospace; margin-top:2px;">${exp.receipt_number ? 'Rec: ' + exp.receipt_number : exp.description.substring(0, 30)}</div>
                                    </td>
                                    <td style="padding:10px 10px; color:#475569; font-size:11px; border-bottom:1px solid #f1f5f9;">
                                        ${detailsSub}
                                    </td>
                                    <td style="padding:10px 10px; text-align:right; font-weight:700; font-family:monospace; color:#dc2626; font-size:13px; border-bottom:1px solid #f1f5f9;">
                                        Rs ${amt}
                                    </td>
                                    <td style="padding:10px 10px; text-align:center; border-bottom:1px solid #f1f5f9;">
                                        ${isReadOnly ? `
                                            <span style="font-size:10px; color:#94a3b8; font-weight:600; font-style:italic;">Posted</span>
                                        ` : `
                                            <button type="button" onclick="deleteRouteExpense(${exp.id})" style="background:rgba(239,68,68,0.08); border:none; color:#dc2626; border-radius:6px; cursor:pointer; width:28px; height:28px; display:inline-flex; align-items:center; justify-content:center; transition:all 0.15s;" title="Delete Expense" onmouseover="this.style.background='#dc2626'; this.style.color='#fff';" onmouseout="this.style.background='rgba(239,68,68,0.08)'; this.style.color='#dc2626';">
                                                <i class="ph ph-trash" style="font-size:14px;"></i>
                                            </button>
                                        `}
                                    </td>
                                </tr>
                            `;
                        });
                        html += '</tbody></table>';
                        listContainer.innerHTML = html;
                    }

                    // Apply read-only styling to form if route is completed
                    const form = document.getElementById('formRecordRouteExpense');
                    const elements = form.elements;
                    for (let i = 0; i < elements.length; i++) {
                        if (elements[i].id !== 'expRouteNumber' && elements[i].id !== 'expRepName' && elements[i].id !== 'expDateTime' && elements[i].id !== 'expDelManifest' && elements[i].id !== 'expDelVehicle' && elements[i].id !== 'expDelDriverInfo' && elements[i].id !== 'expDelDateTime') {
                            elements[i].disabled = isReadOnly;
                        }
                    }
                    const submitBtn = document.getElementById('btnSubmitRouteExpense');
                    if (submitBtn) {
                        submitBtn.disabled = isReadOnly;
                        submitBtn.style.opacity = isReadOnly ? '0.5' : '1';
                        submitBtn.style.cursor = isReadOnly ? 'not-allowed' : 'pointer';
                    }
                }
            });
    }

    function onExpenseSourceChanged() {
        const category = document.getElementById('expCategory')?.value || 'Rep';
        const source = document.getElementById('expSource').value;
        const type = document.getElementById('expType').value;
        const vehicle = (category === 'Delivery') ? document.getElementById('expDelVehicle').value : document.getElementById('expVehicleNumber').value;
        const descInput = document.getElementById('expDescription');

        // Autofill description
        if (type) {
            let autofillDesc = category + ' ' + type + ' expense on route';
            if (type === 'Fuel') {
                autofillDesc = 'Fuel allowance for vehicle ' + (vehicle || 'trip');
            } else if (type === 'Vehicle Maintenance') {
                autofillDesc = 'Routine maintenance for vehicle ' + (vehicle || 'trip');
            }
            descInput.value = autofillDesc;
        }
    }

    // Attach type change listener as well
    document.getElementById('expType')?.addEventListener('change', onExpenseSourceChanged);

    function submitRouteExpense(event) {
        event.preventDefault();

        const routeId = currentRouteId;
        const category = document.getElementById('expCategory').value || 'Rep';
        const amount = parseFloat(document.getElementById('expAmount').value);
        const type = document.getElementById('expType').value;
        const source = document.getElementById('expSource').value;
        const desc = document.getElementById('expDescription').value;
        const receipt = document.getElementById('expReceiptNumber').value;

        let vehicleNumber = '';
        let driverName = '';

        if (category === 'Rep') {
            vehicleNumber = document.getElementById('expVehicleNumber').value;
            if (!vehicleNumber) {
                alert('Please select an assigned vehicle for Representative Expense.');
                return;
            }
        } else {
            vehicleNumber = document.getElementById('expDelVehicle').value;
            driverName = document.getElementById('expDelDriverInfo').value;
            if (!vehicleNumber || vehicleNumber === 'Not Arranged') {
                alert('Delivery has not been arranged yet for this route. Please arrange logistics in the Dispatch stage before recording Delivery Expenses.');
                return;
            }
        }

        if (!type || !source || isNaN(amount) || amount <= 0 || !desc) {
            alert('Please fill in all required fields.');
            return;
        }

        const payload = {
            rep_route_id: routeId,
            expense_category: category,
            amount: amount,
            expense_type: type,
            payment_source: source,
            description: desc,
            receipt_number: receipt,
            vehicle_number: vehicleNumber,
            driver_name: driverName
        };

        document.getElementById('btnSubmitRouteExpense').disabled = true;

        fetchSecure('<?= APP_URL ?>/RepTracking/api_add_route_expense', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('btnSubmitRouteExpense').disabled = false;
            if (data.status === 'success') {
                alert(data.message);
                // Clear inputs
                document.getElementById('expAmount').value = '';
                document.getElementById('expReceiptNumber').value = '';
                document.getElementById('expDescription').value = '';
                document.getElementById('expType').selectedIndex = 0;
                document.getElementById('expSource').selectedIndex = 0;
                
                // Reload
                loadTabExpenses(routeId);
                
                // Fetch fresh delivery details so Reconciliation gets updated expected cash handover!
                const rdata = document.getElementById('route_data_' + routeId);
                const delId = rdata ? rdata.getAttribute('data-delivery-id') : null;
                if (delId && delId !== '0') {
                    fetchSecure('<?= APP_URL ?>/RepTracking/api_get_delivery_details/' + delId)
                        .then(res => res.json())
                        .then(freshDel => {
                            currentDeliveryDetails = freshDel;
                        });
                }
            } else {
                alert(data.message || 'Failed to record expense.');
            }
        })
        .catch(err => {
            document.getElementById('btnSubmitRouteExpense').disabled = false;
            alert('Error recording expense: ' + err.message);
        });
    }

    function deleteRouteExpense(expenseId) {
        if (confirm('Are you sure you want to delete this expense? This will reverse the journal entry and restore the balance.')) {
            fetchSecure('<?= APP_URL ?>/RepTracking/api_delete_route_expense/' + expenseId, {
                method: 'POST'
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    loadTabExpenses(currentRouteId);
                    
                    // Fetch fresh delivery details to update reconciliation
                    const rdata = document.getElementById('route_data_' + currentRouteId);
                    const delId = rdata ? rdata.getAttribute('data-delivery-id') : null;
                    if (delId && delId !== '0') {
                        fetchSecure('<?= APP_URL ?>/RepTracking/api_get_delivery_details/' + delId)
                            .then(res => res.json())
                            .then(dData => {
                                currentDeliveryDetails = dData;
                            });
                    }
                } else {
                    alert(data.message);
                }
            });
        }
    }

    // Expose delete function to global scope
    window.deleteRouteExpense = deleteRouteExpense;
    window.loadTabExpenses = loadTabExpenses;
    window.onExpenseSourceChanged = onExpenseSourceChanged;
    window.submitRouteExpense = submitRouteExpense;
    window.switchExpenseSubTab = switchExpenseSubTab;

    function loadTab8Reconciliation(routeId) {
        const rdata = document.getElementById('route_data_' + routeId);
        const delId = rdata ? rdata.getAttribute('data-delivery-id') : null;

        if (applyDefensiveGuard(delId, 'tab7GuardContainer', 'tab7ContentContainer')) {
            return;
        }

        document.getElementById('reconExpectedCash').innerText = 'Rs 0.00';
        document.getElementById('reconExpectedCollections').innerText = 'Rs 0.00';
        document.getElementById('reconTotalExpectedCash').innerText = 'Rs 0.00';
        if (document.getElementById('reconRouteExpenses')) document.getElementById('reconRouteExpenses').innerText = 'Rs 0.00';
        if (document.getElementById('reconNetExpectedCash')) document.getElementById('reconNetExpectedCash').innerText = 'Rs 0.00';
        document.getElementById('reconActualCash').value = '0.00';
        document.getElementById('reconCashVariance').innerText = 'Rs 0.00';
        document.getElementById('reconAuditNotes').value = '';
        document.getElementById('reconChequesTbody').innerHTML = '<tr><td colspan="4" style="text-align:center;">Loading cheques... </td></tr>';

        const isReadOnly = (currentRouteStatus === 'Completed' || currentRouteStatus === 'Finalized');
        const saveBtn = document.getElementById('btnSaveReconciliationDraft');
        if (saveBtn) {
            saveBtn.disabled = isReadOnly;
            saveBtn.style.opacity = isReadOnly ? '0.5' : '1';
            saveBtn.style.cursor = isReadOnly ? 'not-allowed' : 'pointer';
        }
        document.getElementById('reconActualCash').disabled = isReadOnly;
        document.getElementById('reconAuditNotes').disabled = isReadOnly;

        const denList = [5000, 2000, 1000, 500, 100, 50, 20];
        denList.forEach(den => {
            document.getElementById('actQty' + den).disabled = isReadOnly;
        });
        document.getElementById('actValCoins').disabled = isReadOnly;

        fetchSecure('<?= APP_URL ?>/RepTracking/api_get_delivery_details/' + delId)
            .then(res => res.json())
            .then(data => {
                if (data.status !== 'success') return;

                currentDeliveryDetails = data;
                const balancing = data.balancing;

                const expectedCashSales = parseFloat(balancing.cash_sales || 0);
                const rawCashCollections = parseFloat(balancing.raw_cash_collections || 0);
                const expectedCashColls = Math.max(0, rawCashCollections - expectedCashSales);
                const routeExpenses = parseFloat(balancing.collected_cash_expenses_total || 0);
                const netExpectedCash = Math.max(0, rawCashCollections - routeExpenses);

                document.getElementById('reconExpectedCash').innerText = 'Rs ' + expectedCashSales.toLocaleString('en-US', {minimumFractionDigits: 2});
                document.getElementById('reconExpectedCollections').innerText = 'Rs ' + expectedCashColls.toLocaleString('en-US', {minimumFractionDigits: 2});
                document.getElementById('reconTotalExpectedCash').innerText = 'Rs ' + rawCashCollections.toLocaleString('en-US', {minimumFractionDigits: 2});
                if (document.getElementById('reconRouteExpenses')) {
                    document.getElementById('reconRouteExpenses').innerText = 'Rs ' + routeExpenses.toLocaleString('en-US', {minimumFractionDigits: 2});
                }
                if (document.getElementById('reconNetExpectedCash')) {
                    document.getElementById('reconNetExpectedCash').innerText = 'Rs ' + netExpectedCash.toLocaleString('en-US', {minimumFractionDigits: 2});
                }

                let actualCash = 0;
                let remarks = '';
                let actualDenoms = null;
                let chequeApprovals = {};
                let isReadOnly = (currentRouteStatus === 'Completed' || currentRouteStatus === 'Finalized');

                if (data.delivery && data.delivery.reconciliation_json) {
                    try {
                        const recon = JSON.parse(data.delivery.reconciliation_json);
                        actualCash = parseFloat(recon.actual_cash || 0);
                        remarks = recon.audit_remarks || '';
                        actualDenoms = recon.actual_denominations || null;
                        chequeApprovals = recon.cheque_approvals || {};
                    } catch(e) {}
                }

                // Re-apply read-only locks to form elements
                const saveBtn = document.getElementById('btnSaveReconciliationDraft');
                if (saveBtn) {
                    saveBtn.disabled = isReadOnly;
                    saveBtn.style.opacity = isReadOnly ? '0.5' : '1';
                    saveBtn.style.cursor = isReadOnly ? 'not-allowed' : 'pointer';
                }
                document.getElementById('reconActualCash').disabled = isReadOnly;
                document.getElementById('reconAuditNotes').disabled = isReadOnly;
                denList.forEach(den => {
                    document.getElementById('actQty' + den).disabled = isReadOnly;
                });
                document.getElementById('actValCoins').disabled = isReadOnly;

                // Parse collector's denominations
                let colDenoms = {};
                if (data.delivery && data.delivery.cash_denominations) {
                    try {
                        colDenoms = JSON.parse(data.delivery.cash_denominations) || {};
                    } catch(e) {}
                }

                // Populate collector column UI
                denList.forEach(den => {
                    const qty = parseInt(colDenoms[den] || 0);
                    const val = qty * den;
                    document.getElementById('colQty' + den).innerText = qty;
                    document.getElementById('colVal' + den).innerText = val.toLocaleString('en-US', {minimumFractionDigits: 2});
                });
                const colCoins = parseFloat(colDenoms.coins || 0);
                document.getElementById('colValCoins').innerText = colCoins.toLocaleString('en-US', {minimumFractionDigits: 2});

                // Populate actual column UI
                // If actualDenoms is present, use it. Otherwise, default to collector's denominations.
                const denToUse = actualDenoms || colDenoms;

                denList.forEach(den => {
                    const qty = parseInt(denToUse[den] || 0);
                    document.getElementById('actQty' + den).value = qty || '';
                });
                const actCoins = parseFloat(denToUse.coins || 0);
                document.getElementById('actValCoins').value = actCoins || '';

                // Call recalculateDenominations to calculate totals and values
                recalculateDenominations();

                document.getElementById('reconAuditNotes').value = remarks;

                // Render cheques
                const chequesTbody = document.getElementById('reconChequesTbody');
                chequesTbody.innerHTML = '';
                const cheques = balancing.payments ? balancing.payments.filter(p => p.payment_method === 'Cheque') : [];
                if (cheques.length === 0) {
                    chequesTbody.innerHTML = '<tr><td colspan="4" style="text-align:center; color:#94a3b8; padding:30px; font-weight:500;"><i class="ph ph-check-circle" style="font-size:24px; color:#cbd5e1; display:block; margin-bottom:6px;"></i>No cheques collected for this route.</td></tr>';
                } else {
                    cheques.forEach((ch, idx) => {
                        let isChApproved = (chequeApprovals && (chequeApprovals[ch.id] === true || chequeApprovals[String(ch.id)] === true)) || parseInt(ch.is_verified) === 1;
                        ch.is_verified = isChApproved ? 1 : 0;
                        const rowBg = (idx % 2 === 0) ? '#ffffff' : '#f8fafc';
                        let approveBox = `
                            <input type="checkbox" onchange="toggleReconChequeApproval(${ch.id}, this.checked)" 
                                   ${isChApproved ? 'checked' : ''} ${isReadOnly ? 'disabled' : ''} 
                                   data-payment-id="${ch.id}"
                                   style="width:17px; height:17px; cursor:${isReadOnly ? 'not-allowed' : 'pointer'}; accent-color:#16a34a;" />
                        `;
                        chequesTbody.innerHTML += `
                            <tr style="background:${rowBg}; border-bottom:1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='${rowBg}'">
                                <td style="padding:10px; font-weight:600; color:#0f172a; border-bottom:1px solid #f1f5f9;">${ch.customer_name}</td>
                                <td style="padding:10px; font-family:monospace; color:#475569; font-size:11px; border-bottom:1px solid #f1f5f9;">${ch.cheque_number || 'N/A'}</td>
                                <td style="padding:10px; text-align:right; font-family:monospace; font-weight:700; color:#0f172a; font-size:13px; border-bottom:1px solid #f1f5f9;">Rs ${parseFloat(ch.amount).toFixed(2)}</td>
                                <td style="padding:10px; text-align:center; border-bottom:1px solid #f1f5f9;">${approveBox}</td>
                            </tr>
                        `;
                    });
                }
            });
    }

    function recalculateDenominations() {
        const denoms = [5000, 2000, 1000, 500, 100, 50, 20];
        let total = 0;
        denoms.forEach(den => {
            const qty = parseInt(document.getElementById('actQty' + den).value || 0);
            const val = qty * den;
            document.getElementById('actVal' + den).innerText = val.toLocaleString('en-US', {minimumFractionDigits: 2});
            total += val;
        });

        const coinsVal = parseFloat(document.getElementById('actValCoins').value || 0);
        document.getElementById('actValCoinsTotal').innerText = coinsVal.toLocaleString('en-US', {minimumFractionDigits: 2});
        total += coinsVal;

        document.getElementById('reconActualCash').value = total.toFixed(2);
        calculateCashVariance();
    }

    function calculateCashVariance() {
        const netExpectedEl = document.getElementById('reconNetExpectedCash');
        const expectedStr = netExpectedEl ? netExpectedEl.innerText.replace('Rs ', '').replace(/,/g, '') : '0';
        const expected = parseFloat(expectedStr) || 0;
        const actual = parseFloat(document.getElementById('reconActualCash').value) || 0;
        const variance = actual - expected;

        const el = document.getElementById('reconCashVariance');
        el.innerText = 'Rs ' + variance.toLocaleString('en-US', {minimumFractionDigits: 2});
        if (variance < 0) {
            el.style.color = '#c62828';
        } else if (variance > 0) {
            el.style.color = '#2e7d32';
        } else {
            el.style.color = '#000';
        }
    }

    function toggleReconChequeApproval(paymentId, checked) {
        if (!currentDeliveryDetails) return;
        const payments = currentDeliveryDetails.balancing.payments || [];
        const ch = payments.find(p => p.id === paymentId);
        if (ch) {
            ch.is_verified = checked ? 1 : 0;
        }
    }

    function saveReconciliationDraft() {
        if (!currentRouteId || !currentDeliveryDetails) return;
        
        // Verify all cheques are checked
        const chequeCheckboxes = document.querySelectorAll('#reconChequesTbody input[type="checkbox"]');
        let allChequesVerified = true;
        chequeCheckboxes.forEach(cb => {
            if (!cb.checked) {
                allChequesVerified = false;
            }
        });
        if (!allChequesVerified) {
            alert('Verification of all cheques is required before saving the reconciliation.');
            return;
        }

        const actualCash = parseFloat(document.getElementById('reconActualCash').value) || 0;
        const remarks = document.getElementById('reconAuditNotes').value;

        const chequeApprovals = {};
        const payments = currentDeliveryDetails.balancing.payments || [];
        payments.forEach(p => {
            if (p.payment_method === 'Cheque') {
                chequeApprovals[p.id] = parseInt(p.is_verified) === 1;
            }
        });

        const actualDenoms = {};
        const denoms = [5000, 2000, 1000, 500, 100, 50, 20];
        denoms.forEach(den => {
            actualDenoms[den] = parseInt(document.getElementById('actQty' + den).value || 0);
        });
        actualDenoms['coins'] = parseFloat(document.getElementById('actValCoins').value || 0);

        const reconData = {
            actual_cash: actualCash,
            audit_remarks: remarks,
            cheque_approvals: chequeApprovals,
            actual_denominations: actualDenoms
        };

        const deliveryId = currentDeliveryDetails.delivery.id;

        fetchSecure('<?= APP_URL ?>/RepTracking/api_save_reconciliation', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ delivery_id: deliveryId, reconciliation_data: reconData })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                alert("Reconciliation draft saved successfully!");
                onRouteDataChanged();
            } else {
                alert("Error: " + data.message);
            }
        });
    }

    function loadTab9ReturnStock(routeId) {
        const rdata = document.getElementById('route_data_' + routeId);
        const delId = rdata ? rdata.getAttribute('data-delivery-id') : null;

        const tbody = document.getElementById('settleStockTableBody');
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">Loading return stock counts... </td></tr>';

        const verifyStockCheck = document.getElementById('settleVerifyStock');
        if (verifyStockCheck) {
            verifyStockCheck.checked = false;
        }

        if (!delId || delId === '0' || delId === '') {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; color:#888;">Delivery has not been arranged.</td></tr>';
            return;
        }

        const isReadOnly = (currentRouteStatus === 'Completed' || currentRouteStatus === 'Finalized');
        const saveBtn = document.getElementById('btnSaveReturnStockDraft');
        if (saveBtn) {
            saveBtn.disabled = isReadOnly;
            saveBtn.style.opacity = isReadOnly ? '0.5' : '1';
            saveBtn.style.cursor = isReadOnly ? 'not-allowed' : 'pointer';
        }
        if (verifyStockCheck) {
            verifyStockCheck.disabled = isReadOnly;
        }

        fetchSecure('<?= APP_URL ?>/RepTracking/api_get_delivery_details/' + delId)
            .then(res => res.json())
            .then(data => {
                if (data.status !== 'success') return;
                currentDeliveryDetails = data;

                let savedReturnStock = null;
                if (data.delivery && data.delivery.return_stock_json) {
                    try {
                        savedReturnStock = JSON.parse(data.delivery.return_stock_json);
                    } catch(e) {}
                }

                const isReadOnly = (currentRouteStatus === 'Completed' || currentRouteStatus === 'Finalized');
                if (saveBtn) {
                    saveBtn.disabled = isReadOnly;
                    saveBtn.style.opacity = isReadOnly ? '0.5' : '1';
                    saveBtn.style.cursor = isReadOnly ? 'not-allowed' : 'pointer';
                }
                if (verifyStockCheck) {
                    verifyStockCheck.disabled = isReadOnly;
                    if (savedReturnStock) {
                        verifyStockCheck.checked = true;
                    }
                }

                tbody.innerHTML = '';
                if (!data.balancing.stock_items || data.balancing.stock_items.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; color:#888;">No stock items loaded.</td></tr>';
                } else {
                    data.balancing.stock_items.forEach(st => {
                        let expectedReturned = parseInt(st.loaded_qty) - parseInt(st.delivered_qty);
                        if (expectedReturned < 0) expectedReturned = 0;
                        
                        let actualCounted = parseInt(expectedReturned);
                        if (savedReturnStock) {
                            const savedVal = savedReturnStock.find(x => x.item_id === st.item_id && x.variation_option_id === st.variation_option_id);
                            if (savedVal) {
                                actualCounted = parseInt(savedVal.actual_returned_qty);
                            }
                        }

                        tbody.innerHTML += `
                            <tr>
                                <td><strong>${st.item_name}</strong></td>
                                <td style="text-align:center; font-weight:bold;">${parseInt(st.loaded_qty)}</td>
                                <td style="text-align:center; color:#2e7d32; font-weight:bold;">${parseInt(st.delivered_qty)}</td>
                                <td style="text-align:center; font-weight:bold; font-family:monospace; background:#fafafa;">${expectedReturned}</td>
                                <td style="text-align:right;">
                                    <input type="number" class="actual-returned-input" 
                                           data-name="${st.item_name}" data-item-id="${st.item_id}" data-var-id="${st.variation_option_id || 0}"
                                           data-loaded="${st.loaded_qty}" data-delivered="${st.delivered_qty}" 
                                           value="${actualCounted}" min="0" ${isReadOnly ? 'disabled' : ''}
                                           style="width:80px; text-align:right; padding:4px; font-family:monospace; font-weight:bold;" />
                                </td>
                            </tr>
                        `;
                    });
                }

                checkSettleVerification();
            });
    }

    function saveReturnStockDraft() {
        if (!currentRouteId || !currentDeliveryDetails) {
            console.log("saveReturnStockDraft aborted: missing currentRouteId or currentDeliveryDetails", {currentRouteId, currentDeliveryDetails});
            return;
        }

        if (!confirm("Save return stock draft?")) {
            return;
        }

        const returnedItems = [];
        document.querySelectorAll('.actual-returned-input').forEach(input => {
            returnedItems.push({
                item_name: input.getAttribute('data-name'),
                item_id: parseInt(input.getAttribute('data-item-id') || 0),
                variation_option_id: parseInt(input.getAttribute('data-var-id') || 0),
                loaded_qty: parseInt(input.getAttribute('data-loaded') || 0),
                delivered_qty: parseInt(input.getAttribute('data-delivered') || 0),
                actual_returned_qty: parseInt(input.value || 0)
            });
        });

        const deliveryId = currentDeliveryDetails.delivery.id;
        const payload = { delivery_id: deliveryId, return_stock_data: returnedItems };
        console.log("[ReturnStock] Submitting Save Draft. Payload:", payload);

        fetchSecure('<?= APP_URL ?>/RepTracking/api_save_return_stock', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => {
            console.log("[ReturnStock] Server response status:", res.status);
            return res.text();
        })
        .then(text => {
            console.log("[ReturnStock] Server raw body:", text);
            let data;
            try {
                data = JSON.parse(text);
            } catch(e) {
                console.error("[ReturnStock] Failed to parse response as JSON. Error:", e);
                alert("Server returned invalid response. Please check the browser console.");
                return;
            }

            console.log("[ReturnStock] Parsed response data:", data);
            if (data.debug_logs && Array.isArray(data.debug_logs)) {
                console.group("[ReturnStock] Backend Debug Execution Trace:");
                data.debug_logs.forEach(logLine => console.log(logLine));
                console.groupEnd();
            }

            if (data.status === 'success') {
                console.log("[ReturnStock] Save draft success. Updating local state and UI.");
                alert("Return stock verified and saved successfully!");
                // Update local delivery details to avoid page-reload requirements
                if (currentDeliveryDetails && currentDeliveryDetails.delivery) {
                    currentDeliveryDetails.delivery.return_stock_json = JSON.stringify(returnedItems);
                }
                // Synchronize the checkbox
                const verifyStockCheck = document.getElementById('settleVerifyStock');
                if (verifyStockCheck) {
                    verifyStockCheck.checked = true;
                }
                checkSettleVerification();
                onRouteDataChanged();
            } else {
                console.warn("[ReturnStock] Save draft failed. Server message:", data.message);
                alert("Error: " + data.message);
            }
        })
        .catch(err => {
            console.error("[ReturnStock] Fetch error:", err);
            alert("An unexpected network error occurred. Please check the browser console.");
        });
    }

    function loadTab10Accounting(routeId) {
        const rdata = document.getElementById('route_data_' + routeId);
        const delId = rdata ? rdata.getAttribute('data-delivery-id') : null;

        if (applyPaymentsDefensiveGuard(routeId, delId)) {
            return;
        }

        const colContainer = document.getElementById('settleDeCollectionsContainer');
        const salesContainer = document.getElementById('settleDeSalesContainer');
        
        colContainer.innerHTML = '<p style="text-align:center; color:#888;">Loading account mappings... </p>';
        salesContainer.innerHTML = '';

        document.getElementById('settleDaVehicle').value = '';
        document.getElementById('settleDaDriver').value = '';
        document.getElementById('settleDaPartner').value = '';

        const isReadOnly = (currentRouteStatus === 'Completed' || currentRouteStatus === 'Finalized');
        const saveBtn = document.getElementById('btnSaveAccountingDraft');
        if (saveBtn) {
            saveBtn.disabled = isReadOnly;
            saveBtn.style.opacity = isReadOnly ? '0.5' : '1';
            saveBtn.style.cursor = isReadOnly ? 'not-allowed' : 'pointer';
        }
        document.getElementById('settleDaVehicle').disabled = isReadOnly;
        document.getElementById('settleDaDriver').disabled = isReadOnly;
        document.getElementById('settleDaPartner').disabled = isReadOnly;

        fetchSecure('<?= APP_URL ?>/RepTracking/api_get_delivery_details/' + (delId || 0) + '?route_id=' + routeId)
            .then(res => res.json())
            .then(data => {
                if (data.status !== 'success') return;
                currentDeliveryDetails = data;

                if (data.delivery) {
                    document.getElementById('settleDaVehicle').value = data.delivery.vehicle_number || '';
                    document.getElementById('settleDaDriver').value = data.delivery.driver_name || '';
                    document.getElementById('settleDaPartner').value = data.delivery.partner_name || '';
                }

                renderSettleDoubleEntries();

                if (data.delivery && data.delivery.accounting_entries_json) {
                    try {
                        const accEntries = JSON.parse(data.delivery.accounting_entries_json);
                        document.querySelectorAll('.settle-de-select, .settle-payment-debit, .settle-payment-credit').forEach(sel => {
                            const id = sel.getAttribute('data-id');
                            const type = sel.getAttribute('data-type');
                            let val = null;
                            if (accEntries[type]) {
                                if (accEntries[type][id]) {
                                    val = accEntries[type][id];
                                } else {
                                    const rawId = id.replace(/^(pay_|inv_)/, '');
                                    val = accEntries[type][rawId];
                                }
                            }
                            if (val) {
                                sel.value = val;
                            }
                        });
                    } catch(e) {}
                }

                if (isReadOnly) {
                    document.querySelectorAll('.settle-de-select, .settle-payment-debit, .settle-payment-credit, .settle-payment-chk, .settle-invoice-chk, .settle-payment-adj, .settle-payment-notes').forEach(el => {
                        el.disabled = true;
                    });
                }

                // Render attached credit bills table under Payments tab
                renderTab10AttachedCreditBills(data);

                checkSettleVerification();
            });
    }

    let currentTab10AttachedBills = [];
    function renderTab10AttachedCreditBills(data) {
        const tbody = document.getElementById('tab10CreditBillsTbody');
        if (!tbody) return;

        tbody.innerHTML = '';
        currentTab10AttachedBills = data.credit_bills || [];

        if (currentTab10AttachedBills.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center; color:#888; padding:15px;">No credit bills attached to this route/delivery.</td></tr>';
            return;
        }

        const isReadOnly = (currentRouteStatus === 'Completed' || currentRouteStatus === 'Finalized');

        currentTab10AttachedBills.forEach(bill => {
            const grandTotal = parseFloat(bill.true_grand_total || bill.total_amount || 0);
            const amountPaid = parseFloat(bill.amount_paid || 0);
            const balanceDue = Math.max(0, grandTotal - amountPaid);
            
            let statusBadge = '<span style="font-size:10px; padding:2px 6px; border-radius:4px; font-weight:700; background:#fef3c7; color:#d97706;">Unpaid</span>';
            if (balanceDue <= 0.01) {
                statusBadge = '<span style="font-size:10px; padding:2px 6px; border-radius:4px; font-weight:700; background:#dcfce7; color:#16a34a;">Paid</span>';
            } else if (amountPaid > 0) {
                statusBadge = '<span style="font-size:10px; padding:2px 6px; border-radius:4px; font-weight:700; background:#e0f2fe; color:#0369a1;">Partially Paid</span>';
            }

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><strong>${escapeHtml(bill.customer_name || 'N/A')}</strong></td>
                <td><span style="font-family:monospace; font-weight:700; color:#0284c7;">${escapeHtml(bill.invoice_number)}</span></td>
                <td>${bill.invoice_date || '-'}</td>
                <td style="text-align:right; font-family:monospace;">Rs ${grandTotal.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                <td style="text-align:right; font-family:monospace; font-weight:700; color:${balanceDue > 0 ? '#dc2626' : '#16a34a'};">Rs ${balanceDue.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                <td style="text-align:center;">${statusBadge}</td>
                <td style="text-align:center;">
                    ${balanceDue > 0.01 && !isReadOnly ? `
                        <button type="button" onclick="openApplyCreditPaymentModal(${bill.id})" style="padding:4px 10px; background:#0284c7; color:#fff; border:none; border-radius:4px; font-size:11px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:4px;">
                            <i class="ph ph-currency-dollar"></i> Apply Pay
                        </button>
                    ` : `
                        <span style="font-size:11px; color:#94a3b8; font-style:italic;">${isReadOnly ? 'Read Only' : 'Settled'}</span>
                    `}
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function openApplyCreditPaymentModal(invoiceId) {
        const bill = currentTab10AttachedBills.find(b => parseInt(b.id) === parseInt(invoiceId));
        if (!bill) {
            alert("Credit bill details not found.");
            return;
        }

        const grandTotal = parseFloat(bill.true_grand_total || bill.total_amount || 0);
        const amountPaid = parseFloat(bill.amount_paid || 0);
        const balanceDue = Math.max(0, grandTotal - amountPaid);

        document.getElementById('acpInvoiceId').value = bill.id;
        document.getElementById('acpCustomerId').value = bill.customer_id;
        document.getElementById('acpCustName').innerText = bill.customer_name || 'N/A';
        document.getElementById('acpInvoiceNum').innerText = bill.invoice_number || 'N/A';
        document.getElementById('acpBalanceDue').innerText = 'Rs ' + balanceDue.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2});
        document.getElementById('acpAmount').value = balanceDue.toFixed(2);
        document.getElementById('acpAmount').max = balanceDue.toFixed(2);
        document.getElementById('acpPaymentMethod').value = 'Cash';
        document.getElementById('acpReference').value = '';
        document.getElementById('acpNotes').value = '';
        document.getElementById('acpChequeBank').value = '';
        document.getElementById('acpChequeNum').value = '';
        document.getElementById('acpChequeDate').value = '';

        toggleAcpPaymentFields();

        const modal = document.getElementById('applyCreditBillPaymentModal');
        if (modal) modal.style.display = 'flex';
    }

    function closeApplyCreditPaymentModal() {
        const modal = document.getElementById('applyCreditBillPaymentModal');
        if (modal) modal.style.display = 'none';
    }

    function toggleAcpPaymentFields() {
        const method = document.getElementById('acpPaymentMethod').value;
        const bankBox = document.getElementById('acpBankTransferBox');
        const chequeBox = document.getElementById('acpChequeBox');

        if (method === 'Bank Transfer') {
            bankBox.style.display = 'block';
            chequeBox.style.display = 'none';
        } else if (method === 'Cheque') {
            bankBox.style.display = 'none';
            chequeBox.style.display = 'block';
        } else {
            bankBox.style.display = 'none';
            chequeBox.style.display = 'none';
        }
    }

    function submitApplyCreditPayment(event) {
        event.preventDefault();

        const routeId = currentRouteId;
        const invoiceId = parseInt(document.getElementById('acpInvoiceId').value);
        const customerId = parseInt(document.getElementById('acpCustomerId').value);
        const amount = parseFloat(document.getElementById('acpAmount').value);
        const method = document.getElementById('acpPaymentMethod').value;
        const paymentDate = document.getElementById('acpPaymentDate').value;
        const reference = document.getElementById('acpReference').value;
        const assetAccountId = document.getElementById('acpAssetAccount').value;
        const chequeBank = document.getElementById('acpChequeBank').value;
        const chequeNum = document.getElementById('acpChequeNum').value;
        const chequeDate = document.getElementById('acpChequeDate').value;
        const notes = document.getElementById('acpNotes').value;

        if (isNaN(amount) || amount <= 0) {
            alert('Please enter a valid payment amount.');
            return;
        }

        if (method === 'Bank Transfer' && !assetAccountId) {
            alert('Please select a destination bank account for Bank Transfer.');
            return;
        }

        if (method === 'Cheque') {
            if (!chequeBank || !chequeNum || !chequeDate) {
                alert('Please fill in all Cheque details.');
                return;
            }
            if (!/^\d{6}$/.test(chequeNum)) {
                alert('Cheque number must be exactly 6 digits.');
                return;
            }
        }

        const btnSubmit = document.getElementById('btnSubmitCreditPayment');
        btnSubmit.disabled = true;

        const payload = {
            route_id: routeId,
            invoice_id: invoiceId,
            customer_id: customerId,
            amount: amount,
            payment_method: method,
            payment_date: paymentDate,
            reference: reference,
            asset_account_id: assetAccountId,
            cheque_bank: chequeBank,
            cheque_number: chequeNum,
            cheque_date: chequeDate,
            notes: notes
        };

        fetchSecure('<?= APP_URL ?>/RepTracking/api_apply_credit_bill_payment', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            btnSubmit.disabled = false;
            if (data.status === 'excess_warning') {
                const msg = data.message + 
                    "\n\n• Invoice Balance Due: Rs " + (data.balance_due || 0).toLocaleString() +
                    "\n• Bill Allocation: Rs " + (data.allocated_amount || 0).toLocaleString() +
                    "\n• Excess Customer Advance: Rs " + (data.excess_amount || 0).toLocaleString() +
                    "\n\nDo you want to confirm and keep the excess Rs " + (data.excess_amount || 0).toLocaleString() + " as customer advance credit?";
                if (confirm(msg)) {
                    payload.allow_excess_as_advance = true;
                    btnSubmit.disabled = true;
                    fetchSecure('<?= APP_URL ?>/RepTracking/api_apply_credit_bill_payment', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    })
                    .then(r => r.json())
                    .then(d2 => {
                        btnSubmit.disabled = false;
                        if (d2.status === 'success') {
                            alert(d2.message);
                            closeApplyCreditPaymentModal();
                            loadTab10Accounting(routeId);
                            loadTab8Reconciliation(routeId);
                        } else {
                            alert(d2.message || 'Failed to apply credit payment.');
                        }
                    })
                    .catch(err => {
                        btnSubmit.disabled = false;
                        alert('Error applying payment: ' + err.message);
                    });
                }
                return;
            }
            if (data.status === 'success') {
                alert(data.message);
                closeApplyCreditPaymentModal();
                loadTab10Accounting(routeId);
                loadTab8Reconciliation(routeId);
            } else {
                alert(data.message || 'Failed to apply credit payment.');
            }
        })
        .catch(err => {
            btnSubmit.disabled = false;
            alert('Error applying payment: ' + err.message);
        });
    }

    window.openApplyCreditPaymentModal = openApplyCreditPaymentModal;
    window.closeApplyCreditPaymentModal = closeApplyCreditPaymentModal;
    window.toggleAcpPaymentFields = toggleAcpPaymentFields;
    window.submitApplyCreditPayment = submitApplyCreditPayment;

    function applyFinalizeDefensiveGuard(routeId, delId) {
        const guardEl = document.getElementById('tab11GuardContainer');
        const contentEl = document.getElementById('tab11ContentContainer');
        if (!guardEl || !contentEl) return false;

        let isBlocked = false;
        let title = '';
        let desc = '';

        if (currentRouteStatus === 'Active') {
            isBlocked = true;
            title = 'Route Still Active';
            desc = 'Route summary and finalization options are not available until the representative has ended the route from their app.';
        }

        if (isBlocked) {
            guardEl.innerHTML = `
                <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 45px 20px; text-align: center; max-width: 580px; margin: 40px auto; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                    <div style="width: 60px; height: 60px; background: #fffbeb; color: #d97706; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto; font-size: 28px;">
                        <i class="ph ph-warning-circle"></i>
                    </div>
                    <h4 style="margin: 0 0 10px 0; font-size: 15px; font-weight: bold; color: #0f172a;">${title}</h4>
                    <p style="margin: 0; font-size: 12px; color: #64748b; line-height: 1.6;">${desc}</p>
                </div>
            `;
            guardEl.style.display = 'block';
            contentEl.style.display = 'none';
            return true;
        } else {
            guardEl.style.display = 'none';
            contentEl.style.display = 'block';
            return false;
        }
    }

    function loadTab11Finalize(routeId) {
        const rdata = document.getElementById('route_data_' + routeId);
        const delId = rdata ? rdata.getAttribute('data-delivery-id') : null;

        if (applyFinalizeDefensiveGuard(routeId, delId)) {
            return;
        }

        fetchSecure('<?= APP_URL ?>/RepTracking/api_get_delivery_details/' + (delId || 0) + '?route_id=' + routeId)
            .then(res => res.json())
            .then(data => {
                if (data.status !== 'success') return;
                currentDeliveryDetails = data;
                
                const delivery = data.delivery || {};
                const balancing = data.balancing || {};
                
                document.getElementById('sumRouteName').innerText = delivery.route_name || '--';
                document.getElementById('sumRepName').innerText = ((delivery.first_name || '') + ' ' + (delivery.last_name || '')).trim() || '--';
                document.getElementById('sumVehicleNumber').innerText = delivery.vehicle_number || '--';
                document.getElementById('sumDriverName').innerText = delivery.driver_name || '--';
                document.getElementById('sumPartnerName').innerText = delivery.partner_name || 'None';
                
                const startMeter = parseFloat(delivery.start_meter) || 0;
                const endMeter = parseFloat(delivery.end_meter) || 0;
                document.getElementById('sumStartMeter').innerText = startMeter.toLocaleString() + ' KM';
                document.getElementById('sumEndMeter').innerText = endMeter.toLocaleString() + ' KM';
                document.getElementById('sumDistanceTraveled').innerText = Math.max(0, endMeter - startMeter).toLocaleString() + ' KM';
                
                const cashSales = parseFloat(balancing.cash_sales) || 0;
                const chequeSales = parseFloat(balancing.cheque_sales) || 0;
                const bankSales = parseFloat(balancing.bank_sales) || 0;
                const creditSales = parseFloat(balancing.credit_sales) || 0;
                const totalSales = cashSales + chequeSales + bankSales + creditSales;
                
                document.getElementById('sumCashSales').innerText = 'Rs ' + cashSales.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                document.getElementById('sumChequeSales').innerText = 'Rs ' + chequeSales.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                document.getElementById('sumBankSales').innerText = 'Rs ' + bankSales.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                document.getElementById('sumCreditSales').innerText = 'Rs ' + creditSales.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                document.getElementById('sumTotalSales').innerText = 'Rs ' + totalSales.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                
                fetchSecure('<?= APP_URL ?>/RepTracking/api_get_route_expenses/' + routeId)
                    .then(r => r.json())
                    .then(eData => {
                        const rExp = (eData && eData.status === 'success') ? parseFloat(eData.total_route_expenses || 0) : 0;
                        const expEl = document.getElementById('sumRouteExpenses');
                        if (expEl) expEl.innerText = 'Rs ' + rExp.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    }).catch(err => {});
                
                const rawExpectedCash = parseFloat(balancing.raw_cash_collections || 0);
                const routeExpenses = parseFloat(balancing.collected_cash_expenses_total || 0);
                const expectedCash = Math.max(0, rawExpectedCash - routeExpenses);

                let actualCash = 0.0;
                let hasRecon = false;
                if (delivery.reconciliation_json) {
                    try {
                        const recon = JSON.parse(delivery.reconciliation_json);
                        if (recon && recon.actual_cash !== undefined) {
                            actualCash = parseFloat(recon.actual_cash || 0);
                            hasRecon = true;
                        }
                    } catch(e) {}
                }
                
                if (!hasRecon) {
                    let cashDenoms = {};
                    try {
                        if (delivery.cash_denominations) {
                            cashDenoms = JSON.parse(delivery.cash_denominations);
                        }
                    } catch(e) {}
                    
                    const denomList = [5000, 2000, 1000, 500, 100, 50, 20];
                    denomList.forEach(den => {
                        const cnt = parseInt(cashDenoms[den]) || 0;
                        actualCash += cnt * den;
                    });
                    actualCash += parseFloat(cashDenoms['coins']) || 0;
                }
                
                const variance = actualCash - expectedCash;
                
                document.getElementById('sumExpectedCash').innerText = 'Rs ' + expectedCash.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                document.getElementById('sumActualCash').innerText = 'Rs ' + actualCash.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                
                const varEl = document.getElementById('sumCashVariance');
                varEl.innerText = (variance >= 0 ? '+' : '') + 'Rs ' + variance.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                if (Math.abs(variance) < 0.01) {
                    varEl.style.color = '#166534';
                } else if (variance < 0) {
                    varEl.style.color = '#dc2626';
                } else {
                    varEl.style.color = '#ef6c00';
                }
                
                checkSettleVerification();
            });
    }

    function saveAccountingDraft() {
        if (!currentRouteId || !currentDeliveryDetails) return;

        const debitAccounts = {};
        const creditAccounts = {};
        document.querySelectorAll('.settle-de-select').forEach(sel => {
            const id = sel.getAttribute('data-id');
            const type = sel.getAttribute('data-type');
            const val = parseInt(sel.value);
            if (type === 'debit') { debitAccounts[id] = val; } else { creditAccounts[id] = val; }
        });

        // Also add the collection debit/credit accounts so they are preserved in delivery accounting json as draft
        document.querySelectorAll('.settle-payment-row').forEach(row => {
            const payId = parseInt(row.getAttribute('data-pay-id'));
            const debAcc = parseInt(row.querySelector('.settle-payment-debit').value);
            const credAcc = parseInt(row.querySelector('.settle-payment-credit').value);
            debitAccounts['pay_' + payId] = debAcc;
            creditAccounts['pay_' + payId] = credAcc;
        });

        const accountingData = {
            debit: debitAccounts,
            credit: creditAccounts
        };

        const deliveryId = currentDeliveryDetails.delivery.id;

        const updates = [];
        document.querySelectorAll('.settle-payment-row').forEach(row => {
            const payId = parseInt(row.getAttribute('data-pay-id'));
            const isVerified = row.querySelector('.settle-payment-chk').checked ? 1 : 0;
            const debAcc = parseInt(row.querySelector('.settle-payment-debit').value);
            const credAcc = parseInt(row.querySelector('.settle-payment-credit').value);
            const adjAmt = parseFloat(row.querySelector('.settle-payment-adj').value);
            const notes = row.querySelector('.settle-payment-notes').value;

            updates.push({
                id: payId,
                is_verified: isVerified,
                is_flagged: 0,
                adjusted_amount: adjAmt,
                verification_notes: notes,
                debit_account_id: debAcc,
                credit_account_id: credAcc
            });
        });

        // 1. Save collections verification
        fetchSecure('<?= APP_URL ?>/RepTracking/api_verify_collections', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ updates: updates })
        })
        .then(res => res.json())
        .then(colData => {
            if (colData.status !== 'success') {
                alert("Error saving collections: " + colData.message);
                return;
            }

            // 2. Save accounting entries json
            fetchSecure('<?= APP_URL ?>/RepTracking/api_save_accounting_entries', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    delivery_id: deliveryId, 
                    route_id: currentRouteId,
                    accounting_entries_json: accountingData 
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    alert("Accounting mappings and collection verification draft saved successfully!");
                    if (data.delivery_id) {
                        const rdata = document.getElementById('route_data_' + currentRouteId);
                        if (rdata) {
                            rdata.setAttribute('data-delivery-id', data.delivery_id);
                        }
                        if (currentDeliveryDetails && currentDeliveryDetails.delivery) {
                            currentDeliveryDetails.delivery.id = data.delivery_id;
                        }
                    }
                    onRouteDataChanged();
                } else {
                    alert("Error: " + data.message);
                }
            });
        });
    }

    function checkSettleVerification() {
        console.log("[SettleVerify] checkSettleVerification started.");
        const verifyStockCheck = document.getElementById('settleVerifyStock');
        let verifyStock = verifyStockCheck ? verifyStockCheck.checked : false;

        // Fallback: check if the global delivery details already have return stock saved
        if (!verifyStock && currentDeliveryDetails && currentDeliveryDetails.delivery && currentDeliveryDetails.delivery.return_stock_json !== null && currentDeliveryDetails.delivery.return_stock_json !== '') {
            console.log("[SettleVerify] Fallback: Found saved return stock in database. Auto-checking checkbox.");
            verifyStock = true;
            if (verifyStockCheck) {
                verifyStockCheck.checked = true;
            }
        }
        console.log("[SettleVerify] verifyStock state:", verifyStock);

        const btn = document.getElementById('settleSubmitBtn');
        const text = document.getElementById('settleStatusText');
        if (!btn || !text) return;

        if (currentRouteStatus === 'Completed' || currentRouteStatus === 'Finalized') {
            btn.disabled = true;
            btn.style.opacity = '0.5';
            btn.style.cursor = 'not-allowed';
            text.innerHTML = '<span style="color:#2e7d32; font-weight:bold;">Route Finalized & Settled</span>';
            return;
        }

        let allCollectionsApproved = true;
        let pendingOrFlaggedCount = 0;
        
        // Check collections verification from Cash/Cheques Posting DOM checkboxes
        document.querySelectorAll('.settle-payment-chk').forEach(chk => {
            if (!chk.checked) {
                allCollectionsApproved = false;
                pendingOrFlaggedCount++;
            }
        });

        if (allCollectionsApproved && verifyStock) {
            btn.disabled = false;
            btn.style.opacity = '1';
            btn.style.cursor = 'pointer';
            text.innerHTML = '<span style="color:#2e7d32; font-weight:bold;">Verification Complete!</span> Ready to settle balancing.';
        } else {
            btn.disabled = true;
            btn.style.opacity = '0.5';
            btn.style.cursor = 'not-allowed';
            
            let msg = '';
            if (!allCollectionsApproved) {
                msg += `Please approve/verify all collections under Cash/Cheques Posting tab (${pendingOrFlaggedCount} remaining). `;
            }
            if (!verifyStock) {
                msg += 'Please verify Return Stock checkbox under Return Stock tab.';
            }
            text.innerHTML = `<span style="color:#dc2626; font-weight:bold;">Locked:</span> ${msg}`;
        }
    }

    let settleActiveDeTab = 'collections';
    function switchSettleDeTab(tab) {
        settleActiveDeTab = tab;
        document.getElementById('settleDeTabCollectionsBtn').classList.toggle('active', tab === 'collections');
        document.getElementById('settleDeTabSalesBtn').classList.toggle('active', tab === 'sales');
        document.getElementById('settleDeCollectionsContainer').style.display = tab === 'collections' ? 'block' : 'none';
        document.getElementById('settleDeSalesContainer').style.display = tab === 'sales' ? 'block' : 'none';
    }

    function renderSettleDeAccountSelect(id, type, selectedCode) {
        let optionsHtml = '';
        globalAllAccounts.forEach(acc => {
            let isSel = acc.account_code === selectedCode ? 'selected' : '';
            optionsHtml += `<option value="${acc.id}" ${isSel}>${acc.account_code} - ${acc.account_name}</option>`;
        });
        return `<select class="settle-de-select" data-id="${id}" data-type="${type}" style="padding:4px 8px; font-size:12px; border-radius:4px; border:1px solid #ccc; width:100%;">${optionsHtml}</select>`;
    }

    function renderSettleDeAccountSelectForPayment(id, type, selectedId, isReadOnly) {
        let optionsHtml = '';
        globalAllAccounts.forEach(acc => {
            let isSel = String(acc.id) === String(selectedId) ? 'selected' : '';
            optionsHtml += `<option value="${acc.id}" ${isSel}>${acc.account_code} - ${acc.account_name}</option>`;
        });
        return `<select class="settle-payment-${type}" data-id="${id}" data-type="${type}" style="padding:4px 8px; font-size:11px; border-radius:4px; border:1px solid #cbd5e1; width:100%;" ${isReadOnly ? 'disabled' : ''}>${optionsHtml}</select>`;
    }

    function renderSettleDoubleEntries() {
        const colContainer = document.getElementById('settleDeCollectionsContainer');
        const salesContainer = document.getElementById('settleDeSalesContainer');
        
        colContainer.innerHTML = '';
        salesContainer.innerHTML = '';

        if (!currentDeliveryDetails) return;

        const payments = currentDeliveryDetails.balancing.payments || [];
        const invoices = currentDeliveryDetails.invoices || [];

        // 1. Collections
        if (payments.length === 0) {
            colContainer.innerHTML = '<p style="color:#888; text-align:center;">No payments logged on this trip.</p>';
        } else {
            let tableHTML = `
                <div style="border: 0.5px solid var(--c-separator); border-radius: var(--r-md); background: var(--c-surface); overflow: hidden;">
                    <table class="data-table" style="margin-top:0;">
                        <thead>
                            <tr style="background:var(--c-surface2);">
                                <th style="text-align:left; width:22%;">Customer / Pay</th>
                                <th style="text-align:right; width:12%;">Collected</th>
                                <th style="text-align:center; width:8%;">Approve</th>
                                <th style="text-align:left; width:20%;">Debit Account</th>
                                <th style="text-align:left; width:20%;">Credit Account</th>
                                <th style="text-align:right; width:10%;">Adjusted</th>
                                <th style="text-align:left; width:10%;">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            payments.forEach(p => {
                const isVerified = parseInt(p.is_verified) === 1;
                const isReadOnly = (currentRouteStatus === 'Completed' || currentRouteStatus === 'Finalized');
                const amt = parseFloat(p.amount);
                const adjustedVal = p.adjusted_amount !== null ? p.adjusted_amount : p.amount;

                let defaultDebitCode = '1000'; // Cash
                if (p.payment_method === 'Cheque') { defaultDebitCode = '1010'; }
                else if (p.payment_method === 'Bank Transfer') { defaultDebitCode = '1605'; }

                const selectedDebit = p.debit_account_id || getAccountIdByCode(defaultDebitCode);
                const selectedCredit = p.credit_account_id || getAccountIdByCode('1200');

                tableHTML += `
                    <tr class="settle-payment-row" data-pay-id="${p.id}" style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:6px 4px;">
                            <strong>${p.customer_name}</strong><br>
                            <span style="font-size:10px; color:#64748b;">${p.payment_method} ${p.cheque_number ? '(' + p.cheque_number + ')' : (p.reference ? '(' + p.reference + ')' : '')}</span>
                            ${isReadOnly ? '<br><span style="font-size:10px; color:#2e7d32; font-weight:bold;">Posted to GL</span>' : ''}
                        </td>
                        <td style="padding:6px 4px; text-align:right; font-family:monospace; font-weight:bold;">
                            Rs ${amt.toFixed(2)}
                        </td>
                        <td style="padding:6px 4px; text-align:center;">
                            <input type="checkbox" class="settle-payment-chk" value="${p.id}" 
                                   ${(isVerified || isReadOnly) ? 'checked' : ''} ${isReadOnly ? 'disabled' : ''} 
                                   onchange="checkSettleVerification(); updateSidebarProgress();"
                                   style="width:16px; height:16px; cursor:${isReadOnly ? 'not-allowed' : 'pointer'};" />
                        </td>
                        <td style="padding:6px 4px;">
                            ${renderSettleDeAccountSelectForPayment('pay_' + p.id, 'debit', selectedDebit, isReadOnly)}
                        </td>
                        <td style="padding:6px 4px;">
                            ${renderSettleDeAccountSelectForPayment('pay_' + p.id, 'credit', selectedCredit, isReadOnly)}
                        </td>
                        <td style="padding:6px 4px; text-align:center;">
                            <input type="number" step="0.01" min="0" class="settle-payment-adj" value="${parseFloat(adjustedVal).toFixed(2)}"
                                   ${isReadOnly ? 'disabled' : ''}
                                   style="width:80px; padding:3px; border:1px solid #cbd5e1; border-radius:4px; text-align:right; font-family:monospace; font-size:11px;" />
                        </td>
                        <td style="padding:6px 4px; text-align:center;">
                            <input type="text" class="settle-payment-notes" value="${p.verification_notes || ''}" placeholder="Notes"
                                   ${isReadOnly ? 'disabled' : ''}
                                   style="width:100px; padding:3px; border:1px solid #cbd5e1; border-radius:4px; font-size:11px;" />
                        </td>
                    </tr>
                `;
            });

            tableHTML += `
                        </tbody>
                    </table>
                </div>
            `;
            colContainer.innerHTML = tableHTML;
        }

        // 2. Sales
        const deliveredInvoices = invoices.filter(inv => inv.delivery_status === 'Delivered');
        if (deliveredInvoices.length === 0) {
            salesContainer.innerHTML = '<p style="color:#888; text-align:center;">No delivered sales invoices on this trip.</p>';
        } else {
            deliveredInvoices.forEach(inv => {
                salesContainer.innerHTML += `
                    <div style="display:flex; justify-content:space-between; align-items:center; background:#fafafa; border:1px solid #eee; padding:10px; margin-bottom:8px; border-radius:6px; flex-wrap:wrap; gap:10px;">
                        <div style="flex:1; min-width:200px;">
                            <label style="display:flex; align-items:center; gap:8px; font-weight:bold;">
                                <input type="checkbox" class="settle-invoice-chk" value="${inv.id}" checked>
                                ${inv.invoice_number} (${inv.customer_name})
                            </label>
                        </div>
                        <div style="font-weight:bold; color:#0066cc;">Rs ${parseFloat(inv.true_grand_total).toFixed(2)}</div>
                        <div style="display:flex; gap:10px; flex:2;">
                            <div style="flex:1;">
                                <span style="font-size:9px; color:#666;">Debit Account (AR)</span>
                                ${renderSettleDeAccountSelect('inv_' + inv.id, 'debit', '1090')}
                            </div>
                            <div style="flex:1;">
                                <span style="font-size:9px; color:#666;">Credit Account (Sales)</span>
                                ${renderSettleDeAccountSelect('inv_' + inv.id, 'credit', '3000')}
                            </div>
                        </div>
                    </div>
                `;
            });
        }
    }

    function submitFinalSettle() {
        if (!currentDeliveryDetails || !currentDeliveryDetails.delivery) {
            alert("Error: Delivery details not loaded.");
            return;
        }

        const vehicle = currentDeliveryDetails.delivery.vehicle_number || document.getElementById('settleDaVehicle').value;
        const driver = currentDeliveryDetails.delivery.driver_name || document.getElementById('settleDaDriver').value;
        const partner = currentDeliveryDetails.delivery.partner_name || document.getElementById('settleDaPartner').value;

        if (!vehicle || vehicle === 'Pending Vehicle') { alert("Please select a Vehicle Number."); return; }
        if (!driver) { alert("Please select a Driver Name."); return; }

        if (!confirm("Are you sure you want to FINALIZE and SETTLE this delivery route?\n\nThis will post all selected collections to GL and update inventory.")) {
            return;
        }

        const d = document.getElementById('route_data_' + currentRouteId);
        const delId = d.getAttribute('data-delivery-id');

        const selectedPaymentIds = [];
        document.querySelectorAll('.settle-payment-chk:checked').forEach(cb => {
            selectedPaymentIds.push(parseInt(cb.value));
        });

        const selectedInvoiceIds = [];
        document.querySelectorAll('.settle-invoice-chk:checked').forEach(cb => {
            selectedInvoiceIds.push(parseInt(cb.value));
        });

        const debitAccounts = {};
        const creditAccounts = {};
        document.querySelectorAll('.settle-de-select').forEach(sel => {
            const id = sel.getAttribute('data-id');
            const type = sel.getAttribute('data-type');
            const val = parseInt(sel.value);
            if (type === 'debit') { debitAccounts[id] = val; } else { creditAccounts[id] = val; }
        });
        document.querySelectorAll('.settle-payment-debit').forEach(sel => {
            const id = sel.getAttribute('data-id');
            const val = parseInt(sel.value);
            debitAccounts[id] = val;
        });
        document.querySelectorAll('.settle-payment-credit').forEach(sel => {
            const id = sel.getAttribute('data-id');
            const val = parseInt(sel.value);
            creditAccounts[id] = val;
        });

        const returnedItems = [];
        document.querySelectorAll('.actual-returned-input').forEach(input => {
            returnedItems.push({
                item_name: input.getAttribute('data-name'),
                item_id: parseInt(input.getAttribute('data-item-id') || 0),
                variation_option_id: parseInt(input.getAttribute('data-var-id') || 0),
                loaded_qty: parseInt(input.getAttribute('data-loaded') || 0),
                delivered_qty: parseInt(input.getAttribute('data-delivered') || 0),
                actual_returned_qty: parseInt(input.value || 0)
            });
        });

        const btn = document.getElementById('settleSubmitBtn');
        btn.disabled = true;
        btn.innerText = 'Settling Route... ';

        fetchSecure('<?= APP_URL ?>/RepTracking/finalize', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                delivery_id: parseInt(delId),
                selected_payment_ids: selectedPaymentIds,
                selected_invoice_ids: selectedInvoiceIds,
                debit_accounts: debitAccounts,
                credit_accounts: creditAccounts,
                returned_items: returnedItems,
                vehicle_number: vehicle,
                driver_name: driver,
                partner_name: partner
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                alert("Settle balancing successfully completed! Route marked as Completed.");
                window.shouldReloadOnClose = true;
                openBalancingReportModal(delId);
            } else {
                alert("Error: " + data.message);
                btn.disabled = false;
                btn.innerText = '<i class="ph ph-scales"></i> Settle Balancing & Finalize Route';
                checkSettleVerification();
            }
        });
    }

    window.shouldReloadOnClose = false;

    function openBalancingReportModal(delId) {
        const modal = document.getElementById('balancingReportModal');
        const iframe = document.getElementById('balancingReportIframe');
        if (modal && iframe) {
            iframe.src = '<?= APP_URL ?>/RepTracking/balancing_report/' + delId;
            modal.style.display = 'flex';
        }
    }

    function closeBalancingReportModal() {
        const modal = document.getElementById('balancingReportModal');
        const iframe = document.getElementById('balancingReportIframe');
        if (modal && iframe) {
            modal.style.display = 'none';
            iframe.src = 'about:blank';
        }
        if (window.shouldReloadOnClose) {
            window.shouldReloadOnClose = false;
            window.location.reload();
        }
    }

    function printBalancingReportFromModal() {
        const iframe = document.getElementById('balancingReportIframe');
        if (iframe && iframe.contentWindow) {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        }
    }

    function printBalancingReport() {
        const d = document.getElementById('route_data_' + currentRouteId);
        const delId = d ? d.getAttribute('data-delivery-id') : null;
        if (delId) { openBalancingReportModal(delId); }
    }

    function printLoadingSheetSpreadsheet() {
        const d = document.getElementById('route_data_' + currentRouteId);
        const delId = d ? d.getAttribute('data-delivery-id') : null;
        if (delId) { window.open('<?= APP_URL ?>/RepTracking/spreadsheet/' + delId, '_blank'); }
    }

    function exportCSV() {
        const d = document.getElementById('route_data_' + currentRouteId);
        const delId = d ? d.getAttribute('data-delivery-id') : null;
        if (delId) { window.location.href = '<?= APP_URL ?>/RepTracking/export_csv/' + delId; }
    }

    function getDataTypeFromStatus(status) {
        if (status === 'Active') return 'active';
        if (status === 'Pending GL') return 'pending_gl';
        if (status === 'Adjustments') return 'adjustments';
        if (status === 'Loading') return 'loading';
        if (status === 'Variance Adjustment') return 'variance';
        if (status === 'Finalizing' || status === 'Delivery Arranged') return 'finalizing';
        return 'completed';
    }

    function advanceRouteStatus(targetStatus) {
        if (!confirm(`Are you sure you want to advance this route to "${targetStatus}" stage?`)) {
            return;
        }
        fetchSecure('<?= APP_URL ?>/RepTracking/api_update_route_status', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ route_id: currentRouteId, status: targetStatus })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                alert(`Route advanced to ${targetStatus}`);
                const filterType = getDataTypeFromStatus(targetStatus);
                window.location.href = window.location.pathname + `?route_id=${currentRouteId}&filter=${filterType}`;
            } else {
                if (data.show_override) {
                    if (confirm(data.message + "\n\nDo you want to override this block and force advance the status?")) {
                        fetchSecure('<?= APP_URL ?>/RepTracking/api_update_route_status', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ route_id: currentRouteId, status: targetStatus, admin_override: true })
                        })
                        .then(res2 => res2.json())
                        .then(data2 => {
                            if (data2.status === 'success') {
                                alert(`Route advanced to ${targetStatus} (via Override)`);
                                const filterType = getDataTypeFromStatus(targetStatus);
                                window.location.href = window.location.pathname + `?route_id=${currentRouteId}&filter=${filterType}`;
                            } else {
                                alert("Error: " + data2.message);
                            }
                        });
                    }
                } else {
                    alert("Error: " + data.message);
                }
            }
        });
    }

    function redirectToAddInvoice() {
        if (currentRouteId) {
            window.location.href = '<?= APP_URL ?>/sales/create?type=sales_order&route_id=' + currentRouteId + '&back_url=' + encodeURIComponent(window.location.href);
        }
    }

    function printRouteInvoices() {
        if (currentRouteId) {
            window.open('<?= APP_URL ?>/RepTracking/print_route_invoices/' + currentRouteId, '_blank');
        }
    }

    function printRouteInvoicesSummary() {
        if (currentRouteId) {
            window.open('<?= APP_URL ?>/RepTracking/print_route_invoices_summary/' + currentRouteId, '_blank');
        }
    }

    function printReturnStock() {
        if (currentRouteId) {
            window.open('<?= APP_URL ?>/RepTracking/print_return_stock/' + currentRouteId, '_blank');
        }
    }

    function openInvoiceSlider(invoiceId) {
        const backdrop = document.getElementById('invoiceSliderBackdrop');
        const iframe = document.getElementById('invoiceIframe');
        iframe.src = 'about:blank';
        setTimeout(() => {
            iframe.src = '<?= APP_URL ?>/sales/show/' + invoiceId + '?hide_buttons=1';
        }, 50);
        backdrop.style.display = 'flex';
    }

    function closeInvoiceSlider() {
        document.getElementById('invoiceSliderBackdrop').style.display = 'none';
        document.getElementById('invoiceIframe').src = 'about:blank';
    }
    function deleteSalesOrder() {
        const invoiceId = document.getElementById('btnDeleteInvoice').getAttribute('data-invoice-id');
        if (!invoiceId) return;
        if (!confirm("Are you sure you want to delete this Sales Order? This will release reserved stock back to inventory and cannot be undone.")) {
            return;
        }
        fetchSecure('<?= APP_URL ?>/RepTracking/api_delete_sales_order/' + invoiceId, {
            method: 'POST'
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                alert("Success: " + data.message);
                closeInvoiceSlider();
                loadRouteDetails(currentRouteId);
            } else {
                alert("Error: " + data.message);
            }
        });
    }
    // --- GPS Path Map Handlers ---
    function openMapModal() {
        document.getElementById('mapModalBackdrop').style.display = 'flex';
        loadRoutePath(currentRouteId);
    }

    function closeMapModal() {
        document.getElementById('mapModalBackdrop').style.display = 'none';
    }

    function initRoutePathMap() {
        if (routeMap !== null) return;
        routeMap = L.map('routePathMap').setView([7.8731, 80.7718], 8);
        L.tileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            attribution: '&copy; Google Maps',
            maxZoom: 20
        }).addTo(routeMap);
    }

    function clearRoutePathMap() {
        routeMapLayers.forEach(layer => routeMap.removeLayer(layer));
        routeMapLayers = [];
    }

    // ─── Inline Tab 1 Map Functions ─────────────────────────────────────────
    function initTab1Map() {
        if (tab1Map !== null) return;
        const el = document.getElementById('tab1RoutePathMap');
        if (!el || typeof L === 'undefined') return;
        initMapIcons();
        tab1Map = L.map('tab1RoutePathMap').setView([7.8731, 80.7718], 8);
        L.tileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            attribution: '&copy; Google Maps',
            maxZoom: 20
        }).addTo(tab1Map);
    }

    function createCustomCircleIcon(wp) {
        const type = wp.type || 'default';
        let shopName = wp.customer_name || '';
        if (!shopName && (type === 'invoice' || type === 'unproductive')) {
            shopName = wp.detail || wp.label || wp.name || 'Location';
        } else if (!shopName) {
            shopName = wp.label || wp.name || 'Location';
        }
        
        let iconClass = 'ph ph-map-pin';
        if (type === 'start') iconClass = 'ph ph-flag';
        else if (type === 'end') iconClass = 'ph ph-flag-checkered';
        else if (type === 'invoice') iconClass = 'ph ph-receipt';
        else if (type === 'unproductive') iconClass = 'ph ph-x-circle';

        const html = `
            <div class="custom-circle-pin-container">
                <div class="pin-shop-badge" title="${shopName.replace(/"/g, '&quot;')}">${shopName}</div>
                <div class="pin-circle pin-type-${type}">
                    <i class="${iconClass}"></i>
                </div>
            </div>
        `;

        return L.divIcon({
            html: html,
            className: 'custom-leaflet-div-icon',
            iconSize: [140, 60],
            iconAnchor: [70, 56],
            popupAnchor: [0, -56]
        });
    }

    function createMapPopupCardHtml(wp) {
        let badgeBg = '#dbeafe';
        let badgeColor = '#1e40af';
        let badgeText = 'Invoice Issued';
        let iconClass = 'ph ph-receipt';

        if (wp.type === 'start') {
            badgeBg = '#d1fae5'; badgeColor = '#065f46'; badgeText = 'Route Started'; iconClass = 'ph ph-flag';
        } else if (wp.type === 'end') {
            badgeBg = '#fee2e2'; badgeColor = '#991b1b'; badgeText = 'Route End'; iconClass = 'ph ph-flag-checkered';
        } else if (wp.type === 'unproductive') {
            badgeBg = '#fef3c7'; badgeColor = '#92400e'; badgeText = 'Unproductive Visit'; iconClass = 'ph ph-x-circle';
        }

        let shopTitle = wp.customer_name || '';
        if (!shopTitle && (wp.type === 'invoice' || wp.type === 'unproductive')) {
            shopTitle = wp.detail || wp.label || wp.name || 'Location';
        } else if (!shopTitle) {
            shopTitle = wp.label || wp.name || 'Location';
        }

        let desc = wp.detail || wp.description || '';
        if (wp.invoice_number) {
            desc = 'Invoice #' + wp.invoice_number;
        }

        const amountStr = wp.amount ? `<div style="font-size:13px; font-weight:700; color:#2563eb; margin-top:6px; margin-bottom:4px;">Rs. ${parseFloat(wp.amount).toLocaleString('en-US', {minimumFractionDigits:2})}</div>` : '';

        return `
            <div class="map-popup-card">
                <div class="map-popup-header">
                    <span class="map-popup-badge" style="background:${badgeBg}; color:${badgeColor};">${badgeText}</span>
                    <span style="font-size:14px; color:#64748b;"><i class="${iconClass}"></i></span>
                </div>
                <div class="map-popup-title">${shopTitle}</div>
                ${desc ? `<div class="map-popup-desc">${desc}</div>` : ''}
                ${amountStr}
                <div class="map-popup-footer">
                    <span><i class="ph ph-clock"></i> ${wp.time || 'N/A'}</span>
                </div>
            </div>
        `;
    }

    window.focusTab1Waypoint = function(index, lat, lng) {
        if (!tab1Map) return;
        
        tab1Map.flyTo([lat, lng], 16, {
            animate: true,
            duration: 0.8
        });

        setTimeout(() => {
            if (window._tab1Markers && window._tab1Markers[index]) {
                window._tab1Markers[index].openPopup();
            }
        }, 250);

        const cards = document.querySelectorAll('.activity-log-item');
        cards.forEach((c, idx) => {
            if (idx === index) {
                c.style.borderColor = '#2563eb';
                c.style.boxShadow = '0 0 0 2px rgba(37, 99, 235, 0.25)';
                c.style.background = '#f8fafc';
            } else {
                c.style.borderColor = '#e2e8f0';
                c.style.boxShadow = '0 1px 3px rgba(0,0,0,0.02)';
                c.style.background = '#ffffff';
            }
        });
    };

    function clearTab1Map() {
        tab1MapLayers.forEach(layer => tab1Map.removeLayer(layer));
        tab1MapLayers = [];
    }

    function loadTab1RoutePath(routeId) {
        const emptyOverlay = document.getElementById('tab1MapEmptyOverlay');
        const emptyMsg = document.getElementById('tab1MapEmptyMsg');
        const stepList = document.getElementById('tab1PathStepList');
        const stepEmpty = document.getElementById('tab1PathStepEmpty');
        if (!emptyOverlay) return;

        emptyOverlay.style.display = 'flex';
        if (emptyMsg) emptyMsg.innerText = 'Loading GPS path...';
        if (stepList) stepList.style.display = 'none';
        if (stepEmpty) stepEmpty.style.display = 'block';

        initTab1Map();
        if (tab1Map) clearTab1Map();

        fetch('<?= APP_URL ?>/RepTracking/api_get_route_path/' + routeId)
            .then(r => r.json())
            .then(data => {
                if (data.status !== 'success' || !data.path) {
                    if (emptyMsg) emptyMsg.innerText = data.message || 'No GPS path available for this route.';
                    return;
                }
                renderTab1RoutePath(data.path);
            })
            .catch(err => {
                console.error('[Tab1 Map] Fetch error:', err);
                if (emptyMsg) emptyMsg.innerText = 'Failed to load GPS path.';
            });
    }

    function isValidLatLng(lat, lng) {
        if (lat === null || lat === undefined || lng === null || lng === undefined) return false;
        const nLat = parseFloat(lat);
        const nLng = parseFloat(lng);
        if (isNaN(nLat) || isNaN(nLng)) return false;
        if (nLat === 0 && nLng === 0) return false;
        // Katunayake dummy default fallback check (7.1824, 79.8801)
        if (Math.abs(nLat - 7.1824) < 0.001 && Math.abs(nLng - 79.8801) < 0.001) return false;
        // Sri Lanka bounding box (5.5 - 10.5 N, 79.0 - 82.5 E)
        if (nLat < 5.5 || nLat > 10.5 || nLng < 79.0 || nLng > 82.5) return false;
        return true;
    }

    function renderTab1RoutePath(path) {
        const emptyOverlay = document.getElementById('tab1MapEmptyOverlay');
        const stepList = document.getElementById('tab1PathStepList');
        const stepEmpty = document.getElementById('tab1PathStepEmpty');
        const stepOl = document.getElementById('tab1PathStepOl');
        const pointCountEl = document.getElementById('tab1PathPointCount');
        const mapNameEl = document.getElementById('tab1MapRouteName');
        const mapPointCountEl = document.getElementById('tab1MapPointCount');

        const rawWps = path.waypoints || [];
        const wps = rawWps.filter(wp => isValidLatLng(wp.lat, wp.lng));

        // Sort waypoints: Day Start first, Day End last, middle activities chronologically ascending
        const parseWpTime = (item) => {
            if (!item.time || item.time === 'N/A') return 0;
            const t = Date.parse(item.time);
            return isNaN(t) ? 0 : t;
        };

        wps.sort((a, b) => {
            if (a.type === 'start' && b.type !== 'start') return -1;
            if (b.type === 'start' && a.type !== 'start') return 1;
            if (a.type === 'end' && b.type !== 'end') return 1;
            if (b.type === 'end' && a.type !== 'end') return -1;

            const tA = parseWpTime(a);
            const tB = parseWpTime(b);
            return tA - tB;
        });

        if (mapNameEl) mapNameEl.innerText = path.route_name || '';
        if (mapPointCountEl) mapPointCountEl.innerText = wps.length ? `(${wps.length} pts)` : '';
        if (pointCountEl) pointCountEl.innerText = wps.length ? `${wps.length} points` : '';

        if (stepOl) stepOl.innerHTML = '';

        if (wps.length === 0) {
            if (emptyOverlay) emptyOverlay.style.display = 'flex';
            const emptyMsg = document.getElementById('tab1MapEmptyMsg');
            if (emptyMsg) emptyMsg.innerText = 'No GPS points recorded for this route.';
            if (stepList) stepList.style.display = 'none';
            if (stepEmpty) stepEmpty.style.display = 'block';
            if (tab1Map) setTimeout(() => tab1Map.invalidateSize(), 150);
            return;
        }

        if (emptyOverlay) emptyOverlay.style.display = 'none';
        if (stepList) { stepList.style.display = 'block'; }
        if (stepEmpty) stepEmpty.style.display = 'none';

        window._tab1Markers = [];
        const latlngs = [];
        wps.forEach((wp, index) => {
            const latlng = [wp.lat, wp.lng];
            latlngs.push(latlng);
            
            const circleIcon = createCustomCircleIcon(wp);
            const popupContent = createMapPopupCardHtml(wp);
            const marker = L.marker(latlng, { icon: circleIcon }).addTo(tab1Map);
            marker.bindPopup(popupContent, { offset: [0, -10] });
            tab1MapLayers.push(marker);
            window._tab1Markers.push(marker);

            if (stepOl) {
                let cardIcon = '<i class="ph ph-receipt" style="font-size:18px; color:#2563eb;"></i>';
                let cardBg = '#eff6ff';
                let badgeBg = '#dbeafe';
                let badgeColor = '#1e40af';
                let typeTitle = 'Invoice Issued';

                if (wp.type === 'start') {
                    cardIcon = '<i class="ph ph-flag" style="font-size:18px; color:#059669;"></i>';
                    cardBg = '#ecfdf5'; badgeBg = '#d1fae5'; badgeColor = '#065f46';
                    typeTitle = 'Route Started';
                } else if (wp.type === 'end') {
                    cardIcon = '<i class="ph ph-flag-checkered" style="font-size:18px; color:#dc2626;"></i>';
                    cardBg = '#fef2f2'; badgeBg = '#fee2e2'; badgeColor = '#991b1b';
                    typeTitle = 'Route End';
                } else if (wp.type === 'unproductive') {
                    cardIcon = '<i class="ph ph-x-circle" style="font-size:18px; color:#d97706;"></i>';
                    cardBg = '#fffbeb'; badgeBg = '#fef3c7'; badgeColor = '#92400e';
                    typeTitle = 'Unproductive Visit';
                }

                const wpTitle = wp.customer_name || (wp.type === 'invoice' || wp.type === 'unproductive' ? wp.detail || wp.label : wp.label || wp.name || '');
                let wpDesc = wp.detail || wp.description || '';
                if (wp.invoice_number) {
                    wpDesc = 'Invoice #' + wp.invoice_number;
                }
                const amountStr = wp.amount ? ` • <strong>Rs. ${parseFloat(wp.amount).toLocaleString('en-US', {minimumFractionDigits:2})}</strong>` : '';

                stepOl.innerHTML += `
                    <div class="activity-log-item" onclick="focusTab1Waypoint(${index}, ${wp.lat}, ${wp.lng})" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:14px; padding:12px 14px; display:flex; align-items:center; gap:12px; box-shadow:0 1px 3px rgba(0,0,0,0.02); transition:all 0.15s;">
                        <div style="width:40px; height:40px; border-radius:12px; background:${cardBg}; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            ${cardIcon}
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div style="display:flex; align-items:center; justify-content:space-between; gap:8px; margin-bottom:2px;">
                                <span style="font-size:13px; font-weight:700; color:#0f172a; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${wpTitle}</span>
                                <span style="font-size:10px; font-weight:700; padding:2px 8px; border-radius:12px; background:${badgeBg}; color:${badgeColor}; white-space:nowrap; flex-shrink:0;">${typeTitle}</span>
                            </div>
                            <div style="font-size:11px; color:#64748b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                ${wpDesc}${amountStr}
                            </div>
                            <div style="font-size:10px; font-family:var(--f-mono); color:#94a3b8; margin-top:3px; display:flex; align-items:center; gap:4px;">
                                <i class="ph ph-clock" style="font-size:11px;"></i> ${wp.time}
                            </div>
                        </div>
                    </div>
                `;
            }
        });

        if (latlngs.length > 1) {
            const poly = L.polyline(latlngs, { color: '#2563eb', weight: 3.5, opacity: 0.8 }).addTo(tab1Map);
            tab1MapLayers.push(poly);
            window._tab1MapBounds = poly.getBounds();
            // Invalidate first, then fit — ensures container has correct dimensions
            setTimeout(() => {
                tab1Map.invalidateSize();
                tab1Map.fitBounds(window._tab1MapBounds, { padding: [60, 60], maxZoom: 15 });
            }, 150);
            setTimeout(() => {
                tab1Map.invalidateSize();
                tab1Map.fitBounds(window._tab1MapBounds, { padding: [60, 60], maxZoom: 15 });
            }, 500);
        } else if (latlngs.length === 1) {
            window._tab1MapBounds = null;
            setTimeout(() => {
                tab1Map.invalidateSize();
                tab1Map.setView(latlngs[0], 14);
            }, 150);
        }
    }

    function loadRoutePath(routeId) {
        console.log('[Map Tracking] Loading route path for routeId:', routeId);
        document.getElementById('mapEmptyOverlay').style.display = 'flex';
        document.getElementById('mapEmptyOverlay').innerText = 'Loading route path...';
        document.getElementById('pathStepList').style.display = 'none';

        initRoutePathMap();
        clearRoutePathMap();

        fetch('<?= APP_URL ?>/RepTracking/api_get_route_path/' + routeId)
            .then(r => {
                console.log('[Map Tracking] Response HTTP Status:', r.status);
                return r.json();
            })
            .then(data => {
                console.log('[Map Tracking] Received payload:', data);
                if (data.status !== 'success' || !data.path) {
                    const errMsg = data.message || 'Could not load route path.';
                    console.warn('[Map Tracking] Failed to load route path:', errMsg);
                    document.getElementById('mapEmptyOverlay').innerText = errMsg;
                    return;
                }
                renderRoutePath(data.path);
            })
            .catch(err => {
                console.error('[Map Tracking] Fetch exception:', err);
                document.getElementById('mapEmptyOverlay').innerText = 'Failed to load route path.';
            });
    }

    function renderRoutePath(path) {
        console.log('[Map Tracking] Rendering route path waypoints:', path);
        const rawWps = path.waypoints || [];
        const wps = rawWps.filter(wp => isValidLatLng(wp.lat, wp.lng));

        document.getElementById('pathPointCount').innerText = wps.length ? `(${wps.length} points)` : '(no GPS)';
        document.getElementById('modalRouteName').innerText = path.route_name || '';

        const stepOl = document.getElementById('pathStepOl');
        stepOl.innerHTML = '';

        if (wps.length === 0) {
            console.warn('[Map Tracking] 0 waypoints found for route.');
            document.getElementById('mapEmptyOverlay').style.display = 'flex';
            document.getElementById('mapEmptyOverlay').innerHTML = 'No GPS points recorded for this route.';
            document.getElementById('pathStepList').style.display = 'none';
            setTimeout(() => routeMap.invalidateSize(), 100);
            return;
        }

        document.getElementById('mapEmptyOverlay').style.display = 'none';
        document.getElementById('pathStepList').style.display = 'block';

        const latlngs = [];
        wps.forEach((wp) => {
            const latlng = [wp.lat, wp.lng];
            latlngs.push(latlng);

            let icon = pathBlueIcon;
            let stepClass = 'path-step-invoice';
            if (wp.type === 'start') { icon = pathGreenIcon; stepClass = 'path-step-start'; }
            else if (wp.type === 'end') { icon = pathRedIcon; stepClass = 'path-step-end'; }
            else if (wp.type === 'unproductive') { icon = pathOrangeIcon; stepClass = 'path-step-unproductive'; }

            const wpTitle = wp.label || wp.name || '';
            const wpDesc = wp.detail || wp.description || '';

            const marker = L.marker(latlng, { icon: icon }).addTo(routeMap);
            marker.bindPopup(`<strong>${wpTitle}</strong><br>${wpDesc}<br><span style="font-size:10px; color:#666;">${wp.time}</span>`);
            routeMapLayers.push(marker);

            stepOl.innerHTML += `<li class="${stepClass}"><strong>${wp.time}</strong> - ${wpTitle} (${wpDesc})</li>`;
        });

        if (latlngs.length > 1) {
            const polyline = L.polyline(latlngs, { color: '#0066cc', weight: 4, opacity: 0.7 }).addTo(routeMap);
            routeMapLayers.push(polyline);
            routeMap.fitBounds(polyline.getBounds(), { padding: [30, 30] });
        } else {
            routeMap.setView(latlngs[0], 14);
        }

        setTimeout(() => routeMap.invalidateSize(), 100);
    }

    // --- Route Binding Handlers ---
    function getEligibleBindingRoutes() {
        const routes = [];
        const seenIds = new Set();
        document.querySelectorAll('.route-item').forEach(item => {
            const rType = item.getAttribute('data-route-type');
            if (rType && rType !== 'completed') {
                const id = parseInt(item.id.replace('route_', ''));
                if (id && !seenIds.has(id)) {
                    seenIds.add(id);
                    const dataDiv = document.getElementById('route_data_' + id);
                    if (dataDiv) {
                        routes.push({ 
                            id: id, 
                            name: dataDiv.getAttribute('data-rname'), 
                            rep: dataDiv.getAttribute('data-rep'),
                            date: dataDiv.getAttribute('data-date') || ''
                        });
                    }
                }
            }
        });
        return routes;
    }

    function updateBindingSlotDropdowns() {
        // Collect all currently selected route IDs (excluding empty)
        const selectedIds = [];
        document.querySelectorAll('select.rb-slot-select').forEach(select => {
            if (select.value) {
                selectedIds.push(select.value);
            }
        });

        // Update each select dropdown
        document.querySelectorAll('select.rb-slot-select').forEach(select => {
            const currentValue = select.value;
            // Iterate over all options in this select
            Array.from(select.options).forEach(option => {
                if (!option.value) return; // Skip placeholder option
                
                // If this option is selected in another dropdown, hide/disable it
                if (selectedIds.includes(option.value) && option.value !== currentValue) {
                    option.disabled = true;
                    option.style.display = 'none';
                } else {
                    option.disabled = false;
                    option.style.display = 'block';
                }
            });
        });
    }

    function openRouteBindingModal() {
        document.getElementById('rbBoundName').value = '';
        document.getElementById('rbSlotsContainer').innerHTML = '';
        rbSlotsCount = 0;
        addBindingSlot();
        addBindingSlot();
        document.getElementById('routeBindingModal').style.display = 'flex';
        updateBindingSlotDropdowns();
    }

    function closeRouteBindingModal() {
        document.getElementById('routeBindingModal').style.display = 'none';
    }

    function addBindingSlot() {
        rbSlotsCount++;
        const index = rbSlotsCount;
        const eligibleRoutes = getEligibleBindingRoutes();
        
        let optionsHtml = '<option value="">-- Choose Route --</option>';
        eligibleRoutes.forEach(r => {
            const displayDate = r.date ? ` - ${r.date}` : '';
            optionsHtml += `<option value="${r.id}">${r.name} (Rep: ${r.rep})${displayDate}</option>`;
        });
        
        const slotHtml = `
            <div class="rb-slot-column" id="rb_slot_col_${index}" style="position: relative;">
                ${index > 2 ? `<button type="button" onclick="removeBindingSlot(${index})" style="position: absolute; top: 10px; right: 10px; border: none; background: #dc2626; color: #fff; width: 22px; height: 22px; border-radius: 50%; cursor: pointer; font-size: 11px; display: flex; align-items: center; justify-content: center; font-weight: bold; padding:0;">✕</button>` : ''}
                <h5 style="margin: 0 0 5px 0; color: #3f51b5; font-size: 12px; font-weight: bold; text-transform: uppercase;">Slot ${index}</h5>
                <div class="rb-slot-box">
                    <div style="font-size: 20px; color: #cbd5e1; margin-bottom: 6px;" id="rb_slot_icon_${index}">+</div>
                    <select class="rb-slot-select" id="rb_select_${index}" onchange="onBindingSlotRouteSelect(${index}, this)">
                        ${optionsHtml}
                    </select>
                </div>
                <div class="rb-bill-list" id="rb_bills_${index}"></div>
            </div>
        `;
        document.getElementById('rbSlotsContainer').insertAdjacentHTML('beforeend', slotHtml);
        updateBindingSlotDropdowns();
    }

    function removeBindingSlot(index) {
        const el = document.getElementById(`rb_slot_col_${index}`);
        if (el) el.remove();
        updateBindingSlotDropdowns();
    }

    function onBindingSlotRouteSelect(index, select) {
        const routeId = select.value;
        const billsContainer = document.getElementById(`rb_bills_${index}`);
        const icon = document.getElementById(`rb_slot_icon_${index}`);
        
        if (!routeId) {
            billsContainer.style.display = 'none';
            billsContainer.innerHTML = '';
            icon.innerText = '+';
            updateBindingSlotDropdowns();
            return;
        }
        
        // Find the selected route object
        const eligibleRoutes = getEligibleBindingRoutes();
        const selectedRoute = eligibleRoutes.find(r => r.id === parseInt(routeId));
        const selectedRouteName = selectedRoute ? selectedRoute.name : '';
        const selectedRepName = selectedRoute ? selectedRoute.rep : '';

        icon.innerText = '<i class="ph ph-link"></i>';
        billsContainer.style.display = 'block';
        billsContainer.innerHTML = '<p style="text-align: center; color: #888;">Loading details... </p>';
        
        fetch('<?= APP_URL ?>/RepTracking/api_get_route_details/' + routeId)
            .then(res => res.json())
            .then(data => {
                if (data.status !== 'success' || !data.bills) {
                    billsContainer.innerHTML = '<p style="text-align: center; color: #888;">Error loading details.</p>';
                    return;
                }
                
                // Calculate values for Route Preview
                const invoices = data.bills;
                const invoiceCount = invoices.length;
                const uniqueCustomers = new Set(invoices.map(b => b.customer_id)).size;
                const totalRouteValue = invoices.reduce((sum, b) => sum + parseFloat(b.true_grand_total), 0);

                let previewHtml = `
                    <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px; margin-bottom: 12px; font-size: 11px; line-height: 1.5; color: #334155; text-align: left; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                        <div style="margin-bottom: 3px;"><strong>Route:</strong> ${selectedRouteName}</div>
                        <div style="margin-bottom: 3px;"><strong>Route ID:</strong> #${routeId}</div>
                        <div style="margin-bottom: 3px;"><strong>Rep Name:</strong> ${selectedRepName}</div>
                        <div style="margin-bottom: 3px;"><strong>Customers Count:</strong> ${uniqueCustomers}</div>
                        <div style="margin-bottom: 3px;"><strong>Invoice Count:</strong> ${invoiceCount}</div>
                        <div><strong>Total Value:</strong> <strong style="color: #16a34a;">Rs ${totalRouteValue.toLocaleString('en-IN', {minimumFractionDigits: 2})}</strong></div>
                    </div>
                `;

                if (invoices.length === 0) {
                    previewHtml += '<p style="text-align: center; color: #888; font-size: 11px;">No sales orders in this route.</p>';
                    billsContainer.innerHTML = previewHtml;
                    return;
                }
                
                previewHtml += '<div style="font-weight: bold; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; margin-bottom: 8px; font-size: 10px; text-transform: uppercase; color: #475569;">Sales Orders / Bills</div>';
                previewHtml += '<div style="max-height: 150px; overflow-y: auto; display: flex; flex-direction: column; gap: 4px;">';
                invoices.forEach(b => {
                    let trueTotal = parseFloat(b.true_grand_total).toLocaleString('en-IN', {minimumFractionDigits: 2});
                    previewHtml += `
                        <div class="rb-bill-item" style="display: flex; justify-content: space-between; align-items: flex-start; background: #fff; border: 1px solid #e2e8f0; padding: 6px; border-radius: 4px; font-size: 11px;">
                            <div style="display: flex; flex-direction: column; max-width: 65%;">
                                <strong>${b.invoice_number}</strong>
                                <span style="color: #64748b; font-size: 10px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${b.customer_name}</span>
                            </div>
                            <strong style="font-family: monospace; color: #0f172a; white-space: nowrap;">Rs ${trueTotal}</strong>
                        </div>
                    `;
                });
                previewHtml += '</div>';
                billsContainer.innerHTML = previewHtml;
            });
        updateBindingSlotDropdowns();
    }

    function submitRouteBinding() {
        const boundName = document.getElementById('rbBoundName').value.trim();
        if (!boundName) { alert("Please enter a custom name for the bound route."); return; }
        
        const routeIds = [];
        document.querySelectorAll('select.rb-slot-select').forEach(select => {
            if (select.value) { routeIds.push(parseInt(select.value)); }
        });
        
        const uniqueRouteIds = [...new Set(routeIds)];
        if (uniqueRouteIds.length < 2) { alert("Please select at least 2 distinct routes to bind."); return; }
        if (uniqueRouteIds.length !== routeIds.length) { 
            alert("Please make sure you do not select the same route in multiple slots.\n\nAll Selected IDs: " + routeIds.join(', ') + "\nUnique IDs: " + uniqueRouteIds.join(', ')); 
            return; 
        }
        
        if (!confirm(`Are you sure you want to bind these ${uniqueRouteIds.length} routes together under "${boundName}"?`)) { return; }
        
        fetch('<?= APP_URL ?>/RepTracking/api_create_binding', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ binding_name: boundName, route_ids: uniqueRouteIds })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                alert("Success: " + data.message);
                closeRouteBindingModal();
                // Redirect/reload switching filter to Adjustments
                window.location.href = window.location.pathname + `?filter=adjustments`;
            } else {
                alert("Error: " + data.message);
            }
        });
    }

    function unbindActiveRoute() {
        unbindCombinedRoute();
    }

    function unbindCombinedRoute() {
        if (!currentRouteId) {
            alert("No active route selected.");
            return;
        }
        const btnUnbind = document.getElementById('btnUnbindRouteTab1') || document.getElementById('btnUnbindRoute');
        const bindingId = btnUnbind ? btnUnbind.getAttribute('data-binding-id') : null;

        if (!confirm("Are you sure you want to Undo this route binding? All invoices, loading data, and collections will be restored to their original separate routes, and this combined route will be removed.")) {
            return;
        }
        
        fetchSecure('<?= APP_URL ?>/RepTracking/api_unbind_route', {
            method: 'POST',
            body: JSON.stringify({
                route_id: parseInt(currentRouteId),
                binding_id: bindingId ? parseInt(bindingId) : 0
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                alert("Success: " + data.message);
                window.location.href = window.location.pathname + `?filter=adjustments`;
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(err => {
            console.error('[Unbind Route Error]', err);
            alert("Failed to process undo route binding: " + err.message);
        });
    }

    // --- Attach Invoice Modal Handlers ---
    function openAttachInvoiceModal() {
        document.getElementById('invoiceSearchInput').value = '';
        document.getElementById('soFilterStartDate').value = '';
        document.getElementById('soFilterEndDate').value = '';
        if (document.getElementById('soFilterRepSelect')) document.getElementById('soFilterRepSelect').value = '';
        if (document.getElementById('soFilterRouteSelect')) document.getElementById('soFilterRouteSelect').value = '';
        
        const countElem = document.getElementById('attachSelectedCount');
        if (countElem) countElem.innerText = '0';
        
        const unattachedBadge = document.getElementById('unattachedCount');
        if (unattachedBadge) unattachedBadge.innerText = '0 orders';

        document.getElementById('attachInvoiceModal').style.display = 'flex';
        searchUnattachedInvoices();
    }

    function closeAttachInvoiceModal() {
        document.getElementById('attachInvoiceModal').style.display = 'none';
    }

    function searchUnattachedInvoices() {
        const query = document.getElementById('invoiceSearchInput').value;
        const startDate = document.getElementById('soFilterStartDate').value;
        const endDate = document.getElementById('soFilterEndDate').value;
        const repId = document.getElementById('soFilterRepSelect') ? document.getElementById('soFilterRepSelect').value : '';
        const routeId = document.getElementById('soFilterRouteSelect') ? document.getElementById('soFilterRouteSelect').value : '';
        const container = document.getElementById('unattachedInvoicesContainer');
        
        container.innerHTML = `
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 48px 16px; color: #64748b; gap: 10px;">
                <div style="width: 28px; height: 28px; border: 3px solid #cbd5e1; border-top-color: #007aff; border-radius: 50%; animation: iosSpin 0.8s linear infinite;"></div>
                <span style="font-size: 13px; font-weight: 500;">Searching unattached sales orders...</span>
            </div>
            <style>@keyframes iosSpin { to { transform: rotate(360deg); } }</style>
        `;
        
        let url = '<?= APP_URL ?>/RepTracking/api_get_unattached_invoices?search=' + encodeURIComponent(query);
        if (startDate) url += '&start_date=' + encodeURIComponent(startDate);
        if (endDate) url += '&end_date=' + encodeURIComponent(endDate);
        if (repId) url += '&rep_id=' + encodeURIComponent(repId);
        if (routeId) url += '&route_id=' + encodeURIComponent(routeId);
        
        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.status !== 'success' || !data.invoices || data.invoices.length === 0) {
                    container.innerHTML = `
                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 48px 16px; color: #94a3b8; text-align: center;">
                            <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom: 8px; opacity: 0.6;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                            </svg>
                            <div style="font-weight: 600; font-size: 14px; color: #64748b;">No Unattached Sales Orders Found</div>
                            <div style="font-size: 12px; margin-top: 2px;">Try adjusting your search criteria or date filters.</div>
                        </div>
                    `;
                    const unattachedBadge = document.getElementById('unattachedCount');
                    if (unattachedBadge) unattachedBadge.innerText = '0 orders';
                    updateAttachSelectedCount();
                    return;
                }
                
                const unattachedBadge = document.getElementById('unattachedCount');
                if (unattachedBadge) unattachedBadge.innerText = data.invoices.length + ' orders';
                
                let html = '<div style="display: grid; grid-template-columns: 1fr; gap: 10px;">';
                data.invoices.forEach(inv => {
                    let amtFormatted = parseFloat(inv.true_grand_total || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    let repName = inv.sales_rep_name || inv.rep_name || inv.salesperson || 'Unassigned Rep';
                    let routeName = inv.route_name || inv.territory || '';
                    
                    html += `
                        <div class="unattached-so-card" onclick="toggleUnattachedCardCheck(this, event)" style="display: flex; align-items: center; gap: 14px; padding: 14px 16px; background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 14px; cursor: pointer; transition: all 0.18s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                            <input type="checkbox" class="attach-invoice-checkbox" value="${inv.id}" onclick="event.stopPropagation(); updateAttachSelectedCount();" style="width: 18px; height: 18px; accent-color: #007aff; cursor: pointer; flex-shrink: 0;">
                            <div style="flex: 1; min-width: 0;">
                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 4px;">
                                    <span style="font-weight: 700; font-size: 14px; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${inv.customer_name}</span>
                                    <span style="font-weight: 800; font-family: monospace; font-size: 14px; color: #059669; white-space: nowrap;">Rs ${amtFormatted}</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; font-size: 11px; color: #64748b;">
                                    <span style="background: #eff6ff; color: #1d4ed8; font-weight: 700; padding: 2px 8px; border-radius: 6px; font-family: monospace; font-size: 11px;">${inv.invoice_number}</span>
                                    <span>📅 ${inv.invoice_date}</span>
                                    <span>👤 ${repName}</span>
                                    ${routeName ? `<span style="background: #f1f5f9; color: #475569; padding: 1px 6px; border-radius: 4px;">🛣️ ${routeName}</span>` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                container.innerHTML = html;
                updateAttachSelectedCount();
            })
            .catch(err => {
                console.error('[searchUnattachedInvoices Error]', err);
                container.innerHTML = `<div style="text-align: center; color: #ef4444; padding: 20px; font-size: 13px;">Failed to load sales orders. Please try again.</div>`;
            });
    }

    function toggleUnattachedCardCheck(card, event) {
        const checkbox = card.querySelector('.attach-invoice-checkbox');
        if (checkbox) {
            checkbox.checked = !checkbox.checked;
            updateAttachSelectedCount();
        }
    }

    function updateAttachSelectedCount() {
        const checkboxes = document.querySelectorAll('.attach-invoice-checkbox');
        let checkedCount = 0;
        checkboxes.forEach(cb => {
            const card = cb.closest('.unattached-so-card');
            if (cb.checked) {
                checkedCount++;
                if (card) {
                    card.style.borderColor = '#007aff';
                    card.style.background = '#f0f7ff';
                    card.style.boxShadow = '0 0 0 1px #007aff';
                }
            } else {
                if (card) {
                    card.style.borderColor = '#e2e8f0';
                    card.style.background = '#ffffff';
                    card.style.boxShadow = '0 1px 3px rgba(0,0,0,0.03)';
                }
            }
        });
        const countElem = document.getElementById('attachSelectedCount');
        if (countElem) countElem.innerText = checkedCount;
    }

    function toggleSelectAllUnattached() {
        const checkboxes = document.querySelectorAll('.attach-invoice-checkbox');
        if (checkboxes.length === 0) return;
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => {
            cb.checked = !allChecked;
        });
        updateAttachSelectedCount();
    }

    function confirmAttachInvoices() {
        const checkedInvoices = [];
        document.querySelectorAll('.attach-invoice-checkbox:checked').forEach(cb => {
            checkedInvoices.push(cb.value);
        });
        
        if (checkedInvoices.length === 0) { alert("Please select at least one sales order to attach."); return; }
        
        closeAttachInvoiceModal();
        
        fetchSecure('<?= APP_URL ?>/RepTracking/api_attach_invoices', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ route_id: currentRouteId, invoice_ids: checkedInvoices })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                alert("Attached successfully!");
                loadRouteDetails(currentRouteId);
            } else {
                alert("Error: " + data.message);
            }
        });
    }

    function resetSalesOrderFilters() {
        document.getElementById('invoiceSearchInput').value = '';
        document.getElementById('soFilterStartDate').value = '';
        document.getElementById('soFilterEndDate').value = '';
        if (document.getElementById('soFilterRepSelect')) document.getElementById('soFilterRepSelect').value = '';
        if (document.getElementById('soFilterRouteSelect')) document.getElementById('soFilterRouteSelect').value = '';
        searchUnattachedInvoices();
    }

    /* Dots Menu Handlers */
    function toggleDotsMenu(e, id) {
        e.stopPropagation();
        const btn = e.currentTarget;
        const dropdown = document.getElementById('dots-dropdown-' + id);
        if (!dropdown) return;
        
        const isShowing = dropdown.classList.contains('show');
        
        // Hide all other dropdowns
        closeAllDotsMenus();
        
        if (!isShowing) {
            const rect = btn.getBoundingClientRect();
            const windowHeight = window.innerHeight;
            const windowWidth = window.innerWidth;
            
            // Temporarily show to measure dimensions, then hide
            dropdown.style.visibility = 'hidden';
            dropdown.style.display = 'block';
            const dropdownHeight = dropdown.offsetHeight;
            const dropdownWidth = Math.max(190, dropdown.offsetWidth || 190);
            dropdown.style.display = '';
            dropdown.style.visibility = '';
            
            // Determine vertical position
            let top, bottom;
            const spaceBelow = windowHeight - rect.bottom;
            const spaceAbove = rect.top;
            
            if (spaceBelow < dropdownHeight + 12 && spaceAbove >= dropdownHeight + 12) {
                // Not enough space below but enough above -> open upward
                top = 'auto';
                bottom = (windowHeight - rect.top + 8) + 'px';
            } else {
                // Open downward (default)
                top = (rect.bottom + 8) + 'px';
                bottom = 'auto';
            }
            
            // Determine horizontal position: right-align with button
            let left = rect.right - dropdownWidth;
            // Clamp to viewport bounds (with 8px padding)
            left = Math.max(8, Math.min(left, windowWidth - dropdownWidth - 8));
            
            dropdown.style.top = top;
            dropdown.style.bottom = bottom;
            dropdown.style.left = left + 'px';
            dropdown.style.right = 'auto';
            dropdown.style.margin = '0';
            
            dropdown.classList.add('show');
            
            const backdrop = document.getElementById('menuBackdrop');
            if (backdrop) backdrop.style.display = 'block';
        }
    }

    function closeAllDotsMenus() {
        document.querySelectorAll('.dots-dropdown').forEach(d => d.classList.remove('show'));
        const backdrop = document.getElementById('menuBackdrop');
        if (backdrop) backdrop.style.display = 'none';
        document.querySelectorAll('.dots-menu-container').forEach(c => c.style.zIndex = '');
    }

    // Close dropdowns on click outside
    document.addEventListener('click', function() {
        closeAllDotsMenus();
    });

    function updateSingleInvoiceDeliveryStatus(invoiceId, customerId, newStatus) {
        const payload = {
            route_id: parseInt(currentRouteId),
            customer_id: parseInt(customerId),
            deliveries: [
                {
                    invoice_id: parseInt(invoiceId),
                    delivery_status: newStatus,
                    items: []
                }
            ],
            collections: null
        };
        
        console.log('[updateSingleInvoiceDeliveryStatus] Updating status:', { invoiceId, customerId, newStatus, payload });

        fetchSecure('<?= APP_URL ?>/RepTracking/api_process_delivery_visit', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(async res => {
            const rawText = await res.text();
            console.log('[updateSingleInvoiceDeliveryStatus] HTTP Status:', res.status, res.statusText);
            console.log('[updateSingleInvoiceDeliveryStatus] Server Response Raw:', rawText);
            let data = null;
            try {
                data = JSON.parse(rawText);
            } catch (jsonErr) {
                console.error('[updateSingleInvoiceDeliveryStatus] Response is not valid JSON:', rawText);
                throw new Error('Server returned non-JSON response (HTTP ' + res.status + '). Check console log for details.\nPreview: ' + rawText.substring(0, 200));
            }
            return { res, data };
        })
        .then(({ res, data }) => {
            if (data.status === 'success') {
                loadDeliveryLiveStage(currentRouteId);
            } else {
                alert('Error updating status: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(err => {
            console.error('[updateSingleInvoiceDeliveryStatus] Failed:', err);
            alert('An unexpected error occurred during status update:\n' + err.message);
        });
    }

    function editSalesOrder(id) {
        window.open('<?= APP_URL ?>/sales/edit/' + id + '?type=sales_order&back_url=' + encodeURIComponent(window.location.href), '_blank');
    }

    function printInvoice(id) {
        window.open('<?= APP_URL ?>/sales/show/' + id + '?print=1', '_blank');
    }

    function viewCustomerProfile(customerId, customerName) {
        if (customerId && !isNaN(customerId) && parseInt(customerId) > 0) {
            window.open('<?= APP_URL ?>/customer/index/' + parseInt(customerId), '_blank');
        } else if (customerName && customerName !== 'null') {
            window.open('<?= APP_URL ?>/customer?search=' + encodeURIComponent(customerName), '_blank');
        } else {
            window.open('<?= APP_URL ?>/customer', '_blank');
        }
    }

    function downloadInvoicePdf(id) {
        window.open('<?= APP_URL ?>/sales/show/' + id + '?pdf=1', '_blank');
    }

    function exportInvoiceExcel(id) {
        window.open('<?= APP_URL ?>/sales/show/' + id + '?excel=1', '_blank');
    }

    /* Secure Delete Handlers */
    let deleteTargetId = null;

    function confirmDeleteSalesOrder(id, invNum) {
        deleteTargetId = id;
        document.getElementById('deleteTargetInvNum').innerText = invNum;
        document.getElementById('deleteConfirmPassword').value = '';
        document.getElementById('deleteConfirmReason').value = '';
        document.getElementById('deleteConfirmModal').style.display = 'flex';
    }

    function closeDeleteConfirmModal() {
        document.getElementById('deleteConfirmModal').style.display = 'none';
        deleteTargetId = null;
    }

    function submitDeleteSalesOrder() {
        const password = document.getElementById('deleteConfirmPassword').value;
        const reason = document.getElementById('deleteConfirmReason').value.trim();
        
        if (!password) { alert("Please enter the administrator password."); return; }
        if (!reason) { alert("Please enter a deletion reason."); return; }
        
        const targetId = deleteTargetId;
        console.log("[Delete Sales Order] Initiating deletion for ID:", targetId);
        
        closeDeleteConfirmModal();
        
        const formData = new URLSearchParams();
        formData.append('password', password);
        formData.append('delete_reason', reason);
        formData.append('is_ajax', '1');
        
        fetchSecure('<?= APP_URL ?>/sales/delete/' + targetId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: formData.toString()
        })
        .then(res => res.json())
        .then(data => {
            console.log("[Delete Sales Order] Response data:", data);
            if (data.status === 'success') {
                alert(data.message || "Sales Order successfully deleted and stock balances reversed!");
                onRouteDataChanged();
                loadAdjustmentsStage(currentRouteId);
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(err => {
            console.error("[Delete Sales Order] Fetch error:", err);
            alert("Error deleting sales order: " + err.message);
        });
    }

    /* Route Deletion Handlers */
    function scramblePassword(str, key) {
        if (!key) return str;
        let result = "";
        for (let i = 0; i < str.length; i++) {
            let charCode = str.charCodeAt(i) ^ key.charCodeAt(i % key.length);
            result += String.fromCharCode(charCode);
        }
        return btoa(result);
    }

    function refreshDeleteRouteCaptcha() {
        document.getElementById('deleteRouteCaptchaQuestion').innerText = 'Loading CAPTCHA...';
        document.getElementById('deleteRouteCaptchaAnswer').value = '';
        fetchSecure('<?= APP_URL ?>/RepTracking/get_captcha')
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById('deleteRouteCaptchaQuestion').innerText = data.question;
            } else {
                document.getElementById('deleteRouteCaptchaQuestion').innerText = 'Failed to load CAPTCHA';
            }
        })
        .catch(err => {
            document.getElementById('deleteRouteCaptchaQuestion').innerText = 'Error loading CAPTCHA';
        });
    }

    function openDeleteRouteModal() {
        if (!currentRouteId) { alert("No route selected!"); return; }
        const routeNumText = `#RT-${String(currentRouteId).padStart(5, '0')}`;
        document.getElementById('deleteRouteTargetNum').innerText = routeNumText;
        document.getElementById('deleteRoutePassword').value = '';
        document.getElementById('deleteRouteReason').value = '';
        document.getElementById('deleteRouteModal').style.display = 'flex';
        refreshDeleteRouteCaptcha();
    }

    function closeDeleteRouteModal() {
        document.getElementById('deleteRouteModal').style.display = 'none';
    }

    function submitDeleteRoute() {
        const password = document.getElementById('deleteRoutePassword').value;
        const reason = document.getElementById('deleteRouteReason').value.trim();
        const mode = document.querySelector('input[name="deleteRouteMode"]:checked').value;
        const captchaAnswer = document.getElementById('deleteRouteCaptchaAnswer').value.trim();
        
        if (!password) { alert("Please enter the administrator password."); return; }
        if (!captchaAnswer) { alert("Please enter the CAPTCHA answer."); return; }
        if (!reason) { alert("Please enter a deletion reason."); return; }
        
        console.log("[Delete Route] Initiating deletion for Route ID:", currentRouteId, "Mode:", mode);
        
        closeDeleteRouteModal();
        
        const scrambledPassword = scramblePassword(password, CSRF_TOKEN);
        
        const formData = new URLSearchParams();
        formData.append('route_id', currentRouteId);
        formData.append('mode', mode);
        formData.append('password', scrambledPassword);
        formData.append('delete_reason', reason);
        formData.append('captcha_answer', captchaAnswer);
        formData.append('is_ajax', '1');
        
        fetchSecure('<?= APP_URL ?>/RepTracking/delete_route', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: formData.toString()
        })
        .then(res => res.json())
        .then(data => {
            console.log("[Delete Route] Response data:", data);
            if (data.status === 'success') {
                alert(data.message || "Route successfully deleted!");
                goBackToRoutes();
                onRouteDataChanged();
            } else {
                alert("Error: " + data.message);
                document.getElementById('deleteRouteModal').style.display = 'flex';
                refreshDeleteRouteCaptcha();
            }
        })
        .catch(err => {
            console.error("[Delete Route] Fetch error:", err);
            alert("Error deleting route: " + err.message);
            document.getElementById('deleteRouteModal').style.display = 'flex';
            refreshDeleteRouteCaptcha();
        });
    }

    /* Move Sales Order Handlers */
    let moveTargetId = null;

    function openMoveInvoiceModal(id, invNum) {
        moveTargetId = id;
        document.getElementById('moveTargetInvNum').innerText = invNum;
        
        const select = document.getElementById('moveDestinationRouteSelect');
        select.innerHTML = '<option value="">-- Select Destination Route --</option>';
        
        document.querySelectorAll('.route-item').forEach(el => {
            const rId = el.id.replace('route_', '');
            if (parseInt(rId) === parseInt(currentRouteId)) return;
            
            const d = document.getElementById('route_data_' + rId);
            if (!d) return;
            const rName = d.getAttribute('data-rname') || '';
            const repName = d.getAttribute('data-rep') || '';
            const date = d.getAttribute('data-date') || '';
            
            const opt = document.createElement('option');
            opt.value = rId;
            opt.innerText = `#RT-${String(rId).padStart(5, '0')} - ${rName} (${repName} | ${date})`;
            select.appendChild(opt);
        });
        
        document.getElementById('moveInvoiceModal').style.display = 'flex';
    }

    function closeMoveInvoiceModal() {
        document.getElementById('moveInvoiceModal').style.display = 'none';
        moveTargetId = null;
    }

    function submitMoveSalesOrder() {
        const targetRouteId = document.getElementById('moveDestinationRouteSelect').value;
        if (!targetRouteId) { alert("Please select a destination route."); return; }
        
        const targetId = moveTargetId;
        console.log("[Move Sales Order] Initiating move for ID:", targetId, "to route:", targetRouteId);
        
        closeMoveInvoiceModal();
        
        fetchSecure('<?= APP_URL ?>/RepTracking/api_attach_invoices', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ route_id: targetRouteId, invoice_ids: ['route:' + targetId] })
        })
        .then(res => res.json())
        .then(data => {
            console.log("[Move Sales Order] Response data:", data);
            if (data.status === 'success') {
                alert("Sales Order successfully moved to the destination route!");
                onRouteDataChanged();
                loadAdjustmentsStage(currentRouteId);
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(err => {
            console.error("[Move Sales Order] Fetch error:", err);
            alert("Error moving sales order: " + err.message);
        });
    }



    // Explicitly export functions to global scope for inline HTML handlers
    
    // Centrally registered DOM event listeners (MED-4)
    const initCentrallyRegisteredListeners = () => {
        document.getElementById('filterRepSelect')?.addEventListener('change', (event) => { searchRouteList(); });
        document.getElementById('filterRouteSelect')?.addEventListener('change', (event) => { searchRouteList(); });
        document.getElementById('filterDateInput')?.addEventListener('change', (event) => { searchRouteList(); });
        document.getElementById('filterTerritorySelect')?.addEventListener('change', (event) => { searchRouteList(); });
        document.getElementById('auto-evt-button-1')?.addEventListener('click', (event) => { clearFilters(); });
        document.getElementById('auto-evt-button-1')?.addEventListener('mouseover', function(event) { this.style.background='var(--c-fill)'; this.style.color='var(--t-primary)';; });
        document.getElementById('auto-evt-button-1')?.addEventListener('mouseout', function(event) { this.style.background='var(--c-surface2)'; this.style.color='var(--t-secondary)';; });
        document.getElementById('auto-evt-button-2')?.addEventListener('click', (event) => { goBackToRoutes(); });
        document.getElementById('auto-evt-button-3')?.addEventListener('click', (event) => { openRouteSwitcherModal(); });
        document.getElementById('btnViewMap')?.addEventListener('click', (event) => { openMapModal(); });
        document.getElementById('auto-evt-button-4')?.addEventListener('click', function(event) { switchRouteTab(1, this); });
        document.getElementById('auto-evt-button-6')?.addEventListener('click', function(event) { switchRouteTab(3, this); });
        document.getElementById('auto-evt-button-7')?.addEventListener('click', function(event) { switchRouteTab(4, this); });
        document.getElementById('auto-evt-button-8')?.addEventListener('click', function(event) { switchRouteTab(5, this); });
        document.getElementById('auto-evt-button-9')?.addEventListener('click', function(event) { switchRouteTab(6, this); });
        document.getElementById('btnTabCreditBills')?.addEventListener('click', function(event) { switchRouteTab(14, this); });
        document.getElementById('auto-evt-button-11')?.addEventListener('click', function(event) { switchRouteTab(8, this); });
        document.getElementById('btnTabExpenses')?.addEventListener('click', function(event) { switchRouteTab(12, this); });
        document.getElementById('auto-evt-button-10')?.addEventListener('click', function(event) { switchRouteTab(7, this); });
        document.getElementById('auto-evt-button-12')?.addEventListener('click', function(event) { switchRouteTab(9, this); });
        document.getElementById('btnTabMarketReturns')?.addEventListener('click', function(event) { switchRouteTab(13, this); });
        document.getElementById('btnTabFinalize')?.addEventListener('click', function(event) { switchRouteTab(11, this); });
        document.getElementById('auto-evt-button-14')?.addEventListener('click', (event) => { goBackToRoutes(); });
        document.getElementById('sb-step-1')?.addEventListener('click', (event) => { switchRouteTab(1); });
        document.getElementById('sb-step-3')?.addEventListener('click', (event) => { switchRouteTab(3); });
        document.getElementById('sb-step-4')?.addEventListener('click', (event) => { switchRouteTab(4); });
        document.getElementById('sb-step-5')?.addEventListener('click', (event) => { switchRouteTab(5); });
        document.getElementById('sb-step-6')?.addEventListener('click', (event) => { switchRouteTab(6); });
        document.getElementById('sb-step-14')?.addEventListener('click', (event) => { switchRouteTab(14); });
        document.getElementById('sb-step-8')?.addEventListener('click', (event) => { switchRouteTab(8); });
        document.getElementById('sb-step-12')?.addEventListener('click', (event) => { switchRouteTab(12); });
        document.getElementById('sb-step-7')?.addEventListener('click', (event) => { switchRouteTab(7); });
        document.getElementById('sb-step-9')?.addEventListener('click', (event) => { switchRouteTab(9); });
        document.getElementById('sb-step-10')?.addEventListener('click', (event) => { switchRouteTab(10); });
        document.getElementById('sb-step-13')?.addEventListener('click', (event) => { switchRouteTab(13); });
        document.getElementById('sb-step-11')?.addEventListener('click', (event) => { switchRouteTab(11); });
        document.getElementById('auto-evt-button-15')?.addEventListener('click', (event) => { printBalancingReport(); });
        document.getElementById('auto-evt-button-16')?.addEventListener('click', (event) => { printLoadingSheetSpreadsheet(); });
        document.getElementById('auto-evt-button-17')?.addEventListener('click', (event) => { printLoadingSheet('summary'); });
        document.getElementById('auto-evt-button-18')?.addEventListener('click', (event) => { exportCSV(); });
        document.getElementById('auto-evt-button-19')?.addEventListener('click', (event) => { openRouteSwitcherModal(); });
        document.getElementById('btnViewMapDetails')?.addEventListener('click', (event) => { openMapModal(); });
        document.getElementById('auto-evt-button-20')?.addEventListener('click', (event) => { openDeleteRouteModal(); });
        document.getElementById('btnSaveRouteNotes')?.addEventListener('click', (event) => { saveRouteNotes(); });
        document.getElementById('btnTab3CreateSO')?.addEventListener('click', (event) => { redirectToAddInvoice(); });
        document.getElementById('btnTab3AttachSO')?.addEventListener('click', (event) => { openAttachInvoiceModal(); });
        document.getElementById('btnTab3PrintInvoices')?.addEventListener('click', (event) => { printRouteInvoices(); });
        document.getElementById('btnTab3PrintSummary')?.addEventListener('click', (event) => { printRouteInvoicesSummary(); });
        document.getElementById('auto-evt-button-21')?.addEventListener('click', (event) => { printLoadingSheet('summary'); });
        document.getElementById('auto-evt-button-22')?.addEventListener('click', (event) => { submitAdjustmentsLogisticsArrange(); });
        document.getElementById('creditBillsSearch')?.addEventListener('input', (event) => { filterCreditBillsList(); });
        document.getElementById('creditBillsRouteFilter')?.addEventListener('change', (event) => { filterCreditBillsList(); });
        document.getElementById('actQty5000')?.addEventListener('input', (event) => { recalculateDenominations(); });
        document.getElementById('actQty2000')?.addEventListener('input', (event) => { recalculateDenominations(); });
        document.getElementById('actQty1000')?.addEventListener('input', (event) => { recalculateDenominations(); });
        document.getElementById('actQty500')?.addEventListener('input', (event) => { recalculateDenominations(); });
        document.getElementById('actQty100')?.addEventListener('input', (event) => { recalculateDenominations(); });
        document.getElementById('actQty50')?.addEventListener('input', (event) => { recalculateDenominations(); });
        document.getElementById('actQty20')?.addEventListener('input', (event) => { recalculateDenominations(); });
        document.getElementById('actValCoins')?.addEventListener('input', (event) => { recalculateDenominations(); });
        document.getElementById('btnSaveReconciliationDraft')?.addEventListener('click', (event) => { saveReconciliationDraft(); });
        document.getElementById('settleVerifyStock')?.addEventListener('change', (event) => { checkSettleVerification(); updateSidebarProgress(); });
        document.getElementById('btnSaveReturnStockDraft')?.addEventListener('click', (event) => { saveReturnStockDraft(); });
        document.getElementById('btnPrintReturnStock')?.addEventListener('click', (event) => {
            if (!currentRouteId) return;
            window.open('<?= APP_URL ?>/RepTracking/print_return_stock/' + currentRouteId, '_blank');
        });
        document.getElementById('settleDeTabCollectionsBtn')?.addEventListener('click', (event) => { switchSettleDeTab('collections'); });
        document.getElementById('settleDeTabSalesBtn')?.addEventListener('click', (event) => { switchSettleDeTab('sales'); });
        document.getElementById('btnSaveAccountingDraft')?.addEventListener('click', (event) => { saveAccountingDraft(); });
        document.getElementById('settleSubmitBtn')?.addEventListener('click', (event) => { submitFinalSettle(); });
        document.getElementById('auto-evt-button-23')?.addEventListener('click', (event) => { printBalancingReport(); });
        document.getElementById('auto-evt-button-24')?.addEventListener('click', (event) => { printLoadingSheetSpreadsheet(); });
        document.getElementById('auto-evt-button-25')?.addEventListener('click', (event) => { printLoadingSheet('summary'); });
        document.getElementById('auto-evt-button-26')?.addEventListener('click', (event) => { exportCSV(); });
        document.getElementById('compTabInvoicesBtn')?.addEventListener('click', (event) => { switchCompletedTab('invoices'); });
        document.getElementById('compTabCollectionsBtn')?.addEventListener('click', (event) => { switchCompletedTab('collections'); });
        document.getElementById('compTabVariancesBtn')?.addEventListener('click', (event) => { switchCompletedTab('variances'); });
        document.getElementById('auto-evt-div-27')?.addEventListener('click', (event) => { document.getElementById('floatingSearchInput').focus(); });
        document.getElementById('floatingSearchInput')?.addEventListener('input', (event) => { searchRouteList(); });
        document.getElementById('auto-evt-button-28')?.addEventListener('click', (event) => { openCreateRouteModal(); });
        document.getElementById('btnOpenRouteBinding')?.addEventListener('click', (event) => { openRouteBindingModal(); });
        document.getElementById('btnToggleWorkflowSidebar')?.addEventListener('click', (event) => { toggleWorkflowSidebar(); });
        document.getElementById('auto-evt-button-29')?.addEventListener('click', (event) => { window.location.reload(); });

        // Restore saved sidebar collapse state
        const savedState = localStorage.getItem('rep_workflow_sidebar_collapsed');
        const sidebar = document.getElementById('workflowSidebar');
        if (sidebar && savedState === '1') {
            sidebar.classList.add('collapsed');
            const icon = document.getElementById('sidebarToggleIcon');
            if (icon) icon.className = 'ph ph-caret-double-right';
        }
    };

    function toggleWorkflowSidebar() {
        const sidebar = document.getElementById('workflowSidebar');
        if (!sidebar) return;
        const isCollapsed = sidebar.classList.toggle('collapsed');
        localStorage.setItem('rep_workflow_sidebar_collapsed', isCollapsed ? '1' : '0');
        const icon = document.getElementById('sidebarToggleIcon');
        if (icon) {
            icon.className = isCollapsed ? 'ph ph-caret-double-right' : 'ph ph-sidebar-simple';
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCentrallyRegisteredListeners);
    } else {
        initCentrallyRegisteredListeners();
    }
    window.toggleWorkflowSidebar = toggleWorkflowSidebar;
window.buildAccountOptions = buildAccountOptions;
    window.getAccountIdByCode = getAccountIdByCode;
    window.fetchRoutesList = fetchRoutesList;
    window.changePage = changePage;
    window.filterLeftPane = filterLeftPane;
    window.searchRouteList = searchRouteList;
    window.clearFilters = clearFilters;
    window.openRouteSwitcherModal = openRouteSwitcherModal;
    window.closeRouteSwitcherModal = closeRouteSwitcherModal;
    window.openCreateRouteModal = openCreateRouteModal;
    window.closeCreateRouteModal = closeCreateRouteModal;
    window.searchRouteSwitcherList = searchRouteSwitcherList;
    window.selectRouteFromSwitcher = selectRouteFromSwitcher;
    window.goBackToRoutes = goBackToRoutes;
    window.fetchSecure = fetchSecure;
    window.onRouteDataChanged = onRouteDataChanged;
    window.updateSidebarProgress = updateSidebarProgress;
    window.updateWizardProgress = updateWizardProgress;
    window.loadRouteDetails = loadRouteDetails;
    window.switchRouteTab = switchRouteTab;
    window.loadTab1Details = loadTab1Details;
    window.saveRouteNotes = saveRouteNotes;

    window.loadOutstandingBillsChecklist = loadOutstandingBillsChecklist;
    window.filterCreditBillsList = filterCreditBillsList;
    window.toggleCreditBillSelection = toggleCreditBillSelection;
    window.loadAdjustmentsStage = loadAdjustmentsStage;
    window.detachInvoice = detachInvoice;
    window.submitAdjustmentsLogisticsArrange = submitAdjustmentsLogisticsArrange;
    window.loadLoadingStage = loadLoadingStage;
    window.renderLoadingStageUI = renderLoadingStageUI;
    window.filterLoadingProductsBySearch = filterLoadingProductsBySearch;
    window.adjustLoadingQty = adjustLoadingQty;
    window.toggleItemVerified = toggleItemVerified;
    window.verifyAllLoadingItems = verifyAllLoadingItems;
    window.completeLoadingVerification = completeLoadingVerification;
    window.openReplaceProductModal = openReplaceProductModal;
    window.closeReplaceProductModal = closeReplaceProductModal;
    window.handleReplacementSearchInput = handleReplacementSearchInput;
    window.onSelectReplacementRow = onSelectReplacementRow;
    window.selectReplacementProduct = selectReplacementProduct;
    window.submitProductSubstitution = submitProductSubstitution;
    window.loadVarianceAdjustmentStage = loadVarianceAdjustmentStage;
    window.renderVarianceReconciliation = renderVarianceReconciliation;
    window.updateRemoveCompletelyChoice = updateRemoveCompletelyChoice;
    window.applyProductSubstitution = applyProductSubstitution;
    window.updateInvoiceAllocation = updateInvoiceAllocation;
    window.autoDistributeVariance = autoDistributeVariance;
    window.submitVarianceAdjustments = submitVarianceAdjustments;
    window.filterVarianceBySearch = filterVarianceBySearch;
    window.printLoadingSheet = printLoadingSheet;
    window.loadDispatchStage = loadDispatchStage;
    window.loadDeliveryLiveStage = loadDeliveryLiveStage;
    window.openServerDeliveryProcessModal = openServerDeliveryProcessModal;
    window.filterSdpItems = filterSdpItems;
    window.updateSdpBalance = updateSdpBalance;
    window.recalculateSdpInvoiceTotal = recalculateSdpInvoiceTotal;
    window.closeServerDeliveryProcessModal = closeServerDeliveryProcessModal;
    window.addSdpChequeRow = addSdpChequeRow;
    window.submitServerDeliveryProcess = submitServerDeliveryProcess;
    window.applyDefensiveGuard = applyDefensiveGuard;
    window.loadTab8Reconciliation = loadTab8Reconciliation;
    window.recalculateDenominations = recalculateDenominations;
    window.calculateCashVariance = calculateCashVariance;
    window.toggleReconChequeApproval = toggleReconChequeApproval;
    window.saveReconciliationDraft = saveReconciliationDraft;
    window.loadTab9ReturnStock = loadTab9ReturnStock;
    window.saveReturnStockDraft = saveReturnStockDraft;
    window.loadTab10Accounting = loadTab10Accounting;
    window.loadTab11Finalize = loadTab11Finalize;
    window.saveAccountingDraft = saveAccountingDraft;
    window.checkSettleVerification = checkSettleVerification;
    window.switchSettleDeTab = switchSettleDeTab;
    window.renderSettleDeAccountSelect = renderSettleDeAccountSelect;
    window.renderSettleDoubleEntries = renderSettleDoubleEntries;
    window.submitFinalSettle = submitFinalSettle;
    window.openBalancingReportModal = openBalancingReportModal;
    window.closeBalancingReportModal = closeBalancingReportModal;
    window.printBalancingReportFromModal = printBalancingReportFromModal;
    window.printBalancingReport = printBalancingReport;
    window.printLoadingSheetSpreadsheet = printLoadingSheetSpreadsheet;
    window.exportCSV = exportCSV;
    window.getDataTypeFromStatus = getDataTypeFromStatus;
    window.advanceRouteStatus = advanceRouteStatus;
    window.redirectToAddInvoice = redirectToAddInvoice;
    window.printRouteInvoices = printRouteInvoices;
    window.printRouteInvoicesSummary = printRouteInvoicesSummary;
    window.printReturnStock = printReturnStock;
    window.openInvoiceSlider = openInvoiceSlider;
    window.closeInvoiceSlider = closeInvoiceSlider;
    window.deleteSalesOrder = deleteSalesOrder;
    window.openMapModal = openMapModal;
    window.closeMapModal = closeMapModal;
    window.initRoutePathMap = initRoutePathMap;
    window.clearRoutePathMap = clearRoutePathMap;
    window.loadRoutePath = loadRoutePath;
    window.renderRoutePath = renderRoutePath;
    window.getEligibleBindingRoutes = getEligibleBindingRoutes;
    window.openRouteBindingModal = openRouteBindingModal;
    window.closeRouteBindingModal = closeRouteBindingModal;
    window.addBindingSlot = addBindingSlot;
    window.removeBindingSlot = removeBindingSlot;
    window.onBindingSlotRouteSelect = onBindingSlotRouteSelect;
    window.submitRouteBinding = submitRouteBinding;
    window.unbindActiveRoute = unbindActiveRoute;
    // ==========================================
    // MARKET RETURNS WORKFLOW IMPLEMENTATION
    // ==========================================
    let mrCustomerProducts = [];
    let mrRouteCustomersList = [];
    let mrActiveFocusedRow = null;

    function showLoader() {
        const loader = document.getElementById('midLoader');
        if (loader) loader.style.display = 'block';
    }

    function hideLoader() {
        const loader = document.getElementById('midLoader');
        if (loader) loader.style.display = 'none';
    }

    function loadMarketReturnsStage(routeId) {
        showLoader();
        
        // Reset Form
        document.getElementById('marketReturnForm')?.reset();
        document.getElementById('mrLinesBody').innerHTML = '';
        document.getElementById('mrCustomerInfoCard').style.display = 'none';
        document.getElementById('mrGrandTotalText').innerText = 'Rs 0.00';
        
        const searchInput = document.getElementById('mrCustomerSearchInput');
        if (searchInput) searchInput.value = '';
        const select = document.getElementById('mrCustomerSelect');
        if (select) select.value = '';

        resetMrHistoryPanel();

        // 1. Fetch Route Customers
        fetchSecure('<?= APP_URL ?>/RepTracking/api_get_route_customers/' + routeId)
            .then(res => res.json())
            .then(data => {
                mrRouteCustomersList = data.customers || [];
                
                // 2. Fetch Existing Market Returns for Route
                return fetchSecure('<?= APP_URL ?>/RepTracking/api_get_market_returns/' + routeId);
            })
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('mrSavedNotesBody');
                if (tbody) {
                    tbody.innerHTML = '';
                    const returns = data.returns || [];
                    if (returns.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: var(--t-secondary); padding: 20px;">No market returns logged for this route yet.</td></tr>';
                    } else {
                        returns.forEach(r => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td style="font-family: var(--f-mono); font-weight: bold;">${r.credit_note_number}</td>
                                <td>${r.customer_name}</td>
                                <td>${r.note_date}</td>
                                <td style="text-align: right; font-weight: bold; color: var(--c-green);">Rs ${parseFloat(r.total_amount).toFixed(2)}</td>
                                <td style="text-align: center;">
                                    <button type="button" onclick="window.open('<?= APP_URL ?>/RepTracking/print_market_return/${r.id}', '_blank')" style="padding: 4px 8px; background: var(--c-surface2); border: 0.5px solid var(--c-separator); border-radius: var(--r-xs); cursor: pointer; font-size: 11px; font-weight: 600;"><i class="ph ph-printer"></i> Print</button>
                                </td>
                            `;
                            tbody.appendChild(tr);
                        });
                    }
                }
                hideLoader();
            })
            .catch(err => {
                console.error("Error loading Market Returns Stage:", err);
                hideLoader();
            });
    }

    function showMrCustomerSuggestions(input) {
        closeAllMrSuggestions();
        const container = document.getElementById('mrCustomerSuggestions');
        if (!container) return;
        
        container.innerHTML = '';
        container.style.display = 'block';

        if (mrRouteCustomersList.length === 0) {
            container.innerHTML = '<div style="padding:8px 12px; color:var(--t-secondary); font-size:12px;">No customers on this route.</div>';
            return;
        }

        mrRouteCustomersList.forEach(c => {
            const div = document.createElement('div');
            div.className = 'autocomplete-item';
            div.style.padding = '8px 12px';
            div.style.cursor = 'pointer';
            div.style.borderBottom = '0.5px solid var(--c-separator)';
            div.innerHTML = `<strong>${c.name}</strong> <span style="font-size:10.5px; color:var(--t-secondary); float:right;">Outstanding: Rs ${parseFloat(c.outstanding || 0).toFixed(0)}</span>`;
            
            div.addEventListener('mousedown', function(e) {
                e.preventDefault(); // prevent losing focus
                input.value = c.name;
                document.getElementById('mrCustomerSelect').value = c.id;
                container.style.display = 'none';
                onMrCustomerChange();
            });
            container.appendChild(div);
        });
    }

    function filterMrCustomerSuggestions(input) {
        const filter = input.value.toUpperCase();
        const container = document.getElementById('mrCustomerSuggestions');
        if (!container) return;
        
        const items = container.querySelectorAll('.autocomplete-item');
        items.forEach(item => {
            const txt = item.textContent || item.innerText;
            if (txt.toUpperCase().indexOf(filter) > -1) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function onMrCustomerChange() {
        const customerId = parseInt(document.getElementById('mrCustomerSelect').value || 0);
        
        const linesBody = document.getElementById('mrLinesBody');
        linesBody.innerHTML = '';
        document.getElementById('mrGrandTotalText').innerText = 'Rs 0.00';
        resetMrHistoryPanel();

        const infoCard = document.getElementById('mrCustomerInfoCard');
        if (customerId <= 0) {
            infoCard.style.display = 'none';
            mrCustomerProducts = [];
            return;
        }

        // Display customer outstanding / credit limit info card
        const cust = mrRouteCustomersList.find(c => parseInt(c.id) === customerId);
        if (cust) {
            document.getElementById('mrCustName').innerText = cust.name;
            document.getElementById('mrCustOutstanding').innerText = 'Rs ' + parseFloat(cust.outstanding || 0).toFixed(2);
            document.getElementById('mrCustLimit').innerText = 'Rs ' + parseFloat(cust.credit_limit || 0).toFixed(2);
            document.getElementById('mrCustPhone').innerText = cust.phone || 'N/A';
            infoCard.style.display = 'block';
        }

        // Fetch eligible returnable products for customer
        fetchSecure('<?= APP_URL ?>/creditnote/get_customer_products?customer_id=' + customerId)
            .then(res => res.json())
            .then(data => {
                mrCustomerProducts = data || [];
                addMrLine(); // insert first line automatically
            })
            .catch(err => console.error("Error fetching customer returnable products:", err));
    }

    function addMrLine() {
        const customerId = parseInt(document.getElementById('mrCustomerSelect').value || 0);
        if (customerId <= 0) {
            alert('Please select a Customer first.');
            return;
        }

        const tbody = document.getElementById('mrLinesBody');
        const tr = document.createElement('tr');
        
        tr.innerHTML = `
            <td style="position: relative; overflow: visible; z-index: 9999;">
                <input type="text" class="mr-product-input" placeholder="Type product name to search..." onfocus="showMrSuggestions(this)" oninput="filterMrSuggestions(this)" style="width:100%; padding: 6px 10px; border: 0.5px solid var(--c-separator); border-radius: var(--r-xs); background: var(--c-surface2); color: var(--t-primary); outline: none;" required autocomplete="off">
                <div class="autocomplete-items" style="display: none; position: absolute; left: 0; right: 0; z-index: 99999; background: var(--c-surface); border: 0.5px solid var(--c-separator); border-radius: var(--r-xs); max-height: 180px; overflow-y: auto; box-shadow: var(--shadow-md);"></div>
                <input type="hidden" class="mr-item-id-hidden">
                <input type="hidden" class="mr-var-opt-id-hidden">
                <input type="hidden" class="mr-desc-hidden">
                <input type="hidden" class="mr-invoice-id-hidden">
                <input type="hidden" class="mr-invoice-item-id-hidden">
            </td>
            <td>
                <div style="position: relative;">
                    <input type="number" class="mr-qty-input" value="1" min="1" step="1" oninput="validateMrQty(this); calcMrTotals();" style="width:100%; padding: 6px 10px; border: 0.5px solid var(--c-separator); border-radius: var(--r-xs); background: var(--c-surface2); color: var(--t-primary); outline: none; text-align: right;" required>
                    <span class="max-qty-badge" style="position: absolute; right: 5px; top: -18px; font-size: 9px; background: #64748b; color: #fff; padding: 1px 4px; border-radius: 3px; display: none;"></span>
                </div>
            </td>
            <td><input type="number" class="mr-price-input" value="0.00" min="0" step="0.01" oninput="calcMrTotals();" style="width:100%; padding: 6px 10px; border: 0.5px solid var(--c-separator); border-radius: var(--r-xs); background: var(--c-surface2); color: var(--t-primary); outline: none; text-align: right;" required></td>
            <td>
                <select class="mr-condition-select" onchange="calcMrTotals()" style="width:100%; padding: 6px 10px; border: 0.5px solid var(--c-separator); border-radius: var(--r-xs); background: var(--c-surface2); color: var(--t-primary); outline: none;">
                    <option value="Good" style="color: #2e7d32; font-weight: bold;">Good (Restock)</option>
                    <option value="Damaged" style="color: #d32f2f; font-weight: bold;">Damaged (Loss)</option>
                </select>
            </td>
            <td><input type="text" class="mr-remarks-input" placeholder="Reason..." style="width:100%; padding: 6px 10px; border: 0.5px solid var(--c-separator); border-radius: var(--r-xs); background: var(--c-surface2); color: var(--t-primary); outline: none;"></td>
            <td style="text-align: center; vertical-align: middle;">
                <button type="button" onclick="removeMrLine(this)" style="border: none; background: transparent; color: #ef4444; font-size: 16px; cursor: pointer;"><i class="ph ph-trash"></i></button>
            </td>
        `;
        
        tbody.appendChild(tr);
        renumberMrRows();
    }

    function removeMrLine(btn) {
        const row = btn.closest('tr');
        row.remove();
        
        if (mrActiveFocusedRow === row) {
            mrActiveFocusedRow = null;
            resetMrHistoryPanel();
        }
        
        renumberMrRows();
        calcMrTotals();
    }

    function renumberMrRows() {
        // Reserved for line renumbering if required
    }

    function showMrSuggestions(input) {
        const row = input.closest('tr');
        mrActiveFocusedRow = row;
        
        const itemId = row.querySelector('.mr-item-id-hidden').value;
        const varId = row.querySelector('.mr-var-opt-id-hidden').value;
        if (itemId) {
            fetchMrHistoryPrices(itemId, varId);
        } else {
            resetMrHistoryPanel();
        }

        closeAllMrSuggestions();
        const container = input.nextElementSibling;
        container.innerHTML = '';
        container.style.display = 'block';

        mrCustomerProducts.forEach(prod => {
            const div = document.createElement('div');
            div.className = 'autocomplete-item';
            div.style.padding = '8px 12px';
            div.style.cursor = 'pointer';
            div.style.borderBottom = '0.5px solid var(--c-separator)';
            div.innerHTML = `<strong>${prod.product_name}</strong> <span style="font-size:10px; color:var(--t-secondary); float:right;">Max: ${parseFloat(prod.max_returnable).toFixed(0)}</span>`;
            
            div.addEventListener('mousedown', function(e) {
                e.preventDefault(); // prevent losing focus
                input.value = prod.product_name;
                row.querySelector('.mr-item-id-hidden').value = prod.item_id;
                row.querySelector('.mr-var-opt-id-hidden').value = prod.variation_option_id || '';
                row.querySelector('.mr-desc-hidden').value = prod.product_name;
                
                const badge = row.querySelector('.max-qty-badge');
                badge.innerText = 'Max: ' + parseFloat(prod.max_returnable).toFixed(0);
                badge.style.display = 'block';
                row.querySelector('.mr-qty-input').max = prod.max_returnable;
                
                container.style.display = 'none';
                fetchMrHistoryPrices(prod.item_id, prod.variation_option_id);
            });
            container.appendChild(div);
        });
    }

    function filterMrSuggestions(input) {
        const filter = input.value.toUpperCase();
        const container = input.nextElementSibling;
        const items = container.querySelectorAll('.autocomplete-item');
        
        items.forEach(item => {
            const txt = item.textContent || item.innerText;
            if (txt.toUpperCase().indexOf(filter) > -1) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function closeAllMrSuggestions() {
        document.querySelectorAll('#mrLinesTable .autocomplete-items').forEach(c => {
            c.style.display = 'none';
        });
        const custSug = document.getElementById('mrCustomerSuggestions');
        if (custSug) custSug.style.display = 'none';
    }

    function validateMrQty(input) {
        const val = parseFloat(input.value || 0);
        const max = parseFloat(input.max || 999999);
        if (val > max) {
            alert('Return quantity cannot exceed the purchased quantity limit of ' + max);
            input.value = max;
        }
    }

    function fetchMrHistoryPrices(itemId, varOptId) {
        const customerId = parseInt(document.getElementById('mrCustomerSelect').value || 0);
        if (customerId <= 0) return;

        const prodVal = itemId + '|' + (varOptId || '');
        
        document.getElementById('mrHistoryPlaceholder').style.display = 'none';
        const tbody = document.getElementById('mrHistoryBody');
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding:10px;">Loading purchase history...</td></tr>';
        document.getElementById('mrHistoryTable').style.display = 'table';

        fetchSecure(`<?= APP_URL ?>/creditnote/get_product_sale_history?customer_id=${customerId}&product_val=${prodVal}`)
            .then(res => res.json())
            .then(history => {
                tbody.innerHTML = '';
                if (history.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding:10px; color:var(--t-secondary);">No previous invoices found for this customer.</td></tr>';
                    return;
                }
                history.forEach(h => {
                    const maxRet = parseFloat(h.max_returnable);
                    if (maxRet <= 0) return;

                    const tr = document.createElement('tr');
                    tr.style.cursor = 'pointer';
                    tr.innerHTML = `
                        <td style="font-family: var(--f-mono); font-weight: bold; color: var(--c-blue);">${h.invoice_number}</td>
                        <td>${h.invoice_date}</td>
                        <td style="text-align: right;">${parseFloat(h.quantity).toFixed(0)}</td>
                        <td style="text-align: right; font-weight: bold;">Rs ${parseFloat(h.unit_price).toFixed(2)}</td>
                        <td style="text-align: center;">
                            <button type="button" class="btn" style="padding: 2px 6px; font-size: 10px; background: var(--c-blue); color: #fff; border: none; border-radius: 3px;" onclick="applyMrHistoryPrice(${h.invoice_id}, ${h.invoice_item_id}, ${h.unit_price})">Select</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
                if (tbody.innerHTML === '') {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding:10px; color:var(--t-secondary);">All previously purchased units have been fully returned.</td></tr>';
                }
            })
            .catch(err => {
                console.error("Error fetching historical prices:", err);
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding:10px; color:#ef4444;">Failed to load history.</td></tr>';
            });
    }

    function applyMrHistoryPrice(invoiceId, invoiceItemId, price) {
        if (!mrActiveFocusedRow) {
            alert('Please click on the product search input field of the line you wish to modify first.');
            return;
        }
        
        mrActiveFocusedRow.querySelector('.mr-price-input').value = parseFloat(price).toFixed(2);
        mrActiveFocusedRow.querySelector('.mr-invoice-id-hidden').value = invoiceId;
        mrActiveFocusedRow.querySelector('.mr-invoice-item-id-hidden').value = invoiceItemId;
        
        calcMrTotals();
    }

    function resetMrHistoryPanel() {
        document.getElementById('mrHistoryPlaceholder').style.display = 'block';
        document.getElementById('mrHistoryTable').style.display = 'none';
        document.getElementById('mrHistoryBody').innerHTML = '';
    }

    function calcMrTotals() {
        let totalMrValue = 0;
        document.querySelectorAll('#mrLinesBody tr').forEach(row => {
            const qty = parseFloat(row.querySelector('.mr-qty-input').value || 0);
            const price = parseFloat(row.querySelector('.mr-price-input').value || 0);
            totalMrValue += (qty * price);
        });
        document.getElementById('mrGrandTotalText').innerText = 'Rs ' + totalMrValue.toFixed(2);
    }

    function submitMarketReturn(event) {
        event.preventDefault();
        
        const customerId = parseInt(document.getElementById('mrCustomerSelect').value || 0);
        if (customerId <= 0) {
            alert('Please search and select a Customer.');
            return;
        }

        const rows = document.querySelectorAll('#mrLinesBody tr');
        if (rows.length === 0) {
            alert('Please add at least one return item line.');
            return;
        }

        let items = [];
        let isValid = true;

        rows.forEach((row, i) => {
            const itemId = parseInt(row.querySelector('.mr-item-id-hidden').value || 0);
            const varOptId = row.querySelector('.mr-var-opt-id-hidden').value;
            const desc = row.querySelector('.mr-desc-hidden').value;
            const qty = parseFloat(row.querySelector('.mr-qty-input').value || 0);
            const price = parseFloat(row.querySelector('.mr-price-input').value || 0);
            const condition = row.querySelector('.mr-condition-select').value;
            const remarks = row.querySelector('.mr-remarks-input').value;
            const invoiceId = row.querySelector('.mr-invoice-id-hidden').value;
            const invoiceItemId = row.querySelector('.mr-invoice-item-id-hidden').value;

            if (itemId <= 0 || qty <= 0 || price < 0) {
                alert(`Line #${i + 1} contains invalid or incomplete item selections, quantities, or prices.`);
                isValid = false;
                return;
            }

            items.push({
                item_id: itemId,
                var_opt_id: varOptId || null,
                desc: desc,
                qty: qty,
                price: price,
                condition: condition,
                remarks: remarks,
                invoice_id: invoiceId || null,
                invoice_item_id: invoiceItemId || null
            });
        });

        if (!isValid) return;

        const payload = {
            route_id: currentRouteId,
            customer_id: customerId,
            date: document.getElementById('mrNoteDate').value,
            items: items,
            csrf_token: CSRF_TOKEN
        };

        showLoader();
        fetchSecure('<?= APP_URL ?>/RepTracking/api_save_market_return', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            hideLoader();
            if (data.status === 'success') {
                showMrnNotificationToast('success', data.message || 'Market Return Note saved successfully.');
                loadMarketReturnsStage(currentRouteId);
                window.open('<?= APP_URL ?>/RepTracking/print_market_return/' + data.return_note_id, '_blank');
            } else {
                showMrnNotificationToast('error', data.message || 'Failed to save return note.');
            }
        })
        .catch(err => {
            console.error("Error saving market return:", err);
            hideLoader();
            showMrnNotificationToast('error', 'Network error. Try again.');
        });
    }

    function showMrnNotificationToast(status, message) {
        const container = document.getElementById('successToast') || document.getElementById('errorToast');
        if (container) {
            container.querySelector('.ios-toast-message').innerText = message;
            container.style.opacity = '1';
            container.style.transform = 'translate(-50%, 0) scale(1)';
            setTimeout(() => {
                container.style.opacity = '0';
                container.style.transform = 'translate(-50%, -20px) scale(0.95)';
            }, 5000);
        } else {
            alert(status.toUpperCase() + ': ' + message);
        }
    }

    // Prevent clicks inside inputs/suggestions from bubbling up to document click listener
    document.addEventListener('click', function(e) {
        if (e.target.id === 'mrCustomerSearchInput' || e.target.closest('#mrCustomerSuggestions')) {
            return;
        }
        if (e.target.classList.contains('mr-product-input') || e.target.closest('.autocomplete-items')) {
            return;
        }
        
        // Clicked outside - close all suggestions
        closeAllMrSuggestions();
        const custSug = document.getElementById('mrCustomerSuggestions');
        if (custSug) custSug.style.display = 'none';
    });

    // Expose functions to global context
    window.loadMarketReturnsStage = loadMarketReturnsStage;
    window.showMrCustomerSuggestions = showMrCustomerSuggestions;
    window.filterMrCustomerSuggestions = filterMrCustomerSuggestions;
    window.onMrCustomerChange = onMrCustomerChange;
    window.addMrLine = addMrLine;
    window.removeMrLine = removeMrLine;
    window.showMrSuggestions = showMrSuggestions;
    window.filterMrSuggestions = filterMrSuggestions;
    window.validateMrQty = validateMrQty;
    window.applyMrHistoryPrice = applyMrHistoryPrice;
    window.calcMrTotals = calcMrTotals;
    window.submitMarketReturn = submitMarketReturn;

    window.unbindCombinedRoute = unbindCombinedRoute;
    window.openAttachInvoiceModal = openAttachInvoiceModal;
    window.closeAttachInvoiceModal = closeAttachInvoiceModal;
    window.searchUnattachedInvoices = searchUnattachedInvoices;
    window.confirmAttachInvoices = confirmAttachInvoices;
    window.resetSalesOrderFilters = resetSalesOrderFilters;
    window.toggleUnattachedCardCheck = toggleUnattachedCardCheck;
    window.updateAttachSelectedCount = updateAttachSelectedCount;
    window.toggleSelectAllUnattached = toggleSelectAllUnattached;
    window.toggleDotsMenu = toggleDotsMenu;
    window.closeAllDotsMenus = closeAllDotsMenus;
    window.updateSingleInvoiceDeliveryStatus = updateSingleInvoiceDeliveryStatus;
    window.editSalesOrder = editSalesOrder;
    window.printInvoice = printInvoice;
    window.viewCustomerProfile = viewCustomerProfile;
    window.downloadInvoicePdf = downloadInvoicePdf;
    window.exportInvoiceExcel = exportInvoiceExcel;
    window.confirmDeleteSalesOrder = confirmDeleteSalesOrder;
    window.closeDeleteConfirmModal = closeDeleteConfirmModal;
    window.submitDeleteSalesOrder = submitDeleteSalesOrder;
    window.scramblePassword = scramblePassword;
    window.refreshDeleteRouteCaptcha = refreshDeleteRouteCaptcha;
    window.openDeleteRouteModal = openDeleteRouteModal;
    window.closeDeleteRouteModal = closeDeleteRouteModal;
    window.submitDeleteRoute = submitDeleteRoute;
    window.openMoveInvoiceModal = openMoveInvoiceModal;
    window.closeMoveInvoiceModal = closeMoveInvoiceModal;
    window.submitMoveSalesOrder = submitMoveSalesOrder;
})();
</script>