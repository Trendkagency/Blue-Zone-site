<?php

namespace App\Observers;

use App\Models\ProductRecipeItem;

class ProductRecipeItemObserver
{
    public function saved(ProductRecipeItem $item): void
    {
        $item->product?->syncCostFromBom();
    }

    public function deleted(ProductRecipeItem $item): void
    {
        $item->product?->syncCostFromBom();
    }
}
