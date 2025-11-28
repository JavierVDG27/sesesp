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
        Schema::table('users', function (Blueprint $table) {

            // Agregar campo y llave foránea
            $table->foreignId('institucion_id')
                ->nullable()
                ->constrained('instituciones')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Primero se elimina la llave foránea
            $table->dropForeign(['institucion_id']);

            // Luego se elimina la columna
            $table->dropColumn('institucion_id');
        });
    }
};
