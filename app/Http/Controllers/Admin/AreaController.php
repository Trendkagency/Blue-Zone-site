<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Services\Mr\AreaTerritoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AreaController extends Controller
{
    /**
     * Display a paginated listing of Areas with Country and City filters.
     */
    public function index(Request $request): View|JsonResponse
    {
        $countryId = $request->query('country_id');
        $cityId = $request->query('city_id');
        $search = $request->query('search');

        $query = Area::with(['country', 'city'])
            ->withCount(['medicalReps', 'contacts']);

        if ($countryId) {
            $query->where('country_id', $countryId);
        }

        if ($cityId) {
            $query->where('city_id', $cityId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name_en', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $areas = $query->orderBy('sort_order')->orderBy('name_en')->paginate(20)->withQueryString();

        $countries = Country::where('is_active', true)->orderBy('sort_order')->orderBy('name_en')->get();
        $cities = $countryId 
            ? City::where('country_id', $countryId)->where('is_active', true)->orderBy('name_en')->get() 
            : City::where('is_active', true)->orderBy('name_en')->get();

        $territoryService = AreaTerritoryService::getInstance();
        $stats = $territoryService->getTerritoryStats();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'areas' => $areas,
                'stats' => $stats,
            ]);
        }

        return view('admin.mr.areas.index', compact('areas', 'countries', 'cities', 'countryId', 'cityId', 'search', 'stats'));
    }

    /**
     * Store a newly created Area.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $city = City::findOrFail($validated['city_id']);
        if ((int)$city->country_id !== (int)$validated['country_id']) {
            return back()->withErrors(['city_id' => app()->getLocale() === 'ar' ? 'المدينة المختارة لا تنتمي إلى هذه الدولة' : 'Selected city does not belong to this country'])->withInput();
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $area = Area::create($validated);

        $msg = app()->getLocale() === 'ar' ? "تمت إضافة المنطقة [{$area->name_ar}] بنجاح" : "Area [{$area->name_en}] created successfully";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'area' => $area->load(['country', 'city']),
            ]);
        }

        return redirect()->route('admin.mr.areas.index', ['country_id' => $area->country_id, 'city_id' => $area->city_id])
            ->with('success', $msg);
    }

    /**
     * Update an existing Area.
     */
    public function update(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $area = Area::findOrFail($id);

        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $city = City::findOrFail($validated['city_id']);
        if ((int)$city->country_id !== (int)$validated['country_id']) {
            return back()->withErrors(['city_id' => app()->getLocale() === 'ar' ? 'المدينة المختارة لا تنتمي إلى هذه الدولة' : 'Selected city does not belong to this country'])->withInput();
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $area->update($validated);

        $msg = app()->getLocale() === 'ar' ? "تم تحديث بيانات المنطقة [{$area->name_ar}] بنجاح" : "Area [{$area->name_en}] updated successfully";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'area' => $area->load(['country', 'city']),
            ]);
        }

        return redirect()->route('admin.mr.areas.index', ['country_id' => $area->country_id, 'city_id' => $area->city_id])
            ->with('success', $msg);
    }

    /**
     * Toggle the active status of an Area.
     */
    public function toggleStatus(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $area = Area::findOrFail($id);
        $area->is_active = !$area->is_active;
        $area->save();

        $msg = $area->is_active 
            ? (app()->getLocale() === 'ar' ? "تم تفعيل منطقة {$area->name_ar} بنجاح" : "Area {$area->name_en} activated successfully")
            : (app()->getLocale() === 'ar' ? "تم تعطيل منطقة {$area->name_ar} بنجاح" : "Area {$area->name_en} deactivated successfully");

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'is_active' => $area->is_active,
                'message' => $msg,
            ]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Safely delete an Area.
     */
    public function destroy(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $area = Area::findOrFail($id);

        $assignedRepsCount = $area->medicalReps()->count();
        $contactsCount = $area->contacts()->count();

        if ($assignedRepsCount > 0 || $contactsCount > 0) {
            $msg = app()->getLocale() === 'ar'
                ? "لا يمكن حذف هذه المنطقة لوجود {$assignedRepsCount} مندوب طبي و {$contactsCount} طبيب/عيادة مسجلين عليها"
                : "Cannot delete this area because {$assignedRepsCount} MR(s) and {$contactsCount} contact(s) are assigned to it";

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }

            return back()->withErrors(['delete_error' => $msg]);
        }

        $countryId = $area->country_id;
        $cityId = $area->city_id;
        $name = $area->name;
        $area->delete();

        $msg = app()->getLocale() === 'ar' ? "تم حذف المنطقة [{$name}] بنجاح" : "Area [{$name}] deleted successfully";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('admin.mr.areas.index', ['country_id' => $countryId, 'city_id' => $cityId])
            ->with('success', $msg);
    }

    /**
     * Fast AJAX Endpoint: Return JSON areas for a given City ID.
     */
    public function getAreasByCity(Request $request, int $cityId): JsonResponse
    {
        $city = City::with('country')->findOrFail($cityId);
        $areas = AreaTerritoryService::getInstance()->getAreasByCity($cityId, true);

        return response()->json([
            'success' => true,
            'city' => [
                'id' => $city->id,
                'name_en' => $city->name_en,
                'name_ar' => $city->name_ar,
                'country_id' => $city->country_id,
                'country_name' => $city->country?->name ?? '',
            ],
            'areas' => $areas->map(function ($a) {
                return [
                    'id' => $a->id,
                    'city_id' => $a->city_id,
                    'country_id' => $a->country_id,
                    'name_en' => $a->name_en,
                    'name_ar' => $a->name_ar,
                    'name' => $a->name,
                    'code' => $a->code,
                ];
            }),
        ]);
    }
}
