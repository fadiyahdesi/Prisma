<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpmUsulanRab extends Model
{
    use HasFactory;

    protected $table = 'ppm_usulan_rab';

    protected $fillable = [
        'id_usulan',
        'pos_belanja',
        'item_keterangan',
        'volume',
        'satuan',
        'harga_satuan',
        'total_harga',
    ];

    protected $casts = [
        'volume' => 'integer',
        'harga_satuan' => 'float',
        'total_harga' => 'float',
    ];

    public function usulan(): BelongsTo
    {
        return $this->belongsTo(PpmUsulan::class, 'id_usulan');
    }
}

