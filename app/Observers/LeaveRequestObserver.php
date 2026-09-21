<?php

namespace App\Observers;

use App\Models\LeaveRequest;
use App\Services\Hr\HrNotificationService;

class LeaveRequestObserver
{
    public function updated(LeaveRequest $request): void
    {
        if ($request->wasChanged('status') && in_array($request->status, ['approved', 'rejected'], true)) {
            $employee = $request->employee;
            if ($employee && $employee->user_id) {
                $statusText = $request->status === 'approved' ? 'Approved' : 'Rejected';
                $title = "Leave Request {$statusText}";
                $message = "Your leave request for {$request->total_days} days from {$request->start_date->format('Y-m-d')} has been {$request->status}.";
                
                HrNotificationService::getInstance()->sendEmployeeNotification($employee, $title, $message, [
                    'type' => 'leave',
                    'action_url' => url('/admin/hr/leaves'),
                ]);
            }
        }
    }
}
