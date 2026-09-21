<?php

namespace App\Services\Hr;

use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class EmployeeNumberService
{
    private static ?self $instance = null;

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Generate the next transaction-safe unique employee number (EMP-000001).
     */
    public function generateNextNumber(string $prefix = 'EMP-'): string
    {
        return DB::transaction(function () use ($prefix) {
            // Find max numeric suffix from existing employees including trashed
            $latest = Employee::withTrashed()
                ->where('employee_number', 'like', "{$prefix}%")
                ->lockForUpdate()
                ->orderByRaw('CAST(SUBSTRING(employee_number, ?) AS UNSIGNED) DESC', [strlen($prefix) + 1])
                ->value('employee_number');

            if ($latest) {
                $numberPart = (int) substr($latest, strlen($prefix));
                $nextNumber = $numberPart + 1;
            } else {
                $nextNumber = 1;
            }

            return sprintf('%s%06d', $prefix, $nextNumber);
        });
    }

    /**
     * Generate next candidate number (CAN-000001).
     */
    public function generateCandidateNumber(string $prefix = 'CAN-'): string
    {
        return DB::transaction(function () use ($prefix) {
            $latest = \App\Models\Candidate::withTrashed()
                ->where('candidate_number', 'like', "{$prefix}%")
                ->lockForUpdate()
                ->orderByRaw('CAST(SUBSTRING(candidate_number, ?) AS UNSIGNED) DESC', [strlen($prefix) + 1])
                ->value('candidate_number');

            if ($latest) {
                $numberPart = (int) substr($latest, strlen($prefix));
                $nextNumber = $numberPart + 1;
            } else {
                $nextNumber = 1;
            }

            return sprintf('%s%06d', $prefix, $nextNumber);
        });
    }
}
