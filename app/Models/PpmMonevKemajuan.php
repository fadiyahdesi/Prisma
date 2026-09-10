<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpmMonevKemajuan extends Model
{
    use HasFactory;

    protected $table = 'ppm_monev_kemajuan';

    protected $fillable = [
        'id_usulan',
        'file_laporan_kemajuan',
        'file_sptb_70',
        'ringkasan_kemajuan',
        'persentase_kemajuan',
        'id_reviewer',
        'skor_monev',
        'catatan_evaluasi',
        'rekomendasi',
        'status',
        'evaluated_at',
    ];

    protected function casts(): array
    {
        return [
            'persentase_kemajuan' => 'decimal:2',
            'skor_monev' => 'decimal:2',
            'evaluated_at' => 'datetime',
        ];
    }

    public function usulan(): BelongsTo
    {
        return $this->belongsTo(PpmUsulan::class, 'id_usulan');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_reviewer');
    }
}

