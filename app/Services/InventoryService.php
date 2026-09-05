<?php

namespace App\Services;

use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\Location;
use App\Models\Product;
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
                        $initialStock = $product->stock_online;
                    } elseif ($loc->id === 'offline') {
                        $initialStock = $product->stock_offline;
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
                    $item->refreshStatus();
                } else {
                    // If online/offline item exists, ensure product columns remain aligned
                    if ($loc->id === 'online' && $item->current_stock !== $product->stock_online) {
                        $item->current_stock = $product->stock_online;
                        $item->refreshStatus();
                    } elseif ($loc->id === 'offline' && $item->current_stock !== $product->stock_offline) {
                        $item->current_stock = $product->stock_offline;
                        $item->refreshStatus();
                    }
                }
            }
        }
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

        $offlineItem = InventoryItem::where('product_id', $product->id)->where('location_id', 'offline')->first();
        if ($offlineItem) {
            $product->stock_offline = $offlineItem->current_stock;
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
            $item->current_stock = 0;
            $item->available_stock = 0;
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
     */
    public static function adjustStock(
        Product $product,
        string $locationId,
        int $quantityDelta,
        string $movementType,
        ?string $reason = null,
        ?string $userName = null,
        ?string $sourceOrTarget = null
    ): InventoryMovement {
        return DB::transaction(function () use ($product, $locationId, $quantityDelta, $movementType, $reason, $userName, $sourceOrTarget) {
            $item = self::getItem($product, $locationId);
            $prevQty = $item->current_stock;
            $newQty = max(0, $prevQty + $quantityDelta);

            $item->current_stock = $newQty;
            $item->refreshStatus();

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
     * Process offline POS sale deduction.
     */
    public static function processOfflineSale(
        Product $product,
        int $quantity,
        string $orderNumber,
        ?string $userName = null,
        ?string $variant = null
    ): InventoryMovement {
        if ($product->stock_offline < $quantity) {
            throw new InvalidArgumentException(app()->getLocale() === 'ar'
                ? "الرصيد المتوفر في مخزون المعرض ({$product->stock_offline} وحدة) غير كافٍ لإتمام عملية البيع."
                : "Insufficient offline boutique stock ({$product->stock_offline} units) for this sale.");
        }

        return DB::transaction(function () use ($product, $quantity, $orderNumber, $userName, $variant) {
            $offlineItem = self::getItem($product, 'offline');
            $prevQty = $product->stock_offline;
            $newQty = max(0, $prevQty - $quantity);

            $product->stock_offline = $newQty;
            $product->save();

            $offlineItem->current_stock = $newQty;
            $offlineItem->refreshStatus();

            return self::recordMovement([
                'product_id' => $product->id,
                'product_name_en' => $product->name_en,
                'product_name_ar' => $product->name_ar,
                'sku' => $product->sku,
                'variant' => $variant ?? $offlineItem->variant_en ?? 'Standard Pack',
                'movement_type' => 'Offline Sale',
                'from_location' => 'Flagship Boutique / POS',
                'to_location' => 'Customer Handover',
                'quantity' => -$quantity,
                'previous_qty' => $prevQty,
                'new_qty' => $newQty,
                'date' => now()->toDateString(),
                'time' => now()->format('H:i:s'),
                'user' => $userName ?? auth()->user()?->name ?? 'POS Cashier',
                'note' => "Offline Boutique POS Sale #{$orderNumber}",
            ]);
        });
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
