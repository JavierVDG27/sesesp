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
        Schema::create('historial_expedientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained('expedientes')->onDelete('cascade');
            $table->unsignedBigInteger('usuario_id');
            $table->string('tipo_cambio'); // Creación, Actualización, Cambio de estatus, etc.
            $table->text('descripcion_cambio')->nullable();
            $table->string('estatus_anterior')->nullable();
            $table->string('estatus_nuevo')->nullable();
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('users');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_expedientes');
    }
};
