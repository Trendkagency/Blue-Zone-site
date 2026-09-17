<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ConvertCrmLeadRequest;
use App\Http\Requests\Admin\StoreCrmLeadRequest;
use App\Http\Requests\Admin\UpdateCrmLeadRequest;
use App\Models\City;
use App\Models\Country;
use App\Models\CrmCampaign;
use App\Models\CrmCompany;
use App\Models\CrmLead;
use App\Models\CrmLeadSource;
use App\Models\CrmPipeline;
use App\Models\User;
use App\Services\CrmLeadService;
use App\Services\CrmReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CrmLeadController extends Controller
{
    public function __construct(
        protected CrmLeadService $leadService,
        protected CrmReportService $reportService
    ) {}

    /**
     * Display a listing of CRM leads with filters.
     */
    public function index(Request $request)
    {
        $query = CrmLead::with(['source', 'owner:id,name,avatar', 'country:id,name_en,name_ar']);

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('lead_number', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($sourceId = $request->input('source_id')) {
            $query->where('source_id', $sourceId);
        }
        if ($ownerId = $request->input('owner_id')) {
            $query->where('owner_id', $ownerId);
        }
        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }
        if ($request->input('overdue') === '1') {
            $query->overdueFollowUp();
        }

        $leads = $query->latest()->paginate(15)->withQueryString();

        $sources = CrmLeadSource::active()->get();
        $owners = User::where('status', 'active')->select('id', 'name')->get();

        return view('admin.crm.leads.index', compact('leads', 'sources', 'owners'));
    }

    /**
     * Show lead creation form.
     */
    public function create()
    {
        $sources = CrmLeadSource::active()->get();
        $campaigns = CrmCampaign::active()->get();
        $companies = CrmCompany::active()->get();
        $owners = User::where('status', 'active')->select('id', 'name')->get();
        $countries = Country::where('is_active', true)->orderBy('name_en')->get();

        return view('admin.crm.leads.create', compact('sources', 'campaigns', 'companies', 'owners', 'countries'));
    }

    /**
     * Store newly created lead.
     */
    public function store(StoreCrmLeadRequest $request)
    {
        $lead = $this->leadService->createLead($request->validated(), auth()->id());

        return redirect()->route('admin.crm.leads.show', $lead->id)
            ->with('success', __('crm.leads.created_successfully', ['number' => $lead->lead_number]));
    }

    /**
     * Display the specified lead details with timeline and duplicate detection.
     */
    public function show(int $id)
    {
        $lead = CrmLead::with([
            'source',
            'campaign',
            'company',
            'customer',
            'owner',
            'country',
            'city',
            'activities.assignee',
            'notesList.user',
            'tags',
        ])->findOrFail($id);

        // Check for duplicates
        $duplicates = $this->leadService->detectDuplicates(
            $lead->email,
            $lead->phone,
            $lead->full_name,
            $lead->id
        );

        $pipelines = CrmPipeline::active()->with('stages')->get();
        $owners = User::where('status', 'active')->select('id', 'name')->get();

        return view('admin.crm.leads.show', compact('lead', 'duplicates', 'pipelines', 'owners'));
    }

    /**
     * Show edit form.
     */
    public function edit(int $id)
    {
        $lead = CrmLead::findOrFail($id);
        $sources = CrmLeadSource::active()->get();
        $campaigns = CrmCampaign::active()->get();
        $companies = CrmCompany::active()->get();
        $owners = User::where('status', 'active')->select('id', 'name')->get();
        $countries = Country::where('is_active', true)->orderBy('name_en')->get();
        $cities = $lead->country_id ? City::where('country_id', $lead->country_id)->get() : collect();

        return view('admin.crm.leads.edit', compact('lead', 'sources', 'campaigns', 'companies', 'owners', 'countries', 'cities'));
    }

    /**
     * Update lead.
     */
    public function update(UpdateCrmLeadRequest $request, int $id)
    {
        $lead = CrmLead::findOrFail($id);
        $data = $request->validated();
        $data['full_name'] = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
        $data['phone_normalized'] = $this->leadService->normalizePhone($data['phone'] ?? null);
        $data['score'] = $this->leadService->calculateLeadScore($data);

        $lead->update($data);

        return redirect()->route('admin.crm.leads.show', $lead->id)
            ->with('success', __('crm.leads.updated_successfully'));
    }

    /**
     * Soft delete lead.
     */
    public function destroy(int $id)
    {
        $lead = CrmLead::findOrFail($id);
        $lead->delete();

        return redirect()->route('admin.crm.leads.index')
            ->with('success', __('crm.leads.deleted_successfully'));
    }

    /**
     * AJAX Endpoint: Check for duplicate leads/customers before submit.
     */
    public function checkDuplicates(Request $request): JsonResponse
    {
        $email = $request->query('email');
        $phone = $request->query('phone');
        $name = $request->query('name');
        $excludeId = $request->query('exclude_id') ? (int) $request->query('exclude_id') : null;

        $results = $this->leadService->detectDuplicates($email, $phone, $name, $excludeId);

        return response()->json($results);
    }

    /**
     * Convert Lead to Customer and Opportunity.
     */
    public function convert(ConvertCrmLeadRequest $request, int $id)
    {
        $lead = CrmLead::findOrFail($id);

        try {
            $results = $this->leadService->convertLead($lead, $request->validated(), auth()->id());

            return redirect()->route('admin.crm.leads.show', $lead->id)
                ->with('success', __('crm.leads.converted_successfully', ['customer' => $results['customer']->name]));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Bulk assign leads.
     */
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'lead_ids' => ['required', 'array'],
            'lead_ids.*' => ['exists:crm_leads,id'],
            'owner_id' => ['required', 'exists:users,id'],
        ]);

        $count = $this->leadService->bulkAssign($request->lead_ids, $request->owner_id, auth()->id());

        return back()->with('success', __('crm.leads.bulk_assigned_success', ['count' => $count]));
    }

    /**
     * Bulk update status.
     */
    public function bulkStatus(Request $request)
    {
        $request->validate([
            'lead_ids' => ['required', 'array'],
            'lead_ids.*' => ['exists:crm_leads,id'],
            'status' => ['required', 'in:new,contacted,qualified,unqualified,lost'],
        ]);

        $count = $this->leadService->bulkUpdateStatus($request->lead_ids, $request->status, auth()->id());

        return back()->with('success', __('crm.leads.bulk_status_success', ['count' => $count]));
    }

    /**
     * Export Leads to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        return $this->reportService->streamLeadsCsv($request->all());
    }
}
