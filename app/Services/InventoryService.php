<?php

namespace App\Services;

use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductBatch;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class InventoryService
{
    /**
     * Ensure every product has inventory_items for all system locations.
     */
    public static function syncAllProductsInventory(): void
    {
        $products = Product::all();
        $locations = Location::where('is_active', true)->get();

        foreach ($products as $product) {
            foreach ($locations as $loc) {
                $item = InventoryItem::firstOrNew([
                    'product_id' => $product->id,
                    'location_id' => $loc->id,
                ]);

                if (!$item->exists) {
                    $initialStock = 0;
                    if ($loc->id === 'online') {
                        $initialStock = $product->stock_online ?? 0;
                    } elseif ($loc->id === 'central_wh') {
                        $initialStock = 50; // default central logistics buffer
                    }

                    $item->location_name_en = $loc->name_en;
                    $item->location_name_ar = $loc->name_ar;
                    $item->variant_en = 'Standard Pack';
                    $item->variant_ar = 'العبوة القياسية';
                    $item->current_stock = $initialStock;
                    $item->available_stock = $initialStock;
                    $item->reserved_stock = 0;
                    $item->low_stock_threshold = $product->low_stock_threshold ?? 15;
                    $item->unit_cost = $product->cost_price ?? ($product->price * 0.4);
                    $item->retail_price = $product->price;
                    $item->save();
                    $item->refreshStatus();
                } else {
                    $item->location_name_en = $loc->name_en;
                    $item->location_name_ar = $loc->name_ar;
                    $item->save();

                    // Ensure Product model columns reflect the InventoryItem balances
                    if ($loc->id === 'online' && $product->stock_online !== $item->current_stock) {
                        $product->stock_online = $item->current_stock;
                        $product->save();
                    }
                }
            }

            self::syncMissingProductBatches($product);
        }
    }

    /**
     * Provision initial ProductBatch records for stocked products that do not yet have any batch history.
     */
    public static function syncMissingProductBatches(Product $product): void
    {
        if ($product->batches()->withTrashed()->exists()) {
            return;
        }

        $stockedItems = InventoryItem::where('product_id', $product->id)
            ->where('current_stock', '>', 0)
            ->get();

        if ($stockedItems->isEmpty()) {
            return;
        }

        foreach ($stockedItems as $item) {
            $qty = (int) $item->current_stock;
            $avail = max(0, min($qty, (int) ($item->available_stock ?? $qty)));
            $reserved = max(0, $qty - $avail);
            $mfg = now()->startOfDay();

            ProductBatch::create([
                'batch_number' => ProductBatch::generateBatchNumber($product),
                'product_id' => $product->id,
                'location_id' => $item->location_id,
                'initial_qty' => $qty,
                'current_qty' => $qty,
                'available_qty' => $avail,
                'reserved_qty' => $reserved,
                'quarantine_qty' => 0,
                'unit_cost' => (float) ($item->unit_cost ?? $product->cost_price ?? 0),
                'manufacture_date' => $mfg,
                'expiry_date' => $product->calculateDefaultExpiryDate($mfg),
                'status' => 'approved',
            ]);
        }
    }

    /**
     * Transfer or split ProductBatch records FEFO between locations so Expiry Date traceability follows stock.
     */
    public static function moveBatchesBetweenLocations(
        Product $product,
        string $fromLocationId,
        string $toLocationId,
        int $quantity
    ): void {
        if ($quantity <= 0 || $fromLocationId === $toLocationId) {
            return;
        }

        $remaining = $quantity;
        $today = now()->toDateString();

        $batches = ProductBatch::where('product_id', $product->id)
            ->where('location_id', $fromLocationId)
            ->where('status', 'approved')
            ->where('available_qty', '>', 0)
            ->orderByRaw('CASE WHEN expiry_date IS NULL OR expiry_date > ? THEN 0 ELSE 1 END', [$today])
            ->orderBy('expiry_date', 'asc')
            ->orderBy('id', 'asc')
            ->lockForUpdate()
            ->get();

        foreach ($batches as $batch) {
            if ($remaining <= 0) {
                break;
            }

            $take = min($remaining, (int) $batch->available_qty);
            if ($take <= 0) {
                continue;
            }

            if (
                $take === (int) $batch->current_qty
                && (int) ($batch->reserved_qty ?? 0) === 0
                && (int) ($batch->quarantine_qty ?? 0) === 0
            ) {
                $batch->location_id = $toLocationId;
                $batch->save();
            } else {
                $batch->deductQuantity($take, "Transfer {$fromLocationId} -> {$toLocationId}");
                ProductBatch::create([
                    'product_id' => $product->id,
                    'batch_number' => ProductBatch::generateChildBatchNumber($batch->batch_number, $toLocationId),
                    'supplier_lot' => $batch->supplier_lot,
                    'work_order_id' => $batch->work_order_id,
                    'goods_receipt_id' => $batch->goods_receipt_id,
                    'location_id' => $toLocationId,
                    'storage_bin' => $batch->storage_bin,
                    'initial_qty' => $take,
                    'current_qty' => $take,
                    'available_qty' => $take,
                    'reserved_qty' => 0,
                    'quarantine_qty' => 0,
                    'unit_cost' => $batch->unit_cost,
                    'manufacture_date' => $batch->manufacture_date,
                    'expiry_date' => $batch->expiry_date,
                    'status' => $batch->status,
                ]);
            }

            $remaining -= $take;
        }

        // Fallback if unbatched legacy hub stock was moved while product already had batches elsewhere
        if ($remaining > 0) {
            $refBatch = ProductBatch::where('product_id', $product->id)
                ->where('status', 'approved')
                ->orderBy('expiry_date', 'asc')
                ->first();
            $mfg = $refBatch?->manufacture_date ?? now()->startOfDay();
            $exp = $refBatch?->expiry_date ?? $product->calculateDefaultExpiryDate($mfg);

            ProductBatch::create([
                'product_id' => $product->id,
                'batch_number' => ProductBatch::generateBatchNumber($product),
                'location_id' => $toLocationId,
                'initial_qty' => $remaining,
                'current_qty' => $remaining,
                'available_qty' => $remaining,
                'reserved_qty' => 0,
                'quarantine_qty' => 0,
                'unit_cost' => (float) ($refBatch?->unit_cost ?? $product->cost_price ?? 0),
                'manufacture_date' => $mfg,
                'expiry_date' => $exp,
                'status' => 'approved',
            ]);
        }
    }

    /**
     * Provision inventory items for a newly created or activated location across all products.
     */
    public static function provisionLocationForProducts(Location $location, int $initialStock = 0): void
    {
        $products = Product::all();
        foreach ($products as $product) {
            $item = InventoryItem::firstOrNew([
                'product_id' => $product->id,
                'location_id' => $location->id,
            ]);

            if (!$item->exists) {
                $stock = $initialStock;
                if ($location->id === 'online') {
                    $stock = $product->stock_online ?? $initialStock;
                } elseif ($location->id === 'central_wh') {
                    $stock = 50;
                }

                $item->location_name_en = $location->name_en;
                $item->location_name_ar = $location->name_ar;
                $item->variant_en = 'Standard Pack';
                $item->variant_ar = 'العبوة القياسية';
                $item->current_stock = $stock;
                $item->available_stock = $stock;
                $item->reserved_stock = 0;
                $item->low_stock_threshold = $product->low_stock_threshold ?? 15;
                $item->unit_cost = $product->cost_price ?? ($product->price * 0.4);
                $item->retail_price = $product->price;
                $item->save();
                $item->refreshStatus();
            } else {
                $item->location_name_en = $location->name_en;
                $item->location_name_ar = $location->name_ar;
                $item->save();
            }
        }
    }

    /**
     * Update cached location names on all associated inventory items.
     */
    public static function syncLocationNames(Location $location): void
    {
        InventoryItem::where('location_id', $location->id)->update([
            'location_name_en' => $location->name_en,
            'location_name_ar' => $location->name_ar,
        ]);
    }

    /**
     * Synchronize Product model online/offline stock with location items.
     */
    public static function syncProductModelStock(Product $product): void
    {
        $onlineItem = InventoryItem::where('product_id', $product->id)->where('location_id', 'online')->first();
        if ($onlineItem) {
            $product->stock_online = $onlineItem->current_stock;
        }

        $product->save();
    }

    /**
     * Get or initialize an InventoryItem for a product and location.
     */
    public static function getItem(Product $product, string $locationId): InventoryItem
    {
        $item = InventoryItem::firstOrNew([
            'product_id' => $product->id,
            'location_id' => $locationId,
        ]);

        if (!$item->exists) {
            $loc = Location::find($locationId);
            $item->location_name_en = $loc?->name_en ?? ucfirst($locationId);
            $item->location_name_ar = $loc?->name_ar ?? ucfirst($locationId);
            $item->variant_en = 'Standard Pack';
            $item->variant_ar = 'العبوة القياسية';
            $initialStock = 0;
            if ($locationId === 'online') {
                $initialStock = (int) ($product->stock_online ?? 0);
            }

            $item->current_stock = $initialStock;
            $item->available_stock = $initialStock;
            $item->reserved_stock = 0;
            $item->low_stock_threshold = $product->low_stock_threshold ?? 15;
            $item->unit_cost = $product->cost_price;
            $item->retail_price = $product->price;
            $item->save();
        }

        return $item;
    }

    /**
     * Transfer stock between two locations with validation and audit movement.
     */
    public static function transferStock(
        Product $product,
        string $fromLocationId,
        string $toLocationId,
        int $quantity,
        ?string $reason = null,
        ?string $userName = null
    ): InventoryMovement {
        if ($fromLocationId === $toLocationId) {
            throw new InvalidArgumentException(app()->getLocale() === 'ar' 
                ? 'لا يمكن التحويل إلى نفس موقع المصدر.' 
                : 'Source and destination locations cannot be identical.');
        }

        if ($quantity <= 0) {
            throw new InvalidArgumentException(app()->getLocale() === 'ar'
                ? 'الكمية المحولة يجب أن تكون أكبر من صفر.'
                : 'Transfer quantity must be greater than zero.');
        }

        return DB::transaction(function () use ($product, $fromLocationId, $toLocationId, $quantity, $reason, $userName) {
            $sourceItem = self::getItem($product, $fromLocationId);
            $destItem = self::getItem($product, $toLocationId);

            if ($sourceItem->available_stock < $quantity) {
                $sourceName = app()->getLocale() === 'ar' ? ($sourceItem->location_name_ar ?? $fromLocationId) : ($sourceItem->location_name_en ?? $fromLocationId);
                throw new InvalidArgumentException(app()->getLocale() === 'ar'
                    ? "الرصيد المتوفر في [{$sourceName}] ({$sourceItem->available_stock} وحدة) غير كافٍ لتحويل {$quantity} وحدة."
                    : "Insufficient available stock in [{$sourceName}] ({$sourceItem->available_stock} units) to transfer {$quantity} units.");
            }

            $prevSourceQty = $sourceItem->current_stock;
            $prevDestQty = $destItem->current_stock;

            // Deduct source
            $sourceItem->current_stock = max(0, $sourceItem->current_stock - $quantity);
            $sourceItem->refreshStatus();

            // Increment destination
            $destItem->current_stock += $quantity;
            $destItem->refreshStatus();

            // Synchronize product batches (FEFO move/split) BEFORE syncing product model stock
            self::moveBatchesBetweenLocations($product, $fromLocationId, $toLocationId, $quantity);

            // Synchronize product columns
            self::syncProductModelStock($product);

            // Record Movement
            return self::recordMovement([
                'product_id' => $product->id,
                'product_name_en' => $product->name_en,
                'product_name_ar' => $product->name_ar,
                'sku' => $product->sku,
                'variant' => $sourceItem->variant_en ?? 'Standard Pack',
                'movement_type' => 'Stock Transfer',
                'from_location' => $sourceItem->location_name_en ?? $fromLocationId,
                'to_location' => $destItem->location_name_en ?? $toLocationId,
                'quantity' => $quantity,
                'previous_qty' => $prevSourceQty,
                'new_qty' => $sourceItem->current_stock,
                'date' => now()->toDateString(),
                'time' => now()->format('H:i:s'),
                'user' => $userName ?? auth()->user()?->name ?? 'System Admin',
                'note' => $reason ?? 'Inter-hub inventory relocation',
            ]);
        });
    }

    /**
     * Adjust stock for a single location (Stock In, Stock Out, Damaged, Expired, Return, Manual Adjustment).
     *
     * @param array<string, mixed>|null $batchMeta
     */
    public static function adjustStock(
        Product $product,
        string $locationId,
        int $quantityDelta,
        string $movementType,
        ?string $reason = null,
        ?string $userName = null,
        ?string $sourceOrTarget = null,
        bool $syncBatch = true,
        ?array $batchMeta = null
    ): InventoryMovement {
        return DB::transaction(function () use ($product, $locationId, $quantityDelta, $movementType, $reason, $userName, $sourceOrTarget, $syncBatch, $batchMeta) {
            $item = self::getItem($product, $locationId);
            $prevQty = $item->current_stock;
            $newQty = max(0, $prevQty + $quantityDelta);

            $item->current_stock = $newQty;
            $item->refreshStatus();

            if ($syncBatch && $quantityDelta !== 0) {
                self::syncBatchOnAdjustment($product, $locationId, $quantityDelta, $movementType, $batchMeta);
            }

            self::syncProductModelStock($product);

            $from = $quantityDelta < 0 ? ($item->location_name_en ?? $locationId) : ($sourceOrTarget ?? 'External Inbound');
            $to = $quantityDelta < 0 ? ($sourceOrTarget ?? 'Inventory Write-Off') : ($item->location_name_en ?? $locationId);

            return self::recordMovement([
                'product_id' => $product->id,
                'product_name_en' => $product->name_en,
                'product_name_ar' => $product->name_ar,
                'sku' => $product->sku,
                'variant' => $item->variant_en ?? 'Standard Pack',
                'movement_type' => $movementType,
                'from_location' => $from,
                'to_location' => $to,
                'quantity' => $quantityDelta,
                'previous_qty' => $prevQty,
                'new_qty' => $newQty,
                'date' => now()->toDateString(),
                'time' => now()->format('H:i:s'),
                'user' => $userName ?? auth()->user()?->name ?? 'System Admin',
                'note' => $reason ?? "Manual inventory adjustment ({$movementType})",
            ]);
        });
    }

    /**
     * Synchronize ProductBatch balances when manual stock adjustments occur.
     *
     * @param array<string, mixed>|null $batchMeta
     */
    protected static function syncBatchOnAdjustment(
        Product $product,
        string $locationId,
        int $quantityDelta,
        string $movementType,
        ?array $batchMeta = null
    ): void {
        $batchMeta = $batchMeta ?? [];

        if ($quantityDelta > 0) {
            if (!empty($batchMeta['product_batch_id'])) {
                $targetBatch = ProductBatch::where('product_id', $product->id)
                    ->lockForUpdate()
                    ->find($batchMeta['product_batch_id']);
                if ($targetBatch) {
                    $targetBatch->restoreQuantity($quantityDelta);
                    return;
                }
            }

            $requestedNumber = !empty($batchMeta['batch_number']) ? trim((string) $batchMeta['batch_number']) : null;
            if ($requestedNumber) {
                $existingSame = ProductBatch::where('batch_number', $requestedNumber)->lockForUpdate()->first();
                if ($existingSame && (int) $existingSame->product_id === (int) $product->id && $existingSame->location_id === $locationId) {
                    $existingSame->restoreQuantity($quantityDelta);
                    return;
                }
                if (ProductBatch::withTrashed()->where('batch_number', $requestedNumber)->exists()) {
                    $requestedNumber = ProductBatch::generateBatchNumber($product);
                }
            }

            $mfgDate = !empty($batchMeta['manufacture_date'])
                ? Carbon::parse($batchMeta['manufacture_date'])->startOfDay()
                : now()->startOfDay();
            $expDate = !empty($batchMeta['expiry_date'])
                ? Carbon::parse($batchMeta['expiry_date'])->startOfDay()
                : $product->calculateDefaultExpiryDate($mfgDate);

            ProductBatch::create([
                'product_id' => $product->id,
                'batch_number' => $requestedNumber ?: ProductBatch::generateBatchNumber($product),
                'supplier_lot' => $batchMeta['supplier_lot'] ?? null,
                'location_id' => $locationId,
                'initial_qty' => $quantityDelta,
                'current_qty' => $quantityDelta,
                'available_qty' => $quantityDelta,
                'reserved_qty' => 0,
                'quarantine_qty' => 0,
                'unit_cost' => (float) ($batchMeta['unit_cost'] ?? $product->cost_price ?? 0),
                'manufacture_date' => $mfgDate,
                'expiry_date' => $expDate,
                'status' => 'approved',
            ]);

            return;
        }

        // Negative delta: deduct from specified batch or FEFO across matching hub batches
        $remaining = abs($quantityDelta);

        if (!empty($batchMeta['product_batch_id'])) {
            $specific = ProductBatch::where('product_id', $product->id)
                ->lockForUpdate()
                ->find($batchMeta['product_batch_id']);
            if ($specific && $specific->available_qty > 0) {
                $take = min($remaining, (int) $specific->available_qty);
                $specific->deductQuantity($take, $movementType);
                $remaining -= $take;
            }
        }

        if ($remaining <= 0) {
            return;
        }

        $query = ProductBatch::where('product_id', $product->id)
            ->where('location_id', $locationId)
            ->where('available_qty', '>', 0)
            ->lockForUpdate();

        if ($movementType === 'Expired') {
            $query->orderByRaw('CASE WHEN expiry_date <= ? THEN 0 ELSE 1 END', [now()->toDateString()]);
        }
        $query->orderBy('expiry_date', 'asc')->orderBy('id', 'asc');

        foreach ($query->get() as $batch) {
            if ($remaining <= 0) {
                break;
            }
            $take = min($remaining, (int) $batch->available_qty);
            if ($take > 0) {
                $batch->deductQuantity($take, $movementType);
                $remaining -= $take;
            }
        }
    }



    /**
     * Record an auditable InventoryMovement.
     *
     * @param array<string, mixed> $data
     */
    public static function recordMovement(array $data): InventoryMovement
    {
        return InventoryMovement::create([
            'product_id' => $data['product_id'],
            'product_name_en' => $data['product_name_en'],
            'product_name_ar' => $data['product_name_ar'] ?? null,
            'sku' => $data['sku'] ?? null,
            'variant' => $data['variant'] ?? null,
            'movement_type' => $data['movement_type'],
            'from_location' => $data['from_location'] ?? null,
            'to_location' => $data['to_location'] ?? null,
            'quantity' => (int) $data['quantity'],
            'previous_qty' => isset($data['previous_qty']) ? (int) $data['previous_qty'] : null,
            'new_qty' => isset($data['new_qty']) ? (int) $data['new_qty'] : null,
            'date' => $data['date'] ?? now()->toDateString(),
            'time' => $data['time'] ?? now()->format('H:i:s'),
            'user' => $data['user'] ?? auth()->user()?->name ?? 'System',
            'note' => $data['note'] ?? null,
        ]);
    }
}
