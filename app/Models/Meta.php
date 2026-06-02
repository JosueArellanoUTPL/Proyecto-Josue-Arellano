<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Plan;
use App\Models\Indicador;
use App\Models\Alineacion;

class Meta extends Model
{
    use HasFactory;

    // Campos que se guardan cuando creo o edito una meta.
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'plan_id',
        'valor_objetivo',
        'unidad',
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
        // Una meta puede estar conectada con ODS, PDN y objetivos estrategicos.
        return $this->hasMany(Alineacion::class);
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
}
