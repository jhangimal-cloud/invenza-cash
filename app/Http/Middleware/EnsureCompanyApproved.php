<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->is_platform_admin) {
            return $next($request);
        }

        if ($request->routeIs('account.pending') || $request->routeIs('logout')) {
            return $next($request);
        }

        if ($user->company?->status !== 'active') {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Tu empresa aún no ha sido aprobada. Contacta a soporte para activar tu cuenta.',
                ], 403);
            }

            return redirect()->route('account.pending');
        }

        return $next($request);
    }
}
