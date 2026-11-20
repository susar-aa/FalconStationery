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
   - Pinned pagination and status footer MUST remain permanently pinned at the bottom of the table card, always visible without scrolling.
   - Maintain adequate bottom padding (`padding-bottom: 54px;`) so the bottom floating island bar never obscures the last rows of data or pagination.

---

## RULE 2: Filters Always Positioned Above the Table at the Right Corner

1. **Dedicated Table Filter Toolbar**:
   - Filter controls must be positioned directly above the data table in a dedicated toolbar (`.erp-filter-bar`).
   
2. **Left Corner Layout**:
   - Quick search or active results counter (e.g., `Showing 1,050 products`) and selection/bulk action indicators.

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

## RULE 3: Professional ERP System Design Language & Typography

1. **Mandatory Font Family — SF Pro Display**:
   - Use the local `SF Pro Display` font suite (`public/assets/fonts/sf_pro_display_*.ttf`) across the whole system on all views, tables, forms, and buttons.
   - Monospace (`SF Mono`, `ui-monospace`) for SKUs, codes, currencies, and quantities.

2. **Mandatory Zebra Striping on All Data Tables**:
   - Every data table across all modules must implement alternating zebra striping (`tbody tr:nth-child(even)` subtly tinted) for effortless line tracking on dense records.

3. **No Redundant Large Headlines**:
   - Do NOT render large `<h1>` module titles or multi-line descriptive subtitles (e.g., "Inventory Catalog - Manage products, stock levels...").
   - The active module is already indicated in the top navigation bar. Keep the workspace top header strip ultra-compact and functional.

4. **Floating Command Bar**:
   - Keep the sleek floating island pill at the bottom containing the primary live-search input and the `+ New` button.

5. **Interactive Status Toggles**:
   - Operational statuses (Active / Inactive) must use interactive iOS-style toggle switches (`.erp-switch`), not static power or text icons.
   - All status toggle requests must include CSRF tokens and AJAX headers (`X-CSRF-TOKEN`, `X-Requested-With`) with instant UI updates and automatic rollback on error.

---

## RULE 4: Always Provide Standalone SQL Code for Any Database Changes (Mandatory)

1. **Explicit Production-Ready SQL in Responses**:
   - Whenever any database change is made or required (including `CREATE TABLE`, `ALTER TABLE`, `ADD COLUMN`, `MODIFY COLUMN`, `DROP COLUMN`, `ADD INDEX`, `CREATE TRIGGER`, schema migrations, or essential reference data `INSERT`/`UPDATE`), you MUST ALWAYS provide the exact, copy-pasteable SQL code block in your response.
   - Never assume database migrations run automatically on the online server or rely solely on local PHP migration scripts. The user must be given the direct SQL statements to execute on their live online server (e.g., in phpMyAdmin, MySQL CLI, cPanel, or Plesk).

2. **Safe & Idempotent SQL Syntax**:
   - Use safe statements wherever possible (`CREATE TABLE IF NOT EXISTS`, conditional column alterations, or clear comments on target table/column constraints).
   - Group the SQL code in an easily identifiable `sql` markdown code block with clear instructions on which database and environment to apply it to.
   - If rollback or verification queries are relevant, provide them alongside the migration SQL.
