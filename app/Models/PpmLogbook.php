<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpmLogbook extends Model
{
    use HasFactory;

    protected $table = 'ppm_logbook';

    protected $fillable = [
        'id_usulan',
        'tanggal',
        'aktivitas',
        'persentase_capaian',
        'file_bukti',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'persentase_capaian' => 'decimal:2',
        ];
    }

    public function usulan(): BelongsTo
    {
        return $this->belongsTo(PpmUsulan::class, 'id_usulan');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

