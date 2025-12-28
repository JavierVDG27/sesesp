<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fasp_asignaciones_institucion', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('year');
            $table->string('entidad', 10)->default('8300');

            // 2=programa, 3=subprograma (nosotros usaremos 3 como estándar)
            $table->unsignedTinyInteger('nivel')->default(3);

            $table->string('eje', 10);
            $table->string('programa', 10)->nullable();
            $table->string('subprograma', 10)->nullable();

            $table->foreignId('institucion_id')->constrained('instituciones')->cascadeOnDelete();
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete(); // validador

            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique([
                'year','entidad','nivel',
                'eje','programa','subprograma',
                'institucion_id'
            ], 'uq_fasp_asig_inst');

            $table->index(['year','entidad','institucion_id','active'], 'idx_fasp_asig_inst_main');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fasp_asignaciones_institucion');
    }
};
