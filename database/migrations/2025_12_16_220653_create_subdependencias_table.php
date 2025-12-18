<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subdependencias', function (Blueprint $table) {
            $table->id();

            $table->foreignId('institucion_id')
                ->constrained('instituciones')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('nombre');
            $table->string('siglas')->nullable();

            $table->timestamps();

            $table->unique(['institucion_id', 'nombre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subdependencias');
    }
};
