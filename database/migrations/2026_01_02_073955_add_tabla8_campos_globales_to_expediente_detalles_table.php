<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('expediente_detalles', function (Blueprint $table) {
            if (!Schema::hasColumn('expediente_detalles', 'tabla8_fecha_entrega')) {
                $table->string('tabla8_fecha_entrega', 255)->nullable()->after('no_aplica_20');
            }
            if (!Schema::hasColumn('expediente_detalles', 'tabla8_responsable_validar')) {
                $table->string('tabla8_responsable_validar', 255)->nullable()->after('tabla8_fecha_entrega');
            }
            if (!Schema::hasColumn('expediente_detalles', 'tabla8_lugar_entrega')) {
                $table->string('tabla8_lugar_entrega', 255)->nullable()->after('tabla8_responsable_validar');
            }
        });
    }

    public function down(): void {
        Schema::table('expediente_detalles', function (Blueprint $table) {
            foreach (['tabla8_fecha_entrega','tabla8_responsable_validar','tabla8_lugar_entrega'] as $col) {
                if (Schema::hasColumn('expediente_detalles', $col)) $table->dropColumn($col);
            }
        });
    }
};
