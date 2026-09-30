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
        Schema::create('agenda_interna_acao', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('id_acao')->constrained('acao')->cascadeOnDelete();
            
            // igual agenda_acao
            $table->string('titulo_evento');
            $table->dateTime('data_hora_inicio');
            $table->dateTime('data_hora_fim');
            $table->string('local_formato');
            $table->string('descricao');
            
            $table->text('pauta_interna')->nullable(); 

            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agenda_interna_acao');
    }
};
