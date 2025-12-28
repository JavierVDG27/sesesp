<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fasp_asignaciones', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('year');
            $table->string('entidad', 10)->default('8300');

            // 2 = Programa, 3 = Subprograma (nosotros usaremos 3 como estándar)
            $table->unsignedTinyInteger('nivel')->default(3);

            $table->string('eje', 10);
            $table->string('programa', 10)->nullable();
            $table->string('subprograma', 10)->nullable();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // capturista
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete(); // validador

            $table->boolean('active')->default(true);

            $table->timestamps();

            // Evita duplicados
            $table->unique([
                'year', 'entidad', 'nivel',
                'eje', 'programa', 'subprograma',
                'user_id'
            ], 'uq_fasp_asig');

            // Índices para filtrar rápido
            $table->index(['year', 'entidad', 'user_id', 'active'], 'idx_fasp_asig_user');
            $table->index(['year', 'entidad', 'eje', 'programa', 'subprograma'], 'idx_fasp_asig_cat');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fasp_asignaciones');
    }
};
