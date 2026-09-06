<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
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

        return view('customer.auth.register');
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
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
        ]);

        $defaultAddress = [
            [
                'id' => 1,
                'title' => app()->getLocale() === 'ar' ? 'المقر السكني الرئيسي' : 'Primary Residence',
                'recipient' => $validated['name'],
                'phone' => $validated['phone'] ?? '+966 50 000 0000',
                'street' => $validated['address'] ?? 'Primary Delivery Address',
                'city' => $validated['city'] ?? 'Riyadh',
                'country' => $validated['country'] ?? 'Saudi Arabia',
                'postal_code' => '12271',
                'is_default' => true,
            ],
        ];

        $customer = Customer::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'country' => $validated['country'] ?? 'Saudi Arabia',
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
