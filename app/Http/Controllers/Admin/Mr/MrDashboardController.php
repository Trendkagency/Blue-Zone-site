<?php

namespace App\Http\Controllers\Admin\Mr;

use App\Http\Controllers\Controller;
use App\Models\Mr\Contact;
use App\Models\Mr\ContactAssignment;
use App\Models\Mr\ContactClassification;
use App\Models\Mr\ContactSpecialty;
use App\Models\Mr\RepDailyLog;
use App\Models\Mr\RepPerformanceSnapshot;
use App\Models\Mr\ScheduledVisit;
use App\Models\Mr\Visit;
use App\Models\Mr\VisitCycle;
use App\Models\Product;
use App\Models\User;
use App\Services\FcmService;
use App\Services\Mr\CrmMrReportService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MrDashboardController extends Controller
{
    protected CrmMrReportService $reportService;

    public function __construct(CrmMrReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display the Executive Medical Rep CRM Dashboard & Scheduling Command Center.
     */
    public function index(Request $request)
    {
        $currentUser = auth()->user();
        $isManager = $currentUser ? $currentUser->canManageAllMr() : false;
        $isRep = $currentUser ? ($currentUser->isMedicalRep() && !$isManager) : false;

        $selectedDate = $request->input('date', now()->toDateString());
        $viewMode = $request->input('view', 'calendar'); // 'calendar', 'day', 'timeline', 'week', 'reps'
        $selectedMrId = $isRep ? $currentUser->id : ($request->filled('mr_id') ? $request->integer('mr_id') : null);
        $selectedCycleId = $request->filled('cycle_id') ? $request->integer('cycle_id') : null;
        $selectedSpecialtyId = $request->filled('specialty_id') ? $request->integer('specialty_id') : null;
        $selectedClassId = $request->filled('classification_id') ? $request->integer('classification_id') : null;
        $selectedStatus = $request->input('status', 'all');

        // 1. Visit Cycles
        $cycles = VisitCycle::latest('start_date')->get();
        $activeCycle = $selectedCycleId
            ? $cycles->firstWhere('id', $selectedCycleId)
            : ($cycles->firstWhere('status', 'active') ?? $cycles->first());
        $cycleId = $activeCycle?->id;

        // 2. Medical Representatives List (Scoped to self if rep, otherwise all)
        if ($isRep) {
            $medicalReps = User::where('id', $currentUser->id)
                ->with(['area', 'city', 'country'])
                ->select('id', 'name', 'email', 'phone', 'country_id', 'city_id', 'area_id', 'status')
                ->get();
        } else {
            $medicalReps = User::where(function ($query) {
                $query->whereHas('role', function ($q) {
                    $q->where('name', 'mr');
                })->orWhere('role_id', 2);
            })
            ->with(['area', 'city', 'country'])
            ->select('id', 'name', 'email', 'phone', 'country_id', 'city_id', 'area_id', 'status')
            ->orderBy('name')
            ->get();
        }

        // 3. Specialties & Classifications for filters and forms
        $specialties = ContactSpecialty::orderBy('name')->get();
        $classifications = ContactClassification::orderBy('code')->get();

        // 4. Available Active Doctors / Contacts (Scoped to rep's assigned portfolio if rep)
        $availableDoctorsQuery = Contact::where('is_active', true)
            ->with(['specialty', 'classification', 'city', 'area']);

        if ($isRep) {
            $assignedDoctorIds = ContactAssignment::where('mr_id', $currentUser->id)
                ->when($cycleId, fn ($q) => $q->where('cycle_id', $cycleId))
                ->where('is_active', true)
                ->pluck('contact_id');

            if ($assignedDoctorIds->isNotEmpty()) {
                $availableDoctorsQuery->whereIn('id', $assignedDoctorIds);
            }
        }

        $availableDoctors = $availableDoctorsQuery->orderBy('name')->get();

        // 5. Active Products for Visit Sampling & Promotion
        $products = Product::where('is_active', true)
            ->select('id', 'name_en', 'name_ar', 'sku')
            ->orderBy('name_en')
            ->get();

        // 6. Fetch Scheduled Visits for the Selected Date (Day View)
        $scheduledQuery = ScheduledVisit::with([
            'representative.area',
            'representative.city',
            'contact.specialty',
            'contact.classification',
            'contact.city',
            'contact.area',
            'cycle',
            'visit.products',
            'visit.product',
        ])->whereDate('scheduled_at', $selectedDate);

        if ($selectedMrId) {
            $scheduledQuery->where('mr_id', $selectedMrId);
        }
        if ($cycleId) {
            $scheduledQuery->where('cycle_id', $cycleId);
        }
        if ($selectedStatus && $selectedStatus !== 'all') {
            $scheduledQuery->where('status', $selectedStatus);
        }
        if ($selectedSpecialtyId) {
            $scheduledQuery->whereHas('contact', fn ($q) => $q->where('specialty_id', $selectedSpecialtyId));
        }
        if ($selectedClassId) {
            $scheduledQuery->whereHas('contact', fn ($q) => $q->where('classification_id', $selectedClassId));
        }

        $scheduledVisits = $scheduledQuery->orderBy('scheduled_at', 'asc')->get();

        // 7. Calculate Real-time KPIs for Today
        $allTodayVisits = ScheduledVisit::whereDate('scheduled_at', $selectedDate)
            ->when($selectedMrId, fn ($q) => $q->where('mr_id', $selectedMrId))
            ->when($cycleId, fn ($q) => $q->where('cycle_id', $cycleId))
            ->get();

        $todayTotal = $allTodayVisits->count();
        $todayCompleted = $allTodayVisits->where('status', 'completed')->count();
        $todayPlanned = $allTodayVisits->where('status', 'planned')->count();
        $todayCancelled = $allTodayVisits->where('status', 'cancelled')->count();
        $todayInProgress = $allTodayVisits->where('status', 'in_progress')->count();
        $todayCompletionRate = $todayTotal > 0 ? round(($todayCompleted / $todayTotal) * 100, 1) : 0;

        // Executed Visits KPI for GPS & Duration metrics
        $todayExecutedVisits = Visit::whereDate('checkin_at', $selectedDate)
            ->when($selectedMrId, fn ($q) => $q->where('mr_id', $selectedMrId))
            ->when($cycleId, fn ($q) => $q->where('cycle_id', $cycleId))
            ->get();

        $todayVerifiedGps = $todayExecutedVisits->where('gps_verified', true)->count();
        $todayGpsRate = $todayExecutedVisits->count() > 0 ? round(($todayVerifiedGps / $todayExecutedVisits->count()) * 100, 1) : 100;
        $activeRepsOnDutyToday = $scheduledVisits->pluck('mr_id')->merge($todayExecutedVisits->pluck('mr_id'))->unique()->count();

        // 8. Cycle Level Aggregated Metrics
        $cycleTotalAssigned = 0;
        $cycleTargetVisits = 0;
        $cycleVisitsDone = 0;
        $cycleTargetPoints = 0;
        $cycleAchievedPoints = 0;

        if ($cycleId) {
            $assignments = ContactAssignment::where('cycle_id', $cycleId)
                ->where('is_active', true)
                ->when($selectedMrId, fn ($q) => $q->where('mr_id', $selectedMrId))
                ->get();

            $cycleTotalAssigned = $assignments->count();
            $cycleTargetVisits = (int) $assignments->sum('target_visits');
            $cycleVisitsDone = (int) $assignments->sum('visits_done');
            $cycleTargetPoints = (int) $assignments->sum('target_points');
            $cycleAchievedPoints = (int) $assignments->sum('achieved_points');
        }

        $cycleVisitProgressRate = $cycleTargetVisits > 0 ? round(($cycleVisitsDone / $cycleTargetVisits) * 100, 1) : 0;
        $cyclePointProgressRate = $cycleTargetPoints > 0 ? round(($cycleAchievedPoints / $cycleTargetPoints) * 100, 1) : 0;

        // 9. Build 360 Information Array for Each Medical Rep
        $carbonSelectedDate = Carbon::parse($selectedDate);
        $repsData = $medicalReps->map(function ($rep) use ($cycleId, $carbonSelectedDate, $selectedDate) {
            $repTodayVisits = ScheduledVisit::where('mr_id', $rep->id)
                ->whereDate('scheduled_at', $selectedDate)
                ->with(['contact.specialty', 'contact.classification', 'visit'])
                ->orderBy('scheduled_at')
                ->get();

            $repAssignedCount = ContactAssignment::where('mr_id', $rep->id)
                ->when($cycleId, fn ($q) => $q->where('cycle_id', $cycleId))
                ->where('is_active', true)
                ->count();

            $repCycleTargetVisits = ContactAssignment::where('mr_id', $rep->id)
                ->when($cycleId, fn ($q) => $q->where('cycle_id', $cycleId))
                ->where('is_active', true)
                ->sum('target_visits');

            $repCycleVisitsDone = ContactAssignment::where('mr_id', $rep->id)
                ->when($cycleId, fn ($q) => $q->where('cycle_id', $cycleId))
                ->where('is_active', true)
                ->sum('visits_done');

            $repCycleTargetPoints = ContactAssignment::where('mr_id', $rep->id)
                ->when($cycleId, fn ($q) => $q->where('cycle_id', $cycleId))
                ->where('is_active', true)
                ->sum('target_points');

            $repCycleAchievedPoints = ContactAssignment::where('mr_id', $rep->id)
                ->when($cycleId, fn ($q) => $q->where('cycle_id', $cycleId))
                ->where('is_active', true)
                ->sum('achieved_points');

            $latestCheckin = Visit::where('mr_id', $rep->id)
                ->latest('checkin_at')
                ->with('contact')
                ->first();

            $todayCompletedCount = $repTodayVisits->where('status', 'completed')->count();
            $todayPlannedCount = $repTodayVisits->count();
            $todayCompPct = $todayPlannedCount > 0 ? round(($todayCompletedCount / $todayPlannedCount) * 100, 1) : 0;
            $cycleCompPct = $repCycleTargetVisits > 0 ? round(($repCycleVisitsDone / $repCycleTargetVisits) * 100, 1) : 0;

            return [
                'id' => $rep->id,
                'name' => $rep->name,
                'email' => $rep->email,
                'phone' => $rep->phone,
                'territory' => $rep->territory_label ?? '—',
                'area_name' => $rep->area?->name ?? '—',
                'city_name' => $rep->city?->name ?? '—',
                'assigned_doctors_count' => $repAssignedCount,
                'today_total' => $todayPlannedCount,
                'today_completed' => $todayCompletedCount,
                'today_pct' => $todayCompPct,
                'today_visits' => $repTodayVisits,
                'cycle_target_visits' => $repCycleTargetVisits,
                'cycle_visits_done' => $repCycleVisitsDone,
                'cycle_target_points' => $repCycleTargetPoints,
                'cycle_achieved_points' => $repCycleAchievedPoints,
                'cycle_pct' => $cycleCompPct,
                'latest_checkin_at' => $latestCheckin?->checkin_at?->diffForHumans() ?? 'No check-in yet',
                'latest_doctor' => $latestCheckin?->contact?->name,
                'latest_gps_verified' => $latestCheckin?->gps_verified ?? false,
            ];
        });

        // 10. Multi-Day Matrix data (For Week or Month Views)
        $weekDates = [];
        $weekMatrix = [];
        if ($viewMode === 'week' || $viewMode === 'month') {
            $start = $viewMode === 'week' ? $carbonSelectedDate->copy()->startOfWeek() : $carbonSelectedDate->copy()->startOfMonth();
            $end = $viewMode === 'week' ? $carbonSelectedDate->copy()->endOfWeek() : $carbonSelectedDate->copy()->endOfMonth();
            $period = CarbonPeriod::create($start, $end);

            $rangeVisits = ScheduledVisit::with(['representative', 'contact.specialty', 'contact.classification'])
                ->whereBetween('scheduled_at', [$start->startOfDay(), $end->endOfDay()])
                ->when($selectedMrId, fn ($q) => $q->where('mr_id', $selectedMrId))
                ->when($cycleId, fn ($q) => $q->where('cycle_id', $cycleId))
                ->get();

            foreach ($period as $dt) {
                $dStr = $dt->toDateString();
                $weekDates[] = [
                    'date' => $dStr,
                    'day_name' => $dt->format('D'),
                    'day_num' => $dt->format('d'),
                    'is_today' => $dt->isToday(),
                    'is_selected' => $dStr === $selectedDate,
                ];
                $dayVisits = $rangeVisits->filter(fn ($v) => $v->scheduled_at->toDateString() === $dStr);
                $weekMatrix[$dStr] = [
                    'visits' => $dayVisits,
                    'total' => $dayVisits->count(),
                    'completed' => $dayVisits->where('status', 'completed')->count(),
                    'planned' => $dayVisits->where('status', 'planned')->count(),
                ];
            }
        }

        // 11. Full Calendar Events for the surrounding window
        $calendarQuery = ScheduledVisit::with([
            'representative.area',
            'representative.city',
            'contact.specialty',
            'contact.classification',
            'contact.city',
            'cycle',
        ]);

        if ($selectedMrId) {
            $calendarQuery->where('mr_id', $selectedMrId);
        }
        if ($cycleId) {
            $calendarQuery->where('cycle_id', $cycleId);
        }
        if ($selectedStatus && $selectedStatus !== 'all') {
            $calendarQuery->where('status', $selectedStatus);
        }
        if ($selectedSpecialtyId) {
            $calendarQuery->whereHas('contact', fn ($q) => $q->where('specialty_id', $selectedSpecialtyId));
        }
        if ($selectedClassId) {
            $calendarQuery->whereHas('contact', fn ($q) => $q->where('classification_id', $selectedClassId));
        }

        $calendarVisits = $calendarQuery
            ->whereBetween('scheduled_at', [now()->subDays(45)->startOfDay(), now()->addDays(60)->endOfDay()])
            ->orderBy('scheduled_at')
            ->get();

        $calendarEvents = $this->buildCalendarEvents($calendarVisits);

        // 12. Recent Live GPS Field Check-ins
        $recentCheckinsQuery = Visit::with(['representative', 'contact.specialty', 'contact.classification', 'contact.city'])
            ->latest('checkin_at');

        if ($isRep) {
            $recentCheckinsQuery->where('mr_id', $currentUser->id);
        }

        $recentCheckins = $recentCheckinsQuery->limit(6)->get();

        // 13. Doctor Classification Coverage Breakdown
        $classDistribution = ContactClassification::withCount(['contacts' => function($q) {
            $q->where('is_active', true);
        }])->orderBy('code')->get();

        // Summary KPI bundle
        $kpis = [
            'total_reps' => $isRep ? 1 : $medicalReps->count(),
            'active_reps_today' => $isRep ? (($todayTotal > 0 || $todayExecutedVisits->count() > 0) ? 1 : 0) : $activeRepsOnDutyToday,
            'today_total' => $todayTotal,
            'today_completed' => $todayCompleted,
            'today_planned' => $todayPlanned,
            'today_cancelled' => $todayCancelled,
            'today_in_progress' => $todayInProgress,
            'today_completion_rate' => $todayCompletionRate,
            'today_gps_rate' => $todayGpsRate,
            'cycle_total_assigned' => $cycleTotalAssigned,
            'cycle_target_visits' => $cycleTargetVisits,
            'cycle_visits_done' => $cycleVisitsDone,
            'cycle_visit_progress_rate' => $cycleVisitProgressRate,
            'cycle_target_points' => $cycleTargetPoints,
            'cycle_achieved_points' => $cycleAchievedPoints,
            'cycle_point_progress_rate' => $cyclePointProgressRate,
        ];

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'kpis' => $kpis,
                'scheduledVisits' => $scheduledVisits,
                'repsData' => $repsData,
                'calendarEvents' => $calendarEvents,
            ]);
        }

        return view('admin.mr.dashboard', compact(
            'isRep',
            'isManager',
            'currentUser',
            'selectedDate',
            'viewMode',
            'selectedMrId',
            'selectedCycleId',
            'selectedSpecialtyId',
            'selectedClassId',
            'selectedStatus',
            'cycles',
            'activeCycle',
            'medicalReps',
            'specialties',
            'classifications',
            'availableDoctors',
            'products',
            'scheduledVisits',
            'repsData',
            'kpis',
            'weekDates',
            'weekMatrix',
            'calendarEvents',
            'recentCheckins',
            'classDistribution'
        ));
    }

    /**
     * Provide JSON Event Feed for FullCalendar.
     */
    public function calendarFeed(Request $request): JsonResponse
    {
        $currentUser = auth()->user();
        $isManager = $currentUser ? $currentUser->canManageAllMr() : false;
        $isRep = $currentUser ? ($currentUser->isMedicalRep() && !$isManager) : false;

        $start = $request->query('start') ? Carbon::parse($request->query('start')) : now()->startOfMonth();
        $end = $request->query('end') ? Carbon::parse($request->query('end')) : now()->endOfMonth();
        $mrId = $isRep ? $currentUser->id : ($request->filled('mr_id') ? $request->integer('mr_id') : null);
        $cycleId = $request->filled('cycle_id') ? $request->integer('cycle_id') : null;
        $status = $request->query('status');
        $specialtyId = $request->filled('specialty_id') ? $request->integer('specialty_id') : null;
        $classId = $request->filled('classification_id') ? $request->integer('classification_id') : null;

        $query = ScheduledVisit::with([
            'representative.area',
            'representative.city',
            'contact.specialty',
            'contact.classification',
            'contact.city',
        ])->whereBetween('scheduled_at', [$start, $end]);

        if ($mrId) {
            $query->where('mr_id', $mrId);
        }
        if ($cycleId) {
            $query->where('cycle_id', $cycleId);
        }
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }
        if ($specialtyId) {
            $query->whereHas('contact', fn ($q) => $q->where('specialty_id', $specialtyId));
        }
        if ($classId) {
            $query->whereHas('contact', fn ($q) => $q->where('classification_id', $classId));
        }

        $visits = $query->orderBy('scheduled_at')->get();
        $events = $this->buildCalendarEvents($visits);

        return response()->json($events);
    }

    /**
     * Transform Scheduled Visits collection into FullCalendar compliant event array.
     */
    protected function buildCalendarEvents($scheduledVisits): array
    {
        return $scheduledVisits->map(function ($v) {
            $contact = $v->contact;
            $rep = $v->representative;
            $status = $v->status;

            $color = match ($status) {
                'completed' => '#10B981', // Emerald
                'in_progress' => '#06B6D4', // Cyan
                'cancelled' => '#EF4444', // Rose/Red
                default => '#F59E0B', // Amber / Planned
            };

            $borderColor = match ($status) {
                'completed' => '#059669',
                'in_progress' => '#0891B2',
                'cancelled' => '#DC2626',
                default => '#D97706',
            };

            $start = $v->scheduled_at ? $v->scheduled_at->toIso8601String() : null;
            $end = $v->scheduled_at ? $v->scheduled_at->copy()->addMinutes(45)->toIso8601String() : null;

            $cleanDocName = $contact?->name ?? 'Doctor';
            if (!preg_match('/^(dr\.|د\.|د\/|dr )/i', trim($cleanDocName))) {
                $cleanDocName = 'Dr. ' . $cleanDocName;
            }

            return [
                'id' => (string) $v->id,
                'title' => $cleanDocName . ' (' . ($contact?->specialty?->name ?? 'Specialist') . ')',
                'start' => $start,
                'end' => $end,
                'backgroundColor' => $color,
                'borderColor' => $borderColor,
                'textColor' => '#FFFFFF',
                'className' => 'fc-event-mr-schedule status-' . $status,
                'extendedProps' => [
                    'visit_id' => $v->id,
                    'mr_id' => $v->mr_id,
                    'mr_name' => $rep?->name ?? 'Rep',
                    'rep_avatar' => substr($rep?->name ?? 'MR', 0, 1),
                    'rep_territory' => $rep?->territory_label ?? 'Field Rep',
                    'rep_phone' => $rep?->phone,
                    'contact_id' => $v->contact_id,
                    'doctor_name' => $cleanDocName,
                    'clinic_name' => $contact?->hospital_clinic_name ?? 'Clinic',
                    'specialty' => $contact?->specialty?->name ?? 'General',
                    'classification' => $contact?->classification?->code ?? 'C',
                    'city' => $contact?->city?->name ?? '',
                    'doctor_phone' => $contact?->phone,
                    'doctor_lat' => $contact?->latitude,
                    'doctor_lng' => $contact?->longitude,
                    'status' => $status,
                    'scheduled_date' => $v->scheduled_at ? $v->scheduled_at->format('Y-m-d') : '',
                    'scheduled_time' => $v->scheduled_at ? $v->scheduled_at->format('H:i') : '',
                    'time_formatted' => $v->scheduled_at ? $v->scheduled_at->format('h:i A') : '',
                    'notes' => $v->notes ?? '',
                ],
            ];
        })->values()->toArray();
    }

    /**
     * Quick Schedule a single visit for a Medical Representative.
     */
    public function quickSchedule(Request $request)
    {
        $currentUser = auth()->user();
        $isManager = $currentUser ? $currentUser->canManageAllMr() : false;
        $isRep = $currentUser ? ($currentUser->isMedicalRep() && !$isManager) : false;

        $validated = $request->validate([
            'mr_id' => $isRep ? 'nullable' : 'required|exists:users,id',
            'contact_id' => 'required|exists:mr_contacts,id',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'required|string',
            'notes' => 'nullable|string|max:1000',
            'cycle_id' => 'nullable|exists:mr_visit_cycles,id',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        $mrId = $isRep ? (int) $currentUser->id : (int) ($validated['mr_id'] ?? $currentUser->id);
        $contactId = (int) $validated['contact_id'];
        $dateTimeStr = $validated['scheduled_date'] . ' ' . $validated['scheduled_time'];
        $scheduledAt = Carbon::parse($dateTimeStr);

        $cycle = null;
        if (!empty($validated['cycle_id'])) {
            $cycle = VisitCycle::find($validated['cycle_id']);
        }
        if (!$cycle) {
            $cycle = VisitCycle::where('status', 'active')
                ->where('start_date', '<=', $scheduledAt->toDateString())
                ->where('end_date', '>=', $scheduledAt->toDateString())
                ->first() ?: VisitCycle::where('status', 'active')->first() ?: VisitCycle::latest('id')->first();
        }

        if (!$cycle) {
            return $this->respondError(app()->getLocale() === 'ar' ? 'لا توجد دورة زيارات صالحة ومفتوحة.' : 'No valid visit cycle found.', $request);
        }

        $contact = Contact::findOrFail($contactId);

        // Enforce doctor visit quota based on classification
        $quota = $contact->getVisitQuotaStatus($cycle->id);
        if (!$quota['can_schedule']) {
            $msg = app()->getLocale() === 'ar'
                ? "لا يمكن جدولة زيارة جديدة للطبيب ({$contact->name}). لقد تم استنفاد الحد الأقصى للزيارات المسموحة في هذه الدورة ({$quota['current_count']}/{$quota['max_visits']} زيارات لتصنيف Class {$quota['class_code']})."
                : "Cannot schedule visit for Dr. {$contact->name}. Maximum visit quota reached for this cycle ({$quota['current_count']}/{$quota['max_visits']} visits for Class {$quota['class_code']}).";
            return $this->respondError($msg, $request);
        }

        $class = $contact->classification;
        $targetVisits = $class ? (int) $class->required_visits : 1;
        $targetPoints = $class ? ((int) $class->points * $targetVisits) : 3;

        // Ensure Assignment exists
        $assignment = ContactAssignment::firstOrCreate(
            [
                'cycle_id' => $cycle->id,
                'mr_id' => $mrId,
                'contact_id' => $contactId,
            ],
            [
                'target_visits' => $targetVisits,
                'target_points' => $targetPoints,
                'is_active' => true,
            ]
        );

        $scheduledVisit = ScheduledVisit::create([
            'assignment_id' => $assignment->id,
            'mr_id' => $mrId,
            'contact_id' => $contactId,
            'cycle_id' => $cycle->id,
            'scheduled_at' => $scheduledAt,
            'status' => 'planned',
            'notes' => $validated['notes'] ?? 'Scheduled from Executive Dashboard',
        ]);

        // Push FCM notification to rep
        $user = User::find($mrId);
        try {
            if ($user && method_exists($user, 'getActiveFcmTokens')) {
                $tokens = $user->getActiveFcmTokens();
                if (!empty($tokens)) {
                    $fcm = FcmService::getInstance();
                    $fcm->sendToTokens(
                        $tokens,
                        'New Visit Scheduled',
                        "New appointment with Dr. {$contact->name} scheduled on " . $scheduledAt->format('Y-m-d H:i'),
                        ['type' => 'scheduled_visit', 'visit_id' => (string) $scheduledVisit->id]
                    );
                }
            }
        } catch (\Throwable $e) {
            Log::warning("MR quick schedule FCM failed: " . $e->getMessage());
        }

        $successMsg = app()->getLocale() === 'ar'
            ? "تمت جدولة الموعد بنجاح للمندوب ({$user?->name}) مع الطبيب ({$contact->name}) ليوم " . $scheduledAt->format('Y-m-d H:i')
            : "Visit scheduled successfully for rep {$user?->name} with Dr. {$contact->name} on " . $scheduledAt->format('Y-m-d H:i');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'visit' => $scheduledVisit->load(['contact.specialty', 'contact.classification', 'representative']),
            ]);
        }

        return redirect()->route('admin.mr.dashboard', [
            'date' => $scheduledAt->toDateString(),
            'mr_id' => $mrId,
        ])->with('success', $successMsg);
    }

    /**
     * Quick Update Status of Scheduled Visit (planned, completed, cancelled, in_progress).
     */
    public function quickUpdateStatus(Request $request, int $id)
    {
        $currentUser = auth()->user();
        $isManager = $currentUser ? $currentUser->canManageAllMr() : false;
        $isRep = $currentUser ? ($currentUser->isMedicalRep() && !$isManager) : false;

        $scheduledVisit = ScheduledVisit::with(['contact.classification', 'assignment', 'cycle'])->findOrFail($id);

        if ($isRep && (int)$scheduledVisit->mr_id !== (int)$currentUser->id) {
            return $this->respondError(app()->getLocale() === 'ar' ? 'غير مصرح لك بتعديل مواعيد مناديب آخرين.' : 'Unauthorized to update visits belonging to another representative.', $request);
        }

        $validated = $request->validate([
            'status' => 'required|in:planned,completed,cancelled,in_progress',
            'notes' => 'nullable|string|max:1000',
            'outcome' => 'nullable|string|in:successful,positive,neutral,doctor_interested,order_placed,doctor_busy,cancelled,unsuccessful',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        $oldStatus = $scheduledVisit->status;
        $newStatus = $validated['status'];

        $scheduledVisit->status = $newStatus;
        if (!empty($validated['notes'])) {
            $scheduledVisit->notes = $validated['notes'];
        }
        $scheduledVisit->save();

        // If transitioning to completed, ensure a Visit record exists and increments assignment stats
        if ($newStatus === 'completed' && $oldStatus !== 'completed') {
            $cycle = $scheduledVisit->cycle ?: VisitCycle::where('status', 'active')->first() ?: VisitCycle::latest('id')->first();
            $contact = $scheduledVisit->contact;
            $class = $contact?->classification;
            $pointsPerVisit = $class ? (int) $class->points : 3;

            $visit = Visit::firstOrCreate(
                ['scheduled_visit_id' => $scheduledVisit->id],
                [
                    'assignment_id' => $scheduledVisit->assignment_id,
                    'mr_id' => $scheduledVisit->mr_id,
                    'contact_id' => $scheduledVisit->contact_id,
                    'cycle_id' => $cycle?->id,
                    'checkin_at' => $scheduledVisit->scheduled_at ?? now(),
                    'checkout_at' => ($scheduledVisit->scheduled_at ?? now())->copy()->addMinutes(20),
                    'duration_minutes' => 20,
                    'outcome' => $validated['outcome'] ?? 'successful',
                    'notes' => $validated['notes'] ?? 'Completed and verified via CRM Dashboard',
                    'gps_verified' => true,
                    'distance_from_contact_m' => 0,
                    'gps_flag' => 'verified',
                ]
            );

            if (!empty($validated['product_ids'])) {
                $visit->products()->sync($validated['product_ids']);
                $visit->product_id = $validated['product_ids'][0] ?? null;
                $visit->save();
            }

            // Increment assignment
            if ($scheduledVisit->assignment) {
                $scheduledVisit->assignment->increment('visits_done');
                $scheduledVisit->assignment->increment('achieved_points', $pointsPerVisit);
            }
        } elseif ($newStatus === 'cancelled' && $oldStatus === 'completed') {
            // Revert assignment counters if previously completed
            $class = $scheduledVisit->contact?->classification;
            $pointsPerVisit = $class ? (int) $class->points : 3;
            if ($scheduledVisit->assignment) {
                $scheduledVisit->assignment->decrement('visits_done');
                $scheduledVisit->assignment->decrement('achieved_points', $pointsPerVisit);
            }
        }

        $msg = app()->getLocale() === 'ar'
            ? "تم تحديث حالة الموعد إلى ({$newStatus}) بنجاح."
            : "Visit appointment status updated to ({$newStatus}) successfully.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'status' => $newStatus,
            ]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Reschedule an existing visit to a new date and time.
     */
    public function reschedule(Request $request, int $id)
    {
        $currentUser = auth()->user();
        $isManager = $currentUser ? $currentUser->canManageAllMr() : false;
        $isRep = $currentUser ? ($currentUser->isMedicalRep() && !$isManager) : false;

        $scheduledVisit = ScheduledVisit::with('contact', 'representative')->findOrFail($id);

        if ($isRep && (int)$scheduledVisit->mr_id !== (int)$currentUser->id) {
            return $this->respondError(app()->getLocale() === 'ar' ? 'غير مصرح لك بإعادة جدولة مواعيد مناديب آخرين.' : 'Unauthorized to reschedule visits belonging to another representative.', $request);
        }

        $validated = $request->validate([
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'required|string',
            'notes' => 'nullable|string|max:500',
        ]);

        $dateTimeStr = $validated['scheduled_date'] . ' ' . $validated['scheduled_time'];
        $newScheduledAt = Carbon::parse($dateTimeStr);

        $scheduledVisit->scheduled_at = $newScheduledAt;
        if (!empty($validated['notes'])) {
            $scheduledVisit->notes = $validated['notes'];
        }
        $scheduledVisit->status = 'planned';
        $scheduledVisit->save();

        $msg = app()->getLocale() === 'ar'
            ? "تمت إعادة جدولة موعد الطبيب ({$scheduledVisit->contact?->name}) إلى " . $newScheduledAt->format('Y-m-d H:i') . " بنجاح."
            : "Visit for Dr. {$scheduledVisit->contact?->name} rescheduled to " . $newScheduledAt->format('Y-m-d H:i') . " successfully.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'new_datetime' => $newScheduledAt->format('Y-m-d H:i'),
            ]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Cancel or delete a scheduled visit.
     */
    public function deleteSchedule(Request $request, int $id)
    {
        $currentUser = auth()->user();
        $isManager = $currentUser ? $currentUser->canManageAllMr() : false;
        $isRep = $currentUser ? ($currentUser->isMedicalRep() && !$isManager) : false;

        $scheduledVisit = ScheduledVisit::findOrFail($id);

        if ($isRep && (int)$scheduledVisit->mr_id !== (int)$currentUser->id) {
            return $this->respondError(app()->getLocale() === 'ar' ? 'غير مصرح لك بإلغاء مواعيد مناديب آخرين.' : 'Unauthorized to cancel visits belonging to another representative.', $request);
        }

        $scheduledVisit->update(['status' => 'cancelled']);

        $msg = app()->getLocale() === 'ar' ? 'تم إلغاء الموعد المجدول بنجاح.' : 'Scheduled visit cancelled successfully.';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
            ]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Bulk Schedule visits for multiple doctors / dates at once.
     */
    public function bulkSchedule(Request $request)
    {
        $currentUser = auth()->user();
        $isManager = $currentUser ? $currentUser->canManageAllMr() : false;
        $isRep = $currentUser ? ($currentUser->isMedicalRep() && !$isManager) : false;

        $validated = $request->validate([
            'mr_id' => $isRep ? 'nullable' : 'required|exists:users,id',
            'contact_ids' => 'required|array|min:1',
            'contact_ids.*' => 'exists:mr_contacts,id',
            'scheduled_date' => 'required|date',
            'start_time' => 'required|string',
            'interval_minutes' => 'nullable|integer|min:15|max:240',
            'cycle_id' => 'nullable|exists:mr_visit_cycles,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $mrId = $isRep ? (int) $currentUser->id : (int) ($validated['mr_id'] ?? $currentUser->id);
        $contactIds = array_values(array_unique(array_map('intval', $validated['contact_ids'])));
        $baseDate = $validated['scheduled_date'];
        $startTime = $validated['start_time'];
        $interval = (int) ($validated['interval_minutes'] ?? 45);

        $cycle = null;
        if (!empty($validated['cycle_id'])) {
            $cycle = VisitCycle::find($validated['cycle_id']);
        }
        if (!$cycle) {
            $cycle = VisitCycle::where('status', 'active')
                ->where('start_date', '<=', $baseDate)
                ->where('end_date', '>=', $baseDate)
                ->first() ?: VisitCycle::where('status', 'active')->first() ?: VisitCycle::latest('id')->first();
        }

        $createdCount = 0;
        $currentSlot = Carbon::parse($baseDate . ' ' . $startTime);

        DB::beginTransaction();
        try {
            foreach ($contactIds as $contactId) {
                $contact = Contact::find($contactId);
                if (!$contact) {
                    continue;
                }

                // Check quota per classification
                $quota = $contact->getVisitQuotaStatus($cycle->id);
                if (!$quota['can_schedule']) {
                    continue;
                }

                $class = $contact->classification;
                $targetVisits = $class ? (int) $class->required_visits : 1;
                $targetPoints = $class ? ((int) $class->points * $targetVisits) : 3;

                $assignment = ContactAssignment::firstOrCreate(
                    [
                        'cycle_id' => $cycle->id,
                        'mr_id' => $mrId,
                        'contact_id' => $contactId,
                    ],
                    [
                        'target_visits' => $targetVisits,
                        'target_points' => $targetPoints,
                        'is_active' => true,
                    ]
                );

                ScheduledVisit::create([
                    'assignment_id' => $assignment->id,
                    'mr_id' => $mrId,
                    'contact_id' => $contactId,
                    'cycle_id' => $cycle->id,
                    'scheduled_at' => $currentSlot->copy(),
                    'status' => 'planned',
                    'notes' => $validated['notes'] ?? 'Bulk Scheduled from MR Command Hub',
                ]);

                $createdCount++;
                $currentSlot->addMinutes($interval);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Bulk schedule error: " . $e->getMessage());
            return $this->respondError(app()->getLocale() === 'ar' ? 'حدث خطأ أثناء الجدولة الجماعية.' : 'Error during bulk scheduling.', $request);
        }

        $rep = User::find($mrId);
        $msg = app()->getLocale() === 'ar'
            ? "تم إنشاء ({$createdCount}) مواعيد مجدولة بنجاح للمندوب ({$rep?->name}) ليوم {$baseDate}."
            : "Successfully generated ({$createdCount}) scheduled visits for rep {$rep?->name} on {$baseDate}.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'count' => $createdCount,
                'message' => $msg,
            ]);
        }

        return redirect()->route('admin.mr.dashboard', [
            'date' => $baseDate,
            'mr_id' => $mrId,
        ])->with('success', $msg);
    }

    /**
     * Get Complete 360 Degree Dossier for a Medical Representative.
     */
    public function repDetails(Request $request, int $id): JsonResponse
    {
        $currentUser = auth()->user();
        $isManager = $currentUser ? $currentUser->canManageAllMr() : false;
        $isRep = $currentUser ? ($currentUser->isMedicalRep() && !$isManager) : false;

        if ($isRep && (int)$id !== (int)$currentUser->id) {
            return response()->json([
                'success' => false,
                'message' => app()->getLocale() === 'ar' ? 'غير مصرح لك بالاطلاع على ملفات مناديب آخرين.' : 'Unauthorized to view details of other representatives.',
            ], 403);
        }

        $rep = User::with(['area', 'city', 'country', 'role'])->findOrFail($id);
        $cycleId = $request->integer('cycle_id');
        $activeCycle = $cycleId ? VisitCycle::find($cycleId) : (VisitCycle::where('status', 'active')->first() ?? VisitCycle::latest('id')->first());
        $cycle = $activeCycle;

        // Assignments with doctors
        $assignments = ContactAssignment::where('mr_id', $rep->id)
            ->when($cycle, fn ($q) => $q->where('cycle_id', $cycle->id))
            ->with(['contact.specialty', 'contact.classification', 'contact.city', 'contact.area'])
            ->get();

        // Today's Agenda
        $todayVisits = ScheduledVisit::where('mr_id', $rep->id)
            ->whereDate('scheduled_at', now()->toDateString())
            ->with(['contact.specialty', 'contact.classification', 'visit.products'])
            ->orderBy('scheduled_at')
            ->get();

        // Recent executed visits with GPS
        $recentVisits = Visit::where('mr_id', $rep->id)
            ->with(['contact.specialty', 'products'])
            ->latest('checkin_at')
            ->limit(15)
            ->get();

        // Performance snapshot
        $snapshot = null;
        if ($cycle) {
            $snapshot = RepPerformanceSnapshot::where('mr_id', $rep->id)
                ->where('cycle_id', $cycle->id)
                ->first();
        }

        $totalAssigned = $assignments->count();
        $targetVisits = (int) $assignments->sum('target_visits');
        $visitsDone = (int) $assignments->sum('visits_done');
        $targetPoints = (int) $assignments->sum('target_points');
        $achievedPoints = (int) $assignments->sum('achieved_points');
        $uniqueVisited = $assignments->where('visits_done', '>', 0)->count();
        $coveragePct = $totalAssigned > 0 ? round(($uniqueVisited / $totalAssigned) * 100, 1) : 0;
        $compliancePct = $targetVisits > 0 ? round(($visitsDone / $targetVisits) * 100, 1) : 0;

        return response()->json([
            'success' => true,
            'rep' => [
                'id' => $rep->id,
                'name' => $rep->name,
                'email' => $rep->email,
                'phone' => $rep->phone,
                'territory' => $rep->territory_label ?? '—',
                'area' => $rep->area?->name ?? '—',
                'city' => $rep->city?->name ?? '—',
                'country' => $rep->country?->name ?? '—',
                'status' => $rep->status ?? 'active',
                'cycle_name' => $cycle?->name ?? '—',
                'stats' => [
                    'total_assigned_doctors' => $totalAssigned,
                    'unique_visited_doctors' => $uniqueVisited,
                    'coverage_pct' => $coveragePct,
                    'target_visits' => $targetVisits,
                    'visits_done' => $visitsDone,
                    'compliance_pct' => $compliancePct,
                    'target_points' => $targetPoints,
                    'achieved_points' => $achievedPoints,
                    'points_pct' => $targetPoints > 0 ? round(($achievedPoints / $targetPoints) * 100, 1) : 0,
                    'today_scheduled_count' => $todayVisits->count(),
                    'today_completed_count' => $todayVisits->where('status', 'completed')->count(),
                ],
                'assignments' => $assignments,
                'today_agenda' => $todayVisits,
                'recent_visits' => $recentVisits,
                'snapshot' => $snapshot,
            ],
        ]);
    }

    /**
     * Direct Visit Recording Logger from Dashboard.
     */
    public function quickRecordVisit(Request $request)
    {
        $currentUser = auth()->user();
        $isManager = $currentUser ? $currentUser->canManageAllMr() : false;
        $isRep = $currentUser ? ($currentUser->isMedicalRep() && !$isManager) : false;

        $validated = $request->validate([
            'mr_id' => $isRep ? 'nullable' : 'required|exists:users,id',
            'contact_id' => 'required|exists:mr_contacts,id',
            'visited_at' => 'required|date',
            'outcome' => 'required|string|in:successful,positive,neutral,doctor_interested,order_placed,doctor_busy,cancelled,unsuccessful',
            'notes' => 'required|string|min:3|max:1500',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
        ]);

        $mrId = $isRep ? (int) $currentUser->id : (int) ($validated['mr_id'] ?? $currentUser->id);
        $contactId = (int) $validated['contact_id'];
        $visitedAt = Carbon::parse($validated['visited_at']);
        $productIds = array_values(array_unique(array_map('intval', $validated['product_ids'])));

        $cycle = VisitCycle::where('status', 'active')
            ->where('start_date', '<=', $visitedAt->toDateString())
            ->where('end_date', '>=', $visitedAt->toDateString())
            ->first() ?: VisitCycle::where('status', 'active')->first() ?: VisitCycle::latest('id')->first();

        $contact = Contact::findOrFail($contactId);
        $class = $contact->classification;
        $targetVisits = $class ? (int) $class->required_visits : 1;
        $targetPoints = $class ? ((int) $class->points * $targetVisits) : 3;

        $assignment = ContactAssignment::firstOrCreate(
            [
                'cycle_id' => $cycle->id,
                'mr_id' => $mrId,
                'contact_id' => $contactId,
            ],
            [
                'target_visits' => $targetVisits,
                'target_points' => $targetPoints,
                'is_active' => true,
            ]
        );

        $visit = Visit::create([
            'assignment_id' => $assignment->id,
            'mr_id' => $mrId,
            'contact_id' => $contactId,
            'cycle_id' => $cycle->id,
            'checkin_at' => $visitedAt,
            'checkout_at' => $visitedAt->copy()->addMinutes(25),
            'duration_minutes' => 25,
            'outcome' => $validated['outcome'],
            'notes' => $validated['notes'],
            'product_id' => $productIds[0] ?? null,
            'gps_verified' => true,
            'distance_from_contact_m' => 0,
            'gps_flag' => 'verified',
        ]);

        $visit->products()->sync($productIds);

        // Mark any matching planned scheduled visit for today as completed
        ScheduledVisit::where('mr_id', $mrId)
            ->where('contact_id', $contactId)
            ->whereDate('scheduled_at', $visitedAt->toDateString())
            ->where('status', 'planned')
            ->update([
                'status' => 'completed',
            ]);

        $assignment->increment('visits_done');
        $pointsPerVisit = $class ? (int) $class->points : 3;
        $assignment->increment('achieved_points', $pointsPerVisit);

        $msg = app()->getLocale() === 'ar'
            ? "تم تسجيل الزيارة الميدانية المباشرة بنجاح وحساب النقاط واعتمادها بالكامل."
            : "Direct field visit recorded and points credited successfully.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'visit' => $visit->load('products', 'contact'),
            ]);
        }

        return redirect()->route('admin.mr.dashboard', [
            'date' => $visitedAt->toDateString(),
            'mr_id' => $mrId,
        ])->with('success', $msg);
    }

    protected function respondError(string $message, Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => false, 'message' => $message], 422);
        }
        return back()->with('error', $message);
    }
}
