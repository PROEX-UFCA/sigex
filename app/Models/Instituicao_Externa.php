<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Instituicao_Externa extends Model
{
    use HasUuids, SoftDeletes, LogsActivity;

    protected $table = 'instituicao_externa';
    protected $fillable = ['nome', 'email', 'cnpj', 'cep', 'logradouro', 'numero', 'complemento', 'status', 'telefone_contato'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nome','email', 'cnpj', 'cep', 'logradouro', 'numero', 'complemento', 'telefone_contato', 'status'])
            ->useLogName('instituicao_externa')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
