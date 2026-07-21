<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('actividades_operativas', function (Blueprint $table) {
            $table->string('estado', 20)->default('borrador')->after('presupuesto');
            $table->text('comentario_revision')->nullable()->after('estado');
            $table->foreignId('revisado_por')->nullable()->after('comentario_revision')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('revisado_en')->nullable()->after('revisado_por');
        });
    }

    public function down(): void
    {
        Schema::table('actividades_operativas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('revisado_por');
            $table->dropColumn(['estado', 'comentario_revision', 'revisado_en']);
        });
    }
};
