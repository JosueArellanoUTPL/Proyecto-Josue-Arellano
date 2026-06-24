<?php

namespace Tests\Feature;

use App\Models\Entidad;
use App\Models\Indicador;
use App\Models\Meta;
use App\Models\ObjetivoEstrategico;
use App\Models\Ods;
use App\Models\Pdn;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class NamingConsistencyTest extends TestCase
{
    use RefreshDatabase;

    // Comprobar nombres de tablas y rutas de edicion.
    public function test_nombres_normalizados_funcionan_en_los_modulos_principales(): void
    {
        $administrador = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'activo' => true,
        ]);

        $entidad = Entidad::create([
            'codigo' => 'ENT-NOM',
            'nombre' => 'Entidad de prueba',
            'activo' => true,
        ]);

        $pdn = Pdn::create([
            'codigo' => 'PND-NOM',
            'nombre' => 'PND de prueba',
            'activo' => true,
        ]);

        $plan = Plan::create([
            'codigo' => 'PLAN-NOM',
            'nombre' => 'Plan de prueba',
            'anio_inicio' => 2026,
            'anio_fin' => 2027,
            'pdn_id' => $pdn->id,
            'entidad_id' => $entidad->id,
            'activo' => true,
        ]);

        $meta = Meta::create([
            'codigo' => 'META-NOM',
            'nombre' => 'Meta de prueba',
            'plan_id' => $plan->id,
            'activo' => true,
        ]);

        $indicador = Indicador::create([
            'codigo' => 'IND-NOM',
            'nombre' => 'Indicador de prueba',
            'meta_id' => $meta->id,
            'linea_base' => 0,
            'valor_meta' => 100,
            'unidad' => '%',
            'activo' => true,
        ]);

        $ods = Ods::create([
            'codigo' => 'ODS-NOM',
            'nombre' => 'ODS de prueba',
            'activo' => true,
        ]);

        $objetivo = ObjetivoEstrategico::create([
            'codigo' => 'OE-NOM',
            'nombre' => 'Objetivo de prueba',
            'activo' => true,
        ]);

        $this->actingAs($administrador)->get(route('entidades.edit', $entidad))->assertOk();
        $this->actingAs($administrador)->get(route('planes.edit', $plan))->assertOk();
        $this->actingAs($administrador)->get(route('indicadores.edit', $indicador))->assertOk();
        $this->actingAs($administrador)->get(route('ods.edit', $ods))->assertOk();
        $this->actingAs($administrador)->get(route('objetivos-estrategicos.edit', $objetivo))->assertOk();
        $this->actingAs($administrador)->get(route('usuarios.edit', $administrador))->assertOk();

        $this->assertTrue(Schema::hasTable('planes'));
        $this->assertTrue(Schema::hasTable('objetivos_estrategicos'));
        $this->assertFalse(Schema::hasTable('plans'));
        $this->assertFalse(Schema::hasTable('objetivo_estrategicos'));
    }
}
