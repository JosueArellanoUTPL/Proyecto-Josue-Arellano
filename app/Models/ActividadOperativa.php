<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActividadOperativa extends Model
{
    use HasFactory;

    // Laravel no pluraliza correctamente este nombre compuesto en español.
    protected $table = 'actividades_operativas';

    public const ESTADOS = [
        'borrador' => 'Borrador',
        'en_revision' => 'En revisión',
        'aprobada' => 'Aprobada',
        'observada' => 'Observada',
        'rechazada' => 'Rechazada',
        'en_ejecucion' => 'En ejecución',
        'reprogramada' => 'Reprogramada',
        'finalizada' => 'Finalizada',
        'cerrada' => 'Cerrada',
    ];

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'plan_id',
        'proyecto_id',
        'objetivo_estrategico_id',
        'indicador_id',
        'responsable',
        'anio',
        'fecha_inicio',
        'fecha_fin',
        'meta_operativa',
        'meta_anual',
        'unidad_medida',
        'avance',
        'presupuesto',
        'prioridad',
        'estado',
        'comentario_revision',
        'evidencia',
        'revisado_por',
        'revisado_en',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'presupuesto' => 'decimal:2',
            'activo' => 'boolean',
            'revisado_en' => 'datetime',
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
        ];
    }

    // Cada actividad operativa pertenece a un plan institucional.
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function revisor()
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function objetivoEstrategico()
    {
        return $this->belongsTo(ObjetivoEstrategico::class);
    }

    public function indicador()
    {
        return $this->belongsTo(Indicador::class);
    }

    public function puedeEditar(): bool
    {
        return in_array($this->estado, ['borrador', 'observada'], true);
    }

    public function transicionesPermitidas(): array
    {
        return match ($this->estado) {
            'aprobada' => ['en_ejecucion'],
            'en_ejecucion' => ['reprogramada', 'finalizada'],
            'reprogramada' => ['en_ejecucion', 'finalizada'],
            'finalizada' => ['cerrada'],
            default => [],
        };
    }
}
