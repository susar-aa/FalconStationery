---
description: Mandatory UI, layout, typography, zebra striping, and database change rules for Falcon Stationery ERP modules (viewport locking, scrollable table rows only, filter bar placement above table on the right corner, SF Pro Display fonts, zebra striping, no redundant headlines, floating command bar, mandatory SQL code generation for online server).
always_on: true
---

# Falcon Stationery ERP — Mandatory System & Architectural Rules

1. **Rule 1: Viewport-Locked Screen & Scrollable Table Rows Only (Zero Page Scroll)**
   - Every module page MUST fit 100% within the screen height (`100vh`).
   - The outer page/body must NEVER scroll (`overflow: hidden;`). No page-level vertical or horizontal scrollbars.
   - Pinned layout: Navigation Bar -> Compact Action/KPI Strip -> Filter Bar -> [Scrollable Table Body] -> Pinned Pagination Footer.
   - The table container (`.erp-table-scroll` / `#table-container`) MUST use `flex: 1; min-height: 0; overflow-y: auto; overflow-x: auto;`.
   - Table headers (`thead th`) MUST be sticky (`position: sticky; top: 0; z-index: 10;`).
   - ONLY the rows (`tbody tr`) of the table scroll inside the table container.
   - Pinned pagination footer is always visible without scrolling.
   - Keep adequate bottom padding (`padding-bottom: 54px;`) so the floating command bar never covers table rows.

2. **Rule 2: Filters Always Positioned Above the Table at the Right Corner**
   - Filter bar sits directly above the table (`.erp-filter-bar`).
   - Left side: Active results counter / bulk selection info.
   - Right corner: ALL filtering options (Category, Stock Status, Active Status, Price/Amount ranges, Date ranges, Reset button) aligned to the right-hand corner.
   - All filter controls must be compact, dense, professional ERP select/input elements.

3. **Rule 3: Professional ERP System Design Language & Typography**
   - **SF Pro Display Fonts**: Mandatory across the whole system (`public/assets/fonts/sf_pro_display_*.ttf`).
   - **Zebra Striping**: Mandatory on all data tables (`tbody tr:nth-child(even)` subtly tinted).
   - **No Redundant Large Headlines**: Do not render bulky `<h1>` titles and descriptions that waste vertical screen space.
   - **Floating Command Bar**: Sleek bottom pill containing the primary search input and the `+ New` button.
   - **Interactive Status Toggles**: Use `.erp-switch` iOS-style toggle switches for operational statuses with CSRF tokens and AJAX state rollback.

4. **Rule 4: Mandatory Standalone SQL Code for Any Database Changes**
   - Whenever any database change is made or required (including `CREATE TABLE`, `ALTER TABLE`, `ADD COLUMN`, `MODIFY COLUMN`, `DROP COLUMN`, `ADD INDEX`, `CREATE TRIGGER`, schema migrations, or reference data `INSERT`/`UPDATE`), you MUST ALWAYS provide the exact, copy-pasteable SQL code block in your response.
   - Never rely solely on local PHP migration scripts. The user must be given the direct SQL statements to execute on their live online server (e.g., in phpMyAdmin, MySQL CLI, cPanel, or Plesk).
   - Use safe and idempotent syntax where possible (`IF NOT EXISTS`, clear comments) with rollback and verification steps.
