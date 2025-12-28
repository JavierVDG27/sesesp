<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fasp_distribucion_locks', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('year');
            $table->string('entidad', 10)->default('8300');
            $table->unsignedTinyInteger('nivel')->default(3);

            $table->string('eje', 10);
            $table->string('programa', 10)->nullable();
            $table->string('subprograma', 10)->nullable();

            $table->timestamp('locked_at')->nullable();
            $table->foreignId('locked_by')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->unique(['year','entidad','nivel','eje','programa','subprograma'], 'uq_fasp_dist_lock');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fasp_distribucion_locks');
    }
};
