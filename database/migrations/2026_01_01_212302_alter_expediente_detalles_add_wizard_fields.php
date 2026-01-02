<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('expediente_detalles', function (Blueprint $table) {

            // === Portada / textos superiores ===
            if (!Schema::hasColumn('expediente_detalles', 'titulo_documento')) {
                $table->string('titulo_documento')->nullable()->after('expediente_id');
            }
            if (!Schema::hasColumn('expediente_detalles', 'subtitulo_documento')) {
                $table->string('subtitulo_documento')->nullable()->after('titulo_documento');
            }
            if (!Schema::hasColumn('expediente_detalles', 'fasp_texto')) {
                $table->string('fasp_texto', 500)->nullable()->after('subtitulo_documento');
            }
            if (!Schema::hasColumn('expediente_detalles', 'ejercicio_fiscal_label')) {
                $table->string('ejercicio_fiscal_label')->nullable()->after('fasp_texto');
            }
            if (!Schema::hasColumn('expediente_detalles', 'logo_path')) {
                $table->string('logo_path')->nullable()->after('ejercicio_fiscal_label');
            }

            // === Overrides texto (si capturista necesita editar el texto) ===
            if (!Schema::hasColumn('expediente_detalles', 'eje_override')) {
                $table->string('eje_override')->nullable()->after('logo_path');
            }
            if (!Schema::hasColumn('expediente_detalles', 'programa_override')) {
                $table->string('programa_override')->nullable()->after('eje_override');
            }
            if (!Schema::hasColumn('expediente_detalles', 'subprograma_override')) {
                $table->string('subprograma_override')->nullable()->after('programa_override');
            }

            // === Marco legal en JSON (repeater) ===
            if (!Schema::hasColumn('expediente_detalles', 'marco_legal_json')) {
                $table->longText('marco_legal_json')->nullable()->after('marco_legal');
            }
        });
    }

    public function down(): void
    {
        Schema::table('expediente_detalles', function (Blueprint $table) {
            foreach ([
                'titulo_documento','subtitulo_documento','fasp_texto','ejercicio_fiscal_label','logo_path',
                'eje_override','programa_override','subprograma_override','marco_legal_json'
            ] as $col) {
                if (Schema::hasColumn('expediente_detalles', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
