# Rep App → ERP Rep Tracking workflow audit

**Scope.** Static code review of the mobile Rep App sync/billing path and the ERP Rep Dashboard, Rep Tracking, invoice, delivery and collection code. This report does not claim that every issue has occurred in production; findings labelled **confirmed** are directly evidenced in the current source. Database data, database constraints and production logs still need a separate reconciliation audit.

## Executive summary

The intended workflow is sensible: a rep creates an invoice offline, the app pushes it to ERP, ERP posts the sales, AR, stock and COGS journals, and the tracking screen controls loading, returns, collections and final route finalisation. Collections are initially held in `pending_collections`, then Accounts verifies/finalises them into `customer_payments` and a GL journal.

The most serious risks are:

1. The mobile sync endpoint trusts a caller-supplied user ID and does not authenticate the bearer token.
2. A product that cannot be mapped from the app is silently replaced by the first item in ERP.
3. Route matching can fall back to *any active route* for the rep, permitting bills or collections to be attached to the wrong route.
4. The sync is only transactional per individual entity. A response can report success even when a related customer, route, invoice or payment has failed.
5. The invoice AR account selection can select a Cash-in-Hand account instead of Accounts Receivable.

These should be fixed before adding more Rep Tracking features.

## Workflow traced

```text
Rep App billing
  ├─ local SQLite: invoice + invoice_items, payments, daily_routes, UUIDs/statuses
  └─ SyncManager POST /rep/RepDashboard/sync_push
       ├─ customers → customers
       ├─ routes → rep_daily_routes
       ├─ invoices → Invoice::create/updateInvoiceWithAccounting
       │    ├─ journal_entries / transactions: Dr AR; Dr discount; Cr revenue/tax
       │    ├─ invoice_items, stock/FIFO/stock ledger, COGS/inventory
       │    └─ invoices.rep_route_id
       └─ payments → pending_collections (no GL posting at this stage)

Rep Tracking
  ├─ verifies collection / route accounting / returns / delivery loading
  ├─ finalises pending collections → customer_payments + allocations + GL
  ├─ finalises delivery → route status, stock/returns and invoice delivery handling
  └─ reports route sales, collections, discrepancies and printed documents
```

## Confirmed defects and control gaps

| Priority | Finding | Impact | Evidence / required correction |
|---|---|---|---|
| Critical | **Sync authentication can be impersonated.** `sync_push()` accepts `X-User-ID` or `payload.user_id`, checks only that this user exists and is active, and never validates `Authorization: Bearer ...`. | An attacker or modified app can create invoices, collections and routes as another active rep. This is a financial-integrity issue, not merely an app-security issue. | `ERP/app/Controllers/RepDashboardController.php` `sync_push()` around lines 709–770; `ERP/core/App.php` treats mobile requests as stateless. Require and validate a short-lived signed token on every API request; derive user ID/role from it only; revoke on logout; log device/token identity. |
| Critical | **Unknown products are silently substituted.** During invoice sync, missing `product_id` first tries an exact product name, then assigns the first ERP item (or ID 1). | A valid invoice can post the wrong SKU, quantity, stock movement, COGS, revenue reporting and route loading requirement. The rep and accounts team may not notice. | `RepDashboardController.php`, invoice item mapping in `sync_push()` around lines 1260–1290 and again on update. Reject the invoice item, return a structured mapping error, and keep it pending. Map using immutable ERP SKU/variation IDs and validate variation ownership. Never use a “first product” fallback. |
| Critical | **Wrong-route fallback.** If the server route ID, UUID and current-payload mapping fail, the API uses date range, then the most recent `Active` route for that rep. | A late/offline invoice or collection can be posted to another day/route. It then changes route sales, cash reconciliation, loading, commission and finalisation controls. | `RepDashboardController.php` `$resolveRouteId` around lines 1110–1165. Remove the active-route fallback. Route UUID must be mandatory for route-bound records; reject unmatched records into an exception queue. Date matching may be a suggestion for an operator, never an automatic posting rule. |
| Critical | **AR mapping can select Cash in Hand.** The sales sync query accepts `account_code LIKE COA_CODE_CASH_HAND . '%'` (`1100%`) as one of the candidates for the AR debit account, while the configured AR code is `1200`. `LIMIT 1` has no deterministic order. | Some mobile invoices may debit cash rather than receivables, materially misstating AR and cash. | `RepDashboardController.php` around lines 1176–1184; constants in `ERP/config/database.php:77–79`. Resolve only a single configured AR account (`1200`) through an accounting configuration service and fail closed if absent. |
| High | **Mobile sync is not atomic as a business transaction.** Each customer, route, invoice and payment is committed separately; failures are appended to `mappings.errors`, but the endpoint returns `success: true`. | Partial route uploads leave bills without the expected customer/route, or a route without all collections. The app can mark mapped records synced while staff see an incomplete route. | `RepDashboardController.php` `sync_push()` processes and commits entities one by one. Add a sync batch/receipt table with an idempotency key. Either atomically accept the dependency graph, or return `partial_success` and prevent route closure until all dependency errors are resolved. |
| High | **Invoice identity repair changes document numbers.** Cross-user invoice/UUID collisions are “solved” by appending `U{user}` or random `-###` values. A retry changes the document reference rather than resolving the collision deterministically. | Printed/mobile invoice number, stock ledger reference, journal reference and ERP record may diverge. Random suffixes also make reconciliation harder. | `RepDashboardController.php` invoice collision handling around lines 1215–1260 and retry logic near 1490. Use server-issued invoice sequences and immutable UUID/idempotency keys. Preserve the client invoice reference as a separate `mobile_document_number`; never mutate an already-issued legal/business number. |
| High | **Duplicate collection prevention is application-only.** Payments are looked up by UUID or `(mobile_local_id, mobile_rep_id)`, but migrations add these fields without visible unique constraints. | Concurrent/replayed requests can insert duplicate `pending_collections`, leading to duplicate cash, cheques and finalised GL entries. | `MigrationManager.php:515–517` adds columns only; `sync_push()` checks then inserts. Add unique DB indexes: `(mobile_rep_id, mobile_local_id)` where non-null and a unique UUID constraint. Use `INSERT ... ON DUPLICATE KEY`/transactional upsert and return the existing mapping. |
| High | **Finalisation silently skips an unmapped payment.** `finalizePayments()` uses `continue` when an asset account cannot be mapped; it later commits and returns `true`, leaving the collection pending but not flagged as a failure. | A route can look processed while collection money has no GL/customer-payment posting. | `ERP/app/Models/RepTracking.php` around lines 470–475. Treat missing/invalid debit or credit account as a hard validation error for the selected payment and report it to the UI. |
| High | **Credit-bill payment can overstate the payment allocation control.** It caps allocation at balance due (`$allocAmount`) but posts the full `$amount` to asset/AR and stores a finalised pending collection. This is valid only if the surplus is deliberately customer credit; the UI does not require explicit confirmation of that outcome. | A typo can create a large unallocated customer credit and inflate route cash; it is easy to interpret as a double collection. | `RepTrackingController.php` `api_apply_credit_bill_payment()` around lines 3240–3355. Require an explicit “keep excess as customer advance” choice, show amount/allocated/excess before post, and separate the advance receipt from the invoice allocation in reports. |
| Medium | **Collection history displays a fabricated journal ID.** The route-collection query returns `999999` for every finalised collection rather than the actual `journal_entry_id`. | Staff cannot drill down, reconcile or audit a collection to its real GL journal. It can conceal posting discrepancies. | `ERP/app/Models/RepTracking.php` around line 403. Store `journal_entry_id` on `pending_collections` during finalisation and select it; remove the sentinel value. |
| Medium | **Invoice edits remain allowed after stock has been deducted.** Existing synced invoices are editable when `stock_status` is `reserved` **or** `deducted`. | A stale retry or altered device record can rewrite sales/stock after a field invoice has already affected inventory; controls need a stricter business state than stock status alone. | `RepDashboardController.php` invoice update condition around line 1275. Allow amendments only in an explicit Draft/Unapproved state; after posting, create a controlled credit note/reversal + replacement invoice. |
| Medium | **Dynamic COA creation by operational flows.** Invoice and expense code creates default COA accounts (`4050`, `5000`, `1300`, `2110`, `1090`) when it cannot find one; route services also use name/LIKE matching. | Duplicate or misclassified accounts may be created in a live chart, reporting splits across accounts, and an incorrect account could be selected through a fuzzy match. | `ERP/app/Models/Invoice.php` lines ~70–150; `RouteExpenseService.php` lines ~131–160 and ~449 onward. Only finance-admin setup may create accounts. Use stable account IDs/configuration, active flags, fiscal-period controls and a migration/setup checklist. |
| Medium | **Route sales formulas are repeated in several queries.** The UI/report totals recompute `total_amount - global discount + tax`, while accounting is based on journal lines and invoice item discounts are handled separately. | Minor formula divergence, rounding and future tax/discount changes can cause route total, invoice total and GL revenue to differ. | `ERP/app/Models/RepTracking.php` `getAllRoutes()`, `getRouteBills()` and `getRouteById()`. Store a canonical invoice `grand_total`/net amount and reconcile route totals from that field; add a per-route GL reconciliation report. |
| Medium | **Route deletion directly deletes posted journals and transactions in one mode.** | Deleting accounting history is weaker than a controlled reversal/void trail and risks changes in closed periods. | `ERP/app/Services/RepRouteService.php` around lines 208–234. Forbid hard delete after posting; create reversal entries and mark business documents voided with reason/approval. |
| Medium | **Detailed API errors/traces are returned to the app.** | Production internals and SQL failures may leak to a device/user. | `RepDashboardController.php` pull error handling near the start of `sync_push` region returns `trace`; logs are also written under project root. Return a correlation ID and generic error; retain stack traces server-side only. |

## Accounting model: what should change

### Invoice from the Rep App

Target posting should be a balanced, immutable posting after approval/delivery:

| Debit | Credit |
|---|---|
| Accounts Receivable — customer subledger: invoice net total | Sales revenue: gross sales before discounts |
| Discounts allowed / contra revenue: approved discounts | Output VAT: tax charged |
| Cost of sales: FIFO/standard cost | Inventory asset: same cost |

Controls to add: one configured account per role (AR, revenue, discount, VAT, inventory, COGS); a journal-balance check; total = header = sum of lines = GL AR movement; no automatic account creation; and a reversal/credit-note process instead of edits after posting.

### Rep collection

Target posting after Accounts verifies cash/cheque/bank evidence:

| Debit | Credit |
|---|---|
| Cash in hand / undeposited funds / cheques received / selected bank | Accounts Receivable — same customer |

`pending_collections` should be a non-GL operational staging record. On finalisation it must store the generated `customer_payment_id`, `journal_entry_id`, verifier, verified amount and variance reason. Its status transition should be append-only and idempotent.

For cash held by a rep, consider using **Rep Cash Clearing / Undeposited Funds**, not the main cash account, until a deposit handover is approved. This makes cash custody and shortages visible.

## Double-entry and transaction controls to add

1. Add database constraints: unique mobile event UUIDs; unique `(mobile_rep_id, mobile_local_id)`; foreign keys for route/customer/invoice/payment relations; non-negative amounts; valid status enums.
2. Add a `mobile_sync_batches` table (batch UUID, device ID, authenticated rep, received/processed timestamps, overall state, hash) and an immutable `sync_events` audit trail.
3. Use a server-side idempotency key for every invoice, payment, customer and route. Repeated payloads must return the same IDs without changing content.
4. On every finalisation, assert `SUM(debit) = SUM(credit)`, journal is posted once, and customer-payment allocation never exceeds both payment and invoice balance.
5. Generate a daily exception report: unlinked invoices/payments, unmatched routes, pending collections older than one day, duplicate UUID/local IDs, invoice header vs item vs GL differences, and negative stock/FIFO failures.
6. Lock invoice/customer allocation rows during payment application; add a unique or guarded allocation rule to prevent concurrent overpayment.

## Rep Tracking workflow improvements

1. Replace auto-fallback behaviour with an **Exceptions** inbox: “unmapped product”, “unknown customer”, “route not found”, “duplicate event”, “COA not configured”, and “stock conflict”. An authorised user resolves and replays a specific event.
2. Give each route a reconciliation panel: opening load, invoiced quantities, free issues, returns/damage, closing load, expected cash/cheques/bank, verified amount, expenses and variance. Each total should link to underlying documents.
3. Show collection lifecycle distinctly: *received by rep → evidence verified → GL posted → deposited/cleared*. Do not label an unverified mobile collection as paid in customer ageing.
4. Disallow final route closure unless all mobile sync events are acknowledged, all collections are resolved, stock return is verified, and the reconciliation variance is approved with a reason.
5. Record server receipt date/time separately from invoice/payment business date; prevent backdating into a closed period without finance approval.
6. Use stable ERP IDs/SKUs in the app payload and version the sync contract. Validate payload schema and reject unknown/extra critical fields.

## Recommended remediation order

1. **Immediate:** bearer-token authentication, remove product and active-route fallbacks, correct AR lookup, and stop exposing traces.
2. **Next release:** unique idempotency constraints, sync-batch/exception handling, exact product/variation validation, and finalisation hard failures for unmapped accounts.
3. **Finance hardening:** configure COA roles centrally, persist journal/payment links, use undeposited-funds clearing, restrict edits/deletions, and add daily reconciliation queries.
4. **Assurance:** automated tests for replayed sync requests, concurrent payment finalisation, wrong route/product rejection, invoice/GL balance, route closure gates, and reversal/period-close permissions.

## Validation checklist before release

- Attempt sync with a valid-looking `X-User-ID` but no/invalid bearer token: it must be rejected.
- Submit the exact same invoice/payment event twice and concurrently: exactly one ERP document and one GL journal must result.
- Sync a missing product/variation: it must remain in Exceptions; no substitute stock item may move.
- Sync an invoice/payment with a missing route UUID: it must not attach to an active route.
- Compare a completed route’s invoice net totals, AR movement, revenue/discount/tax journals, stock ledger/COGS, collections, allocations and cash-clearing balance.
- Try to finalise a collection with no configured debit/credit account: the operation must fail visibly and leave no partial journal.
- Try to edit/delete a posted/finalised invoice/route: the system must require a controlled reversal and audit approval.
