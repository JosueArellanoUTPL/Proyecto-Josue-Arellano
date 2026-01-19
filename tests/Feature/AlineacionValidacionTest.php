<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Entidad;
use App\Models\Pdn;
use App\Models\Plan;
use App\Models\Meta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AlineacionValidacionTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_permita_crear_alineacion_sin_instrumentos()
    {
        // Admin
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Datos mínimos: entidad -> pdn -> plan -> meta
        $entidad = Entidad::create([
            'nombre' => 'Entidad Test',
            'descripcion' => 'Entidad para pruebas',
            'activo' => true,
        ]);

        $pdn = Pdn::create([
            'codigo' => 'PDN-TEST',
            'nombre' => 'PDN Test',
            'descripcion' => 'PDN para pruebas',
            'activo' => true,
        ]);

        $plan = Plan::create([
            'codigo' => 'PLAN-TEST',
            'nombre' => 'Plan Test',
            'descripcion' => 'Plan para pruebas',
            'anio_inicio' => 2025,
            'anio_fin' => 2027,
            'pdn_id' => $pdn->id,
            'entidad_id' => $entidad->id,
            'activo' => true,
        ]);

        $meta = Meta::create([
            'codigo' => 'META-TEST',
            'nombre' => 'Meta Test',
            'descripcion' => 'Meta para pruebas',
            'plan_id' => $plan->id,
            'valor_objetivo' => 80,
            'unidad' => '%',
            'activo' => true,
        ]);

        // Intentar crear alineación SIN ods/pdn/objetivo
        $response = $this->actingAs($admin)->post('/alineaciones', [
            'meta_id' => $meta->id,
            'indicador_id' => null,
            'ods_id' => null,
            'pdn_id' => null,
            'objetivo_estrategico_id' => null,
            'activo' => 1,
        ]);

        // Debe volver al form con errores
        $response->assertSessionHasErrors();

        // Y NO debe existir registro en BD
        $this->assertDatabaseCount('alineaciones', 0);
    }
}
