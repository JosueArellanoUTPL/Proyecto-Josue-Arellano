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

class AvanceConEvidenciaTest extends TestCase
{
    use RefreshDatabase;

    public function test_registrar_avance_con_evidencia_guarda_bd_y_archivo()
    {
        // Fake storage para no ensuciar tu disco real
        Storage::fake('public');

        // Usuario (técnico o admin). Uso técnico para que sea realista.
        $user = User::create([
            'name' => 'Tecnico Test',
            'email' => 'tecnico@test.com',
            'password' => Hash::make('password123'),
            'role' => 'tecnico',
        ]);

        // Crear datos mínimos: Entidad -> PDN -> Plan -> Meta -> Indicador
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

        // Archivo fake (PDF)
        $file = UploadedFile::fake()->create('evidencia.pdf', 200, 'application/pdf');

        // POST al endpoint real del seguimiento
        $response = $this->actingAs($user)->post(
            "/seguimiento/indicadores/{$indicador->id}/avance",
            [
                'fecha' => '2026-01-18',
                'valor_reportado' => 70,
                'comentario' => 'Prueba con evidencia',
                'evidencia' => $file,
            ]
        );

        // Debe redirigir al detalle de la meta
        $response->assertRedirect(route('seguimiento.meta.show', $meta->id));

        // Verificar que se creó el registro en BD
        $this->assertDatabaseHas('indicador_avances', [
            'indicador_id' => $indicador->id,
            'user_id' => $user->id,
            'valor_reportado' => 70,
        ]);

        // Obtener el registro para verificar el path
        $avance = IndicadorAvance::first();
        $this->assertNotNull($avance->evidencia_path);

        // Verificar que el archivo se guardó en storage fake
        Storage::disk('public')->assertExists($avance->evidencia_path);
    }
}
