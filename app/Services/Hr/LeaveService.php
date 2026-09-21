<?php

namespace App\Services\Hr;

use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class LeaveService
{
    /**
     * Submit a leave request and reserve pending days on the employee's balance.
     */
    public function submitRequest(Employee $employee, array $data): LeaveRequest
    {
        return DB::transaction(function () use ($employee, $data) {
            $startDate = Carbon::parse($data['start_date']);
            $endDate = Carbon::parse($data['end_date']);

            if ($endDate->lessThan($startDate)) {
                throw new InvalidArgumentException("End date cannot be earlier than start date.");
            }

            $totalDays = $startDate->diffInDays($endDate) + 1;
            $year = (int) $startDate->year;

            $balance = LeaveBalance::firstOrCreate(
                [
                    'employee_id' => $employee->id,
                    'leave_type_id' => $data['leave_type_id'],
                    'year' => $year,
                ],
                [
                    'allocated_days' => 21.00,
                    'used_days' => 0.00,
                    'pending_days' => 0.00,
                    'remaining_days' => 21.00,
                ]
            );

            // Check if remaining days are sufficient
            if ($balance->remaining_days < $totalDays) {
                throw new InvalidArgumentException("Insufficient leave balance. Available: {$balance->remaining_days} days, Requested: {$totalDays} days.");
            }

            // Reserve pending days
            $balance->pending_days += $totalDays;
            $balance->save();

            return LeaveRequest::create([
                'employee_id' => $employee->id,
                'leave_type_id' => $data['leave_type_id'],
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'total_days' => $totalDays,
                'reason' => $data['reason'] ?? '',
                'attachment_path' => $data['attachment_path'] ?? null,
                'status' => 'pending',
            ]);
        });
    }

    /**
     * Approve leave request and deduct days from balance.
     */
    public function approveRequest(LeaveRequest $request, ?int $approvedByUserId = null): bool
    {
        return DB::transaction(function () use ($request, $approvedByUserId) {
            if ($request->status === 'approved') {
                return true;
            }

            $year = (int) Carbon::parse($request->start_date)->year;
            $balance = LeaveBalance::where('employee_id', $request->employee_id)
                ->where('leave_type_id', $request->leave_type_id)
                ->where('year', $year)
                ->first();

            if ($balance) {
                $days = (float) $request->total_days;
                $balance->pending_days = max(0, $balance->pending_days - $days);
                $balance->used_days += $days;
                $balance->remaining_days = max(0, $balance->allocated_days - $balance->used_days);
                $balance->save();
            }

            $request->update([
                'status' => 'approved',
                'approved_by' => $approvedByUserId,
            ]);

            return true;
        });
    }

    /**
     * Reject leave request and release pending reserved days.
     */
    public function rejectRequest(LeaveRequest $request, string $reason = '', ?int $rejectedByUserId = null): bool
    {
        return DB::transaction(function () use ($request, $reason, $rejectedByUserId) {
            if ($request->status === 'rejected') {
                return true;
            }

            $year = (int) Carbon::parse($request->start_date)->year;
            $balance = LeaveBalance::where('employee_id', $request->employee_id)
                ->where('leave_type_id', $request->leave_type_id)
                ->where('year', $year)
                ->first();

            if ($balance && $request->status === 'pending') {
                $days = (float) $request->total_days;
                $balance->pending_days = max(0, $balance->pending_days - $days);
                $balance->save();
            }

            $request->update([
                'status' => 'rejected',
                'rejected_by' => $rejectedByUserId,
                'rejection_reason' => $reason,
            ]);

            return true;
        });
    }
}
