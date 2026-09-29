<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\AreaBreak;
use App\Models\City;
use App\Models\Country;
use App\Services\Mr\AreaTerritoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BreakController extends Controller
{
    /**
     * Display a paginated listing of Breaks with Country, City, and Area filters.
     */
    public function index(Request $request): View|JsonResponse
    {
        $countryId = $request->query('country_id');
        $cityId = $request->query('city_id');
        $areaId = $request->query('area_id');
        $search = $request->query('search');

        $query = AreaBreak::with(['country', 'city', 'area'])
            ->withCount(['medicalReps', 'contacts']);

        if ($countryId) {
            $query->where('country_id', $countryId);
        }

        if ($cityId) {
            $query->where('city_id', $cityId);
        }

        if ($areaId) {
            $query->where('area_id', $areaId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name_en', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $breaks = $query->orderBy('sort_order')->orderBy('name_en')->paginate(20)->withQueryString();

        $countries = Country::where('is_active', true)->orderBy('sort_order')->orderBy('name_en')->get();
        $cities = $countryId 
            ? City::where('country_id', $countryId)->where('is_active', true)->orderBy('name_en')->get() 
            : City::where('is_active', true)->orderBy('name_en')->get();
        $areas = $cityId
            ? Area::where('city_id', $cityId)->where('is_active', true)->orderBy('name_en')->get()
            : ($countryId ? Area::where('country_id', $countryId)->where('is_active', true)->orderBy('name_en')->get() : Area::where('is_active', true)->orderBy('name_en')->get());

        $territoryService = AreaTerritoryService::getInstance();
        $stats = $territoryService->getTerritoryStats();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'breaks' => $breaks,
                'stats' => $stats,
            ]);
        }

        return view('admin.mr.breaks.index', compact('breaks', 'countries', 'cities', 'areas', 'countryId', 'cityId', 'areaId', 'search', 'stats'));
    }

    /**
     * Store a newly created Break.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            'area_id' => 'required|exists:areas,id',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $area = Area::findOrFail($validated['area_id']);
        if ((int)$area->city_id !== (int)$validated['city_id'] || (int)$area->country_id !== (int)$validated['country_id']) {
            $err = app()->getLocale() === 'ar' ? 'المنطقة المختارة لا تتطابق مع المحافظة أو الدولة المحددة' : 'Selected area does not match the chosen city or country';
            return back()->withErrors(['area_id' => $err])->withInput();
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $break = AreaBreak::create($validated);

        $msg = app()->getLocale() === 'ar' ? "تمت إضافة القطاع/البريك [{$break->name_ar}] بنجاح" : "Break [{$break->name_en}] created successfully";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'break' => $break->load(['country', 'city', 'area']),
            ]);
        }

        return redirect()->route('admin.mr.breaks.index', ['country_id' => $break->country_id, 'city_id' => $break->city_id, 'area_id' => $break->area_id])
            ->with('success', $msg);
    }

    /**
     * Update an existing Break.
     */
    public function update(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $break = AreaBreak::findOrFail($id);

        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            'area_id' => 'required|exists:areas,id',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $area = Area::findOrFail($validated['area_id']);
        if ((int)$area->city_id !== (int)$validated['city_id'] || (int)$area->country_id !== (int)$validated['country_id']) {
            $err = app()->getLocale() === 'ar' ? 'المنطقة المختارة لا تتطابق مع المحافظة أو الدولة المحددة' : 'Selected area does not match the chosen city or country';
            return back()->withErrors(['area_id' => $err])->withInput();
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $break->update($validated);

        $msg = app()->getLocale() === 'ar' ? "تم تحديث بيانات البريك [{$break->name_ar}] بنجاح" : "Break [{$break->name_en}] updated successfully";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'break' => $break->load(['country', 'city', 'area']),
            ]);
        }

        return redirect()->route('admin.mr.breaks.index', ['country_id' => $break->country_id, 'city_id' => $break->city_id, 'area_id' => $break->area_id])
            ->with('success', $msg);
    }

    /**
     * Toggle the active status of a Break.
     */
    public function toggleStatus(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $break = AreaBreak::findOrFail($id);
        $break->is_active = !$break->is_active;
        $break->save();

        $msg = $break->is_active 
            ? (app()->getLocale() === 'ar' ? "تم تفعيل بريك {$break->name_ar} بنجاح" : "Break {$break->name_en} activated successfully")
            : (app()->getLocale() === 'ar' ? "تم تعطيل بريك {$break->name_ar} بنجاح" : "Break {$break->name_en} deactivated successfully");

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'is_active' => $break->is_active,
                'message' => $msg,
            ]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Safely delete a Break.
     */
    public function destroy(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $break = AreaBreak::findOrFail($id);

        $assignedRepsCount = $break->medicalReps()->count();
        $contactsCount = $break->contacts()->count();

        if ($assignedRepsCount > 0 || $contactsCount > 0) {
            $msg = app()->getLocale() === 'ar'
                ? "لا يمكن حذف هذا البريك لوجود {$assignedRepsCount} مندوب طبي و {$contactsCount} طبيب/عيادة مسجلين عليه"
                : "Cannot delete this break because {$assignedRepsCount} MR(s) and {$contactsCount} contact(s) are assigned to it";

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }

            return back()->withErrors(['delete_error' => $msg]);
        }

        $countryId = $break->country_id;
        $cityId = $break->city_id;
        $areaId = $break->area_id;
        $name = $break->name;
        $break->delete();

        $msg = app()->getLocale() === 'ar' ? "تم حذف البريك [{$name}] بنجاح" : "Break [{$name}] deleted successfully";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('admin.mr.breaks.index', ['country_id' => $countryId, 'city_id' => $cityId, 'area_id' => $areaId])
            ->with('success', $msg);
    }

    /**
     * Fast AJAX Endpoint: Return JSON breaks for a given Area ID.
     */
    public function getBreaksByArea(Request $request, int $areaId): JsonResponse
    {
        $area = Area::with(['city.country'])->findOrFail($areaId);
        $breaks = AreaTerritoryService::getInstance()->getBreaksByArea($areaId, true);

        return response()->json([
            'success' => true,
            'area' => [
                'id' => $area->id,
                'name_en' => $area->name_en,
                'name_ar' => $area->name_ar,
                'city_id' => $area->city_id,
                'city_name' => $area->city?->name ?? '',
                'country_id' => $area->country_id,
                'country_name' => $area->country?->name ?? '',
            ],
            'breaks' => $breaks->map(function ($b) {
                return [
                    'id' => $b->id,
                    'area_id' => $b->area_id,
                    'city_id' => $b->city_id,
                    'country_id' => $b->country_id,
                    'name_en' => $b->name_en,
                    'name_ar' => $b->name_ar,
                    'name' => $b->name,
                    'code' => $b->code,
                ];
            }),
        ]);
    }
}
