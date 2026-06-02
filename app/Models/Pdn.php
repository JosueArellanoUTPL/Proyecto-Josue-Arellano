<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pdn extends Model
{
    use HasFactory;

    // Campos guardados desde el CRUD de PND/PDN.
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'activo'
    ];
}
