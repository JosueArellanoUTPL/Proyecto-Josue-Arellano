<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actividades_operativas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();
            $table->foreignId('plan_id')->constrained('planes')->cascadeOnDelete();
            $table->string('responsable', 150);
            $table->unsignedSmallInteger('anio');
            $table->string('meta_operativa', 200);
            $table->unsignedTinyInteger('avance')->default(0);
            $table->decimal('presupuesto', 15, 2)->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividades_operativas');
    }
};
