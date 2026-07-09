<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // Aplicar cambios.
    public function up(): void
    {
        // El avance de proyecto se maneja como porcentaje entero de 0 a 100.
        DB::statement('ALTER TABLE proyecto_avances MODIFY porcentaje_avance TINYINT UNSIGNED NOT NULL');
    }

    // Revertir cambios.
    public function down(): void
    {
        // Se deja decimal por si se necesita volver al formato anterior.
        DB::statement('ALTER TABLE proyecto_avances MODIFY porcentaje_avance DECIMAL(5,2) NOT NULL');
    }
};
