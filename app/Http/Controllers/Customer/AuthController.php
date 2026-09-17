<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use App\Services\CaptchaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show customer login form.
     */
    public function showLogin(): View
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('customer.account.dashboard');
        }

        return view('customer.auth.login');
    }

    /**
     * Handle customer login attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (CaptchaService::isEnabled('login')) {
            if (!CaptchaService::verify($request->input('captcha'))) {
                return back()->withErrors([
                    'captcha' => app()->getLocale() === 'ar'
                        ? 'رمز التحقق الأمني غير صحيح أو منتهي الصلاحية، يرجى المحاولة مرة أخرى.'
                        : 'The security verification answer is incorrect or expired. Please try again.',
                ])->withInput($request->except(['password', 'captcha']));
            }
        }

        $remember = $request->boolean('remember');

        if (Auth::guard('customer')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            /** @var Customer $customer */
            $customer = Auth::guard('customer')->user();

            if ($customer->status === 'inactive') {
                Auth::guard('customer')->logout();
                return back()->withErrors([
                    'email' => __('app.account_suspended', ['default' => 'Your account has been deactivated. Please contact support.']),
                ]);
            }

            return redirect()->intended(route('customer.account.dashboard'))
                ->with('success', __('app.welcome_back', ['default' => 'Welcome back, :name!', 'name' => $customer->name]));
        }

        if (User::where('email', $credentials['email'])->exists()) {
            return back()->withErrors([
                'email' => app()->getLocale() === 'ar'
                    ? 'هذا الحساب مسجل كمسؤول نظام. يرجى تسجيل الدخول من خلال بوابة الإدارة: ' . route('admin.login')
                    : 'This account belongs to an Administrator. Please log in via the Admin Portal: ' . route('admin.login'),
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => __('auth.failed', ['default' => 'These credentials do not match our records.']),
        ])->onlyInput('email');
    }

    /**
     * Show customer registration form.
     */
    public function showRegister(): View
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('customer.account.dashboard');
        }

        $countries = \App\Models\Country::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name_en')
            ->get();

        $defaultCountry = $countries->firstWhere('iso2', 'SA') ?? $countries->first();

        return view('customer.auth.register', compact('countries', 'defaultCountry'));
    }

    /**
     * Handle customer registration.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:customers,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'phone' => ['nullable', 'string', 'max:30'],
            'phone_code' => ['nullable', 'string', 'max:10'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'city_id' => ['nullable', 'integer'],
            'country' => ['nullable', 'string', 'max:100'],
            'country_id' => ['nullable', 'integer'],
        ]);

        if (CaptchaService::isEnabled('register')) {
            if (!CaptchaService::verify($request->input('captcha'))) {
                return back()->withErrors([
                    'captcha' => app()->getLocale() === 'ar'
                        ? 'رمز التحقق الأمني غير صحيح أو منتهي الصلاحية، يرجى المحاولة مرة أخرى.'
                        : 'The security verification answer is incorrect or expired. Please try again.',
                ])->withInput($request->except(['password', 'password_confirmation', 'captcha']));
            }
        }

        // Resolve Country Details
        $countryName = $validated['country'] ?? 'Saudi Arabia';
        $countryPhoneCode = $validated['phone_code'] ?? '+966';
        if (!empty($validated['country_id'])) {
            $countryObj = \App\Models\Country::find($validated['country_id']);
            if ($countryObj) {
                $countryName = $countryObj->name_en;
                $countryPhoneCode = $countryObj->phone_code;
            }
        }

        // Resolve City Details
        $cityName = $validated['city'] ?? 'Riyadh';
        if (!empty($validated['city_id'])) {
            $cityObj = \App\Models\City::find($validated['city_id']);
            if ($cityObj) {
                $cityName = $cityObj->name_en;
            }
        }

        // Format phone with dial code if needed
        $phone = trim($validated['phone']);
        if (!str_starts_with($phone, '+')) {
            $cleanDial = rtrim($countryPhoneCode, ' ');
            $cleanPhone = ltrim($phone, '0');
            $phone = $cleanDial . ' ' . $cleanPhone;
        }

        $defaultAddress = [
            [
                'id' => 1,
                'title' => app()->getLocale() === 'ar' ? 'المقر السكني الرئيسي' : 'Primary Residence',
                'recipient' => $validated['name'],
                'phone' => $phone,
                'street' => $validated['address'] ?? 'Primary Delivery Address',
                'city' => $cityName,
                'country' => $countryName,
                'postal_code' => '12271',
                'is_default' => true,
            ],
        ];

        $customer = Customer::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $phone,
            'address' => $validated['address'] ?? null,
            'city' => $cityName,
            'country' => $countryName,
            'saved_addresses' => $defaultAddress,
            'loyalty_points' => 100, // 100 welcome points
            'status' => 'active',
            'registered_at' => now(),
        ]);

        Auth::guard('customer')->login($customer);
        $request->session()->regenerate();

        return redirect()->route('customer.account.dashboard')
            ->with('success', __('app.registration_successful', ['default' => 'Welcome to BLUE ZONE! Your wellness journey begins here.']));
    }

    /**
     * Handle customer logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('customer.home')
            ->with('info', __('app.logged_out', ['default' => 'You have been safely signed out.']));
    }

    /**
     * Show forgot password form.
     */
    public function showForgotPassword(): View
    {
        return view('customer.auth.forgot-password');
    }

    /**
     * Handle forgot password request.
     */
    public function forgotPassword(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        if (CaptchaService::isEnabled('forgot-password')) {
            if (!CaptchaService::verify($request->input('captcha'))) {
                return back()->withErrors([
                    'captcha' => app()->getLocale() === 'ar'
                        ? 'رمز التحقق الأمني غير صحيح أو منتهي الصلاحية، يرجى المحاولة مرة أخرى.'
                        : 'The security verification answer is incorrect or expired. Please try again.',
                ])->withInput($request->except(['captcha']));
            }
        }

        // Simulating password reset notification / dispatching
        return back()->with('success', __('app.reset_link_sent', ['default' => 'If an account exists with this email, a recovery link has been dispatched.']));
    }

    /**
     * Show reset password form.
     */
    public function showResetPassword(Request $request): View
    {
        return view('customer.auth.reset-password', [
            'token' => $request->route('token') ?? 'demo-token',
            'email' => $request->query('email') ?? '',
        ]);
    }

    /**
     * Handle password reset submission.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $customer = Customer::where('email', $validated['email'])->first();

        if ($customer) {
            $customer->update(['password' => Hash::make($validated['password'])]);
            return redirect()->route('customer.auth.login')
                ->with('success', __('app.password_updated_login', ['default' => 'Password reset successfully. Please sign in with your new password.']));
        }

        return back()->withErrors(['email' => __('app.user_not_found', ['default' => 'Unable to locate an account with this email address.'])]);
    }
}
