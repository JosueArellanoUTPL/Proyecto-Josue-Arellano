<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Entidad;
use App\Models\Pdn;
use App\Models\Plan;
use App\Models\Meta;
use App\Models\Indicador;
use App\Models\IndicadorAvance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class TecnicoSeguimientoTest extends TestCase
{
    use RefreshDatabase;

    public function test_tecnico_puede_ver_seguimiento_y_registrar_avance()
    {
        Storage::fake('public');

        // 1) Técnico
        $tecnico = User::create([
            'name' => 'Tecnico Test',
            'email' => 'tecnico@test.com',
            'password' => Hash::make('password123'),
            'role' => 'tecnico',
        ]);

        // 2) Datos mínimos del sistema
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

        // 3) Técnico puede ver la pantalla de seguimiento (200 OK)
        $this->actingAs($tecnico)
            ->get('/seguimiento/metas')
            ->assertStatus(200);

        // 4) Técnico registra avance con evidencia
        $file = UploadedFile::fake()->create('evidencia.pdf', 120, 'application/pdf');

        $response = $this->actingAs($tecnico)->post(
            "/seguimiento/indicadores/{$indicador->id}/avance",
            [
                'fecha' => '2026-01-18',
                'valor_reportado' => 70,
                'comentario' => 'Avance técnico',
                'evidencia' => $file,
            ]
        );

        // 5) Debe redirigir a detalle de la meta
        $response->assertRedirect(route('seguimiento.meta.show', $meta->id));

        // 6) Verificar BD
        $this->assertDatabaseHas('indicador_avances', [
            'indicador_id' => $indicador->id,
            'user_id' => $tecnico->id,
            'valor_reportado' => 70,
        ]);

        // 7) Verificar archivo guardado
        $avance = IndicadorAvance::first();
        $this->assertNotNull($avance->evidencia_path);

        Storage::disk('public')->assertExists($avance->evidencia_path);
    }
}
