<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('historial_modificaciones', function (Blueprint $table) {

            // Si no existen, se agregan
            if (!Schema::hasColumn('historial_modificaciones', 'expediente_id')) {
                $table->foreignId('expediente_id')
                    ->after('id')
                    ->constrained('expedientes')
                    ->cascadeOnDelete();
            }

            if (!Schema::hasColumn('historial_modificaciones', 'usuario_id')) {
                $table->foreignId('usuario_id')
                    ->after('expediente_id')
                    ->constrained('users')
                    ->cascadeOnDelete();
            }

            if (!Schema::hasColumn('historial_modificaciones', 'estado_anterior')) {
                $table->string('estado_anterior')->nullable()->after('usuario_id');
            }

            if (!Schema::hasColumn('historial_modificaciones', 'estado_nuevo')) {
                $table->string('estado_nuevo')->after('estado_anterior');
            }

            if (!Schema::hasColumn('historial_modificaciones', 'observaciones')) {
                $table->text('observaciones')->nullable()->after('estado_nuevo');
            }

            $table->index(['expediente_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('historial_modificaciones', function (Blueprint $table) {
            if (Schema::hasColumn('historial_modificaciones', 'expediente_id')) {
                $table->dropForeign(['expediente_id']);
                $table->dropColumn('expediente_id');
            }

            if (Schema::hasColumn('historial_modificaciones', 'usuario_id')) {
                $table->dropForeign(['usuario_id']);
                $table->dropColumn('usuario_id');
            }

            if (Schema::hasColumn('historial_modificaciones', 'estado_anterior')) {
                $table->dropColumn('estado_anterior');
            }

            if (Schema::hasColumn('historial_modificaciones', 'estado_nuevo')) {
                $table->dropColumn('estado_nuevo');
            }

            if (Schema::hasColumn('historial_modificaciones', 'observaciones')) {
                $table->dropColumn('observaciones');
            }
        });
    }
};
