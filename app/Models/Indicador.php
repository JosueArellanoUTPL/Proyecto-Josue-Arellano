<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indicador extends Model
{
    use HasFactory;

    // Nombre de la tabla.
    protected $table = 'indicadores';

    // Campos permitidos.
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'meta_id',
        'linea_base',
        'valor_meta',
        'unidad',
        'activo',
    ];

    // Relacion con la meta.
    public function meta()
    {
        return $this->belongsTo(Meta::class);
    }

    // Relacion con los avances.
    public function avances()
    {
        return $this->hasMany(IndicadorAvance::class);
    }

    // Ultimo avance registrado.
    public function ultimoAvance()
    {
        return $this->hasOne(IndicadorAvance::class)->latestOfMany('fecha');
    }

    // Calculo del progreso del indicador.
    public function getProgresoAttribute(): float
    {
        $lineaBase = $this->linea_base;
        $valorMeta = $this->valor_meta;
        $valorActual = $this->ultimoAvance?->valor_reportado;

        if ($lineaBase === null || $valorMeta === null || $valorActual === null) {
            return 0;
        }

        if ((float) $valorMeta === (float) $lineaBase) {
            return (float) $valorActual === (float) $valorMeta ? 100 : 0;
        }

        $porcentaje = (($valorActual - $lineaBase) / ($valorMeta - $lineaBase)) * 100;

        return round(max(0, min(100, $porcentaje)), 2);
    }

    // Calculo del estado completado.
    public function getCompletadoAttribute(): bool
    {
        return $this->progreso >= 100;
    }
}
