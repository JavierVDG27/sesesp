<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('instituciones', function (Blueprint $table) {
            $table->unsignedInteger('orden')->default(0)->after('siglas');
            $table->unique('orden');
        });
    }

    public function down(): void
    {
        Schema::table('instituciones', function (Blueprint $table) {
            $table->dropUnique(['orden']);
            $table->dropColumn('orden');
        });
    }

};
