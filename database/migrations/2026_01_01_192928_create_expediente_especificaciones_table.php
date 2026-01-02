<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('expediente_entregables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained('expedientes')->cascadeOnDelete();

            $table->unsignedInteger('num')->default(1);
            $table->longText('descripcion')->nullable();
            $table->decimal('cantidad', 14, 2)->nullable();
            $table->string('fecha_entrega')->nullable();
            $table->string('responsable_validar')->nullable();
            $table->string('lugar_entrega')->nullable();

            $table->unsignedInteger('orden')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('expediente_entregables');
    }
};