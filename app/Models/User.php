<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'no_urut',
        'name',
        'nipy',
        'email',
        'avatar',
        'password',
        'nidn_nim',
        'jabatan',
        'rumpun_ilmu',
        'bidang_keahlian',
        'reviewer_application_status',
        'reviewer_applied_at',
        'reviewer_expertise_notes',
        'jabatan_fungsional',
        'sinta_id',
        'sinta_score_3yr',
        'sinta_score_overall',
        'h_index_scopus',
        'h_index_google_scholar',
        'phone_number',
        'id_fakultas',
        'id_prodi',
        'is_otp_verified',
        'otp_failed_attempts',
        'otp_locked_until',
        'last_login_ip',
        'last_login_at',
        'last_sinta_sync_at',
        'is_sinta_manual_fallback',
        'sinta_proof_file',
        'sinta_verification_status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_otp_verified' => 'boolean',
            'is_sinta_manual_fallback' => 'boolean',
            'otp_locked_until' => 'datetime',
            'last_login_at' => 'datetime',
            'last_sinta_sync_at' => 'datetime',
            'reviewer_applied_at' => 'datetime',
            'sinta_score_3yr' => 'decimal:2',
            'sinta_score_overall' => 'decimal:2',
            'h_index_scopus' => 'integer',
            'h_index_google_scholar' => 'integer',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'model_has_roles', 'user_id', 'role_id');
    }

    public function hasRole(string|array $roleName): bool
    {
        if (is_array($roleName)) {
            return $this->roles()->whereIn('name', $roleName)->exists();
        }
        return $this->roles()->where('name', $roleName)->exists();
    }

    public function scopeRole($query, string|array $roles)
    {
        $roles = (array) $roles;
        return $query->whereHas('roles', function ($q) use ($roles) {
            $q->whereIn('name', $roles);
        });
    }

    public function primaryRoleName(): string
    {
        $roleNames = $this->roles->pluck('name')->all();
        if (empty($roleNames)) {
            $roleNames = $this->roles()->pluck('name')->all();
        }

        $priority = [
            'Superadmin',
            'Kepala P3M',
            'Admin P3M',
            'Divisi Penelitian',
            'Divisi Pengabdian',
            'Divisi Publikasi',
            'Keuangan',
            'Dekanat',
            'Kaprodi',
            'Dosen / Pengusul',
            'Reviewer',
            'Dosen / Mahasiswa Anggota',
        ];

        foreach ($priority as $candidate) {
            if (in_array($candidate, $roleNames, true)) {
                return $candidate;
            }
        }

        return $roleNames[0] ?? 'Dosen / Pengusul';
    }

    /**
     * Resolve effective active role (respects session active_role when present).
     */
    public function effectiveRoleName(): string
    {
        $sessionRole = session('active_role');
        if (!empty($sessionRole)) {
            return $sessionRole;
        }

        return $this->primaryRoleName();
    }

    /**
     * Resolve effective permission names for the active session role (or union of DB roles).
     */
    public function effectivePermissionNames(): array
    {
        $sessionRole = session('active_role');

        if (!empty($sessionRole)) {
            if ($sessionRole === 'Superadmin') {
                return array_column(Permission::catalog(), 'name');
            }

            $roleObj = Role::where('name', $sessionRole)->with('permissions:id,name')->first();
            if ($roleObj) {
                return $roleObj->permissions->pluck('name')->all();
            }
        }

        $roles = $this->relationLoaded('roles') ? $this->roles : $this->roles()->get();
        if ($roles->contains('name', 'Superadmin')) {
            return array_column(Permission::catalog(), 'name');
        }

        $roleIds = $roles->pluck('id')->all();
        if (empty($roleIds)) {
            return [];
        }

        return \Illuminate\Support\Facades\DB::table('role_has_permissions')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->whereIn('role_has_permissions.role_id', $roleIds)
            ->distinct()
            ->pluck('permissions.name')
            ->all();
    }

    /**
     * Check whether the user has a given feature permission.
     * Superadmin automatically has full access to every feature.
     */
    public function hasPermission(string $permission): bool
    {
        $sessionRole = session('active_role');
        if (!empty($sessionRole)) {
            if ($sessionRole === 'Superadmin') {
                return true;
            }
        } elseif ($this->hasRole('Superadmin')) {
            return true;
        }

        return in_array($permission, $this->effectivePermissionNames(), true);
    }

    /**
     * Check whether the user holds at least one of the listed feature permissions.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        $sessionRole = session('active_role');
        if (!empty($sessionRole)) {
            if ($sessionRole === 'Superadmin') {
                return true;
            }
        } elseif ($this->hasRole('Superadmin')) {
            return true;
        }

        $held = $this->effectivePermissionNames();
        foreach ($permissions as $perm) {
            if (in_array($perm, $held, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check whether the user holds full-dataset access for a division module.
     * Modules: 'penelitian', 'pengabdian', 'publikasi', 'hki'.
     */
    public function isFullDivisionFor(string $module): bool
    {
        $permMap = [
            'penelitian' => 'penelitian.manage_all',
            'pengabdian' => 'pengabdian.manage_all',
            'publikasi' => 'publikasi.manage_all',
            'hki' => 'hki.manage_all',
        ];

        $requiredPerm = $permMap[strtolower(trim($module))] ?? null;
        if (!$requiredPerm) {
            return false;
        }

        return $this->hasPermission($requiredPerm);
    }

    /**
     * Extract clean lecturer name without academic titles for author matching.
     */
    public function cleanedBaseName(): string
    {
        $firstSegment = trim(explode(',', (string) $this->name)[0]);
        $cleaned = preg_replace('/^(?:(?:Prof|Dr|Ir|apt|Bdn|Ns|H|Hj)\.?\s+)+/i', '', $firstSegment);

        return trim((string) $cleaned);
    }

    public function isOtpLocked(): bool
    {
        return $this->otp_locked_until && $this->otp_locked_until->isFuture();
    }

    public function fakultas()
    {
        return $this->belongsTo(RefFakultas::class, 'id_fakultas', 'id_fakultas');
    }

    public function prodi()
    {
        return $this->belongsTo(RefProgramStudi::class, 'id_prodi', 'id_prodi');
    }

    public function externalIdentities()
    {
        return $this->hasMany(ExternalIdentity::class);
    }
}
