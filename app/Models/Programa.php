<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Proyecto;
use App\Models\Entidad;

class Programa extends Model
{
    use HasFactory;

    // Tabla real de programas.
    protected $table = 'programas';

    // Campos que se guardan desde el CRUD de programas.
    protected $fillable = [
        'codigo',
        'entidad_id',
        'nombre',
        'descripcion',
        'activo',
    ];

    public function entidad()
    {
        // Un programa pertenece a una entidad.
        return $this->belongsTo(Entidad::class);
    }

    public function proyectos()
    {
        // Un programa puede tener varios proyectos.
        return $this->hasMany(Proyecto::class);
    }
}
