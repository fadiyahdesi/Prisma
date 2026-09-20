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
        'name',
        'email',
        'avatar',
        'password',
        'nidn_nim',
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
        $role = $this->roles()->first();
        return $role ? $role->name : 'Dosen/Pengusul';
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
