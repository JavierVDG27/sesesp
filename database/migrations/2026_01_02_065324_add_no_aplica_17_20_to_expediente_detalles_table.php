<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('expediente_detalles', function (Blueprint $table) {
            if (!Schema::hasColumn('expediente_detalles', 'no_aplica_17')) {
                $table->longText('no_aplica_17')->nullable()->after('no_aplica_16');
            }
            if (!Schema::hasColumn('expediente_detalles', 'no_aplica_18')) {
                $table->longText('no_aplica_18')->nullable()->after('no_aplica_17');
            }
            if (!Schema::hasColumn('expediente_detalles', 'no_aplica_19')) {
                $table->longText('no_aplica_19')->nullable()->after('no_aplica_18');
            }
            if (!Schema::hasColumn('expediente_detalles', 'no_aplica_20')) {
                $table->longText('no_aplica_20')->nullable()->after('no_aplica_19');
            }
        });
    }

    public function down(): void {
        Schema::table('expediente_detalles', function (Blueprint $table) {
            foreach (['no_aplica_17','no_aplica_18','no_aplica_19','no_aplica_20'] as $col) {
                if (Schema::hasColumn('expediente_detalles', $col)) $table->dropColumn($col);
            }
        });
    }
};
