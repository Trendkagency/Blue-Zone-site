<?php

namespace App\Policies;

use App\Models\GoodsReceipt;
use App\Models\User;

class GoodsReceiptPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('goods_receipts.view') || $user->hasPermission('*');
    }

    public function view(User $user, GoodsReceipt $receipt): bool
    {
        return $user->hasPermission('goods_receipts.view') || $user->hasPermission('*');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('goods_receipts.create') || $user->hasPermission('*');
    }
}
