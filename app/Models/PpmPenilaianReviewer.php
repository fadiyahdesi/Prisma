<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PpmPenilaianReviewer extends Model
{
    use HasFactory;

    protected $table = 'ppm_penilaian_reviewer';

    protected $fillable = [
        'id_penugasan',
        'skor_kriteria',
        'total_skor',
        'komentar_kualitatif',
        'rekomendasi',
        'is_locked',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'skor_kriteria' => 'array',
            'total_skor' => 'decimal:2',
            'is_locked' => 'boolean',
            'submitted_at' => 'datetime',
        ];
    }

    public function penugasan()
    {
        return $this->belongsTo(PpmPenugasanReviewer::class, 'id_penugasan');
    }
}

