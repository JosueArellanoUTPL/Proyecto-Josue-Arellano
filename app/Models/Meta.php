<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Plan;
use App\Models\Indicador;
use App\Models\Alineacion;
use App\Models\Proyecto;

class Meta extends Model
{
    use HasFactory;

    // Campos que se guardan cuando creo o edito una meta.
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'plan_id',
        'activo'
    ];

    /* =========================
     | Relaciones
     ========================= */

    public function plan()
    {
        // Una meta pertenece a un plan.
        return $this->belongsTo(Plan::class);
    }

    public function indicadores()
    {
        // Una meta puede tener varios indicadores.
        return $this->hasMany(Indicador::class);
    }

    public function alineaciones()
    {
        // Una meta puede estar conectada con ODS, PND y objetivos estratégicos.
        return $this->hasMany(Alineacion::class);
    }

    public function proyectos()
    {
        // Una meta puede cumplirse mediante varios proyectos.
        return $this->hasMany(Proyecto::class);
    }

    /* =========================
     | Calculos para seguimiento
     ========================= */

    public function getProgresoAttribute(): float
    {
        // El progreso de la meta sale del promedio de sus indicadores.
        if ($this->indicadores->count() === 0) {
            return 0;
        }

        return round(
            $this->indicadores->avg(fn ($indicador) => $indicador->progreso),
            2
        );
    }

    public function getCompletadaAttribute(): bool
    {
        // La meta se marca completa si todos sus indicadores llegaron al 100%.
        if ($this->indicadores->count() === 0) {
            return false;
        }

        return $this->indicadores->every(fn ($indicador) => $indicador->completado);
    }

    public function getEstadoSeguimientoAttribute(): string
    {
        // Sin indicadores todavia no existe una forma de medir la meta.
        if ($this->indicadores->isEmpty()) {
            return 'Pendiente de indicadores';
        }

        return $this->completada ? 'Completada' : 'En progreso';
    }
}
