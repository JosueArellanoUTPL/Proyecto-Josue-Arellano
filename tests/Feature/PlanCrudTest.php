<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pdn;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class PlanCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_puede_crear_plan()
    {
        // 1) Crear admin
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // 2) Crear PDN (porque Plan requiere pdn_id)
        $pdn = Pdn::create([
            'codigo' => 'PDN-TEST',
            'nombre' => 'PDN de Prueba',
            'descripcion' => 'PDN creado para pruebas',
            'activo' => true,
        ]);

        // 3) Enviar POST al store de plans
        $response = $this->actingAs($admin)->post('/plans', [
            'codigo' => 'PLAN-TEST',
            'nombre' => 'Plan de Prueba',
            'descripcion' => 'Plan creado desde prueba',
            'anio_inicio' => 2025,
            'anio_fin' => 2027,
            'pdn_id' => $pdn->id,
            'activo' => 1,
        ]);

        // 4) Esperamos redirección (normalmente a index)
        $response->assertRedirect('/plans');

        // 5) Verificar que se guardó en BD
        $this->assertDatabaseHas('plans', [
            'codigo' => 'PLAN-TEST',
            'pdn_id' => $pdn->id,
        ]);
    }
}
