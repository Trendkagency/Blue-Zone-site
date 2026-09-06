<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('customer.auth.login');
    }

    public function showRegister(): View
    {
        return view('customer.auth.register');
    }

    public function showForgotPassword(): View
    {
        return view('customer.auth.forgot-password');
    }

    public function showResetPassword(): View
    {
        return view('customer.auth.reset-password');
    }
}
