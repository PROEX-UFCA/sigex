<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Match_Acao extends Model
{
    use HasUuids;

    protected $table = 'match_acao';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_instituicao',
        'id_acao',
        'mutual',
        'concluida',
    ];

    public function instituicao()
    {
        return $this->belongsTo(Instituicao_Externa::class, 'id_instituicao', 'id');
    }

    public function acao()
    {
        return $this->belongsTo(Acao::class, 'id_acao', 'id');
    }
}