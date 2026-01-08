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
    Schema::create('proyectos', function (Blueprint $table) {
        $table->id();
        $table->string('nombre', 150);
        $table->text('descripcion')->nullable();

        $table->foreignId('entidad_id')->constrained('entidades')->onDelete('restrict');
        $table->foreignId('programa_id')->constrained('programas')->onDelete('restrict');

        $table->boolean('activo')->default(true);
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyectos');
    }
};
