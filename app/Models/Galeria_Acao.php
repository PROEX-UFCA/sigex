<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Galeria_Acao extends Model
{
    use HasUuids;

    protected $table = 'galeria_acao';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_acao',
        'caminho_imagem',
    ];

    public function acao()
    {
        return $this->belongsTo(Acao::class, 'id_acao', 'id');
    }
}