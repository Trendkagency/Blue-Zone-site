<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::guard('web')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', app()->getLocale() === 'ar'
                    ? 'مرحباً بعودتك! تم تسجيل الدخول إلى لوحة التحكم بنجاح.'
                    : 'Welcome back! Logged into clinical dashboard successfully.');
        }

        return back()->withErrors([
            'email' => app()->getLocale() === 'ar'
                ? 'بيانات الاعتماد المدخلة غير صحيحة أو الحساب غير مفعل.'
                : 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', app()->getLocale() === 'ar'
                ? 'تم تسجيل الخروج من لوحة التحكم بأمان.'
                : 'Logged out from administrative portal safely.');
    }
}
