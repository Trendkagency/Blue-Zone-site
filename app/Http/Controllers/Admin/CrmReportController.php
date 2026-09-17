<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrmCampaign;
use App\Models\CrmLead;
use App\Models\CrmOpportunity;
use App\Models\Customer;
use App\Models\User;
use App\Services\CrmReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CrmReportController extends Controller
{
    public function __construct(
        protected CrmReportService $reportService
    ) {}

    /**
     * Display comprehensive CRM Intelligence & Reports.
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        // 1. Leads Funnel
        $leadsReport = $this->reportService->getLeadsReport($startDate, $endDate);

        // 2. Opportunities Report
        $oppsReport = $this->reportService->getOpportunitiesReport($startDate, $endDate);

        // 3. Sales Owner Performance
        $ownersReport = User::where('status', 'active')
            ->withCount(['crmAssignedLeads', 'crmAssignedOpportunities'])
            ->get()
            ->map(function ($owner) {
                $wonDeals = CrmOpportunity::where('owner_id', $owner->id)->where('status', 'won')->count();
                $wonRevenue = (float) CrmOpportunity::where('owner_id', $owner->id)->where('status', 'won')->sum('value');
                return [
                    'owner' => $owner,
                    'leads_count' => $owner->crm_assigned_leads_count,
                    'opps_count' => $owner->crm_assigned_opportunities_count,
                    'won_deals' => $wonDeals,
                    'won_revenue' => $wonRevenue,
                ];
            });

        // 4. Marketing Campaigns Performance
        $campaignsReport = CrmCampaign::withCount(['leads', 'opportunities'])
            ->get()
            ->map(function ($camp) {
                return [
                    'campaign' => $camp,
                    'leads' => $camp->leads_count,
                    'revenue' => (float) $camp->revenue_generated,
                    'budget' => (float) $camp->budget,
                    'roi' => $camp->roi_percentage,
                ];
            });

        return view('admin.crm.reports.index', compact(
            'startDate',
            'endDate',
            'leadsReport',
            'oppsReport',
            'ownersReport',
            'campaignsReport'
        ));
    }
}
