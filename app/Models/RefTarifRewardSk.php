<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefTarifRewardSk extends Model
{
    use HasFactory;

    protected $table = 'ref_tarif_reward_sk';

    protected $fillable = [
        'kategori',
        'sub_kategori',
        'nominal_insentif',
        'nomor_sk_rektor',
        'keterangan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'nominal_insentif' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}

