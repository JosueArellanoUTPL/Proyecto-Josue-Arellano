<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Entidad;
use App\Models\Programa;
use App\Models\ProyectoAvance;

class Proyecto extends Model
{
    use HasFactory;

    // Campos que se guardan al crear o editar proyectos.
    protected $fillable = [
        'nombre',
        'descripcion',
        'entidad_id',
        'programa_id',
        'activo'
    ];

    public function entidad()
    {
        // El proyecto pertenece a una entidad.
        return $this->belongsTo(Entidad::class);
    }

    public function programa()
    {
        // El proyecto pertenece a un programa.
        return $this->belongsTo(Programa::class);
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
