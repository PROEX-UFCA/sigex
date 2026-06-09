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
        if (Schema::hasTable('pergunta')) {
            return;
        }

        Schema::create('pergunta', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_secao');
            $table->foreign('id_secao')->references('id')->on('secao')->onDelete('cascade');
            $table->string('tipo');
            $table->text('enunciado');
            $table->boolean('obrigatoria');
            $table->double('min')->nullable();
            $table->double('max')->nullable();
            $table->double('step')->nullable();
            $table->string('accept')->nullable();
            $table->string('regex')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pergunta');
    }
};
