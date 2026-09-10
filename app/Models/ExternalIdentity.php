<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalIdentity extends Model
{
    protected $fillable = [
        'user_id',
        'provider',
        'provider_user_id',
        'identifier_type',
        'identifier_value',
        'metadata',
        'last_synced_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'last_synced_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
