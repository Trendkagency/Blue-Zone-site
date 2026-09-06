<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production') || config('app.env') === 'production' || str_starts_with(config('app.url', ''), 'https://')) {
            URL::forceScheme('https');
        }

        Gate::before(function ($user, string $ability) {
            if ($user instanceof User && $user->hasRole(['super_admin', 'Super Admin', 'admin'])) {
                return true;
            }
        });

        $permissions = [
            'manage_products',
            'manage_inventory',
            'manage_orders',
            'manage_customers',
            'manage_content',
            'manage_settings',
            'manage_users',
            'view_reports',
            'create_offline_sales',
            'manage_invoices',
        ];

        foreach ($permissions as $permission) {
            Gate::define($permission, function ($user) use ($permission) {
                return $user instanceof User && $user->hasPermission($permission);
            });
        }
    }
}