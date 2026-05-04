<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pergunta extends Model
{
    use HasUuids;

    protected $table = 'pergunta';
    protected $fillable = ['id_secao', 'tipo', 'enunciado', 'obrigatoria', 'min', 'max', 'step', 'accept', 'regex'];

    public function opcoes() : HasMany{
        return $this->hasMany(Opcao_Pergunta::class, 'id_pergunta', 'id');
    }
}
