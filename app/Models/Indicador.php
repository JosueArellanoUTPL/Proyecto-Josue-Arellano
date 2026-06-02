<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Meta;
use App\Models\IndicadorAvance;

class Indicador extends Model
{
    use HasFactory;

    // Nombre real de la tabla en MySQL.
    protected $table = 'indicadores';

    // Campos que vienen de los formularios de indicadores.
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
        // Cada indicador pertenece a una meta.
        return $this->belongsTo(Meta::class);
    }

    public function avances()
    {
        // Historial completo de avances registrados para este indicador.
        return $this->hasMany(IndicadorAvance::class);
    }

    public function ultimoAvance()
    {
        // Toma el avance mas reciente por fecha para calcular el progreso actual.
        return $this->hasOne(IndicadorAvance::class)->latestOfMany('fecha');
    }

    /* =========================
     | Calculos para seguimiento
     ========================= */

    public function getProgresoAttribute(): float
    {
        // Formula: compara valor actual contra linea base y valor meta.
        $lineaBase = $this->linea_base;
        $valorMeta = $this->valor_meta;
        $valorActual = $this->ultimoAvance?->valor_reportado;

        // Si falta algun dato importante, el avance queda en 0.
        if ($lineaBase === null || $valorMeta === null || $valorActual === null) {
            return 0;
        }

        // Evita division por cero si la linea base y la meta son iguales.
        if ((float) $valorMeta === (float) $lineaBase) {
            return 0;
        }

        $porcentaje = (($valorActual - $lineaBase) / ($valorMeta - $lineaBase)) * 100;

        // El porcentaje se limita entre 0 y 100 para que no rompa las barras.
        return round(max(0, min(100, $porcentaje)), 2);
    }

    public function getCompletadoAttribute(): bool
    {
        // Se considera completo cuando llega al 100%.
        return $this->progreso >= 100;
    }
}
