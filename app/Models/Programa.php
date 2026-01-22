<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Proyecto;
use App\Models\Entidad;

class Programa extends Model
{
    use HasFactory;

    protected $table = 'programas';

    protected $fillable = [
        'entidad_id',
        'nombre',
        'descripcion',
        'activo',
    ];

    /**
     * Relación: un programa pertenece a una entidad
     */
    public function entidad()
    {
        return $this->belongsTo(Entidad::class);
    }

    /**
     * Relación: un programa tiene muchos proyectos
     */
    public function proyectos()
    {
        return $this->hasMany(Proyecto::class);
    }
}
