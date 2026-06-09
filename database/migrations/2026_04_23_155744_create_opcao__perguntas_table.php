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
        if (Schema::hasTable('opcao_pergunta')) {
            return;
        }

        Schema::create('opcao_pergunta', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_pergunta');
            $table->foreign('id_pergunta')->references('id')->on('pergunta')->onDelete('cascade');
            $table->text('rotulo');
            $table->text('valor');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opcao_pergunta');
    }
};
