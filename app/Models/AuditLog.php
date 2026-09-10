<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'ppm_audit_logs';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'action',
        'ip_address',
        'user_agent',
        'payload_sebelum',
        'payload_sesudah',
        'created_at',
    ];

    protected $casts = [
        'payload_sebelum' => 'array',
        'payload_sesudah' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}

