<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'curp')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('curp', 18)->nullable()->after('apellido_materno');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'curp')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('curp');
            });
        }
    }

};