<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Importamos modelos relacionados (buena práctica)
use App\Models\Pdn;
use App\Models\Entidad;
use App\Models\Meta;

/**
 * Modelo Plan
 *
 * Representa un Plan Estratégico institucional.
 * Un plan pertenece a una Entidad y a un PDN,
 * y contiene múltiples Metas.
 */
class Plan extends Model
{
    use HasFactory;

    /**
     * Campos permitidos para asignación masiva.
     * Se usan en el CRUD de planes.
     */
    protected $fillable = [
        'codigo',        // Código del plan (ej: PLAN-2025)
        'nombre',
        'descripcion',
        'anio_inicio',
        'anio_fin',
        'pdn_id',        // Relación con Plan de Desarrollo Nacional
        'entidad_id',    // Entidad a la que pertenece
        'activo'
    ];

    /**
     * Relación: un plan pertenece a un PDN.
     * Usado para alineación estratégica y visualización.
     */
    public function pdn()
    {
        return $this->belongsTo(Pdn::class);
    }

    /**
     * Relación: un plan pertenece a una entidad.
     * Clave para el seguimiento organizacional.
     */
    public function entidad()
    {
        return $this->belongsTo(Entidad::class);
    }

    /**
     * Relación: un plan tiene muchas metas.
     * Es la base del seguimiento (metas → indicadores → avances).
     */
    public function metas()
    {
        return $this->hasMany(Meta::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Atributos calculados (opcional pero recomendado)
    |--------------------------------------------------------------------------
    | Estos métodos ayudan a NO repetir lógica en controladores
    | y hacen el modelo más "inteligente".
    */

    /**
     * Progreso promedio del plan.
     * Se calcula a partir del progreso de sus metas.
     */
    public function getProgresoAttribute()
    {
        if ($this->metas->count() === 0) {
            return 0;
        }

        return round(
            $this->metas->avg('progreso'),
            2
        );
    }

    /**
     * Indica si el plan está completamente cumplido.
     * Se considera cumplido si el progreso llega a 100%.
     */
    public function getCompletadoAttribute()
    {
        return $this->progreso >= 100;
    }
}
