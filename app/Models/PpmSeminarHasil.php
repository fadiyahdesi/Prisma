<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpmSeminarHasil extends Model
{
    use HasFactory;

    protected $table = 'ppm_seminar_hasil';

    protected $fillable = [
        'id_usulan',
        'jadwal_seminar',
        'ruangan_or_link',
        'id_penguji_1',
        'id_penguji_2',
        'skor_seminar',
        'catatan_penguji',
        'status_seminar',
    ];

    protected function casts(): array
    {
        return [
            'jadwal_seminar' => 'datetime',
            'skor_seminar' => 'decimal:2',
        ];
    }

    public function usulan(): BelongsTo
    {
        return $this->belongsTo(PpmUsulan::class, 'id_usulan');
    }

    public function penguji1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_penguji_1');
    }

    public function penguji2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_penguji_2');
    }
}

