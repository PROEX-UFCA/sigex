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
        Schema::create('submissao', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_relatorio');
            $table->foreign('id_relatorio')->references('id')->on('relatorio')->onDelete('cascade');
            $table->uuid('id_acao')->nullable();
            $table->foreign('id_acao')->references('id')->on('acao')->onDelete('cascade');
            $table->uuid('id_user')->nullable();
            $table->foreign('id_user')->references('uuid')->on('users')->onDelete('cascade');
            $table->datetime('finalizada_em')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissao');
    }
};
