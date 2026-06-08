<?php

namespace App\Http\Controllers\Web\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Form\StoreFormRequest;
use App\Http\Requests\Web\Form\StoreQuestionRequest;
use App\Http\Requests\Web\Form\StoreSessionRequest;
use App\Http\Requests\Web\Form\UpdateFormRequest;
use App\Http\Requests\Web\Form\UpdateSessionRequest;
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

        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('dir', 'desc') === 'asc' ? 'asc' : 'desc';

        $allowedFields = ['titulo', 'status', 'created_at'];

        if (!in_array($sort, $allowedFields)) $sort = 'created_at';

        $this->data['forms'] = $this->formsRepository->getByFilter($request->query(), $sort, $direction);
        return view('pages.forms.index', $this->data);
    }

    public function store(StoreFormRequest $request){
        try {
            $form = $this->formsRepository->create($request);
            return to_route('sessions.index', $form->secoes->first()->id)->with("success", "Formulário cadastrado com sucesso.");
        } catch (\Throwable $th) {
            return redirect()->back()->with("error", "Erro ao cadastrar formulário. Por favor, tente novamente mais tarde.")->withInput();
        }
    }

    public function storeQuestion(StoreQuestionRequest $request, $uuid){
        try {
            if(!$this->verify($uuid, 2)){
                return redirect()->back()->with("error", "Não é possível mais adicionar perguntas pois o formulário já foi publicado.")->withInput();
            }

            $this->formsRepository->createQuestion($request, $uuid);
            return redirect()->back()->with("success", "Pergunta cadastrada com sucesso.");
        } catch (\Throwable $th) {
            return redirect()->back()->with("error", "Erro ao cadastrar pergunta. Por favor, tente novamente mais tarde.")->withInput();
        }
    }
    
    public function update(UpdateFormRequest $request, $uuid){
        try {
            $this->formsRepository->update($request, $uuid);
            return redirect()->back()->with("success", "Formulário atualizado com sucesso.");
        } catch (\Throwable $th) {
            return redirect()->back()->with("error", "Erro ao atualizar formulário. Por favor, tente novamente mais tarde.")->withInput();
        }
    }

    public function sessionUpdate(UpdateSessionRequest $request, $uuid){
        try {
            if(!$this->verify($uuid, 2)){
                return redirect()->back()->with("error", "Não é possível mais editar seções pois o formulário já foi publicado.")->withInput();
            }

            $this->formsRepository->updateSession($request, $uuid);
            return redirect()->back()->with("success", "Seção atualizado com sucesso.");
        } catch (\Throwable $th) {
            return redirect()->back()->with("error", "Erro ao atualizar seção. Por favor, tente novamente mais tarde.")->withInput();
        }
    }

    public function session($uuid){
        $this->data['session'] = $this->formsRepository->getSessionById($uuid);
        $this->data['form'] = $this->formsRepository->getFormById($this->data['session']->id_formulario);
        return view('pages.forms.session', $this->data);
    }

    public function sessionDelete($uuid){
        try {
            if(!$this->verify($uuid, 2)){
                return redirect()->back()->with("error", "Não é possível mais deletar seções pois o formulário já foi publicado.")->withInput();
            }

            $session = $this->formsRepository->getSessionById($uuid);
            $form = $this->formsRepository->getFormById($session->id_formulario);
            $this->formsRepository->deleteSessions($uuid);

            if($form->secoes->count() > 0){
                return to_route('sessions.index', $form->secoes->first()->id)->with("success", "Seção deletada com sucesso.");
            }
            else{
                return to_route('forms.index')->with("success", "Todas as seções foram deletadas com sucesso.");
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with("error", "Erro ao deletar seção. Por favor, tente novamente mais tarde.")->withInput();
        }
    }

    public function deleteQuestion($uuid){
        try {
            if(!$this->verify($uuid, 3)){
                return redirect()->back()->with("error", "Não é possível mais deletar perguntas pois o formulário já foi publicado.")->withInput();
            }

            $this->formsRepository->deleteQuestion($uuid);

            return redirect()->back()->with("success", "Pergunta deletada com sucesso.");
        } catch (\Throwable $th) {
            return redirect()->back()->with("error", "Erro ao deletar pergunta. Por favor, tente novamente mais tarde.")->withInput();
        }
    }

    public function sessionStore(StoreSessionRequest $request, $uuid){
        try {
            if(!$this->verify($uuid, 1)){
                return redirect()->back()->with("error", "Não é possível mais adicionar seções pois o formulário já foi publicado.")->withInput();
            }

            $this->formsRepository->storeSessions($request, $uuid);
            return redirect()->back()->with("success", "Seção adicionada com sucesso.");
        } catch (\Throwable $th) {
            return redirect()->back()->with("error", "Erro ao adicionar seção. Por favor, tente novamente mais tarde.")->withInput();
        }
    }

    public function destroy(Request $request, $uuid) {
        try {
            if(!$this->verify($uuid, 1)){
                return redirect()->back()->with("error", "Não é possível mais deletar pois o formulário já foi publicado.")->withInput();
            }

            $this->formsRepository->destroy($uuid);
            return to_route('forms.index')->with('success', 'Formulário deletado com sucesso.');
        } catch (\Throwable $err) {
            return redirect()->back()->with('error', 'Erro ao deletar formulário. Por favor, tente novamente mais tarde.');
        }
    }

    public function verify($uuid, $type) {
        try {
            if($type == 1){
                $form = $this->formsRepository->getFormById($uuid);
    
                if($form->published == 1){
                    return false;
                }
            }
            if($type == 2){
                $form_uuid = $this->formsRepository->getSessionById($uuid)->id_formulario;
                $form = $this->formsRepository->getFormById($form_uuid);
    
                if($form->published == 1){
                    return false;
                }
            }
            if($type == 3){
                $session_uuid = $this->formsRepository->getQuestionById($uuid)->id_secao;
                $form_uuid = $this->formsRepository->getSessionById($session_uuid)->id_formulario;
                $form = $this->formsRepository->getFormById($form_uuid);
    
                if($form->published == 1){
                    return false;
                }
            }
            return true;
        } catch (\Throwable $err) {
            return true;
        }
    }
}
