<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    use HasFactory;

    // Campos permitidos.
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'plan_id',
        'activo',
    ];

    // Relacion con el plan.
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    // Relacion con los indicadores.
    public function indicadores()
    {
        return $this->hasMany(Indicador::class);
    }

    // Relacion con las alineaciones.
    public function alineaciones()
    {
        return $this->hasMany(Alineacion::class);
    }

    // Calculo del progreso de la meta.
    public function getProgresoAttribute(): float
    {
        if ($this->indicadores->count() === 0) {
            return 0;
        }

        return round(
            $this->indicadores->avg(fn ($indicador) => $indicador->progreso),
            2
        );
    }

    // Calculo del estado completado.
    public function getCompletadaAttribute(): bool
    {
        if ($this->indicadores->count() === 0) {
            return false;
        }

        return $this->indicadores->every(fn ($indicador) => $indicador->completado);
    }

    // Texto del estado de seguimiento.
    public function getEstadoSeguimientoAttribute(): string
    {
        if ($this->indicadores->isEmpty()) {
            return 'Pendiente de indicadores';
        }

        return $this->completada ? 'Completada' : 'En progreso';
    }
}
