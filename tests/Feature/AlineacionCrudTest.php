<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pdn;
use App\Models\Plan;
use App\Models\Meta;
use App\Models\Indicador;
use App\Models\Ods;
use App\Models\ObjetivoEstrategico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AlineacionCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_puede_crear_alineacion()
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

        // 5) Indicador (opcional)
        $indicador = Indicador::create([
            'codigo' => 'IND-TEST',
            'nombre' => 'Indicador de Prueba',
            'descripcion' => 'Indicador para pruebas',
            'meta_id' => $meta->id,
            'linea_base' => 30,
            'valor_meta' => 70,
            'unidad' => '%',
            'activo' => true,
        ]);

        // 6) ODS
        $ods = Ods::create([
            'codigo' => 'ODS 16',
            'nombre' => 'Instituciones sólidas',
            'descripcion' => 'ODS para pruebas',
            'activo' => true,
        ]);

        // 7) Objetivo Estratégico
        $obj = ObjetivoEstrategico::create([
            'nombre' => 'Fortalecer la gestión institucional',
            'descripcion' => 'Objetivo para pruebas',
            'activo' => true,
        ]);

        // 8) POST a alineaciones.store
        $response = $this->actingAs($admin)->post('/alineaciones', [
            'meta_id' => $meta->id,
            'indicador_id' => $indicador->id,
            'ods_id' => $ods->id,
            'pdn_id' => $pdn->id,
            'objetivo_estrategico_id' => $obj->id,
            'activo' => 1,
        ]);

        // 9) Redirección al listado
        $response->assertRedirect('/alineaciones');

        // 10) Verificar BD (tabla "alineaciones")
        $this->assertDatabaseHas('alineaciones', [
            'meta_id' => $meta->id,
            'indicador_id' => $indicador->id,
            'ods_id' => $ods->id,
            'pdn_id' => $pdn->id,
            'objetivo_estrategico_id' => $obj->id,
        ]);
    }
}
