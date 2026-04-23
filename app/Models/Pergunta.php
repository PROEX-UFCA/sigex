<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Pergunta extends Model
{
    use HasUuids;

    protected $table = 'pergunta';
    protected $fillable = ['id_secao', 'id_secao', 'tipo', 'enunciado', 'obrigatoria', 'min', 'max', 'step', 'accept', 'regex'];
}
