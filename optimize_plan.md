# Curtiss ERP – Complete Production Readiness, Accounting Audit & Optimization

Perform a comprehensive technical, functional, financial and accounting audit of the entire Curtiss ERP system. The objective is to make the system stable, secure, accurate, optimized and production-ready for handling large volumes of business transactions.

**Do not start by changing code. First understand the entire system, database architecture, workflows and accounting structure. Identify actual problems before implementing fixes.**

## 1. Complete System Audit

Analyze every module, page, API, controller, model, database table and background process.

Check:
- Broken workflows, missing functionality and incorrect calculations.
- PHP errors, warnings, exceptions and JavaScript errors.
- Incorrect queries, joins, relationships and database constraints.
- Missing validations, permissions and authorization checks.
- Duplicate records and inconsistent data.
- Incorrect date, timezone, currency and decimal handling.
- Missing indexes and inefficient database queries.
- Forms, filters, pagination, search and exports.
- Cross-module integrations and transaction consistency.

Identify issues that only appear with large datasets or concurrent users.

## 2. Complete Accounting Audit – Highest Priority

Audit the entire Chart of Accounts, General Ledger, Sub-ledgers, Journal Entries and financial reports.

Verify:
- Every financial transaction generates correct double-entry accounting.
- Total debits always equal total credits.
- No duplicate or missing journal entries.
- Correct debit/credit account selection.
- Correct transaction dates and accounting periods.
- Opening balances and closing balances.
- Account hierarchy and parent/child account calculations.
- Ledger balances against COA balances.
- Sub-ledger balances against control accounts.
- Reversal, cancellation and editing workflows.
- No direct balance modifications that bypass accounting entries.

Test all transaction types, including:
- Sales, invoices and sales returns.
- Customer receipts and outstanding balances.
- Supplier invoices, GRNs and supplier payments.
- Cash, cheques, deposits and bank transfers.
- Petty cash and reimbursements.
- Expenses and route expenses.
- Stock purchases, adjustments and returns.
- Salary, payroll, salary advances, EPF and ETF.
- Bank loans and employee loans.
- VAT transactions.
- Cheque returns and bank reconciliation.
- Owner withdrawals and stock consumption.
- Opening balances and COA adjustments.

For each workflow, verify the original transaction, journal entry, relevant ledgers, COA balance and financial reports.

## 3. Reconciliation & Financial Integrity

Perform automated cross-checks:

- Trial Balance debit total = credit total.
- General Ledger closing balances = corresponding COA balances.
- Customer balances = customer subsidiary ledgers = Accounts Receivable control account, subject to correctly classified reconciling items.
- Supplier balances = supplier subsidiary ledgers = Accounts Payable control account.
- Petty Cash balance = Petty Cash transaction history.
- Bank ledger balances = bank account balances.
- Cheque-in-hand balance = outstanding cheque records.
- EPF/ETF payable balances = unpaid contribution liabilities.
- Salary payable = outstanding approved payroll liabilities.
- Salary advance accounts = employee advance balances.
- Inventory valuation = stock ledger valuation.
- Stock quantities = underlying stock movements.
- Bank reconciliation balances = finalized bank statements and book balances.

Identify differences and trace them to their original transactions. Never hide differences by directly adjusting balances.

## 4. Database & Data Integrity

Inspect the complete database.

Check:
- Primary keys, foreign keys and unique constraints.
- Missing indexes and unnecessary indexes.
- Orphaned records.
- Duplicate transaction references.
- Incorrect relationships between modules.
- Missing transaction links and journal references.
- NULL values in critical fields.
- Decimal precision for financial amounts.
- Date and timestamp consistency.
- Soft deletes and inactive records.
- Transaction rollback and partial-save problems.

Use database transactions for all multi-step financial operations.

Ensure simultaneous users cannot create duplicate payments, invoices, journal entries or stock movements.

## 5. Performance & Large Dataset Optimization

Optimize the system for substantial business data volumes.

Review:
- Slow SQL queries and N+1 query problems.
- Full-table scans.
- Missing composite indexes.
- Excessive PHP loops and memory usage.
- Unnecessary API requests.
- Repeated calculations.
- Large data exports.
- Dashboard loading time.
- Pagination and server-side filtering.

Implement efficient server-side pagination for large lists.

Ensure pagination supports:
- Search across relevant fields.
- Date ranges.
- Multiple filters.
- Sorting.
- Accurate total record counts.
- Stable results when new records are inserted.
- Appropriate page-size limits.

Avoid loading thousands of records into the browser when only one page is needed.

Optimize reports and exports without changing their financial results.

## 6. Security & Validation

Perform a security audit covering:

- SQL injection.
- XSS and CSRF.
- Authentication and session handling.
- Role-based permissions.
- Unauthorized access to financial records.
- File upload validation.
- Sensitive data exposure.
- API authorization.
- Password handling.
- Audit log protection.
- Destructive actions and approval workflows.

Validate all financial inputs on the server side. Never rely only on frontend validation.

## 7. Audit Trails & Financial Controls

Verify that all important operations record:

- User.
- Date and time.
- Original transaction reference.
- Previous and updated values where applicable.
- Approval details.
- Related journal entry.
- Reversal or cancellation reference.

Posted accounting transactions should not be silently deleted or overwritten.

Implement safe correction and reversal workflows where missing.

## 8. CLI & Automated Testing

Use the existing PHP CLI and database environment to build and execute automated tests.

Create a structured test suite covering:

- Unit tests for calculations.
- Integration tests for module workflows.
- Accounting double-entry tests.
- Database integrity tests.
- API tests.
- Permission tests.
- Regression tests.
- Large dataset performance tests.
- Concurrent transaction tests.

Create realistic test scenarios using isolated test data or a separate test database.

Do not run destructive tests against production data.

Test complete workflows, not just individual pages.

Examples:
1. Create an invoice → receive payment → verify customer ledger, AR, journal and COA.
2. Create a GRN → record supplier payment → verify inventory, supplier ledger, AP and bank/cash accounts.
3. Issue a cheque → deposit → clear or return → verify every accounting entry.
4. Issue salary advance → recover through payroll → verify employee advance account and salary payable.
5. Record petty cash expense → reimburse → verify petty cash balance and journals.
6. Reconcile bank statements across consecutive months.
7. Process a stock return → verify stock quantity, valuation and accounting.

Every test must verify that debits equal credits and no duplicate transactions are created.

## 9. UI & User Experience

Review all screens for consistency and practical usability.

Check:
- Responsive desktop and mobile layouts.
- Loading states and error messages.
- Empty states.
- Form validation messages.
- Search and pagination usability.
- Table performance.
- Print layouts and PDF generation.
- Date and currency formatting.
- Navigation and module consistency.

Maintain the existing clean black-and-white Curtiss ERP design direction.

Do not redesign working screens unnecessarily.

## 10. Practical Business Scenario Testing

Think like an actual accountant, cashier, finance manager and ERP administrator.

Identify practical situations that could cause real financial problems, including:

- Paying the same invoice twice.
- Receiving a partial customer payment.
- Depositing a cheque that is later rejected.
- Cancelling a transaction after it has been reconciled.
- Editing an employee after salary advances exist.
- Changing a product cost after previous GRNs.
- Returning damaged stock.
- Reopening a previous accounting period.
- Two users processing the same payment simultaneously.
- A transaction failing halfway through posting.
- Deleting or deactivating a bank account with existing transactions.
- Processing payroll twice for the same period.
- Reversing a transaction after a related payment has been made.

Test how the system handles each situation and fix genuine problems.

## 11. Implementation Rules

- Analyze before modifying.
- Preserve existing business logic and working features.
- Do not replace existing accounting architecture unnecessarily.
- Do not delete historical financial records.
- Do not directly modify balances to force reconciliation.
- Use proper journal reversals for posted transaction corrections.
- Prevent duplicate submissions and repeated posting.
- Preserve existing APIs and Rep App integrations.
- Back up the database before any schema or data migration.
- Use reversible migrations wherever possible.
- Never run destructive operations against production during testing.

### 11.1 Mandatory Enterprise ERP UI & Layout Rules

1. **Rule 1: Viewport-Locked Screen & Scrollable Table Rows Only (Zero Page Scroll)**
   - The entire module page MUST fit 100% within the screen height (`100vh`).
   - The outer page/body must NEVER scroll (`overflow: hidden;`). No page-level vertical or horizontal scrollbars.
   - Pinned layout: Navigation Bar -> Page Header & Action Bar -> Compact Metric Strip -> Filter Bar -> [Scrollable Table Body] -> Pinned Pagination Footer.
   - The table container (`.erp-table-scroll` / `#table-container`) MUST use `flex: 1; min-height: 0; overflow-y: auto; overflow-x: auto;`.
   - Table headers (`thead th`) MUST be sticky (`position: sticky; top: 0; z-index: 10;`).
   - ONLY the rows (`tbody tr`) of the table scroll inside the table container.
   - Pagination footer is pinned at the bottom of the table card, always visible without scrolling.
   - Never use floating command bars or docks overlapping table rows. Action buttons belong in the clean top header toolbar.

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

4. **Rule 4: Mandatory Standalone SQL Code for Online Server Execution**
   - Whenever any database change is made or required (including `CREATE TABLE`, `ALTER TABLE`, `ADD COLUMN`, `MODIFY COLUMN`, `DROP COLUMN`, `ADD INDEX`, `CREATE TRIGGER`, schema migrations, or data fixes / default values), ALWAYS provide the exact, copy-pasteable SQL snippet in your response.
   - Never rely solely on automated local migration runners or PHP scripts; the user must be given standalone SQL code ready to execute directly on the production/online database (e.g., via phpMyAdmin, cPanel, Plesk, or MySQL CLI).
   - Provide safe and idempotent SQL statements (`IF NOT EXISTS`, conditional alterations, or explicit checks) along with rollback and verification instructions.

### 11.2 Module Optimization Progress

- [x] **Module 1: Inventory Catalog (`InventoryController`, `Item`, `Views/inventory/index.php`)**
  - Applied 100vh viewport locking (`.erp-viewport-view`). Page body does not scroll.
  - Implemented `.erp-table-scroll` with sticky `thead` where only product table rows scroll.
  - Filter bar moved directly above the table with all filters (Category, Stock Status, Status, Min Rs, Max Rs, Reset) aligned to the top-right corner.
  - Relocated actions (`+ New Product`, `Import`, `Export`, `Reservations`, `Trash`) to clean top ERP action toolbar, eliminating the floating command pill.
  - Replaced oversized consumer pastel cards with compact, high-density KPI status badges.
  - Fixed N+1 query bottleneck by introducing batch variation preloading `Item::getVariationsForItems()`.
  - Added project rules in `AGENTS.md` and `.agents/rules/erp-ui-standards.md`.
- [x] **Module 2: Sales, Invoices & Sales Order Center (`SalesController`, `SalesOrderController`, `Invoice`, `Views/sales/`, `Views/sales_orders/`)**
  - **Audit Findings**:
    - Discovered an extreme N+1 query loop in `create()` where variations were not preloaded in the controller, causing `sales/index.php` to query variations in a loop for all 1,050+ catalog items on every single invoice page load (>1,050 individual SQL queries).
    - Customer outstanding balance calculations ran 3 correlated subqueries (`invoices`, `customer_payments`, `credit_notes`) across all active customers without composite index optimization.
    - Missing CSRF token in `<form id="invoiceForm">` and missing CSRF validation in `SalesController::store()`.
    - No double-submission protection on submit buttons (`Save & Close`, `Save & New`, `Save & Print`, `Save & WhatsApp`), creating risks of duplicate invoices and double ledger postings.
    - Invoices and Sales Orders list views suffered from unlocked viewports causing page-level scrolling, with bulky multi-row filter cards occupying half the screen and pushing table rows out of view.
    - Hardcoded pagination limit of 10 records per page in both `SalesController::index()` and `SalesOrderController::index()`.
    - Missing CSRF validation and RBAC checks on `delete()`, `bulk_action()`, and `convert_to_so()`.
    - `convert_to_so` was exposed via unauthenticated GET request with raw browser `confirm()`, vulnerable to accidental execution or CSRF.
    - Absence of alternating zebra striping and sticky table headers on the invoice and sales order data tables.
  - **Fixes & Optimizations**:
    - Batch preloaded variations in `SalesController::create()` in 1 single database query using `Item::getVariationsForItems()`, and guarded view against redundant per-item variation queries.
    - Added CSRF token protection to the main invoice form and enforced `$this->validateCsrfOrDie()` in `SalesController::store()`.
    - Added RBAC permission enforcement (`$this->checkPermission('sales', 'view' | 'create_edit' | 'delete')`) across both controllers.
    - Added double-submission lock (`isInvoiceSubmitting`) and active button disabling to prevent concurrent duplicate invoice creation.
    - Preserved 100% of the existing invoice creation UI layout, styles, and workflows per user instructions ("do not redesign it").
    - Overhauled Invoices Index & Sales Order Center to 100vh Viewport-Locked layout (`.inv-erp-workspace`, `.so-erp-workspace`, sticky `thead`, table rows only scrolling inside `.erp-table-scroll` with `padding-bottom: 54px;`, pinned `.erp-pagination` footer).
    - Replaced the bulky filter cards with an ultra-compact `.erp-filter-bar` aligned to the top-right corner above the table (Customer, Date Range, Channel/Source, Rep, Status, Reset).
    - Added high-density KPI stat strips on both screens (`Total Records`, `Total Sales / Order Value`, `Pending Orders / Unpaid Accounts`).
    - Made pagination limits dynamic (15, 25, 50, 100, 1000/All) with default of 25.
    - Upgraded `convert_to_so` to a secure POST confirmation modal (`#convertModal`) with CSRF protection and clear accounting warnings.
    - Upgraded `delete` and bulk action flows with strict CSRF verification and admin password authentication.
    - Applied SF Pro Display fonts, SF Mono for codes/amounts, and alternating zebra striping (`tbody tr:nth-child(even)`).
    - **Invoice Edit & Sales Rep Audit Findings**:
      - Opening an existing invoice for editing displayed the Sales Rep field as "Select Rep..." (unselected).
      - Root cause 1: `Invoice::getInvoiceById($id)` queried `i.*` without joining `rep_daily_routes`, leaving `$inv->rep_name` undefined or null.
      - Root cause 2: `app/Views/sales/index.php` fetched reps strictly with `job_title = 'Rep'`, excluding reps with job titles like `'Sales Representative'` or `'Sales Rep'`.
      - Root cause 3: In `SalesController::store()`, `$_POST['rep_name']` was not resolved to a route ID (`getOrCreateRouteForRep`), and `$_POST['rep_route_id']` remained tied to the old route or empty.
      - Root cause 4: `Invoice::updateInvoiceWithAccounting()` completely omitted `rep_route_id` and `rep_name` from the `UPDATE invoices SET ...` query, preventing changed reps from being saved.
      - Root cause 5: Catalog items in `SalesController::edit()` suffered from the same N+1 variation query loop present earlier in `create()`.
    - **Invoice Edit & Sales Rep Fixes & Optimizations**:
      - Updated `Invoice::getInvoiceById()` and `SalesController::edit()` to automatically resolve the Sales Rep name from `rep_daily_routes` (via `rep_route_id`), `users`, or `employees` table.
      - Enhanced `app/Views/sales/index.php` to query strictly Sales Representatives (`e.job_title = 'Rep' OR LOWER(e.job_title) LIKE '%rep%' OR u.role = 'rep'`), filtering out non-rep staff (Drivers, Accountants, Admins, Office staff, Cashiers, Storekeepers), with case-insensitive matching and fallback rendering so the current rep is always pre-selected.
      - Fixed 500 error (`ERR-SYS-1E4AB4DC-1795522542`) by safely decoupling route resolution in `SalesController::store()` and `SalesOrderController::store()`. Prevented uncaught PDOExceptions when saving counter/direct invoices by returning `null` when no physical route exists rather than attempting invalid inserts into `rep_daily_routes`.
      - Removed runtime DDL (`ALTER TABLE`) and uncached `SHOW COLUMNS` from `Invoice::__construct()`, implementing static request-lifecycle caching (`hasRepNameColumn()`) to prevent unbuffered query conflicts during transactions.
      - Updated `Invoice::updateInvoiceWithAccounting()` and `createInvoiceWithAccounting()` to save both `rep_route_id` and `rep_name`.
      - Optimized `SalesController::edit()` by batch-preloading all item variations in 1 query, eliminating 1,050+ N+1 queries on invoice editing.
      - Generated standalone copy-pasteable production SQL for `ALTER TABLE invoices ADD COLUMN rep_name` and backfill migration.
- [x] **Module 3: Purchases & GRN (`PurchaseController`, `GRNController`, `PurchaseOrder`, `GRN`, `Views/purchases/`, `Views/grns/`)**
  - **Audit Findings**:
    - Discovered missing CSRF tokens on Delete PO, Delete GRN, and Admin Approval POST forms, causing security risks and potential CSRF exploits.
    - Discovered severe N+1 query loops in PO `index()` (`hasMixItems` in loop), PO `create()` / `edit()`, and GRN `create()` / `edit()` (`getItemVariations` called for 1,050+ items in a loop).
    - GRN index was missing pagination controls in the UI, locking users to 10 records.
    - Unprotected DELETE on POs allowed potential deletion of received POs that already had GRN and stock movements.
  - **Fixes & Optimizations**:
    - Eliminated all N+1 bottlenecks by prefetching `has_mix` and `items_count` in the main SQL queries, and batch-preloading catalog variations using `Item::getVariationsForItems()`.
    - Protected referential integrity in `PurchaseOrder::deletePO()` with atomic database transaction and blocked deletion of received POs.
    - Added RBAC permission checks (`checkPermission('purchase', ...)`, `checkPermission('grn', ...)`) and CSRF tokens across all actions.
    - Applied 100vh Viewport-Locked ERP layout (`.erp-viewport-view`, sticky `thead`, scrollable table rows only, pinned pagination footer) across both Purchase Orders and GRN.
    - Grouped all filters (Supplier, Status, Period, Reset) to the top-right corner above the table.
    - Enforced SF Pro Display typography, monospace data cells, and alternating zebra striping on all tables.
    - Standardized bottom floating command bar (320px live search + Create action).
    - Replaced raw browser `confirm()` with sleek CSRF-protected ERP modals for Delete and Admin Approval.
- [x] **Module 4: Customer Center & CRM (`CustomerController`, `Customer`, `Views/customers/index.php`)**
  - **Audit Findings**:
    - Page-level body scrolling was active with oversized consumer pastel cards and a massive pending review table pushing the main customer table off the viewport.
    - Filters (Route, Ledger) were placed on the left instead of being grouped at the top-right corner above the table.
    - Missing CSRF validation on all customer mutations (`add_customer`, `update_customer`, `review_customer`, `quick_review`, `api_add_customer`, `importCSV`, `delete`), leaving the platform open to cross-site request forgery.
    - Missing role-based authorization checks (`checkPermission`) on customer viewing, creation, and CSV import.
    - Delete customer modal had mismatched element IDs (`delete_customer_id` vs `delete-customer-id`) and lacked the `submitDeleteCustomer` event listener, breaking the AJAX delete workflow.
    - Filter counter did not account for the Ledger status filter, returning an inaccurate filtered record count.
  - **Fixes & Optimizations**:
    - Applied 100vh Viewport-Locked ERP layout (`.cust-erp-workspace`, sticky `thead`, only table rows scroll inside `.erp-table-scroll`, pinned pagination footer).
    - Grouped all filters (Route, Ledger Status, Operational Status, Reset) aligned to the **top-right corner** directly above the data table (Rule 2).
    - Replaced the 3 oversized pastel cards with a compact, high-density KPI stat strip (Total Customers, Total Outstanding, Owed Accounts).
    - Replaced the vertical-bloating pending review table with a sleek, compact alert bar (36px) featuring 1-click Quick Verify and Review actions.
    - Enforced SF Pro Display typography suite, monospace formatting on customer codes, balances, and phone numbers, and alternating zebra striping (`tbody tr:nth-child(even)`) across all main tables, Customer Profile modal tabs (Activity Ledger, Invoices, Cheques), and statement ledgers.
    - Standardized the floating island command bar (320px live search + CSV Export + CSV Import + `+ New Customer`).
    - Added full CSRF protection and token validation (`validateCsrfOrDie`) across all customer mutating endpoints and modals.
    - Added RBAC permission checks (`checkPermission('customer', 'view' | 'create_edit' | 'delete')`).
    - Fixed delete customer modal IDs and bound it to `submitDeleteCustomer` with CSRF protection and error handling.
    - Provided production composite indexing SQL for `customers`, `invoices`, and `customer_payments`.
- [x] **Module 5: Supplier Management & Supply Chain Center** (COMPLETED)
  - **Issues Found**:
    - Unlocked viewport layout allowing page-level body scrolling, with oversized pastel stat cards pushing the table out of view.
    - Filter shelf positioned on the left rather than aligned to the top-right corner above the data table.
    - Missing CSRF token generation and validation on supplier creation, profile updates, and status changes.
    - Missing granular RBAC permission checks (`checkPermission('supplier', 'view' | 'create_edit')`) on index, mutations, and CSV export.
    - Static status text badge with no interactive status toggle for operational statuses.
    - Lack of alternating zebra striping and sticky headers on modal lists (Activity Ledger, Purchase Orders, Products).
  - **Fixes & Optimizations**:
    - Converted to 100vh Viewport-Locked ERP layout (`.sup-erp-workspace`, sticky `thead`, scrollable rows inside `.erp-table-scroll` with `padding-bottom: 54px`, pinned pagination footer).
    - Replaced oversized cards with a compact KPI Stats Strip (Total Suppliers, Total Payable Outstanding, Accounts to Settle).
    - Grouped all filters (Ledger Status, Account Status, Reset) aligned to the **top-right corner** directly above the data table (Rule 2).
    - Added interactive iOS status toggle switches (`.erp-switch`) with CSRF headers, AJAX updates, and state rollback on error (Rule 3.5).
    - Applied SF Pro Display font suite, monospace formatting for balances and phones, and alternating zebra striping (`tbody tr:nth-child(even)`) on main table and modal tabs (Activity Ledger, POs, Products).
    - Standardized floating island command bar (320px live search + Export CSV + `+ New Supplier` CTA button).
    - Added strict CSRF protection (`validateCsrfOrDie()`) across supplier addition, updates, and status toggles.
    - Enforced RBAC authorization (`checkPermission('supplier', 'view' | 'create_edit')`).
    - Provided safe production SQL script for `vendors` indexes and schema validation.
- [x] **Module 6: Service Provider Center (`ServiceProviderController`, `ServiceProvider`, `Views/service_providers/index.php`)** (COMPLETED)
  - **Issues Found**:
    - Unlocked page layout with page-level body scroll and oversized pastel stat cards.
    - Category and Ledger filter controls were on the left shelf with missing Account Status filter.
    - Missing CSRF token validation and RBAC checks across service provider registration, profile updates, service bill entries, and status toggles.
    - Operational status was rendered as a static text badge with no interactive toggle.
    - Lack of alternating zebra striping on the master table and modal lists (Activity Ledger, Service Bills SRN history).
  - **Fixes & Optimizations**:
    - Locked to 100vh viewport (`.sp-erp-workspace`, sticky `thead`, table rows only scrolling in `.erp-table-scroll` with `padding-bottom: 54px;`, pinned `.erp-pagination` footer).
    - Replaced 3 oversized cards with high-density KPI stat strip (`Total Service Providers`, `Total Payable Outstanding`, `Accounts to Settle`).
    - Grouped all filters aligned to the **top-right corner** above the table (Category, Ledger, Account Status, Reset button).
    - Added interactive iOS status toggle switches (`.erp-switch`) with CSRF tokens, AJAX endpoint (`/serviceprovider/toggle_status/{id}`), and instant state rollback on failure.
    - Enforced SF Pro Display typography suite, monospace formatting for currencies and codes, and alternating zebra striping (`tbody tr:nth-child(even)`) on main table and modal tabs.
    - Standardized floating bottom command bar pill (320px live search + `+ New Service Provider` button).
    - Added strict CSRF validation (`validateCsrfOrDie()`) and RBAC checks (`checkPermission('supplier', 'view' | 'create_edit')`) across all endpoints and forms.
    - Provided copy-pasteable production SQL for `service_providers` and related tables per Rule 1 & Rule 4.
- [x] **Module 7: Rep Tracking & Route Audits (`RepTrackingController`, `RepTracking`, `app/Views/rep-tracking/`)**
  - **Audit Findings**:
    - **Broken RBAC & Privilege Escalation Risks**: Main dashboard views (`index()` and `history()` via `renderRouteDashboard()`) lacked `$this->checkPermission('reptracking', 'view')`. Unprivileged users could access complete daily route operational tracking, live rep locations, vehicle dispatches, and financial settlements.
    - **RBAC Slug Typo Lockout**: Lines 3083 & 3111 in `api_add_route_expense` and `api_delete_route_expense` checked `'rep_tracking', 'edit'` instead of `'reptracking', 'create_edit'`. Because `core/RbacService.php` checks `$action === 'create_edit'`, this caused non-admin users with reptracking permissions to be locked out.
    - **Incorrect Permission in Route Deletion**: `delete_route()` checked `perms['sales']['can_delete']` instead of canonical `reptracking:delete`.
    - **Mutating APIs Lacking Authorization**: Multiple critical state mutation APIs (`arrange`, `finalize`, `api_update_route_status`, `api_verify_collections`, `api_adjust_variance_billing`, `api_apply_substitution`, `api_finalize_collections`, `api_delete_sales_order`, `api_attach_invoices`, `api_create_binding`, `api_unbind_route`, `api_save_reconciliation`, `api_process_delivery_visit`, `api_save_return_stock`, `api_save_accounting_entries`, `api_save_route_notes`, `api_detach_invoice`, `create_route_manual`, `api_save_market_return`, `api_apply_credit_bill_payment`, `api_cancel_credit_bill_draft`) lacked RBAC permission validation.
    - **Fatal Error Bug in `api_delete_sales_order`**: When deleting a sales order with reserved stock containing item variations, `$itemModel->syncVariationsJsonColumn()` was called on a `null` variable because `$itemModel` was only instantiated in the `if ($stockStatus === 'deducted')` branch, causing a 500 fatal crash.
    - **Missing Exception Rollback**: In `api_delete_sales_order`, if an exception occurred during item deletion or stock revert, `$db->rollBack()` was omitted, potentially leaving open uncommitted transactions.
    - **Double-Entry General Ledger Balance Disruption**: In `api_save_accounting_entries`, an unconditional `UPDATE transactions SET account_id = :aid, credit = :cred WHERE journal_entry_id = :jid AND credit > 0` set all credit transaction rows (including Tax / Discount lines) to the invoice gross total `$gTotal`, breaking double-entry trial balance ($Debit \ne $Credit).
    - **Redundant View-Level Database Queries**: `_route_selection_filters.php` instantiated a new `Database()` object directly in the view to query reps, despite `loadRouteDashboardData()` already querying reps in the controller.
    - **Route Dropdown Filter Truncation**: Route filter dropdown was built using only the current page's 20 items (`$data['routes']`) rather than all unique route names in the system.
    - **Database Performance & Missing Composite Indexes**: Correlated subqueries in `getUnifiedRoutes()` and `getCompletedRoutes()` performed full table scans on `invoices` (`GROUP BY rep_route_id`) and `pending_collections` (`GROUP BY route_id`) without composite indexes on `(rep_route_id, status)` and `(route_id, status)`.
  - **Fixes & Optimizations**:
    - Preserved 100% of existing UI layout, multi-panel workflow, tabs 1-12, and Leaflet map integration without redesign per strict instructions.
    - Enforced `$this->checkPermission('reptracking', 'view')` in `renderRouteDashboard()` and all print/report actions (`balancing_report`, `spreadsheet`, `export_csv`, `print_loading`, `print_route_invoices`, `print_route_invoices_summary`, `print_return_stock`, `print_market_return`).
    - Enforced `$this->checkPermission('reptracking', 'create_edit')` and `checkPermission('reptracking', 'delete')` across all mutating endpoints.
    - Fixed permission slug typo from `'rep_tracking', 'edit'` to `'reptracking', 'create_edit'`.
    - Fixed fatal crash in `api_delete_sales_order` by instantiating `Item` model at the method scope and added transaction rollback in `catch`.
    - Targeted primary AR debit row and primary Revenue credit row with `ORDER BY debit/credit DESC LIMIT 1` in `api_save_accounting_entries` to prevent ledger corruption.
    - Preloaded all distinct routes and filter reps in controller `loadRouteDashboardData()`, eliminating view database queries and enabling full catalog route selection in the filter bar.
    - Generated standalone copy-pasteable production SQL composite indexes for live server execution.
- [x] **Module 8: Double-Entry Accounting & Banking (Bank Register, Ledgers & Bank Reconciliation Engine)**
    - **Audit Findings (Bank Register & Account Ledgers)**:
      - Unlocked viewport layout allowing page-level body scrolling, with bulky stat cards pushing the table out of view and creating an awkward web layout.
      - Absence of a chronological Running Balance column — the single most critical data point expected in any bank or cash register.
      - Lack of top-right filter controls (no date range preset, no date pickers, no status filter, no inflow/outflow filter), forcing users to view all records in a single unpaginated dump.
      - Subquery duplication in `ChartOfAccount::getBankLedger()`, executing repeated subqueries for every single transaction row.
      - Missing RBAC authorization (`checkPermission('accounting', 'view' | 'create_edit')`) on the ledger and reconciliation endpoints.
      - External font dependency instead of local offline SF Pro Display font suite.
    - **Audit Findings (Bank Reconciliation Engine — `/bankreconciliation`)**:
      - **Critical Disconnect Bug**: `finalizeReconciliation()` only marked `transactions.is_reconciled = 1` while leaving `transactions.is_cleared = 0`. As a result, transactions reconciled and locked in bank reconciliation remained displayed as "UNCLEARED" on the Bank Register.
      - **Security & Authorization Flaws**: `BankReconciliationController` had an incorrect unauthenticated redirect to non-existent `/users/login` instead of `/auth/login`. Lacked CSRF verification across mutating POST/AJAX endpoints (`store`, `match_ajax`, `bank_only_ajax`, `finalize_ajax`, `reopen_ajax`). `delete($id)` was an unprotected GET link allowing deletion without confirmation tokens. Lacked granular RBAC (`checkPermission('accounting', 'view' | 'create_edit' | 'delete')`).
      - **UI Architecture Flaws**: Violated Mandatory Rule 1 (lacked 100vh Viewport lock; page-level scrollbars present across all 4 views). Violated Mandatory Rule 2 (history list lacked top-right filter toolbar for bank accounts, statuses, and live search). Violated Mandatory Rule 3 (loaded external CDN Google Fonts / Inter / FontAwesome instead of local SF Pro Display, and lacked alternating zebra striping).
      - **Navigation Disconnect**: Clicking "Reconcile" on the Bank Register used an obsolete disconnected screen `banking/reconcile` rather than routing seamlessly into the dedicated Bank Reconciliation engine.
    - **Fixes & Optimizations**:
      - **Bank Register**: Converted to 100vh Viewport-Locked ERP layout (`.bank-register-workspace`, sticky `thead`, table rows only scrolling inside `.erp-table-scroll` with `padding-bottom: 54px;`, pinned `.erp-pagination` footer with dynamic page sizes: 25, 50, 100, All). Added chronological historical Running Balance calculation (`$t->running_balance`). Grouped all filters aligned to the **top-right corner** (Rule 2). Replaced bulky cards with compact KPI stat strip. Monospace `SF Mono` formatting.
      - **Bank Reconciliation Engine**:
        - Fixed `BankReconciliation::finalizeReconciliation()` to atomically update both `is_reconciled = 1` and `is_cleared = 1` in `transactions`, perfectly synchronizing with the Bank Register.
        - Fixed `reopenReconciliation()` and `deleteReconciliation()` to cleanly revert `is_reconciled = 0` and `is_cleared = 0`.
        - Filtered out zero-amount items in `populateReconciliationItems()` with `AND (t.debit > 0 OR t.credit > 0)`.
        - Fixed login redirect to `/auth/login`, added `$this->generateCsrfToken()`, enforced `$this->validateCsrfOrDie()` on all mutating endpoints (`store`, `match_ajax`, `bank_only_ajax`, `finalize_ajax`, `reopen_ajax`, `delete`), and enforced RBAC permissions (`view`, `create_edit`, `delete`).
        - Integrated `BankingController::reconcile($accountId)` to check for active draft reconciliations and route directly into `/bankreconciliation/reconcile/{id}` or `/bankreconciliation/create?account_id={$accountId}`.
        - Redesigned `bank_reconciliation/index.php` into 100vh Viewport-Locked ERP layout with sticky header, scrollable rows, top-right filter toolbar (Bank Account filter, Status filter: Draft/Finalized, search, reset), local SF Pro Display, monospace numbers, zebra striping, and secure CSRF delete modal.
        - Redesigned `bank_reconciliation/create.php` with auto-selection of bank accounts via GET parameter, dynamic opening balance and prior month status fetch, and CSRF protection.
        - Redesigned `bank_reconciliation/reconcile.php` into 100vh Viewport-Locked workspace with sticky KPI strip, top-right transaction filters (Match status, Origin, Flow type, search), and CSRF tokens across all AJAX requests (`match_ajax`, `bank_only_ajax`, `finalize_ajax`, `reopen_ajax`).
        - **Opening Balance Difference & Target Editing Engine**:
          - Identified root cause of the unreconciled difference matching the opening balance value (`34,215.66`): `statement_opening_balance` had been saved with an inverted negative sign (`-34,215.66`), which canceled out the matched opening balance deposit (`ADJ-20261110-131536` for `+34,215.66`), driving cleared bank balance to negative `-12,414.50` and creating an exact difference of `34,215.66`.
          - Added dynamic statement targets editing engine (`updateStatementTargets()` in model, `update_statement_ajax` in controller, `#statementTargetsModal` in workspace view) allowing live adjustment of Statement Opening and Target Statement Closing balances with real-time recalculation of difference, cleared balance, and finalize lock state.
          - Provided standalone production SQL queries to immediately balance the active draft reconciliation in phpMyAdmin.
        - Redesigned `bank_reconciliation/report.php` into clean, print/PDF-optimized audit statement using local SF Pro typography and zebra striping without external CDN dependencies.
        - Delivered standalone copy-pasteable production SQL composite indexes for live server execution.
- [ ] **Module 9: HRM & Payroll**
- [x] **Module 10: Reports Engine (Central Reports Hub & Report Viewer Engine)**
    - Restored 100% of all 38 authentic Falcon Stationery ERP reports across the 9 system categories (`Inventory Reports`, `Sales Reports`, `Procurement Reports`, `Customer Reports`, `Supplier Reports`, `Finance & Accounts Reports`, `Collection & Payment Reports`, `Route & Distribution Reports`, `Management Reports`), with exact SQL queries, column definitions, and filters preserved. Removed extraneous/hallucinated cashier reports.
    - Preserved the redesigned modern UI layout from the reference design: 100vh viewport-locked screen with zero page scroll, dedicated top-right filter toolbar, column customization, multi-format export dropdown, alternating zebra striping, monospace amounts, and pinned summary footer.

## 12. Final Deliverables

Before implementation, produce a practical audit report containing:

- Actual issues found.
- Severity: Critical / High / Medium / Low.
- Affected modules and files.
- Financial or operational impact.
- Recommended fix.

Then fix issues systematically, prioritizing accounting integrity, data safety and critical business workflows.

After implementation, provide:

1. Issues found and fixed.
2. Issues still unresolved.
3. Files and database tables changed.
4. Database migrations performed.
5. Accounting verification results.
6. Test cases passed and failed.
7. Performance improvements.
8. Security findings.
9. Remaining production risks.

**Final acceptance criteria:** Trial Balance balances, double-entry accounting is correct, subsidiary ledgers reconcile with control accounts, stock movements are consistent, no duplicate financial postings exist, pagination works with large datasets, and all critical business workflows pass automated regression tests.

Do not claim the system is production-ready unless the tests have actually been executed and the results support that conclusion.