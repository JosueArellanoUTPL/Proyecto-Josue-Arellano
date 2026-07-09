<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // Aplicar cambios.
    public function up(): void
    {
        // Redondeo datos existentes antes de cambiar las columnas a enteros.
        DB::statement('UPDATE indicadores SET linea_base = ROUND(linea_base), valor_meta = ROUND(valor_meta)');
        DB::statement('UPDATE indicador_avances SET valor_reportado = ROUND(valor_reportado)');

        // Valores base y meta del indicador como enteros.
        DB::statement('ALTER TABLE indicadores MODIFY linea_base INT NOT NULL');
        DB::statement('ALTER TABLE indicadores MODIFY valor_meta INT NOT NULL');

        // Valor registrado en cada avance como entero.
        DB::statement('ALTER TABLE indicador_avances MODIFY valor_reportado INT NOT NULL');
    }

    // Revertir cambios.
    public function down(): void
    {
        DB::statement('ALTER TABLE indicadores MODIFY linea_base DECIMAL(15,2) NOT NULL');
        DB::statement('ALTER TABLE indicadores MODIFY valor_meta DECIMAL(15,2) NOT NULL');
        DB::statement('ALTER TABLE indicador_avances MODIFY valor_reportado DECIMAL(15,2) NOT NULL');
    }
};
