<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\ExternalIdentity;
use App\Models\RefFakultas;
use App\Models\RefProgramStudi;
use App\Services\AuditLogService;
use App\Services\Integrations\SiakadClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SsoController extends Controller
{
    /**
     * Show the SSO Login Selection Page.
     */
    public function showLogin()
    {
        $demoUsers = User::with(['roles', 'fakultas', 'prodi'])->get();
        return view('auth.login', compact('demoUsers'));
    }

    /**
     * Redirect to SIAKAD Cloud OAuth Provider.
     */
    public function redirect(Request $request)
    {
        $state = Str::random(40);
        session(['oauth_state' => $state]);

        if (config('services.siakad.enabled')) {
            return redirect(app(SiakadClient::class)->authorizationUrl($state));
        }

        return redirect()->route('sso.callback', [
            'state' => $state,
            'demo_role' => $request->query('role', 'Dosen / Pengusul')
        ]);
    }

    /**
     * Handle OAuth Callback from SIAKAD Cloud UHN.
     */
    public function callback(Request $request)
    {
        $expectedState = session()->pull('oauth_state');
        $receivedState = (string) $request->query('state');
        $profile = [];

        if (!$expectedState || !$receivedState || !hash_equals($expectedState, $receivedState)) {
            return redirect()->route('login')->withErrors([
                'sso' => 'Sesi SSO tidak valid atau telah kedaluwarsa. Silakan coba lagi.',
            ]);
        }

        if (config('services.siakad.enabled')) {
            if ($request->filled('error') || !$request->filled('code')) {
                return redirect()->route('login')->withErrors([
                    'sso' => $request->query('error_description', 'Login SSO dibatalkan atau gagal.'),
                ]);
            }

            try {
                $profile = app(SiakadClient::class)->userFromCode($request->string('code')->toString());
            } catch (\Throwable $exception) {
                report($exception);
                return redirect()->route('login')->withErrors([
                    'sso' => 'SIAKAD tidak dapat dihubungi. Silakan coba lagi.',
                ]);
            }

            $providerUserId = (string) ($profile['sub'] ?? $profile['id'] ?? '');
            $email = strtolower((string) ($profile['email'] ?? ''));

            $allowedDomains = config('services.siakad.allowed_email_domains', []);
            $emailDomain = substr(strrchr($email, '@') ?: '', 1);

            if (!$providerUserId || !$email || ($allowedDomains && !in_array($emailDomain, $allowedDomains, true))) {
                return redirect()->route('login')->withErrors([
                    'sso' => 'Profil SSO tidak memiliki identitas yang diizinkan untuk mengakses PRISMA.',
                ]);
            }

            $identity = [
                'name' => $profile['name'] ?? $profile['preferred_username'] ?? $email,
                'email' => $email,
                'nidn_nim' => $profile['nidn'] ?? $profile['nim'] ?? null,
            ];
            $roleName = null;
            $user = ExternalIdentity::where('provider', 'siakad')
                ->where('provider_user_id', $providerUserId)
                ->with('user')
                ->first()?->user;
            $user ??= User::where('email', $email)->first();
        } else {
            $identities = [
            'Dosen / Pengusul' => [
                'name' => 'Dr. Ir. Hendra Prasetya, M.T.',
                'email' => 'dosen@harkatnegeri.ac.id',
                'nidn_nim' => '0615037801',
            ],
            'Reviewer' => [
                'name' => 'Prof. Dr. Ir. Budi Santoso, M.Sc.',
                'email' => 'reviewer@harkatnegeri.ac.id',
                'nidn_nim' => '0620087102',
            ],
            'Kaprodi' => [
                'name' => 'Dr. Ratna Sari, S.E., M.Si.',
                'email' => 'kaprodi@harkatnegeri.ac.id',
                'nidn_nim' => '0612058001',
            ],
            'Dekanat' => [
                'name' => 'Drs. Bambang Sudiro, M.Kom.',
                'email' => 'dekan@harkatnegeri.ac.id',
                'nidn_nim' => '0604087103',
            ],
            'Admin P3M' => ['name' => 'Admin P3M UHN', 'email' => 'adminp3m@harkatnegeri.ac.id', 'nidn_nim' => 'ADM-P3M-01'],
            'Kepala P3M' => ['name' => 'Sharfina Febbi Handayani, S.Kom., M.Kom.', 'email' => 'kepalap3m@harkatnegeri.ac.id', 'nidn_nim' => '0617029201'],
            'Keuangan' => ['name' => 'Bendahara P3M UHN', 'email' => 'keuangan@harkatnegeri.ac.id', 'nidn_nim' => 'KEU-P3M-01'],
            'Superadmin' => ['name' => 'Super Administrator UHN', 'email' => 'superadmin@harkatnegeri.ac.id', 'nidn_nim' => 'ROOT-UHN-01'],
            ];
            $roleName = $request->query('demo_role', 'Dosen / Pengusul');
            $identity = $identities[$roleName] ?? $identities['Dosen / Pengusul'];
            $providerUserId = 'demo-'.$identity['email'];
            $user = User::where('email', $identity['email'])->first();
        }

        if (!$user) {
            $fakultas = RefFakultas::first();
            $prodi = RefProgramStudi::first();

            $user = User::create([
                'name' => $identity['name'],
                'email' => $identity['email'],
                'password' => Hash::make('password'),
                'nidn_nim' => $identity['nidn_nim'],
                'jabatan_fungsional' => 'Lektor',
                'sinta_id' => 'SINTA-' . rand(1000, 9999),
                'sinta_score_3yr' => 150.00,
                'sinta_score_overall' => 280.00,
                'id_fakultas' => $fakultas?->id_fakultas,
                'id_prodi' => $prodi?->id_prodi,
                'is_otp_verified' => false,
            ]);

            $roleObj = $roleName ? Role::where('name', $roleName)->first() : null;
            if ($roleObj) {
                $user->roles()->sync([$roleObj->id]);
            }
        }

        ExternalIdentity::updateOrCreate(
            ['provider' => config('services.siakad.enabled') ? 'siakad' : 'demo', 'provider_user_id' => $providerUserId],
            [
                'user_id' => $user->id,
                'identifier_type' => $identity['nidn_nim'] ? (isset($profile['nim']) ? 'nim' : 'nidn') : null,
                'identifier_value' => $identity['nidn_nim'],
                'metadata' => config('services.siakad.enabled') ? $profile : ['role' => $roleName],
                'last_synced_at' => now(),
            ]
        );

        // Reset OTP state upon new SSO login attempt
        $user->update(['is_otp_verified' => false]);
        Auth::login($user);
        $request->session()->regenerate();

        // Generate 6-Digit OTP Code (US-02.2)
        $otpCode = (string) rand(100000, 999999);
        Cache::put('otp_code_' . $user->id, $otpCode, now()->addMinutes(5));

        // Audit Trail Logging (US-02.4)
        AuditLogService::log('SSO_OAUTH_LOGIN_SUCCESS', null, [
            'email' => $user->email,
            'role' => $user->primaryRoleName(),
            'ip' => $request->ip(),
            'otp_generated' => true
        ], $user->id);

        return redirect()->route('otp.show')->with('info', "Kode OTP 6-digit baru telah terkirim ke surel {$user->email} (Kode Simulasi: {$otpCode}).");
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        $userId = Auth::id();
        if ($userId) {
            AuditLogService::log('LOGOUT', null, ['user_id' => $userId], $userId);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing')->with('success', 'Anda telah berhasil keluar dari sistem PRISMA UHN.');
    }
}

