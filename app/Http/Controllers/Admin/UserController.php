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
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(email) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhere('nidn_nim', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->whereHas('roles', fn($rq) => $rq->where('name', $role));
        }

        $users = $query->latest('id')->paginate(15)->withQueryString();
        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles'));
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
            'email' => 'required|email|max:255|unique:users,email',
            'nidn_nim' => 'nullable|string|max:50|unique:users,nidn_nim',
            'password' => 'required|string|min:6',
            'phone_number' => 'nullable|string|max:30',
            'jabatan_fungsional' => 'nullable|string|max:100',
            'sinta_id' => 'nullable|string|max:50',
            'id_fakultas' => 'nullable|integer',
            'id_prodi' => 'nullable|integer',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'nidn_nim.unique' => 'NIDN/NIM sudah terdaftar.',
            'password.required' => 'Kata sandi awal wajib diisi.',
            'roles.required' => 'Pilih minimal satu peran (role) untuk akun ini.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower(trim($validated['email'])),
            'nidn_nim' => !empty($validated['nidn_nim']) ? trim($validated['nidn_nim']) : null,
            'password' => Hash::make($validated['password']), // Securely hashed!
            'phone_number' => $validated['phone_number'] ?? null,
            'jabatan_fungsional' => $validated['jabatan_fungsional'] ?? null,
            'sinta_id' => $validated['sinta_id'] ?? null,
            'id_fakultas' => $validated['id_fakultas'] ?? null,
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

        return redirect()->route('admin.users.index')->with('success', "Akun pengguna '{$user->name}' berhasil dibuat.");
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
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'nidn_nim' => 'nullable|string|max:50|unique:users,nidn_nim,' . $user->id,
            'password' => 'nullable|string|min:6',
            'phone_number' => 'nullable|string|max:30',
            'jabatan_fungsional' => 'nullable|string|max:100',
            'sinta_id' => 'nullable|string|max:50',
            'id_fakultas' => 'nullable|integer',
            'id_prodi' => 'nullable|integer',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
        ]);

        $user->name = $validated['name'];
        $user->email = strtolower(trim($validated['email']));
        $user->nidn_nim = !empty($validated['nidn_nim']) ? trim($validated['nidn_nim']) : null;
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']); // Securely hashed!
        }
        $user->phone_number = $validated['phone_number'] ?? null;
        $user->jabatan_fungsional = $validated['jabatan_fungsional'] ?? null;
        $user->sinta_id = $validated['sinta_id'] ?? null;
        $user->id_fakultas = $validated['id_fakultas'] ?? null;
        $user->id_prodi = $validated['id_prodi'] ?? null;
        $user->save();

        $user->roles()->sync($validated['roles']);

        AuditLogService::log('admin_updated_user', null, [
            'updated_user_id' => $user->id,
            'updated_by' => $admin->id,
        ]);

        return redirect()->route('admin.users.index')->with('success', "Data akun '{$user->name}' berhasil diperbarui.");
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user): RedirectResponse
    {
        $admin = Auth::user();
        abort_unless($admin->hasRole(['Admin P3M', 'Kepala P3M', 'Superadmin']), 403, 'Akses terbatas.');

        if ($user->id === $admin->id) {
            return redirect()->back()->with('warning', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $userName = $user->name;
        $user->roles()->detach();
        $user->delete();

        AuditLogService::log('admin_deleted_user', null, [
            'deleted_user_name' => $userName,
            'deleted_by' => $admin->id,
        ]);

        return redirect()->route('admin.users.index')->with('success', "Akun '{$userName}' berhasil dihapus.");
    }
}

