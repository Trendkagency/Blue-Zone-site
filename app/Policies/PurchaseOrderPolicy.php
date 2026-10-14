<?php

namespace App\Policies;

use App\Models\PurchaseOrder;
use App\Models\User;

class PurchaseOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('purchase_orders.view') || $user->hasPermission('*');
    }

    public function view(User $user, PurchaseOrder $order): bool
    {
        return $user->hasPermission('purchase_orders.view') || $user->hasPermission('*');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('purchase_orders.create') || $user->hasPermission('*');
    }

    public function update(User $user, PurchaseOrder $order): bool
    {
        if (in_array($order->status->value, ['received', 'cancelled'], true)) {
            return false;
        }

        return $user->hasPermission('purchase_orders.edit') || $user->hasPermission('*');
    }

    public function delete(User $user, PurchaseOrder $order): bool
    {
        if ($order->status->value !== 'draft') {
            return false;
        }

        return $user->hasPermission('purchase_orders.delete') || $user->hasPermission('*');
    }

    public function approve(User $user, PurchaseOrder $order): bool
    {
        return $order->canBeApprovedBy($user);
    }
}
