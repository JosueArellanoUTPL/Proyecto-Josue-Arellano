<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            // Quitar relación proyecto-meta.
            if (Schema::hasColumn('proyectos', 'meta_id')) {
                $table->dropConstrainedForeignId('meta_id');
            }
        });
    }

    public function down(): void
    {
        // No se restaura la relación porque ya no forma parte del alcance del proyecto.
    }
};
