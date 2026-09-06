<?php

namespace App\Providers;

<<<<<<< HEAD
use Illuminate\Support\Facades\URL;
=======
use App\Models\User;
use Illuminate\Support\Facades\Gate;
>>>>>>> origin/main
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
<<<<<<< HEAD
        if ($this->app->environment('production') || config('app.env') === 'production' || str_starts_with(config('app.url', ''), 'https://')) {
            URL::forceScheme('https');
=======
        Gate::before(function ($user, string $ability) {
            if ($user instanceof User && $user->hasRole(['super_admin', 'Super Admin', 'admin'])) {
                return true;
            }
        });

        $permissions = [
            'manage_products',
            'manage_inventory',
            'manage_orders',
            'manage_offline_sales',
            'manage_customers',
            'manage_users',
            'manage_roles',
            'manage_cms',
            'view_reports',
            'manage_settings',
        ];

        foreach ($permissions as $permission) {
            Gate::define($permission, function ($user) use ($permission) {
                return $user instanceof User && $user->hasPermission($permission);
            });
>>>>>>> origin/main
        }
    }
}
