<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Proyecto;
use App\Models\User;
use App\Models\ProyectoAvanceEvidencia;

class ProyectoAvance extends Model
{
    use HasFactory;

    // Campos del formulario de avance de proyecto.
    protected $fillable = [
        'proyecto_id',
        'user_id',
        'fecha',
        'porcentaje_avance',
        'comentario'
    ];

    // Fecha como Carbon y porcentaje como decimal.
    protected $casts = [
        'fecha' => 'date',
        'porcentaje_avance' => 'decimal:2',
    ];

    public function proyecto()
    {
        // El avance pertenece a un proyecto.
        return $this->belongsTo(Proyecto::class);
    }

    public function user()
    {
        // Usuario que registro el avance.
        return $this->belongsTo(User::class);
    }

    public function evidencias()
    {
        // Un avance puede tener varios archivos de evidencia.
        return $this->hasMany(ProyectoAvanceEvidencia::class, 'proyecto_avance_id');
    }
}
