<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('actividades_operativas', function (Blueprint $table) {
            $table->date('fecha_inicio')->nullable()->after('anio');
            $table->date('fecha_fin')->nullable()->after('fecha_inicio');
            $table->string('unidad_medida', 50)->default('%')->after('meta_anual');
            $table->string('prioridad', 10)->default('media')->after('presupuesto');
        });
    }

    public function down(): void
    {
        Schema::table('actividades_operativas', function (Blueprint $table) {
            $table->dropColumn(['fecha_inicio', 'fecha_fin', 'unidad_medida', 'prioridad']);
        });
    }
};
