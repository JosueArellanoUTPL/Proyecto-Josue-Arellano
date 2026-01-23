<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('entidades', function (Blueprint $table) {
            $table->string('codigo', 20)->nullable()->after('id');
        });

        Schema::table('programas', function (Blueprint $table) {
            $table->string('codigo', 30)->nullable()->after('id');
        });

        Schema::table('proyectos', function (Blueprint $table) {
            $table->string('codigo', 30)->nullable()->after('id');
        });

        Schema::table('objetivo_estrategicos', function (Blueprint $table) {
            $table->string('codigo', 20)->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('entidades', function (Blueprint $table) {
            $table->dropColumn('codigo');
        });

        Schema::table('programas', function (Blueprint $table) {
            $table->dropColumn('codigo');
        });

        Schema::table('proyectos', function (Blueprint $table) {
            $table->dropColumn('codigo');
        });

        Schema::table('objetivo_estrategicos', function (Blueprint $table) {
            $table->dropColumn('codigo');
        });
    }
};

