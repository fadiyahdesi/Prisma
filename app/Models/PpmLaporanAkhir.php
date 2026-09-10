<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpmLaporanAkhir extends Model
{
    use HasFactory;

    protected $table = 'ppm_laporan_akhir';

    protected $fillable = [
        'id_usulan',
        'file_laporan_akhir',
        'file_sptb_100',
        'ringkasan_hasil',
        'verification_token',
        'is_approved_p3m',
        'approved_by_p3m_at',
    ];

    protected function casts(): array
    {
        return [
            'is_approved_p3m' => 'boolean',
            'approved_by_p3m_at' => 'datetime',
        ];
    }

    public function usulan(): BelongsTo
    {
        return $this->belongsTo(PpmUsulan::class, 'id_usulan');
    }
}

