<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Completa códigos antiguos que quedaron vacíos cuando se agregó la columna.
        $this->fillCodes('entidades', 'ENT');
        $this->fillCodes('programas', 'PRG');
        $this->fillCodes('proyectos', 'PRY');
        $this->fillCodes('objetivos_estrategicos', 'OE');
    }

    public function down(): void
    {
        // No se borran códigos al revertir para no perder información de negocio.
    }

    private function fillCodes(string $table, string $prefix): void
    {
        DB::table($table)
            ->whereNull('codigo')
            ->orWhere('codigo', '')
            ->orderBy('id')
            ->get(['id'])
            ->each(function ($row) use ($table, $prefix) {
                DB::table($table)
                    ->where('id', $row->id)
                    ->update(['codigo' => $prefix.'-'.str_pad((string) $row->id, 3, '0', STR_PAD_LEFT)]);
            });
    }
};
