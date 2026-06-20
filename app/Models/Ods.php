<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ods extends Model
{
    use HasFactory;

    // Campos permitidos.
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'activo',
    ];
}
