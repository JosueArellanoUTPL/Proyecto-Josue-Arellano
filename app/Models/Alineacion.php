<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alineacion extends Model
{
    use HasFactory;

    protected $table = 'alineaciones';

    protected $fillable = [
        'meta_id',
        'indicador_id',
        'ods_id',
        'pdn_id',
        'objetivo_estrategico_id',
        'activo'
    ];

    public function meta()
    {
        return $this->belongsTo(Meta::class);
    }

    public function indicador()
    {
        return $this->belongsTo(Indicador::class);
    }

    public function ods()
    {
        return $this->belongsTo(Ods::class);
    }

    public function pdn()
    {
        return $this->belongsTo(Pdn::class);
    }

    public function objetivoEstrategico()
    {
        return $this->belongsTo(ObjetivoEstrategico::class, 'objetivo_estrategico_id');
    }
}
