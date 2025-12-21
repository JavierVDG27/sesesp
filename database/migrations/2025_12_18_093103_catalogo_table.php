<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('fasp_catalogo', function (Blueprint $table) {
      $table->id();

      $table->unsignedSmallInteger('year');
      $table->string('entidad', 10)->default('8300');

      $table->unsignedTinyInteger('nivel'); // 1..7
      $table->foreignId('parent_id')->nullable()->constrained('fasp_catalogo')->nullOnDelete();

      $table->string('eje', 5)->nullable();
      $table->string('programa', 5)->nullable();
      $table->string('subprograma', 5)->nullable();
      $table->string('capitulo', 10)->nullable();
      $table->string('concepto', 10)->nullable();
      $table->string('partida_generica', 10)->nullable();
      $table->string('bien', 20)->nullable();

      $table->string('nombre', 500)->nullable();

      // Montos CAPTURADOS (editables)
      $table->decimal('fed_federal', 16, 2)->default(0);
      $table->decimal('fed_municipal', 16, 2)->default(0);
      $table->decimal('est_estatal', 16, 2)->default(0);
      $table->decimal('est_municipal', 16, 2)->default(0);

      // Montos CALCULADOS (rollup desde hijos)
      $table->decimal('calc_fed_federal', 16, 2)->default(0);
      $table->decimal('calc_fed_municipal', 16, 2)->default(0);
      $table->decimal('calc_est_estatal', 16, 2)->default(0);
      $table->decimal('calc_est_municipal', 16, 2)->default(0);

      $table->string('unidad_medida', 50)->nullable();
      $table->decimal('cantidad', 16, 2)->nullable();
      $table->string('persona_cantidad2', 50)->nullable();
      $table->string('rlcf', 50)->nullable();

      $table->timestamps();

      // Único por ruta (evita duplicados por “escalera”)
      $table->unique([
        'year','entidad','nivel',
        'eje','programa','subprograma','capitulo','concepto','partida_generica','bien'
      ], 'fasp_ruta_unique');

      $table->index(['year','entidad','nivel']);
      $table->index(['parent_id']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('fasp_catalogo');
  }
};
