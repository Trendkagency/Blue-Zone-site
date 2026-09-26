<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'phone', 'password', 'role_id', 'country_id', 'city_id', 'area_id', 'status', 'avatar', 'bio', 'preferences', 'fcm_token', 'fcm_device_info'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Determine if the user can access Filament admin panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    /**
     * Get the registered FCM devices for this user.
     */
    public function devices(): HasMany
    {
        return $this->hasMany(UserDevice::class);
    }

    /**
     * Get all active FCM device tokens for this user.
     * Combines user_devices tokens with fallback to users.fcm_token.
     *
     * @return array<string>
     */
    public function getActiveFcmTokens(): array
    {
        $tokens = $this->devices()->where('is_active', true)->pluck('token')->toArray();

        if (!empty($this->fcm_token) && !in_array($this->fcm_token, $tokens, true)) {
            $tokens[] = $this->fcm_token;
        }

        return array_unique(array_filter($tokens));
    }

    /**
     * Get the role that this user belongs to.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function getTerritoryLabelAttribute(): string
    {
        $parts = [];
        if ($this->country) {
            $parts[] = $this->country->name;
        }
        if ($this->city) {
            $parts[] = $this->city->name;
        }
        if ($this->area) {
            $parts[] = $this->area->name;
        }

        return !empty($parts) ? implode(' › ', $parts) : (app()->getLocale() === 'ar' ? 'غير محدد' : 'Unassigned');
    }

    public function employee(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Employee::class);
    }


    /**
     * Check if user has specific role(s).
     */
    public function hasRole(string|array $roles): bool
    {
        if (! $this->role) {
            return false;
        }

        $roleName = strtolower(str_replace([' ', '-'], '_', $this->role->name));

        if (is_array($roles)) {
            $formattedRoles = array_map(fn ($r) => strtolower(str_replace([' ', '-'], '_', $r)), $roles);
            return in_array($roleName, $formattedRoles, true);
        }

        return $roleName === strtolower(str_replace([' ', '-'], '_', $roles));
    }

    /**
     * Check if user is a Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(['super_admin', 'super-admin', 'super admin', 'admin']) || (isset($this->role_id) && (int)$this->role_id === 3);
    }

    /**
     * Check if user is an Admin or Manager.
     */
    public function isAdmin(): bool
    {
        return $this->isSuperAdmin() || $this->hasRole(['admin', 'administrator']);
    }

    /**
     * Check if user is an Operations Manager.
     */
    public function isOperationsManager(): bool
    {
        return (isset($this->role_id) && (int)$this->role_id === 4) || $this->hasRole(['operations_manager', 'operations']);
    }

    /**
     * Check if user is an Inventory Officer.
     */
    public function isInventoryOfficer(): bool
    {
        return (isset($this->role_id) && (int)$this->role_id === 6) || $this->hasRole(['inventory_officer', 'inventory', 'warehouse_officer']);
    }

    /**
     * Check if user is an HR Manager.
     */
    public function isHrManager(): bool
    {
        return (isset($this->role_id) && (int)$this->role_id === 7) || $this->hasRole(['hr_manager', 'hr', 'human_resources']);
    }

    /**
     * Check if user is a Commercial Accounts Rep (B2B Corporate Sales).
     */
    public function isCommercialRep(): bool
    {
        return (isset($this->role_id) && (int)$this->role_id === 5) || $this->hasRole(['commercial_accounts_rep', 'commercial_rep', 'commercial_sales', 'commercial']);
    }

    /**
     * Check if user is a Medical Representative (Field Agent).
     */
    public function isMedicalRep(): bool
    {
        return (isset($this->role_id) && (int)$this->role_id === 1) || $this->hasRole(['mr', 'medical_rep', 'medical_representative']);
    }

    /**
     * Check if user is an MR Line Manager.
     */
    public function isMrLineManager(): bool
    {
        return (isset($this->role_id) && (int)$this->role_id === 2) || $this->hasRole(['mr_line_manager', 'line_manager']);
    }

    /**
     * Check if user has administrative authority over all MRs.
     */
    public function canManageAllMr(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin() || $this->isMrLineManager();
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        if (! $this->role) {
            return false;
        }

        // Super admin and system Admin roles have all permissions
        if ($this->hasRole(['super_admin', 'super-admin', 'super admin', 'admin'])) {
            return true;
        }

        $rawPermissions = $this->role->permissions;
        if (is_string($rawPermissions)) {
            $decoded = json_decode($rawPermissions, true);
            $rawPermissions = is_array($decoded) ? $decoded : [$rawPermissions];
        }

        $permissions = (array) ($rawPermissions ?? []);

        // Flatten permissions supporting both flat strings, keys with bools, and wildcards
        $flatPermissions = [];
        foreach ($permissions as $k => $v) {
            if (is_int($k) && is_string($v)) {
                $flatPermissions[] = strtolower(trim($v));
            } elseif (is_string($k) && !empty($v)) {
                $flatPermissions[] = strtolower(trim($k));
            }
        }

        // 1. Root wildcard check
        if (in_array('*', $flatPermissions, true) || in_array('all', $flatPermissions, true) || isset($permissions['*']) || isset($permissions['all'])) {
            return true;
        }

        $normalized = strtolower(trim($permission));

        // 2. Direct string match in flat permission list
        if (in_array($normalized, $flatPermissions, true)) {
            return true;
        }

        // 3. Parse module & action (e.g. "products.view", "manage_products", "view_reports")
        $module = $normalized;
        $action = null;

        if (str_contains($normalized, '.')) {
            [$module, $action] = explode('.', $normalized, 2);
        } elseif (str_starts_with($normalized, 'manage_')) {
            $module = substr($normalized, 7);
        } elseif (str_starts_with($normalized, 'view_')) {
            $module = substr($normalized, 5);
            $action = 'view';
        }

        // Module aliases
        $module = match ($module) {
            'cms' => 'content',
            'pos' => 'offline_sales',
            'product' => 'products',
            'order' => 'orders',
            'customer', 'client', 'clients' => 'customers',
            'invoice' => 'invoices',
            'report' => 'reports',
            'setting' => 'settings',
            'user', 'staff', 'admin' => 'users',
            'role' => 'roles',
            'notification', 'notifications' => 'notifications',
            'employee', 'employees' => 'hr_employees',
            'attendance' => 'hr_attendance',
            'department', 'departments', 'position', 'positions' => 'hr_departments',
            'payroll', 'advance', 'advances', 'loan', 'loans' => 'hr_payroll',
            'lead', 'leads' => 'crm_leads',
            'opportunity', 'opportunities', 'deal', 'deals' => 'crm_opportunities',
            'activity', 'activities' => 'crm_activities',
            'visit', 'visits' => 'mr_visits',
            'contact', 'contacts' => 'mr_contacts',
            'assignment', 'assignments' => 'mr_assignments',
            'territory', 'territories' => 'mr_territories',
            default => $module,
        };

        // Determine all potential keys for this module (canonical + legacy aliases)
        $moduleKeys = [$module];
        if (str_starts_with($module, 'hr_')) {
            $moduleKeys[] = substr($module, 3);
        }
        if (str_starts_with($module, 'crm_')) {
            $moduleKeys[] = substr($module, 4);
        }
        if (str_starts_with($module, 'mr_')) {
            $moduleKeys[] = substr($module, 3);
        }

        // Check flat wildcard for module: e.g. "products.*" or "products"
        foreach ($moduleKeys as $mKey) {
            if (in_array("{$mKey}.*", $flatPermissions, true) || in_array($mKey, $flatPermissions, true)) {
                return true;
            }
        }

        // Domain-level wildcards (e.g., 'mr.*' covers 'mr_visits', 'mr_contacts', etc.)
        if (str_starts_with($module, 'mr_') && (in_array('mr.*', $flatPermissions, true) || in_array('mr', $flatPermissions, true))) {
            return true;
        }
        if (str_starts_with($module, 'crm_') && (in_array('crm.*', $flatPermissions, true) || in_array('crm', $flatPermissions, true))) {
            return true;
        }
        if (str_starts_with($module, 'hr_') && (in_array('hr.*', $flatPermissions, true) || in_array('hr', $flatPermissions, true))) {
            return true;
        }

        // Check flat action permission: e.g. "products.view" or "employees.view"
        if ($action) {
            foreach ($moduleKeys as $mKey) {
                if (in_array("{$mKey}.{$action}", $flatPermissions, true)) {
                    return true;
                }
                // Handle aliases like 'manage' or 'update' for 'edit'
                if ($action === 'edit' && (in_array("{$mKey}.update", $flatPermissions, true) || in_array("{$mKey}.manage", $flatPermissions, true))) {
                    return true;
                }
            }
        } else {
            // If checking module without specific action (e.g. hasPermission('crm_leads')),
            // grant if user has any action on this module
            foreach ($moduleKeys as $mKey) {
                foreach ($flatPermissions as $fp) {
                    if ($fp === $mKey || str_starts_with($fp, "{$mKey}.")) {
                        return true;
                    }
                }
            }
        }

        // Check matrix structure: e.g. $permissions['products']['view']
        foreach ($moduleKeys as $mKey) {
            if (isset($permissions[$mKey]) && is_array($permissions[$mKey])) {
                $modPerms = $permissions[$mKey];

                if ($action) {
                    return !empty($modPerms[$action]);
                }

                foreach (['view', 'create', 'edit', 'delete'] as $act) {
                    if (!empty($modPerms[$act])) {
                        return true;
                    }
                }

                return !empty($modPerms);
            }
        }

        return false;
    }

    /**
     * Get avatar URL (Spatie media or legacy avatar column).
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if (method_exists($this, 'hasMedia') && $this->hasMedia('avatar')) {
            $media = $this->getFirstMedia('avatar');
            if ($media) {
                return asset('storage/' . $media->id . '/' . $media->file_name);
            }
        }

        if (! empty($this->avatar)) {
            $path = $this->avatar;
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                return $path;
            }
            if (str_contains($path, '/storage/')) {
                $path = substr($path, strpos($path, '/storage/') + 9);
                return asset('storage/' . ltrim($path, '/'));
            }
            if (str_starts_with($path, 'storage/')) {
                return asset(ltrim($path, '/'));
            }
            if (str_starts_with($path, 'avatars/')) {
                return asset('storage/' . ltrim($path, '/'));
            }
            if (file_exists(public_path(ltrim($path, '/')))) {
                return asset(ltrim($path, '/'));
            }
            return asset('storage/' . ltrim($path, '/'));
        }

        return null;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'preferences' => 'array',
            'fcm_device_info' => 'array',
        ];
    }

    /**
     * CRM Relationships
     */
    public function crmAssignedLeads(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CrmLead::class, 'owner_id');
    }

    public function crmAssignedOpportunities(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CrmOpportunity::class, 'owner_id');
    }

    public function crmActivities(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CrmActivity::class, 'assigned_to');
    }

    public function crmNotes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CrmNote::class);
    }

    public function crmCampaigns(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CrmCampaign::class, 'owner_id');
    }

    public function crmCompanies(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CrmCompany::class, 'owner_id');
    }

    /**
     * Medical Representative (MR) Module Relationships
     */
    public function mrAssignments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Mr\ContactAssignment::class, 'mr_id');
    }

    public function mrScheduledVisits(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Mr\ScheduledVisit::class, 'mr_id');
    }

    public function mrVisits(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Mr\Visit::class, 'mr_id');
    }

    public function mrDailyLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Mr\RepDailyLog::class, 'mr_id');
    }

    public function mrPerformanceSnapshots(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Mr\RepPerformanceSnapshot::class, 'mr_id');
    }

    public function mrGpsConfig(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\Mr\GpsConfig::class, 'user_id');
    }
}
