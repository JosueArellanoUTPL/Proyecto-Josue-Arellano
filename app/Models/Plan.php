<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Pdn;
use App\Models\Entidad;
use App\Models\Meta;

class Plan extends Model
{
    use HasFactory;

    // Campos que se guardan desde el CRUD de planes.
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'anio_inicio',
        'anio_fin',
        'pdn_id',
        'entidad_id',
        'activo'
    ];

    public function pdn()
    {
        // Un plan pertenece a un PDN.
        return $this->belongsTo(Pdn::class);
    }

    public function entidad()
    {
        // Un plan pertenece a una entidad.
        return $this->belongsTo(Entidad::class);
    }

    public function metas()
    {
        // Un plan puede tener varias metas.
        return $this->hasMany(Meta::class);
    }

    public function getProgresoAttribute()
    {
        // Progreso del plan = promedio del progreso de sus metas.
        if ($this->metas->count() === 0) {
            return 0;
        }

        return round(
            $this->metas->avg('progreso'),
            2
        );
    }

    public function getCompletadoAttribute()
    {
        // El plan esta completo si su progreso llega a 100%.
        return $this->progreso >= 100;
    }
}
