<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indicador extends Model
{
    use HasFactory;

    protected $table = 'indicadores';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'meta_id',
        'linea_base',
        'valor_meta',
        'unidad',
        'activo'
    ];

    /* =========================
     | Relaciones
     ========================= */

    public function meta()
    {
        return $this->belongsTo(Meta::class);
    }

    public function avances()
    {
        return $this->hasMany(IndicadorAvance::class);
    }

    public function ultimoAvance()
    {
        return $this->hasOne(IndicadorAvance::class)->latestOfMany();
    }

    /* =========================
     | Lógica de negocio
     ========================= */

    // % de avance del indicador
    public function getProgresoAttribute(): float
    {
        $lineaBase = $this->linea_base;
        $valorMeta = $this->valor_meta;
        $valorActual = optional($this->ultimoAvance)->valor_reportado;

        if ($lineaBase === null || $valorMeta === null || $valorActual === null) {
            return 0;
        }

        if ($valorMeta == $lineaBase) {
            return 0;
        }

        $porcentaje = (($valorActual - $lineaBase) / ($valorMeta - $lineaBase)) * 100;

        return round(max(0, min(100, $porcentaje)), 2);
    }

    // Indicador completado o no
    public function getCompletadoAttribute(): bool
    {
        return $this->progreso >= 100;
    }
}
