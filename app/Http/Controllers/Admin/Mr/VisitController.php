<?php

namespace App\Http\Controllers\Admin\Mr;

use App\Http\Controllers\Controller;
use App\Models\Mr\Visit;
use App\Models\Mr\VisitCycle;
use App\Models\User;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    public function index(Request $request)
    {
        $cycles = VisitCycle::latest('start_date')->get();
        $selectedCycleId = $request->integer('cycle_id');

        $query = Visit::with([
            'representative',
            'contact.specialty',
            'contact.classification',
            'contact.city',
            'cycle',
        ]);

        if ($selectedCycleId) {
            $query->where('cycle_id', $selectedCycleId);
        }

        if ($request->filled('mr_id')) {
            $query->where('mr_id', $request->integer('mr_id'));
        }

        if ($request->filled('gps_status')) {
            if ($request->gps_status === 'verified') {
                $query->where('gps_verified', true);
            } elseif ($request->gps_status === 'unverified') {
                $query->where('gps_verified', false);
            }
        }

        if ($request->filled('outcome')) {
            $query->where('outcome', $request->outcome);
        }

        $visits = $query->latest('checkin_at')->paginate(20)->withQueryString();

        $medicalReps = User::whereHas('role', function ($q) {
            $q->where('name', 'mr');
        })->orWhere('role_id', 2)->select('id', 'name')->get();

        return view('admin.mr.visits.index', compact('visits', 'cycles', 'selectedCycleId', 'medicalReps'));
    }

    public function show(int $id)
    {
        $visit = Visit::with([
            'representative',
            'contact.specialty',
            'contact.classification',
            'contact.city',
            'cycle',
        ])->findOrFail($id);

        return view('admin.mr.visits.show', compact('visit'));
    }
}
