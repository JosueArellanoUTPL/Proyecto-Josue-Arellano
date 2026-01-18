<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    use HasFactory;

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
        return $this->belongsTo(Plan::class);
    }

    public function indicadores()
    {
        return $this->hasMany(Indicador::class);
    }

    /* =========================
     | Lógica de negocio
     ========================= */

    // % de avance de la meta (promedio de indicadores)
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

    // Meta completada si TODOS los indicadores están completos
    public function getCompletadaAttribute(): bool
    {
        if ($this->indicadores->count() === 0) {
            return false;
        }

        return $this->indicadores->every(fn ($indicador) => $indicador->completado);
    }
}

