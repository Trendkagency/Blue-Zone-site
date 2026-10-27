# BLUE ZONE™ — POS Removal Backup & Baseline Registry

**Timestamp:** 2026-10-27 12:55:00 UTC+3  
**Branch:** `chore/remove-pos`  
**Database:** `Blue_Zone_db` (MySQL 8.4)

---

## 1. Database & Snapshot Backups

| Asset | Location | Algorithm | Checksum / Details |
|---|---|---|---|
| **Full DB SQL Dump** | `storage/backups/blue_zone_db_baseline_before_pos_removal.sql` | SHA256 | `1B73107DDDCC8B71D6F4BBE96630D1458F06D51BE7B7E7509746F42CC1CCD7E0` |
| **Offline Orders Export** | `docs/pos-removal/exports/offline_orders.csv` | CSV | 8 orders (ZATCA historical records preserved) |
| **Offline Order Items** | `docs/pos-removal/exports/offline_order_items.csv` | CSV | All order items for the 8 offline orders |
| **Offline Inventory Movements** | `docs/pos-removal/exports/offline_movements.csv` | CSV | 1 historical 'Offline Sale' movement record |
| **Product Stock Baseline** | `docs/pos-removal/exports/product_stock_baseline.csv` | CSV | Product inventory balances across Online, POS, and Central |

---

## 2. Baseline Reconciliation Metrics (§3.2)

These numbers constitute the immutable reconciliation targets for **Phase 8 (Reconciliation & Sign-off)**.

### 2.1 Product Stock Baseline
| Product ID | SKU | Product Name | Stock Online | Stock Offline | Stock Central (`central_wh`) | Total Physical Units |
|---|---|---|---|---|---|---|
| 1 | `BZ-JNT-001` | BLUE JOINT Joint & Cartilage Support | 150 | 45 | 50 | **245** |
| 2 | `BZ-MND-001` | BLUE MIND Precision Nootropic | 124 | 38 | 50 | **212** |
| 3 | `BZ-PEA-001` | BLUE PEA Pure Ultra-Micronized PEA | 110 | 35 | 50 | **195** |
| 4 | `BZ-SHD-001` | BLUE SHIELD Cellular Defense Complex | 180 | 60 | 50 | **290** |
| **SUM** | — | — | **564** | **178** | **200** | **942 units** |

**Invariant Requirement:** After merging `stock_offline` into `central_wh`, the total units per product must remain strictly identical:
- Total `stock_offline` must become **0**.
- Total `stock_central` must become **378** (200 + 178).
- Total system units must remain **942**.

### 2.2 Orders by Channel Baseline
| Channel | Status | Payment Status | Count | Total Revenue (SAR) |
|---|---|---|---|---|
| `online` | delivered | Paid | 10 | 2,240.20 |
| `online` | processing | Paid | 1 | 235.75 |
| `online` | shipped | Paid | 1 | 148.40 |
| `offline` | delivered | Paid | 8 | 1,266.15 |
| **TOTAL** | — | — | **20** | **3,890.50 SAR** |

**Invariant Requirement:**
- Historical offline orders count: strictly **8** before == after.
- Offline revenue: strictly **1,266.15 SAR** before == after.

### 2.3 Inventory Movements Baseline
| Movement Type | Count | Sum Quantity |
|---|---|---|
| Stock Transfer | 1 | +50 |
| Online Sale | 1 | -2 |
| Offline Sale | 1 | -1 |
| Damaged | 1 | -1 |
| Return | 1 | +1 |
| **TOTAL** | **5** | — |

**Invariant Requirement:**
- Existing movements stay untouched.
- Phase 4 migration will add exactly 4 `'Stock Reconciliation'` movements (one per product with `stock_offline > 0`), bringing total movements count to **9**.

---

## 3. Test Suite Baseline
- Total tests executed: 248
- Passing: 242
- Failing (pre-existing): 6 (unrelated to POS: `AdminProductScienceTest`, `ProductSectionCustomizerTest`)
- POS/Offline-specific tests: 6 passing in `tests/Feature/InventoryAndOfflineSalesTest.php` and `tests/Feature/StockAllocatorTest.php`.
