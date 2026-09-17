<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCrmCampaignRequest;
use App\Models\CrmCampaign;
use App\Models\User;
use Illuminate\Http\Request;

class CrmCampaignController extends Controller
{
    /**
     * Display marketing campaigns.
     */
    public function index(Request $request)
    {
        $query = CrmCampaign::with(['owner:id,name'])
            ->withCount(['leads', 'opportunities']);

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('utm_campaign', 'like', "%{$search}%");
        }
        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $campaigns = $query->latest()->paginate(15)->withQueryString();

        return view('admin.crm.campaigns.index', compact('campaigns'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $owners = User::where('status', 'active')->select('id', 'name')->get();
        return view('admin.crm.campaigns.create', compact('owners'));
    }

    /**
     * Store new campaign.
     */
    public function store(StoreCrmCampaignRequest $request)
    {
        $campaign = CrmCampaign::create($request->validated());

        return redirect()->route('admin.crm.campaigns.show', $campaign->id)
            ->with('success', __('crm.campaigns.created_successfully'));
    }

    /**
     * Show campaign details and attribution analysis.
     */
    public function show(int $id)
    {
        $campaign = CrmCampaign::with([
            'owner',
            'leads.owner',
            'opportunities.stage',
        ])->findOrFail($id);

        return view('admin.crm.campaigns.show', compact('campaign'));
    }

    /**
     * Show edit form.
     */
    public function edit(int $id)
    {
        $campaign = CrmCampaign::findOrFail($id);
        $owners = User::where('status', 'active')->select('id', 'name')->get();

        return view('admin.crm.campaigns.edit', compact('campaign', 'owners'));
    }

    /**
     * Update campaign.
     */
    public function update(StoreCrmCampaignRequest $request, int $id)
    {
        $campaign = CrmCampaign::findOrFail($id);
        $campaign->update($request->validated());

        return redirect()->route('admin.crm.campaigns.show', $campaign->id)
            ->with('success', __('crm.campaigns.updated_successfully'));
    }

    /**
     * Soft delete campaign.
     */
    public function destroy(int $id)
    {
        $campaign = CrmCampaign::findOrFail($id);
        $campaign->delete();

        return redirect()->route('admin.crm.campaigns.index')
            ->with('success', __('crm.campaigns.deleted_successfully'));
    }
}
