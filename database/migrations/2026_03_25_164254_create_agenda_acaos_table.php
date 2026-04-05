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
        Schema::create('agenda_acao', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_acao');
            $table->foreign('id_acao')->references('id')->on('acao')->onDelete('cascade');
            $table->string('titulo_evento');
            $table->datetime('data_hora_inicio');
            $table->datetime('data_hora_fim');
            $table->string('local_formato');
            $table->string('descricao');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agenda_acao');
    }
};
