<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expediente_detalles', function (Blueprint $table) {
            $table->unsignedSmallInteger('anio_override')->nullable()->after('ejercicio_fiscal_label');
        });
    }

    public function down(): void
    {
        Schema::table('expediente_detalles', function (Blueprint $table) {
            $table->dropColumn('anio_override');
        });
    }

};
