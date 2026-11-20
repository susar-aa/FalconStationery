---
description: Mandatory UI and layout rules for Falcon Stationery ERP modules (viewport locking, scrollable table rows only, filter bar placement above table on the right corner, professional ERP aesthetic).
always_on: true
---

# Falcon Stationery ERP — Mandatory UI & Layout Rules

1. **Rule 1: Viewport-Locked Screen & Scrollable Table Rows Only**
   - Every module page MUST fit 100% within the screen height (`100vh`).
   - The outer page/body must NEVER scroll (`overflow: hidden;`). No page-level vertical or horizontal scrollbars.
   - Pinned layout: Navbar -> Page Header & Action Bar -> Compact Metric Strip -> Filter Bar -> [Scrollable Table Body] -> Pinned Pagination Footer.
   - The table container (`.erp-table-scroll` / `.table-container`) MUST use `flex: 1; min-height: 0; overflow-y: auto; overflow-x: auto;`.
   - Table headers (`thead th`) MUST be sticky (`position: sticky; top: 0; z-index: 10;`).
   - ONLY the rows (`tbody tr`) of the table scroll inside the table container.
   - Pagination footer is pinned at the bottom of the table card, always visible without scrolling.
   - No floating command bars/docks overlapping table rows.

2. **Rule 2: Filters Always Positioned Above the Table at the Right Corner**
   - Filter bar sits directly above the table (`.erp-filter-bar`).
   - Left side: Fast search input + active results counter / bulk selection info.
   - Right corner: ALL filtering options (Category, Stock Status, Active Status, Price/Amount ranges, Date ranges, Reset button) aligned to the right-hand corner.
   - All filter controls must be compact, dense, professional ERP select/input elements.

3. **Rule 3: Professional ERP System Design Language**
   - High information density (crisp 12px-13px typography, monospace numbers, 8-10px row padding).
   - Crisp 1px borders, subtle 4px-6px radii (no bubbly 24px+ consumer shapes).
   - Compact inline KPI badges instead of bloated oversized cards.
   - Batch database queries to eliminate N+1 query bottlenecks in views.

