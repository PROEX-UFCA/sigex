<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Instituicao_Externa extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = ['nome', 'cnpj', 'cep', 'logradouro', 'numero', 'complemento', 'telefone_contato'];
}
