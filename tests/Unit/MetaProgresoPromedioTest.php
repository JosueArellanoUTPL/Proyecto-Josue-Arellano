<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pdn;
use App\Models\Entidad;
use App\Models\Plan;
use App\Models\Meta;
use App\Models\Indicador;
use App\Models\IndicadorAvance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class MetaProgresoPromedioTest extends TestCase
{
    use RefreshDatabase;

    public function test_progreso_meta_es_promedio_de_indicadores()
    {
        // User para avances
        $user = User::create([
            'name' => 'User Test',
            'email' => 'user@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Entidad + PDN + Plan + Meta
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

        // Indicadores con misma base/meta para que el cálculo sea consistente
        // Base=50, Meta=100

        $ind1 = Indicador::create([
            'codigo' => 'IND-01',
            'nombre' => 'Indicador 1',
            'descripcion' => 'Indicador 1',
            'meta_id' => $meta->id,
            'linea_base' => 50,
            'valor_meta' => 100,
            'unidad' => '%',
            'activo' => true,
        ]);

        $ind2 = Indicador::create([
            'codigo' => 'IND-02',
            'nombre' => 'Indicador 2',
            'descripcion' => 'Indicador 2',
            'meta_id' => $meta->id,
            'linea_base' => 50,
            'valor_meta' => 100,
            'unidad' => '%',
            'activo' => true,
        ]);

        // Avances:
        // IND1 -> valor_reportado=60 => (60-50)/(100-50)=10/50=20%
        // IND2 -> valor_reportado=80 => (80-50)/(100-50)=30/50=60%

        IndicadorAvance::create([
            'indicador_id' => $ind1->id,
            'user_id' => $user->id,
            'fecha' => now()->toDateString(),
            'valor_reportado' => 60,
            'comentario' => '20%',
            'evidencia_path' => null,
        ]);

        IndicadorAvance::create([
            'indicador_id' => $ind2->id,
            'user_id' => $user->id,
            'fecha' => now()->toDateString(),
            'valor_reportado' => 80,
            'comentario' => '60%',
            'evidencia_path' => null,
        ]);

        // Recargar meta con indicadores y ultimoAvance para que los accessors calculen bien
        $meta = Meta::with(['indicadores.ultimoAvance'])->findOrFail($meta->id);

        // Promedio esperado: (20 + 60)/2 = 40
        $this->assertEquals(40.0, round($meta->progreso, 1));
    }
}
