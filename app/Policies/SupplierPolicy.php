<?php

namespace App\Policies;

use App\Models\Supplier;
use App\Models\User;

class SupplierPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('suppliers.view') || $user->hasPermission('*');
    }

    public function view(User $user, Supplier $supplier): bool
    {
        return $user->hasPermission('suppliers.view') || $user->hasPermission('*');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('suppliers.create') || $user->hasPermission('*');
    }

    public function update(User $user, Supplier $supplier): bool
    {
        return $user->hasPermission('suppliers.edit') || $user->hasPermission('*');
    }

    public function delete(User $user, Supplier $supplier): bool
    {
        // Dependency guard: cannot delete if active purchase orders or batches exist
        if ($supplier->purchaseOrders()->whereNotIn('status', ['cancelled', 'received'])->exists() || $supplier->batches()->where('current_qty', '>', 0)->exists()) {
            return false;
        }

        return $user->hasPermission('suppliers.delete') || $user->hasPermission('*');
    }

    public function approve(User $user, Supplier $supplier): bool
    {
        return $user->hasPermission('suppliers.approve') || $user->hasPermission('*');
    }
}
