<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\Role;
use App\Models\User;
use App\Services\Mr\AreaTerritoryService;
use App\View\ViewModels\RoleViewModel;
use App\View\ViewModels\UserViewModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $roleFilter = $request->query('role_id');
        $isTrashed = $status === 'trashed';

        $query = User::with(['role', 'country', 'city', 'area', 'employee']);

        if ($isTrashed) {
            $query->onlyTrashed();
        } elseif ($status && in_array($status, ['active', 'inactive', 'suspended'])) {
            $query->where('status', $status);
        }

        if ($roleFilter) {
            $query->where('role_id', $roleFilter);
        }

        $search = $request->query('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $trashedCount = User::onlyTrashed()->count();
        $activeCount = User::where('status', 'active')->count();
        $totalDbCount = User::withTrashed()->count();
        $dbUsers = $query->latest()->paginate(15)->withQueryString();

        if ($totalDbCount > 0) {
            $users = $dbUsers->map(function ($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'phone' => $u->phone,
                    'role' => $u->role?->name ?? 'Admin',
                    'role_id' => $u->role_id,
                    'status' => $u->status ?? 'active',
                    'avatar' => $u->avatar ?? null,
                    'avatar_url' => $u->avatar_url,
                    'country_id' => $u->country_id,
                    'city_id' => $u->city_id,
                    'area_id' => $u->area_id,
                    'country' => $u->country?->name,
                    'city' => $u->city?->name,
                    'area' => $u->area?->name,
                    'territory_label' => $u->territory_label,
                    'employee_id' => $u->employee?->id,
                    'last_login' => $u->updated_at?->diffForHumans() ?? 'Recently',
                    'deleted_at' => $u->deleted_at,
                ];
            })->toArray();
            $currentPage = $dbUsers->currentPage();
            $totalPages = $dbUsers->lastPage();
        } else {
            $users = UserViewModel::all();
            $currentPage = 1;
            $totalPages = 1;
        }

        $dbRoles = Role::all();
        $roles = $dbRoles->isNotEmpty() ? $dbRoles->toArray() : RoleViewModel::all();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => $roles,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'totalCount' => $totalDbCount > 0 ? $dbUsers->total() : count($users),
            'trashedCount' => $trashedCount,
            'activeCount' => $activeCount,
            'isTrashed' => $isTrashed,
            'currentStatus' => $status,
            'currentRole' => $roleFilter,
        ]);
    }

    public function create(): View
    {
        $dbRoles = Role::all();
        $roles = $dbRoles->isNotEmpty() ? $dbRoles->toArray() : RoleViewModel::all();
        $countries = Country::where('is_active', true)->orderBy('sort_order')->orderBy('name_en')->get();

        return view('admin.users.create', [
            'roles' => $roles,
            'countries' => $countries,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'bio' => 'nullable|string|max:1000',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'nullable|integer|exists:roles,id',
            'country_id' => 'nullable|integer|exists:countries,id',
            'city_id' => 'nullable|integer|exists:cities,id',
            'area_id' => 'nullable|integer|exists:areas,id',
            'status' => 'required|string|in:active,inactive,suspended',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'] ?? null,
            'country_id' => $validated['country_id'] ?? null,
            'city_id' => $validated['city_id'] ?? null,
            'area_id' => $validated['area_id'] ?? null,
            'status' => $validated['status'],
        ]);

        if (!empty($validated['area_id'])) {
            AreaTerritoryService::getInstance()->assignRepToTerritory(
                $user,
                (int)$validated['area_id'],
                !empty($validated['city_id']) ? (int)$validated['city_id'] : null,
                !empty($validated['country_id']) ? (int)$validated['country_id'] : null
            );
        }

        return redirect()->route('admin.users.index')
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم إضافة المستخدم [{$user->name}] وتعيين صلاحياته بنجاح!" 
                : "User [{$user->name}] added successfully!");
    }

    public function show(int $id): View
    {
        $dbUser = User::withTrashed()
            ->with([
                'role',
                'country',
                'city',
                'area',
                'employee.department',
                'employee.position',
                'employee.manager',
                'employee.workSchedule',
                'employee.location',
            ])
            ->findOrFail($id);

        $employee = $dbUser->employee;
        $attendanceRecords = collect();
        $attendanceStats = [
            'total_days' => 0,
            'present' => 0,
            'late' => 0,
            'absent' => 0,
            'half_day' => 0,
            'worked_hours' => 0,
            'overtime_hours' => 0,
            'attendance_rate' => 100,
        ];
        $todayAttendance = null;

        if ($employee) {
            $attendanceRecords = \App\Models\AttendanceRecord::where('employee_id', $employee->id)
                ->latest('attendance_date')
                ->take(30)
                ->get();

            $totalLogged = \App\Models\AttendanceRecord::where('employee_id', $employee->id)->count();
            $presentCount = \App\Models\AttendanceRecord::where('employee_id', $employee->id)->where('status', 'present')->count();
            $lateCount = \App\Models\AttendanceRecord::where('employee_id', $employee->id)->where('status', 'late')->count();
            $absentCount = \App\Models\AttendanceRecord::where('employee_id', $employee->id)->where('status', 'absent')->count();
            $workedMins = (int)\App\Models\AttendanceRecord::where('employee_id', $employee->id)->sum('worked_minutes');
            $overtimeMins = (int)\App\Models\AttendanceRecord::where('employee_id', $employee->id)->sum('overtime_minutes');

            $attendanceStats = [
                'total_days' => $totalLogged,
                'present' => $presentCount,
                'late' => $lateCount,
                'absent' => $absentCount,
                'worked_hours' => round($workedMins / 60, 1),
                'overtime_hours' => round($overtimeMins / 60, 1),
                'attendance_rate' => $totalLogged > 0 ? round((($presentCount + $lateCount) / $totalLogged) * 100, 1) : 100,
            ];

            $todayAttendance = \App\Models\AttendanceRecord::where('employee_id', $employee->id)
                ->where('attendance_date', now()->toDateString())
                ->first();
        }

        // Role-Based Operations & Insights
        $roleName = strtolower($dbUser->role?->name ?? '');
        $roleId = (int)$dbUser->role_id;
        $roleType = 'general';

        if ($dbUser->isMedicalRep() || $roleId === 1 || in_array($roleName, ['mr', 'medical_rep', 'medical_representative'])) {
            $roleType = 'mr';
        } elseif ($roleId === 5 || str_contains($roleName, 'sales')) {
            $roleType = 'sales';
        } elseif ($roleId === 6 || str_contains($roleName, 'inventory') || str_contains($roleName, 'warehouse')) {
            $roleType = 'inventory';
        } elseif ($roleId === 3 || $roleId === 4 || str_contains($roleName, 'admin') || str_contains($roleName, 'manager')) {
            $roleType = 'manager';
        }

        $roleData = [];

        if ($roleType === 'mr') {
            $assignedDocsCount = \App\Models\Mr\ContactAssignment::where('mr_id', $dbUser->id)->count();
            $totalVisits = \App\Models\Mr\Visit::where('mr_id', $dbUser->id)->count();
            $gpsVerifiedVisits = \App\Models\Mr\Visit::where('mr_id', $dbUser->id)->where('gps_verified', true)->count();
            $todayScheduled = \App\Models\Mr\ScheduledVisit::where('mr_id', $dbUser->id)->whereDate('scheduled_at', now()->toDateString())->count();
            $recentVisits = \App\Models\Mr\Visit::with(['contact.specialty', 'contact.classification', 'cycle'])
                ->where('mr_id', $dbUser->id)
                ->latest('checkin_at')
                ->take(5)
                ->get();
            $activeCycle = \App\Models\Mr\VisitCycle::where('status', 'active')->latest()->first();
            $scorecard = \App\Models\Mr\RepPerformanceSnapshot::where('mr_id', $dbUser->id)->latest('id')->first();

            $roleData = [
                'assigned_doctors' => $assignedDocsCount,
                'total_visits' => $totalVisits,
                'gps_verified_visits' => $gpsVerifiedVisits,
                'gps_rate' => $totalVisits > 0 ? round(($gpsVerifiedVisits / $totalVisits) * 100, 1) : 100,
                'today_scheduled' => $todayScheduled,
                'active_cycle' => $activeCycle?->name ?? 'None',
                'achieved_points' => $scorecard?->achieved_points ?? 0,
                'target_points' => $scorecard?->target_points ?? 0,
                'coverage_rate' => $scorecard?->coverage_rate_pct ?? 0,
                'recent_visits' => $recentVisits,
            ];
        } elseif ($roleType === 'sales') {
            $leadsCount = \App\Models\CrmLead::where('owner_id', $dbUser->id)->count();
            $oppsCount = \App\Models\CrmOpportunity::where('owner_id', $dbUser->id)->count();
            $wonOppsCount = \App\Models\CrmOpportunity::where('owner_id', $dbUser->id)->where('status', 'won')->count();
            $pipelineValue = \App\Models\CrmOpportunity::where('owner_id', $dbUser->id)->sum('value');
            $activitiesCount = \App\Models\CrmActivity::where('assigned_to', $dbUser->id)->orWhere('created_by', $dbUser->id)->count();
            $recentLeads = \App\Models\CrmLead::where('owner_id', $dbUser->id)->latest()->take(5)->get();

            $roleData = [
                'leads_count' => $leadsCount,
                'total_leads' => $leadsCount,
                'opportunities_count' => $oppsCount,
                'total_opportunities' => $oppsCount,
                'won_deals_count' => $wonOppsCount,
                'won_opportunities' => $wonOppsCount,
                'pipeline_value' => (float)$pipelineValue,
                'won_revenue' => (float)$pipelineValue,
                'activities_count' => $activitiesCount,
                'recent_leads' => $recentLeads,
            ];
        } elseif ($roleType === 'inventory') {
            $movementsCount = \App\Models\InventoryMovement::where('user', $dbUser->name)->orWhere('user', $dbUser->email)->count();
            $totalProducts = \App\Models\Product::count();
            $recentMovements = \App\Models\InventoryMovement::with('product')->latest('date')->take(5)->get();
            $inwardCount = \App\Models\InventoryMovement::where(function ($q) {
                $q->where('movement_type', 'like', '%in%')->orWhere('movement_type', 'like', '%received%')->orWhere('quantity', '>', 0);
            })->count();

            $roleData = [
                'movements_count' => $movementsCount,
                'total_movements' => $movementsCount,
                'total_products' => $totalProducts,
                'active_products' => $totalProducts,
                'inward_count' => $inwardCount,
                'recent_movements' => $recentMovements,
            ];
        } elseif ($roleType === 'manager') {
            $totalStaff = User::count();
            $activeStaff = User::where('status', 'active')->count();
            $totalEmployees = \App\Models\Employee::count();
            $activeCycles = \App\Models\Mr\VisitCycle::where('status', 'active')->count();

            $roleData = [
                'total_staff' => $totalStaff,
                'active_staff' => $activeStaff,
                'total_employees' => $totalEmployees,
                'active_cycles' => $activeCycles,
            ];
        }

        // Granular Permissions
        $modules = RoleViewModel::modules();
        $actions = ['view', 'create', 'edit', 'delete'];
        $evaluatedPermissions = [];
        foreach ($modules as $modKey => $modLabel) {
            $evaluatedPermissions[$modKey] = [
                'label' => $modLabel,
                'actions' => [],
            ];
            foreach ($actions as $act) {
                $evaluatedPermissions[$modKey]['actions'][$act] = $dbUser->hasPermission("{$modKey}.{$act}");
            }
        }

        $user = [
            'id' => $dbUser->id,
            'name' => $dbUser->name,
            'email' => $dbUser->email,
            'phone' => $dbUser->phone ?? null,
            'bio' => $dbUser->bio ?? null,
            'role' => $dbUser->role?->name ?? 'Staff',
            'role_id' => $dbUser->role_id,
            'role_description' => $dbUser->role?->description ?? '',
            'country' => $dbUser->country?->name,
            'city' => $dbUser->city?->name,
            'area' => $dbUser->area?->name,
            'territory_label' => $dbUser->territory_label,
            'status' => $dbUser->status ?? 'active',
            'avatar' => $dbUser->avatar ?? null,
            'avatar_url' => $dbUser->avatar_url,
            'email_verified_at' => $dbUser->email_verified_at,
            'has_fcm' => !empty($dbUser->fcm_token),
            'fcm_device' => $dbUser->fcm_device_info['device'] ?? ($dbUser->fcm_device_info['platform'] ?? null),
            'created_at' => $dbUser->created_at?->format('d M Y, h:i A') ?? 'N/A',
            'updated_at' => $dbUser->updated_at?->diffForHumans() ?? 'Recently',
            'deleted_at' => $dbUser->deleted_at,
        ];

        return view('admin.users.show', compact(
            'dbUser',
            'user',
            'employee',
            'attendanceRecords',
            'attendanceStats',
            'todayAttendance',
            'roleType',
            'roleData',
            'evaluatedPermissions'
        ));
    }

    public function toggleStatus(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if (auth()->id() === $user->id) {
            return redirect()->back()
                ->with('error', app()->getLocale() === 'ar'
                    ? 'لا يمكنك تعطيل أو تغيير حالة حسابك الشخصي الحالي أثناء تسجيل الدخول.'
                    : 'You cannot suspend or modify your own currently logged-in account.');
        }

        $newStatus = ($user->status === 'active') ? 'suspended' : 'active';
        $user->update(['status' => $newStatus]);

        $msgAr = $newStatus === 'active' 
            ? "تم تنشيط وتفعيل حساب المستخدم [{$user->name}] بنجاح." 
            : "تم إيقاف وتعليق حساب المستخدم [{$user->name}] بنجاح.";
        $msgEn = $newStatus === 'active'
            ? "User [{$user->name}] account has been activated successfully."
            : "User [{$user->name}] account has been suspended.";

        return redirect()->back()
            ->with('success', app()->getLocale() === 'ar' ? $msgAr : $msgEn);
    }

    public function recordAttendance(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $employee = $user->employee;
        if (!$employee) {
            return back()->with('error', app()->getLocale() === 'ar' ? 'لا يوجد ملف وظيفي (HR Employee) مرتبط بهذا المستخدم.' : 'No HR employee record linked to this staff user.');
        }

        $validated = $request->validate([
            'attendance_date' => 'required|date',
            'check_in' => 'nullable|string',
            'check_out' => 'nullable|string',
            'status' => 'required|in:present,late,absent,half_day,on_leave',
            'notes' => 'nullable|string|max:500',
        ]);

        $checkIn = !empty($validated['check_in']) ? $validated['check_in'] : null;
        $checkOut = !empty($validated['check_out']) ? $validated['check_out'] : null;
        $workedMins = 0;
        $lateMins = 0;

        if ($checkIn && $checkOut) {
            $cIn = \Carbon\Carbon::parse($validated['attendance_date'] . ' ' . $checkIn);
            $cOut = \Carbon\Carbon::parse($validated['attendance_date'] . ' ' . $checkOut);
            $workedMins = max(0, $cIn->diffInMinutes($cOut));
        } elseif ($checkIn) {
            $workedMins = 480;
        }

        if ($validated['status'] === 'late') {
            $lateMins = 20;
        }

        \App\Models\AttendanceRecord::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'attendance_date' => $validated['attendance_date'],
            ],
            [
                'check_in' => $checkIn ?: ($validated['status'] === 'absent' ? null : '09:00:00'),
                'check_out' => $checkOut ?: ($validated['status'] === 'absent' ? null : '17:00:00'),
                'worked_minutes' => $validated['status'] === 'absent' ? 0 : ($workedMins ?: 480),
                'late_minutes' => $lateMins,
                'early_leave_minutes' => 0,
                'overtime_minutes' => 0,
                'status' => $validated['status'],
                'source' => 'manual',
                'notes' => $validated['notes'] ?? ('Updated via Staff Profile by ' . auth()->user()->name),
                'approved_by' => auth()->id(),
            ]
        );

        return back()->with('success', app()->getLocale() === 'ar' 
            ? "تم تسجيل وتحديث حضور وانصراف الموظف [{$user->name}] بنجاح!" 
            : "Attendance record updated successfully for [{$user->name}]!");
    }

    public function edit(int $id): View
    {
        $dbUser = User::with(['country', 'city', 'area'])->find($id);

        if ($dbUser) {
            $user = [
                'id' => $dbUser->id,
                'name' => $dbUser->name,
                'email' => $dbUser->email,
                'phone' => $dbUser->phone ?? '',
                'bio' => $dbUser->bio ?? '',
                'role_id' => $dbUser->role_id,
                'country_id' => $dbUser->country_id,
                'city_id' => $dbUser->city_id,
                'area_id' => $dbUser->area_id,
                'territory_label' => $dbUser->territory_label,
                'status' => $dbUser->status ?? 'active',
                'avatar' => $dbUser->avatar ?? 'assets/avatars/user-1.jpg',
            ];

            $cities = $dbUser->country_id 
                ? City::where('country_id', $dbUser->country_id)->where('is_active', true)->orderBy('name_en')->get() 
                : collect();

            $areas = $dbUser->city_id 
                ? Area::where('city_id', $dbUser->city_id)->where('is_active', true)->orderBy('name_en')->get() 
                : collect();
        } else {
            $users = UserViewModel::all();
            $user = null;
            foreach ($users as $u) {
                if ($u['id'] === $id) {
                    $user = $u;
                    break;
                }
            }
            $cities = collect();
            $areas = collect();
        }

        $dbRoles = Role::all();
        $roles = $dbRoles->isNotEmpty() ? $dbRoles->toArray() : RoleViewModel::all();
        $countries = Country::where('is_active', true)->orderBy('sort_order')->orderBy('name_en')->get();

        return view('admin.users.edit', [
            'user' => $user ?? ($users[0] ?? []),
            'roles' => $roles,
            'countries' => $countries,
            'cities' => $cities,
            'areas' => $areas,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:30',
            'bio' => 'nullable|string|max:1000',
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'nullable|integer|exists:roles,id',
            'country_id' => 'nullable|integer|exists:countries,id',
            'city_id' => 'nullable|integer|exists:cities,id',
            'area_id' => 'nullable|integer|exists:areas,id',
            'status' => 'required|string|in:active,inactive,suspended',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'role_id' => $validated['role_id'] ?? null,
            'country_id' => $validated['country_id'] ?? null,
            'city_id' => $validated['city_id'] ?? null,
            'area_id' => $validated['area_id'] ?? null,
            'status' => $validated['status'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        if (!empty($validated['area_id'])) {
            AreaTerritoryService::getInstance()->assignRepToTerritory(
                $user,
                (int)$validated['area_id'],
                !empty($validated['city_id']) ? (int)$validated['city_id'] : null,
                !empty($validated['country_id']) ? (int)$validated['country_id'] : null
            );
        }

        return redirect()->route('admin.users.index')
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم تحديث بيانات المستخدم [{$user->name}] بنجاح!" 
                : "User [{$user->name}] updated successfully!");
    }

    public function destroy(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        // Protect super admin self-deletion
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')
                ->with('error', app()->getLocale() === 'ar'
                    ? 'لا يمكنك حذف حسابك الشخصي الحالي أثناء تسجيل الدخول.'
                    : 'You cannot delete your own logged-in account.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم نقل المستخدم [{$name}] إلى سلة المحذوفات بنجاح." 
                : "User [{$name}] moved to trash successfully.");
    }

    public function restore(int $id): RedirectResponse
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $name = $user->name;
        $user->restore();

        return redirect()->route('admin.users.index', ['status' => 'trashed'])
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم استعادة حساب المستخدم [{$name}] بنجاح!" 
                : "User [{$name}] restored successfully!");
    }

    public function forceDelete(int $id): RedirectResponse
    {
        $user = User::withTrashed()->findOrFail($id);

        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')
                ->with('error', app()->getLocale() === 'ar'
                    ? 'لا يمكنك حذف حسابك الشخصي نهائياً.'
                    : 'You cannot permanently delete your own account.');
        }

        $name = $user->name;
        $user->forceDelete();

        return redirect()->route('admin.users.index', ['status' => 'trashed'])
            ->with('success', app()->getLocale() === 'ar' 
                ? "تم الحذف النهائي للمستخدم [{$name}] نهائياً!" 
                : "User [{$name}] permanently deleted!");
    }

    /**
     * Log in as the selected staff user (Impersonation).
     */
    public function impersonate(Request $request, int $id): RedirectResponse
    {
        $currentUser = auth()->user();
        if (!$currentUser) {
            abort(401);
        }

        $targetUser = User::findOrFail($id);

        if ($targetUser->id === $currentUser->id) {
            return redirect()->route('admin.users.index')
                ->with('error', app()->getLocale() === 'ar'
                    ? 'لا يمكنك تسجيل الدخول بحسابك الحالي.'
                    : 'You cannot impersonate your own logged-in account.');
        }

        if ($targetUser->trashed() || $targetUser->status !== 'active') {
            return redirect()->route('admin.users.index')
                ->with('error', app()->getLocale() === 'ar'
                    ? 'لا يمكن تسجيل الدخول بحساب غير نشط أو معلق.'
                    : 'Cannot impersonate an inactive or suspended user.');
        }

        // Store original admin ID and name if not already impersonating
        $originalAdminId = session('impersonated_by', $currentUser->id);
        $originalAdminName = session('impersonator_name', $currentUser->name);

        // Perform login as target user
        auth()->login($targetUser);
        $request->session()->regenerate();

        // Preserve impersonation tracking in the new session
        session()->put('impersonated_by', $originalAdminId);
        session()->put('impersonator_name', $originalAdminName);

        $msg = app()->getLocale() === 'ar'
            ? "تم تسجيل الدخول بنجاح كالموظف [{$targetUser->name}]. يمكنك العودة لحسابك في أي وقت عبر الشريط العلوي."
            : "Successfully logged in as staff user [{$targetUser->name}]. You can switch back anytime via the top banner.";

        return redirect()->route('admin.dashboard')->with('success', $msg);
    }

    /**
     * Leave impersonation session and return to the original admin account.
     */
    public function leaveImpersonation(Request $request): RedirectResponse
    {
        $impersonatorId = session('impersonated_by');

        if (!$impersonatorId) {
            return redirect()->route('admin.dashboard');
        }

        $originalAdmin = User::find($impersonatorId);
        if (!$originalAdmin) {
            session()->forget(['impersonated_by', 'impersonator_name']);
            return redirect()->route('admin.login');
        }

        auth()->login($originalAdmin);
        $request->session()->regenerate();
        session()->forget(['impersonated_by', 'impersonator_name']);

        $msg = app()->getLocale() === 'ar'
            ? "تم إنهاء جلسة المحاكاة والعودة إلى حسابك الإداري [{$originalAdmin->name}] بنجاح."
            : "Impersonation session ended. Returned to your admin account [{$originalAdmin->name}] successfully.";

        return redirect()->route('admin.users.index')->with('success', $msg);
    }

    /**
     * Self-service: Staff clocks in for today's shift.
     */
    public function selfCheckIn(Request $request): RedirectResponse
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('admin.dashboard');
        }

        // Ensure an Employee HR record exists
        $employee = $user->employee;
        if (!$employee) {
            // Auto-create a minimal employee record so attendance works
            $employee = \App\Models\Employee::create([
                'user_id' => $user->id,
                'employee_number' => 'EMP-' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                'first_name' => explode(' ', $user->name)[0] ?? $user->name,
                'last_name' => explode(' ', $user->name)[1] ?? '',
                'email' => $user->email,
                'phone' => $user->phone ?? '',
                'employment_type' => 'full_time',
                'employment_status' => 'active',
                'hire_date' => now()->toDateString(),
            ]);
        }

        $today = \Carbon\Carbon::today()->toDateString();
        $now = now();

        // Prevent double check-in
        $existing = AttendanceRecord::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $today)
            ->first();

        if ($existing && $existing->check_in) {
            return back()->with('info', app()->getLocale() === 'ar'
                ? 'لقد سجلت الحضور مسبقًا اليوم.'
                : 'You have already checked in today.');
        }

        // Determine late status (standard shift starts 09:00)
        $standardStart = \Carbon\Carbon::parse($today . ' 09:00:00');
        $lateMins = max(0, $standardStart->diffInMinutes($now, false));
        $status = $lateMins > 15 ? 'late' : 'present';

        AttendanceRecord::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'attendance_date' => $today,
            ],
            [
                'check_in' => $now->format('H:i:s'),
                'status' => $status,
                'late_minutes' => (int) $lateMins,
                'source' => 'self_service',
                'notes' => 'Self check-in by ' . $user->name,
            ]
        );

        return back()->with('success', app()->getLocale() === 'ar'
            ? 'تم تسجيل الحضور بنجاح ✓ (' . $now->format('h:i A') . ')'
            : 'Checked in successfully ✓ (' . $now->format('h:i A') . ')');
    }

    /**
     * Self-service: Staff clocks out for today's shift.
     */
    public function selfCheckOut(Request $request): RedirectResponse
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('admin.dashboard');
        }

        $employee = $user->employee;
        if (!$employee) {
            return back()->with('error', app()->getLocale() === 'ar'
                ? 'لا يوجد ملف وظيفي مرتبط بحسابك.'
                : 'No employee record found for your account.');
        }

        $today = \Carbon\Carbon::today()->toDateString();
        $now = now();

        $record = AttendanceRecord::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $today)
            ->first();

        if (!$record || !$record->check_in) {
            return back()->with('error', app()->getLocale() === 'ar'
                ? 'يجب تسجيل الحضور أولاً قبل تسجيل الانصراف.'
                : 'You must check in first before checking out.');
        }

        if ($record->check_out) {
            return back()->with('info', app()->getLocale() === 'ar'
                ? 'لقد سجلت الانصراف مسبقًا اليوم.'
                : 'You have already checked out today.');
        }

        // Calculate worked time
        $checkInTime = \Carbon\Carbon::parse($today . ' ' . $record->check_in);
        $workedMins = max(0, $checkInTime->diffInMinutes($now));

        // Early leave (standard shift ends 17:00)
        $standardEnd = \Carbon\Carbon::parse($today . ' 17:00:00');
        $earlyLeaveMins = $now->lt($standardEnd) ? max(0, $now->diffInMinutes($standardEnd)) : 0;

        // Overtime
        $overtimeMins = $now->gt($standardEnd) ? max(0, $standardEnd->diffInMinutes($now)) : 0;

        $record->update([
            'check_out' => $now->format('H:i:s'),
            'worked_minutes' => (int) $workedMins,
            'early_leave_minutes' => (int) $earlyLeaveMins,
            'overtime_minutes' => (int) $overtimeMins,
            'notes' => ($record->notes ? $record->notes . ' | ' : '') . 'Self check-out by ' . $user->name,
        ]);

        $hoursWorked = round($workedMins / 60, 1);

        return back()->with('success', app()->getLocale() === 'ar'
            ? "تم تسجيل الانصراف بنجاح ✓ ({$now->format('h:i A')}) — مدة العمل: {$hoursWorked} ساعات"
            : "Checked out successfully ✓ ({$now->format('h:i A')}) — Worked: {$hoursWorked} hours");
    }
}

