<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('expediente_estructura_programatica', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained('expedientes')->cascadeOnDelete();

            // Manual (transcripción presupuestal)
            $table->string('programa')->nullable();
            $table->string('subprograma')->nullable();
            $table->string('partida_bien_servicio')->nullable();
            $table->decimal('costo', 14, 2)->nullable();
            $table->string('unidad_medida')->nullable();
            $table->decimal('meta_cantidad', 14, 2)->nullable();
            $table->string('aportacion')->nullable();

            $table->unsignedInteger('orden')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('expediente_estructura_programatica');
    }
};