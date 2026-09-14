<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Galeria_Acao extends Model
{
    use HasUuids, SoftDeletes, LogsActivity;

    protected $table = 'galeria_acao';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_acao',
        'caminho_imagem',
        'texto_alternativo',
    ];

    public function acao()
    {
        return $this->belongsTo(Acao::class, 'id_acao', 'id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['id_acao', 'caminho_imagem', 'texto_alternativo'])
            ->useLogName('galeria_acao')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}