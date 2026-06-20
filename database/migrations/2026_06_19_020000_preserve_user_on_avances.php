<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite se usa en pruebas. La aplicación real usa MySQL.
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        $this->makeUserOptional('indicador_avances');
        $this->makeUserOptional('proyecto_avances');
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        $this->makeUserRequired('indicador_avances');
        $this->makeUserRequired('proyecto_avances');
    }

    private function makeUserOptional(string $tableName): void
    {
        Schema::table($tableName, function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table($tableName, function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    private function makeUserRequired(string $tableName): void
    {
        Schema::table($tableName, function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table($tableName, function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
