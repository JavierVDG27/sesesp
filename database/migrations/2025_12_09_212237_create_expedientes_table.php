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
        Schema::create('expedientes', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->unique();
            $table->string('nombre_proyecto');
            $table->string('dependencia');
            $table->string('tipo_recurso'); // Federal / Estatal / Mixto
            $table->integer('anio_ejercicio');
            $table->string('estatus')->default('borrador'); 
            $table->unsignedBigInteger('user_id'); // capturista que creó el expediente
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expedientes');
    }
};
