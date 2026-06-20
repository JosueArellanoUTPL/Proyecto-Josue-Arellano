<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            // Conecta cada proyecto con la meta a la que ayuda a cumplir.
            // Es nullable para conservar los proyectos creados anteriormente.
            $table->foreignId('meta_id')
                ->nullable()
                ->after('programa_id')
                ->constrained('metas')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('meta_id');
        });
    }
};