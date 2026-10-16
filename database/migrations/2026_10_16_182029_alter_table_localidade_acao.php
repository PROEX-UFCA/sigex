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
         Schema::table('localidade_acao', function (Blueprint $table) {
            // Remove a coluna desejada
            $table->dropColumn('sim');
            $table->dropColumn('area_tematica');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('localidade_acao', function (Blueprint $table) {
            // Recria a coluna caso a migration seja revertida
            $table->string('sim')->nullable();
            $table->string('area_tematica')->nullable();
        });
    }
};
