<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $products = DB::table('products')->get();

            foreach ($products as $product) {
                $stockOffline = (int) ($product->stock_offline ?? 0);
                if ($stockOffline <= 0) {
                    continue;
                }

                $stockOnline = (int) ($product->stock_online ?? 0);

                // Find or create central inventory item
                $centralItem = DB::table('inventory_items')
                    ->where('product_id', $product->id)
                    ->where('location_id', 'central_wh')
                    ->first();

                $prevCentralStock = $centralItem ? (int) $centralItem->current_stock : 0;
                $newCentralStock = $prevCentralStock + $stockOffline;

                // Total stock before merge
                $totalBefore = $stockOnline + $stockOffline + $prevCentralStock;

                if ($centralItem) {
                    DB::table('inventory_items')
                        ->where('id', $centralItem->id)
                        ->update([
                            'current_stock' => $newCentralStock,
                            'available_stock' => (int) $centralItem->available_stock + $stockOffline,
                            'updated_at' => now(),
                        ]);
                } else {
                    DB::table('inventory_items')->insert([
                        'product_id' => $product->id,
                        'location_id' => 'central_wh',
                        'location_name_en' => 'Central Quarantine Warehouse',
                        'location_name_ar' => 'المستودع المركزي الرئيسي',
                        'variant_en' => 'Standard Pack',
                        'variant_ar' => 'العبوة القياسية',
                        'current_stock' => $newCentralStock,
                        'available_stock' => $newCentralStock,
                        'reserved_stock' => 0,
                        'low_stock_threshold' => 15,
                        'status' => 'in_stock',
                        'unit_cost' => $product->cost_price ?? 25.00,
                        'retail_price' => $product->price ?? 65.00,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Update offline inventory item to 0
                DB::table('inventory_items')
                    ->where('product_id', $product->id)
                    ->where('location_id', 'offline')
                    ->update([
                        'current_stock' => 0,
                        'available_stock' => 0,
                        'status' => 'out_of_stock',
                        'updated_at' => now(),
                    ]);

                // Record immutable audit ledger movement
                DB::table('inventory_movements')->insert([
                    'product_id' => $product->id,
                    'product_name_en' => $product->name_en,
                    'product_name_ar' => $product->name_ar,
                    'sku' => $product->sku,
                    'movement_type' => 'Stock Reconciliation',
                    'from_location' => 'Warehouse / POS',
                    'to_location' => 'Central Quarantine Warehouse',
                    'quantity' => $stockOffline,
                    'previous_qty' => $prevCentralStock,
                    'new_qty' => $newCentralStock,
                    'date' => now()->toDateString(),
                    'time' => now()->toTimeString(),
                    'user' => 'System Decommission (POS Removal)',
                    'note' => 'POS decommission – merge to central (REC-POS-MERGE-' . $product->id . ')',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Set stock_offline = 0 on product
                DB::table('products')
                    ->where('id', $product->id)
                    ->update([
                        'stock_offline' => 0,
                        'updated_at' => now(),
                    ]);

                // Total stock after merge
                $totalAfter = $stockOnline + 0 + $newCentralStock;

                if ($totalBefore !== $totalAfter) {
                    throw new \RuntimeException(
                        "Stock invariant mismatch for product ID {$product->id}: before={$totalBefore}, after={$totalAfter}."
                    );
                }
            }
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            // Find all reconciliation movements created during merge
            $movements = DB::table('inventory_movements')
                ->where('movement_type', 'Stock Reconciliation')
                ->where('note', 'like', '%REC-POS-MERGE-%')
                ->get();

            foreach ($movements as $m) {
                $qty = (int) $m->quantity;

                // Re-credit offline stock
                DB::table('products')
                    ->where('id', $m->product_id)
                    ->update([
                        'stock_offline' => $qty,
                        'updated_at' => now(),
                    ]);

                // Re-credit offline inventory item
                DB::table('inventory_items')
                    ->where('product_id', $m->product_id)
                    ->where('location_id', 'offline')
                    ->update([
                        'current_stock' => $qty,
                        'available_stock' => $qty,
                        'status' => 'in_stock',
                        'updated_at' => now(),
                    ]);

                // Deduct from central item
                $central = DB::table('inventory_items')
                    ->where('product_id', $m->product_id)
                    ->where('location_id', 'central_wh')
                    ->first();

                if ($central) {
                    DB::table('inventory_items')
                        ->where('id', $central->id)
                        ->update([
                            'current_stock' => max(0, (int) $central->current_stock - $qty),
                            'available_stock' => max(0, (int) $central->available_stock - $qty),
                            'updated_at' => now(),
                        ]);
                }

                // Delete reconciliation movement
                DB::table('inventory_movements')->where('id', $m->id)->delete();
            }
        });
    }
};
