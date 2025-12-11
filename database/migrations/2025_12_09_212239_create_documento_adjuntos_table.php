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
        Schema::create('documentos_adjuntos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained('expedientes')->onDelete('cascade');
            $table->string('nombre_archivo');
            $table->string('ruta_almacenamiento');
            $table->string('tipo_mime')->nullable();
            $table->unsignedBigInteger('tamano_bytes')->nullable();
            $table->unsignedBigInteger('usuario_carga_id')->nullable();
            $table->timestamps();

            $table->foreign('usuario_carga_id')->references('id')->on('users');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documento_adjuntos');
    }
};
