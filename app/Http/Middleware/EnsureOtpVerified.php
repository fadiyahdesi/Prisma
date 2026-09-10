<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureOtpVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && !$user->is_otp_verified) {
            return redirect()->route('otp.show')->with('warning', 'Silakan selesaikan verifikasi 2FA OTP 6-digit terlebih dahulu.');
        }

        return $next($request);
    }
}

