<?php

namespace App\Services\Hr;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AttendanceService
{
    /**
     * Record daily check-in for an employee.
     */
    public function recordCheckIn(int $employeeId, ?string $time = null, string $source = 'web', ?string $date = null): AttendanceRecord
    {
        $attendanceDate = $date ? Carbon::parse($date)->toDateString() : now()->toDateString();
        $checkInTime = $time ? Carbon::parse($time)->format('H:i:s') : now()->format('H:i:s');

        // Check for duplicate attendance
        $existing = AttendanceRecord::where('employee_id', $employeeId)
            ->where('attendance_date', $attendanceDate)
            ->first();

        if ($existing && !empty($existing->check_in)) {
            throw new InvalidArgumentException("Employee {$employeeId} already checked in on {$attendanceDate}.");
        }

        $employee = Employee::with('workSchedule')->findOrFail($employeeId);
        $schedule = $employee->workSchedule;

        $lateMinutes = 0;
        $status = 'present';

        if ($schedule && $schedule->start_time) {
            $schedStart = Carbon::parse($attendanceDate . ' ' . $schedule->start_time);
            $actualStart = Carbon::parse($attendanceDate . ' ' . $checkInTime);
            $graceCutoff = $schedStart->copy()->addMinutes($schedule->grace_minutes ?? 0);

            if ($actualStart->greaterThan($graceCutoff)) {
                $lateMinutes = (int) $schedStart->diffInMinutes($actualStart);
                $status = 'late';
            }
        }

        return AttendanceRecord::updateOrCreate(
            [
                'employee_id' => $employeeId,
                'attendance_date' => $attendanceDate,
            ],
            [
                'check_in' => $checkInTime,
                'late_minutes' => $lateMinutes,
                'status' => $status,
                'source' => $source,
            ]
        );
    }

    /**
     * Record daily check-out for an employee and compute worked hours, early departure, and overtime.
     */
    public function recordCheckOut(int $employeeId, ?string $time = null, ?string $date = null): AttendanceRecord
    {
        $attendanceDate = $date ? Carbon::parse($date)->toDateString() : now()->toDateString();
        $checkOutTime = $time ? Carbon::parse($time)->format('H:i:s') : now()->format('H:i:s');

        $record = AttendanceRecord::where('employee_id', $employeeId)
            ->where('attendance_date', $attendanceDate)
            ->firstOrFail();

        $employee = Employee::with('workSchedule')->findOrFail($employeeId);
        $schedule = $employee->workSchedule;

        $checkIn = Carbon::parse($attendanceDate . ' ' . $record->check_in);
        $checkOut = Carbon::parse($attendanceDate . ' ' . $checkOutTime);

        $workedMinutes = max(0, (int) $checkIn->diffInMinutes($checkOut));
        if ($schedule && $schedule->break_minutes && $workedMinutes > $schedule->break_minutes) {
            $workedMinutes -= $schedule->break_minutes;
        }

        $earlyLeaveMinutes = 0;
        $overtimeMinutes = 0;

        if ($schedule && $schedule->end_time) {
            $schedEnd = Carbon::parse($attendanceDate . ' ' . $schedule->end_time);
            if ($checkOut->lessThan($schedEnd)) {
                $earlyLeaveMinutes = (int) $checkOut->diffInMinutes($schedEnd);
            } elseif ($checkOut->greaterThan($schedEnd)) {
                $overtimeMinutes = (int) $schedEnd->diffInMinutes($checkOut);
            }
        }

        $record->update([
            'check_out' => $checkOutTime,
            'worked_minutes' => $workedMinutes,
            'early_leave_minutes' => $earlyLeaveMinutes,
            'overtime_minutes' => $overtimeMinutes,
        ]);

        return $record;
    }

    /**
     * Record manual attendance entry by HR administrator.
     */
    public function recordManualAttendance(array $data, ?int $approvedBy = null): AttendanceRecord
    {
        return DB::transaction(function () use ($data, $approvedBy) {
            $employee = Employee::with('workSchedule')->findOrFail($data['employee_id']);
            $date = $data['attendance_date'];
            $checkIn = !empty($data['check_in']) ? Carbon::parse($data['check_in'])->format('H:i:s') : null;
            $checkOut = !empty($data['check_out']) ? Carbon::parse($data['check_out'])->format('H:i:s') : null;

            $workedMinutes = 0;
            $lateMinutes = 0;
            $earlyMinutes = 0;
            $overtimeMinutes = 0;

            if ($checkIn && $checkOut) {
                $inDt = Carbon::parse($date . ' ' . $checkIn);
                $outDt = Carbon::parse($date . ' ' . $checkOut);
                $workedMinutes = max(0, (int) $inDt->diffInMinutes($outDt));
                
                $schedule = $employee->workSchedule;
                if ($schedule) {
                    if ($schedule->break_minutes && $workedMinutes > $schedule->break_minutes) {
                        $workedMinutes -= $schedule->break_minutes;
                    }
                    if ($schedule->start_time) {
                        $startDt = Carbon::parse($date . ' ' . $schedule->start_time)->addMinutes($schedule->grace_minutes ?? 0);
                        if ($inDt->greaterThan($startDt)) {
                            $lateMinutes = (int) Carbon::parse($date . ' ' . $schedule->start_time)->diffInMinutes($inDt);
                        }
                    }
                    if ($schedule->end_time) {
                        $endDt = Carbon::parse($date . ' ' . $schedule->end_time);
                        if ($outDt->lessThan($endDt)) {
                            $earlyMinutes = (int) $outDt->diffInMinutes($endDt);
                        } elseif ($outDt->greaterThan($endDt)) {
                            $overtimeMinutes = (int) $endDt->diffInMinutes($outDt);
                        }
                    }
                }
            }

            return AttendanceRecord::updateOrCreate(
                [
                    'employee_id' => $data['employee_id'],
                    'attendance_date' => $date,
                ],
                [
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'worked_minutes' => $workedMinutes,
                    'late_minutes' => $lateMinutes,
                    'early_leave_minutes' => $earlyMinutes,
                    'overtime_minutes' => $overtimeMinutes,
                    'status' => $data['status'] ?? ($lateMinutes > 0 ? 'late' : 'present'),
                    'source' => $data['source'] ?? 'manual',
                    'notes' => $data['notes'] ?? null,
                    'approved_by' => $approvedBy,
                ]
            );
        });
    }
}
