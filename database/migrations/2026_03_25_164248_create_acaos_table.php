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
        Schema::create('acao', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_proponente');
            $table->foreign('id_proponente')->references('uuid')->on('users')->onDelete('cascade');
            $table->string('id_projeto')->nullable();
            $table->text('titulo')->nullable();
            $table->text('palavras_chave')->nullable();
            $table->text('resumo')->nullable();
            $table->string('centro_departamento')->nullable();
            $table->string('com_bolsa')->nullable();
            $table->string('ods')->nullable();
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->datetime('data_atualizacao')->nullable();
            $table->year('ano')->nullable();
            $table->string('tipo_acao')->nullable();
            $table->string('area_tematica')->nullable();
            $table->string('modalidade')->nullable();
            $table->string('situacao')->nullable();
            $table->integer('status')->default(1);
            $table->text('img')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acao');
    }
};