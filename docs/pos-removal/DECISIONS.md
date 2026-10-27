# BLUE ZONE™ — POS Removal Architectural Decisions

**Document Date:** 2026-10-27  
**Branch:** `chore/remove-pos`  
**Status:** Approved & Recorded

---

## 1. Core Architectural Decisions

| # | Decision | Chosen Option | Rationale & Impact |
|---|---|---|---|
| **D1** | **Disposition of `stock_offline` pool** | **(a) Merge into `central_wh` via ledgered adjustment** | No stock is lost, duplicated, or invented. All units currently sitting in POS warehouse (`LOC-POS`) are consolidated into Central Quarantine Warehouse (`LOC-CWH` / `central_wh`). An explicit `InventoryMovement` of type `'Stock Reconciliation'` is logged per product. |
| **D2** | **Historical `channel='offline'` orders** | **(a) Keep rows, keep channel value, hide from new-order creation** | Zero financial or legal risk. Orders table preserves all 8 historical offline transactions for tax and ZATCA compliance. No customer invoice history is modified. |
| **D3** | **`INV-POS-*` and `POS-*` invoices** | **Keep printable via shared route `/admin/invoices/{id}/print`** | Legal requirement. Retains ZATCA QR codes and VAT breakdowns. Only thermal modal and POS-specific receipt templates are removed. |
| **D4** | **Database column `products.stock_offline`** | **Deferred Drop (Phase-out now, drop in future migration)** | The application immediately ceases reading, displaying, or updating `stock_offline`. The column is set to 0. Dropping the column is deferred to a future maintenance cycle to eliminate migration lock risks on active databases. |
| **D5** | **Location `LOC-POS` (`Warehouse / POS`)** | **Soft-disable (`is_active = 0`), retain row** | Foreign key constraints on historical `inventory_items` and `inventory_movements` remain intact. Location is hidden from active inventory transfer and dropdown UI selectors. |
| **D6** | **Role & Permissions (POS)** | **Reassign any POS-dedicated users to "Sales Staff", purge permissions** | Any users assigned to POS retail roles are safely reassigned to "Sales Staff" (Role ID: 3). Permissions `manage_offline_sales` and `offline_sales.*` are removed from the system. |
| **D7** | **Historical `inventory_movements` of type `'Offline Sale'`** | **Keep untouched** | Absolute ledger immutability. Historical stock movements represent real past physical events and remain forever accessible in inventory audit trails. |

---

## 2. Operational Questions (§11)

1. **Does finance/legal require keeping POS-era invoices printable?**  
   **Yes.** Tax invoices generated during POS counter operation (`INV-POS-*`) remain accessible and printable through the standard administrative invoice view.

2. **Should old offline stock go to central or online?**  
   **Central (`central_wh`).** Central warehouse serves as the master reserve buffer for replenishments and quality control.

3. **Which existing role should former POS cashiers be moved to?**  
   **Sales Staff (Role ID: 3).** This role retains permission to view orders and customer details without POS direct deduction capabilities.

4. **Showroom operational procedure going forward:**  
   In-store physical showroom orders, if placed, will be processed via standard administrative/online order creation (`admin.orders.create`), fulfilling directly from centralized/online inventory with standard logistics dispatch.
