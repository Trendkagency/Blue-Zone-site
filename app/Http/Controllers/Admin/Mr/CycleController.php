<?php

namespace App\Http\Controllers\Admin\Mr;

use App\Http\Controllers\Controller;
use App\Models\Mr\VisitCycle;
use Illuminate\Http\Request;

class CycleController extends Controller
{
    public function index()
    {
        $cycles = VisitCycle::withCount(['assignments', 'visits'])
            ->latest('start_date')
            ->get();

        return view('admin.mr.cycles.index', compact('cycles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:mr_visit_cycles,code',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:active,upcoming,closed',
            'notes' => 'nullable|string',
        ]);

        // If setting this to active, optionally make others non-active if desired
        if ($validated['status'] === 'active') {
            VisitCycle::where('status', 'active')->update(['status' => 'closed']);
        }

        VisitCycle::create($validated);

        return redirect()->route('admin.mr.cycles.index')
            ->with('success', __('admin.mr.cycle_created_successfully'));
    }

    public function update(Request $request, int $id)
    {
        $cycle = VisitCycle::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:mr_visit_cycles,code,' . $cycle->id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:active,upcoming,closed',
            'notes' => 'nullable|string',
        ]);

        if ($validated['status'] === 'active') {
            VisitCycle::where('id', '!=', $cycle->id)
                ->where('status', 'active')
                ->update(['status' => 'closed']);
        }

        $cycle->update($validated);

        return redirect()->route('admin.mr.cycles.index')
            ->with('success', __('admin.mr.cycle_updated_successfully'));
    }

    public function destroy(int $id)
    {
        $cycle = VisitCycle::withCount('visits')->findOrFail($id);

        if ($cycle->visits_count > 0) {
            return redirect()->route('admin.mr.cycles.index')
                ->with('error', __('admin.mr.cannot_delete_cycle_with_visits'));
        }

        $cycle->delete();

        return redirect()->route('admin.mr.cycles.index')
            ->with('success', __('admin.mr.cycle_deleted_successfully'));
    }
}
