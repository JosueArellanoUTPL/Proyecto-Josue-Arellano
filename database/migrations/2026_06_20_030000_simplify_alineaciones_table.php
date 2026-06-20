<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // La meta ya permite conocer su PND y sus indicadores.
        Schema::table('alineaciones', function (Blueprint $table) {
            $table->dropConstrainedForeignId('indicador_id');
            $table->dropConstrainedForeignId('pdn_id');
        });
    }

    public function down(): void
    {
        Schema::table('alineaciones', function (Blueprint $table) {
            $table->foreignId('indicador_id')->nullable()->constrained('indicadores')->nullOnDelete();
            $table->foreignId('pdn_id')->nullable()->constrained('pdns')->nullOnDelete();
        });
    }
};
