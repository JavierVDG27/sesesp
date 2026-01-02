<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('expediente_detalles', function (Blueprint $table) {
            // Cambiar a TEXT para permitir textos largos
            $table->text('tabla8_fecha_entrega')->nullable()->change();
            $table->text('tabla8_responsable_validar')->nullable()->change();
            $table->text('tabla8_lugar_entrega')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('expediente_detalles', function (Blueprint $table) {
            // Volver a string(255) si hiciera falta
            $table->string('tabla8_fecha_entrega', 255)->nullable()->change();
            $table->string('tabla8_responsable_validar', 255)->nullable()->change();
            $table->string('tabla8_lugar_entrega', 255)->nullable()->change();
        });
    }
};
