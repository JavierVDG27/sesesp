<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('subdependencias', function (Blueprint $table) {
            $table->unsignedInteger('orden')->default(1)->after('institucion_id');
            $table->index(['institucion_id', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::table('subdependencias', function (Blueprint $table) {
            $table->dropIndex(['institucion_id', 'orden']);
            $table->dropColumn('orden');
        });
    }
};
