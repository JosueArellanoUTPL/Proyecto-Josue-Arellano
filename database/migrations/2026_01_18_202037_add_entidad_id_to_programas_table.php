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
        Schema::table('programas', function (Blueprint $table) {
            $table->foreignId('entidad_id')->nullable()->constrained('entidades')->nullOnDelete()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    // Revertir cambios.
    public function down(): void
    {
        Schema::table('programas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('entidad_id');
        });
    }
};
