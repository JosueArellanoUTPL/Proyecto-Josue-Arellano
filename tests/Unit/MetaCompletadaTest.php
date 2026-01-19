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

class MetaCompletadaTest extends TestCase
{
    use RefreshDatabase;

    public function test_meta_no_completada_si_falta_un_indicador_por_completar()
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

        // Indicador 1 (completo)
        $ind1 = Indicador::create([
            'codigo' => 'IND-01',
            'nombre' => 'Indicador 1',
            'descripcion' => 'Indicador completo',
            'meta_id' => $meta->id,
            'linea_base' => 55,
            'valor_meta' => 80,
            'unidad' => '%',
            'activo' => true,
        ]);

        // Indicador 2 (incompleto)
        $ind2 = Indicador::create([
            'codigo' => 'IND-02',
            'nombre' => 'Indicador 2',
            'descripcion' => 'Indicador incompleto',
            'meta_id' => $meta->id,
            'linea_base' => 55,
            'valor_meta' => 80,
            'unidad' => '%',
            'activo' => true,
        ]);

        // Avance para ind1 en 80 => 100%
        IndicadorAvance::create([
            'indicador_id' => $ind1->id,
            'user_id' => $user->id,
            'fecha' => now()->toDateString(),
            'valor_reportado' => 80,
            'comentario' => 'avance completo',
            'evidencia_path' => null,
        ]);

        // Avance para ind2 en 70 => 60%
        IndicadorAvance::create([
            'indicador_id' => $ind2->id,
            'user_id' => $user->id,
            'fecha' => now()->toDateString(),
            'valor_reportado' => 70,
            'comentario' => 'avance incompleto',
            'evidencia_path' => null,
        ]);

        // Recargar meta con indicadores y ultimoAvance (importante)
        $meta = Meta::with(['indicadores.ultimoAvance'])->findOrFail($meta->id);

        // Como no todos están completos, meta NO debe estar completada
        $this->assertFalse($meta->completada);
    }

    public function test_meta_completada_si_todos_los_indicadores_estan_completos()
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

        // Dos indicadores
        $ind1 = Indicador::create([
            'codigo' => 'IND-01',
            'nombre' => 'Indicador 1',
            'descripcion' => 'Indicador 1',
            'meta_id' => $meta->id,
            'linea_base' => 55,
            'valor_meta' => 80,
            'unidad' => '%',
            'activo' => true,
        ]);

        $ind2 = Indicador::create([
            'codigo' => 'IND-02',
            'nombre' => 'Indicador 2',
            'descripcion' => 'Indicador 2',
            'meta_id' => $meta->id,
            'linea_base' => 55,
            'valor_meta' => 80,
            'unidad' => '%',
            'activo' => true,
        ]);

        // Avances completos para ambos (80 => 100%)
        IndicadorAvance::create([
            'indicador_id' => $ind1->id,
            'user_id' => $user->id,
            'fecha' => now()->toDateString(),
            'valor_reportado' => 80,
            'comentario' => 'completo',
            'evidencia_path' => null,
        ]);

        IndicadorAvance::create([
            'indicador_id' => $ind2->id,
            'user_id' => $user->id,
            'fecha' => now()->toDateString(),
            'valor_reportado' => 80,
            'comentario' => 'completo',
            'evidencia_path' => null,
        ]);

        // Recargar meta con indicadores y ultimoAvance
        $meta = Meta::with(['indicadores.ultimoAvance'])->findOrFail($meta->id);

        // Ahora sí debe estar completada
        $this->assertTrue($meta->completada);
    }
}
