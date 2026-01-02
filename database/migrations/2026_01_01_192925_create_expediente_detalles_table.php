<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('expediente_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->unique()->constrained('expedientes')->cascadeOnDelete();

            // Secciones 1..21 (textos)
            $table->longText('introduccion')->nullable();
            $table->longText('marco_legal')->nullable();
            $table->longText('objeto')->nullable();
            $table->longText('alcance')->nullable();
            $table->longText('justificacion')->nullable();
            $table->longText('requerimientos')->nullable();
            $table->longText('criterios_aceptacion')->nullable();

            // 9..16 (por default "No aplica", pero editables)
            $table->longText('no_aplica_9')->nullable();
            $table->longText('no_aplica_10')->nullable();
            $table->longText('no_aplica_11')->nullable();
            $table->longText('no_aplica_12')->nullable();
            $table->longText('no_aplica_13')->nullable();
            $table->longText('no_aplica_14')->nullable();
            $table->longText('no_aplica_15')->nullable();
            $table->longText('no_aplica_16')->nullable();

            // 17..21 (firmas/validación)
            $table->string('responsable_subprograma_nombre')->nullable();
            $table->string('responsable_subprograma_cargo')->nullable();
            $table->string('titular_dependencia_nombre')->nullable();
            $table->string('titular_dependencia_cargo')->nullable();
            $table->longText('observaciones_finales')->nullable();

            $table->boolean('segunda_parte_completa')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('expediente_detalles');
    }
};