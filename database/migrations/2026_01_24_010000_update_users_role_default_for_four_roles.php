<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * El rol por defecto pasa a consulta.
     *
     * Esto mantiene seguros los usuarios creados sin seleccionar rol:
     * por defecto solo podrán consultar, no modificar información.
     */
    public function up(): void
    {
        // SQLite se usa solo en pruebas y no acepta la palabra MODIFY de MySQL.
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE users MODIFY role VARCHAR(255) NOT NULL DEFAULT 'consulta'");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE users MODIFY role VARCHAR(255) NOT NULL DEFAULT 'tecnico'");
    }
};
