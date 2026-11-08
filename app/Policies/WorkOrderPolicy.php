<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkOrder;

class WorkOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('work_orders.view') || $user->hasPermission('*');
    }

    public function view(User $user, WorkOrder $order): bool
    {
        return $user->hasPermission('work_orders.view') || $user->hasPermission('*');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('work_orders.create') || $user->hasPermission('*');
    }

    public function update(User $user, WorkOrder $order): bool
    {
        return $user->hasPermission('work_orders.create') || $user->hasPermission('work_orders.edit') || $user->hasPermission('*');
    }

    public function dispense(User $user, WorkOrder $order): bool
    {
        return $user->hasPermission('work_orders.dispense') || $user->hasPermission('work_orders.create') || $user->hasPermission('*');
    }

    public function complete(User $user, WorkOrder $order): bool
    {
        return $user->hasPermission('work_orders.complete') || $user->hasPermission('work_orders.create') || $user->hasPermission('*');
    }

    public function cancel(User $user, WorkOrder $order): bool
    {
        return $user->hasPermission('work_orders.cancel') || $user->hasPermission('work_orders.create') || $user->hasPermission('*');
    }
}
