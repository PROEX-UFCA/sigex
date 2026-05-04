<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Opcao_Pergunta extends Model
{
    use HasUuids;

    protected $table = 'opcao_pergunta';
    protected $fillable = ['id_pergunta', 'rotulo', 'valor'];
}
