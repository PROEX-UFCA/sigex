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
        Schema::table('match_acao', function (Blueprint $table) {
            $table->string('solicitante')->default('instituicao')->after('id_acao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('match_acao', function (Blueprint $table) {
            $table->dropColumn('solicitante');
        });
    }
};
