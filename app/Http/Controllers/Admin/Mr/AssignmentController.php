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

        if ($request->filled('mr_id')) {
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

        $assignments = $query->latest()->paginate(15)->withQueryString();

        $medicalReps = User::whereHas('role', function ($q) {
            $q->where('name', 'mr');
        })->orWhere('role_id', 2)->select('id', 'name')->get();

        $availableDoctors = Contact::where('is_active', true)->select('id', 'name', 'code', 'hospital_clinic_name')->get();

        return view('admin.mr.assignments.index', compact('assignments', 'cycles', 'selectedCycleId', 'medicalReps', 'availableDoctors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cycle_id' => 'required|exists:mr_visit_cycles,id',
            'mr_id' => 'required|exists:users,id',
            'contact_ids' => 'required|array',
            'contact_ids.*' => 'exists:mr_contacts,id',
        ]);

        $count = $this->assignmentService->bulkAssignContacts(
            (int) $validated['cycle_id'],
            (int) $validated['mr_id'],
            $validated['contact_ids']
        );

        return redirect()->route('admin.mr.assignments.index', ['cycle_id' => $validated['cycle_id']])
            ->with('success', __('admin.mr.assigned_contacts_count_successfully', ['count' => $count]));
    }

    public function destroy(int $id)
    {
        $assignment = ContactAssignment::findOrFail($id);
        $cycleId = $assignment->cycle_id;
        $assignment->delete();

        return redirect()->route('admin.mr.assignments.index', ['cycle_id' => $cycleId])
            ->with('success', __('admin.mr.assignment_removed_successfully'));
    }
}
