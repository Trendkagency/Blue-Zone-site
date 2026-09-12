<?php

namespace App\Providers;

require_once __DIR__ . '/../Helpers/helpers.php';

use App\Models\User;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use App\Services\CurrencyService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Singleton pattern: FcmService
        $this->app->singleton(\App\Services\FcmService::class, function () {
            return \App\Services\FcmService::getInstance();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        //  Share Localization ISRtl or IsLtr
        Blade::if('isRtl', function () {
            return app()->getLocale() === 'ar';
        });
        Blade::if('isLtr', function () {
            return app()->getLocale() === 'en';
        });
        // Dynamic Currency Directives
        Blade::directive('currency', function ($expression) {
            return "<?php echo \App\Services\CurrencyService::format({$expression}); ?>";
        });
        Blade::directive('currencySymbol', function ($expression = '') {
            $expr = $expression ? "({$expression})" : "()";
            return "<?php echo \App\Services\CurrencyService::symbol{$expr}; ?>";
        });
        Blade::directive('currencyCode', function () {
            return "<?php echo \App\Services\CurrencyService::code(); ?>";
        });

        // Observer pattern: Orders, Inventory items, and movements observers
        \App\Models\Order::observe(\App\Observers\OrderObserver::class);
        \App\Models\InventoryItem::observe(\App\Observers\InventoryItemObserver::class);
        \App\Models\InventoryMovement::observe(\App\Observers\InventoryMovementObserver::class);

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
        }

        $this->configureRateLimiting();
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // 1. Authentication: Login (Customer & Admin) - 5 attempts / minute per IP + email
        RateLimiter::for('login', function (Request $request) {
            $identifier = Str::transliterate(Str::lower($request->input('email', $request->input('username', ''))) . '|' . $request->ip());

            return Limit::perMinute(5)->by($identifier)->response(function (Request $request, array $headers) {
                $retryAfter = $headers['Retry-After'] ?? 60;
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => app()->getLocale() === 'ar'
                            ? "تم تجاوز عدد محاولات تسجيل الدخول المسموح بها. يرجى الانتظار {$retryAfter} ثانية قبل المحاولة مرة أخرى."
                            : "Too many login attempts. Please try again in {$retryAfter} seconds.",
                        'retry_after' => (int) $retryAfter,
                    ], 429, $headers);
                }

                return back()->withInput($request->only('email', 'username', 'remember'))
                    ->withErrors([
                        'email' => app()->getLocale() === 'ar'
                            ? "تم تجاوز عدد محاولات الدخول المسموح بها. يرجى الانتظار {$retryAfter} ثانية."
                            : "Too many login attempts. Please try again in {$retryAfter} seconds.",
                    ])->withHeaders($headers);
            });
        });

        // 2. Authentication: Registration - 3 registrations / minute per IP
        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip())->response(function (Request $request, array $headers) {
                $retryAfter = $headers['Retry-After'] ?? 60;
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => app()->getLocale() === 'ar'
                            ? "تم تجاوز الحد المسموح به لإنشاء الحسابات. يرجى الانتظار {$retryAfter} ثانية."
                            : "Registration limit exceeded. Please wait {$retryAfter} seconds.",
                        'retry_after' => (int) $retryAfter,
                    ], 429, $headers);
                }

                return back()->withInput($request->except('password', 'password_confirmation'))
                    ->withErrors([
                        'email' => app()->getLocale() === 'ar'
                            ? "تم تجاوز محاولات التسجيل. يرجى الانتظار {$retryAfter} ثانية."
                            : "Too many registration attempts. Please wait {$retryAfter} seconds.",
                    ])->withHeaders($headers);
            });
        });

        // 3. Authentication: Password Reset - 3 requests / minute per IP + email
        RateLimiter::for('password-reset', function (Request $request) {
            $identifier = Str::transliterate(Str::lower($request->input('email', '')) . '|' . $request->ip());

            return Limit::perMinute(3)->by($identifier)->response(function (Request $request, array $headers) {
                $retryAfter = $headers['Retry-After'] ?? 60;
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => app()->getLocale() === 'ar'
                            ? "تم تجاوز الحد المسموح لطلبات الاستعادة. يرجى الانتظار {$retryAfter} ثانية."
                            : "Password reset limit exceeded. Please wait {$retryAfter} seconds.",
                        'retry_after' => (int) $retryAfter,
                    ], 429, $headers);
                }

                return back()->withInput($request->only('email'))
                    ->withErrors([
                        'email' => app()->getLocale() === 'ar'
                            ? "يرجى الانتظار {$retryAfter} ثانية قبل طلب رابط جديد."
                            : "Please wait {$retryAfter} seconds before requesting another link.",
                    ])->withHeaders($headers);
            });
        });

        // 4. Cart Operations (Add, Update, Remove, Clear) - 30 requests / minute
        RateLimiter::for('cart', function (Request $request) {
            $key = $request->user()?->id ?: ($request->hasSession() ? $request->session()->getId() : null) ?: $request->ip();

            return Limit::perMinute(30)->by($key)->response(function (Request $request, array $headers) {
                $retryAfter = $headers['Retry-After'] ?? 60;
                return response()->json([
                    'success' => false,
                    'message' => app()->getLocale() === 'ar'
                        ? "تم إرسال طلبات متتالية سريعة للسلة. يرجى الانتظار {$retryAfter} ثانية قبل المحاولة مجدداً."
                        : "Cart request rate limit exceeded. Please wait {$retryAfter} seconds.",
                    'retry_after' => (int) $retryAfter,
                ], 429, $headers);
            });
        });

        // 5. Coupon Verification - 5 attempts / minute (Anti-brute force)
        RateLimiter::for('coupon', function (Request $request) {
            $key = $request->user()?->id ?: ($request->hasSession() ? $request->session()->getId() : null) ?: $request->ip();

            return Limit::perMinute(5)->by($key)->response(function (Request $request, array $headers) {
                $retryAfter = $headers['Retry-After'] ?? 60;
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => app()->getLocale() === 'ar'
                            ? "تم تجاوز الحد المسموح لتجربة الكوبونات. يرجى الانتظار {$retryAfter} ثانية."
                            : "Too many coupon attempts. Please wait {$retryAfter} seconds.",
                        'retry_after' => (int) $retryAfter,
                    ], 429, $headers);
                }

                return back()->withInput()->with(
                    'error',
                    app()->getLocale() === 'ar'
                    ? "تم تجاوز الحد المسموح لتجربة الكوبونات. يرجى الانتظار {$retryAfter} ثانية."
                    : "Too many coupon attempts. Please wait {$retryAfter} seconds."
                )->withHeaders($headers);
            });
        });

        // 6. Checkout & Order Placement - 5 orders / minute
        RateLimiter::for('checkout', function (Request $request) {
            $key = $request->user()?->id ?: $request->ip();

            return Limit::perMinute(5)->by($key)->response(function (Request $request, array $headers) {
                $retryAfter = $headers['Retry-After'] ?? 60;
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => app()->getLocale() === 'ar'
                            ? "تم إرسال عدة طلبات دفع في وقت قصير. يرجى الانتظار {$retryAfter} ثانية."
                            : "Too many checkout requests. Please wait {$retryAfter} seconds.",
                        'retry_after' => (int) $retryAfter,
                    ], 429, $headers);
                }

                return back()->with(
                    'error',
                    app()->getLocale() === 'ar'
                    ? "يرجى الانتظار {$retryAfter} ثانية قبل تأكيد الطلب مجدداً."
                    : "Please wait {$retryAfter} seconds before placing an order again."
                )->withHeaders($headers);
            });
        });

        // 7. Polling / Refresh / Heartbeat - 60 requests / minute
        RateLimiter::for('polling', function (Request $request) {
            $key = $request->user()?->id ?: $request->ip();

            return Limit::perMinute(60)->by($key)->response(function (Request $request, array $headers) {
                return response()->json([
                    'status' => 'throttled',
                    'message' => 'Polling frequency limit reached.',
                ], 429, $headers);
            });
        });
    }
}
