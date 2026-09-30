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
        if (Schema::hasTable('formulario')) {
            return;
        }

        Schema::create('formulario', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('titulo');
            $table->text('descricao');
            $table->boolean('status')->default(0);
            $table->boolean('published')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formulario');
    }
};
