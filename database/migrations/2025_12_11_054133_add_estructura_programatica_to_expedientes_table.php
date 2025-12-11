<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expedientes', function (Blueprint $table) {
            $table->string('entidad', 10)->default('8300')->after('anio_ejercicio');
            $table->string('eje', 5)->nullable()->after('entidad');          // 01..05
            $table->string('programa', 5)->nullable()->after('eje');         // 01..14
            $table->string('subprograma', 5)->nullable()->after('programa'); // 01..33
            $table->string('tema', 255)->nullable()->after('subprograma');
            $table->string('area_ejecutora', 5)->nullable()->after('tema');  // 01..06
        });
    }

    public function down(): void
    {
        Schema::table('expedientes', function (Blueprint $table) {
            $table->dropColumn([
                'entidad',
                'eje',
                'programa',
                'subprograma',
                'tema',
                'area_ejecutora',
            ]);
        });
    }
};
