<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    // Nombre de la tabla.
    protected $table = 'planes';

    // Campos permitidos.
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'anio_inicio',
        'anio_fin',
        'pdn_id',
        'entidad_id',
        'activo',
    ];

    // Relacion con el PDN.
    public function pdn()
    {
        return $this->belongsTo(Pdn::class);
    }

    // Relacion con la entidad.
    public function entidad()
    {
        return $this->belongsTo(Entidad::class);
    }

    // Relacion con las metas.
    public function metas()
    {
        return $this->hasMany(Meta::class);
    }
}
