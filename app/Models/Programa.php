<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Proyecto;

class Programa extends Model
{
    use HasFactory;

    protected $table = 'programas';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
    ];

    // Relación: un programa tiene muchos proyectos
    public function proyectos()
    {
        return $this->hasMany(Proyecto::class);
    }
}
