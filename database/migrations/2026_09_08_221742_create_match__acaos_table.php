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
        Schema::create('match_acao', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('id_instituicao')->constrained('instituicao_externa')->cascadeOnDelete();
            $table->foreignUuid('id_acao')->constrained('acao')->cascadeOnDelete();

            $table->boolean('mutual')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_acao');
    }
};
