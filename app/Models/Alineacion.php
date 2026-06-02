<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Meta;
use App\Models\Indicador;
use App\Models\Ods;
use App\Models\Pdn;
use App\Models\ObjetivoEstrategico;

class Alineacion extends Model
{
    use HasFactory;

    // Tabla real de alineaciones estrategicas.
    protected $table = 'alineaciones';

    // Campos que se guardan al crear una alineacion.
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
        // La alineacion siempre pertenece a una meta.
        return $this->belongsTo(Meta::class);
    }

    public function indicador()
    {
        // El indicador es opcional para hacer la alineacion mas especifica.
        return $this->belongsTo(Indicador::class);
    }

    public function ods()
    {
        // Relacion con ODS.
        return $this->belongsTo(Ods::class);
    }

    public function pdn()
    {
        // Relacion con PDN.
        return $this->belongsTo(Pdn::class);
    }

    public function objetivoEstrategico()
    {
        // Relacion con objetivo estrategico.
        return $this->belongsTo(ObjetivoEstrategico::class, 'objetivo_estrategico_id');
    }

    public function getResumenInstrumentosAttribute(): string
    {
        // Texto corto para mostrar que instrumentos tiene la alineacion.
        $parts = [];

        if ($this->ods) {
            $parts[] = 'ODS';
        }

        if ($this->pdn) {
            $parts[] = 'PDN';
        }

        if ($this->objetivoEstrategico) {
            $parts[] = 'OE';
        }

        return $parts ? implode(' + ', $parts) : 'Sin instrumentos';
    }
}
