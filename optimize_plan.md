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

### 11.2 Module Optimization Progress

- [x] **Module 1: Inventory Catalog (`InventoryController`, `Item`, `Views/inventory/index.php`)**
  - Applied 100vh viewport locking (`.erp-viewport-view`). Page body does not scroll.
  - Implemented `.erp-table-scroll` with sticky `thead` where only product table rows scroll.
  - Filter bar moved directly above the table with all filters (Category, Stock Status, Status, Min Rs, Max Rs, Reset) aligned to the top-right corner.
  - Relocated actions (`+ New Product`, `Import`, `Export`, `Reservations`, `Trash`) to clean top ERP action toolbar, eliminating the floating command pill.
  - Replaced oversized consumer pastel cards with compact, high-density KPI status badges.
  - Fixed N+1 query bottleneck by introducing batch variation preloading `Item::getVariationsForItems()`.
  - Added project rules in `AGENTS.md` and `.agents/rules/erp-ui-standards.md`.
- [ ] **Module 2: Sales & Billing**
- [ ] **Module 3: Purchases & GRN**
- [ ] **Module 4: Rep Tracking & Route Audits**
- [ ] **Module 5: Double-Entry Accounting & Banking**
- [ ] **Module 6: HRM & Payroll**
- [ ] **Module 7: Reports Engine**

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