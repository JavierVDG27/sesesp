<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expedientes', function (Blueprint $table) {
            $table->foreignId('institucion_id')
                ->nullable()
                ->after('user_id')
                ->constrained('instituciones')
                ->nullOnDelete();

            // opcional pero recomendado para dashboard
            $table->index(['anio_ejercicio','entidad','institucion_id']);
        });
    }

    public function down(): void
    {
        Schema::table('expedientes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('institucion_id');
            $table->dropIndex(['anio_ejercicio','entidad','institucion_id']);
        });
    }
};
