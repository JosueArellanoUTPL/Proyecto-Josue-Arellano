<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObjetivoEstrategico extends Model
{
    use HasFactory;

    // Nombre de la tabla.
    protected $table = 'objetivos_estrategicos';

    // Campos permitidos.
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'activo',
    ];
}
