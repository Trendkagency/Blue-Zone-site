<?php

namespace App\Http\Controllers\Admin\Mr;

use App\Http\Controllers\Controller;
use App\Models\Mr\Contact;
use App\Models\Mr\ContactAssignment;
use App\Models\Mr\ScheduledVisit;
use App\Models\Mr\Visit;
use App\Models\Mr\VisitCycle;
use App\Models\Product;
use App\Models\User;
use App\Services\FcmService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class VisitController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->input('tab', 'today');
        $selectedDate = $request->input('date', now()->toDateString());
        $selectedMrId = $request->filled('mr_id') ? $request->integer('mr_id') : null;

        $cycles = VisitCycle::latest('start_date')->get();
        $selectedCycleId = $request->integer('cycle_id');

        $medicalReps = User::whereHas('role', function ($q) {
            $q->where('name', 'mr');
        })->orWhere('role_id', 2)->select('id', 'name')->get();

        $availableDoctors = Contact::where('is_active', true)
            ->with(['specialty', 'classification', 'city'])
            ->select('id', 'name', 'code', 'hospital_clinic_name', 'specialty_id', 'classification_id', 'city_id')
            ->orderBy('name')
            ->get();

        $products = Product::where('is_active', true)
            ->select('id', 'name_en', 'name_ar')
            ->orderBy('name_en')
            ->get();

        // 1. Today's / Selected Date Scheduled Visits Query
        $todayQuery = ScheduledVisit::with([
            'representative',
            'contact.specialty',
            'contact.classification',
            'contact.city',
            'cycle',
            'visit.products',
            'visit.product',
        ])->whereDate('scheduled_at', $selectedDate);

        if ($selectedMrId) {
            $todayQuery->where('mr_id', $selectedMrId);
        }
        if ($request->filled('status')) {
            $todayQuery->where('status', $request->status);
        }

        $todayVisits = $todayQuery->orderBy('scheduled_at')->get();

        $todayKpi = [
            'total' => $todayVisits->count(),
            'planned' => $todayVisits->where('status', 'planned')->count(),
            'completed' => $todayVisits->where('status', 'completed')->count(),
            'cancelled' => $todayVisits->where('status', 'cancelled')->count(),
        ];

        // 2. Executed Visits History Query
        $query = Visit::with([
            'representative',
            'contact.specialty',
            'contact.classification',
            'contact.city',
            'cycle',
            'products',
            'product',
        ]);

        if ($selectedCycleId) {
            $query->where('cycle_id', $selectedCycleId);
        }

        if ($selectedMrId) {
            $query->where('mr_id', $selectedMrId);
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

        if ($request->has('export')) {
            return $this->exportVisits($query->get(), $request, $cycles);
        }

        $visits = $query->latest('checkin_at')->paginate(20)->withQueryString();

        return view('admin.mr.visits.index', compact(
            'visits',
            'todayVisits',
            'todayKpi',
            'activeTab',
            'selectedDate',
            'cycles',
            'selectedCycleId',
            'medicalReps',
            'availableDoctors',
            'products'
        ));
    }

    public function schedule(Request $request)
    {
        $validated = $request->validate([
            'mr_id' => 'required|exists:users,id',
            'contact_id' => 'required|exists:mr_contacts,id',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'required|string',
            'notes' => 'nullable|string|max:500',
            'cycle_id' => 'nullable|exists:mr_visit_cycles,id',
        ]);

        $mrId = (int) $validated['mr_id'];
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
            return back()->with('error', app()->getLocale() === 'ar' ? 'لا توجد دورة زيارات نشطة.' : 'No active visit cycle found.');
        }

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

        $scheduledVisit = ScheduledVisit::create([
            'assignment_id' => $assignment->id,
            'mr_id' => $mrId,
            'contact_id' => $contactId,
            'cycle_id' => $cycle->id,
            'scheduled_at' => $scheduledAt,
            'status' => 'planned',
            'notes' => $validated['notes'] ?? 'Scheduled by administrator',
        ]);

        $user = User::find($mrId);
        try {
            if ($user && method_exists($user, 'getActiveFcmTokens')) {
                $tokens = $user->getActiveFcmTokens();
                if (!empty($tokens)) {
                    $fcm = FcmService::getInstance();
                    $fcm->sendToTokens(
                        $tokens,
                        'New Visit Scheduled',
                        "A new visit has been scheduled with Dr. {$contact->name} on " . $scheduledAt->format('Y-m-d H:i'),
                        ['type' => 'scheduled_visit', 'visit_id' => (string) $scheduledVisit->id]
                    );
                }
            }
        } catch (\Throwable $e) {
            Log::warning("FCM alert failed: " . $e->getMessage());
        }

        $msg = app()->getLocale() === 'ar'
            ? "تمت جدولة الزيارة بنجاح للمندوب ({$user?->name}) مع الطبيب ({$contact->name}) ليوم " . $scheduledAt->format('Y-m-d H:i') . " وتظهر فوراً في أجندة المندوب بالبوابة."
            : "Visit scheduled successfully for rep {$user?->name} with Dr. {$contact->name} on " . $scheduledAt->format('Y-m-d H:i') . " and is now active on the MR Portal agenda.";

        return redirect()->route('admin.mr.visits.index', [
            'tab' => 'today',
            'date' => $scheduledAt->toDateString(),
            'mr_id' => $mrId
        ])->with('success', $msg);
    }

    public function recordDirectVisit(Request $request)
    {
        $validated = $request->validate([
            'mr_id' => 'required|exists:users,id',
            'contact_id' => 'required|exists:mr_contacts,id',
            'scheduled_visit_id' => 'nullable|exists:mr_scheduled_visits,id',
            'visited_at' => 'required|date',
            'outcome' => 'required|string|in:successful,positive,neutral,doctor_interested,order_placed,doctor_busy,cancelled,unsuccessful',
            'notes' => 'required|string|min:3|max:1500',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
        ]);

        $mrId = (int) $validated['mr_id'];
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

        $scheduledVisitId = $validated['scheduled_visit_id'] ?? null;

        $visit = Visit::create([
            'scheduled_visit_id' => $scheduledVisitId,
            'assignment_id' => $assignment->id,
            'mr_id' => $mrId,
            'contact_id' => $contactId,
            'cycle_id' => $cycle->id,
            'checkin_at' => $visitedAt,
            'checkout_at' => $visitedAt->copy()->addMinutes(20),
            'duration_minutes' => 20,
            'outcome' => $validated['outcome'],
            'notes' => $validated['notes'],
            'product_id' => $productIds[0] ?? null,
            'gps_verified' => true,
            'distance_from_contact_m' => 0,
            'gps_flag' => 'verified',
        ]);

        $visit->products()->sync($productIds);

        if ($scheduledVisitId) {
            ScheduledVisit::where('id', $scheduledVisitId)->update(['status' => 'completed']);
        }

        $assignment->increment('visits_done');
        $pointsPerVisit = $class ? (int) $class->points : 3;
        $assignment->increment('achieved_points', $pointsPerVisit);

        $msg = app()->getLocale() === 'ar'
            ? "تم تسجيل زيارة المندوب بنجاح واعتمادها في سجل الزيارات وحساب النقاط."
            : "Visit recorded and verified successfully on behalf of the Medical Representative.";

        return redirect()->route('admin.mr.visits.index', ['tab' => 'today', 'date' => $visitedAt->toDateString()])
            ->with('success', $msg);
    }

    public function cancelSchedule(int $id)
    {
        $scheduledVisit = ScheduledVisit::findOrFail($id);
        $scheduledVisit->update(['status' => 'cancelled']);

        return back()->with('success', app()->getLocale() === 'ar' ? 'تم إلغاء الزيارة المجدولة بنجاح.' : 'Scheduled visit cancelled successfully.');
    }

    protected function exportVisits($visits, Request $request, $cycles)
    {
        $exporter = app(\App\Services\Mr\Export\MrTableExcelExporter::class);

        $selectedCycleId = $request->integer('cycle_id');
        $selectedCycle = $cycles->firstWhere('id', $selectedCycleId);
        $repUser = $request->filled('mr_id') ? User::find($request->mr_id) : null;

        $metadata = [
            'Visit Cycle' => $selectedCycle ? $selectedCycle->name : 'All Cycles',
            'Medical Rep' => $repUser ? $repUser->name : 'All Representatives',
            'GPS Filter' => $request->filled('gps_status') ? ucfirst($request->gps_status) : 'All Records',
            'Outcome Filter' => $request->filled('outcome') ? ucfirst(str_replace('_', ' ', $request->outcome)) : 'All Outcomes',
            'Total Audited Visits' => $visits->count(),
        ];

        $totalVisits = $visits->count();
        $verifiedCount = $visits->where('gps_verified', true)->count();
        $flaggedCount = $visits->where('gps_verified', false)->count();
        $completedCheckouts = $visits->whereNotNull('checkout_at')->count();
        $positiveOutcomes = $visits->filter(fn($v) => in_array($v->outcome, ['successful', 'positive', 'order_placed', 'doctor_interested']))->count();

        $kpiCards = [
            ['label' => 'Total Audited Visits', 'val' => (string)$totalVisits, 'bg' => 'F1F5F9', 'fg' => '0F172A', 'border' => 'CBD5E1'],
            ['label' => 'GPS Verified (In Radius)', 'val' => (string)$verifiedCount, 'bg' => 'DCFCE7', 'fg' => '15803D', 'border' => '86EFAC'],
            ['label' => 'GPS Flagged (Outside)', 'val' => (string)$flaggedCount, 'bg' => 'FEE2E2', 'fg' => 'B91C1C', 'border' => 'FCA5A5'],
            ['label' => 'Completed Check-outs', 'val' => (string)$completedCheckouts, 'bg' => 'E0F2FE', 'fg' => '0369A1', 'border' => 'BAE6FD'],
            ['label' => 'Positive Outcomes', 'val' => (string)$positiveOutcomes, 'bg' => 'EDE9FE', 'fg' => '6D28D9', 'border' => 'C4B5FD'],
        ];

        $columns = [
            ['key' => fn($v) => '#' . $v->id, 'header' => 'Visit ID', 'width' => 12, 'align' => Alignment::HORIZONTAL_CENTER],
            ['key' => fn($v) => $v->representative?->name ?? 'Rep #' . $v->mr_id, 'header' => 'Medical Rep', 'width' => 22, 'align' => Alignment::HORIZONTAL_LEFT],
            ['key' => fn($v) => $v->contact?->name ?? 'Doctor #' . $v->contact_id, 'header' => 'Doctor / Contact Name', 'width' => 24, 'align' => Alignment::HORIZONTAL_LEFT],
            ['key' => fn($v) => $v->contact?->specialty?->name ?? 'General', 'header' => 'Specialty', 'width' => 16, 'align' => Alignment::HORIZONTAL_CENTER],
            [
                'key' => fn($v) => $v->contact?->classification?->code ?? 'C',
                'header' => 'Class',
                'width' => 10,
                'type' => 'badge',
                'align' => Alignment::HORIZONTAL_CENTER,
                'badgeColors' => fn($val) => match (strtoupper((string)$val)) {
                    'A+' => ['bg' => 'FEF3C7', 'fg' => '92400E'],
                    'A' => ['bg' => 'E0F2FE', 'fg' => '0369A1'],
                    'B' => ['bg' => 'F1F5F9', 'fg' => '334155'],
                    default => ['bg' => 'F8FAFC', 'fg' => '64748B'],
                }
            ],
            ['key' => fn($v) => $v->contact?->hospital_clinic_name ?: '—', 'header' => 'Hospital / Facility', 'width' => 24, 'align' => Alignment::HORIZONTAL_LEFT],
            ['key' => fn($v) => $v->cycle?->name ?? '—', 'header' => 'Visit Cycle', 'width' => 20, 'align' => Alignment::HORIZONTAL_LEFT],
            ['key' => fn($v) => $v->checkin_at ? $v->checkin_at->format('Y-m-d H:i') : '—', 'header' => 'Check-In Time', 'width' => 18, 'align' => Alignment::HORIZONTAL_CENTER],
            ['key' => fn($v) => $v->checkout_at ? $v->checkout_at->format('Y-m-d H:i') : 'In Progress', 'header' => 'Check-Out Time', 'width' => 18, 'align' => Alignment::HORIZONTAL_CENTER],
            ['key' => fn($v) => (int)$v->duration_minutes, 'header' => 'Duration (Min)', 'width' => 15, 'type' => 'number', 'align' => Alignment::HORIZONTAL_CENTER],
            [
                'key' => fn($v) => $v->gps_verified ? 'Verified' : 'Flagged',
                'header' => 'GPS Status',
                'width' => 14,
                'type' => 'badge',
                'align' => Alignment::HORIZONTAL_CENTER,
                'badgeColors' => fn($val) => $val === 'Verified' ? ['bg' => 'DCFCE7', 'fg' => '15803D'] : ['bg' => 'FEE2E2', 'fg' => 'B91C1C']
            ],
            ['key' => fn($v) => $v->distance_from_contact_m ? (int)$v->distance_from_contact_m : 0, 'header' => 'Distance (m)', 'width' => 14, 'type' => 'number', 'align' => Alignment::HORIZONTAL_CENTER],
            [
                'key' => fn($v) => $v->outcome ? ucfirst(str_replace('_', ' ', $v->outcome)) : 'Pending',
                'header' => 'Visit Outcome',
                'width' => 18,
                'type' => 'badge',
                'align' => Alignment::HORIZONTAL_CENTER,
                'badgeColors' => fn($val) => match (strtolower((string)$val)) {
                    'successful', 'positive', 'order_placed', 'doctor_interested' => ['bg' => 'DCFCE7', 'fg' => '15803D'],
                    'doctor_busy', 'rescheduled', 'follow_up_needed' => ['bg' => 'FEF9C3', 'fg' => 'A16207'],
                    'doctor_refused', 'cancelled', 'not_available' => ['bg' => 'FEE2E2', 'fg' => 'B91C1C'],
                    default => ['bg' => 'F1F5F9', 'fg' => '334155'],
                }
            ],
            [
                'key' => function ($v) {
                    $prods = $v->products->pluck('name')->all();
                    if (empty($prods) && $v->product) {
                        $prods = [$v->product->name];
                    }
                    return !empty($prods) ? implode(', ', $prods) : 'None';
                },
                'header' => 'Discussed Products',
                'width' => 28,
                'align' => Alignment::HORIZONTAL_LEFT
            ],
            ['key' => fn($v) => $v->notes ?: '—', 'header' => 'Visit Notes', 'width' => 32, 'align' => Alignment::HORIZONTAL_LEFT],
        ];

        return $exporter->export(
            'Executed Field Visits & GPS Audit Log',
            $metadata,
            $kpiCards,
            $columns,
            $visits,
            'visits-gps-audit-' . date('Y-m-d') . '.xlsx',
            [
                'col' => 'A',
                'mergeTo' => 'I',
                'label' => 'PORTFOLIO TOTALS',
                'align' => Alignment::HORIZONTAL_RIGHT,
            ]
        );
    }

    public function show(int $id)
    {
        $visit = Visit::with([
            'representative',
            'contact.specialty',
            'contact.classification',
            'contact.city',
            'cycle',
            'products',
            'product',
        ])->findOrFail($id);

        return view('admin.mr.visits.show', compact('visit'));
    }
}
