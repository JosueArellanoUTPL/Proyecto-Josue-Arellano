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

    public function meta()
    {
        return $this->belongsTo(Meta::class);
    }
}
