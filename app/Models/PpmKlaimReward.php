<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PpmKlaimReward extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ppm_klaim_reward';

    protected $fillable = [
        'nomor_klaim',
        'user_id',
        'jenis_klaim',
        'id_publikasi',
        'id_hki',
        'kategori_insentif',
        'tarif_dasar_sk',
        'total_reward',
        'file_surat_pernyataan',
        'status_klaim',
        'approved_by_p3m',
        'approved_at',
        'catatan_p3m',
    ];

    protected function casts(): array
    {
        return [
            'tarif_dasar_sk' => 'decimal:2',
            'total_reward' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function publikasi(): BelongsTo
    {
        return $this->belongsTo(PpmPublikasiJurnal::class, 'id_publikasi');
    }

    public function hki(): BelongsTo
    {
        return $this->belongsTo(PpmHki::class, 'id_hki');
    }

    public function distribusi(): HasMany
    {
        return $this->hasMany(PpmRewardDistribusi::class, 'id_klaim_reward');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_p3m');
    }

    public function isApproved(): bool
    {
        return in_array($this->status_klaim, ['Approved_P3M', 'Disbursed']);
    }

    public function isFullyDisbursed(): bool
    {
        if ($this->distribusi->isEmpty()) {
            return false;
        }

        return $this->distribusi->every(fn($d) => $d->status_transfer === 'Disbursed');
    }
}

