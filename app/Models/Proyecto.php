<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Programa;
use App\Models\Meta;
use App\Models\ProyectoAvance;

class Proyecto extends Model
{
    use HasFactory;

    // Campos que se guardan al crear o editar proyectos.
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'programa_id',
        'meta_id',
        'activo'
    ];

    public function programa()
    {
        // El proyecto pertenece a un programa.
        return $this->belongsTo(Programa::class);
    }

    public function getEntidadAttribute()
    {
        // No se guarda otra vez: se toma desde el programa del proyecto.
        return $this->programa?->entidad;
    }

    public function meta()
    {
        // La meta explica que resultado del plan apoya este proyecto.
        return $this->belongsTo(Meta::class);
    }

    public function avances()
    {
        // Historial de avances registrados para este proyecto.
        return $this->hasMany(ProyectoAvance::class);
    }

    public function ultimoAvance()
    {
        // Ultimo avance por fecha: sirve para mostrar el avance actual.
        return $this->hasOne(ProyectoAvance::class)->latestOfMany('fecha');
    }

    public function getProgresoAttribute()
    {
        // El progreso del proyecto se toma del ultimo avance registrado.
        $valor = (float) ($this->ultimoAvance?->porcentaje_avance ?? 0);

        // Seguridad: siempre entre 0 y 100.
        return max(0, min(100, $valor));
    }
}
