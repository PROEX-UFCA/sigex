<?php

namespace App\Http\Controllers\Web\System;

use App\Http\Controllers\Controller;
use App\Repositories\Actions\ActionsRepository;
use App\Repositories\Parametros\ParametrosRepository;
use Illuminate\Http\Request;

class ActionController extends Controller
{
    private $data;
    private $actionsRepository;
    private $parametrosRepository;

    public function __construct(ActionsRepository $actionsRepository, ParametrosRepository $parametrosRepository)
    {
        $this->actionsRepository = $actionsRepository;
        $this->parametrosRepository = $parametrosRepository;
    }

    public function index(Request $request){

        $this->data['actions'] = $this->actionsRepository->getByFilter($request->query());
        $this->data['parametros'] = $this->parametrosRepository->getAllActiveByFunctions(['TIPO', 'MODALIDADE', 'CENTRO_DEPARTAMENTO', 'ÁREA_TEMÁTICA', ''])->groupBy('function');

        return view('pages.actions.index', $this->data);
    }
}
