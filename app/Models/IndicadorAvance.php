<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndicadorAvance extends Model
{
    use HasFactory;

    protected $table = 'indicador_avances';

    protected $fillable = [
        'indicador_id',
        'user_id',
        'fecha',
        'valor_reportado',
        'comentario',
        'evidencia_path',
    ];

    public function indicador()
    {
        return $this->belongsTo(Indicador::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
