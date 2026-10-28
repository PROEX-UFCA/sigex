<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sincronizacao', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->string('etapa_atual');
            $table->datetime('iniciado_em')->nullable();
            $table->datetime('finalizado_em')->nullable();
            $table->string('resultados');
            $table->text('erro')->nullable();	
            $table->uuid('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sincronizacao');
    }
};
