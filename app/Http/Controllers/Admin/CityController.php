<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function getCitiesByCountry(Request $request, int $countryId): JsonResponse
    {
        $country = Country::findOrFail($countryId);
        $cities = City::where('country_id', $countryId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name_en')
            ->get(['id', 'country_id', 'name_en', 'name_ar', 'shipping_cost', 'is_active']);

        return response()->json([
            'success' => true,
            'country' => [
                'id' => $country->id,
                'name_en' => $country->name_en,
                'name_ar' => $country->name_ar,
                'iso2' => $country->iso2,
                'phone_code' => $country->phone_code,
                'flag_emoji' => $country->flag_emoji,
                'currency_symbol' => $country->currency_symbol,
            ],
            'cities' => $cities,
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'state_or_province' => 'nullable|string|max:255',
            'shipping_cost' => 'nullable|numeric|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['shipping_cost'] = $validated['shipping_cost'] ?? 0.00;
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $city = City::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => app()->getLocale() === 'ar' ? 'تمت إضافة المدينة بنجاح' : 'City added successfully',
                'city' => $city->load('country'),
            ]);
        }

        return redirect()->route('admin.settings.geo.index', ['country_id' => $city->country_id])
            ->with('success', app()->getLocale() === 'ar' ? 'تمت إضافة المدينة بنجاح' : 'City added successfully');
    }

    public function update(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $city = City::findOrFail($id);

        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'state_or_province' => 'nullable|string|max:255',
            'shipping_cost' => 'nullable|numeric|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['shipping_cost'] = $validated['shipping_cost'] ?? 0.00;
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $city->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => app()->getLocale() === 'ar' ? 'تم تحديث بيانات المدينة بنجاح' : 'City updated successfully',
                'city' => $city->load('country'),
            ]);
        }

        return redirect()->route('admin.settings.geo.index', ['country_id' => $city->country_id])
            ->with('success', app()->getLocale() === 'ar' ? 'تم تحديث بيانات المدينة بنجاح' : 'City updated successfully');
    }

    public function toggleStatus(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $city = City::findOrFail($id);
        $city->is_active = !$city->is_active;
        $city->save();

        $msg = $city->is_active 
            ? (app()->getLocale() === 'ar' ? "تم تفعيل مدينة {$city->name_ar} بنجاح" : "City {$city->name_en} activated successfully")
            : (app()->getLocale() === 'ar' ? "تم تعطيل مدينة {$city->name_ar} بنجاح" : "City {$city->name_en} deactivated successfully");

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'is_active' => $city->is_active,
                'message' => $msg,
            ]);
        }

        return back()->with('status', $msg);
    }

    public function destroy(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $city = City::findOrFail($id);

        if ($city->locations()->count() > 0) {
            $msg = app()->getLocale() === 'ar'
                ? 'لا يمكن حذف هذه المدينة لوجود مستودعات ومواقع مرتبطة بها'
                : 'Cannot delete this city because facilities/locations are linked to it';
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->withErrors(['delete_error' => $msg]);
        }

        $countryId = $city->country_id;
        $city->delete();

        $msg = app()->getLocale() === 'ar' ? 'تم حذف المدينة بنجاح' : 'City deleted successfully';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('admin.settings.geo.index', ['country_id' => $countryId])->with('success', $msg);
    }
}
