<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Modelos relacionados
use App\Models\Proyecto;
use App\Models\User;
use App\Models\ProyectoAvanceEvidencia;

/**
 * Modelo ProyectoAvance
 *
 * Representa un registro de avance (seguimiento) de un proyecto:
 * - fecha del avance
 * - porcentaje de avance (0 a 100)
 * - comentario opcional
 * - usuario que registró el avance
 *
 * El progreso del proyecto se basa en el último avance registrado.
 */
class ProyectoAvance extends Model
{
    use HasFactory;

    /**
     * Campos permitidos para create() / update().
     */
    protected $fillable = [
        'proyecto_id',
        'user_id',
        'fecha',
        'porcentaje_avance',
        'comentario'
    ];

    /**
     * Casts automáticos:
     * - fecha se trata como date (Carbon)
     * - porcentaje_avance se trata como decimal para evitar strings
     */
    protected $casts = [
        'fecha' => 'date',
        'porcentaje_avance' => 'decimal:2',
    ];

    /**
     * Relación: el avance pertenece a un proyecto.
     */
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    /**
     * Relación: el avance pertenece al usuario que lo registró.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: un avance puede tener múltiples evidencias (archivos).
     * Cada evidencia es un registro independiente (no se sobrescribe).
     */
    public function evidencias()
    {
        return $this->hasMany(ProyectoAvanceEvidencia::class, 'proyecto_avance_id');
    }
}
