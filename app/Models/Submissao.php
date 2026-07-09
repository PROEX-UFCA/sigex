<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submissao extends Model
{
    use HasUuids;

    protected $table = 'submissao';
    protected $fillable = ['id_relatorio', 'id_acao', 'finalizada_em'];

    public function relatorio() :BelongsTo{
        return $this->belongsTo(Relatorio::class, 'id_relatorio', 'id');
    }

    public function getProgressAttribute() : float{
        $submissao = $this->relatorio->formulario->secoes;

        $qtdPerguntas = 0;
        $qtdPerguntasRespondidas = 0;

        foreach($submissao as $index => $secao){
            foreach ($secao->perguntas as $pergunta){
                $qtdPerguntas++;

                $resposta = $pergunta->getRespostaPorSubmissao($this->id);

                if ($resposta && $resposta->valor !== null && $resposta->valor !== '') {
                    $qtdPerguntasRespondidas++; 
                }
            }
        }

        return $qtdPerguntas > 0 ? round(($qtdPerguntasRespondidas / $qtdPerguntas) * 100, 2) : 0;
    }
}
