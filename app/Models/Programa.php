<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programa extends Model
{
    use HasFactory;

    // Nombre de la tabla.
    protected $table = 'programas';

    // Campos permitidos.
    protected $fillable = [
        'codigo',
        'entidad_id',
        'nombre',
        'descripcion',
        'activo',
    ];

    // Relacion con la entidad.
    public function entidad()
    {
        return $this->belongsTo(Entidad::class);
    }

    // Relacion con los proyectos.
    public function proyectos()
    {
        return $this->hasMany(Proyecto::class);
    }
}
