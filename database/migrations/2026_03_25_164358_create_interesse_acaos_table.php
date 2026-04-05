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
        Schema::create('interesse_acao', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_instituicao');
            $table->foreign('id_instituicao')->references('id')->on('instituicao_externa')->onDelete('cascade');
            $table->uuid('id_acao');
            $table->foreign('id_acao')->references('id')->on('acao')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interesse_acao');
    }
};
