<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entidad extends Model
{
    use HasFactory;

    // IMPORTANTE: tu tabla real se llama "entidades"
    protected $table = 'entidades';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
    ];

    public function plans()
    {
        return $this->hasMany(Plan::class);
    }

    public function programas()
    {
        return $this->hasMany(Programa::class);
    }

    public function proyectos()
    {
        return $this->hasMany(Proyecto::class);
    }
}
