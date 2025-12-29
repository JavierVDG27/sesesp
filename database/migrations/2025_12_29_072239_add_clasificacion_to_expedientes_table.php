<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expedientes', function (Blueprint $table) {
            $table->string('capitulo', 10)->nullable()->after('subprograma');
            $table->string('concepto', 10)->nullable()->after('capitulo');
            $table->string('partida_generica', 10)->nullable()->after('concepto');

            // recomendado para consultas
            $table->index(['anio_ejercicio','entidad','institucion_id','eje','programa','subprograma'], 'exp_eps_idx');
            $table->index(['capitulo','concepto','partida_generica'], 'exp_clasif_idx');
        });
    }

    public function down(): void
    {
        Schema::table('expedientes', function (Blueprint $table) {
            $table->dropIndex('exp_eps_idx');
            $table->dropIndex('exp_clasif_idx');

            $table->dropColumn(['capitulo','concepto','partida_generica']);
        });
    }
};
