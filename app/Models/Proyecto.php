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

    protected $fillable = [
        'nombre',
        'descripcion',
        'entidad_id',
        'programa_id',
        'activo'
    ];

    /**
     * Relaciones base
     */
    public function entidad()
    {
        return $this->belongsTo(Entidad::class);
    }

    public function programa()
    {
        return $this->belongsTo(Programa::class);
    }

    /**
     * Avances del proyecto
     */
    public function avances()
    {
        return $this->hasMany(ProyectoAvance::class);
    }

    /**
     * Último avance registrado (por fecha)
     */
    public function ultimoAvance()
    {
        return $this->hasOne(ProyectoAvance::class)->latestOfMany('fecha');
    }

    /**
     * Progreso calculado automáticamente
     * Se basa en el último avance registrado
     */
    public function getProgresoAttribute()
    {
        $valor = (float) ($this->ultimoAvance?->porcentaje_avance ?? 0);

        // Seguridad: siempre entre 0 y 100
        return max(0, min(100, $valor));
    }
}
