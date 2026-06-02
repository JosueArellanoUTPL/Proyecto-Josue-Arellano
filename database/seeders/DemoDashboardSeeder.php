<?php

namespace Database\Seeders;

use App\Models\Alineacion;
use App\Models\Entidad;
use App\Models\Indicador;
use App\Models\IndicadorAvance;
use App\Models\Meta;
use App\Models\ObjetivoEstrategico;
use App\Models\Ods;
use App\Models\Pdn;
use App\Models\Plan;
use App\Models\Programa;
use App\Models\Proyecto;
use App\Models\ProyectoAvance;
use App\Models\ProyectoAvanceEvidencia;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDashboardSeeder extends Seeder
{
    /**
     * Data pequeña para que el dashboard, seguimiento y reportes luzcan completos.
     * Usa firstOrCreate/updateOrCreate para evitar duplicados si se ejecuta varias veces.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@sipeip.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('Admin123*'),
                'role' => User::ROLE_ADMIN,
            ]
        );

        $tecnico = User::firstOrCreate(
            ['email' => 'tecnico@sipeip.com'],
            [
                'name' => 'Técnico de Seguimiento',
                'password' => Hash::make('Tecnico123*'),
                'role' => User::ROLE_TECNICO,
            ]
        );

        User::firstOrCreate(
            ['email' => 'planificacion@sipeip.com'],
            [
                'name' => 'Responsable de Planificación',
                'password' => Hash::make('Plan123*'),
                'role' => User::ROLE_PLANIFICACION,
            ]
        );

        User::firstOrCreate(
            ['email' => 'autoridad@sipeip.com'],
            [
                'name' => 'Autoridad de Consulta',
                'password' => Hash::make('Consulta123*'),
                'role' => User::ROLE_CONSULTA,
            ]
        );

        $planificacion = Entidad::firstOrCreate(
            ['codigo' => 'ENT-PLAN'],
            [
                'nombre' => 'Dirección de Planificación',
                'descripcion' => 'Entidad responsable de coordinar planificación institucional.',
                'activo' => true,
            ]
        );

        $tecnologia = Entidad::firstOrCreate(
            ['codigo' => 'ENT-TIC'],
            [
                'nombre' => 'Dirección de Tecnología',
                'descripcion' => 'Entidad responsable de soporte tecnológico y sistemas.',
                'activo' => true,
            ]
        );

        $ods = Ods::firstOrCreate(
            ['codigo' => 'ODS 16'],
            [
                'nombre' => 'Paz, justicia e instituciones sólidas',
                'descripcion' => 'Fortalecimiento institucional y transparencia.',
                'activo' => true,
            ]
        );

        $pdn = Pdn::firstOrCreate(
            ['codigo' => 'PND-1'],
            [
                'nombre' => 'Eje institucional y gobernanza',
                'descripcion' => 'Mejora de servicios públicos y gestión institucional.',
                'activo' => true,
            ]
        );

        $objetivo = ObjetivoEstrategico::firstOrCreate(
            ['nombre' => 'Fortalecer la gestión institucional'],
            [
                'descripcion' => 'Optimizar procesos, seguimiento y toma de decisiones.',
                'activo' => true,
            ]
        );

        $programaPlanificacion = Programa::firstOrCreate(
            ['nombre' => 'Programa de Gestión Estratégica'],
            [
                'entidad_id' => $planificacion->id,
                'descripcion' => 'Programa para fortalecer planificación, monitoreo y evaluación.',
                'activo' => true,
            ]
        );

        $programaTecnologia = Programa::firstOrCreate(
            ['nombre' => 'Programa de Transformación Digital'],
            [
                'entidad_id' => $tecnologia->id,
                'descripcion' => 'Programa para digitalizar servicios y procesos institucionales.',
                'activo' => true,
            ]
        );

        $plan = Plan::firstOrCreate(
            ['codigo' => 'PLAN-2026'],
            [
                'nombre' => 'Plan Institucional 2026',
                'descripcion' => 'Plan académico de referencia para seguimiento SIPeIP simplificado.',
                'anio_inicio' => 2026,
                'anio_fin' => 2026,
                'pdn_id' => $pdn->id,
                'entidad_id' => $planificacion->id,
                'activo' => true,
            ]
        );

        $metaSeguimiento = Meta::firstOrCreate(
            ['codigo' => 'META-01'],
            [
                'nombre' => 'Incrementar cumplimiento de metas institucionales',
                'descripcion' => 'Meta orientada a mejorar el seguimiento de resultados.',
                'plan_id' => $plan->id,
                'valor_objetivo' => 100,
                'unidad' => '%',
                'activo' => true,
            ]
        );

        $metaDigital = Meta::firstOrCreate(
            ['codigo' => 'META-02'],
            [
                'nombre' => 'Digitalizar procesos priorizados',
                'descripcion' => 'Meta para impulsar procesos con soporte tecnológico.',
                'plan_id' => $plan->id,
                'valor_objetivo' => 100,
                'unidad' => '%',
                'activo' => true,
            ]
        );

        $indicadorSeguimiento = Indicador::firstOrCreate(
            ['codigo' => 'IND-01'],
            [
                'nombre' => 'Porcentaje de metas con seguimiento actualizado',
                'descripcion' => 'Mide el avance del seguimiento institucional.',
                'meta_id' => $metaSeguimiento->id,
                'linea_base' => 0,
                'valor_meta' => 100,
                'unidad' => '%',
                'activo' => true,
            ]
        );

        $indicadorDigital = Indicador::firstOrCreate(
            ['codigo' => 'IND-02'],
            [
                'nombre' => 'Procesos digitalizados',
                'descripcion' => 'Mide el porcentaje de procesos priorizados ya digitalizados.',
                'meta_id' => $metaDigital->id,
                'linea_base' => 0,
                'valor_meta' => 100,
                'unidad' => '%',
                'activo' => true,
            ]
        );

        $this->avanceIndicador($indicadorSeguimiento, $tecnico, now()->subMonths(2), 35, 'Primer corte de seguimiento institucional.');
        $this->avanceIndicador($indicadorSeguimiento, $tecnico, now()->subMonth(), 68, 'Se actualizó la matriz de seguimiento.');
        $this->avanceIndicador($indicadorDigital, $admin, now()->subMonth(), 40, 'Digitalización inicial de procesos.');
        $this->avanceIndicador($indicadorDigital, $tecnico, now(), 72, 'Procesos priorizados con avance operativo.');

        $proyectoMonitoreo = Proyecto::firstOrCreate(
            ['nombre' => 'Tablero de monitoreo institucional'],
            [
                'descripcion' => 'Proyecto para visualizar avances, indicadores y alertas.',
                'entidad_id' => $planificacion->id,
                'programa_id' => $programaPlanificacion->id,
                'activo' => true,
            ]
        );

        $proyectoServicios = Proyecto::firstOrCreate(
            ['nombre' => 'Digitalización de servicios internos'],
            [
                'descripcion' => 'Proyecto para mejorar procesos internos mediante herramientas digitales.',
                'entidad_id' => $tecnologia->id,
                'programa_id' => $programaTecnologia->id,
                'activo' => true,
            ]
        );

        $avanceMonitoreo = $this->avanceProyecto($proyectoMonitoreo, $tecnico, now()->subMonths(2), 30, 'Levantamiento inicial de necesidades.');
        $this->avanceProyecto($proyectoMonitoreo, $tecnico, now()->subMonth(), 65, 'Tablero funcional con indicadores principales.');
        $this->avanceProyecto($proyectoServicios, $admin, now()->subMonths(3), 25, 'Identificación de procesos críticos.');
        $avanceServicios = $this->avanceProyecto($proyectoServicios, $tecnico, now(), 80, 'Servicios internos con avance alto de digitalización.');

        $this->evidenciaProyecto($avanceMonitoreo, 'evidencias/proyectos/demo-monitoreo.pdf', 'demo-monitoreo.pdf');
        $this->evidenciaProyecto($avanceServicios, 'evidencias/proyectos/demo-servicios.pdf', 'demo-servicios.pdf');

        Alineacion::firstOrCreate(
            [
                'meta_id' => $metaSeguimiento->id,
                'indicador_id' => $indicadorSeguimiento->id,
            ],
            [
                'ods_id' => $ods->id,
                'pdn_id' => $pdn->id,
                'objetivo_estrategico_id' => $objetivo->id,
                'activo' => true,
            ]
        );

        Alineacion::firstOrCreate(
            [
                'meta_id' => $metaDigital->id,
                'indicador_id' => $indicadorDigital->id,
            ],
            [
                'ods_id' => $ods->id,
                'pdn_id' => $pdn->id,
                'objetivo_estrategico_id' => $objetivo->id,
                'activo' => true,
            ]
        );
    }

    private function avanceIndicador(Indicador $indicador, User $user, $fecha, int $valor, string $comentario): void
    {
        IndicadorAvance::updateOrCreate(
            [
                'indicador_id' => $indicador->id,
                'fecha' => $fecha->toDateString(),
            ],
            [
                'user_id' => $user->id,
                'valor_reportado' => $valor,
                'comentario' => $comentario,
            ]
        );
    }

    private function avanceProyecto(Proyecto $proyecto, User $user, $fecha, int $porcentaje, string $comentario): ProyectoAvance
    {
        return ProyectoAvance::updateOrCreate(
            [
                'proyecto_id' => $proyecto->id,
                'fecha' => $fecha->toDateString(),
            ],
            [
                'user_id' => $user->id,
                'porcentaje_avance' => $porcentaje,
                'comentario' => $comentario,
            ]
        );
    }

    private function evidenciaProyecto(ProyectoAvance $avance, string $path, string $name): void
    {
        ProyectoAvanceEvidencia::firstOrCreate(
            [
                'proyecto_avance_id' => $avance->id,
                'path' => $path,
            ],
            [
                'original_name' => $name,
                'mime_type' => 'application/pdf',
                'size' => 128000,
            ]
        );
    }
}
