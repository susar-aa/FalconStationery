# Falcon Stationery ERP — System Rules & UI Architecture Standards

These rules are MANDATORY for all existing and future modules in the Falcon Stationery ERP platform (Inventory, Sales, Billing, Orders, Purchases, GRN, HRM, Accounting, Rep Tracking, Deliveries, Reports, Settings).

---

## RULE 1: Viewport-Locked Screen & Scrollable Table Rows Only (Zero Page Scroll)

1. **Fixed Viewport (100vh)**:
   - Every module page MUST fit 100% within the screen height (`100vh`).
   - The outer page/body must NEVER scroll (`overflow: hidden;`). No page-level vertical or horizontal scrollbars are permitted.
   - The main application layout (`.app-container` and `.main-content`) must calculate height dynamically (`calc(100vh - nav_height)`) and pin all headers, toolbars, and footers.

2. **Scrollable Table Viewport Only**:
   - The table container (`.erp-table-scroll` / `.table-container`) MUST use `flex: 1; min-height: 0; overflow-y: auto; overflow-x: auto;`.
   - Table column headers (`thead th`) MUST be sticky (`position: sticky; top: 0; z-index: 10;`).
   - ONLY the rows (`tbody tr`) of the table scroll inside the table container.
   - The pagination and status footer MUST remain permanently pinned at the bottom of the table card, always visible without scrolling.

3. **No Intrusive Floating Docks**:
   - Never use floating docks or command bars that overlap or obscure data rows or pagination controls.
   - All primary actions (Add, Import, Export, Bulk Actions) belong in a clean, dedicated top ERP action bar.

---

## RULE 2: Filters Always Positioned Above the Table at the Right Corner

1. **Dedicated Table Filter Toolbar**:
   - Filter controls must be positioned directly above the data table in a dedicated toolbar (`.erp-filter-bar`).
   
2. **Left Corner Layout**:
   - Quick search input (`Search SKU, Name, Barcode, etc.`) with clear icon (`×`).
   - Active results counter (e.g., `Showing 1,050 items`) and selection/bulk indicator.

3. **Right Corner Layout (Mandatory Filter Placement)**:
   - ALL filtering options must be grouped together and aligned to the **top-right corner** above the table:
     - Category filter dropdown
     - Stock / Record status filter dropdown
     - Operational status filter dropdown (Active / Inactive)
     - Value / Price range numeric filters (Min / Max)
     - Date range picker (where applicable)
     - Reset Filters button to clear all inputs back to defaults
   - Filter inputs must be compact, dense, professional ERP select/input elements with clear labels/placeholders.

---

## RULE 3: Professional ERP System Design Language (Not a "Fancy Website")

1. **High Information Density**:
   - Use crisp, compact typography (Inter, SF Pro, Roboto, or system sans-serif; monospace for SKUs, codes, currencies, and quantities).
   - Standard table cell padding: 8px to 10px vertical, 12px horizontal.
   - Avoid oversized empty cards, huge paddings, and bloated consumer-website card layouts.

2. **Clean Enterprise Geometry**:
   - Subtle, crisp borders (`1px solid #e2e8f0` / `var(--border-color)`).
   - Clean professional border radii: `4px` to `6px` for tables, inputs, buttons, and badges. Avoid bubbly 24px+ pill radii.
   - Professional status badges: Subtle tinted backgrounds with solid dots/text (Emerald for Active/In-Stock, Amber for Low-Stock, Rose for Out-of-Stock/Inactive).

3. **Compact Inline KPI Metrics**:
   - Display summary KPIs (Total Count, Active, Warnings, Critical) as a compact inline status bar or horizontal strip at the top, rather than massive oversized cards that consume vertical screen space.

4. **Performance & Batching (N+1 Prevention)**:
   - Always batch-load child records (e.g., product variations, order lines) in a single SQL query using `IN (...)`. Never execute SQL queries inside PHP view loops.
   - Server-side pagination with database `LIMIT` and `OFFSET` is mandatory for all large datasets.

