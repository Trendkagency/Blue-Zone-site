<?php

namespace App\Http\Controllers\Admin\Mr;

use App\Http\Controllers\Controller;
use App\Models\Mr\VisitCycle;
use Illuminate\Http\Request;

class CycleController extends Controller
{
    public function index(Request $request)
    {
        $cycles = VisitCycle::withCount(['assignments', 'visits'])
            ->latest('start_date')
            ->get();

        if ($request->has('export')) {
            $exporter = app(\App\Services\Mr\Export\MrTableExcelExporter::class);
            $metadata = ['Total Cycles' => $cycles->count()];
            $activeCount = $cycles->where('status', 'active')->count();
            $totalAssigns = $cycles->sum('assignments_count');
            $totalVisits = $cycles->sum('visits_count');

            $kpiCards = [
                ['label' => 'Total Cycles', 'val' => (string)$cycles->count(), 'bg' => 'F1F5F9', 'fg' => '0F172A', 'border' => 'CBD5E1'],
                ['label' => 'Active Cycles', 'val' => (string)$activeCount, 'bg' => 'DCFCE7', 'fg' => '15803D', 'border' => '86EFAC'],
                ['label' => 'Total Assignments', 'val' => (string)$totalAssigns, 'bg' => 'E0F2FE', 'fg' => '0369A1', 'border' => 'BAE6FD'],
                ['label' => 'Executed Visits', 'val' => (string)$totalVisits, 'bg' => 'EDE9FE', 'fg' => '6D28D9', 'border' => 'C4B5FD'],
            ];

            $columns = [
                ['key' => fn($c) => $c->code, 'header' => 'Cycle Code', 'width' => 16, 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                ['key' => fn($c) => $c->name, 'header' => 'Cycle Name', 'width' => 26, 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT],
                ['key' => fn($c) => $c->start_date?->format('Y-m-d'), 'header' => 'Start Date', 'width' => 14, 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                ['key' => fn($c) => $c->end_date?->format('Y-m-d'), 'header' => 'End Date', 'width' => 14, 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                [
                    'key' => fn($c) => ucfirst($c->status),
                    'header' => 'Status',
                    'width' => 14,
                    'type' => 'badge',
                    'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'badgeColors' => fn($val) => match (strtolower((string)$val)) {
                        'active' => ['bg' => 'DCFCE7', 'fg' => '15803D'],
                        'upcoming' => ['bg' => 'E0F2FE', 'fg' => '0369A1'],
                        default => ['bg' => 'F1F5F9', 'fg' => '64748B'],
                    }
                ],
                ['key' => fn($c) => (int)$c->assignments_count, 'header' => 'Assignments', 'width' => 14, 'type' => 'number', 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                ['key' => fn($c) => (int)$c->visits_count, 'header' => 'Visits Done', 'width' => 14, 'type' => 'number', 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                ['key' => fn($c) => $c->notes ?: '—', 'header' => 'Notes / Details', 'width' => 30, 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT],
            ];

            return $exporter->export(
                'Field Visit Cycles & Planning Periods',
                $metadata,
                $kpiCards,
                $columns,
                $cycles,
                'visit-cycles-' . date('Y-m-d') . '.xlsx'
            );
        }

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
