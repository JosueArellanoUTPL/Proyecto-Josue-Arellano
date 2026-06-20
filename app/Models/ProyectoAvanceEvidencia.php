<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProyectoAvanceEvidencia extends Model
{
    use HasFactory;

    // Nombre de la tabla.
    protected $table = 'proyecto_avance_evidencias';

    // Campos permitidos.
    protected $fillable = [
        'proyecto_avance_id',
        'path',
        'original_name',
        'mime_type',
        'size',
    ];

    // Relacion con el avance.
    public function avance()
    {
        return $this->belongsTo(ProyectoAvance::class, 'proyecto_avance_id');
    }

    // Comprobar si el archivo es una imagen.
    public function isImage(): bool
    {
        return (bool) ($this->mime_type && str_starts_with($this->mime_type, 'image/'));
    }
}
