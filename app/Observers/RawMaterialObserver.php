<?php

namespace App\Observers;

use App\Models\RawMaterial;

class RawMaterialObserver
{
    public function saved(RawMaterial $rawMaterial): void
    {
        if ($rawMaterial->isDirty('standard_cost')) {
            $products = $rawMaterial->products()->get();
            foreach ($products as $product) {
                $product->syncCostFromBom();
            }
        }
    }
}
