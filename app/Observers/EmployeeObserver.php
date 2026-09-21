<?php

namespace App\Observers;

use App\Models\Employee;
use App\Services\Hr\EmployeeNumberService;

class EmployeeObserver
{
    public function creating(Employee $employee): void
    {
        if (empty($employee->employee_number)) {
            $employee->employee_number = EmployeeNumberService::getInstance()->generateNextNumber();
        }
    }
}
