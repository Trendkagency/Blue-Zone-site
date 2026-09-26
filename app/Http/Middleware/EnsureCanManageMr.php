<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCanManageMr
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('web') ?? $request->user() ?? auth()->user();

        if (! $user) {
            return redirect()->guest(route('filament.admin.auth.login'));
        }

        if ($user->isMedicalRep() && ! $user->canManageAllMr()) {
            abort(403, __('admin.unauthorized_access', ['default' => 'Access Denied: Medical Representatives cannot access administrative MR configuration modules.']));
        }

        return $next($request);
    }
}
