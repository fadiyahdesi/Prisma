<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Ensure the authenticated user has at least one of the required permissions.
     */
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Akses ditolak: Anda belum login.');
        }

        if ($user->hasRole('Superadmin') || session('active_role') === 'Superadmin') {
            return $next($request);
        }

        if (!empty($permissions) && !$user->hasAnyPermission($permissions)) {
            abort(403, 'Akses ditolak: Peran Anda tidak memiliki izin (' . implode(' / ', $permissions) . ').');
        }

        return $next($request);
    }
}
