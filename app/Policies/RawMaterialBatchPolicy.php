<?php

namespace App\Policies;

use App\Models\RawMaterialBatch;
use App\Models\User;

class RawMaterialBatchPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('batches.view') || $user->hasPermission('*');
    }

    public function view(User $user, RawMaterialBatch $batch): bool
    {
        return $user->hasPermission('batches.view') || $user->hasPermission('*');
    }

    public function transfer(User $user, RawMaterialBatch $batch): bool
    {
        return $user->hasPermission('batches.transfer') || $user->hasPermission('*');
    }

    public function adjust(User $user, RawMaterialBatch $batch): bool
    {
        return $user->hasPermission('batches.adjust') || $user->hasPermission('*');
    }
}
