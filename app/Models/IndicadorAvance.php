<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndicadorAvance extends Model
{
    use HasFactory;

    // Nombre de la tabla.
    protected $table = 'indicador_avances';

    // Campos permitidos.
    protected $fillable = [
        'indicador_id',
        'user_id',
        'fecha',
        'valor_reportado',
        'comentario',
        'evidencia_path',
    ];

    // Conversion de tipos.
    protected $casts = [
        'fecha' => 'date',
        'valor_reportado' => 'decimal:2',
    ];

    // Relacion con el indicador.
    public function indicador()
    {
        return $this->belongsTo(Indicador::class);
    }

    // Relacion con el usuario.
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
