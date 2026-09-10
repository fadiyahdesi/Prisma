<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class PpmHki extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ppm_hki';

    protected $fillable = [
        'user_id',
        'jenis_hki',
        'judul_hki',
        'nomor_permohonan',
        'nomor_sertifikat',
        'tanggal_permohonan',
        'tanggal_terbit',
        'pemegang_hak',
        'file_sertifikat',
        'status_hki',
        'verified_by_user_id',
        'verified_at',
        'catatan_verifikasi',
        'is_claimed_reward',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_permohonan' => 'date',
            'tanggal_terbit' => 'date',
            'verified_at' => 'datetime',
            'is_claimed_reward' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }

    public function klaimReward(): HasOne
    {
        return $this->hasOne(PpmKlaimReward::class, 'id_hki');
    }

    public function isVerified(): bool
    {
        return $this->status_hki === 'Terverifikasi HKI';
    }
}

