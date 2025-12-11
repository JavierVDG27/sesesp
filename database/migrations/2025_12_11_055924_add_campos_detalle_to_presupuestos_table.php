<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presupuestos', function (Blueprint $table) {
            $table->string('capitulo', 10)->nullable()->after('expediente_id');
            $table->string('bien', 255)->nullable()->after('descripcion_concepto');
            $table->string('persona', 255)->nullable()->after('cantidad');
            $table->string('rlc', 10)->nullable()->after('persona');

            $table->decimal('fasp_federal', 15, 2)->nullable()->after('rlc');
            $table->decimal('fasp_municipal', 15, 2)->nullable()->after('fasp_federal');
            $table->decimal('fasp_subtotal', 15, 2)->nullable()->after('fasp_municipal');

            $table->decimal('est_estatal', 15, 2)->nullable()->after('fasp_subtotal');
            $table->decimal('est_municipal', 15, 2)->nullable()->after('est_estatal');
            $table->decimal('est_subtotal', 15, 2)->nullable()->after('est_municipal');

            $table->decimal('total_financiamiento', 15, 2)->nullable()->after('est_subtotal');
        });
    }

    public function down(): void
    {
        Schema::table('presupuestos', function (Blueprint $table) {
            $table->dropColumn([
                'capitulo',
                'bien',
                'persona',
                'rlc',
                'fasp_federal',
                'fasp_municipal',
                'fasp_subtotal',
                'est_estatal',
                'est_municipal',
                'est_subtotal',
                'total_financiamiento',
            ]);
        });
    }
};
