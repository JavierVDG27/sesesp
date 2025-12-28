<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fasp_distribuciones', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('year');
            $table->string('entidad', 10)->default('8300');
            $table->unsignedTinyInteger('nivel')->default(3);

            $table->string('eje', 10);
            $table->string('programa', 10)->nullable();
            $table->string('subprograma', 10)->nullable();

            $table->enum('fuente', ['fed_federal','fed_municipal','est_estatal','est_municipal']);
            $table->string('descripcion', 255)->nullable();

            $table->foreignId('institucion_id')->nullable()
                ->constrained('instituciones')->nullOnDelete();

            $table->decimal('monto', 14, 2)->default(0);

            $table->foreignId('created_by')
                ->constrained('users')->cascadeOnDelete();

            $table->timestamps();

            $table->index(['year','entidad','nivel','eje','programa','subprograma'], 'idx_fasp_dist_key');
            $table->index(['year','entidad','fuente'], 'idx_fasp_dist_fuente');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fasp_distribuciones');
    }
};