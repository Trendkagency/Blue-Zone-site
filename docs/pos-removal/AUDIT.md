# BLUE ZONE™ — POS & Offline Sales Removal Audit Inventory

**Audit Date:** 2026-10-27  
**Branch:** `chore/remove-pos`  
**Application:** BlueZone Cellular Longevity & Wellness Platform (Laravel 11/12)

---

## 1. Audit Matrix

| File / Component / Record | Type | Action | Risk | Owner Phase | Description / Justification |
|---|---|---|---|---|---|
| `app/Http/Controllers/Admin/OfflineSaleController.php` | Code | **DELETE** | High | Phase 5.3 | Cashier checkout controller, store, print redirect, hold cart handlers. Solely POS. |
| `resources/views/admin/offline-sales/index.blade.php` | View | **DELETE** | Medium | Phase 5.4 | Historical/listing view for offline sales counter. Replaced by general orders listing. |
| `resources/views/admin/offline-sales/create.blade.php` | View | **DELETE** | High | Phase 5.4 | Full cashier terminal UI, barcode listener, Web Audio synthesizers, thermal modal. |
| `resources/views/admin/offline-sales/show.blade.php` | View | **DELETE** | Medium | Phase 5.4 | Offline sale detail view. Historical orders readable in `admin/orders/{id}`. |
| `routes/admin.php` (lines ~243-248) | Route | **MODIFY** | High | Phase 5.1 | Remove `offline-sales` route group and `admin.offline-sales.*` named routes. |
| `resources/views/components/layouts/admin.blade.php` | View | **MODIFY** | Medium | Phase 5.2 | Remove Offline Sales sidebar link, sublinks, and `offline_sales.*` permission gates. |
| `resources/views/admin/dashboard/index.blade.php` | View | **MODIFY** | Low | Phase 5.2 | Remove "New Offline Sale" quick action button and POS offline revenue widget. |
| `resources/views/admin/sales/orders.blade.php` | View | **MODIFY** | Low | Phase 5.2 | Remove "New POS Sale" action button. Historical offline orders remain visible. |
| `resources/views/admin/sales/products-services.blade.php` | View | **MODIFY** | Low | Phase 5.2 | Remove "POS Checkout" button per product row. |
| `resources/views/admin/reports/index.blade.php` | View | **MODIFY** | Low | Phase 5.2 | Remove POS navigation shortcuts and offline sales stream buttons. |
| `app/Services/InventoryService.php` | Code | **MODIFY** | High | Phase 5.5 | Remove `processOfflineSale()`, remove `offline` location branch from stock syncing. Keep generic ledger movement methods. |
| `resources/views/admin/inventory/allocator.blade.php` | View | **MODIFY** | High | Phase 5.6 | Shift from 3-pool allocator (Online, POS, Central) to 2-pool (Online & Central). Update JS sliders, progress bars, and ratio split. |
| `app/Http/Controllers/Admin/InventoryController.php` | Code | **MODIFY** | High | Phase 5.6 | Remove `stock_offline` logic, update Allocator batch split and transfer endpoints to 2-pool model. Keep 'Offline Sale' label in history for immutable past ledger. |
| `app/Models/Product.php` | Code | **MODIFY** | Medium | Phase 5.7 | Remove `stock_offline` from `$fillable`. Update total stock accessor to `stock_online + stock_central`. |
| `app/Http/Requests/Admin/StoreProductRequest.php` | Code | **MODIFY** | Low | Phase 5.7 | Remove `stock_offline` validation rules and attribute labels. |
| `app/Http/Requests/Admin/UpdateProductRequest.php` | Code | **MODIFY** | Low | Phase 5.7 | Remove `stock_offline` validation rules and attribute labels. |
| `resources/views/admin/products/{create,edit,index}.blade.php` | View | **MODIFY** | Low | Phase 5.7 | Remove offline stock inputs and table columns. |
| `app/Models/Order.php` | Code | **KEEP / MODIFY** | High | Phase 5.8 | Keep `channel = 'offline'` support so historical ZATCA tax invoices render. Remove any POS order creation triggers. |
| `app/View/ViewModels/OrderViewModel.php` | Code | **MODIFY** | Low | Phase 5.8 | Remove `offlineSales()` helper. Existing offline orders stay in database. |
| `app/Http/Controllers/Admin/InvoiceController.php` | Code | **MODIFY** | Medium | Phase 5.9 | Keep shared invoice routes (`/admin/invoices/{id}/print`) for tax compliance. Remove thermal modal logic. |
| `app/Http/Controllers/Admin/ReportController.php` | Code | **MODIFY** | Medium | Phase 5.10 | Remove `offline_sales` revenue stream and cashier metrics. Re-route reports to online ecommerce. |
| `app/View/ViewModels/ReportViewModel.php` | Code | **MODIFY** | Low | Phase 5.10 | Remove `offline_sales` mock KPIs. |
| `app/Http/Controllers/Admin/SupplyChainReportController.php` | Code | **MODIFY** | Low | Phase 5.10 | Remove POS distribution metrics. |
| `app/Providers/AppServiceProvider.php` | Code | **MODIFY** | Low | Phase 5.11 | Remove `manage_offline_sales` gate definition. |
| `app/Http/Controllers/Admin/RoleController.php` | Code | **MODIFY** | Low | Phase 5.11 | Remove `offline_sales` permission group from matrix. |
| `app/View/ViewModels/RoleViewModel.php` | Code | **MODIFY** | Low | Phase 5.11 | Remove `offline_sales` permission group definitions. |
| `app/Models/User.php` | Code | **MODIFY** | Low | Phase 5.11 | Remove `'pos' => 'offline_sales'` mapping from `hasPermission()`. |
| `lang/en/admin.php` | i18n | **MODIFY** | Low | Phase 5.13 | Remove POS-specific keys (`offline_sales`, `open_pos`, `pos.*`). |
| `lang/ar/admin.php` | i18n | **MODIFY** | Low | Phase 5.13 | Remove Arabic POS keys matching English removals (maintain 100% EN/AR parity). |
| `database/seeders/NotificationSeeder.php` | Data | **MODIFY** | Low | Phase 5.14 | Remove POS offline sale notification seeding. |
| `tests/Feature/InventoryAndOfflineSalesTest.php` | Test | **MODIFY** | Medium | Phase 5.15 | Remove offline sale POS tests (`test_offline_sale_pos_*`). Retain inventory transfer, ledger, and cancellation tests. |
| `tests/Feature/StockAllocatorTest.php` | Test | **MODIFY** | Medium | Phase 5.15 | Update allocator tests to 2-pool configuration (Online & Central). |
| Location `LOC-POS` (`offline`) in `locations` | Data | **MIGRATE (Soft-Disable)** | Medium | Phase 4.2 | Set `is_active = 0`. Retain row to maintain FK integrity on historical `inventory_items` and `inventory_movements`. |
| `InventoryMovement` rows with `movement_type = 'Offline Sale'` | Data | **KEEP** | Critical | Phase 4 | Immutable financial/inventory audit ledger required by accounting and tax authority. Never drop. |
| `Order` rows with `channel = 'offline'` | Data | **KEEP** | Critical | Phase 4 | ZATCA/VAT historical sales records. Never drop. |
| `products.stock_offline` data pool | Data | **MIGRATE** | High | Phase 4.1 | Merge `stock_offline` into `central_wh` via ledgered `Stock Reconciliation` movement. Set `stock_offline = 0`. |
| Column `products.stock_offline` | Schema | **KEEP (Deferred Drop)** | High | Phase 4.6 | Retain column nullable/zeroed in current release to avoid downtime/breaking changes. Schedule drop for next release. |
| Client-side assets (`pos_held_cart`, Web Audio, hotkeys) | Asset | **DELETE** | Low | Phase 5.4 | Removed automatically with `create.blade.php`. No standalone external npm POS package detected. |

---

## 2. Shared Components Flagged (MODIFY, NOT DELETE)
The following files merely touch or reference POS sales and must strictly be **MODIFIED** rather than deleted:
1. `app/Http/Controllers/Admin/InvoiceController.php` — tax retention requirement.
2. `app/Http/Controllers/Admin/ReportController.php` — general reporting.
3. `app/Http/Controllers/Admin/InventoryController.php` — core inventory control.
4. `app/Http/Controllers/Admin/ProductController.php` — product catalog management.
5. `app/Services/InventoryService.php` — core stock movements.
6. `app/Models/Order.php` & `app/Models/Product.php` — core domain models.
7. `resources/views/admin/inventory/allocator.blade.php` — two-hub inventory rebalancing.

---

## 3. Summary Statistics
- Total files to DELETE: 4 (1 controller, 3 views)
- Total files to MODIFY: 28 (controllers, views, models, viewmodels, tests, translations)
- Total data migrations to add: 4 reversible migrations
- Critical financial/audit records preserved: 100% (`orders`, `order_items`, `inventory_movements`, `LOC-POS` location)
