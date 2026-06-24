<?php

namespace Tests\Feature;

use App\Models\Entidad;
use App\Models\Pdn;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

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

        // 3) Crear la entidad responsable del plan.
        $entidad = Entidad::create([
            'codigo' => 'ENT-TEST',
            'nombre' => 'Entidad de Prueba',
            'activo' => true,
        ]);

        // Enviar datos para crear el plan.
        $response = $this->actingAs($admin)->post('/planes', [
            'codigo' => 'PLAN-TEST',
            'nombre' => 'Plan de Prueba',
            'descripcion' => 'Plan creado desde prueba',
            'anio_inicio' => 2025,
            'anio_fin' => 2027,
            'pdn_id' => $pdn->id,
            'entidad_id' => $entidad->id,
            'activo' => 1,
        ]);

        // 4) Esperamos redirección (normalmente a index)
        $response->assertRedirect('/planes');

        // 5) Verificar que se guardó en BD
        $this->assertDatabaseHas('planes', [
            'codigo' => 'PLAN-TEST',
            'pdn_id' => $pdn->id,
            'entidad_id' => $entidad->id,
        ]);
    }
}
