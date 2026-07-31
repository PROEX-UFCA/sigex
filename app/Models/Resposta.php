<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Resposta extends Model
{
    use HasUuids;
    
    protected $table = 'resposta'; 
    protected $fillable = ['id_submissao', 'id_pergunta', 'valor', 'indice_grupo'];

    public function validacao() :HasOne{
        return $this->hasOne(ValidacaoResposta::class, 'id_resposta', 'id');
    }
}
