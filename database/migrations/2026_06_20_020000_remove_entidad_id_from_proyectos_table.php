<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // La entidad del proyecto se obtiene desde su programa.
        Schema::table('proyectos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('entidad_id');
        });
    }

    public function down(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->foreignId('entidad_id')
                ->nullable()
                ->constrained('entidades')
                ->restrictOnDelete();
        });
    }
};
