<?php

namespace App\Http\Controllers\Mr;

use App\Http\Controllers\Controller;
use App\Models\Mr\Contact;
use App\Models\Mr\ContactAssignment;
use App\Models\Mr\ScheduledVisit;
use App\Models\Mr\Visit;
use App\Models\Mr\VisitCycle;
use App\Models\Product;
use App\Services\Mr\CrmMrReportService;
use App\Services\Mr\CrmScheduleService;
use App\Services\Mr\CrmVisitService;
use App\Services\Mr\GpsValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MrDashboardController extends Controller
{
    protected CrmVisitService $visitService;
    protected CrmScheduleService $scheduleService;
    protected CrmMrReportService $reportService;
    protected GpsValidationService $gpsService;

    public function __construct(
        CrmVisitService $visitService,
        CrmScheduleService $scheduleService,
        CrmMrReportService $reportService,
        GpsValidationService $gpsService
    ) {
        $this->visitService = $visitService;
        $this->scheduleService = $scheduleService;
        $this->reportService = $reportService;
        $this->gpsService = $gpsService;
    }

    /**
     * MR Mobile-First Dashboard Main View
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $activeCycle = VisitCycle::where('status', 'active')->first();

        // 1. Personal Performance Snapshot
        $snapshot = null;
        if ($activeCycle && $user) {
            $snapshot = $this->reportService->recalculateRepSnapshot($user->id, $activeCycle->id);
        }

        // 2. Today's Scheduled Visits
        $todayVisits = [];
        $activeOngoingVisit = null;
        if ($user) {
            $todayVisits = ScheduledVisit::with(['contact.specialty', 'contact.classification', 'contact.city', 'visit'])
                ->where('mr_id', $user->id)
                ->whereDate('scheduled_at', now()->toDateString())
                ->orderBy('scheduled_at')
                ->get();

            foreach ($todayVisits as $tv) {
                if ($tv->contact) {
                    $tv->contact->quota_info = $tv->contact->getVisitQuotaStatus($tv->cycle_id ?: $activeCycle?->id);
                }
            }

            // Check if there is an active check-in without checkout
            $activeOngoingVisit = Visit::with(['contact.specialty', 'contact.classification'])
                ->where('mr_id', $user->id)
                ->whereNull('checkout_at')
                ->latest('checkin_at')
                ->first();
        }

        // 3. Proactive At-Risk Doctors
        $atRiskAssignments = [];
        if ($user && $activeCycle) {
            $atRiskAssignments = $this->scheduleService->getAtRiskAssignmentsForRep($user->id, $activeCycle->id);
        }

        // 4. Assigned Doctors Portfolio
        $allAssignments = [];
        if ($user && $activeCycle) {
            $allAssignments = ContactAssignment::with(['contact.specialty', 'contact.classification', 'contact.city'])
                ->where('mr_id', $user->id)
                ->where('cycle_id', $activeCycle->id)
                ->where('is_active', true)
                ->get();

            foreach ($allAssignments as $assign) {
                if ($assign->contact) {
                    $assign->contact->quota_info = $assign->contact->getVisitQuotaStatus($activeCycle->id);
                }
            }
        }

        // 5. Completed Visits / Doctors Done (with relations: contact, products, product)
        $completedVisits = [];
        if ($user) {
            $completedVisits = Visit::with([
                    'contact.specialty',
                    'contact.classification',
                    'contact.city',
                    'product',
                    'products',
                ])
                ->where('mr_id', $user->id)
                ->when($activeCycle, function ($q) use ($activeCycle) {
                    $q->where('cycle_id', $activeCycle->id);
                })
                ->whereNotNull('checkout_at')
                ->latest('checkout_at')
                ->get();
        }

        $availableProducts = Product::where('status', 'active')
            ->orWhereNull('status')
            ->orderBy('name_en')
            ->get(['id', 'name_en', 'name_ar', 'sku']);

        return view('mr.dashboard', compact(
            'user',
            'activeCycle',
            'snapshot',
            'todayVisits',
            'activeOngoingVisit',
            'atRiskAssignments',
            'allAssignments',
            'completedVisits',
            'availableProducts'
        ));
    }

    /**
     * Submit Check-In with GPS Coordinates
     */
    public function checkIn(Request $request): JsonResponse
    {
        $request->validate([
            'contact_id' => 'required|integer',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'accuracy_m' => 'nullable|numeric',
            'scheduled_visit_id' => 'nullable|integer',
            'assignment_id' => 'nullable|integer',
        ]);

        $user = Auth::user();

        try {
            $visit = $this->visitService->submitCheckIn($user->id, $request->all());

            return response()->json([
                'success' => true,
                'message' => $visit->gps_verified ? 'Check-in verified at clinic!' : 'Check-in recorded (GPS flagged: ' . $visit->gps_flag . ')',
                'visit' => $visit,
                'gps_verified' => $visit->gps_verified,
                'gps_flag' => $visit->gps_flag,
                'distance_m' => $visit->distance_from_contact_m,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Submit Check-Out with Outcome and Notes
     */
    public function checkOut(Request $request): JsonResponse
    {
        $request->validate([
            'visit_id' => 'required|integer',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'outcome' => 'required|string',
            'notes' => 'required|string|min:3|max:2000',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'integer|exists:products,id',
            'product_id' => 'nullable|integer|exists:products,id',
        ], [
            'notes.required' => app()->getLocale() === 'ar' ? 'ملاحظات الزيارة وملاحظات الطبيب مطلوبة.' : 'Meeting notes and feedback are required.',
            'notes.min' => app()->getLocale() === 'ar' ? 'يرجى كتابة 3 أحرف على الأقل في الملاحظات.' : 'Meeting notes must be at least 3 characters.',
        ]);

        $user = Auth::user();

        try {
            $visit = $this->visitService->submitCheckOut((int) $request->visit_id, $user->id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Visit successfully completed and logged!',
                'visit' => $visit,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Create or update scheduled visit slot
     */
    public function schedule(Request $request): JsonResponse
    {
        $request->validate([
            'assignment_id' => 'required|integer',
            'scheduled_at' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        try {
            $scheduled = $this->scheduleService->scheduleVisit(
                (int) $request->assignment_id,
                $request->scheduled_at,
                $request->notes
            );

            return response()->json([
                'success' => true,
                'message' => 'Visit scheduled on your agenda!',
                'scheduled' => $scheduled,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Calendar events JSON endpoint for FullCalendar / Live calendar
     */
    public function calendarEvents(Request $request): JsonResponse
    {
        $user = Auth::user();
        $start = $request->input('start', now()->startOfMonth()->toDateString());
        $end = $request->input('end', now()->endOfMonth()->toDateString());

        $events = $this->scheduleService->getCalendarEventsForRep($user->id, $start, $end);

        return response()->json($events);
    }
}
