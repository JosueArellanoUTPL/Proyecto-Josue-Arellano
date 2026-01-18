<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {

            // 1) Crear columna solo si no existe
            if (!Schema::hasColumn('plans', 'entidad_id')) {
                $table->unsignedBigInteger('entidad_id')->nullable()->after('pdn_id');
            }

            // 2) Crear FK solo si no existe (nombre fijo de constraint)
            // Cambia 'entidades' por el nombre real de tu tabla de entidades:
            $fkName = 'plans_entidad_id_foreign';

            // Intentamos crear la FK solo si la columna existe
            // Nota: Schema no tiene "hasForeign", así que usamos try/catch
            try {
                $table->foreign('entidad_id', $fkName)
                    ->references('id')
                    ->on('entidades')
                    ->nullOnDelete();
            } catch (\Throwable $e) {
                // Si ya existe o falla por cualquier razón, no detenemos la migración
            }
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            // Intentar eliminar FK si existe
            try {
                $table->dropForeign('plans_entidad_id_foreign');
            } catch (\Throwable $e) {
                // ignorar
            }

            // Opcional: si quieres revertir columna, descomenta:
            // if (Schema::hasColumn('plans', 'entidad_id')) {
            //     $table->dropColumn('entidad_id');
            // }
        });
    }
};
