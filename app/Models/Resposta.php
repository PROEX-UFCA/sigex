<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Resposta extends Model
{
    use HasUuids;
    
    protected $table = 'resposta'; 
    protected $fillable = ['id_submissao', 'id_pergunta', 'valor', 'indice_grupo'];
}
