<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('alineaciones', function (Blueprint $table) {
            $table->id();

            // Relacion principal.
            $table->foreignId('meta_id')->constrained('metas')->cascadeOnDelete();

            // Columna historica retirada en una migracion posterior.
            $table->foreignId('indicador_id')->nullable()->constrained('indicadores')->nullOnDelete();

            // Instrumentos estrategicos.
            $table->foreignId('ods_id')->nullable()->constrained('ods')->nullOnDelete();
            $table->foreignId('pdn_id')->nullable()->constrained('pdns')->nullOnDelete();
            $table->foreignId('objetivo_estrategico_id')->nullable()->constrained('objetivo_estrategicos')->nullOnDelete();

            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alineaciones');
    }
};
