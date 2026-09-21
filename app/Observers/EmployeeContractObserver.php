<?php

namespace App\Observers;

use App\Models\EmployeeContract;
use App\Services\Hr\HrNotificationService;

class EmployeeContractObserver
{
    public function created(EmployeeContract $contract): void
    {
        $employee = $contract->employee;
        if ($employee && $employee->user_id) {
            HrNotificationService::getInstance()->sendEmployeeNotification(
                $employee,
                'New Employment Contract',
                "Your contract ({$contract->contract_number}) has been registered.",
                ['type' => 'contract', 'action_url' => url('/admin/hr/employees/' . $employee->id)]
            );
        }
    }
}
