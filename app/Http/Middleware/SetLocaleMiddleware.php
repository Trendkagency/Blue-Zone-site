<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = ['en', 'ar'];

        if ($request->has('lang') && in_array($request->query('lang'), $supportedLocales, true)) {
            $locale = $request->query('lang');
            Session::put('locale', $locale);
        } elseif (Session::has('locale') && in_array(Session::get('locale'), $supportedLocales, true)) {
            $locale = Session::get('locale');
        } else {
            $locale = config('app.locale', 'en');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
