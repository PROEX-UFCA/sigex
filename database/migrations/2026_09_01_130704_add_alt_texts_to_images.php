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
        Schema::table('acao', function (Blueprint $table) {
            $table->string('alt_capa')->nullable();
        });

        Schema::table('galeria_acao', function (Blueprint $table) {
            $table->string('texto_alternativo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('acao', function (Blueprint $table) {
            $table->dropColumn('alt_capa');
        });

        Schema::table('galeria_acao', function (Blueprint $table) {
            $table->dropColumn('texto_alternativo');
        });
    }
};
