<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Aplicar cambios.
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->foreignId('entidad_id')
                ->nullable()
                ->after('pdn_id')
                ->constrained('entidades')
                ->nullOnDelete();
        });
    }

    // Revertir cambios.
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('entidad_id');
        });
    }
};
