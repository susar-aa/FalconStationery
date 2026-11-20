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
- [x] **Module 2: Sales & Billing (`SalesController`, `Invoice`, `Views/sales/index.php`)**
  - **Audit Findings**:
    - Discovered an extreme N+1 query loop in `create()` where variations were not preloaded in the controller, causing `sales/index.php` to query variations in a loop for all 1,050+ catalog items on every single invoice page load (>1,050 individual SQL queries).
    - Customer outstanding balance calculations ran 3 correlated subqueries (`invoices`, `customer_payments`, `credit_notes`) across all active customers without composite index optimization.
    - Missing CSRF token in `<form id="invoiceForm">` and missing CSRF validation in `SalesController::store()`.
    - No double-submission protection on submit buttons (`Save & Close`, `Save & New`, `Save & Print`, `Save & WhatsApp`), creating risks of duplicate invoices and double ledger postings.
  - **Fixes & Optimizations**:
    - Batch preloaded variations in `SalesController::create()` in 1 single database query using `Item::getVariationsForItems()`, and guarded view against redundant per-item variation queries.
    - Added CSRF token protection to the main invoice form and enforced `$this->validateCsrfOrDie()` in `SalesController::store()`.
    - Added RBAC permission enforcement (`$this->checkPermission('sales', 'create_edit')`).
    - Added double-submission lock (`isInvoiceSubmitting`) and active button disabling to prevent concurrent duplicate invoice creation.
    - Preserved 100% of the existing invoice creation UI layout, styles, and workflows per user instructions ("do not redesign it").
    - Provided production composite indexing SQL for `invoices`, `invoice_items`, `sales_orders`, and `customer_payments`.
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
- [ ] **Module 5: Rep Tracking & Route Audits**
- [ ] **Module 6: Double-Entry Accounting & Banking**
- [ ] **Module 7: HRM & Payroll**
- [ ] **Module 8: Reports Engine**

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