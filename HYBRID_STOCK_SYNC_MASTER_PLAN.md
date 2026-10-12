# Curtiss ERP & Mobile Rep App — Real-Time Stock & Hybrid Sync Master Plan

**Document Version:** 1.0  
**Target Systems:** Curtiss ERP Web Backend (PHP/MySQL) & Curtiss Sales Rep App (Android Java/SQLite)  
**Status:** Architecture Blueprint (Pending Review — Not Implemented)

---

## Table of Contents
1. [Executive Summary & Core Architecture](#1-executive-summary--core-architecture)
2. [End-to-End Business Lifecycle](#2-end-to-end-business-lifecycle)
3. [Loading State & UX Design Specifications](#3-loading-state--ux-design-specifications)
4. [Mobile App Architecture & Checkpoints](#4-mobile-app-architecture--checkpoints)
5. [ERP Web Backend Architecture](#5-erp-web-backend-architecture)
6. [Accounting & Route Finalization Mechanics](#6-accounting--route-finalization-mechanics)
7. [Conflict Handling & Stock Deficit Workflows](#7-conflict-handling--stock-deficit-workflows)
8. [Phased Stage-by-Stage Implementation Plan](#8-phased-stage-by-stage-implementation-plan)

---

## 1. Executive Summary & Core Architecture

### Core Problem Solved
Currently, inventory is only deducted when a route is finalized inside the Rep Tracking system. Because delivery takes place 2 to 3 days after a bill is created, items remain visible on the shelf and are frequently double-sold to other field reps, showroom buyers, or web customers.

### The Solution: Direct Real-Time Deduction + Hybrid Reconnect Engine
1. **Direct Deduction at Billing/Sync:** Stock is deducted directly from warehouse inventory (`quantity_on_hand`) the second a bill is created or synced. This immediately blocks goods from being sold by anyone else during the 3-day gap.
2. **Simplified Inventory Model:** Eliminates separate virtual stock reservation tables. Only one true number exists: physical warehouse inventory.
3. **Route Finalize Shift:** Route Finalize in Rep Tracking changes from a *Stock Deduction Gate* to a *Stock Return & Payment Settlement Gate*. Only goods refused/returned by customers at the doorstep are added back ($+\text{Qty}$) to inventory.
4. **Clean Accounting (Zero Reversals):** Bills are saved in `Draft / Pending Delivery` financial status on Day 1. On Day 3 (Route Finalize), one clean General Ledger entry is posted for the exact delivered quantities. No messy reversals or adjusting journal entries.
5. **Instant Network Auto-Flush:** The Android app listens continuously for network restoration and pushes queued bills within 1–3 seconds of regaining 4G/Wi-Fi without user action.
6. **Pre-Billing Stock Checkpoints:** Lightweight micro-pings (<150ms) refresh stock at key stages in the mobile billing flow to prevent overselling before bills are saved.

---

## 2. End-to-End Business Lifecycle

```
========================================================================================
DAY 1: SHOP ORDER CREATION
========================================================================================
  [Rep enters shop]
        │
        ├─► Opens "Create Invoice" ──► Fast background Micro-Ping updates stock.
        ├─► Adds items to Cart     ──► Live stock badge validated on tile.
        └─► Taps "Save / Checkout" ──► Instant Cart Check (<0.1s).
                                         │
                                         ├─► Online: Saves & pushes immediately.
                                         └─► Offline: Saves locally; queues for auto-flush.
        │
        ▼
  [Server Real-Time Stock Deduction]
        ├── items.quantity_on_hand -= Billed Qty
        ├── item_variation_options.quantity_on_hand -= Billed Qty
        └── Invoice created with status = 'Draft / Pending Delivery'
        (Stock is now completely blocked from other reps / showroom for the next 3 days)

========================================================================================
DAY 2: 3-DAY BUFFER PERIOD
========================================================================================
  [Warehouse & Other Reps]
        └── View catalog: Stock is already removed. Zero chance of overselling.

========================================================================================
DAY 3: PHYSICAL DELIVERY & ROUTE FINALIZATION
========================================================================================
  [Delivery Driver arrives at customer]
        │
        ├─► Customer accepts 8 units, rejects 2 units (Doorstep return).
        │
        ▼
  [Route Finalization in Rep Tracking (Web ERP)]
        ├── Delivered Items (8 Units):
        │     └── NO stock deduction needed (already deducted on Day 1).
        │
        ├── Rejected / Returned Items (2 Units):
        │     └── ADDED back to inventory (+2 quantity_on_hand).
        │
        └── Financial Ledger Posting:
              ├── DEBIT: Accounts Receivable (for 8 delivered units)
              ├── CREDIT: Sales Revenue (for 8 delivered units)
              ├── DEBIT: Cost of Goods Sold (for 8 delivered units)
              ├── CREDIT: Inventory Asset (for 8 delivered units)
              └── Settle Driver Collections (Cash/Cheque -> AR).
========================================================================================
```

---

## 3. Loading State & UX Design Specifications

To ensure the app feels fast, responsive, and clear while performing background stock checks and sync flushes, three distinct loading states will be introduced:

### 3.1 Non-Blocking Catalog Stock Refresh (Subtle Shimmer / Header Pill)
* **Where:** Top of `BillingActivity` product catalog.
* **Behavior:** When the screen opens or the rep pulls down to refresh:
  * A small pill indicator appears at the top center:
    `[ 🔄 Checking Live Warehouse Stock... ]`
  * Product grid remains clickable and interactive.
  * When complete (under 200ms), pill transforms smoothly to:
    `[ 🟢 Live Stock Connected ]` or `[ 🟠 Offline (Local Stock) ]`

### 3.2 Pre-Checkout Cart Verification HUD (Modal Overlay)
* **Where:** Triggered when the rep taps **"Complete Bill / Checkout"**.
* **Design:** A sleek, lightweight Material HUD modal dialog:
  ```
  ┌──────────────────────────────────────────────────────────┐
  │                 Curtiss Fast Verification                │
  │                                                          │
  │                [ ⏳ Circular Animated Ring ]              │
  │                                                          │
  │            Verifying Warehouse Stock Availability...     │
  │                 (Securing 4 items in cart)               │
  │                                                          │
  │  [ Cancel ]                                              │
  └──────────────────────────────────────────────────────────┘
  ```
* **Performance Rule:**
  * Maximum timeout: **1.5 seconds**.
  * If verified: Displays checkmark `[ ✔️ Stock Confirmed ]` and transitions directly to invoice print/success.
  * If network drops or times out: Silently bypasses and completes offline checkout with a Toast message: *"Network timeout. Saved to offline queue."*

### 3.3 Background Auto-Flush Toast Notification
* **Where:** Global Android notification or floating snackbar on `MainActivity`.
* **Behavior:** When network recovers while the rep is driving or on home screen:
  * Floating pill appears at bottom: `[ 🚀 2 Offline Bills Synced to ERP Server ]`

---

## 4. Mobile App Architecture & Checkpoints

### 4.1 The 4 Strategic Stock Refresh Checkpoints

| Checkpoint | Trigger Location | Target Payload | Timeout | Fallback |
|---|---|---|:---:|---|
| **1. Screen Load** | Tapping "Create Invoice" | Compressed Stock Delta (~15KB) | 1.5s | Silent fallback to SQLite |
| **2. Catalog Pull** | Pull-to-Refresh gesture | Entire category stock levels | 2.0s | Pull-to-refresh spinner hides |
| **3. Variation Tile** | Opening product detail sheet | Single Item ID (<500 bytes) | 1.0s | Uses local SQLite quantity |
| **4. Final Checkout** | Tapping "Complete Bill" | Cart items only (<1KB) | 1.5s | Proceeds via offline mode |

### 4.2 Network Reconnect Listener (`NetworkMonitor.java`)
* Implements `ConnectivityManager.NetworkCallback` on Android 7.0+ (API 24+).
* Registers `onAvailable()` callback.
* Triggers `SyncManager.triggerImmediateAutoFlush()` via a non-blocking background thread.
* Does not require rep interaction or manual sync buttons.

---

## 5. ERP Web Backend Architecture

### 5.1 New Lightweight Endpoints in `RepDashboardController.php`

1. **`live_stock_delta` Endpoint:**
   * **URL:** `GET /RepDashboard/live_stock_delta?since=<timestamp>`
   * **Purpose:** Returns only `item_id`, `variation_option_id`, and `quantity_on_hand` for items modified since last sync.
   * **Payload Size:** ~5 KB to 20 KB.

2. **`quick_stock_check` Endpoint:**
   * **URL:** `POST /RepDashboard/quick_stock_check`
   * **Payload:** `{"items": [{"id": 12, "var_id": 105, "qty": 10}, ...]}`
   * **Response:**
     ```json
     {
       "success": true,
       "all_available": true,
       "items": [
         {"id": 12, "var_id": 105, "available": 45, "status": "ok"}
       ]
     }
     ```

### 5.2 Direct Deduction on Sync Push
* In `RepDashboardController.php` (`sync_push`):
  * When receiving a new bill from mobile, execute physical stock deduction directly:
    * `items.quantity_on_hand -= item.quantity`
    * `item_variation_options.quantity_on_hand -= item.quantity`
  * Log audit entry in `stock_movements` / `StockLedger`:
    * Type: `Mobile Bill Direct Deduction`
    * Reference: `INV-XXXXXXXX`
  * Set invoice status to `'Pending Delivery'`, `stock_status = 'deducted'`.

---

## 6. Accounting & Route Finalization Mechanics

### 6.1 Clean Accounting Posting (No Reversals)
* **At Day 1 (Billing):**
  * The invoice's linked Journal Entry is created in status **`Draft`** (or delayed until delivery).
  * No entry is posted to General Ledger accounts (AR, Revenue, COGS, Inventory Asset are untouched).
* **At Day 3 (Route Finalize in `Delivery.php`):**
  * Promotes Journal Entry to **`Posted`** using the **exact delivered quantities**:
    * **DEBIT:** Accounts Receivable (`1200`) = Delivered Net Total
    * **CREDIT:** Sales Revenue (`4000`) = Delivered Gross Total
    * **DEBIT:** Cost of Goods Sold (`5000`) = FIFO Cost of Delivered Units
    * **CREDIT:** Inventory Asset (`1300`) = FIFO Cost of Delivered Units
  * Customer ledger reflects only what was physically left at their shop.

### 6.2 Doorstep Return Handling in `Delivery.php`
* In `finalizeDelivery()`:
  * Remove any secondary deduction of delivered invoices.
  * For any rejected/returned items entered in the Return Stock modal:
    * Run `updateStockDelta($itemId, +$returnedQty)`.
    * Update `item_variation_options SET quantity_on_hand = quantity_on_hand + :qty`.
    * Log movement in `StockLedger`: `Delivery Doorstep Return Restock`.

---

## 7. Conflict Handling & Stock Deficit Workflows

### 7.1 Offline Auto-Sync Deficit Scenario
* **Problem:** Rep was offline and billed 10 units. Meanwhile, warehouse stock was depleted to 4 units. Phone reconnects and auto-syncs.

### 7.2 The Resolution Workflow
1. **Server Processing:**
   * Accepts the invoice with status: `Stock Deficit - Needs Review`.
   * Flags conflict JSON in sync response: `{"conflict": true, "item_name": "Box File", "requested": 10, "available": 4}`.
2. **Mobile App Dashboard Banner (`MainActivity`):**
   * High-priority warning card appears at the top:
     ```
     ┌─────────────────────────────────────────────────────────────┐
     │ ⚠️  1 Invoice Requires Stock Review                        │
     │ Bill #2026-0042 has stock shortages. Tap to resolve now.     │
     └─────────────────────────────────────────────────────────────┘
     ```
3. **One-Tap Deep Link to Edit Mode (`BillingActivity`):**
   * Tapping the banner opens the invoice in edit mode directly.
   * The problematic item is highlighted with a **bold red card border**:
     ```
     ┌─────────────────────────────────────────────────────────────┐
     │ 🔴 Box File (Black)                                         │
     │ ⚠️ Insufficient Stock: Only 4 available (Billed 10)         │
     │ [ - ]  [ 4 ]  [ + ]     [ Swap Product ]    [ Remove ]      │
     └─────────────────────────────────────────────────────────────┘
     ```
4. **Rep Action:**
   * Rep calls customer, adjusts quantity to 4 (or swaps item), and taps **"Save Changes"**.
   * Re-syncs cleanly, and the home screen banner vanishes.

---

## 8. Phased Stage-by-Stage Implementation Plan

To ensure safe, controlled deployment without disrupting live operations, the implementation is divided into **5 sequential stages**:

```
 ┌─────────────┐     ┌─────────────┐     ┌─────────────┐     ┌─────────────┐     ┌─────────────┐
 │   STAGE 1   │ ──► │   STAGE 2   │ ──► │   STAGE 3   │ ──► │   STAGE 4   │ ──► │   STAGE 5   │
 │ Lightweight │     │ Direct Cut  │     │ Mobile App  │     │ Auto-Flush  │     │ Edit Logic  │
 │ Backend API │     │ & Finalize  │     │ Checkpoints │     │ & Conflict  │     │ & Full Test │
 └─────────────┘     └─────────────┘     └─────────────┘     └─────────────┘     └─────────────┘
```

---

### **Stage 1: Backend Lightweight Stock Endpoints (ERP Web)**
* **Objective:** Enable high-speed, micro-payload stock queries.
* **Tasks:**
  1. Add `live_stock_delta` and `quick_stock_check` endpoints in [`RepDashboardController.php`](file:///c:/xampp/htdocs/CURTISS/FalconStationery/ERP/app/Controllers/RepDashboardController.php).
  2. Optimize SQL queries using explicit `INNER JOIN` and covering indexes on `items` and `item_variation_options`.
  3. Validate endpoints with Postman / cURL to ensure execution times are under 150ms.
* **Risk Level:** Zero (read-only endpoints, does not touch existing billing).

---

### **Stage 2: Direct Real-Time Deduction & Route Finalize Overhaul (ERP Web)**
* **Objective:** Remove the 3-day overselling gap on the server.
* **Tasks:**
  1. Update `sync_push` in [`RepDashboardController.php`](file:///c:/xampp/htdocs/CURTISS/FalconStationery/ERP/app/Controllers/RepDashboardController.php) to deduct `quantity_on_hand` immediately on bill creation.
  2. Set invoice initial status to `'Pending Delivery'` and financial status to `'Draft'`.
  3. Overhaul [`Delivery.php`](file:///c:/xampp/htdocs/CURTISS/FalconStationery/ERP/app/Models/Delivery.php) `finalizeDelivery()`:
     * Remove redundant deduction of delivered items.
     * Ensure doorstep returned items run `updateStockDelta(+$returnedQty)` and update `item_variation_options`.
     * Post clean General Ledger entries for delivered quantities only.
* **Risk Level:** Medium (affects route closure; tested on local XAMPP first).

---

### **Stage 3: Mobile App Stock Checkpoints & Loading State HUD (Rep App)**
* **Objective:** Implement pre-billing stock checks and user feedback.
* **Tasks:**
  1. Create `StockCheckService.java` for executing micro-requests against `quick_stock_check`.
  2. Implement the **4 Stock Checkpoints** in [`BillingActivity.java`](file:///c:/xampp/htdocs/CURTISS/FalconStationery/Curtiss%20ERP%20Rep%20App/app/src/main/java/com/example/curtiss/BillingActivity.java):
     * Checkpoint 1: Opening "Create Invoice" (background load).
     * Checkpoint 2: Pull-to-refresh on catalog.
     * Checkpoint 3: Selecting product variation.
     * Checkpoint 4: Pre-checkout cart verification.
  3. Build the sleek **Verification HUD modal** and the **Live/Offline status chip** (`🟢/🟠`).
  4. Implement strict 1.5s timeout with silent SQLite fallback.
* **Risk Level:** Low (UI & local network additions).

---

### **Stage 4: Network Reconnect Auto-Flush & Conflict UI (Rep App + ERP)**
* **Objective:** Zero-delay sync on internet return + visual conflict resolution.
* **Tasks:**
  1. Implement `NetworkMonitor.java` using Android `ConnectivityManager.NetworkCallback`.
  2. Wire `NetworkMonitor` to automatically trigger `SyncManager.triggerImmediateAutoFlush()`.
  3. Update sync response parser to capture stock deficit items.
  4. Build **Home Screen Alert Banner** in [`MainActivity.java`](file:///c:/xampp/htdocs/CURTISS/FalconStationery/Curtiss%20ERP%20Rep%20App/app/src/main/java/com/example/curtiss/MainActivity.java).
  5. Add **Red-Highlighted Item Card** inside `BillingActivity` when editing a conflicted bill.
* **Risk Level:** Medium (background service orchestration).

---

### **Stage 5: Invoice Editing Reconcile Logic & End-to-End Audit**
* **Objective:** Perfect stock precision when invoices are edited from Web or Mobile.
* **Tasks:**
  1. Verify and reinforce **Revert-and-Reapply** logic in [`Invoice.php`](file:///c:/xampp/htdocs/CURTISS/FalconStationery/ERP/app/Models/Invoice.php) (`updateInvoiceWithAccounting`):
     * Put old quantities back $\rightarrow$ validate & deduct new quantities.
     * Guard: Block quantity increases if insufficient warehouse stock.
  2. End-to-end simulation on local XAMPP:
     * Full flow: Offline bill $\rightarrow$ Reconnect auto-flush $\rightarrow$ Edit quantity $\rightarrow$ Day 3 Route Finalize with 2 returned items.
     * Verify database integrity in `items`, `item_variation_options`, `StockLedger`, and General Ledger.
* **Risk Level:** Low (system verification & safety tests).

---

## Conclusion & Next Step
This master plan eliminates the 3-day overselling vulnerability, provides instantaneous auto-sync when field reps regain reception, and guarantees accounting and inventory precision without complex virtual reservation tables.

**Review Check:** Once you review and approve this master plan, we can commence implementation starting with **Stage 1**.
