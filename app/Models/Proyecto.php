<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    use HasFactory;

    // Campos permitidos.
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'programa_id',
        'activo',
    ];

    // Relacion con el programa.
    public function programa()
    {
        return $this->belongsTo(Programa::class);
    }

    // Entidad obtenida desde el programa.
    public function getEntidadAttribute()
    {
        return $this->programa?->entidad;
    }

    // Relacion con los avances.
    public function avances()
    {
        return $this->hasMany(ProyectoAvance::class);
    }

    // Ultimo avance registrado.
    public function ultimoAvance()
    {
        return $this->hasOne(ProyectoAvance::class)->latestOfMany('fecha');
    }

    // Calculo del progreso del proyecto.
    public function getProgresoAttribute()
    {
        $valor = (float) ($this->ultimoAvance?->porcentaje_avance ?? 0);

        return max(0, min(100, $valor));
    }
}
