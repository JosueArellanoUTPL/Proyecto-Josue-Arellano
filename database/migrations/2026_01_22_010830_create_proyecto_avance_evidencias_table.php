<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('proyecto_avance_evidencias', function (Blueprint $table) {
    $table->id();
    $table->foreignId('proyecto_avance_id')->constrained()->cascadeOnDelete();
    $table->string('path');
    $table->string('original_name')->nullable();
    $table->string('mime_type')->nullable();
    $table->unsignedBigInteger('size')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyecto_avance_evidencias');
    }
};
