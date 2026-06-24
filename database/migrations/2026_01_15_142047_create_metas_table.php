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
        Schema::create('metas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30);
            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();

            // Relacion con plan.
            $table->foreignId('plan_id')->constrained('planes')->cascadeOnDelete();

            // Campos historicos retirados en una migracion posterior.
            $table->decimal('valor_objetivo', 15, 2)->nullable();
            $table->string('unidad', 50)->nullable();

            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metas');
    }
};
