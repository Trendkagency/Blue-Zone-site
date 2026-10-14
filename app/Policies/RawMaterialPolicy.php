<?php

namespace App\Policies;

use App\Models\RawMaterial;
use App\Models\User;

class RawMaterialPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('raw_materials.view') || $user->hasPermission('*');
    }

    public function view(User $user, RawMaterial $material): bool
    {
        return $user->hasPermission('raw_materials.view') || $user->hasPermission('*');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('raw_materials.create') || $user->hasPermission('*');
    }

    public function update(User $user, RawMaterial $material): bool
    {
        return $user->hasPermission('raw_materials.edit') || $user->hasPermission('*');
    }

    public function delete(User $user, RawMaterial $material): bool
    {
        // Dependency guard: cannot delete if used in an active recipe or has physical stock
        if ($material->recipeItems()->exists() || $material->batches()->where('current_qty', '>', 0)->exists()) {
            return false;
        }

        return $user->hasPermission('raw_materials.delete') || $user->hasPermission('*');
    }
}
