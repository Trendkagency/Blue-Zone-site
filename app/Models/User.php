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
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Fillable(['name', 'email', 'phone', 'password', 'role_id', 'status', 'avatar', 'bio', 'preferences'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser, HasMedia
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, InteractsWithMedia;

    /**
     * Determine if the user can access Filament admin panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    /**
     * Get the role that this user belongs to.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
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

        // 1. Root wildcard check
        if (in_array('*', $permissions, true) || in_array('all', $permissions, true) || isset($permissions['*']) || isset($permissions['all'])) {
            return true;
        }

        $normalized = strtolower(trim($permission));

        // 2. Direct string match in flat permission list
        if (in_array($normalized, $permissions, true)) {
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
            'customer' => 'customers',
            'invoice' => 'invoices',
            'report' => 'reports',
            'setting' => 'settings',
            'user' => 'users',
            'role' => 'roles',
            default => $module,
        };

        // Check flat wildcard for module: e.g. "products.*" or "products"
        if (in_array("{$module}.*", $permissions, true) || in_array($module, $permissions, true)) {
            return true;
        }

        // Check flat action permission: e.g. "products.view"
        if ($action && in_array("{$module}.{$action}", $permissions, true)) {
            return true;
        }

        // Check matrix structure: e.g. $permissions['products']['view']
        if (isset($permissions[$module]) && is_array($permissions[$module])) {
            $modPerms = $permissions[$module];

            // If specific action is requested
            if ($action) {
                return !empty($modPerms[$action]);
            }

            // If general module access is checked (e.g. "products" or "manage_products"):
            // Grant access if ANY action (view, create, edit, delete) is allowed
            foreach (['view', 'create', 'edit', 'delete'] as $act) {
                if (!empty($modPerms[$act])) {
                    return true;
                }
            }

            return !empty($modPerms);
        }

        return false;
    }

    /**
     * Register Spatie media collections for User.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile();
    }

    /**
     * Register Spatie media conversions for User.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(100)
            ->height(100)
            ->nonQueued();

        $this->addMediaConversion('avatar')
            ->width(300)
            ->height(300)
            ->nonQueued();
    }

    /**
     * Get avatar URL (Spatie media or legacy avatar column).
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->hasMedia('avatar')) {
            $media = $this->getFirstMedia('avatar');
            if ($media) {
                return asset('storage/' . $media->id . '/' . $media->file_name);
            }
        }

        if (! empty($this->avatar)) {
            $path = $this->avatar;
            if (str_contains($path, '/storage/')) {
                $path = substr($path, strpos($path, '/storage/') + 9);
                return asset('storage/' . ltrim($path, '/'));
            }
            if (str_starts_with($path, 'http')) {
                return $path;
            }
            return asset(ltrim($path, '/'));
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
        ];
    }
}
