<?php

namespace App\Http\Controllers\Admin\Mr;

use App\Http\Controllers\Controller;
use App\Models\Mr\Contact;
use App\Models\Mr\ContactAssignment;
use App\Models\Mr\VisitCycle;
use App\Models\User;
use App\Services\Mr\CrmAssignmentService;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    protected CrmAssignmentService $assignmentService;

    public function __construct(CrmAssignmentService $assignmentService)
    {
        $this->assignmentService = $assignmentService;
    }

    public function index(Request $request)
    {
        $currentUser = auth()->user();
        $isManager = $currentUser ? $currentUser->canManageAllMr() : false;
        $isRep = $currentUser ? ($currentUser->isMedicalRep() && !$isManager) : false;

        $cycles = VisitCycle::latest('start_date')->get();
        $selectedCycleId = $request->integer('cycle_id') ?: ($cycles->firstWhere('status', 'active')?->id ?? $cycles->first()?->id);

        $query = ContactAssignment::with([
            'contact.specialty',
            'contact.classification',
            'contact.city',
            'representative',
            'cycle',
        ]);

        if ($selectedCycleId) {
            $query->where('cycle_id', $selectedCycleId);
        }

        if ($isRep) {
            $query->where('mr_id', $currentUser->id);
        } elseif ($request->filled('mr_id')) {
            $query->where('mr_id', $request->integer('mr_id'));
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->whereHas('contact', function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('hospital_clinic_name', 'like', "%{$s}%");
            });
        }

        if ($request->has('export')) {
            return $this->exportAssignments($query->get(), $request, $cycles);
        }

        $assignments = $query->latest()->paginate(15)->withQueryString();

        if ($isRep) {
            $medicalReps = User::where('id', $currentUser->id)->with(['area', 'city'])->select('id', 'name', 'country_id', 'city_id', 'area_id')->get();
        } else {
            $medicalReps = User::whereHas('role', function ($q) {
                $q->where('name', 'mr');
            })->orWhere('role_id', 2)->with(['area', 'city'])->select('id', 'name', 'country_id', 'city_id', 'area_id')->get();
        }

        $availableDoctors = Contact::where('is_active', true)
            ->with(['specialty', 'classification'])
            ->select('id', 'name', 'code', 'hospital_clinic_name', 'specialty_id', 'classification_id')
            ->orderBy('name')
            ->get();

        return view('admin.mr.assignments.index', compact('assignments', 'cycles', 'selectedCycleId', 'medicalReps', 'availableDoctors', 'isRep', 'isManager', 'currentUser'));
    }

    public function store(Request $request)
    {
        $currentUser = auth()->user();
        if ($currentUser && $currentUser->isMedicalRep() && !$currentUser->canManageAllMr()) {
            abort(403, 'Unauthorized. Medical representatives cannot manage doctor assignments.');
        }

        $validated = $request->validate([
            'cycle_id' => 'required|exists:mr_visit_cycles,id',
            'mr_id' => 'required|exists:users,id',
            'contact_ids' => 'required|array',
            'contact_ids.*' => 'exists:mr_contacts,id',
            'auto_schedule' => 'nullable|boolean',
            'schedule_cadence' => 'nullable|string|in:weekly,monthly,daily',
            'schedule_start_date' => 'nullable|date',
            'schedule_today' => 'nullable|boolean',
            'daily_all_days' => 'nullable|boolean',
            'daily_count' => 'nullable|integer|min:1|max:60',
            'schedule_time' => 'nullable|string',
            'schedule_notes' => 'nullable|string',
        ]);

        $autoSchedule = $request->boolean('auto_schedule');
        $scheduleOptions = [
            'cadence' => $request->input('schedule_cadence', 'weekly'),
            'start_date' => $request->input('schedule_start_date', now()->toDateString()),
            'schedule_today' => $request->boolean('schedule_today', true),
            'daily_all_days' => $request->boolean('daily_all_days', false),
            'daily_count' => $request->input('daily_count'),
            'time' => $request->input('schedule_time', '09:30'),
            'notes' => $request->input('schedule_notes', 'Assigned visit'),
        ];

        $result = $this->assignmentService->bulkAssignContacts(
            (int) $validated['cycle_id'],
            (int) $validated['mr_id'],
            $validated['contact_ids'],
            $autoSchedule,
            $scheduleOptions
        );

        $assignedCount = is_array($result) ? ($result['assigned_count'] ?? 0) : (int) $result;
        $scheduledCount = is_array($result) ? ($result['scheduled_count'] ?? 0) : 0;

        $msg = __('admin.mr.assigned_contacts_count_successfully', ['count' => $assignedCount]);
        if ($scheduledCount > 0) {
            $msg .= ' ' . (app()->getLocale() === 'ar' 
                ? "وتمت جدولة {$scheduledCount} زيارة تلقائياً في جدول المندوب (تشمل زيارات اليوم)." 
                : "and {$scheduledCount} visit(s) were scheduled on the rep's agenda (including today).");
        }

        return redirect()->route('admin.mr.assignments.index', ['cycle_id' => $validated['cycle_id']])
            ->with('success', $msg);
    }

    public function destroy(int $id)
    {
        $currentUser = auth()->user();
        if ($currentUser && $currentUser->isMedicalRep() && !$currentUser->canManageAllMr()) {
            abort(403, 'Unauthorized. Medical representatives cannot remove assignments.');
        }

        $assignment = ContactAssignment::findOrFail($id);
        $cycleId = $assignment->cycle_id;
        $assignment->delete();

        return redirect()->route('admin.mr.assignments.index', ['cycle_id' => $cycleId])
            ->with('success', __('admin.mr.assignment_removed_successfully'));
    }

    protected function exportAssignments($assignments, Request $request, $cycles)
    {
        $exporter = app(\App\Services\Mr\Export\MrTableExcelExporter::class);

        $selectedCycleId = $request->integer('cycle_id');
        $selectedCycle = $cycles->firstWhere('id', $selectedCycleId);
        $repUser = $request->filled('mr_id') ? User::find($request->mr_id) : null;

        $metadata = [
            'Visit Cycle' => $selectedCycle ? $selectedCycle->name : 'All Cycles',
            'Medical Rep' => $repUser ? $repUser->name : 'All Representatives',
            'Doctor Search' => $request->search ?: 'All Doctors',
            'Total Assignments' => $assignments->count(),
        ];

        $totalCount = $assignments->count();
        $targetMet = $assignments->filter(fn($a) => $a->target_visits > 0 && $a->visits_done >= $a->target_visits)->count();
        $behind = $assignments->filter(fn($a) => $a->visits_done > 0 && $a->visits_done < $a->target_visits)->count();
        $unvisited = $assignments->where('visits_done', 0)->count();
        $totalVisitsDone = $assignments->sum('visits_done');

        $kpiCards = [
            ['label' => 'Total Assignments', 'val' => (string)$totalCount, 'bg' => 'F1F5F9', 'fg' => '0F172A', 'border' => 'CBD5E1'],
            ['label' => 'Target Completed', 'val' => (string)$targetMet, 'bg' => 'DCFCE7', 'fg' => '15803D', 'border' => '86EFAC'],
            ['label' => 'Behind Frequency', 'val' => (string)$behind, 'bg' => 'FEF9C3', 'fg' => 'A16207', 'border' => 'FDE047'],
            ['label' => 'Unvisited (0 Visits)', 'val' => (string)$unvisited, 'bg' => 'FEE2E2', 'fg' => 'B91C1C', 'border' => 'FCA5A5'],
            ['label' => 'Visits Done', 'val' => (string)$totalVisitsDone, 'bg' => 'E0F2FE', 'fg' => '0369A1', 'border' => 'BAE6FD'],
        ];

        $columns = [
            ['key' => fn($a) => '#' . $a->id, 'header' => 'Assign ID', 'width' => 12, 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ['key' => fn($a) => $a->cycle?->name ?? '—', 'header' => 'Visit Cycle', 'width' => 20, 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT],
            ['key' => fn($a) => $a->representative?->name ?? 'Rep #' . $a->mr_id, 'header' => 'Medical Rep', 'width' => 22, 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT],
            ['key' => fn($a) => $a->contact?->code ?? 'N/A', 'header' => 'Doctor Code', 'width' => 14, 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ['key' => fn($a) => $a->contact?->name ?? 'Doctor #' . $a->contact_id, 'header' => 'Doctor / Contact Name', 'width' => 25, 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT],
            ['key' => fn($a) => $a->contact?->specialty?->name ?? 'General', 'header' => 'Specialty', 'width' => 16, 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            [
                'key' => fn($a) => $a->contact?->classification?->code ?? 'C',
                'header' => 'Class',
                'width' => 10,
                'type' => 'badge',
                'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'badgeColors' => fn($val) => match (strtoupper((string)$val)) {
                    'A+' => ['bg' => 'FEF3C7', 'fg' => '92400E'],
                    'A' => ['bg' => 'E0F2FE', 'fg' => '0369A1'],
                    'B' => ['bg' => 'F1F5F9', 'fg' => '334155'],
                    default => ['bg' => 'F8FAFC', 'fg' => '64748B'],
                }
            ],
            ['key' => fn($a) => ($a->contact?->region ? $a->contact->region . ', ' : '') . ($a->contact?->city?->name_en ?? 'N/A'), 'header' => 'Region / City', 'width' => 20, 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT],
            ['key' => fn($a) => (int)$a->target_visits, 'header' => 'Target Visits', 'width' => 14, 'type' => 'number', 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ['key' => fn($a) => (int)$a->visits_done, 'header' => 'Visits Done', 'width' => 14, 'type' => 'number', 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            [
                'key' => fn($a) => $a->target_visits > 0 ? round(($a->visits_done / $a->target_visits) * 100, 1) : 0,
                'header' => 'Compliance %',
                'width' => 15,
                'type' => 'percent',
                'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ],
            [
                'key' => function ($a) {
                    if ($a->visits_done === 0) return 'Unvisited';
                    if ($a->target_visits > 0 && $a->visits_done >= $a->target_visits) return 'Target Met';
                    return 'Behind';
                },
                'header' => 'Coverage Status',
                'width' => 16,
                'type' => 'badge',
                'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'badgeColors' => fn($val) => match ($val) {
                    'Target Met' => ['bg' => 'DCFCE7', 'fg' => '15803D'],
                    'Behind' => ['bg' => 'FEF9C3', 'fg' => 'A16207'],
                    default => ['bg' => 'FEE2E2', 'fg' => 'B91C1C'],
                }
            ],
        ];

        return $exporter->export(
            'Doctor Portfolio Assignments & Allocation Audit',
            $metadata,
            $kpiCards,
            $columns,
            $assignments,
            'doctor-assignments-' . date('Y-m-d') . '.xlsx',
            [
                'col' => 'A',
                'mergeTo' => 'H',
                'label' => 'PORTFOLIO TOTALS',
                'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
            ]
        );
    }
}
