<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Normalizar nombres de tablas del dominio.
    public function up(): void
    {
        if (Schema::hasTable('plans') && ! Schema::hasTable('planes')) {
            Schema::rename('plans', 'planes');
        }

        if (Schema::hasTable('objetivo_estrategicos') && ! Schema::hasTable('objetivos_estrategicos')) {
            Schema::rename('objetivo_estrategicos', 'objetivos_estrategicos');
        }
    }

    // Mantener los nombres normalizados.
    public function down(): void
    {
        // No se restauran los nombres inconsistentes.
    }
};
