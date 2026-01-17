<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pdn;
use App\Models\Plan;
use App\Models\Meta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class IndicadorCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_puede_crear_indicador_asociado_a_meta()
    {
        // 1) Admin
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // 2) PDN
        $pdn = Pdn::create([
            'codigo' => 'PDN-TEST',
            'nombre' => 'PDN de Prueba',
            'descripcion' => 'PDN para pruebas',
            'activo' => true,
        ]);

        // 3) Plan
        $plan = Plan::create([
            'codigo' => 'PLAN-TEST',
            'nombre' => 'Plan de Prueba',
            'descripcion' => 'Plan para pruebas',
            'anio_inicio' => 2025,
            'anio_fin' => 2027,
            'pdn_id' => $pdn->id,
            'activo' => true,
        ]);

        // 4) Meta
        $meta = Meta::create([
            'codigo' => 'META-TEST',
            'nombre' => 'Meta de Prueba',
            'descripcion' => 'Meta para pruebas',
            'plan_id' => $plan->id,
            'valor_objetivo' => 70,
            'unidad' => '%',
            'activo' => true,
        ]);

        // 5) POST a indicadores.store
        $response = $this->actingAs($admin)->post('/indicadores', [
            'codigo' => 'IND-TEST',
            'nombre' => 'Indicador de Prueba',
            'descripcion' => 'Indicador creado desde prueba',
            'meta_id' => $meta->id,
            'linea_base' => 30,
            'valor_meta' => 70,
            'unidad' => '%',
            'activo' => 1,
        ]);

        // 6) Redirección
        $response->assertRedirect('/indicadores');

        // 7) Verificar en BD (tabla "indicadores")
        $this->assertDatabaseHas('indicadores', [
            'codigo' => 'IND-TEST',
            'meta_id' => $meta->id,
        ]);
    }
}
