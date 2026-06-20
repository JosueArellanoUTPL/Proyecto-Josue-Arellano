<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alineacion extends Model
{
    use HasFactory;

    // Nombre de la tabla.
    protected $table = 'alineaciones';

    // Campos permitidos.
    protected $fillable = [
        'meta_id',
        'ods_id',
        'objetivo_estrategico_id',
        'activo',
    ];

    // Relacion con la meta.
    public function meta()
    {
        return $this->belongsTo(Meta::class);
    }

    // Relacion con el ODS.
    public function ods()
    {
        return $this->belongsTo(Ods::class);
    }

    // PDN obtenido desde el plan de la meta.
    public function getPdnAttribute()
    {
        return $this->meta?->plan?->pdn;
    }

    // Relacion con el objetivo estrategico.
    public function objetivoEstrategico()
    {
        return $this->belongsTo(ObjetivoEstrategico::class, 'objetivo_estrategico_id');
    }
}
