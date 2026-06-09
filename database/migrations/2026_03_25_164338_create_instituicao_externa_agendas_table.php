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
        if (Schema::hasTable('instituicao_externa_agenda')) {
            return;
        }

        Schema::create('instituicao_externa_agenda', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_instituicao');
            $table->foreign('id_instituicao')->references('id')->on('instituicao_externa')->onDelete('cascade');
            $table->date('data_disponivel');
            $table->time('hora_inicio');
            $table->time('hora_fim');
            $table->string('observacao');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instituicao_externa_agenda');
    }
};
