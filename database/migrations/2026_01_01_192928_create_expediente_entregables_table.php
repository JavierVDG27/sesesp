<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('expediente_especificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained('expedientes')->cascadeOnDelete();

            $table->string('partida')->nullable(); // ej 515
            $table->string('titulo_producto')->nullable();
            $table->longText('descripcion_tecnica')->nullable();

            $table->decimal('cantidad', 14, 2)->default(0);
            $table->string('unidad_medida')->nullable();
            $table->decimal('precio_unitario', 14, 2)->default(0);
            $table->decimal('importe_sin_iva', 14, 2)->default(0); // calculado server

            $table->unsignedInteger('orden')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('expediente_especificaciones');
    }
};