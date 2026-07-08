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
        Schema::create('resposta', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_submissao');
            $table->foreign('id_submissao')->references('id')->on('submissao')->onDelete('cascade');
            $table->uuid('id_pergunta');
            $table->foreign('id_pergunta')->references('id')->on('pergunta')->onDelete('cascade');
            $table->text('valor');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resposta');
    }
};
