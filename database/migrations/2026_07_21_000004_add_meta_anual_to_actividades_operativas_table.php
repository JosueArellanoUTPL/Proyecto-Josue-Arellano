<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('actividades_operativas', function (Blueprint $table) {
            $table->unsignedTinyInteger('meta_anual')->default(100)->after('meta_operativa');
        });
    }

    public function down(): void
    {
        Schema::table('actividades_operativas', function (Blueprint $table) {
            $table->dropColumn('meta_anual');
        });
    }
};
