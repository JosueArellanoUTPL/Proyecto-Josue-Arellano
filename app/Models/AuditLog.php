<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    // Campos permitidos.
    protected $fillable = [
        'user_id',
        'module',
        'action',
        'method',
        'route_name',
        'url',
        'ip_address',
        'description',
        'metadata',
    ];

    // Conversion de tipos.
    protected $casts = [
        'metadata' => 'array',
    ];

    // Relacion con el usuario.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
