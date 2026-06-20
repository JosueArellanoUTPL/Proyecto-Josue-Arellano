<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla para registrar acciones importantes del sistema.
     *
     * La auditoría del sistema se enfoca en:
     * - usuario que realizó la acción
     * - módulo afectado
     * - tipo de acción: crear, actualizar, eliminar, etc.
     * - ruta, método HTTP e IP
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('module', 80)->nullable();
            $table->string('action', 50);
            $table->string('method', 10);
            $table->string('route_name')->nullable();
            $table->text('url');
            $table->string('ip_address', 45)->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['module', 'action']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
