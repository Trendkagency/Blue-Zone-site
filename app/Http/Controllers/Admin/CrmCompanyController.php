<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCrmCompanyRequest;
use App\Models\City;
use App\Models\Country;
use App\Models\CrmCompany;
use App\Models\User;
use Illuminate\Http\Request;

class CrmCompanyController extends Controller
{
    /**
     * Display B2B companies (clinics, distributors, pharmacies).
     */
    public function index(Request $request)
    {
        $query = CrmCompany::with(['owner:id,name,avatar', 'country:id,name_en,name_ar'])
            ->withCount(['leads', 'opportunities', 'customers']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('industry', 'like', "%{$search}%");
            });
        }
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $companies = $query->latest()->paginate(15)->withQueryString();

        return view('admin.crm.companies.index', compact('companies'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $countries = Country::where('is_active', true)->orderBy('name_en')->get();
        $owners = User::where('status', 'active')->select('id', 'name')->get();

        return view('admin.crm.companies.create', compact('countries', 'owners'));
    }

    /**
     * Store new company.
     */
    public function store(StoreCrmCompanyRequest $request)
    {
        $company = CrmCompany::create($request->validated());

        return redirect()->route('admin.crm.companies.show', $company->id)
            ->with('success', __('crm.companies.created_successfully'));
    }

    /**
     * Show company profile with linked leads, opportunities, and activities.
     */
    public function show(int $id)
    {
        $company = CrmCompany::with([
            'country',
            'city',
            'owner',
            'leads.owner',
            'opportunities.stage',
            'customers',
            'activities.assignee',
            'notesList.user',
        ])->findOrFail($id);

        return view('admin.crm.companies.show', compact('company'));
    }

    /**
     * Show edit form.
     */
    public function edit(int $id)
    {
        $company = CrmCompany::findOrFail($id);
        $countries = Country::where('is_active', true)->orderBy('name_en')->get();
        $cities = $company->country_id ? City::where('country_id', $company->country_id)->get() : collect();
        $owners = User::where('status', 'active')->select('id', 'name')->get();

        return view('admin.crm.companies.edit', compact('company', 'countries', 'cities', 'owners'));
    }

    /**
     * Update company.
     */
    public function update(StoreCrmCompanyRequest $request, int $id)
    {
        $company = CrmCompany::findOrFail($id);
        $company->update($request->validated());

        return redirect()->route('admin.crm.companies.show', $company->id)
            ->with('success', __('crm.companies.updated_successfully'));
    }

    /**
     * Soft delete company.
     */
    public function destroy(int $id)
    {
        $company = CrmCompany::findOrFail($id);
        $company->delete();

        return redirect()->route('admin.crm.companies.index')
            ->with('success', __('crm.companies.deleted_successfully'));
    }
}
