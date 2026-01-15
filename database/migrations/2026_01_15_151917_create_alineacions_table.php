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

        // Siempre alineamos una META (obligatorio)
        $table->foreignId('meta_id')->constrained('metas')->cascadeOnDelete();

        // Opcional: si quieres alinear un INDICADOR específico
        $table->foreignId('indicador_id')->nullable()->constrained('indicadores')->nullOnDelete();

        // Instrumentos de alineación (al menos uno debe estar lleno)
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
        Schema::dropIfExists('alineacions');
    }
};
