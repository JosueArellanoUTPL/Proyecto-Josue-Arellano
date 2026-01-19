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

class IndicadorProgresoTest extends TestCase
{
    use RefreshDatabase;

    public function test_calcula_progreso_del_indicador_correctamente()
    {
        // 1) Crear usuario (porque IndicadorAvance requiere user_id)
        $user = User::create([
            'name' => 'User Test',
            'email' => 'user@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // 2) Crear Entidad + PDN + Plan + Meta (para llegar al Indicador de forma real)
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

        // 3) Crear Indicador con línea base y valor meta
        $indicador = Indicador::create([
            'codigo' => 'IND-TEST',
            'nombre' => 'Indicador Test',
            'descripcion' => 'Indicador para pruebas',
            'meta_id' => $meta->id,
            'linea_base' => 55,
            'valor_meta' => 80,
            'unidad' => '%',
            'activo' => true,
        ]);

        // 4) Crear avance (último valor = 70)
        IndicadorAvance::create([
            'indicador_id' => $indicador->id,
            'user_id' => $user->id,
            'fecha' => now()->toDateString(),
            'valor_reportado' => 70,
            'comentario' => 'avance test',
            'evidencia_path' => null,
        ]);

        // 5) Recargar el indicador con su relación ultimoAvance
        $indicador = Indicador::with('ultimoAvance')->findOrFail($indicador->id);

        // 6) Progreso esperado: (70-55)/(80-55)*100 = 60
        $this->assertEquals(60.0, round($indicador->progreso, 1));
    }
}
