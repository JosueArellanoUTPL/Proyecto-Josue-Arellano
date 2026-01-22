<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProyectoAvance extends Model
{
    protected $fillable = [
        'proyecto_id',
        'user_id',
        'fecha',
        'porcentaje_avance',
        'comentario'
    ];

    protected $casts = ['fecha' => 'date'];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function evidencias()
    {
        return $this->hasMany(ProyectoAvanceEvidencia::class);
    }
}
