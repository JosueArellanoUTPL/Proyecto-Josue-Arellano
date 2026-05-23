<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

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

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Usuario que ejecutó la acción.
     * Puede ser null si el usuario fue eliminado después.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
