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
            $table->uuid('id_coordenador');
            $table->string('id_atividade');
            $table->string('id_projeto');
            $table->string('titulo');
            $table->string('centro_departamento');
            $table->date('data_inicio');
            $table->date('data_fim');
            $table->year('ano');
            $table->string('tipo_acao');
            $table->string('area_tematica');
            $table->string('modalidade');
            $table->integer('status')->default(1);
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
