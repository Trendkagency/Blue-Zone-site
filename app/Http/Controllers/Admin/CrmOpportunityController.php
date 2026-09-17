<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCrmOpportunityRequest;
use App\Http\Requests\Admin\UpdateCrmOpportunityRequest;
use App\Models\CrmCampaign;
use App\Models\CrmCompany;
use App\Models\CrmLead;
use App\Models\CrmLeadSource;
use App\Models\CrmOpportunity;
use App\Models\CrmPipeline;
use App\Models\CrmPipelineStage;
use App\Models\Customer;
use App\Models\User;
use App\Services\CrmOpportunityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CrmOpportunityController extends Controller
{
    public function __construct(
        protected CrmOpportunityService $opportunityService
    ) {}

    /**
     * Display listing or Kanban board.
     */
    public function index(Request $request)
    {
        $view = $request->input('view', 'kanban'); // Default to Kanban view
        $pipelines = CrmPipeline::active()->get();
        $selectedPipelineId = (int) $request->input('pipeline_id', $pipelines->first()?->id ?? 1);

        if ($view === 'kanban') {
            $board = $this->opportunityService->getKanbanBoard($selectedPipelineId);
            return view('admin.crm.opportunities.index', [
                'view' => 'kanban',
                'pipelines' => $pipelines,
                'selectedPipelineId' => $selectedPipelineId,
                'board' => $board,
            ]);
        }

        // Table view
        $query = CrmOpportunity::where('pipeline_id', $selectedPipelineId)
            ->with(['stage', 'owner:id,name,avatar', 'customer:id,name', 'company:id,name']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('opportunity_number', 'like', "%{$search}%");
            });
        }
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($stageId = $request->input('stage_id')) {
            $query->where('stage_id', $stageId);
        }

        $opportunities = $query->latest()->paginate(15)->withQueryString();
        $stages = CrmPipelineStage::where('pipeline_id', $selectedPipelineId)->orderBy('sort_order')->get();

        return view('admin.crm.opportunities.index', [
            'view' => 'table',
            'pipelines' => $pipelines,
            'selectedPipelineId' => $selectedPipelineId,
            'opportunities' => $opportunities,
            'stages' => $stages,
        ]);
    }

    /**
     * Show creation form.
     */
    public function create(Request $request)
    {
        $pipelines = CrmPipeline::active()->with('stages')->get();
        $owners = User::where('status', 'active')->select('id', 'name')->get();
        $sources = CrmLeadSource::active()->get();
        $campaigns = CrmCampaign::active()->get();
        $customers = Customer::select('id', 'name', 'email', 'phone')->take(50)->get();
        $companies = CrmCompany::active()->get();

        $selectedLeadId = $request->input('lead_id');
        $lead = $selectedLeadId ? CrmLead::find($selectedLeadId) : null;

        return view('admin.crm.opportunities.create', compact('pipelines', 'owners', 'sources', 'campaigns', 'customers', 'companies', 'lead'));
    }

    /**
     * Store new opportunity.
     */
    public function store(StoreCrmOpportunityRequest $request)
    {
        $opp = $this->opportunityService->createOpportunity($request->validated(), auth()->id());

        return redirect()->route('admin.crm.opportunities.show', $opp->id)
            ->with('success', __('crm.opportunities.created_successfully', ['number' => $opp->opportunity_number]));
    }

    /**
     * Display opportunity details.
     */
    public function show(int $id)
    {
        $opportunity = CrmOpportunity::with([
            'pipeline.stages',
            'stage',
            'owner',
            'customer',
            'company',
            'lead',
            'source',
            'campaign',
            'activities.assignee',
            'notesList.user',
            'tags',
        ])->findOrFail($id);

        return view('admin.crm.opportunities.show', compact('opportunity'));
    }

    /**
     * AJAX endpoint to move opportunity stage (supports Kanban drag-and-drop).
     */
    public function updateStage(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'stage_id' => ['required', 'exists:crm_pipeline_stages,id'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $opportunity = CrmOpportunity::findOrFail($id);

        try {
            $updated = $this->opportunityService->updateStage(
                $opportunity,
                (int) $request->stage_id,
                auth()->id(),
                $request->reason
            );

            return response()->json([
                'success' => true,
                'message' => __('crm.opportunities.stage_updated_success'),
                'opportunity' => $updated,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Soft delete opportunity.
     */
    public function destroy(int $id)
    {
        $opp = CrmOpportunity::findOrFail($id);
        $opp->delete();

        return redirect()->route('admin.crm.opportunities.index')
            ->with('success', __('crm.opportunities.deleted_successfully'));
    }
}
