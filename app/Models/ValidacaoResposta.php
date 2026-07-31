<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ValidacaoResposta extends Model
{
    use HasUuids;

    protected $table = 'validacao_resposta';

    protected $fillable = [
        'id_resposta',
        'id_avaliador',
        'status',
        'correcao',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function resposta()
    {
        return $this->belongsTo(Resposta::class, 'id_resposta');
    }

    public function avaliador()
    {
        return $this->belongsTo(User::class, 'id_avaliador', 'uuid');
    }
}
