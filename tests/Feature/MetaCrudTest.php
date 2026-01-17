<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pdn;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class MetaCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_puede_crear_meta_asociada_a_plan()
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
            'descripcion' => 'PDN creado para pruebas',
            'activo' => true,
        ]);

        // 3) Plan (porque Meta requiere plan_id)
        $plan = Plan::create([
            'codigo' => 'PLAN-TEST',
            'nombre' => 'Plan de Prueba',
            'descripcion' => 'Plan para pruebas',
            'anio_inicio' => 2025,
            'anio_fin' => 2027,
            'pdn_id' => $pdn->id,
            'activo' => true,
        ]);

        // 4) POST a metas.store
        $response = $this->actingAs($admin)->post('/metas', [
            'codigo' => 'META-TEST',
            'nombre' => 'Meta de Prueba',
            'descripcion' => 'Meta creada desde prueba',
            'plan_id' => $plan->id,
            'valor_objetivo' => 70,
            'unidad' => '%',
            'activo' => 1,
        ]);

        // 5) Redirección a index
        $response->assertRedirect('/metas');

        // 6) Verificar BD
        $this->assertDatabaseHas('metas', [
            'codigo' => 'META-TEST',
            'plan_id' => $plan->id,
        ]);
    }
}
