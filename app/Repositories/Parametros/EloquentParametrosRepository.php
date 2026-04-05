<?php

namespace App\Repositories\Parametros;

use App\Models\Parametro;

class EloquentParametrosRepository implements ParametrosRepository
{
    public function getAllActiveByFunctions($functions)
    {
        return Parametro::whereIn('function', $functions)->where('status', 1)->get();
    }
}
