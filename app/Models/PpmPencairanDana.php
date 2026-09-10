<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpmPencairanDana extends Model
{
    use HasFactory;

    protected $table = 'ppm_pencairan_dana';

    protected $fillable = [
        'id_kontrak',
        'termin',
        'persentase',
        'jumlah_dana',
        'nomor_referensi',
        'tanggal_transfer',
        'file_bukti_transfer',
        'status_pencairan',
        'processed_by',
        'processed_at',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'termin' => 'integer',
            'persentase' => 'decimal:2',
            'jumlah_dana' => 'decimal:2',
            'tanggal_transfer' => 'date',
            'processed_at' => 'datetime',
        ];
    }

    public function kontrak(): BelongsTo
    {
        return $this->belongsTo(PpmKontrak::class, 'id_kontrak');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}

