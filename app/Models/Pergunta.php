<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pergunta extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'pergunta';
    protected $fillable = ['id_secao', 'tipo', 'enunciado', 'obrigatoria', 'min', 'max', 'step', 'accept', 'regex'];

    public function opcoes() : HasMany{
        return $this->hasMany(Opcao_Pergunta::class, 'id_pergunta', 'id');
    }

    public function respostas() {
        return $this->hasMany(Resposta::class, 'id_pergunta', 'id');
    }

    public function getRespostaPorSubmissao($id_submissao) {
        return $this->respostas()->where('id_submissao', $id_submissao)->first();
    }
}
