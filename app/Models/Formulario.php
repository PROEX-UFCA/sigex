<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Formulario extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'formulario';
    protected $fillable = ['titulo', 'descricao', 'status', 'published'];

    public function secoes() : HasMany{
        return $this->hasMany(Secao::class, 'id_formulario', 'id');
    }
}
