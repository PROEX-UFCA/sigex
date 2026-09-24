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
        Schema::create('localidade_acao', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('id_projeto');
            $table->string('cidade')->nullable();
            $table->string('estado')->nullable();
            $table->string('sim')->nullable();
            $table->string('area_tematica')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('localidade_acao');
    }
};
