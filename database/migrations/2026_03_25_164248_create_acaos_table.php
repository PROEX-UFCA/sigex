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
        if (Schema::hasTable('acao')) {
            return;
        }

        Schema::create('acao', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('id_projeto')->nullable();
            $table->string('ano')->nullable();
            $table->text('titulo')->nullable();
            $table->string('modalidade_edital')->nullable();
            $table->string('bolsas_solicitadas')->nullable();
            $table->string('bolsas_concedidas')->nullable();
            $table->string('financiamento_interno')->nullable();
            $table->string('financiamento_externo')->nullable();
            $table->string('situacao')->nullable();
            $table->date('data_cadastro')->nullable();
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->date('data_atualizacao')->nullable();
            $table->string('centro_departamento_sigla')->nullable();
            $table->string('tipo_acao')->nullable();
            $table->string('area_tematica')->nullable();
            $table->text('resumo')->nullable();
            $table->text('palavras_chave')->nullable();
            $table->string('ods')->nullable();
            $table->string('contexto')->nullable();
            $table->integer('status')->default(1);
            $table->text('img')->nullable();
            $table->boolean('is_ej')->default(0);
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