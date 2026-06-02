<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    // Campos que guarda cada registro de auditoria.
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

    // Metadata se guarda como JSON y Laravel la devuelve como array.
    protected $casts = [
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        // Usuario que hizo la accion. Puede quedar null si se elimina el usuario.
        return $this->belongsTo(User::class);
    }
}
