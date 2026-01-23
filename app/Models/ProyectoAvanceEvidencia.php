<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Modelo relacionado
use App\Models\ProyectoAvance;

/**
 * Modelo ProyectoAvanceEvidencia
 *
 * Representa un archivo (evidencia) asociado a un avance de proyecto.
 * Se guarda:
 * - path (ruta en storage/app/public/...)
 * - nombre original del archivo
 * - tipo MIME (para identificar imágenes)
 * - tamaño del archivo
 */
class ProyectoAvanceEvidencia extends Model
{
    use HasFactory;

    /**
     * Nombre real de la tabla (por claridad).
     * Si la migración ya creó esta tabla con ese nombre, queda consistente.
     */
    protected $table = 'proyecto_avance_evidencias';

    /**
     * Campos permitidos para create() / update().
     */
    protected $fillable = [
        'proyecto_avance_id',
        'path',
        'original_name',
        'mime_type',
        'size'
    ];

    /**
     * Relación: la evidencia pertenece a un avance.
     */
    public function avance()
    {
        return $this->belongsTo(ProyectoAvance::class, 'proyecto_avance_id');
    }

    /**
     * Identifica si el archivo es una imagen para mostrar miniatura.
     */
    public function isImage(): bool
    {
        return (bool) ($this->mime_type && str_starts_with($this->mime_type, 'image/'));
    }
}
