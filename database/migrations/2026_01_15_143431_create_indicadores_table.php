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
    Schema::create('indicadores', function (Blueprint $table) {
        $table->id();
        $table->string('codigo', 30);
        $table->string('nombre', 200);
        $table->text('descripcion')->nullable();

        // Relación con Meta
        $table->foreignId('meta_id')->constrained('metas')->cascadeOnDelete();

        // Campos simples para medición (académico)
        $table->decimal('linea_base', 15, 2)->nullable();
        $table->decimal('valor_meta', 15, 2)->nullable();
        $table->string('unidad', 50)->nullable(); // %, USD, unidades, etc.

        $table->boolean('activo')->default(true);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicadors');
    }
};
