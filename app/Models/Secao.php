<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Secao extends Model
{
    use HasUuids;

    protected $table = 'secao';
    protected $fillable = ["id_formulario", 'titulo', 'descricao', 'ordem'];

    public function perguntas() : HasMany{
        return $this->hasMany(Pergunta::class, 'id_secao', 'id');
    }
}
