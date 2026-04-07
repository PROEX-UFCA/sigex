<?php

namespace App\Http\Controllers\Web\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Action\StoreRequest;
use App\Repositories\Actions\ActionsRepository;
use App\Repositories\Parametros\ParametrosRepository;
use App\Repositories\Settings\User\UsersRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ActionController extends Controller
{
    private $data;
    private $actionsRepository;
    private $parametrosRepository;
    private $usersRepository;

    public function __construct(ActionsRepository $actionsRepository, ParametrosRepository $parametrosRepository, UsersRepository $usersRepository)
    {
        $this->actionsRepository = $actionsRepository;
        $this->parametrosRepository = $parametrosRepository;
        $this->usersRepository = $usersRepository;
    }

    public function index(Request $request){

        $this->data['actions'] = $this->actionsRepository->getByFilter($request->query());
        $this->data['parametros'] = $this->parametrosRepository->getAllActiveByFunctions(['TIPO', 'MODALIDADE', 'CENTRO_DEPARTAMENTO', 'ÁREA_TEMÁTICA', ''])->groupBy('function');

        return view('pages.actions.index', $this->data);
    }

    public function create(){

        $this->data['parametros'] = $this->parametrosRepository->getAllActiveByFunctions(['TIPO', 'MODALIDADE', 'CENTRO_DEPARTAMENTO', 'ÁREA_TEMÁTICA', ''])->groupBy('function');
        $this->data['coordinators'] = $this->usersRepository->getForCoordinator();


        return view('pages.actions.create', $this->data);
    }

    public function store(StoreRequest $request){
        try {
            $this->actionsRepository->create($request);
            return redirect()->back()->with("success", "Ação cadastrada com sucesso.");
        } catch (\Throwable $th) {
            return redirect()->back()->with("error", "Erro ao cadastrar ação. Por favor, tente novamente mais tarde.")->withInput();
        }
    }

    public function my(){

        $this->data['actions'] = $this->actionsRepository->getAllByUuid(Auth::user()->uuid);

        return view('pages.actions.my', $this->data);
    }

    public function details($uuid){

        $this->data['action'] = $this->actionsRepository->getByUserUuid(Auth::user()->uuid, $uuid)->action;

        return view('pages.actions.details', $this->data);
    }
}
