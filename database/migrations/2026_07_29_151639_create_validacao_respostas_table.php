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
        Schema::create('validacao_resposta', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_resposta');
            $table->foreign('id_resposta')->references('id')->on('resposta')->onDelete('cascade');
            $table->uuid('id_pergunta');
            $table->foreign('id_pergunta')->references('id')->on('pergunta')->onDelete('cascade');
            $table->uuid('id_avaliador');
            $table->foreign('id_avaliador')->references('uuid')->on('users')->onDelete('cascade');
            $table->boolean('status'); 
            $table->text('correcao')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validacao_resposta');
    }
};
