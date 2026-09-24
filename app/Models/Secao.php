<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Secao extends Model
{
    use HasUuids, SoftDeletes, LogsActivity;

    protected $table = 'secao';
    protected $fillable = ["id_formulario", 'titulo', 'descricao', 'ordem'];

    public function perguntas() : HasMany{
        return $this->hasMany(Pergunta::class, 'id_secao', 'id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(["id_formulario", 'titulo', 'descricao', 'ordem'])
            ->useLogName('sessao')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
