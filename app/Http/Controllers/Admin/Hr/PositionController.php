<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PositionController extends Controller
{
    public function index(Request $request): View
    {
        $query = Position::with('department')->withCount('employees');

        if ($deptId = $request->input('department_id')) {
            $query->where('department_id', $deptId);
        }

        $positions = $query->latest()->paginate(20)->withQueryString();
        $departments = Department::where('is_active', true)->get();

        return view('admin.hr.organization.positions', compact('positions', 'departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'name_en' => ['required', 'string', 'max:150'],
            'name_ar' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:30', 'unique:positions,code'],
            'level' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        Position::create($validated);

        return back()->with('success', __('hr.position_created_successfully', ['default' => 'Position created successfully.']));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $position = Position::findOrFail($id);

        $validated = $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'name_en' => ['required', 'string', 'max:150'],
            'name_ar' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:30', 'unique:positions,code,' . $position->id],
            'level' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $position->update($validated);

        return back()->with('success', __('hr.position_updated_successfully', ['default' => 'Position updated successfully.']));
    }

    public function destroy(int $id): RedirectResponse
    {
        $position = Position::findOrFail($id);
        if ($position->employees()->count() > 0) {
            return back()->withErrors(['position' => __('hr.cannot_delete_position_with_employees', ['default' => 'Cannot delete position assigned to employees.'])]);
        }

        $position->delete();

        return back()->with('success', __('hr.position_deleted_successfully', ['default' => 'Position deleted successfully.']));
    }
}
