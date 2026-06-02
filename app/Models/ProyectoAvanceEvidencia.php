<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\ProyectoAvance;

class ProyectoAvanceEvidencia extends Model
{
    use HasFactory;

    // Tabla donde se guardan los archivos de evidencias de proyectos.
    protected $table = 'proyecto_avance_evidencias';

    // Datos guardados por cada archivo subido.
    protected $fillable = [
        'proyecto_avance_id',
        'path',
        'original_name',
        'mime_type',
        'size'
    ];

    public function avance()
    {
        // La evidencia pertenece a un avance de proyecto.
        return $this->belongsTo(ProyectoAvance::class, 'proyecto_avance_id');
    }

    public function isImage(): bool
    {
        // Sirve para saber si puedo mostrar miniatura de la evidencia.
        return (bool) ($this->mime_type && str_starts_with($this->mime_type, 'image/'));
    }
}
