<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCrmActivityRequest;
use App\Models\CrmActivity;
use App\Models\CrmCompany;
use App\Models\CrmLead;
use App\Models\CrmOpportunity;
use App\Models\Customer;
use App\Models\User;
use App\Services\CrmActivityService;
use Illuminate\Http\Request;

class CrmActivityController extends Controller
{
    public function __construct(
        protected CrmActivityService $activityService
    ) {}

    /**
     * Display list of CRM activities, tasks, calls, and meetings.
     */
    public function index(Request $request)
    {
        $query = CrmActivity::with(['assignee:id,name,avatar', 'customer:id,name', 'lead:id,full_name,lead_number', 'opportunity:id,name']);

        if ($type = $request->input('type')) {
            $query->where('activity_type', $type);
        }
        if ($status = $request->input('status')) {
            if ($status === 'overdue') {
                $query->overdue();
            } else {
                $query->where('status', $status);
            }
        }
        if ($assigneeId = $request->input('assigned_to')) {
            $query->where('assigned_to', $assigneeId);
        }
        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }

        $activities = $query->orderBy('due_at', 'asc')->paginate(20)->withQueryString();
        $assignees = User::where('status', 'active')->select('id', 'name')->get();

        return view('admin.crm.activities.index', compact('activities', 'assignees'));
    }

    /**
     * Show activity creation form.
     */
    public function create(Request $request)
    {
        $assignees = User::where('status', 'active')->select('id', 'name')->get();
        $customers = Customer::select('id', 'name')->take(50)->get();
        $leads = CrmLead::select('id', 'full_name', 'lead_number')->take(50)->get();
        $opportunities = CrmOpportunity::select('id', 'name')->take(50)->get();
        $companies = CrmCompany::select('id', 'name')->get();

        return view('admin.crm.activities.create', compact('assignees', 'customers', 'leads', 'opportunities', 'companies'));
    }

    /**
     * Store new activity.
     */
    public function store(StoreCrmActivityRequest $request)
    {
        $this->activityService->createActivity($request->validated(), auth()->id());

        return redirect()->route('admin.crm.activities.index')
            ->with('success', __('crm.activities.created_successfully'));
    }

    /**
     * Mark activity as completed.
     */
    public function complete(Request $request, int $id)
    {
        $request->validate([
            'outcome' => ['nullable', 'string', 'max:500'],
        ]);

        $activity = CrmActivity::findOrFail($id);
        $this->activityService->completeActivity($activity, $request->outcome, auth()->id());

        return back()->with('success', __('crm.activities.completed_successfully'));
    }

    /**
     * Cancel an activity.
     */
    public function cancel(Request $request, int $id)
    {
        $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $activity = CrmActivity::findOrFail($id);
        $this->activityService->cancelActivity($activity, $request->reason, auth()->id());

        return back()->with('success', __('crm.activities.cancelled_successfully'));
    }

    /**
     * Delete an activity.
     */
    public function destroy(int $id)
    {
        $activity = CrmActivity::findOrFail($id);
        $activity->delete();

        return back()->with('success', __('crm.activities.deleted_successfully'));
    }
}
