<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Modelos relacionados
use App\Models\Meta;
use App\Models\Indicador;
use App\Models\Ods;
use App\Models\Pdn;
use App\Models\ObjetivoEstrategico;

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

    /**
     * Relación principal: la alineación siempre pertenece a una meta.
     */
    public function meta()
    {
        return $this->belongsTo(Meta::class);
    }

    /**
     * Relación opcional: la alineación puede ser más específica a nivel indicador.
     */
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

    /**
     * Resumen textual para mostrar en listados sin armar lógica en la vista.
     */
    public function getResumenInstrumentosAttribute(): string
    {
        $parts = [];

        if ($this->ods) $parts[] = 'ODS';
        if ($this->pdn) $parts[] = 'PDN';
        if ($this->objetivoEstrategico) $parts[] = 'OE';

        return $parts ? implode(' + ', $parts) : 'Sin instrumentos';
    }
}
