<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'anio_inicio',
        'anio_fin',
        'pdn_id',
        'activo'
    ];

    public function pdn()
    {
        return $this->belongsTo(Pdn::class);
    }
    public function metas()
{
    return $this->hasMany(Meta::class);
}
}
