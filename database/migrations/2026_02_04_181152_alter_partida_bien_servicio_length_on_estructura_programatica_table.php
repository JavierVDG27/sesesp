<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expediente_estructura_programatica', function (Blueprint $table) {
            // Aumentar el tamaño del VARCHAR
            $table->string('partida_bien_servicio', 1000)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('expediente_estructura_programatica', function (Blueprint $table) {
            // Regresar al tamaño anterior (por defecto 255)
            $table->string('partida_bien_servicio', 255)->nullable()->change();
        });
    }
};
