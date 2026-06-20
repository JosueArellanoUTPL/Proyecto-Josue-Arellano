<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // La medicion se guarda solamente en los indicadores.
        Schema::table('metas', function (Blueprint $table) {
            $table->dropColumn(['valor_objetivo', 'unidad']);
        });
    }

    public function down(): void
    {
        // Permite recuperar los campos si se revierte la migracion.
        Schema::table('metas', function (Blueprint $table) {
            $table->decimal('valor_objetivo', 15, 2)->nullable();
            $table->string('unidad', 50)->nullable();
        });
    }
};
