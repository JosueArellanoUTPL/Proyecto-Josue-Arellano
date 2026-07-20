<?php

namespace Database\Seeders;

use App\Models\Alineacion;
use App\Models\AuditLog;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DefensaSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar datos de prueba.
        Schema::disableForeignKeyConstraints();

        ProyectoAvanceEvidencia::truncate();
        ProyectoAvance::truncate();
        IndicadorAvance::truncate();
        AuditLog::truncate();
        Alineacion::truncate();
        Indicador::truncate();
        Meta::truncate();
        Proyecto::truncate();
        Programa::truncate();
        Plan::truncate();
        ObjetivoEstrategico::truncate();
        Ods::truncate();
        Pdn::truncate();
        Entidad::truncate();
        DB::table('password_reset_tokens')->truncate();
        DB::table('sessions')->truncate();
        User::truncate();

        Schema::enableForeignKeyConstraints();

        // Usuarios base para defensa.
        $admin = User::create([
            'name' => 'Administrador del Sistema',
            'email' => 'admin@sipeip.com',
            'password' => Hash::make('Admin123*'),
            'role' => User::ROLE_ADMIN,
            'activo' => true,
        ]);

        $planificacion = User::create([
            'name' => 'Responsable de Planificacion',
            'email' => 'planificacion@sipeip.com',
            'password' => Hash::make('Plan123*'),
            'role' => User::ROLE_PLANIFICACION,
            'activo' => true,
        ]);

        $tecnico = User::create([
            'name' => 'Tecnico de Seguimiento',
            'email' => 'tecnico@sipeip.com',
            'password' => Hash::make('Tecnico123*'),
            'role' => User::ROLE_TECNICO,
            'activo' => true,
        ]);

        User::create([
            'name' => 'Autoridad de Consulta',
            'email' => 'autoridad@sipeip.com',
            'password' => Hash::make('Consulta123*'),
            'role' => User::ROLE_CONSULTA,
            'activo' => true,
        ]);

        // Catalogo nacional usado por los dos casos.
        $pdn = Pdn::create([
            'codigo' => 'PND-2025-2029',
            'nombre' => 'Plan Nacional de Desarrollo Ecuador No Se Detiene 2025-2029',
            'descripcion' => 'Instrumento nacional usado como referencia para alinear planes institucionales.',
            'activo' => true,
        ]);

        $odsSalud = Ods::create([
            'codigo' => 'ODS 3',
            'nombre' => 'Salud y bienestar',
            'descripcion' => 'Garantizar una vida sana y promover el bienestar.',
            'activo' => true,
        ]);

        $odsEducacion = Ods::create([
            'codigo' => 'ODS 4',
            'nombre' => 'Educacion de calidad',
            'descripcion' => 'Garantizar una educacion inclusiva, equitativa y de calidad.',
            'activo' => true,
        ]);

        $objetivoSalud = ObjetivoEstrategico::create([
            'codigo' => 'OE-SALUD',
            'nombre' => 'Fortalecer la atencion publica de salud',
            'descripcion' => 'Mejorar la cobertura y seguimiento de servicios de salud prioritarios.',
            'activo' => true,
        ]);

        $objetivoEducacion = ObjetivoEstrategico::create([
            'codigo' => 'OE-EDU',
            'nombre' => 'Mejorar la calidad del servicio educativo',
            'descripcion' => 'Impulsar condiciones adecuadas para el aprendizaje.',
            'activo' => true,
        ]);

        // Caso 1: Ministerio de Salud Publica.
        $salud = Entidad::create([
            'codigo' => 'MSP',
            'nombre' => 'Ministerio de Salud Publica',
            'descripcion' => 'Entidad rectora de la salud publica en Ecuador.',
            'activo' => true,
        ]);

        $planSalud = Plan::create([
            'codigo' => 'PLAN-MSP-2026',
            'nombre' => 'Plan Institucional de Salud Publica 2026',
            'descripcion' => 'Plan operativo para fortalecer servicios preventivos y seguimiento de salud.',
            'anio_inicio' => 2026,
            'anio_fin' => 2026,
            'pdn_id' => $pdn->id,
            'entidad_id' => $salud->id,
            'activo' => true,
        ]);

        $metaSalud = Meta::create([
            'codigo' => 'META-MSP-01',
            'nombre' => 'Incrementar controles preventivos de salud',
            'descripcion' => 'Meta orientada al seguimiento de controles preventivos en establecimientos priorizados.',
            'plan_id' => $planSalud->id,
            'activo' => true,
        ]);

        $indicadorSalud = Indicador::create([
            'codigo' => 'IND-MSP-01',
            'nombre' => 'Porcentaje de controles preventivos ejecutados',
            'descripcion' => 'Mide el cumplimiento de controles preventivos planificados.',
            'meta_id' => $metaSalud->id,
            'linea_base' => 0,
            'valor_meta' => 100,
            'unidad' => '%',
            'activo' => true,
        ]);

        IndicadorAvance::create([
            'indicador_id' => $indicadorSalud->id,
            'user_id' => $tecnico->id,
            'fecha' => '2026-05-15',
            'valor_reportado' => 45,
            'comentario' => 'Primer corte de controles preventivos ejecutados.',
        ]);

        IndicadorAvance::create([
            'indicador_id' => $indicadorSalud->id,
            'user_id' => $tecnico->id,
            'fecha' => '2026-06-15',
            'valor_reportado' => 70,
            'comentario' => 'Avance acumulado reportado por unidades operativas.',
        ]);

        $programaSalud = Programa::create([
            'codigo' => 'PROG-MSP-01',
            'entidad_id' => $salud->id,
            'nombre' => 'Programa de atencion primaria en salud',
            'descripcion' => 'Programa enfocado en servicios preventivos y atencion cercana a la ciudadania.',
            'activo' => true,
        ]);

        $proyectoSalud = Proyecto::create([
            'codigo' => 'PROY-MSP-01',
            'nombre' => 'Brigadas de salud comunitaria',
            'descripcion' => 'Proyecto para ejecutar controles preventivos en territorio.',
            'programa_id' => $programaSalud->id,
            'activo' => true,
        ]);

        ProyectoAvance::create([
            'proyecto_id' => $proyectoSalud->id,
            'user_id' => $tecnico->id,
            'fecha' => '2026-06-20',
            'porcentaje_avance' => 65,
            'comentario' => 'Brigadas comunitarias ejecutadas en establecimientos priorizados.',
        ]);

        Alineacion::create([
            'meta_id' => $metaSalud->id,
            'ods_id' => $odsSalud->id,
            'objetivo_estrategico_id' => $objetivoSalud->id,
            'activo' => true,
        ]);

        // Caso 2: Ministerio de Educacion.
        $educacion = Entidad::create([
            'codigo' => 'MINEDUC',
            'nombre' => 'Ministerio de Educacion',
            'descripcion' => 'Entidad responsable de la politica publica educativa.',
            'activo' => true,
        ]);

        $planEducacion = Plan::create([
            'codigo' => 'PLAN-MINEDUC-2026',
            'nombre' => 'Plan Institucional de Educacion 2026',
            'descripcion' => 'Plan operativo para mejorar servicios educativos priorizados.',
            'anio_inicio' => 2026,
            'anio_fin' => 2026,
            'pdn_id' => $pdn->id,
            'entidad_id' => $educacion->id,
            'activo' => true,
        ]);

        $metaEducacion = Meta::create([
            'codigo' => 'META-MINEDUC-01',
            'nombre' => 'Mejorar ambientes educativos priorizados',
            'descripcion' => 'Meta enfocada en mejorar condiciones de infraestructura educativa.',
            'plan_id' => $planEducacion->id,
            'activo' => true,
        ]);

        $indicadorEducacion = Indicador::create([
            'codigo' => 'IND-MINEDUC-01',
            'nombre' => 'Porcentaje de unidades educativas atendidas',
            'descripcion' => 'Mide el avance de atencion a unidades educativas priorizadas.',
            'meta_id' => $metaEducacion->id,
            'linea_base' => 10,
            'valor_meta' => 100,
            'unidad' => '%',
            'activo' => true,
        ]);

        IndicadorAvance::create([
            'indicador_id' => $indicadorEducacion->id,
            'user_id' => $tecnico->id,
            'fecha' => '2026-05-10',
            'valor_reportado' => 40,
            'comentario' => 'Primer avance de unidades educativas atendidas.',
        ]);

        IndicadorAvance::create([
            'indicador_id' => $indicadorEducacion->id,
            'user_id' => $tecnico->id,
            'fecha' => '2026-06-10',
            'valor_reportado' => 75,
            'comentario' => 'Segundo avance registrado por seguimiento tecnico.',
        ]);

        $programaEducacion = Programa::create([
            'codigo' => 'PROG-MINEDUC-01',
            'entidad_id' => $educacion->id,
            'nombre' => 'Programa de mejora de infraestructura educativa',
            'descripcion' => 'Programa para atender necesidades de infraestructura en unidades educativas.',
            'activo' => true,
        ]);

        $proyectoEducacion = Proyecto::create([
            'codigo' => 'PROY-MINEDUC-01',
            'nombre' => 'Mantenimiento de unidades educativas priorizadas',
            'descripcion' => 'Proyecto para mejorar ambientes escolares y condiciones basicas.',
            'programa_id' => $programaEducacion->id,
            'activo' => true,
        ]);

        ProyectoAvance::create([
            'proyecto_id' => $proyectoEducacion->id,
            'user_id' => $tecnico->id,
            'fecha' => '2026-06-18',
            'porcentaje_avance' => 55,
            'comentario' => 'Mantenimientos ejecutados en unidades educativas priorizadas.',
        ]);

        Alineacion::create([
            'meta_id' => $metaEducacion->id,
            'ods_id' => $odsEducacion->id,
            'objetivo_estrategico_id' => $objetivoEducacion->id,
            'activo' => true,
        ]);

        // Auditoria inicial realista.
        $this->registrarAuditoria($admin, 'Entidades', 'crear', 'Se creo la entidad Ministerio de Salud Publica.', ['entidad' => $salud->nombre]);
        $this->registrarAuditoria($admin, 'Entidades', 'crear', 'Se creo la entidad Ministerio de Educacion.', ['entidad' => $educacion->nombre]);
        $this->registrarAuditoria($planificacion, 'Planes', 'crear', 'Se registraron los planes institucionales 2026.', ['planes' => [$planSalud->codigo, $planEducacion->codigo]]);
        $this->registrarAuditoria($planificacion, 'Metas', 'crear', 'Se registraron metas institucionales para salud y educacion.', ['metas' => [$metaSalud->codigo, $metaEducacion->codigo]]);
        $this->registrarAuditoria($planificacion, 'Indicadores', 'crear', 'Se registraron indicadores para medir las metas.', ['indicadores' => [$indicadorSalud->codigo, $indicadorEducacion->codigo]]);
        $this->registrarAuditoria($planificacion, 'Alineaciones', 'crear', 'Se alinearon las metas con ODS y objetivos estrategicos.', ['ods' => [$odsSalud->codigo, $odsEducacion->codigo]]);
        $this->registrarAuditoria($planificacion, 'Proyectos', 'crear', 'Se registraron proyectos asociados a programas institucionales.', ['proyectos' => [$proyectoSalud->codigo, $proyectoEducacion->codigo]]);
        $this->registrarAuditoria($tecnico, 'Seguimiento', 'crear', 'Se registraron avances iniciales de indicadores y proyectos.', ['periodo' => '2026']);
    }

    private function registrarAuditoria(User $user, string $module, string $action, string $description, array $metadata): void
    {
        AuditLog::create([
            'user_id' => $user->id,
            'module' => $module,
            'action' => $action,
            'method' => 'SEED',
            'route_name' => 'database.seed',
            'url' => 'Carga inicial para defensa',
            'ip_address' => '127.0.0.1',
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }
}
