<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subdependencias', function (Blueprint $table) {

            if (!Schema::hasColumn('subdependencias', 'nombre')) {
                $table->string('nombre')->after('id');
            }

            if (!Schema::hasColumn('subdependencias', 'institucion_id')) {
                $table->foreignId('institucion_id')
                    ->after('nombre')
                    ->constrained('instituciones')
                    ->cascadeOnUpdate()
                    ->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('subdependencias', function (Blueprint $table) {

            if (Schema::hasColumn('subdependencias', 'institucion_id')) {
                $table->dropForeign(['institucion_id']);
                $table->dropColumn('institucion_id');
            }

            if (Schema::hasColumn('subdependencias', 'nombre')) {
                $table->dropColumn('nombre');
            }
        });
    }
};
