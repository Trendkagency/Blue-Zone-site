<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user('web') ?? $request->user();

        if (! $user) {
            return redirect()->guest(route('filament.admin.auth.login'));
        }

        if (! $user->hasRole($roles)) {
            abort(403, __('admin.unauthorized_access', ['default' => 'You do not have the required role to access this area.']));
        }

        return $next($request);
    }
}
