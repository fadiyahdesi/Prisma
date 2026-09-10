<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PpmSkemaBima extends Model
{
    use HasFactory;

    protected $table = 'ppm_skema_bima';

    protected $fillable = [
        'kode_skema',
        'nama_skema',
        'kategori',
        'min_jafung',
        'min_sinta_3yr',
        'min_sinta_overall',
        'min_tkt',
        'max_tkt',
        'min_sdgs',
        'plafon_dana',
        'is_active',
        'rubrik_penilaian',
    ];

    protected $casts = [
        'min_jafung' => 'array',
        'min_sinta_3yr' => 'float',
        'min_sinta_overall' => 'float',
        'min_tkt' => 'integer',
        'max_tkt' => 'integer',
        'min_sdgs' => 'integer',
        'plafon_dana' => 'float',
        'is_active' => 'boolean',
        'rubrik_penilaian' => 'array',
    ];
}

