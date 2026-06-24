<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Normalizar nombres antiguos de auditoria.
    public function up(): void
    {
        if (Schema::hasTable('audit_logs')) {
            DB::table('audit_logs')
                ->where('module', 'Plans')
                ->update(['module' => 'Planes']);
        }
    }

    // Mantener los nombres normalizados.
    public function down(): void
    {
        // No se restauran los nombres inconsistentes.
    }
};
