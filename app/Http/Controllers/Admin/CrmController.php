<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrmActivity;
use App\Models\CrmCampaign;
use App\Models\CrmLead;
use App\Models\CrmOpportunity;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CrmController extends Controller
{
    /**
     * Display the CRM Executive Dashboard.
     */
    public function index(Request $request)
    {
        // 1. Leads KPI Aggregates
        $leadStats = CrmLead::selectRaw("
            COUNT(*) as total_leads,
            SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new_leads,
            SUM(CASE WHEN status = 'contacted' THEN 1 ELSE 0 END) as contacted_leads,
            SUM(CASE WHEN status = 'qualified' THEN 1 ELSE 0 END) as qualified_leads,
            SUM(CASE WHEN status = 'converted' THEN 1 ELSE 0 END) as converted_leads,
            SUM(CASE WHEN status = 'lost' THEN 1 ELSE 0 END) as lost_leads,
            SUM(estimated_value) as total_estimated_value
        ")->first();

        $conversionRate = $leadStats->total_leads > 0
            ? round(($leadStats->converted_leads / $leadStats->total_leads) * 100, 1)
            : 0.0;

        // 2. Opportunities & Pipeline KPI Aggregates
        $oppStats = CrmOpportunity::selectRaw("
            COUNT(*) as total_opps,
            SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open_opps,
            SUM(CASE WHEN status = 'open' THEN value ELSE 0 END) as pipeline_value,
            SUM(CASE WHEN status = 'open' THEN (value * probability / 100) ELSE 0 END) as weighted_value,
            SUM(CASE WHEN status = 'won' THEN 1 ELSE 0 END) as won_opps,
            SUM(CASE WHEN status = 'won' THEN value ELSE 0 END) as won_value,
            SUM(CASE WHEN status = 'lost' THEN 1 ELSE 0 END) as lost_opps
        ")->first();

        $closedDeals = ($oppStats->won_opps ?? 0) + ($oppStats->lost_opps ?? 0);
        $winRate = $closedDeals > 0
            ? round((($oppStats->won_opps ?? 0) / $closedDeals) * 100, 1)
            : 0.0;

        // 3. Activities & Overdue Tasks
        $todayTasks = CrmActivity::dueToday()
            ->with(['assignee:id,name,avatar', 'lead:id,full_name,phone', 'customer:id,name,phone'])
            ->take(5)
            ->get();

        $overdueCount = CrmActivity::overdue()->count();
        $pendingCount = CrmActivity::pending()->count();

        // 4. Customer Retention Health
        $totalCustomers = Customer::count();
        $repeatCustomers = Customer::where('total_orders', '>', 1)->count();
        $vipCustomers = Customer::where('total_spent', '>=', 5000)->count();

        // At-risk: customers with delivered orders but none in last 90 days
        $atRiskCount = Customer::whereHas('orders', function ($q) {
            $q->where('status', 'delivered');
        })->whereDoesntHave('orders', function ($q) {
            $q->where('status', 'delivered')->where('created_at', '>=', now()->subDays(90));
        })->count();

        // 5. Recent Leads
        $recentLeads = CrmLead::with(['source', 'owner:id,name,avatar'])
            ->latest()
            ->take(6)
            ->get();

        // 6. Top Lead Sources
        $topSources = CrmLead::leftJoin('crm_lead_sources', 'crm_leads.source_id', '=', 'crm_lead_sources.id')
            ->select('crm_lead_sources.name_en', 'crm_lead_sources.name_ar', 'crm_lead_sources.color', DB::raw('COUNT(crm_leads.id) as count'))
            ->groupBy('crm_lead_sources.id', 'crm_lead_sources.name_en', 'crm_lead_sources.name_ar', 'crm_lead_sources.color')
            ->orderByDesc('count')
            ->take(5)
            ->get();

        // 7. Top Campaigns with Attribution
        $topCampaigns = CrmCampaign::active()
            ->orderByDesc('revenue_generated')
            ->take(4)
            ->get();

        return view('admin.crm.dashboard', compact(
            'leadStats',
            'conversionRate',
            'oppStats',
            'winRate',
            'todayTasks',
            'overdueCount',
            'pendingCount',
            'totalCustomers',
            'repeatCustomers',
            'vipCustomers',
            'atRiskCount',
            'recentLeads',
            'topSources',
            'topCampaigns'
        ));
    }
}
