<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Services\AuditLogService;

class OtpController extends Controller
{
    /**
     * Show the 2FA OTP Verification Form.
     */
    public function showForm()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->is_otp_verified) {
            return redirect()->route('dashboard');
        }

        // Get simulated code for demo display
        $demoOtpCode = Cache::get('otp_code_' . $user->id);

        return view('auth.otp-verify', compact('user', 'demoOtpCode'));
    }

    /**
     * Verify the 6-digit OTP code.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'otp_code' => ['required', 'digits:6'],
        ], [
            'otp_code.required' => 'Kode OTP 6-digit wajib diisi.',
            'otp_code.digits' => 'Kode OTP harus terdiri dari 6 digit angka.',
        ]);

        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Check 15-Minute Lockout Status (US-02.2 Criteria 3)
        if ($user->isOtpLocked()) {
            $remainingMinutes = now()->diffInMinutes($user->otp_locked_until) + 1;
            
            AuditLogService::log('OTP_VERIFY_BLOCKED_LOCKED', null, [
                'user_id' => $user->id,
                'locked_until' => $user->otp_locked_until,
            ], $user->id);

            return back()->withErrors([
                'otp_code' => "Akun Anda terkunci sementara karena kesalahan kode OTP 3 kali berturut-turut. Silakan coba lagi dalam {$remainingMinutes} menit."
            ]);
        }

        $cachedOtp = Cache::get('otp_code_' . $user->id);

        // Check if OTP expired
        if (!$cachedOtp) {
            AuditLogService::log('OTP_VERIFY_EXPIRED', null, ['user_id' => $user->id], $user->id);
            return back()->withErrors(['otp_code' => 'Kode OTP telah kadaluarsa (TTL 5 menit). Silakan klik "Kirim Ulang Kode OTP".']);
        }

        // Verify Submitted Code
        if ($request->otp_code === $cachedOtp) {
            // SUCCESSFUL OTP VERIFICATION
            Cache::forget('otp_code_' . $user->id);

            $user->update([
                'is_otp_verified' => true,
                'otp_failed_attempts' => 0,
                'otp_locked_until' => null,
                'last_login_ip' => $request->ip(),
                'last_login_at' => now(),
            ]);

            AuditLogService::log('OTP_VERIFIED_SUCCESS', null, [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->primaryRoleName(),
                'ip' => $request->ip(),
            ], $user->id);

            return redirect()->route('dashboard')->with('success', 'Verifikasi 2FA OTP Berhasil! Selamat datang di Portal PRISMA UHN.');
        } else {
            // FAILED OTP VERIFICATION
            $failedAttempts = $user->otp_failed_attempts + 1;
            $updates = ['otp_failed_attempts' => $failedAttempts];

            if ($failedAttempts >= 3) {
                // Lock account for 15 minutes (US-02.2)
                $updates['otp_locked_until'] = now()->addMinutes(15);

                AuditLogService::log('OTP_ACCOUNT_LOCKED_15_MINUTES', null, [
                    'user_id' => $user->id,
                    'failed_attempts' => $failedAttempts,
                    'locked_until' => $updates['otp_locked_until'],
                ], $user->id);

                $user->update($updates);

                return back()->withErrors([
                    'otp_code' => 'Kode OTP salah 3 kali berturut-turut! Akun Anda dikunci selama 15 menit untuk alasan keamanan.'
                ]);
            }

            $user->update($updates);
            $remaining = 3 - $failedAttempts;

            AuditLogService::log('OTP_VERIFICATION_FAILED', null, [
                'user_id' => $user->id,
                'attempt' => $failedAttempts,
            ], $user->id);

            return back()->withErrors([
                'otp_code' => "Kode OTP yang Anda masukkan salah. Sisa kesempatan: {$remaining} kali."
            ]);
        }
    }

    /**
     * Resend 6-digit OTP code.
     */
    public function resend(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->isOtpLocked()) {
            $remainingMinutes = now()->diffInMinutes($user->otp_locked_until) + 1;
            return back()->withErrors(['otp_code' => "Akun Anda sedang dikunci. Silakan tunggu {$remainingMinutes} menit lagi sebelum mengirim ulang."]);
        }

        $newOtpCode = (string) rand(100000, 999999);
        Cache::put('otp_code_' . $user->id, $newOtpCode, now()->addMinutes(5));

        AuditLogService::log('OTP_RESENT', null, [
            'user_id' => $user->id,
            'email' => $user->email,
        ], $user->id);

        return back()->with('info', "Kode OTP 6-digit baru telah dikirim ulang ke {$user->email} (Kode Simulasi: {$newOtpCode}).");
    }
}

