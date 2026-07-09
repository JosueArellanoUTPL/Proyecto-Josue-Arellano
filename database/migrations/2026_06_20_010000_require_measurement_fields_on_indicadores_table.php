<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Completa registros antiguos antes de volver obligatorios los campos.
        DB::table('indicadores')->whereNull('linea_base')->update(['linea_base' => 0]);
        DB::table('indicadores')->whereNull('valor_meta')->update(['valor_meta' => 100]);
        DB::table('indicadores')->whereNull('unidad')->update(['unidad' => '%']);

        Schema::table('indicadores', function (Blueprint $table) {
            $table->integer('linea_base')->nullable(false)->change();
            $table->integer('valor_meta')->nullable(false)->change();
            $table->string('unidad', 50)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('indicadores', function (Blueprint $table) {
            $table->integer('linea_base')->nullable()->change();
            $table->integer('valor_meta')->nullable()->change();
            $table->string('unidad', 50)->nullable()->change();
        });
    }
};
