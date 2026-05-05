<?php

namespace App\Http\Controllers\Web\System;

use App\Http\Controllers\Controller;
use App\Repositories\Forms\FormsRepository;
use Illuminate\Http\Request;

class FormController extends Controller
{
    private $data = [];
    private $formsRepository;

    public function __construct(FormsRepository $formsRepository)
    {
        $this->formsRepository = $formsRepository;
    }

    public function index(Request $request){
        $this->data['forms'] = $this->formsRepository->getByFilter($request->query());
        return view('pages.forms.index', $this->data);
    }

    public function store(Request $request){
        try {
            $this->formsRepository->create($request);
            return redirect()->back()->with("success", "Formulário cadastrado com sucesso.");
        } catch (\Throwable $th) {
            return redirect()->back()->with("error", "Erro ao cadastrar formulário. Por favor, tente novamente mais tarde.")->withInput();
        }
    }

    public function storeQuestion(Request $request, $uuid){
        // dd($request->all());
        try {
            $this->formsRepository->createQuestion($request, $uuid);
            return redirect()->back()->with("success", "Formulário cadastrado com sucesso.");
        } catch (\Throwable $th) {
            return redirect()->back()->with("error", "Erro ao cadastrar formulário. Por favor, tente novamente mais tarde.")->withInput();
        }
    }
    
    public function update(Request $request, $uuid){
        try {
            $this->formsRepository->update($request, $uuid);
            return redirect()->back()->with("success", "Formulário atualizado com sucesso.");
        } catch (\Throwable $th) {
            return redirect()->back()->with("error", "Erro ao atualizar formulário. Por favor, tente novamente mais tarde.")->withInput();
        }
    }

    public function sessionUpdate(Request $request, $uuid){
        try {
            $this->formsRepository->updateSession($request, $uuid);
            return redirect()->back()->with("success", "Seção atualizado com sucesso.");
        } catch (\Throwable $th) {
            return redirect()->back()->with("error", "Erro ao atualizar seção. Por favor, tente novamente mais tarde.")->withInput();
        }
    }

    public function session($uuid){
        $this->data['session'] = $this->formsRepository->getSessionById($uuid);
        return view('pages.forms.session', $this->data);
    }

}
