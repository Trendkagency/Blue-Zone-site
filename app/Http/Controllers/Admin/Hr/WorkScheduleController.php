<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use App\Models\WorkSchedule;
use App\Models\WorkScheduleDay;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkScheduleController extends Controller
{
    public function index(): View
    {
        $schedules = WorkSchedule::with('days')->withCount('employees')->latest()->paginate(15);
        return view('admin.hr.organization.work_schedules', compact('schedules'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name_en' => ['required', 'string', 'max:150'],
            'name_ar' => ['required', 'string', 'max:150'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'break_minutes' => ['required', 'integer', 'min:0'],
            'grace_minutes' => ['required', 'integer', 'min:0'],
            'working_hours' => ['required', 'numeric', 'min:1'],
            'working_days' => ['array'],
        ]);

        $schedule = WorkSchedule::create($validated);

        $workingDays = $request->input('working_days', [0, 1, 2, 3, 4]);
        for ($day = 0; $day <= 6; $day++) {
            WorkScheduleDay::create([
                'work_schedule_id' => $schedule->id,
                'day_of_week' => $day,
                'is_working_day' => in_array($day, $workingDays),
            ]);
        }

        return back()->with('success', __('hr.schedule_created_successfully', ['default' => 'Work schedule created successfully.']));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $schedule = WorkSchedule::findOrFail($id);

        $validated = $request->validate([
            'name_en' => ['required', 'string', 'max:150'],
            'name_ar' => ['required', 'string', 'max:150'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'break_minutes' => ['required', 'integer', 'min:0'],
            'grace_minutes' => ['required', 'integer', 'min:0'],
            'working_hours' => ['required', 'numeric', 'min:1'],
            'working_days' => ['array'],
        ]);

        $schedule->update($validated);

        if ($request->has('working_days')) {
            $workingDays = $request->input('working_days', []);
            for ($day = 0; $day <= 6; $day++) {
                WorkScheduleDay::updateOrCreate(
                    ['work_schedule_id' => $schedule->id, 'day_of_week' => $day],
                    ['is_working_day' => in_array($day, $workingDays)]
                );
            }
        }

        return back()->with('success', __('hr.schedule_updated_successfully', ['default' => 'Work schedule updated successfully.']));
    }

    public function destroy(int $id): RedirectResponse
    {
        $schedule = WorkSchedule::findOrFail($id);
        if ($schedule->employees()->count() > 0) {
            return back()->withErrors(['schedule' => __('hr.cannot_delete_schedule_with_employees', ['default' => 'Cannot delete schedule assigned to employees.'])]);
        }

        $schedule->delete();

        return back()->with('success', __('hr.schedule_deleted_successfully', ['default' => 'Work schedule deleted successfully.']));
    }
}
