<?php

namespace App\Http\Controllers\Admin\Mr;

use App\Http\Controllers\Controller;
use App\Models\Mr\Visit;
use App\Models\Mr\VisitCycle;
use App\Models\User;
use Illuminate\Http\Request;

class MrMapController extends Controller
{
    /**
     * Display the Live Ops Field Map and Real-Time Activity Feed.
     */
    public function index(Request $request)
    {
        $activeCycle = VisitCycle::where('status', 'active')->latest()->first();

        // Latest 50 check-ins with coordinates for the map
        $visitsQuery = Visit::with([
            'representative:id,name,avatar',
            'contact.specialty',
            'contact.classification',
            'contact.city',
        ])->whereNotNull('checkin_lat')
          ->whereNotNull('checkin_lng')
          ->latest('checkin_at');

        if ($request->filled('mr_id')) {
            $visitsQuery->where('mr_id', $request->integer('mr_id'));
        }

        if ($request->filled('verified_only')) {
            $visitsQuery->where('gps_verified', true);
        }

        $latestVisits = $visitsQuery->take(50)->get();

        // Medical Reps list for filtering
        $medicalReps = User::whereHas('role', function ($q) {
            $q->where('name', 'mr');
        })->orWhere('role_id', 2)->select('id', 'name')->get();

        // Format geojson/markers payload
        $markers = $latestVisits->map(function ($visit) {
            return [
                'id' => $visit->id,
                'rep_name' => $visit->representative?->name ?? 'MR',
                'doctor_name' => $visit->contact?->name ?? 'Doctor',
                'clinic_name' => $visit->contact?->hospital_clinic_name ?? '',
                'specialty' => $visit->contact?->specialty?->name ?? '',
                'classification' => $visit->contact?->classification?->code ?? '',
                'lat' => (float) $visit->checkin_lat,
                'lng' => (float) $visit->checkin_lng,
                'verified' => (bool) $visit->gps_verified,
                'flag' => $visit->gps_flag,
                'distance_m' => $visit->distance_from_contact_m,
                'time_formatted' => $visit->checkin_at?->format('H:i, d M Y'),
                'time_ago' => $visit->checkin_at?->diffForHumans(),
                'outcome' => $visit->outcome,
                'notes' => $visit->notes,
            ];
        });

        return view('admin.mr.map', compact('latestVisits', 'markers', 'medicalReps', 'activeCycle'));
    }
}
