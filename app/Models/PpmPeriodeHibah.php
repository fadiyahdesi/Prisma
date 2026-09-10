<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PpmPeriodeHibah extends Model
{
    use HasFactory;

    protected $table = 'ppm_periode_hibah';

    protected $fillable = [
        'tahun_akademik',
        'semester',
        'nama_periode',
        'waktu_buka',
        'waktu_tutup',
        'is_active',
        'keterangan',
    ];

    protected $casts = [
        'waktu_buka' => 'datetime',
        'waktu_tutup' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Scope for open and active period based on server time.
     */
    public function scopeOpenNow($query)
    {
        $now = now();
        return $query->where('is_active', true)
                     ->where('waktu_buka', '<=', $now)
                     ->where('waktu_tutup', '>=', $now);
    }

    public function getIdPeriodeHibahAttribute()
    {
        return $this->attributes['id'] ?? null;
    }

    public function getTahunAnggaranAttribute()
    {
        if (!empty($this->attributes['tahun_anggaran'])) {
            return $this->attributes['tahun_anggaran'];
        }
        if (!empty($this->attributes['tahun_akademik'])) {
            return (int) substr($this->attributes['tahun_akademik'], 0, 4);
        }
        return (int) date('Y');
    }
}

