<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entidad extends Model
{
    use HasFactory;

    // Nombre de la tabla.
    protected $table = 'entidades';

    // Campos permitidos.
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'activo',
    ];

    // Relacion con los planes.
    public function plans()
    {
        return $this->hasMany(Plan::class);
    }

    // Relacion con los programas.
    public function programas()
    {
        return $this->hasMany(Programa::class);
    }

    // Relacion con proyectos mediante programas.
    public function proyectos()
    {
        // Proyectos derivados de sus programas.
        return $this->hasManyThrough(
            Proyecto::class,
            Programa::class,
            'entidad_id',
            'programa_id'
        );
    }
}
