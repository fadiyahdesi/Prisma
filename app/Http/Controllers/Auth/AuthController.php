<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Handle manual login using Full Name / NIDN / Email as username, and NIDN as password.
     */
    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'identity' => 'required|string',
            'password' => 'required|string',
        ], [
            'identity.required' => 'Nama Lengkap atau NIDN wajib diisi.',
            'password.required' => 'Kata sandi (NIDN) wajib diisi.',
        ]);

        $identity = trim($validated['identity']);
        $password = trim($validated['password']);
        $remember = $request->boolean('remember');

        // 1. Search by exact NIDN
        $user = User::where('nidn_nim', $identity)->first();

        // 2. Search by exact Email
        if (!$user) {
            $user = User::where('email', strtolower($identity))->first();
        }

        // 3. Search by Name (exact match case-insensitive)
        if (!$user) {
            $user = User::whereRaw('LOWER(TRIM(name)) = ?', [strtolower($identity)])->first();
        }

        // 4. Search by Name (LIKE query on cleaned keywords)
        if (!$user) {
            $cleanName = preg_replace('/\b(Dr|Prof|Ir|Drs|Dra|M\.Kom|S\.Kom|M\.T|M\.Si|M\.Sc|S\.T|S\.Pd|M\.Pd|M\.Eng|M\.Farm|A\.Md|S\.E|M\.M)\b/i', '', $identity);
            $cleanName = trim(preg_replace('/[.,\-_]/', ' ', $cleanName));
            $words = array_values(array_filter(explode(' ', $cleanName), fn($w) => strlen($w) >= 3));

            if (!empty($words)) {
                $query = User::query();
                foreach ($words as $word) {
                    $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($word) . '%']);
                }
                $user = $query->first();
            }
        }

        if (!$user) {
            return redirect()->back()
                ->withInput($request->only('identity', 'remember'))
                ->withErrors(['identity' => 'Nama lengkap atau NIDN tidak ditemukan dalam basis data dosen UHN.']);
        }

        // Verify password
        $passwordValid = Hash::check($password, $user->password);

        // Fallback: If password matches user's NIDN, 'password123', or 'password', update and allow
        if (!$passwordValid && (!empty($user->nidn_nim) && $password === $user->nidn_nim || $password === 'password123' || $password === 'password')) {
            $user->password = Hash::make($password);
            $user->save();
            $passwordValid = true;
        }

        if (!$passwordValid) {
            return redirect()->back()
                ->withInput($request->only('identity', 'remember'))
                ->withErrors(['password' => 'Kata sandi tidak sesuai. Gunakan NIDN Anda sebagai kata sandi.']);
        }

        // Successfully authenticated
        Auth::login($user, $remember);
        session(['otp_verified' => true]);

        $user->update([
            'is_otp_verified' => true,
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        return redirect()->intended(route('dashboard'))
            ->with('success', "Selamat datang, {$user->name}! Anda berhasil masuk ke PRISMA UHN.");
    }
}

