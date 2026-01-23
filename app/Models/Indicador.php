<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Modelos relacionados
use App\Models\Meta;
use App\Models\IndicadorAvance;

/**
 * Modelo Indicador
 *
 * Representa un indicador asociado a una Meta.
 * El progreso se calcula con el último avance reportado,
 * considerando línea base y valor meta.
 */
class Indicador extends Model
{
    use HasFactory;

    /**
     * Se especifica la tabla real.
     */
    protected $table = 'indicadores';

    /**
     * Campos permitidos para create() / update() desde formularios (CRUD).
     */
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

    /**
     * Relación: un indicador pertenece a una meta.
     */
    public function meta()
    {
        return $this->belongsTo(Meta::class);
    }

    /**
     * Relación: un indicador tiene muchos avances (historial).
     */
    public function avances()
    {
        return $this->hasMany(IndicadorAvance::class);
    }

    /**
     * Relación: último avance registrado.
     *
     * Si en IndicadorAvance existe el campo "fecha", se usa para determinar el último avance.
     * Si no existe, Laravel tomará el último por id automáticamente.
     */
    public function ultimoAvance()
    {
        // Si tu tabla IndicadorAvance tiene columna "fecha", deja esta línea:
        return $this->hasOne(IndicadorAvance::class)->latestOfMany('fecha');

        // Si NO tienes columna "fecha" en IndicadorAvance, usar:
        // return $this->hasOne(IndicadorAvance::class)->latestOfMany();
    }

    /* =========================
     | Lógica de negocio
     ========================= */

    /**
     * Progreso del indicador (%)
     *
     * Fórmula con línea base:
     * progreso = ((valorActual - lineaBase) / (valorMeta - lineaBase)) * 100
     *
     * Si no existe línea base, valor meta o último avance, el progreso se considera 0.
     */
    public function getProgresoAttribute(): float
    {
        $lineaBase   = $this->linea_base;
        $valorMeta   = $this->valor_meta;
        $valorActual = $this->ultimoAvance?->valor_reportado;

        // Validación básica de datos
        if ($lineaBase === null || $valorMeta === null || $valorActual === null) {
            return 0;
        }

        // Evita división por cero cuando el valor meta es igual a la línea base
        if ((float)$valorMeta === (float)$lineaBase) {
            return 0;
        }

        $porcentaje = (($valorActual - $lineaBase) / ($valorMeta - $lineaBase)) * 100;

        // Se limita el valor entre 0 y 100 para evitar porcentajes negativos o mayores a 100
        return round(max(0, min(100, $porcentaje)), 2);
    }

    /**
     * Estado del indicador:
     * Se considera completado cuando su progreso llega al 100%.
     */
    public function getCompletadoAttribute(): bool
    {
        return $this->progreso >= 100;
    }
}
