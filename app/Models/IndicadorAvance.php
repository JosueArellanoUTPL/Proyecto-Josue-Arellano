<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Modelos relacionados
use App\Models\Indicador;
use App\Models\User;

/**
 * Modelo IndicadorAvance
 *
 * Representa un registro de avance para un indicador:
 * - valor reportado
 * - fecha de registro
 * - comentario
 * - evidencia (ruta guardada en storage)
 */
class IndicadorAvance extends Model
{
    use HasFactory;

    /**
     * Tabla real usada para almacenar avances de indicadores.
     */
    protected $table = 'indicador_avances';

    /**
     * Campos permitidos para asignación masiva (store del formulario).
     */
    protected $fillable = [
        'indicador_id',
        'user_id',
        'fecha',
        'valor_reportado',
        'comentario',
        'evidencia_path',
    ];

    /**
     * Conversión automática de tipos:
     * - fecha se trata como date (Carbon)
     * - valor_reportado se trata como decimal (útil para cálculos)
     */
    protected $casts = [
        'fecha' => 'date',
        'valor_reportado' => 'decimal:2',
    ];

    /**
     * Relación: el avance pertenece a un indicador.
     */
    public function indicador()
    {
        return $this->belongsTo(Indicador::class);
    }

    /**
     * Relación: el avance pertenece a un usuario (quien registró el avance).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
