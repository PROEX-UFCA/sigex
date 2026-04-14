<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipe_Acao extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = "equipe_acao";
    protected $fillable = ['id_acao', 'id_usuario', 'categoria'];

    public function action() :HasOne{
        return $this->hasOne(Acao::class, 'id', 'id_acao');
    }

    public function user() :HasOne{
        return $this->hasOne(User::class, 'uuid', 'id_usuario');
    }
}
