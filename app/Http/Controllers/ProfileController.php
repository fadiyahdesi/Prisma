<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display user profile edit form.
     */
    public function edit(): View
    {
        $user = Auth::user();
        $user->load(['roles', 'fakultas', 'prodi']);

        // Check if user still has empty or placeholder email
        $isPlaceholderEmail = empty($user->email) || str_starts_with((string) $user->email, 'dosen.');

        return view('profile.edit', compact('user', 'isPlaceholderEmail'));
    }

    /**
     * Update personal profile information and avatar.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nipy' => 'nullable|string|max:50',
            'nidn_nim' => 'nullable|string|max:100',
            'jabatan' => 'nullable|string|max:100',
            'jabatan_fungsional' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:30',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar oleh pengguna lain.',
            'avatar.image' => 'File foto profil harus berupa gambar.',
            'avatar.mimes' => 'Format foto profil yang diizinkan: JPG, JPEG, PNG, WEBP.',
            'avatar.max' => 'Ukuran foto profil maksimal 2 MB.',
        ]);

        // Handle Avatar Upload
        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        $user->name = trim($validated['name']);
        if ($request->has('nipy')) {
            $user->nipy = !empty($validated['nipy']) ? trim($validated['nipy']) : null;
        }
        if ($request->has('nidn_nim')) {
            $user->nidn_nim = !empty($validated['nidn_nim']) ? trim($validated['nidn_nim']) : null;
        }
        if ($request->has('jabatan')) {
            $user->jabatan = !empty($validated['jabatan']) ? trim($validated['jabatan']) : $user->jabatan;
        }
        if ($request->has('jabatan_fungsional')) {
            $user->jabatan_fungsional = !empty($validated['jabatan_fungsional']) ? trim($validated['jabatan_fungsional']) : $user->jabatan_fungsional;
        }
        $user->email = !empty($validated['email']) ? strtolower(trim($validated['email'])) : null;
        $user->phone_number = $validated['phone_number'] ?? null;
        $user->save();

        AuditLogService::log('user_profile_updated', null, [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return redirect()->route('profile.edit')->with('success', 'Profil dan informasi pribadi berhasil diperbarui.');
    }

    /**
     * Update user password securely.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            // Allow fallback check against NIDN if password was never reset
            if (empty($user->nidn_nim) || $request->current_password !== $user->nidn_nim) {
                return redirect()->back()->withErrors(['current_password' => 'Kata sandi saat ini tidak sesuai.']);
            }
        }

        $user->password = Hash::make($request->password);
        $user->save();

        AuditLogService::log('user_password_changed', null, [
            'user_id' => $user->id,
        ]);

        return redirect()->route('profile.edit')->with('success', 'Kata sandi berhasil diperbarui dan diamankan.');
    }
}

