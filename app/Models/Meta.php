<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'plan_id',
        'valor_objetivo',
        'unidad',
        'activo'
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
    public function indicadores()
{
    return $this->hasMany(Indicador::class);
}
}
