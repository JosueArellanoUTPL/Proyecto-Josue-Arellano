<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('actividades_operativas', function (Blueprint $table) {
            $table->foreignId('proyecto_id')->nullable()->after('plan_id')
                ->constrained('proyectos')->nullOnDelete();
            $table->foreignId('objetivo_estrategico_id')->nullable()->after('proyecto_id')
                ->constrained('objetivos_estrategicos')->nullOnDelete();
            $table->foreignId('indicador_id')->nullable()->after('objetivo_estrategico_id')
                ->constrained('indicadores')->nullOnDelete();
            $table->string('evidencia')->nullable()->after('comentario_revision');
        });
    }

    public function down(): void
    {
        Schema::table('actividades_operativas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('proyecto_id');
            $table->dropConstrainedForeignId('objetivo_estrategico_id');
            $table->dropConstrainedForeignId('indicador_id');
            $table->dropColumn('evidencia');
        });
    }
};
