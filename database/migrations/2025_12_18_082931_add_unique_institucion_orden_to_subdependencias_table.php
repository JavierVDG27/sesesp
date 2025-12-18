<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('subdependencias', function (Blueprint $table) {
            // índice único por institución + orden
            $table->unique(['institucion_id', 'orden'], 'subdep_inst_orden_unique');
        });
    }

    public function down(): void
    {
        Schema::table('subdependencias', function (Blueprint $table) {
            $table->dropUnique('subdep_inst_orden_unique');
        });
    }
};
