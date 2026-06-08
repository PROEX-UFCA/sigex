<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Submissao extends Model
{
    use HasUuids;

    protected $table = 'submissao';
    protected $fillable = ['id_relatorio', 'id_acao', 'id_user', 'finalizada_em'];
}
