<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Corrige proyectos antiguos tomando como referencia la entidad del programa.
        DB::table('proyectos')
            ->join('programas', 'programas.id', '=', 'proyectos.programa_id')
            ->whereColumn('proyectos.entidad_id', '!=', 'programas.entidad_id')
            ->select('proyectos.id', 'programas.entidad_id')
            ->get()
            ->each(function ($row) {
                DB::table('proyectos')->where('id', $row->id)->update([
                    'entidad_id' => $row->entidad_id,
                ]);
            });

        // Todas las alineaciones históricas usan el PND del plan de su meta.
        DB::table('alineaciones')
            ->join('metas', 'metas.id', '=', 'alineaciones.meta_id')
            ->join('plans', 'plans.id', '=', 'metas.plan_id')
            ->select('alineaciones.id', 'plans.pdn_id')
            ->get()
            ->each(function ($row) {
                DB::table('alineaciones')->where('id', $row->id)->update([
                    'pdn_id' => $row->pdn_id,
                ]);
            });
    }

    public function down(): void
    {
        // La corrección de relaciones no se revierte para evitar inconsistencias.
    }
};
