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
        Schema::create('galeria_acao', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('id_acao')->constrained('acao')->cascadeOnDelete();
            $table->string('caminho_imagem');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galeria__acaos');
    }
};
