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

    public function acao() :BelongsTo{
        return $this->belongsTo(Acao::class, 'id_acao', 'id');
    }

    public function getProgressAttribute() : float
    {
        $secoes = $this->relatorio->formulario->secoes;

        $qtdPerguntas = 0;
        $qtdPerguntasRespondidas = 0;

        foreach($secoes as $secao){
            
            foreach ($secao->perguntas->whereNull('id_pergunta_pai') as $pergunta){

                $qtdPerguntas++;

                if ($pergunta->tipo === 'tabela') {
                    $temRespostaNaTabela = \App\Models\Resposta::whereIn('id_pergunta', $pergunta->filhas->pluck('id'))
                        ->where('id_submissao', $this->id)
                        ->whereNotNull('valor')
                        ->where('valor', '!=', '')
                        ->exists();

                    if ($temRespostaNaTabela) {
                        $qtdPerguntasRespondidas++;
                    }

                } else {
                    $resposta = $pergunta->getRespostaPorSubmissao($this->id);

                    if ($resposta && $resposta->valor !== null && $resposta->valor !== '') {
                        $qtdPerguntasRespondidas++; 
                    }
                    
                }
            }
        }

        return $qtdPerguntas > 0 ? (float) round(($qtdPerguntasRespondidas / $qtdPerguntas) * 100, 2) : 0;
    }
}