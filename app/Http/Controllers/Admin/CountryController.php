<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CountryController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $countries = Country::withCount(['cities', 'locations'])
            ->orderBy('sort_order')
            ->orderBy('name_en')
            ->get();

        $selectedCountryId = $request->query('country_id');
        $citiesQuery = City::with('country')->withCount('locations');
        if ($selectedCountryId) {
            $citiesQuery->where('country_id', $selectedCountryId);
        }
        $cities = $citiesQuery->orderBy('sort_order')->orderBy('name_en')->paginate(25);

        $totalLocations = Location::count();
        $activeCountriesCount = Country::where('is_active', true)->count();
        $totalCitiesCount = City::count();

        if ($request->wantsJson()) {
            return response()->json([
                'countries' => $countries,
                'cities' => $cities,
            ]);
        }

        return view('admin.settings.geo.index', compact(
            'countries',
            'cities',
            'totalLocations',
            'activeCountriesCount',
            'totalCitiesCount',
            'selectedCountryId'
        ));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'iso2' => 'required|string|size:2|unique:countries,iso2',
            'phone_code' => 'required|string|max:10',
            'currency_code' => 'nullable|string|max:10',
            'currency_symbol_en' => 'nullable|string|max:10',
            'currency_symbol_ar' => 'nullable|string|max:10',
            'flag_emoji' => 'nullable|string|max:10',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['iso2'] = strtoupper($validated['iso2']);
        $validated['phone_code'] = str_starts_with($validated['phone_code'], '+') ? $validated['phone_code'] : '+' . ltrim($validated['phone_code'], '+');
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $country = Country::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => app()->getLocale() === 'ar' ? 'تمت إضافة الدولة بنجاح' : 'Country added successfully',
                'country' => $country,
            ]);
        }

        return redirect()->route('admin.settings.geo.index')
            ->with('success', app()->getLocale() === 'ar' ? 'تمت إضافة الدولة بنجاح' : 'Country added successfully');
    }

    public function update(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $country = Country::findOrFail($id);

        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'iso2' => 'required|string|size:2|unique:countries,iso2,' . $country->id,
            'phone_code' => 'required|string|max:10',
            'currency_code' => 'nullable|string|max:10',
            'currency_symbol_en' => 'nullable|string|max:10',
            'currency_symbol_ar' => 'nullable|string|max:10',
            'flag_emoji' => 'nullable|string|max:10',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['iso2'] = strtoupper($validated['iso2']);
        $validated['phone_code'] = str_starts_with($validated['phone_code'], '+') ? $validated['phone_code'] : '+' . ltrim($validated['phone_code'], '+');
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $country->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => app()->getLocale() === 'ar' ? 'تم تحديث بيانات الدولة بنجاح' : 'Country updated successfully',
                'country' => $country,
            ]);
        }

        return redirect()->route('admin.settings.geo.index')
            ->with('success', app()->getLocale() === 'ar' ? 'تم تحديث بيانات الدولة بنجاح' : 'Country updated successfully');
    }

    public function toggleStatus(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $country = Country::findOrFail($id);
        $country->is_active = !$country->is_active;
        $country->save();

        $msg = $country->is_active 
            ? (app()->getLocale() === 'ar' ? "تم تفعيل الدولة {$country->name_ar} بنجاح" : "Country {$country->name_en} activated successfully")
            : (app()->getLocale() === 'ar' ? "تم تعطيل الدولة {$country->name_ar} بنجاح" : "Country {$country->name_en} deactivated successfully");

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'is_active' => $country->is_active,
                'message' => $msg,
            ]);
        }

        return back()->with('status', $msg);
    }

    public function destroy(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $country = Country::findOrFail($id);

        if ($country->locations()->count() > 0) {
            $msg = app()->getLocale() === 'ar'
                ? 'لا يمكن حذف هذه الدولة لوجود مستودعات ومواقع مرتبطة بها'
                : 'Cannot delete this country because facilities/locations are linked to it';
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->withErrors(['delete_error' => $msg]);
        }

        $country->delete();

        $msg = app()->getLocale() === 'ar' ? 'تم حذف الدولة بنجاح' : 'Country deleted successfully';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('admin.settings.geo.index')->with('success', $msg);
    }
}
