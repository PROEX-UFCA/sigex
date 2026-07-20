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
        if (Schema::hasTable('equipe_acao')) {
            return;
        }

        Schema::create('equipe_acao', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_acao');
            $table->foreign('id_acao')->references('id')->on('acao')->onDelete('cascade');
            $table->uuid('id_usuario');
            $table->foreign('id_usuario')->references('uuid')->on('users')->onDelete('cascade');
            $table->string('id_projeto')->nullable();
            $table->string('id_pessoa')->nullable();
            $table->string('tipo_membro')->nullable();
            $table->string('categoria_membro')->nullable();
            $table->string('status')->nullable();
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->string('tipo_vinculo')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipe_acao');
    }
};