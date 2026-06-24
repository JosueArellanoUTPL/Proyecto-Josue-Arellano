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
        Schema::create('planes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30);
            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();
            $table->year('anio_inicio');
            $table->year('anio_fin');
            $table->foreignId('pdn_id')->constrained('pdns');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    // Revertir cambios.
    public function down(): void
    {
        Schema::dropIfExists('planes');
    }
};
