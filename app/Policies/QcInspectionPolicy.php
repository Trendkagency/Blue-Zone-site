<?php

namespace App\Policies;

use App\Models\QcInspection;
use App\Models\User;

class QcInspectionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('qc.view') || $user->hasPermission('*');
    }

    public function view(User $user, QcInspection $inspection): bool
    {
        return $user->hasPermission('qc.view') || $user->hasPermission('*');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('qc.create') || $user->hasPermission('*');
    }

    /**
     * Strict Separation of duties: verifier must differ from inspector
     */
    public function verify(User $user, QcInspection $inspection): bool
    {
        if ($inspection->inspector_id === $user->id) {
            return false;
        }

        return $user->hasPermission('qc.verify') || $user->hasPermission('*');
    }
}
