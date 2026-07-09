<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Aplicar cambios.
    public function up(): void
    {
        Schema::create('indicador_avances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicador_id')->constrained('indicadores')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('fecha');
            $table->integer('valor_reportado');
            $table->text('comentario')->nullable();
            $table->string('evidencia_path')->nullable(); // PDF/JPG/PNG
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    // Revertir cambios.
    public function down(): void
    {
        Schema::dropIfExists('indicador_avances');
    }
};
