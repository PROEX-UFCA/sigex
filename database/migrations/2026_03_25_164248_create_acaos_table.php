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
            $table->foreign('id_coordenador')->references('uuid')->on('users')->onDelete('cascade');
            $table->string('id_atividade')->nullable();
            $table->string('id_projeto')->nullable();
            $table->string('titulo')->nullable();
            $table->string('centro_departamento')->nullable();
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->year('ano')->nullable();
            $table->string('tipo_acao')->nullable();
            $table->string('area_tematica')->nullable();
            $table->string('modalidade')->nullable();
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
