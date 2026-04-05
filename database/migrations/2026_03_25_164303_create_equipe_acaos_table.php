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
        Schema::create('equipe_acao', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_acao');
            $table->foreign('id_acao')->references('id')->on('acao')->onDelete('cascade');
            $table->uuid('id_usuario');
            $table->foreign('id_usuario')->references('uuid')->on('users')->onDelete('cascade');
            $table->string('categoria');
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
