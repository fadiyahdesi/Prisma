<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpmRewardDistribusi extends Model
{
    use HasFactory;

    protected $table = 'ppm_reward_distribusi';

    protected $fillable = [
        'id_klaim_reward',
        'user_id',
        'nama_penulis',
        'nidn_nim',
        'email',
        'peran_penulis',
        'persentase',
        'nominal_bagian',
        'nama_bank',
        'nomor_rekening',
        'nama_pemilik_rekening',
        'status_transfer',
        'tanggal_transfer',
        'nomor_referensi',
        'file_bukti_transfer',
    ];

    protected function casts(): array
    {
        return [
            'persentase' => 'decimal:2',
            'nominal_bagian' => 'decimal:2',
            'tanggal_transfer' => 'date',
        ];
    }

    public function klaimReward(): BelongsTo
    {
        return $this->belongsTo(PpmKlaimReward::class, 'id_klaim_reward');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

