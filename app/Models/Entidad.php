<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// ✅ Importamos los modelos relacionados para que el IDE y Laravel resuelvan bien las clases
use App\Models\Plan;
use App\Models\Programa;
use App\Models\Proyecto;

class Entidad extends Model
{
    use HasFactory;

    /**
     * Nota:
     * Laravel por defecto asumiría "entidads" (mal).
     * Por eso forzamos el nombre real de la tabla: "entidades".
     */
    protected $table = 'entidades';

    /**
     * Campos permitidos para create() / update().
     * IMPORTANTE:
     * - En el sistema usamos "codigo" para identificar la entidad (ej: ENT-001),
     *   por eso debe estar aquí para guardarse correctamente desde el CRUD.
     */
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'activo',
    ];

    /**
     * Relación: una entidad tiene muchos planes estratégicos.
     * Usado en:
     * - CRUD de planes (vinculados a entidad)
     * - Seguimiento organizacional (Entidad → Planes → Metas)
     */
    public function plans()
    {
        return $this->hasMany(Plan::class);
    }

    /**
     * Relación: una entidad tiene muchos programas.
     * Usado en:
     * - Organización (lista de programas por entidad)
     * - Ficha de entidad (detalle)
     */
    public function programas()
    {
        return $this->hasMany(Programa::class);
    }

    /**
     * Relación: una entidad tiene muchos proyectos.
     * Usado en:
     * - Organización (lista de proyectos por entidad)
     * - Seguimiento de proyecto (Proyecto pertenece a entidad)
     */
    public function proyectos()
    {
        return $this->hasMany(Proyecto::class);
    }
}
