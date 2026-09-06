<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user('web') ?? $request->user();

        if (! $user) {
            return redirect()->guest(route('admin.login'));
        }

        if (! $user->hasPermission($permission)) {
            abort(403, __('admin.permission_denied', ['default' => 'Access Denied: You lack the specific permission for this module.']));
        }

        return $next($request);
    }
}
