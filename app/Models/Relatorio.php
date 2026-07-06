<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Relatorio extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'relatorio';
    protected $fillable = ['id_formulario', 'titulo', 'data_inicio', 'prazo', 'status'];

    public function submissoes() :HasMany{
        return $this->hasMany(Submissao::class, 'id_relatorio', 'id');
    }

    public function formulario() :BelongsTo{
        return $this->belongsTo(Formulario::class, 'id_formulario', 'id');
    }
}