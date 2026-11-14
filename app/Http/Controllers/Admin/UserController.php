<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\RefFakultas;
use App\Models\RefProgramStudi;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * List all users with filtering and search.
     */
    public function index(Request $request): View
    {
        $admin = Auth::user();
        abort_unless($admin->hasRole(['Admin P3M', 'Kepala P3M', 'Superadmin']), 403, 'Akses terbatas untuk Pengelola Akun.');

        $query = User::with(['roles', 'fakultas', 'prodi']);

        if ($search = $request->input('search')) {
            $s = strtolower(trim($search));
            $query->where(function ($q) use ($s, $search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$s}%"])
                    ->orWhereRaw('LOWER(COALESCE(email, \'\')) LIKE ?', ["%{$s}%"])
                    ->orWhere('nipy', 'like', "%{$search}%")
                    ->orWhere('nidn_nim', 'like', "%{$search}%")
                    ->orWhereRaw('LOWER(COALESCE(jabatan, \'\')) LIKE ?', ["%{$s}%"])
                    ->orWhereRaw('LOWER(COALESCE(jabatan_fungsional, \'\')) LIKE ?', ["%{$s}%"]);
            });
        }

        if ($role = $request->input('role')) {
            $query->whereHas('roles', fn ($rq) => $rq->where('name', $role));
        }

        if ($prodiId = $request->input('prodi')) {
            $query->where('id_prodi', $prodiId);
        }

        $users = $query->orderBy('id')->paginate(20)->withQueryString();
        $roles = Role::orderBy('name')->get();
        $prodis = RefProgramStudi::orderBy('nama_prodi')->get();

        return view('admin.users.index', compact('users', 'roles', 'prodis'));
    }

    /**
     * Show form to create new user.
     */
    public function create(): View
    {
        $admin = Auth::user();
        abort_unless($admin->hasRole(['Admin P3M', 'Kepala P3M', 'Superadmin']), 403, 'Akses terbatas.');

        $roles = Role::orderBy('name')->get();
        $fakultas = RefFakultas::orderBy('nama_fakultas')->get();
        $prodis = RefProgramStudi::orderBy('nama_prodi')->get();

        return view('admin.users.create', compact('roles', 'fakultas', 'prodis'));
    }

    /**
     * Store newly created user.
     */
    public function store(Request $request): RedirectResponse
    {
        $admin = Auth::user();
        abort_unless($admin->hasRole(['Admin P3M', 'Kepala P3M', 'Superadmin']), 403, 'Akses terbatas.');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nipy' => 'nullable|string|max:50',
            'nidn_nim' => 'nullable|string|max:100',
            'jabatan' => 'nullable|string|max:100',
            'jabatan_fungsional' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'phone_number' => 'nullable|string|max:30',
            'sinta_id' => 'nullable|string|max:50',
            'id_fakultas' => 'nullable|integer',
            'id_prodi' => 'nullable|integer',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Kata sandi awal wajib diisi.',
            'roles.required' => 'Pilih minimal satu peran (role) untuk akun ini.',
        ]);

        // Auto-resolve id_fakultas from id_prodi if id_fakultas is blank
        $fakultasId = $validated['id_fakultas'] ?? null;
        if (!$fakultasId && !empty($validated['id_prodi'])) {
            $fakultasId = RefProgramStudi::where('id_prodi', $validated['id_prodi'])->value('id_fakultas');
        }

        $user = User::create([
            'name' => trim($validated['name']),
            'nipy' => !empty($validated['nipy']) ? trim($validated['nipy']) : null,
            'nidn_nim' => !empty($validated['nidn_nim']) ? trim($validated['nidn_nim']) : null,
            'jabatan' => !empty($validated['jabatan']) ? trim($validated['jabatan']) : 'Dosen',
            'jabatan_fungsional' => !empty($validated['jabatan_fungsional']) ? trim($validated['jabatan_fungsional']) : null,
            'email' => !empty($validated['email']) ? strtolower(trim($validated['email'])) : null,
            'password' => Hash::make($validated['password']),
            'phone_number' => $validated['phone_number'] ?? null,
            'sinta_id' => $validated['sinta_id'] ?? null,
            'id_fakultas' => $fakultasId,
            'id_prodi' => $validated['id_prodi'] ?? null,
            'is_otp_verified' => true,
            'email_verified_at' => now(),
        ]);

        $user->roles()->sync($validated['roles']);

        AuditLogService::log('admin_created_user', null, [
            'created_user_id' => $user->id,
            'created_email' => $user->email,
            'created_by' => $admin->id,
        ]);

        return redirect()->route('admin.users.index')->with('success', "Akun dosen/pengguna '{$user->name}' berhasil ditambahkan.");
    }

    /**
     * Show form to edit user.
     */
    public function edit(User $user): View
    {
        $admin = Auth::user();
        abort_unless($admin->hasRole(['Admin P3M', 'Kepala P3M', 'Superadmin']), 403, 'Akses terbatas.');

        $roles = Role::orderBy('name')->get();
        $fakultas = RefFakultas::orderBy('nama_fakultas')->get();
        $prodis = RefProgramStudi::orderBy('nama_prodi')->get();
        $user->load('roles');

        return view('admin.users.edit', compact('user', 'roles', 'fakultas', 'prodis'));
    }

    /**
     * Update user details.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $admin = Auth::user();
        abort_unless($admin->hasRole(['Admin P3M', 'Kepala P3M', 'Superadmin']), 403, 'Akses terbatas.');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nipy' => 'nullable|string|max:50',
            'nidn_nim' => 'nullable|string|max:100',
            'jabatan' => 'nullable|string|max:100',
            'jabatan_fungsional' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'phone_number' => 'nullable|string|max:30',
            'sinta_id' => 'nullable|string|max:50',
            'id_fakultas' => 'nullable|integer',
            'id_prodi' => 'nullable|integer',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
        ]);

        $fakultasId = $validated['id_fakultas'] ?? null;
        if (!$fakultasId && !empty($validated['id_prodi'])) {
            $fakultasId = RefProgramStudi::where('id_prodi', $validated['id_prodi'])->value('id_fakultas');
        }

        $user->name = trim($validated['name']);
        $user->nipy = !empty($validated['nipy']) ? trim($validated['nipy']) : null;
        $user->nidn_nim = !empty($validated['nidn_nim']) ? trim($validated['nidn_nim']) : null;
        $user->jabatan = !empty($validated['jabatan']) ? trim($validated['jabatan']) : 'Dosen';
        $user->jabatan_fungsional = !empty($validated['jabatan_fungsional']) ? trim($validated['jabatan_fungsional']) : null;
        $user->email = !empty($validated['email']) ? strtolower(trim($validated['email'])) : null;
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->phone_number = $validated['phone_number'] ?? null;
        $user->sinta_id = $validated['sinta_id'] ?? null;
        $user->id_fakultas = $fakultasId;
        $user->id_prodi = $validated['id_prodi'] ?? null;
        $user->save();

        $user->roles()->sync($validated['roles']);

        AuditLogService::log('admin_updated_user', null, [
            'updated_user_id' => $user->id,
            'updated_email' => $user->email,
            'updated_by' => $admin->id,
        ]);

        return redirect()->route('admin.users.index')->with('success', "Data dosen/pengguna '{$user->name}' berhasil diperbarui.");
    }

    /**
     * Delete user account.
     */
    public function destroy(User $user): RedirectResponse
    {
        $admin = Auth::user();
        abort_unless($admin->hasRole(['Admin P3M', 'Kepala P3M', 'Superadmin']), 403, 'Akses terbatas.');

        if ($user->id === $admin->id) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $deletedName = $user->name;
        $deletedId = $user->id;

        $user->roles()->detach();
        $user->delete();

        AuditLogService::log('admin_deleted_user', null, [
            'deleted_user_id' => $deletedId,
            'deleted_name' => $deletedName,
            'deleted_by' => $admin->id,
        ]);

        return redirect()->route('admin.users.index')->with('success', "Akun '{$deletedName}' telah dihapus dari sistem.");
    }
}
