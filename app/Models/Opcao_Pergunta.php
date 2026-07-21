<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Opcao_Pergunta extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'opcao_pergunta';
    protected $fillable = ['id_pergunta', 'rotulo', 'valor'];
}
