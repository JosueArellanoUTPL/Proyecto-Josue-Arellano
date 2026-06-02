<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Indicador;
use App\Models\User;

class IndicadorAvance extends Model
{
    use HasFactory;

    // Tabla donde se guardan los avances de indicadores.
    protected $table = 'indicador_avances';

    // Campos que se guardan al registrar un avance.
    protected $fillable = [
        'indicador_id',
        'user_id',
        'fecha',
        'valor_reportado',
        'comentario',
        'evidencia_path',
    ];

    // Convierte fecha y valor para trabajar mejor en calculos y vistas.
    protected $casts = [
        'fecha' => 'date',
        'valor_reportado' => 'decimal:2',
    ];

    public function indicador()
    {
        // El avance pertenece a un indicador.
        return $this->belongsTo(Indicador::class);
    }

    public function user()
    {
        // Usuario que registro este avance.
        return $this->belongsTo(User::class);
    }
}
