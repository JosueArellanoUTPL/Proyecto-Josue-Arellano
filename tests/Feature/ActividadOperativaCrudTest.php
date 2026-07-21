<?php

namespace Tests\Feature;

use App\Models\Pdn;
use App\Models\Plan;
use App\Models\Entidad;
use App\Models\Indicador;
use App\Models\Meta;
use App\Models\ObjetivoEstrategico;
use App\Models\Programa;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ActividadOperativaCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_planificacion_puede_registrar_una_actividad_operativa(): void
    {
        $usuario = User::create([
            'name' => 'Planificacion Test',
            'email' => 'poa@test.com',
            'password' => Hash::make('password123'),
            'role' => 'planificacion',
        ]);
        $pdn = Pdn::create(['codigo' => 'PDN-TEST', 'nombre' => 'PDN prueba', 'activo' => true]);
        $entidad = Entidad::create(['codigo' => 'ENT-TEST', 'nombre' => 'Entidad prueba', 'activo' => true]);
        $plan = Plan::create([
            'codigo' => 'PLAN-TEST', 'nombre' => 'Plan prueba', 'anio_inicio' => 2026,
            'anio_fin' => 2026, 'pdn_id' => $pdn->id, 'entidad_id' => $entidad->id, 'activo' => true,
        ]);
        $programa = Programa::create(['codigo' => 'PROG-TEST', 'nombre' => 'Programa prueba', 'entidad_id' => $entidad->id, 'activo' => true]);
        $proyecto = Proyecto::create(['codigo' => 'PROY-TEST', 'nombre' => 'Proyecto prueba', 'programa_id' => $programa->id, 'activo' => true]);
        $objetivo = ObjetivoEstrategico::create(['codigo' => 'OBJ-TEST', 'nombre' => 'Objetivo prueba', 'activo' => true]);
        $meta = Meta::create(['codigo' => 'META-TEST', 'nombre' => 'Meta prueba', 'plan_id' => $plan->id, 'activo' => true]);
        $indicador = Indicador::create(['codigo' => 'IND-TEST', 'nombre' => 'Indicador prueba', 'meta_id' => $meta->id, 'linea_base' => 0, 'valor_meta' => 100, 'unidad' => '%', 'activo' => true]);

        $response = $this->actingAs($usuario)->post(route('actividades-operativas.store'), [
            'codigo' => 'POA-001', 'nombre' => 'Registrar actividades operativas',
            'plan_id' => $plan->id, 'responsable' => 'Unidad de Planificacion',
            'proyecto_id' => $proyecto->id, 'objetivo_estrategico_id' => $objetivo->id,
            'indicador_id' => $indicador->id, 'anio' => 2026,
            'fecha_inicio' => '2026-01-01', 'fecha_fin' => '2026-12-31',
            'meta_operativa' => '100% de actividades registradas', 'meta_anual' => 100,
            'unidad_medida' => '%', 'avance' => 0, 'presupuesto' => 1500,
            'prioridad' => 'media', 'activo' => 1,
        ]);

        $response->assertRedirect(route('actividades-operativas.index'));
        $this->assertDatabaseHas('actividades_operativas', ['codigo' => 'POA-001', 'plan_id' => $plan->id]);
    }
}
