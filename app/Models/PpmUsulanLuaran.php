<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpmUsulanLuaran extends Model
{
    use HasFactory;

    protected $table = 'ppm_usulan_luaran';

    protected $fillable = [
        'id_usulan',
        'jenis_luaran',
        'kategori_luaran',
        'target_status',
        'keterangan',
    ];

    public function usulan(): BelongsTo
    {
        return $this->belongsTo(PpmUsulan::class, 'id_usulan');
    }
}

