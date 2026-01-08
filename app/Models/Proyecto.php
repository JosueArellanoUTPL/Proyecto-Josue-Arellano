<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'entidad_id',
        'programa_id',
        'activo'
    ];

    public function entidad()
    {
        return $this->belongsTo(Entidad::class);
    }

    public function programa()
    {
        return $this->belongsTo(Programa::class);
    }
}
