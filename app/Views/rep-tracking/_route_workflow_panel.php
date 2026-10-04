<?php
/**
 * Route Workflow Panel
 * Contains: Header Metadata Bar, Route Workspace Tabs, Collapsible Workflow Sidebar, and All 12 Workflow Tab Panels
 */
?>
    <div class="pane-middle">

        <!-- Route Workspace Tabs -->
        <div class="scroll-tabs" id="routeWorkspaceTabs" style="display: none; border-bottom: 2px solid #cbd5e1; margin-bottom: 0;">
            <button id="auto-evt-button-4" class="scroll-tab-btn active" ><i class="ph ph-clipboard-text"></i> Route Details</button>
            <button id="auto-evt-button-6" class="scroll-tab-btn" ><i class="ph ph-file-text"></i> Bill Adjustments</button>
            <button id="auto-evt-button-7" class="scroll-tab-btn" ><i class="ph ph-truck"></i> Loading Checklist</button>
            <button id="auto-evt-button-8" class="scroll-tab-btn" ><i class="ph ph-scales"></i> Variance Audit</button>
            <button id="auto-evt-button-9" class="scroll-tab-btn" ><i class="ph ph-map-trifold"></i> Delivery Arrangement</button>
            <button id="btnTabCreditBills" class="scroll-tab-btn" ><i class="ph ph-receipt"></i> Credit Bills</button>
            <button id="auto-evt-button-11" class="scroll-tab-btn" ><i class="ph ph-steering-wheel"></i> Delivery Execution</button>
            <button id="btnTabExpenses" class="scroll-tab-btn" ><i class="ph ph-receipt"></i> Expenses</button>
            <button id="auto-evt-button-10" class="scroll-tab-btn" ><i class="ph ph-currency-dollar"></i> Reconciliation</button>
            <button id="auto-evt-button-12" class="scroll-tab-btn" ><i class="ph ph-package"></i> Return Stock</button>
            <button id="auto-evt-button-13" class="scroll-tab-btn" ><i class="ph ph-credit-card"></i> Payments</button>
            <button id="btnTabMarketReturns" class="scroll-tab-btn" ><i class="ph ph-arrow-counter-clockwise"></i> Market Returns</button>
            <button id="btnTabFinalize" class="scroll-tab-btn" ><i class="ph ph-flag-checkered"></i> Finalize</button>
        </div>

                <!-- Workspace Layout Container (Sidebar + Content Body) -->
        <div id="workspaceLayoutWrapper" style="display: none; flex: 1; flex-direction: row; min-height: 0; width: 100%; position: relative;">
            <!-- Left Side: Workflow Sidebar (Hover Expandable with Icons) -->
            <div class="workflow-sidebar" id="workflowSidebar">
                <div class="workflow-sidebar-steps">
                    <div class="sidebar-step-item active" id="sb-step-1" title="Route Details">
                        <div class="step-dot"><i class="ph-bold ph-clipboard-text"></i></div>
                        <div class="step-info">
                            <span class="step-title">Route Details</span>
                            <span class="step-desc">Representative & Odo</span>
                        </div>
                    </div>
                    <div class="sidebar-step-item" id="sb-step-3" title="Bill Adjustments">
                        <div class="step-dot"><i class="ph-bold ph-file-text"></i></div>
                        <div class="step-info">
                            <span class="step-title">Bill Adjustments</span>
                            <span class="step-desc">Attach/detach SOs</span>
                        </div>
                    </div>
                    <div class="sidebar-step-item" id="sb-step-4" title="Loading Checklist">
                        <div class="step-dot"><i class="ph-bold ph-truck"></i></div>
                        <div class="step-info">
                            <span class="step-title">Loading Checklist</span>
                            <span class="step-desc">Verify loaded stock</span>
                        </div>
                    </div>
                    <div class="sidebar-step-item" id="sb-step-5" title="Variance Audit">
                        <div class="step-dot"><i class="ph-bold ph-scales"></i></div>
                        <div class="step-info">
                            <span class="step-title">Variance Audit</span>
                            <span class="step-desc">Confirm product variances</span>
                        </div>
                    </div>
                    <div class="sidebar-step-item" id="sb-step-6" title="Delivery Arrangement">
                        <div class="step-dot"><i class="ph-bold ph-map-trifold"></i></div>
                        <div class="step-info">
                            <span class="step-title">Delivery Arrange</span>
                            <span class="step-desc">Assign driver & vehicle</span>
                        </div>
                    </div>
                    <div class="sidebar-step-item" id="sb-step-14" title="Credit Bills">
                        <div class="step-dot"><i class="ph-bold ph-receipt"></i></div>
                        <div class="step-info">
                            <span class="step-title">Credit Bills</span>
                            <span class="step-desc">Select credit invoices</span>
                        </div>
                    </div>
                    <div class="sidebar-step-item" id="sb-step-8" title="Delivery Execution">
                        <div class="step-dot"><i class="ph-bold ph-steering-wheel"></i></div>
                        <div class="step-info">
                            <span class="step-title">Delivery Execution</span>
                            <span class="step-desc">Track live status</span>
                        </div>
                    </div>
                    <div class="sidebar-step-item" id="sb-step-12" title="Expenses">
                        <div class="step-dot"><i class="ph-bold ph-receipt"></i></div>
                        <div class="step-info">
                            <span class="step-title">Expenses</span>
                            <span class="step-desc">Record route expenses</span>
                        </div>
                    </div>
                    <div class="sidebar-step-item" id="sb-step-7" title="Reconciliation">
                        <div class="step-dot"><i class="ph-bold ph-currency-dollar"></i></div>
                        <div class="step-info">
                            <span class="step-title">Reconciliation</span>
                            <span class="step-desc">Discrepancies & cash</span>
                        </div>
                    </div>
                    <div class="sidebar-step-item" id="sb-step-9" title="Return Stock">
                        <div class="step-dot"><i class="ph-bold ph-package"></i></div>
                        <div class="step-info">
                            <span class="step-title">Return Stock</span>
                            <span class="step-desc">Verify returned items</span>
                        </div>
                    </div>
                    <div class="sidebar-step-item" id="sb-step-10" title="Payments">
                        <div class="step-dot"><i class="ph-bold ph-credit-card"></i></div>
                        <div class="step-info">
                            <span class="step-title">Payments</span>
                            <span class="step-desc">Verify & approve payments</span>
                        </div>
                    </div>
                    <div class="sidebar-step-item" id="sb-step-13" title="Market Returns">
                        <div class="step-dot"><i class="ph-bold ph-arrow-counter-clockwise"></i></div>
                        <div class="step-info">
                            <span class="step-title">Market Returns</span>
                            <span class="step-desc">Representative returns</span>
                        </div>
                    </div>
                    <div class="sidebar-step-item" id="sb-step-11" title="Finalize">
                        <div class="step-dot"><i class="ph-bold ph-flag-checkered"></i></div>
                        <div class="step-info">
                            <span class="step-title">Finalize</span>
                            <span class="step-desc">Settle & Close Route</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Content Area (previously #workspaceBody) -->
            <div style="flex:1; margin-left: 60px; overflow-y:auto; position:relative; background:var(--c-bg); min-width:0;" id="workspaceBody">
                <!-- Loading Indicator -->
                <div id="midLoader" style="display:none; position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); text-align:center; font-weight:bold; color:var(--c-blue); z-index: 10;">
                    Loading Workspace Information... <i class="fa-solid fa-spinner fa-spin"></i>
                </div>

                <!-- Dynamic Stage Containers -->
                <div id="stageContentWrapper" style="display:none; padding: 16px 20px;">
                

                <!-- COMPLETED ARCHIVE OPTIONS (READ ONLY AT THE TOP IF FINALIZED) -->
                <div id="completedArchiveBanner" style="display:none; background:#f1f5f9; border:1px solid #cbd5e1; border-radius:8px; padding:15px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <h4 style="margin:0; font-size:14px; font-weight:bold; color:#2e7d32;"><i class="ph ph-flag-checkered"></i> Route Settle Balancing Finalized</h4>
                        <p style="margin:5px 0 0 0; font-size:12px; color:#666;">This route is read-only. All transactions, inventories, and GL postings are successfully finalized.</p>
                    </div>
                    <div style="display:flex; gap:10px;">
                        <button id="auto-evt-button-15"  style="padding:8px 12px; background:#0066cc; color:#fff; border:none; border-radius:4px; font-weight:bold; cursor:pointer; font-size:12px;"><i class="ph ph-file-text"></i> View Route Summary</button>
                        <button id="auto-evt-button-16"  style="padding:8px 12px; background:#e2e8f0; color:#333; border:none; border-radius:4px; font-weight:bold; cursor:pointer; font-size:12px;"><i class="ph ph-chart-bar"></i> Print Spreadsheet</button>
                        <button id="auto-evt-button-17"  style="padding:8px 12px; background:#e2e8f0; color:#333; border:none; border-radius:4px; font-weight:bold; cursor:pointer; font-size:12px;"><i class="ph ph-truck"></i> Print Loading Summary</button>
                        <button id="auto-evt-button-18"  style="padding:8px 12px; background:#e2e8f0; color:#333; border:none; border-radius:4px; font-weight:bold; cursor:pointer; font-size:12px;"><i class="ph ph-download"></i> Export CSV</button>
                    </div>
                </div>

                <!-- TAB 1: DETAILS -->
                <div class="workspace-tab-panel" id="tabpanel-1" style="display:none; position:relative; overflow:hidden; margin:-16px; height:calc(100vh - 120px); min-height:500px;">

                    <!-- ── FULL SCREEN MAP ── -->
                    <div style="position:absolute; inset:0; right:480px; z-index:0;">
                        <div id="tab1MapEmptyOverlay" style="position:absolute; inset:0; z-index:5; display:flex; flex-direction:column; align-items:center; justify-content:center; background:#f8fafc; gap:14px;">
                            <i class="ph ph-map-trifold" style="font-size:64px; color:#cbd5e1;"></i>
                            <span id="tab1MapEmptyMsg" style="font-size:14px; font-weight:600; color:#64748b; letter-spacing:0.02em;">Select a route to view GPS path</span>
                        </div>
                        <div id="tab1RoutePathMap" style="height:100%; width:100%;"></div>
                    </div>

                    <!-- ── FLOATING TOP-RIGHT MENU BUTTON & POPUP (On Map) ── -->
                    <div style="position:absolute; top:14px; right:494px; z-index:30;">
                        <button id="tab1MapMenuBtn" type="button" onclick="toggleTab1MapMenu()" style="display:inline-flex; align-items:center; gap:8px; padding:10px 16px; background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; font-size:13px; font-weight:700; color:#0f172a; cursor:pointer; box-shadow:0 4px 14px rgba(0,0,0,0.08); transition:all 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#ffffff'">
                            <i class="ph ph-dots-three-vertical" style="font-size:18px;"></i> Actions & Reports
                            <i class="ph ph-caret-down" style="font-size:12px; color:#64748b;"></i>
                        </button>

                        <!-- Dropdown Menu List -->
                        <div id="tab1MapMenuDropdown" style="display:none; position:absolute; top:48px; right:0; width:270px; background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; box-shadow:0 12px 32px rgba(0,0,0,0.12); padding:12px; z-index:40;">
                            
                            <!-- Header Action Buttons -->
                            <div style="font-size:10px; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:8px; padding:0 4px;">Route Controls</div>
                            <div style="display:flex; flex-direction:column; gap:6px; margin-bottom:10px;">
                                <button id="auto-evt-button-19" type="button" title="Switch Route" style="display:flex; align-items:center; gap:10px; padding:9px 12px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; font-size:12px; font-weight:700; color:#0f172a; cursor:pointer; width:100%; text-align:left;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#f8fafc'">
                                    <i class="ph ph-swap" style="font-size:16px; color:#2563eb;"></i> Switch Route
                                </button>
                                <button id="btnUnbindRouteTab1" type="button" onclick="unbindCombinedRoute()" style="display:none; align-items:center; gap:10px; padding:9px 12px; background:#fff5f5; border:1px solid #fca5a5; border-radius:10px; font-size:12px; font-weight:700; color:#dc2626; cursor:pointer; width:100%; text-align:left;">
                                    <i class="ph ph-link-break" style="font-size:16px;"></i> Undo Route Binding
                                </button>
                                <button id="auto-evt-button-20" type="button" title="Delete Route" style="display:flex; align-items:center; gap:10px; padding:9px 12px; background:#fff5f5; border:1px solid #fca5a5; border-radius:10px; font-size:12px; font-weight:700; color:#dc2626; cursor:pointer; width:100%; text-align:left;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fff5f5'">
                                    <i class="ph ph-trash" style="font-size:16px;"></i> Delete Route
                                </button>
                            </div>

                            <div style="height:1px; background:#e2e8f0; margin:8px 0;"></div>

                            <!-- Print & Export Buttons -->
                            <div style="font-size:10px; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:8px; padding:0 4px;">Print & Export</div>
                            <div style="display:flex; flex-direction:column; gap:6px;">
                                <button type="button" id="btnQuickPrintAllInvoices" onclick="printRouteInvoices()" style="display:flex; align-items:center; gap:10px; padding:9px 12px; background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; font-size:12px; font-weight:600; color:#0f172a; cursor:pointer; width:100%; text-align:left;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#ffffff'">
                                    <i class="ph ph-files" style="font-size:16px; color:#475569;"></i> Print All Invoices
                                </button>
                                <button type="button" id="btnQuickPrintInvoiceSummary" onclick="printRouteInvoicesSummary()" style="display:flex; align-items:center; gap:10px; padding:9px 12px; background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; font-size:12px; font-weight:600; color:#0f172a; cursor:pointer; width:100%; text-align:left;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#ffffff'">
                                    <i class="ph ph-file-text" style="font-size:16px; color:#475569;"></i> Print Invoice Summary
                                </button>
                                <button type="button" id="btnQuickPrintLoadingSheet" onclick="printLoadingSheet('full')" style="display:flex; align-items:center; gap:10px; padding:9px 12px; background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; font-size:12px; font-weight:600; color:#0f172a; cursor:pointer; width:100%; text-align:left;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#ffffff'">
                                    <i class="ph ph-truck" style="font-size:16px; color:#475569;"></i> Print Loading Sheet
                                </button>
                                <button type="button" id="btnQuickPrintLoadingSummary" onclick="printLoadingSheet('summary')" style="display:flex; align-items:center; gap:10px; padding:9px 12px; background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; font-size:12px; font-weight:600; color:#0f172a; cursor:pointer; width:100%; text-align:left;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#ffffff'">
                                    <i class="ph ph-list-checks" style="font-size:16px; color:#475569;"></i> Print Loading Summary
                                </button>
                                <button type="button" id="btnQuickPrintReturnStock" onclick="printReturnStock()" style="display:flex; align-items:center; gap:10px; padding:9px 12px; background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; font-size:12px; font-weight:600; color:#0f172a; cursor:pointer; width:100%; text-align:left;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#ffffff'">
                                    <i class="ph ph-package" style="font-size:16px; color:#475569;"></i> Print Return Stock
                                </button>
                                <button type="button" id="btnQuickPrintBalancingReport" onclick="printBalancingReport()" style="display:flex; align-items:center; gap:10px; padding:9px 12px; background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; font-size:12px; font-weight:600; color:#0f172a; cursor:pointer; width:100%; text-align:left;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#ffffff'">
                                    <i class="ph ph-scales" style="font-size:16px; color:#475569;"></i> Print Balancing Report
                                </button>
                                <button type="button" id="btnQuickPrintSpreadsheet" onclick="printLoadingSheetSpreadsheet()" style="display:flex; align-items:center; gap:10px; padding:9px 12px; background:#0f172a; border:none; border-radius:10px; font-size:12px; font-weight:700; color:#ffffff; cursor:pointer; width:100%; text-align:left;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                                    <i class="ph ph-file-xls" style="font-size:16px;"></i> Export Loading Sheet
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- ── RIGHT SIDE PANEL (480px LIGHT THEME) ── -->
                    <div id="tab1RightPanel" style="position:absolute; top:0; right:0; bottom:0; width:480px; z-index:15; display:flex; flex-direction:column; background:#ffffff; border-left:1px solid #e2e8f0; box-shadow:-6px 0 24px rgba(0,0,0,0.06);">

                        <!-- Tab Header Navigation -->
                        <div style="display:flex; border-bottom:1px solid #e2e8f0; background:#f8fafc; flex-shrink:0; padding:6px 6px 0 6px; gap:4px;">
                            <button id="tab1PanelTabOverview" onclick="switchTab1Panel('overview')" style="flex:1; padding:12px 10px; font-size:12px; font-weight:700; color:#0f172a; background:#ffffff; border:none; border-bottom:2px solid #0f172a; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; border-top-left-radius:8px; border-top-right-radius:8px; transition:all 0.15s;">
                                <i class="ph ph-chart-pie-slice" style="font-size:16px;"></i> Totals & Route Info
                            </button>
                            <button id="tab1PanelTabWaypoints" onclick="switchTab1Panel('waypoints')" style="flex:1; padding:12px 10px; font-size:12px; font-weight:700; color:#64748b; background:transparent; border:none; border-bottom:2px solid transparent; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; border-top-left-radius:8px; border-top-right-radius:8px; transition:all 0.15s;">
                                <i class="ph ph-map-pin-line" style="font-size:16px;"></i> Activity & Waypoints
                            </button>
                        </div>

                        <!-- ══ TAB 1: TOTALS & ROUTE INFO ══ -->
                        <div id="tab1PanelOverview" style="flex:1; overflow-y:auto; overflow-x:hidden;">

                            <!-- § Route Identity Header Card -->
                            <div style="padding:18px 20px; border-bottom:1px solid #f1f5f9;">
                                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                                    <div style="font-size:10px; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:0.1em; display:flex; align-items:center; gap:6px;">
                                        <span style="width:3px; height:12px; background:#0f172a; border-radius:2px; display:inline-block;"></span>
                                        Route Identity
                                    </div>
                                    <div id="tab1StatusBadge" style="font-size:11px; font-weight:700; padding:4px 12px; border-radius:20px; background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; white-space:nowrap;">—</div>
                                    <span id="tab1Status" style="display:none;">—</span>
                                </div>
                                <div style="display:grid; grid-template-columns:130px 1fr; gap:12px; align-items:center; margin-bottom:12px; background:#f8fafc; padding:14px; border-radius:12px; border:1px solid #e2e8f0;">
                                    <div>
                                        <div style="font-size:10px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:3px;">Route Code</div>
                                        <div id="tab1RouteNumber" style="font-size:17px; font-weight:800; color:#0f172a; font-family:var(--f-mono); letter-spacing:-0.02em;">—</div>
                                    </div>
                                    <div style="border-left:1px solid #e2e8f0; padding-left:12px;">
                                        <div style="font-size:10px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:3px;">Route Name</div>
                                        <div id="tab1RouteName" style="font-size:14px; font-weight:700; color:#0f172a; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">—</div>
                                        <span id="tab1MapRouteName" style="display:none;"></span>
                                    </div>
                                </div>
                                <div style="display:flex; align-items:center; gap:12px; padding:12px 14px; background:#ffffff; border-radius:12px; border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
                                    <div style="width:36px; height:36px; border-radius:10px; background:#0f172a; color:#ffffff; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                        <i class="ph ph-user" style="font-size:18px;"></i>
                                    </div>
                                    <div style="min-width:0; flex:1;">
                                        <div style="font-size:10px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.06em;">Representative Assigned</div>
                                        <div id="tab1RepName" style="font-size:13px; font-weight:700; color:#0f172a; margin-top:1px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">—</div>
                                    </div>
                                </div>
                            </div>

                            <!-- § Performance & Totals (KPIs) -->
                            <div style="padding:18px 20px; border-bottom:1px solid #f1f5f9;">
                                <div style="font-size:10px; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:0.1em; margin-bottom:12px; display:flex; align-items:center; gap:6px;">
                                    <span style="width:3px; height:12px; background:#0f172a; border-radius:2px; display:inline-block;"></span>
                                    Performance & Financial Totals
                                </div>
                                <!-- Sales Row -->
                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:10px;">
                                    <div style="padding:14px; background:#0f172a; border-radius:14px; box-shadow:0 4px 12px rgba(15,23,42,0.12);">
                                        <div style="font-size:10px; font-weight:600; color:rgba(255,255,255,0.6); text-transform:uppercase; letter-spacing:0.07em; margin-bottom:5px;">Rep Sales Value</div>
                                        <div id="tab1SalesValue" style="font-size:16px; font-weight:800; color:#ffffff; font-family:var(--f-mono); line-height:1.2; word-break:break-all;">—</div>
                                        <div style="font-size:10px; color:rgba(255,255,255,0.4); margin-top:3px;">Original Total</div>
                                    </div>
                                    <div style="padding:14px; background:#f8fafc; border-radius:14px; border:1px solid #e2e8f0;">
                                        <div style="font-size:10px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.07em; margin-bottom:5px;">Actual Sales Value</div>
                                        <div id="tab1ActualSalesValue" style="font-size:16px; font-weight:800; color:#0f172a; font-family:var(--f-mono); line-height:1.2; word-break:break-all;">—</div>
                                        <div style="font-size:10px; color:#94a3b8; margin-top:3px;">Net Adjusted</div>
                                    </div>
                                </div>
                                <!-- Counts Row -->
                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                                    <div style="padding:12px 14px; background:#f8fafc; border-radius:14px; border:1px solid #e2e8f0; display:flex; align-items:center; gap:12px;">
                                        <div style="width:38px; height:38px; border-radius:10px; background:#e0f2fe; color:#0369a1; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                            <i class="ph ph-receipt" style="font-size:20px;"></i>
                                        </div>
                                        <div>
                                            <div style="font-size:10px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:1px;">Total Bills</div>
                                            <div id="tab1BillsCount" style="font-size:24px; font-weight:800; color:#0f172a; font-family:var(--f-mono); line-height:1;">—</div>
                                        </div>
                                    </div>
                                    <div style="padding:12px 14px; background:#f8fafc; border-radius:14px; border:1px solid #e2e8f0; display:flex; align-items:center; gap:12px;">
                                        <div style="width:38px; height:38px; border-radius:10px; background:#fef3c7; color:#b45309; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                            <i class="ph ph-x-circle" style="font-size:20px;"></i>
                                        </div>
                                        <div>
                                            <div style="font-size:10px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:1px;">Failed Visits</div>
                                            <div id="tab1UnproductiveCount" style="font-size:24px; font-weight:800; color:#0f172a; font-family:var(--f-mono); line-height:1;">0</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- § Time & Odometer Log -->
                            <div style="padding:18px 20px; border-bottom:1px solid #f1f5f9;">
                                <div style="font-size:10px; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:0.1em; margin-bottom:12px; display:flex; align-items:center; gap:6px;">
                                    <span style="width:3px; height:12px; background:#0f172a; border-radius:2px; display:inline-block;"></span>
                                    Time & Odometer Log
                                </div>
                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:10px;">
                                    <div style="padding:12px; background:#f8fafc; border-radius:12px; border:1px solid #e2e8f0;">
                                        <div style="font-size:10px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:3px; display:flex; align-items:center; gap:4px;"><i class="ph ph-clock" style="font-size:13px; color:#0f172a;"></i>Start Time</div>
                                        <div id="tab1StartTime" style="font-size:12px; font-weight:700; color:#0f172a; font-family:var(--f-mono);">—</div>
                                    </div>
                                    <div style="padding:12px; background:#f8fafc; border-radius:12px; border:1px solid #e2e8f0;">
                                        <div style="font-size:10px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:3px; display:flex; align-items:center; gap:4px;"><i class="ph ph-clock-afternoon" style="font-size:13px; color:#0f172a;"></i>End Time</div>
                                        <div id="tab1EndTime" style="font-size:12px; font-weight:700; color:#0f172a; font-family:var(--f-mono);">—</div>
                                    </div>
                                </div>
                                <div style="padding:12px 14px; background:#f8fafc; border-radius:12px; border:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; gap:12px;">
                                    <div style="min-width:0;">
                                        <div style="font-size:10px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:3px; display:flex; align-items:center; gap:4px;"><i class="ph ph-gauge" style="font-size:13px; color:#0f172a;"></i>Odometer Range</div>
                                        <div style="font-size:12px; font-weight:700; color:#0f172a; font-family:var(--f-mono); white-space:nowrap;"><span id="tab1StartMeter">—</span> <span style="color:#cbd5e1;">→</span> <span id="tab1EndMeter">—</span></div>
                                    </div>
                                    <div style="text-align:right; flex-shrink:0; border-left:1px solid #e2e8f0; padding-left:14px;">
                                        <div style="font-size:10px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:3px;">Total Distance</div>
                                        <div id="tab1Distance" style="font-size:16px; font-weight:800; color:#0f172a; font-family:var(--f-mono);">—</div>
                                    </div>
                                </div>
                            </div>

                            <!-- § Bound Route Summary -->
                            <div id="boundRouteSummaryContainer" style="display:none; padding:18px 20px; border-bottom:1px solid #f1f5f9;">
                                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
                                    <div style="font-size:10px; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:0.1em; display:flex; align-items:center; gap:6px;">
                                        <span style="width:3px; height:12px; background:#0f172a; border-radius:2px; display:inline-block;"></span>
                                        Bound Route Summary
                                    </div>
                                </div>
                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; font-size:12px;">
                                    <div style="padding:10px; background:#f8fafc; border-radius:10px; border:1px solid #e2e8f0;"><div style="font-size:10px; font-weight:600; color:#64748b; text-transform:uppercase; margin-bottom:2px;">Routes</div><strong id="brsConstituentsList" style="color:#0f172a;">—</strong></div>
                                    <div style="padding:10px; background:#f8fafc; border-radius:10px; border:1px solid #e2e8f0;"><div style="font-size:10px; font-weight:600; color:#64748b; text-transform:uppercase; margin-bottom:2px;">Customers</div><strong id="brsTotalCustomers" style="color:#0f172a;">—</strong></div>
                                    <div style="padding:10px; background:#f8fafc; border-radius:10px; border:1px solid #e2e8f0;"><div style="font-size:10px; font-weight:600; color:#64748b; text-transform:uppercase; margin-bottom:2px;">Invoices</div><strong id="brsTotalInvoices" style="color:#0f172a;">—</strong></div>
                                    <div style="padding:10px; background:#f8fafc; border-radius:10px; border:1px solid #e2e8f0;"><div style="font-size:10px; font-weight:600; color:#64748b; text-transform:uppercase; margin-bottom:2px;">Total Value</div><strong id="brsTotalValue" style="color:#0f172a;">—</strong></div>
                                    <div style="padding:10px; background:#f8fafc; border-radius:10px; border:1px solid #e2e8f0; grid-column:span 2;"><div style="font-size:10px; font-weight:600; color:#64748b; text-transform:uppercase; margin-bottom:2px;">Items Breakdown</div><strong id="brsTotalProducts" style="color:#0f172a;">—</strong></div>
                                </div>
                            </div>

                            <div style="height:20px;"></div>
                        </div><!-- /tab 1 content -->

                        <!-- ══ TAB 2: WAYPOINTS & ACTIVITY LOG ══ -->
                        <div id="tab1PanelWaypoints" style="flex:1; overflow-y:auto; overflow-x:hidden; display:none; padding:18px 20px;">
                            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
                                <div style="font-size:10px; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:0.1em; display:flex; align-items:center; gap:6px;">
                                    <span style="width:3px; height:12px; background:#0f172a; border-radius:2px; display:inline-block;"></span>
                                    Route Timeline & Activity Log
                                </div>
                                <div style="display:flex; gap:6px;">
                                    <span id="tab1PathPointCount" style="font-size:10px; font-weight:700; padding:3px 8px; border-radius:12px; background:#e0f2fe; color:#0369a1;">0 points</span>
                                    <span id="tab1UnproductiveBadge" style="font-size:10px; font-weight:700; padding:3px 8px; border-radius:12px; background:#fef3c7; color:#b45309;">0 Visits</span>
                                </div>
                            </div>

                            <!-- Timeline Card List Container -->
                            <div id="tab1PathStepEmpty" style="font-size:12px; color:#94a3b8; text-align:center; padding:24px 16px; background:#f8fafc; border-radius:12px; border:1px dashed #cbd5e1;">
                                <i class="ph ph-map-pin" style="font-size:32px; color:#cbd5e1; display:block; margin-bottom:8px;"></i>
                                No waypoints or activity recorded for this route yet.
                            </div>

                            <div id="tab1PathStepList" style="display:none;">
                                <div id="tab1PathStepOl" style="display:flex; flex-direction:column; gap:10px; list-style:none; padding:0; margin:0;"></div>
                            </div>

                            <!-- Hidden container for JS backwards compatibility -->
                            <table style="display:none;"><tbody id="tab1UnproductiveTbody"></tbody></table>

                            <div style="height:24px;"></div>
                        </div><!-- /tab 2 content -->

                        <!-- Hidden notes textarea -->
                        <textarea id="tab1RouteNotes" style="display:none;"></textarea>

                    </div><!-- /right panel -->

                </div>

                <!-- Tab 1 Panel Tab Switcher & Dropdown Toggle Script -->
                <script>
                function switchTab1Panel(which) {
                    const overview = document.getElementById('tab1PanelOverview');
                    const waypoints = document.getElementById('tab1PanelWaypoints');
                    const btnOv = document.getElementById('tab1PanelTabOverview');
                    const btnWp = document.getElementById('tab1PanelTabWaypoints');
                    if (!overview || !waypoints) return;
                    if (which === 'overview') {
                        overview.style.display = 'block';
                        waypoints.style.display = 'none';
                        btnOv.style.color = '#0f172a'; btnOv.style.background = '#ffffff'; btnOv.style.borderBottom = '2px solid #0f172a';
                        btnWp.style.color = '#64748b'; btnWp.style.background = 'transparent'; btnWp.style.borderBottom = '2px solid transparent';
                    } else {
                        waypoints.style.display = 'block';
                        overview.style.display = 'none';
                        btnWp.style.color = '#0f172a'; btnWp.style.background = '#ffffff'; btnWp.style.borderBottom = '2px solid #0f172a';
                        btnOv.style.color = '#64748b'; btnOv.style.background = 'transparent'; btnOv.style.borderBottom = '2px solid transparent';
                    }
                }

                function toggleTab1MapMenu() {
                    const menu = document.getElementById('tab1MapMenuDropdown');
                    if (!menu) return;
                    menu.style.display = (menu.style.display === 'none' || !menu.style.display) ? 'block' : 'none';
                }

                document.addEventListener('click', function(e) {
                    const menu = document.getElementById('tab1MapMenuDropdown');
                    const btn = document.getElementById('tab1MapMenuBtn');
                    if (menu && btn && !menu.contains(e.target) && !btn.contains(e.target)) {
                        menu.style.display = 'none';
                    }
                });
                </script>


                <!-- TAB 2: CREDIT COLLECTIONS -->

                <!-- TAB 3: ADJUSTMENTS -->
                <div class="workspace-tab-panel" id="tabpanel-3" style="display:none; position:relative; margin:-16px -20px; min-height:550px; background:var(--c-surface); flex-direction:column; overflow:hidden;">
                    <!-- Table Scrollable Area -->
                    <div style="flex:1; min-height:0; overflow-y:auto; padding:20px 24px 110px 24px;">









                        <table class="data-table" style="margin-top:0;">
                            <thead>
                                <tr>
                                    <th>Invoice Number</th>
                                    <th>Time</th>
                                    <th>Customer Name</th>
                                    <th style="text-align:right;">Grand Total (Rs)</th>
                                    <th style="text-align:center; width:100px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="adjustmentsInvoicesTbody"></tbody>
                            <tfoot id="adjustmentsInvoicesTfoot" style="background:#f8fafc; font-weight:bold; border-top: 2px solid var(--c-separator);">
                                <tr>
                                    <td colspan="3" style="padding:10px; text-align:right; color: var(--t-primary);">Total:</td>
                                    <td id="adjustmentsTotalGrand" style="padding:10px; text-align:right; font-family:monospace; font-size:14px; color:var(--primary);">Rs 0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- Floating Action Bar Fixed to Bottom Center -->
                    <div id="tab3FloatingDock" style="position:fixed; bottom:20px; left:50%; transform:translateX(-50%); z-index:1000; display:flex; align-items:center; gap:10px; padding:8px 14px; background:rgba(255, 255, 255, 0.94); backdrop-filter:blur(24px) saturate(200%); -webkit-backdrop-filter:blur(24px) saturate(200%); border:1px solid rgba(203, 213, 225, 0.9); border-radius:100px; box-shadow:0 16px 40px rgba(15, 23, 42, 0.16), 0 4px 12px rgba(0,0,0,0.06);">
                        <button id="btnTab3CreateSO" type="button" style="padding:10px 20px; background:#007aff; border:none; color:#ffffff; border-radius:100px; font-weight:700; font-size:13px; cursor:pointer; display:inline-flex; align-items:center; gap:8px; white-space:nowrap; box-shadow:0 3px 12px rgba(0, 122, 255, 0.3); transition:all 0.18s cubic-bezier(0.34, 1.56, 0.64, 1);" onmouseover="this.style.transform='scale(1.04)'; this.style.background='#0066cc'" onmouseout="this.style.transform='scale(1)'; this.style.background='#007aff'">
                            <i class="ph ph-plus-circle" style="font-size:18px;"></i> Create Sales Order
                        </button>
                        <button id="btnTab3AttachSO" type="button" style="padding:10px 20px; background:#5c6bc0; border:none; color:#ffffff; border-radius:100px; font-weight:700; font-size:13px; cursor:pointer; display:inline-flex; align-items:center; gap:8px; white-space:nowrap; box-shadow:0 3px 12px rgba(92, 107, 192, 0.3); transition:all 0.18s cubic-bezier(0.34, 1.56, 0.64, 1);" onmouseover="this.style.transform='scale(1.04)'; this.style.background='#4c5bb0'" onmouseout="this.style.transform='scale(1)'; this.style.background='#5c6bc0'">
                            <i class="ph ph-link" style="font-size:18px;"></i> Attach Sales Order
                        </button>
                        <div style="width:1px; height:22px; background:#cbd5e1; margin:0 2px;"></div>
                        <button id="btnTab3PrintInvoices" type="button" style="padding:10px 18px; background:#1e293b; border:none; color:#ffffff; border-radius:100px; font-weight:700; font-size:13px; cursor:pointer; display:inline-flex; align-items:center; gap:8px; white-space:nowrap; box-shadow:0 3px 12px rgba(30, 41, 59, 0.25); transition:all 0.18s;" onmouseover="this.style.transform='scale(1.04)'; this.style.background='#0f172a'" onmouseout="this.style.transform='scale(1)'; this.style.background='#1e293b'">
                            <i class="ph ph-printer" style="font-size:18px; color:#ffffff;"></i> Print All Invoices
                        </button>
                        <button id="btnTab3PrintSummary" type="button" style="padding:10px 18px; background:#0f766e; border:none; color:#ffffff; border-radius:100px; font-weight:700; font-size:13px; cursor:pointer; display:inline-flex; align-items:center; gap:8px; white-space:nowrap; box-shadow:0 3px 12px rgba(15, 118, 110, 0.25); transition:all 0.18s;" onmouseover="this.style.transform='scale(1.04)'; this.style.background='#115e59'" onmouseout="this.style.transform='scale(1)'; this.style.background='#0f766e'">
                            <i class="ph ph-file-text" style="font-size:18px; color:#ffffff;"></i> Print Invoice Summary
                        </button>
                    </div>
                </div>

                <!-- TAB 4: LOADING -->
                <div class="workspace-tab-panel" id="tabpanel-4" style="display:none; position:relative; margin:-16px -20px; min-height:550px; background:var(--c-surface); flex-direction:column; overflow:hidden;">
                    <div id="loadingBox" style="flex:1; min-height:0; overflow-y:auto; padding:20px 24px 110px 24px;"></div>
                </div>

                <!-- TAB 5: VARIANCE -->
                <div class="workspace-tab-panel" id="tabpanel-5" style="display:none; position:relative; margin:-16px -20px; min-height:550px; background:var(--c-surface); flex-direction:column; overflow:hidden;">
                    <div id="varianceAuditBox" style="flex:1; min-height:0; overflow-y:auto; padding:20px 24px 110px 24px;"></div>
                </div>

                <!-- TAB 6: DISPATCH / DELIVERY ARRANGEMENT -->
                <div class="workspace-tab-panel" id="tabpanel-6" style="display:none; padding: 10px 0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                        <div>
                            <h3 style="margin:0; font-size:20px; font-weight:700; color:#0f172a; font-family:var(--font-sf);">Delivery Arrangement</h3>
                            <p style="margin:4px 0 0 0; font-size:13px; color:#64748b;">Assign vehicle, driver, and partner helper for this route delivery manifest.</p>
                        </div>
                        <div>
                            <button id="auto-evt-button-21" type="button" style="padding:9px 18px; background:#ffffff; border:1px solid #cbd5e1; color:#334155; border-radius:100px; font-weight:700; font-size:13px; cursor:pointer; display:inline-flex; align-items:center; gap:8px; box-shadow:0 1px 3px rgba(0,0,0,0.05); transition:all 0.18s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#ffffff'">
                                <i class="ph ph-printer" style="font-size:16px;"></i> Print Loading Summary
                            </button>
                        </div>
                    </div>
                    
                    <!-- Global Status Banner -->
                    <div id="adjDeliveryStatusBanner" style="display:none; margin-bottom: 20px; padding:12px 18px; background:#eff6ff; border:1px solid #bfdbfe; border-radius:12px; color:#1d4ed8; font-size:13px; font-weight:600; align-items:center; gap:10px;">
                        <i class="ph ph-check-circle" style="font-size: 18px;"></i>
                        <span>Delivery Manifest <strong id="adjDeliveryStatusId">#--</strong> successfully generated. Ready for warehouse.</span>
                    </div>

                    <!-- Form View -->
                    <div id="adjDeliveryFormView">
                        <form id="adjDeliveryArrangeForm" style="max-width: 580px; margin: 0 auto;">
                            
                            <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; box-shadow:0 4px 20px rgba(15,23,42,0.05); padding:24px;">
                                <div style="display:flex; align-items:center; gap:10px; margin-bottom:20px; padding-bottom:14px; border-bottom:1px solid #f1f5f9;">
                                    <div style="width:38px; height:38px; border-radius:10px; background:#eff6ff; color:#007aff; display:flex; align-items:center; justify-content:center; font-size:20px;">
                                        <i class="ph ph-truck"></i>
                                    </div>
                                    <div>
                                        <div style="font-size:16px; font-weight:700; color:#0f172a; font-family:var(--font-sf);">Delivery Details</div>
                                        <div style="font-size:12px; color:#64748b;">Configure logistics parameters for dispatch</div>
                                    </div>
                                </div>

                                <div style="display:flex; flex-direction:column; gap:18px;">
                                    <div>
                                        <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.04em;">Delivery Date</label>
                                        <input type="date" id="adjDaDate" value="<?= date('Y-m-d') ?>" style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:10px; font-size:13px; color:#0f172a; background:#ffffff; outline:none; transition:all 0.18s; font-family:inherit;" onfocus="this.style.borderColor='#007aff'; this.style.boxShadow='0 0 0 3px rgba(0,122,255,0.12)'" onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none'">
                                    </div>

                                    <div>
                                        <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.04em;">Vehicle Number *</label>
                                        <select id="adjDaVehicle" required style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:10px; font-size:13px; color:#0f172a; background:#ffffff; outline:none; transition:all 0.18s; font-family:inherit;">
                                            <option value="">-- Select Vehicle --</option>
                                            <?php foreach($data['vehicles'] as $v): ?>
                                                <?php if($v->status === 'Active'): ?>
                                                    <option value="<?= htmlspecialchars($v->vehicle_number) ?>"><?= htmlspecialchars($v->vehicle_number) ?></option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div>
                                        <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.04em;">Driver Name *</label>
                                        <select id="adjDaDriver" required style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:10px; font-size:13px; color:#0f172a; background:#ffffff; outline:none; transition:all 0.18s; font-family:inherit;">
                                            <option value="">-- Select Driver --</option>
                                            <?php foreach($data['drivers'] as $d): ?>
                                                <option value="<?= htmlspecialchars($d->first_name . ' ' . $d->last_name) ?>"><?= htmlspecialchars($d->first_name . ' ' . $d->last_name) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div>
                                        <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.04em;">Partner / Helper</label>
                                        <select id="adjDaPartner" style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:10px; font-size:13px; color:#0f172a; background:#ffffff; outline:none; transition:all 0.18s; font-family:inherit;">
                                            <option value="">-- None --</option>
                                            <?php foreach($data['drivers'] as $d): ?>
                                                <option value="<?= htmlspecialchars($d->first_name . ' ' . $d->last_name) ?>"><?= htmlspecialchars($d->first_name . ' ' . $d->last_name) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div style="margin-top: 10px;">
                                        <button id="auto-evt-button-22" type="button" style="width:100%; padding:12px; background:#007aff; border:none; color:#ffffff; border-radius:12px; font-weight:700; font-size:14px; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; box-shadow:0 4px 14px rgba(0,122,255,0.3); transition:all 0.18s;" onmouseover="this.style.transform='scale(1.02)'; this.style.background='#0062cc'" onmouseout="this.style.transform='scale(1)'; this.style.background='#007aff'">
                                            <i class="ph ph-truck" style="font-size:18px;"></i> Save Delivery Arrangement
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

                <!-- TAB 14: CREDIT BILLS SELECTION -->
                <div class="workspace-tab-panel" id="tabpanel-14" style="display:none; padding: 10px 0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                        <div>
                            <h3 style="margin:0; font-size:20px; font-weight:700; color:#0f172a; font-family:var(--font-sf);">Credit Bills Selection</h3>
                            <p style="margin:4px 0 0 0; font-size:13px; color:#64748b;">Select territory credit invoices to dispatch with this vehicle delivery manifest.</p>
                        </div>
                    </div>

                    <div style="max-width: 920px; margin: 0 auto; background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; box-shadow:0 4px 20px rgba(15,23,42,0.05); padding:24px;">
                        <div style="display:flex; align-items:center; gap:10px; margin-bottom:18px; padding-bottom:14px; border-bottom:1px solid #f1f5f9;">
                            <div style="width:38px; height:38px; border-radius:10px; background:#f0fdf4; color:#16a34a; display:flex; align-items:center; justify-content:center; font-size:20px;">
                                <i class="ph ph-receipt"></i>
                            </div>
                            <div>
                                <div style="font-size:16px; font-weight:700; color:#0f172a; font-family:var(--font-sf);">Territory Credit Invoices</div>
                                <div style="font-size:12px; color:#64748b;">Select outstanding invoices for delivery dispatch</div>
                            </div>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 16px;">
                            <!-- Search & Route Filter controls -->
                            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                                <div style="flex: 1.5; position:relative; display:flex; align-items:center;">
                                    <i class="ph ph-magnifying-glass" style="position:absolute; left:12px; color:#94a3b8; font-size:16px;"></i>
                                    <input type="text" id="creditBillsSearch" placeholder="Search by customer or invoice..." style="width:100%; padding:10px 12px 10px 36px; border:1px solid #cbd5e1; border-radius:10px; background:#f8fafc; color:#0f172a; font-size:13px; outline:none; transition:all 0.18s;" onfocus="this.style.borderColor='#007aff'; this.style.background='#ffffff'; this.style.boxShadow='0 0 0 3px rgba(0,122,255,0.12)'" onblur="this.style.borderColor='#cbd5e1'; this.style.background='#f8fafc'; this.style.boxShadow='none'">
                                </div>
                                <div style="flex: 1; min-width:180px;">
                                    <select id="creditBillsRouteFilter" style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:10px; background:#f8fafc; color:#0f172a; font-size:13px; outline:none; transition:all 0.18s;" onfocus="this.style.borderColor='#007aff'; this.style.background='#ffffff'" onblur="this.style.borderColor='#cbd5e1'; this.style.background='#f8fafc'">
                                        <option value="all">All Routes</option>
                                        <option value="none">No Route / Unassigned</option>
                                    </select>
                                </div>
                            </div>

                            <div id="adjDaBillsContainer" class="macos-checkbox-list" style="flex: 1; min-height: 380px; max-height: none; background:#ffffff; border:1px solid #f1f5f9; border-radius:12px; padding:12px;">
                                <!-- Outstanding credit bills list -->
                            </div>

                            <div style="margin-top: 10px;">
                                <button type="button" onclick="submitAdjustmentsLogisticsArrange()" style="width:100%; padding:12px; background:#16a34a; border:none; color:#ffffff; border-radius:12px; font-weight:700; font-size:14px; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; box-shadow:0 4px 14px rgba(22,163,74,0.3); transition:all 0.18s;" onmouseover="this.style.transform='scale(1.02)'; this.style.background='#15803d'" onmouseout="this.style.transform='scale(1)'; this.style.background='#16a34a'">
                                    <i class="ph ph-check-circle" style="font-size:18px;"></i> Save Credit Bills Selection
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 8: DELIVERY (LIVE MONITORING) -->
                <div class="workspace-tab-panel" id="tabpanel-8" style="display:none;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <div>
                            <h3 style="margin:0; font-size:18px; font-weight:700; color:var(--t-primary);">Delivery Live Execution Status</h3>
                            <p style="margin:4px 0 0 0; font-size:13px; color:var(--t-secondary);">Track live progress of customer dispatches and collections on the route.</p>
                        </div>
                    </div>
                    <div style="border:0.5px solid var(--c-separator); border-radius:var(--r-lg); padding:20px; background:var(--c-surface); box-shadow:var(--shadow-sm); margin-bottom:20px;">
                        <h4 style="margin:0 0 15px 0; color:var(--primary); font-size:15px; font-weight:bold;"><i class="ph ph-chart-bar"></i> Delivery Performance Summary</h4>
                        <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:15px;" id="deliveryTabSummaryCards">
                            <!-- Populated dynamically -->
                        </div>
                    </div>
                    <div style="border:0.5px solid var(--c-separator); border-radius:var(--r-lg); padding:20px; background:var(--c-surface); box-shadow:var(--shadow-sm);">
                        <h4 style="margin:0 0 15px 0; color:var(--primary); font-size:15px; font-weight:bold;"><i class="ph ph-map-pin"></i> Customer Visit & Dispatch Status</h4>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Customer Name</th>
                                    <th>Invoice Number</th>
                                    <th style="text-align:right;">Grand Total (Rs)</th>
                                    <th style="text-align:center;">Delivery Status</th>
                                    <th style="text-align:center;">Payment Status</th>
                                    <th style="text-align:center;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="deliveryTabInvoicesTbody">
                                <!-- Populated dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 12: EXPENSES (NEW STAGE) -->
                <div class="workspace-tab-panel" id="tabpanel-12" style="display:none;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <div>
                            <h3 style="margin:0; font-size:18px; font-weight:700; color:var(--t-primary);">Route Expenses Management</h3>
                            <p style="margin:4px 0 0 0; font-size:13px; color:var(--t-secondary);">Record and manage operational expenses for Representatives and Delivery operations.</p>
                        </div>
                    </div>

                    <!-- Inner Sub-Tabs Header -->
                    <div style="display:flex; gap:10px; border-bottom:1px solid #cbd5e1; margin-bottom:20px;">
                        <button type="button" class="left-tab-btn active" id="btnExpenseSubTabRep" onclick="switchExpenseSubTab('Rep')">
                            <i class="ph ph-user-gear"></i> Rep Expenses
                        </button>
                        <button type="button" class="left-tab-btn" id="btnExpenseSubTabDelivery" onclick="switchExpenseSubTab('Delivery')">
                            <i class="ph ph-truck"></i> Delivery Expenses
                        </button>
                    </div>

                    <div style="display:grid; grid-template-columns:1.1fr 0.9fr; gap:20px;">
                        <!-- Left: Form Container -->
                        <div>
                            <div style="border:0.5px solid var(--c-separator); border-radius:var(--r-lg); padding:20px; background:var(--c-surface); box-shadow:var(--shadow-sm); margin-bottom:20px;">
                                <form id="formRecordRouteExpense" onsubmit="submitRouteExpense(event)">
                                    <input type="hidden" id="expCategory" value="Rep">

                                    <!-- Rep Expenses Header -->
                                    <div id="subtabRepHeader">
                                        <h4 style="margin:0 0 15px 0; color:var(--primary); font-size:15px; font-weight:bold;">
                                            <i class="ph ph-user-gear"></i> Record Representative Expense
                                        </h4>
                                    </div>

                                    <!-- Delivery Expenses Header -->
                                    <div id="subtabDeliveryHeader" style="display:none;">
                                        <h4 style="margin:0 0 15px 0; color:var(--primary); font-size:15px; font-weight:bold;">
                                            <i class="ph ph-truck"></i> Record Delivery Manifest Expense
                                        </h4>
                                    </div>

                                    <!-- Rep Expenses Fields -->
                                    <div id="fieldsRepExpenses">
                                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                                            <div>
                                                <label style="display:block; font-size:11px; font-weight:700; color:var(--t-label); text-transform:uppercase; margin-bottom:5px;">Related Route / Trip</label>
                                                <input type="text" id="expRouteNumber" readonly style="width:100%; padding:8px 12px; border:1px solid var(--c-separator); border-radius:var(--r-xs); background:#f8fafc; font-family:monospace; font-weight:bold;">
                                            </div>
                                            <div>
                                                <label style="display:block; font-size:11px; font-weight:700; color:var(--t-label); text-transform:uppercase; margin-bottom:5px;">Assigned Vehicle <span style="color:red;">*</span></label>
                                                <select id="expVehicleNumber" style="width:100%; padding:8px 12px; border:1px solid var(--c-separator); border-radius:var(--r-xs); font-weight:bold; outline:none;">
                                                    <option value="">-- Select Vehicle --</option>
                                                    <?php if (!empty($data['vehicles'])): ?>
                                                        <?php foreach($data['vehicles'] as $v): ?>
                                                            <?php if($v->status === 'Active'): ?>
                                                                <option value="<?= htmlspecialchars($v->vehicle_number) ?>"><?= htmlspecialchars($v->vehicle_number) ?></option>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                                            <div>
                                                <label style="display:block; font-size:11px; font-weight:700; color:var(--t-label); text-transform:uppercase; margin-bottom:5px;">Representative</label>
                                                <input type="text" id="expRepName" readonly style="width:100%; padding:8px 12px; border:1px solid var(--c-separator); border-radius:var(--r-xs); background:#f8fafc; font-weight:500;">
                                            </div>
                                            <div>
                                                <label style="display:block; font-size:11px; font-weight:700; color:var(--t-label); text-transform:uppercase; margin-bottom:5px;">Date & Time</label>
                                                <input type="text" id="expDateTime" readonly style="width:100%; padding:8px 12px; border:1px solid var(--c-separator); border-radius:var(--r-xs); background:#f8fafc; font-weight:500;">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Delivery Expenses Fields -->
                                    <div id="fieldsDeliveryExpenses" style="display:none;">
                                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                                            <div>
                                                <label style="display:block; font-size:11px; font-weight:700; color:var(--t-label); text-transform:uppercase; margin-bottom:5px;">Delivery Manifest</label>
                                                <input type="text" id="expDelManifest" readonly style="width:100%; padding:8px 12px; border:1px solid var(--c-separator); border-radius:var(--r-xs); background:#f8fafc; font-family:monospace; font-weight:bold;">
                                            </div>
                                            <div>
                                                <label style="display:block; font-size:11px; font-weight:700; color:var(--t-label); text-transform:uppercase; margin-bottom:5px;">Arranged Delivery Vehicle</label>
                                                <input type="text" id="expDelVehicle" readonly style="width:100%; padding:8px 12px; border:1px solid var(--c-separator); border-radius:var(--r-xs); background:#f8fafc; font-weight:bold;">
                                            </div>
                                        </div>

                                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                                            <div>
                                                <label style="display:block; font-size:11px; font-weight:700; color:var(--t-label); text-transform:uppercase; margin-bottom:5px;">Driver & Partner Info</label>
                                                <input type="text" id="expDelDriverInfo" readonly style="width:100%; padding:8px 12px; border:1px solid var(--c-separator); border-radius:var(--r-xs); background:#f8fafc; font-weight:500;">
                                            </div>
                                            <div>
                                                <label style="display:block; font-size:11px; font-weight:700; color:var(--t-label); text-transform:uppercase; margin-bottom:5px;">Date & Time</label>
                                                <input type="text" id="expDelDateTime" readonly style="width:100%; padding:8px 12px; border:1px solid var(--c-separator); border-radius:var(--r-xs); background:#f8fafc; font-weight:500;">
                                            </div>
                                        </div>
                                    </div>

                                    <div style="border-top: 1px solid #e2e8f0; margin: 15px 0; padding-top: 15px;"></div>

                                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                                        <div>
                                            <label style="display:block; font-size:11px; font-weight:700; color:var(--t-label); text-transform:uppercase; margin-bottom:5px;">Expense Type <span style="color:red;">*</span></label>
                                            <select id="expType" required style="width:100%; padding:8px 12px; border:1px solid var(--c-separator); border-radius:var(--r-xs); outline:none;">
                                                <option value="" disabled selected>Select Type</option>
                                                <option value="Fuel">Fuel</option>
                                                <option value="Meals">Meals</option>
                                                <option value="Accommodation">Accommodation</option>
                                                <option value="Parking">Parking</option>
                                                <option value="Vehicle Maintenance">Vehicle Maintenance</option>
                                                <option value="Toll Charges">Toll Charges</option>
                                                <option value="Other Expenses">Other Expenses</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label style="display:block; font-size:11px; font-weight:700; color:var(--t-label); text-transform:uppercase; margin-bottom:5px;">Payment Source <span style="color:red;">*</span></label>
                                            <select id="expSource" required onchange="onExpenseSourceChanged()" style="width:100%; padding:8px 12px; border:1px solid var(--c-separator); border-radius:var(--r-xs); outline:none;">
                                                <option value="" disabled selected>Select Source</option>
                                                <option value="Petty Cash">Petty Cash</option>
                                                <option value="Collected Cash">Collected Cash on Route</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                                        <div>
                                            <label style="display:block; font-size:11px; font-weight:700; color:var(--t-label); text-transform:uppercase; margin-bottom:5px;">Amount (Rs.) <span style="color:red;">*</span></label>
                                            <input type="number" step="0.01" min="0.01" id="expAmount" required style="width:100%; padding:8px 12px; border:1px solid var(--c-separator); border-radius:var(--r-xs); font-weight:bold; font-family:monospace;">
                                        </div>
                                        <div>
                                            <label style="display:block; font-size:11px; font-weight:700; color:var(--t-label); text-transform:uppercase; margin-bottom:5px;">Receipt / Voucher No</label>
                                            <input type="text" id="expReceiptNumber" style="width:100%; padding:8px 12px; border:1px solid var(--c-separator); border-radius:var(--r-xs);">
                                        </div>
                                    </div>

                                    <div style="margin-bottom: 20px;">
                                        <label style="display:block; font-size:11px; font-weight:700; color:var(--t-label); text-transform:uppercase; margin-bottom:5px;">Description <span style="color:red;">*</span></label>
                                        <textarea id="expDescription" required rows="2" style="width:100%; padding:8px 12px; border:1px solid var(--c-separator); border-radius:var(--r-xs); outline:none; resize:none;"></textarea>
                                    </div>

                                    <button type="submit" id="btnSubmitRouteExpense" style="width:100%; background:var(--primary); color:#fff; border:none; padding:10px; border-radius:var(--r-md); font-weight:bold; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; gap:8px;">
                                        <i class="ph ph-floppy-disk"></i> Record Expense
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Right: Balances and Live List -->
                        <div>
                            <!-- Available Balances Card -->
                            <div style="border:0.5px solid var(--c-separator); border-radius:var(--r-lg); padding:20px; background:var(--c-surface); box-shadow:var(--shadow-sm); margin-bottom:20px;">
                                <h4 style="margin:0 0 15px 0; color:var(--primary); font-size:15px; font-weight:bold;"><i class="ph ph-bank"></i> Funding Source Balances</h4>
                                <div style="display:flex; flex-direction:column; gap:12px;">
                                    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #f1f5f9; padding-bottom:8px;">
                                        <div>
                                            <span style="font-weight:600; font-size:13px; color:#334155;">Available Petty Cash Balance</span>
                                            <p style="margin:2px 0 0 0; font-size:11px; color:#64748b;">Current system petty cash ledger</p>
                                        </div>
                                        <span id="lblAvailPettyCash" style="font-family:monospace; font-weight:bold; font-size:14px; color:#2e7d32;">Rs 0.00</span>
                                    </div>
                                    <div style="display:flex; justify-content:space-between; align-items:center; padding-bottom:4px;">
                                        <div>
                                            <span style="font-weight:600; font-size:13px; color:#334155;">Available Route Cash Collections</span>
                                            <p style="margin:2px 0 0 0; font-size:11px; color:#64748b;">Collected route payments minus expenses</p>
                                        </div>
                                        <span id="lblAvailRouteCash" style="font-family:monospace; font-weight:bold; font-size:14px; color:#2e7d32;">Rs 0.00</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Recorded Expenses list -->
                            <div style="border:0.5px solid var(--c-separator); border-radius:var(--r-lg); padding:20px; background:var(--c-surface); box-shadow:var(--shadow-sm); height: 320px; display: flex; flex-direction: column;">
                                <h4 style="margin:0 0 15px 0; color:var(--primary); font-size:15px; font-weight:bold;"><i class="ph ph-list-bullets"></i> Recorded Expenses</h4>
                                <div style="flex:1; overflow-y:auto; font-size:12px;" id="listRecordedExpenses">
                                    <!-- Populated dynamically -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 7: RECONCILIATION -->
                <div class="workspace-tab-panel" id="tabpanel-7" style="display:none;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <div>
                            <h3 style="margin:0; font-size:18px; font-weight:700; color:var(--t-primary);">Route Collections & Variance Reconciliation</h3>
                            <p style="margin:4px 0 0 0; font-size:13px; color:var(--t-secondary);">Count cash, verify cheques and document financial variances. Save draft or submit for final settlement.</p>
                        </div>
                    </div>
                    
                    <div id="tab7ContentContainer">
                        <div style="display:grid; grid-template-columns:1.2fr 0.8fr; gap:20px;">
                            <div>
                                <!-- Cash Reconciliation Card -->
                                <div style="border:0.5px solid var(--c-separator); border-radius:var(--r-lg); padding:20px; background:var(--c-surface); box-shadow:var(--shadow-sm); margin-bottom:20px;">
                                    <h4 style="margin:0 0 15px 0; color:var(--primary); font-size:15px; font-weight:bold;"><i class="ph ph-coins"></i> Cash Collections Counter</h4>
                                    <table style="width:100%; border-collapse:collapse; font-size:13px; margin-bottom:15px;">
                                        <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:10px 0; color:#64748b; font-weight:bold;">Expected Cash Sales</td><td style="padding:10px 0; font-weight:bold; font-family:monospace; text-align:right;" id="reconExpectedCash">Rs 0.00</td></tr>
                                        <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:10px 0; color:#64748b; font-weight:bold;">Expected Cash Collections</td><td style="padding:10px 0; font-weight:bold; font-family:monospace; text-align:right;" id="reconExpectedCollections">Rs 0.00</td></tr>
                                        <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:10px 0; color:#64748b; font-weight:bold;">Total Collected Cash</td><td style="padding:10px 0; font-weight:bold; font-family:monospace; text-align:right;" id="reconTotalExpectedCash">Rs 0.00</td></tr>
                                        <tr style="border-bottom:1px solid #f1f5f9; color:#ef4444;"><td style="padding:10px 0; font-weight:bold;">Less: Route Expenses Paid from Cash</td><td style="padding:10px 0; font-weight:bold; font-family:monospace; text-align:right;" id="reconRouteExpenses">Rs 0.00</td></tr>
                                        <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:10px 0; color:#2e7d32; font-weight:bold;">Net Expected Cash Handover</td><td style="padding:10px 0; font-weight:bold; font-family:monospace; text-align:right; color:#2e7d32;" id="reconNetExpectedCash">Rs 0.00</td></tr>
                                        <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:10px 0; color:#64748b; font-weight:bold;">Actual Counted Cash</td><td style="padding:10px 0; text-align:right;"><input type="number" step="0.01" min="0" id="reconActualCash" readonly style="padding:6px; border:1px solid #ccc; border-radius:4px; width:150px; text-align:right; font-weight:bold; font-family:monospace; background:#f8fafc;" value="0.00"></td></tr>
                                        <tr><td style="padding:10px 0; color:#64748b; font-weight:bold;">Cash Variance</td><td style="padding:10px 0; font-weight:bold; font-family:monospace; text-align:right;" id="reconCashVariance">Rs 0.00</td></tr>
                                    </table>
                                    
                                    <div style="margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 15px;">
                                        <h5 style="margin: 0 0 10px 0; color:#334155; font-size:13px; font-weight:bold; text-transform:uppercase; letter-spacing:0.5px;">Denomination Breakdown</h5>
                                        <table style="width:100%; border-collapse:collapse; font-size:12px;">
                                            <thead>
                                                <tr style="border-bottom: 2px solid #cbd5e1; text-align:left; color:#475569;">
                                                    <th style="padding:6px 4px;">Denom</th>
                                                    <th style="padding:6px 4px; text-align:center;">Collector Qty</th>
                                                    <th style="padding:6px 4px; text-align:right;">Collector Value</th>
                                                    <th style="padding:6px 4px; text-align:center; width: 90px;">Actual Qty</th>
                                                    <th style="padding:6px 4px; text-align:right;">Actual Value</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Denominations Row 5000 -->
                                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                                    <td style="padding:6px 4px; font-weight:500;">Rs 5,000</td>
                                                    <td style="padding:6px 4px; text-align:center; color:#64748b;" id="colQty5000">0</td>
                                                    <td style="padding:6px 4px; text-align:right; color:#64748b; font-family:monospace;" id="colVal5000">0.00</td>
                                                    <td style="padding:6px 4px; text-align:center;"><input type="number" min="0" id="actQty5000"  style="width:70px; padding:4px; text-align:center; border:1px solid #cbd5e1; border-radius:4px;"></td>
                                                    <td style="padding:6px 4px; text-align:right; font-weight:bold; font-family:monospace;" id="actVal5000">0.00</td>
                                                </tr>
                                                <!-- Denominations Row 2000 -->
                                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                                    <td style="padding:6px 4px; font-weight:500;">Rs 2,000</td>
                                                    <td style="padding:6px 4px; text-align:center; color:#64748b;" id="colQty2000">0</td>
                                                    <td style="padding:6px 4px; text-align:right; color:#64748b; font-family:monospace;" id="colVal2000">0.00</td>
                                                    <td style="padding:6px 4px; text-align:center;"><input type="number" min="0" id="actQty2000"  style="width:70px; padding:4px; text-align:center; border:1px solid #cbd5e1; border-radius:4px;"></td>
                                                    <td style="padding:6px 4px; text-align:right; font-weight:bold; font-family:monospace;" id="actVal2000">0.00</td>
                                                </tr>
                                                <!-- Denominations Row 1000 -->
                                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                                    <td style="padding:6px 4px; font-weight:500;">Rs 1,000</td>
                                                    <td style="padding:6px 4px; text-align:center; color:#64748b;" id="colQty1000">0</td>
                                                    <td style="padding:6px 4px; text-align:right; color:#64748b; font-family:monospace;" id="colVal1000">0.00</td>
                                                    <td style="padding:6px 4px; text-align:center;"><input type="number" min="0" id="actQty1000"  style="width:70px; padding:4px; text-align:center; border:1px solid #cbd5e1; border-radius:4px;"></td>
                                                    <td style="padding:6px 4px; text-align:right; font-weight:bold; font-family:monospace;" id="actVal1000">0.00</td>
                                                </tr>
                                                <!-- Denominations Row 500 -->
                                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                                    <td style="padding:6px 4px; font-weight:500;">Rs 500</td>
                                                    <td style="padding:6px 4px; text-align:center; color:#64748b;" id="colQty500">0</td>
                                                    <td style="padding:6px 4px; text-align:right; color:#64748b; font-family:monospace;" id="colVal500">0.00</td>
                                                    <td style="padding:6px 4px; text-align:center;"><input type="number" min="0" id="actQty500"  style="width:70px; padding:4px; text-align:center; border:1px solid #cbd5e1; border-radius:4px;"></td>
                                                    <td style="padding:6px 4px; text-align:right; font-weight:bold; font-family:monospace;" id="actVal500">0.00</td>
                                                </tr>
                                                <!-- Denominations Row 100 -->
                                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                                    <td style="padding:6px 4px; font-weight:500;">Rs 100</td>
                                                    <td style="padding:6px 4px; text-align:center; color:#64748b;" id="colQty100">0</td>
                                                    <td style="padding:6px 4px; text-align:right; color:#64748b; font-family:monospace;" id="colVal100">0.00</td>
                                                    <td style="padding:6px 4px; text-align:center;"><input type="number" min="0" id="actQty100"  style="width:70px; padding:4px; text-align:center; border:1px solid #cbd5e1; border-radius:4px;"></td>
                                                    <td style="padding:6px 4px; text-align:right; font-weight:bold; font-family:monospace;" id="actVal100">0.00</td>
                                                </tr>
                                                <!-- Denominations Row 50 -->
                                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                                    <td style="padding:6px 4px; font-weight:500;">Rs 50</td>
                                                    <td style="padding:6px 4px; text-align:center; color:#64748b;" id="colQty50">0</td>
                                                    <td style="padding:6px 4px; text-align:right; color:#64748b; font-family:monospace;" id="colVal50">0.00</td>
                                                    <td style="padding:6px 4px; text-align:center;"><input type="number" min="0" id="actQty50"  style="width:70px; padding:4px; text-align:center; border:1px solid #cbd5e1; border-radius:4px;"></td>
                                                    <td style="padding:6px 4px; text-align:right; font-weight:bold; font-family:monospace;" id="actVal50">0.00</td>
                                                </tr>
                                                <!-- Denominations Row 20 -->
                                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                                    <td style="padding:6px 4px; font-weight:500;">Rs 20</td>
                                                    <td style="padding:6px 4px; text-align:center; color:#64748b;" id="colQty20">0</td>
                                                    <td style="padding:6px 4px; text-align:right; color:#64748b; font-family:monospace;" id="colVal20">0.00</td>
                                                    <td style="padding:6px 4px; text-align:center;"><input type="number" min="0" id="actQty20"  style="width:70px; padding:4px; text-align:center; border:1px solid #cbd5e1; border-radius:4px;"></td>
                                                    <td style="padding:6px 4px; text-align:right; font-weight:bold; font-family:monospace;" id="actVal20">0.00</td>
                                                </tr>
                                                <!-- Coins Row -->
                                                <tr>
                                                    <td style="padding:6px 4px; font-weight:500;">Coins Total</td>
                                                    <td style="padding:6px 4px; text-align:center; color:#888;">-</td>
                                                    <td style="padding:6px 4px; text-align:right; color:#64748b; font-family:monospace;" id="colValCoins">0.00</td>
                                                    <td style="padding:6px 4px; text-align:center;"><input type="number" step="0.01" min="0" id="actValCoins"  style="width:70px; padding:4px; text-align:center; border:1px solid #cbd5e1; border-radius:4px;"></td>
                                                    <td style="padding:6px 4px; text-align:right; font-weight:bold; font-family:monospace;" id="actValCoinsTotal">0.00</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Cheque Reconciliation Card -->
                                <div style="border:1px solid #cbd5e1; border-radius:8px; padding:20px; background:#fff;">
                                    <h4 style="margin:0 0 15px 0; color:var(--primary); font-size:15px; font-weight:bold;"><i class="ph ph-bank"></i> Cheques Verification</h4>
                                    <table class="data-table" style="font-size:11px;">
                                        <thead>
                                            <tr>
                                                <th>Customer Name</th>
                                                <th>Cheque Number</th>
                                                <th style="text-align:right;">Amount (Rs)</th>
                                                <th style="text-align:center;">Approve</th>
                                            </tr>
                                        </thead>
                                        <tbody id="reconChequesTbody">
                                            <!-- Dynamically populated -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div>
                                <!-- Notes & Save Draft Card -->
                                <div style="border:0.5px solid var(--c-separator); border-radius:var(--r-lg); padding:20px; background:var(--c-surface); box-shadow:var(--shadow-sm); height:100%; display:flex; flex-direction:column; justify-content:space-between;">
                                    <div>
                                        <h4 style="margin:0 0 15px 0; color:var(--primary); font-size:15px; font-weight:bold;"><i class="ph ph-note"></i> Audit Remarks</h4>
                                        <textarea id="reconAuditNotes" style="width:100%; height:180px; padding:10px; border:1px solid #cbd5e1; border-radius:6px; font-size:13px; resize:none;" placeholder="Write any audit notes regarding cash discrepancy, bank transfer receipts verified, etc..."></textarea>
                                    </div>
                                    <div style="text-align:right; margin-top:20px;">
                                        <button id="btnSaveReconciliationDraft"  style="padding:10px 20px; background:#2e7d32; color:#fff; border:none; border-radius:6px; font-weight:bold; font-size:13px; cursor:pointer; width:100%;"><i class="ph ph-floppy-disk"></i> Save Reconciliation Draft</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="tab7GuardContainer" style="display:none;"></div>
                </div>

                <!-- TAB 9: RETURN STOCK VERIFICATION -->
                <div class="workspace-tab-panel" id="tabpanel-9" style="display:none;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <div>
                            <h3 style="margin:0; font-size:18px; font-weight:700; color:var(--t-primary);">Return Stock Verification</h3>
                            <p style="margin:4px 0 0 0; font-size:13px; color:var(--t-secondary);">Verify returned physical stocks and confirm route inventory updates.</p>
                        </div>
                        <div>
                            <button id="btnPrintReturnStock" style="padding:10px 16px; background:#475569; color:#fff; border:none; border-radius:6px; font-weight:bold; font-size:13px; cursor:pointer; display:flex; align-items:center; gap:6px;">
                                🖨️ Print Return Stock Report
                            </button>
                        </div>
                    </div>
                    <div style="border:0.5px solid var(--c-separator); border-radius:var(--r-lg); padding:20px; background:var(--c-surface); box-shadow:var(--shadow-sm); margin-bottom:20px;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                            <h4 style="margin:0; color:var(--primary); font-size:15px; font-weight:bold;">Returned Stock Settle Verification</h4>
                            <label style="font-weight:bold; font-size:12px; display:flex; align-items:center; gap:6px; cursor:pointer;">
                                <input type="checkbox" id="settleVerifyStock"  style="width:16px; height:16px;">
                                I have physically verified all returned inventory and confirm quantities are correct.
                            </label>
                        </div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th style="text-align:center;">Loaded</th>
                                    <th style="text-align:center;">Delivered</th>
                                    <th style="text-align:center;">Expected Returned</th>
                                    <th style="text-align:right; width:150px;">Actual Counted Returns</th>
                                </tr>
                            </thead>
                            <tbody id="settleStockTableBody">
                                <!-- Dynamically populated -->
                            </tbody>
                        </table>
                        <div style="text-align:right; margin-top:20px;">
                            <button id="btnSaveReturnStockDraft"  style="padding:10px 20px; background:#2e7d32; color:#fff; border:none; border-radius:6px; font-weight:bold; font-size:13px; cursor:pointer;">💾 Save Return Stock Draft</button>
                        </div>
                    </div>
                </div>

                <!-- TAB 10: PAYMENTS -->
                <div class="workspace-tab-panel" id="tabpanel-10" style="display:none;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <div>
                            <h3 style="margin:0; font-size:18px; font-weight:700; color:var(--t-primary);">Payments & Settlements</h3>
                            <p style="margin:4px 0 0 0; font-size:13px; color:var(--t-secondary);">Verify collected payments and map route transactions to general ledger postings.</p>
                        </div>
                    </div>

                    <!-- Dispatch Assignment Section inside Accounting final tab (Hidden to streamline interface) -->
                    <div style="display:none;">
                        <select id="settleDaVehicle">
                            <option value="">-- Select Vehicle --</option>
                            <?php foreach($data['vehicles'] as $v): ?>
                                <?php if($v->status === 'Active'): ?>
                                    <option value="<?= htmlspecialchars($v->vehicle_number) ?>"><?= htmlspecialchars($v->vehicle_number) ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <select id="settleDaDriver">
                            <option value="">-- Select Driver --</option>
                            <?php foreach($data['drivers'] as $d): ?>
                                <option value="<?= htmlspecialchars($d->first_name . ' ' . $d->last_name) ?>"><?= htmlspecialchars($d->first_name . ' ' . $d->last_name) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select id="settleDaPartner">
                            <option value="">-- None --</option>
                            <?php foreach($data['drivers'] as $d): ?>
                                <option value="<?= htmlspecialchars($d->first_name . ' ' . $d->last_name) ?>"><?= htmlspecialchars($d->first_name . ' ' . $d->last_name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div id="tab10ContentContainer">
                        <!-- Attached Credit Bills Payment Panel Card -->
                        <div style="border:0.5px solid var(--c-separator); border-radius:var(--r-lg); padding:20px; background:var(--c-surface); box-shadow:var(--shadow-sm); margin-bottom:20px;">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                                <h4 style="margin:0; color:var(--primary); font-size:15px; font-weight:bold; display:flex; align-items:center; gap:8px;">
                                    <i class="ph ph-receipt" style="color: #0284c7;"></i> Attached Credit Bills & Payments
                                </h4>
                                <span style="font-size:11px; background:#e0f2fe; color:#0369a1; padding:3px 10px; border-radius:12px; font-weight:700;">
                                    Direct Credit Settlement
                                </span>
                            </div>
                            <p style="margin:0 0 15px 0; font-size:12px; color:var(--t-secondary);">
                                Outstanding credit bills attached to this route/delivery. Apply payments directly with Cash, Bank Transfer, or Cheque options.
                            </p>
                            <div style="overflow-x:auto;">
                                <table class="data-table" style="margin-top:0; width:100%; font-size:12px;">
                                    <thead>
                                        <tr>
                                            <th>Customer</th>
                                            <th>Invoice #</th>
                                            <th>Invoice Date</th>
                                            <th style="text-align:right;">Grand Total</th>
                                            <th style="text-align:right;">Balance Due</th>
                                            <th style="text-align:center;">Status</th>
                                            <th style="text-align:center; width:130px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tab10CreditBillsTbody">
                                        <tr>
                                            <td colspan="7" style="text-align:center; color:#888; padding:15px;">Loading attached credit bills...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- General Ledger account double entry mappings card -->
                        <div style="border:0.5px solid var(--c-separator); border-radius:var(--r-lg); padding:20px; background:var(--c-surface); box-shadow:var(--shadow-sm); margin-bottom:20px;">
                            <h4 style="margin:0 0 15px 0; color:var(--primary); font-size:15px; font-weight:bold;"><i class="ph ph-briefcase"></i> Account Mappings</h4>
                            <div style="display: flex; gap: 10px; border-bottom: 1px solid #eee; margin-bottom: 15px;">
                                <button type="button" class="left-tab-btn active" id="settleDeTabCollectionsBtn" ><i class="ph ph-coins"></i> Cash/Cheques Posting</button>
                                <button type="button" class="left-tab-btn" id="settleDeTabSalesBtn" ><i class="ph ph-file-text"></i> Invoices Sales Posting</button>
                            </div>
                            <div id="settleDeCollectionsContainer"></div>
                            <div id="settleDeSalesContainer" style="display:none;"></div>
                            <div style="text-align:right; margin-top:20px;">
                                <button id="btnSaveAccountingDraft"  style="padding:10px 20px; background:#2e7d32; color:#fff; border:none; border-radius:6px; font-weight:bold; font-size:13px; cursor:pointer;"><i class="ph ph-floppy-disk"></i> Save Account Mappings Draft</button>
                            </div>
                        </div>

                    </div>
                    <div id="tab10GuardContainer" style="display:none;"></div>

                    <!-- Modal for Applying Payment to Attached Credit Bill -->
                    <div class="modal-backdrop" id="applyCreditBillPaymentModal" style="display:none; z-index:2010;">
                        <div class="modal-panel" style="max-width: 540px; width: 95%;">
                            <div class="modal-header" style="background: #0284c7; color:#fff; padding: 16px 20px; display:flex; justify-content:space-between; align-items:center;">
                                <h3 style="margin:0; font-size:16px; font-weight:700; display:flex; align-items:center; gap:8px;">
                                    <i class="ph ph-currency-dollar"></i> Apply Payment to Credit Bill
                                </h3>
                                <button type="button" onclick="closeApplyCreditPaymentModal()" style="background:none; border:none; color:#fff; font-size:18px; font-weight:bold; cursor:pointer;">✕</button>
                            </div>
                            <form id="applyCreditBillPaymentForm" onsubmit="submitApplyCreditPayment(event)">
                                <input type="hidden" id="acpInvoiceId">
                                <input type="hidden" id="acpCustomerId">
                                <div style="padding:20px; display:flex; flex-direction:column; gap:14px;">
                                    
                                    <!-- Invoice Summary Box -->
                                    <div style="background:#f0f9ff; border:1px solid #bae6fd; border-radius:6px; padding:12px; font-size:12px;">
                                        <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                                            <span style="color:#0369a1; font-weight:600;">Customer:</span>
                                            <strong id="acpCustName" style="color:#0f172a;">-</strong>
                                        </div>
                                        <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                                            <span style="color:#0369a1; font-weight:600;">Invoice Number:</span>
                                            <strong id="acpInvoiceNum" style="font-family:monospace; color:#0f172a;">-</strong>
                                        </div>
                                        <div style="display:flex; justify-content:space-between;">
                                            <span style="color:#0369a1; font-weight:600;">Balance Due:</span>
                                            <strong id="acpBalanceDue" style="font-family:monospace; color:#dc2626; font-size:13px;">Rs 0.00</strong>
                                        </div>
                                    </div>

                                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                                        <div>
                                            <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:#475569; display:block; margin-bottom:4px;">Payment Method *</label>
                                            <select id="acpPaymentMethod" onchange="toggleAcpPaymentFields()" style="width:100%; padding:8px 10px; border:1px solid #cbd5e1; border-radius:6px; font-size:12px;" required>
                                                <option value="Cash">Cash</option>
                                                <option value="Bank Transfer">Bank Transfer</option>
                                                <option value="Cheque">Cheque</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:#475569; display:block; margin-bottom:4px;">Payment Date *</label>
                                            <input type="date" id="acpPaymentDate" value="<?= date('Y-m-d') ?>" style="width:100%; padding:8px 10px; border:1px solid #cbd5e1; border-radius:6px; font-size:12px;" required>
                                        </div>
                                    </div>

                                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                                        <div>
                                            <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:#475569; display:block; margin-bottom:4px;">Payment Amount (Rs) *</label>
                                            <input type="number" step="0.01" min="0.01" id="acpAmount" style="width:100%; padding:8px 10px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px; font-weight:700; color:#16a34a; font-family:monospace;" required>
                                        </div>
                                        <div>
                                            <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:#475569; display:block; margin-bottom:4px;">Reference # / Receipt #</label>
                                            <input type="text" id="acpReference" placeholder="Optional ref" style="width:100%; padding:8px 10px; border:1px solid #cbd5e1; border-radius:6px; font-size:12px;">
                                        </div>
                                    </div>

                                    <!-- Bank Transfer Option: Bank Account Select -->
                                    <div id="acpBankTransferBox" style="display:none;">
                                        <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:#475569; display:block; margin-bottom:4px;">Destination Bank Account *</label>
                                        <select id="acpAssetAccount" style="width:100%; padding:8px 10px; border:1px solid #cbd5e1; border-radius:6px; font-size:12px;">
                                            <option value="">-- Select Bank Account --</option>
                                            <?php foreach ($data['bank_accounts'] as $bAcc): ?>
                                                <option value="<?= $bAcc->id ?>"><?= $bAcc->account_code ?> - <?= htmlspecialchars($bAcc->account_name) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <!-- Cheque Option: Cheque Details Box -->
                                    <div id="acpChequeBox" style="display:none; background:#fffbeb; border:1.5px solid #f59e0b; border-radius:8px; padding:12px;">
                                        <h5 style="margin:0 0 10px 0; font-size:11px; text-transform:uppercase; color:#b45309; font-weight:700;">Cheque Details</h5>
                                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                                            <div>
                                                <label style="font-size:10px; font-weight:700; color:#92400e; display:block; margin-bottom:3px;">Bank Name *</label>
                                                <input type="text" id="acpChequeBank" placeholder="e.g. Commercial Bank" style="width:100%; padding:6px 8px; border:1px solid #fcd34d; border-radius:4px; font-size:12px;">
                                            </div>
                                            <div>
                                                <label style="font-size:10px; font-weight:700; color:#92400e; display:block; margin-bottom:3px;">Cheque Number *</label>
                                                <input type="text" id="acpChequeNum" placeholder="6 digits" maxlength="6" pattern="\d{6}" style="width:100%; padding:6px 8px; border:1px solid #fcd34d; border-radius:4px; font-size:12px;" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                            </div>
                                        </div>
                                        <div style="margin-top:10px;">
                                            <label style="font-size:10px; font-weight:700; color:#92400e; display:block; margin-bottom:3px;">Banking Date *</label>
                                            <input type="date" id="acpChequeDate" style="width:100%; padding:6px 8px; border:1px solid #fcd34d; border-radius:4px; font-size:12px;">
                                        </div>
                                    </div>

                                    <div>
                                        <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:#475569; display:block; margin-bottom:4px;">Memo / Notes</label>
                                        <textarea id="acpNotes" rows="2" placeholder="Internal remarks..." style="width:100%; padding:8px 10px; border:1px solid #cbd5e1; border-radius:6px; font-size:12px; resize:none;"></textarea>
                                    </div>

                                </div>
                                <div style="padding:12px 20px; background:#f8fafc; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:10px; border-radius:0 0 12px 12px;">
                                    <button type="button" onclick="closeApplyCreditPaymentModal()" style="padding:8px 16px; background:#e2e8f0; color:#475569; border:none; border-radius:6px; font-size:12px; font-weight:700; cursor:pointer;">Cancel</button>
                                    <button type="submit" id="btnSubmitCreditPayment" style="padding:8px 18px; background:#0284c7; color:#fff; border:none; border-radius:6px; font-size:12px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:6px;">
                                        <i class="ph ph-check-circle"></i> Apply Payment
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- TAB 13: MARKET RETURNS -->
                <div class="workspace-tab-panel" id="tabpanel-13" style="display:none;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <div>
                            <h3 style="margin:0; font-size:18px; font-weight:700; color:var(--t-primary);">Market Returns</h3>
                            <p style="margin:4px 0 0 0; font-size:13px; color:var(--t-secondary);">Process customer product returns, select previous selling prices, and post entries.</p>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 20px; margin-bottom: 25px; align-items: start;">
                        <!-- Left Side: New Return Form -->
                        <div style="background: var(--c-surface); border: 0.5px solid var(--c-separator); border-radius: var(--r-lg); padding: 20px; box-shadow: var(--shadow-sm);">
                            <h4 style="margin: 0 0 15px 0; font-size: 14px; font-weight: bold; color: var(--primary);"><i class="ph ph-plus-circle"></i> Create Market Return</h4>
                            
                            <form id="marketReturnForm" onsubmit="submitMarketReturn(event)">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                                     <div style="display: flex; flex-direction: column; gap: 6px;">
                                         <label style="font-size: 11px; font-weight: bold; color: var(--t-label); text-transform: uppercase;">Search Customer *</label>
                                         <div style="position: relative; width: 100%;">
                                             <input type="text" id="mrCustomerSearchInput" placeholder="Type customer name to search..." onfocus="showMrCustomerSuggestions(this)" oninput="filterMrCustomerSuggestions(this)" style="width: 100%; padding: 8px 12px; border: 0.5px solid var(--c-separator); border-radius: var(--r-xs); font-size: 13px; background: var(--c-surface2); color: var(--t-primary); outline: none;" required autocomplete="off">
                                             <div id="mrCustomerSuggestions" style="display: none; position: absolute; left: 0; right: 0; z-index: 99999; background: var(--c-surface); border: 0.5px solid var(--c-separator); border-radius: var(--r-xs); max-height: 180px; overflow-y: auto; box-shadow: var(--shadow-md); width: 100%;"></div>
                                             <input type="hidden" id="mrCustomerSelect" value="" required>
                                         </div>
                                     </div>
                                    <div style="display: flex; flex-direction: column; gap: 6px;">
                                        <label style="font-size: 11px; font-weight: bold; color: var(--t-label); text-transform: uppercase;">Date</label>
                                        <input type="date" id="mrNoteDate" value="<?= date('Y-m-d') ?>" style="padding: 7px 12px; border: 0.5px solid var(--c-separator); border-radius: var(--r-xs); font-size: 13px; background: var(--c-surface2); color: var(--t-primary); outline: none; width: 100%;" required>
                                    </div>
                                </div>

                                <div id="mrCustomerInfoCard" style="display: none; background: rgba(0, 102, 204, 0.04); border: 0.5px solid rgba(0, 102, 204, 0.15); border-radius: 6px; padding: 12px 16px; margin-bottom: 15px; font-size: 12.5px;">
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                        <div><strong>Customer:</strong> <span id="mrCustName">-</span></div>
                                        <div><strong>Outstanding Balance:</strong> <span id="mrCustOutstanding" style="color: #b91c1c; font-weight: bold;">Rs 0.00</span></div>
                                        <div><strong>Credit Limit:</strong> <span id="mrCustLimit">Rs 0.00</span></div>
                                        <div><strong>Phone:</strong> <span id="mrCustPhone">-</span></div>
                                    </div>
                                </div>

                                 <style>
                                     #mrLinesTable, #mrLinesTable tbody, #mrLinesTable tr, #mrLinesTable td {
                                         overflow: visible !important;
                                     }
                                 </style>
                                 <table class="data-table" style="width: 100%; margin-bottom: 15px; overflow: visible !important;" id="mrLinesTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 35%;">Product</th>
                                            <th style="width: 12%; text-align: right;">Qty</th>
                                            <th style="width: 15%; text-align: right;">Price (Rs)</th>
                                            <th style="width: 18%;">Condition</th>
                                            <th style="width: 15%;">Remarks</th>
                                            <th style="width: 5%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="mrLinesBody">
                                        <!-- Appended dynamically -->
                                    </tbody>
                                </table>

                                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                                    <button type="button" onclick="addMrLine()" style="padding: 8px 12px; background: var(--c-surface2); color: var(--t-secondary); border: 0.5px solid var(--c-separator); border-radius: var(--r-sm); font-size: 12px; font-weight: 600; cursor: pointer;">
                                        <i class="ph ph-plus"></i> Add Line
                                    </button>
                                    <div style="font-size: 15px; font-weight: bold; color: var(--primary);">
                                        Total Return Value: <span id="mrGrandTotalText" style="color: #b91c1c;">Rs 0.00</span>
                                    </div>
                                </div>

                                <div style="text-align: right; margin-top: 20px; border-top: 0.5px solid var(--c-separator); padding-top: 15px;">
                                    <button type="submit" style="padding: 10px 20px; background: #0066cc; color: #fff; border: none; border-radius: 6px; font-weight: bold; font-size: 13px; cursor: pointer; box-shadow: var(--shadow-sm);">
                                        <i class="ph ph-check-square"></i> Save & Post Market Return
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Right Side: Historical Selling Prices -->
                        <div style="display: flex; flex-direction: column; gap: 20px;">
                            <div style="background: var(--c-surface); border: 0.5px solid var(--c-separator); border-radius: var(--r-lg); padding: 20px; box-shadow: var(--shadow-sm);">
                                <h4 style="margin: 0 0 10px 0; font-size: 14px; font-weight: bold; color: #ff9f0a; display: flex; align-items: center; gap: 6px;">
                                    <span>📊</span> Historical Selling Prices
                                </h4>
                                <p style="font-size: 11.5px; color: var(--t-secondary); margin: 0 0 15px 0;">Click on a product search box on the left, then select one of their previous invoice prices below to apply it.</p>
                                
                                <div id="mrHistoryContainer" style="min-height: 120px; display: flex; align-items: center; justify-content: center; border: 1px dashed var(--c-separator); border-radius: 6px; background: rgba(0, 0, 0, 0.01); width: 100%;">
                                    <div id="mrHistoryPlaceholder" style="color: var(--t-secondary); text-align: center; font-size: 12px; padding: 20px; width: 100%;">
                                        Select a customer and focus on a product field to view purchase history.
                                    </div>
                                    <table class="data-table" id="mrHistoryTable" style="display: none; width: 100%; font-size: 12px;">
                                        <thead>
                                            <tr>
                                                <th>Invoice</th>
                                                <th>Date</th>
                                                <th style="text-align: right;">Qty</th>
                                                <th style="text-align: right;">Price (Rs)</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="mrHistoryBody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Previously Saved Market Returns List -->
                    <div style="background: var(--c-surface); border: 0.5px solid var(--c-separator); border-radius: var(--r-lg); padding: 20px; box-shadow: var(--shadow-sm);">
                        <h4 style="margin: 0 0 15px 0; font-size: 14px; font-weight: bold; color: var(--t-primary);"><i class="ph ph-clock-counter-clockwise"></i> Route Market Return Notes</h4>
                        <table class="data-table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Return Note #</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th style="text-align: right;">Total Amount (Rs)</th>
                                    <th style="text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="mrSavedNotesBody">
                                <tr>
                                    <td colspan="5" style="text-align: center; color: var(--t-secondary); padding: 20px;">No market returns logged for this route yet.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 11: FINALIZE ROUTE -->
                <div class="workspace-tab-panel" id="tabpanel-11" style="display:none;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <div>
                            <h3 style="margin:0; font-size:18px; font-weight:700; color:var(--t-primary);">Finalize Route</h3>
                            <p style="margin:4px 0 0 0; font-size:13px; color:var(--t-secondary);">Review the route summary and submit final settlement.</p>
                        </div>
                    </div>
                    
                    <div id="tab11ContentContainer">
                        <!-- Summary Cards Grid -->
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 20px;">
                            <!-- Card 1: Sales & Collections Summary -->
                            <div style="border:0.5px solid var(--c-separator); border-radius:var(--r-lg); padding:20px; background:var(--c-surface); box-shadow:var(--shadow-sm);">
                                <h4 style="margin:0 0 15px 0; color:var(--primary); font-size:15px; font-weight:bold;"><i class="ph ph-chart-bar"></i> Sales & Collections Summary</h4>
                                
                                <div style="margin-bottom: 15px; background: #f8fafc; padding: 12px; border-radius: 6px;">
                                    <div style="display:flex; justify-content:space-between; margin-bottom: 6px; font-size: 13px;">
                                        <span>Cash Sales:</span>
                                        <strong id="sumCashSales" style="font-family:monospace;">Rs 0.00</strong>
                                    </div>
                                    <div style="display:flex; justify-content:space-between; margin-bottom: 6px; font-size: 13px;">
                                        <span>Cheque Sales:</span>
                                        <strong id="sumChequeSales" style="font-family:monospace;">Rs 0.00</strong>
                                    </div>
                                    <div style="display:flex; justify-content:space-between; margin-bottom: 6px; font-size: 13px;">
                                        <span>Bank Transfer Sales:</span>
                                        <strong id="sumBankSales" style="font-family:monospace;">Rs 0.00</strong>
                                    </div>
                                    <div style="display:flex; justify-content:space-between; margin-bottom: 6px; font-size: 13px;">
                                        <span>Credit Sales:</span>
                                        <strong id="sumCreditSales" style="font-family:monospace;">Rs 0.00</strong>
                                    </div>
                                    <div style="display:flex; justify-content:space-between; margin-bottom: 6px; font-size: 13px; color:#ef4444;">
                                        <span>Route Expenses Incurred:</span>
                                        <strong id="sumRouteExpenses" style="font-family:monospace;">Rs 0.00</strong>
                                    </div>
                                    <div style="display:flex; justify-content:space-between; font-weight:bold; border-top: 1px dashed #cbd5e1; padding-top: 6px; margin-top: 6px; font-size: 13px;">
                                        <span>Total Route Sales:</span>
                                        <strong id="sumTotalSales" style="font-family:monospace;">Rs 0.00</strong>
                                    </div>
                                </div>

                                <div style="background: #f0fdf4; padding: 12px; border-radius: 6px;">
                                    <div style="display:flex; justify-content:space-between; margin-bottom: 6px; font-size: 13px; color: #166534;">
                                        <span>Expected Cash Collections (Driver):</span>
                                        <strong id="sumExpectedCash" style="font-family:monospace;">Rs 0.00</strong>
                                    </div>
                                    <div style="display:flex; justify-content:space-between; margin-bottom: 6px; font-size: 13px; color: #166534;">
                                        <span>Actual Cash Entered:</span>
                                        <strong id="sumActualCash" style="font-family:monospace;">Rs 0.00</strong>
                                    </div>
                                    <div style="display:flex; justify-content:space-between; font-weight:bold; border-top: 1px dashed #bbf7d0; padding-top: 6px; margin-top: 6px; font-size: 13px; color: #166534;">
                                        <span>Cash Count Variance:</span>
                                        <strong id="sumCashVariance" style="font-family:monospace;">Rs 0.00</strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 2: Vehicle & Route Info -->
                            <div style="border:0.5px solid var(--c-separator); border-radius:var(--r-lg); padding:20px; background:var(--c-surface); box-shadow:var(--shadow-sm);">
                                <h4 style="margin:0 0 15px 0; color:var(--primary); font-size:15px; font-weight:bold;"><i class="ph ph-info"></i> Route & Vehicle Info</h4>
                                
                                <div style="margin-bottom: 15px; font-size: 13px;">
                                    <div style="display:flex; justify-content:space-between; margin-bottom: 8px;">
                                        <span style="color:var(--t-secondary);">Route:</span>
                                        <strong id="sumRouteName">--</strong>
                                    </div>
                                    <div style="display:flex; justify-content:space-between; margin-bottom: 8px;">
                                        <span style="color:var(--t-secondary);">Representative:</span>
                                        <strong id="sumRepName">--</strong>
                                    </div>
                                    <div style="display:flex; justify-content:space-between; margin-bottom: 8px;">
                                        <span style="color:var(--t-secondary);">Vehicle Number:</span>
                                        <strong id="sumVehicleNumber">--</strong>
                                    </div>
                                    <div style="display:flex; justify-content:space-between; margin-bottom: 8px;">
                                        <span style="color:var(--t-secondary);">Driver Name:</span>
                                        <strong id="sumDriverName">--</strong>
                                    </div>
                                    <div style="display:flex; justify-content:space-between;">
                                        <span style="color:var(--t-secondary);">Partner/Helper:</span>
                                        <strong id="sumPartnerName">--</strong>
                                    </div>
                                </div>

                                <div style="background: #f8fafc; padding: 12px; border-radius: 6px; font-size: 13px;">
                                    <div style="display:flex; justify-content:space-between; margin-bottom: 6px;">
                                        <span>Start Meter:</span>
                                        <strong id="sumStartMeter">0 KM</strong>
                                    </div>
                                    <div style="display:flex; justify-content:space-between; margin-bottom: 6px;">
                                        <span>End Meter:</span>
                                        <strong id="sumEndMeter">0 KM</strong>
                                    </div>
                                    <div style="display:flex; justify-content:space-between; font-weight:bold; border-top: 1px dashed #cbd5e1; padding-top: 6px; margin-top: 6px;">
                                        <span>Distance Traveled:</span>
                                        <strong id="sumDistanceTraveled">0 KM</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Settle Actions -->
                        <div style="border:0.5px solid var(--c-separator); border-radius:var(--r-lg); padding:20px; background:var(--c-surface); box-shadow:var(--shadow-sm); display:flex; justify-content:space-between; align-items:center;">
                            <div id="settleStatusText" style="font-size:12px; color:#c62828; font-weight:bold;">
                                Please verify Cash, Cheques, and Return stock counts under Reconciliation & Return Stock tabs to unlock Finalization.
                            </div>
                            <button id="settleSubmitBtn"  style="padding:12px 24px; background:#2e7d32; color:#fff; border:none; border-radius:6px; font-weight:bold; font-size:14px; opacity:0.5; cursor:not-allowed;" disabled>
                                <i class="ph ph-scales"></i> Settle Balancing & Finalize Route
                            </button>
                        </div>
                    </div>
                    
                    <div id="tab11GuardContainer" style="display:none;"></div>
                </div>

                <!-- COMPLETED / READ ONLY VIEW -->
                <div class="stage-section-panel" id="ssec-Completed" style="display:none;">
                    <div style="background:#f1f5f9; border:1px solid #cbd5e1; border-radius:8px; padding:15px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <h4 style="margin:0; font-size:14px; font-weight:bold; color:#2e7d32;"><i class="ph ph-flag-checkered"></i> Route Settle Balancing Finalized</h4>
                            <p style="margin:5px 0 0 0; font-size:12px; color:#666;">This route is read-only. All transactions, inventories, and GL postings are successfully finalized.</p>
                        </div>
                        <div style="display:flex; gap:10px;">
                            <button id="auto-evt-button-23"  style="padding:8px 12px; background:#0066cc; color:#fff; border:none; border-radius:4px; font-weight:bold; cursor:pointer; font-size:12px;"><i class="ph ph-file-text"></i> View Route Summary</button>
                            <button id="auto-evt-button-24"  style="padding:8px 12px; background:#e2e8f0; color:#333; border:none; border-radius:4px; font-weight:bold; cursor:pointer; font-size:12px;"><i class="ph ph-chart-bar"></i> Print Spreadsheet</button>
                            <button id="auto-evt-button-25"  style="padding:8px 12px; background:#e2e8f0; color:#333; border:none; border-radius:4px; font-weight:bold; cursor:pointer; font-size:12px;"><i class="ph ph-truck"></i> Print Loading Summary</button>
                            <button id="auto-evt-button-26"  style="padding:8px 12px; background:#e2e8f0; color:#333; border:none; border-radius:4px; font-weight:bold; cursor:pointer; font-size:12px;"><i class="ph ph-download"></i> Export CSV</button>
                        </div>
                    </div>
                    
                    <div style="display:flex; gap:10px; border-bottom:1px solid #eee; margin-bottom:15px;">
                        <button class="left-tab-btn active" id="compTabInvoicesBtn" ><i class="ph ph-file-text"></i> Invoices</button>
                        <button class="left-tab-btn" id="compTabCollectionsBtn" ><i class="ph ph-coins"></i> Settled Collections</button>
                        <button class="left-tab-btn" id="compTabVariancesBtn" ><i class="ph ph-scales"></i> Variances</button>
                    </div>

                    <div id="completedInvoicesTab">
                        <table class="data-table">
                            <thead>
                                <tr><th>Invoice Number</th><th>Time</th><th>Customer Name</th><th style="text-align:right;">Grand Total (Rs)</th><th style="text-align:center;">Status</th></tr>
                            </thead>
                            <tbody class="render-invoices-tbody"></tbody>
                        </table>
                    </div>
                    <div id="completedCollectionsTab" style="display:none;"></div>
                    <div id="completedVariancesTab" style="display:none;"></div>
                </div>

                </div> <!-- closes #stageContentWrapper -->
            </div> <!-- closes #workspaceBody -->
        </div> <!-- closes #workspaceLayoutWrapper -->

        <!-- Empty State (when no route selected) -->
        <div class="empty-state" id="midEmptyState" style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: var(--t-tertiary);">
            <span style="font-size: 50px; margin-bottom: 15px; opacity: 0.5;"><i class="ph ph-map-pin"></i></span>
            Please select a route from the left to view details.
        </div>
    </div> <!-- closes .pane-middle -->
