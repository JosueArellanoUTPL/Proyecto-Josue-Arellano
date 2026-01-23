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

    /**
     * Relación: una meta puede tener varias alineaciones.
     * Esto permite consultar trazabilidad desde la meta.
     */
    public function alineaciones()
    {
        return $this->hasMany(Alineacion::class);
    }

    /* =========================
     | Lógica de negocio
     ========================= */

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

    public function getCompletadaAttribute(): bool
    {
        if ($this->indicadores->count() === 0) {
            return false;
        }

        return $this->indicadores->every(fn ($indicador) => $indicador->completado);
    }
}
