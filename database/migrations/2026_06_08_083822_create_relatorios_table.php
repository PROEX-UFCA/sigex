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
        Schema::create('relatorio', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_formulario');
            $table->foreign('id_formulario')->references('id')->on('formulario')->onDelete('cascade');
            $table->text('titulo');
            $table->datetime('data_inicio');
            $table->datetime('prazo');
            $table->boolean('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relatorio');
    }
};
