<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProyectoAvance extends Model
{
    use HasFactory;

    // Campos permitidos.
    protected $fillable = [
        'proyecto_id',
        'user_id',
        'fecha',
        'porcentaje_avance',
        'comentario',
    ];

    // Conversion de tipos.
    protected $casts = [
        'fecha' => 'date',
        'porcentaje_avance' => 'decimal:2',
    ];

    // Relacion con el proyecto.
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    // Relacion con el usuario.
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relacion con las evidencias.
    public function evidencias()
    {
        return $this->hasMany(ProyectoAvanceEvidencia::class, 'proyecto_avance_id');
    }
}
