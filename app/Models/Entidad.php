<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Plan;
use App\Models\Programa;
use App\Models\Proyecto;

class Entidad extends Model
{
    use HasFactory;

    // Nombre real de la tabla. Laravel no adivina bien "entidades".
    protected $table = 'entidades';

    // Campos que se pueden guardar desde el CRUD de entidades.
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'activo',
    ];

    public function plans()
    {
        // Una entidad puede tener varios planes.
        return $this->hasMany(Plan::class);
    }

    public function programas()
    {
        // Una entidad puede tener varios programas.
        return $this->hasMany(Programa::class);
    }

    public function proyectos()
    {
        // Una entidad puede tener varios proyectos.
        return $this->hasMany(Proyecto::class);
    }
}
