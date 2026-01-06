<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('expedientes', function (Blueprint $table) {
            $table->string('pdf_firmado_path')->nullable()->after('estatus');
            $table->unsignedBigInteger('pdf_firmado_usuario_id')->nullable()->after('pdf_firmado_path');
            $table->timestamp('pdf_firmado_at')->nullable()->after('pdf_firmado_usuario_id');
        });
    }

    public function down(): void
    {
        Schema::table('expedientes', function (Blueprint $table) {
            $table->dropColumn(['pdf_firmado_path','pdf_firmado_usuario_id','pdf_firmado_at']);
        });
    }
};
