<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SslCertificate extends Model
{
    protected $fillable = [
        'ssl_certificate',
        'ssl_private_key',
        'resource_type',
        'resource_id',
        'server_id',
        'common_name',
        'subject_alternative_names',
        'valid_until',
        'is_ca_certificate',
    ];

    protected $casts = [
        'ssl_certificate' => 'encrypted',
        'ssl_private_key' => 'encrypted',
        'subject_alternative_names' => 'array',
        'valid_until' => 'datetime',
    ];

    public function resource()
    {
        return $this->morphTo();
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}
