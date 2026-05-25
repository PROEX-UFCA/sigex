<?php

namespace App\Repositories\Forms;

use App\Models\Formulario;
use App\Models\Opcao_Pergunta;
use App\Models\Pergunta;
use App\Models\Secao;

class EloquentFormsRepository implements FormsRepository
{
    public function getByFilter(array $filtros = [], string $sort = 'desc', string $direction = 'desc')
    {
        $query = Formulario::query();

        $query->when($filtros['search'] ?? null, function ($q, $search) {
            $q->where(function ($subQuery) use ($search) {
                $subQuery->where('titulo', 'like', "%{$search}%");
            });
        });

        return $query->orderBy($sort, $direction)->paginate(30);
    }

    public function create($request)
    {
        $form = Formulario::create([
            'titulo' => $request->titulo, 
            'descricao' => $request->descricao
        ]);

        for ($i=1; $i <= $request->numero_secoes; $i++) { 
            Secao::create([
                "id_formulario" => $form->id,
                'titulo' => "Seção ".$i,
                'descricao' => "Descrição da seção ".$i,
                'ordem' => $i
            ]);
        }

        return $form;
    }

    public function createQuestion($request, $uuid)
    {
        $pergunta = Pergunta::create([
            'id_secao' => $uuid, 
            'tipo' => $request->tipo, 
            'enunciado' => $request->enunciado, 
            'obrigatoria' => $request->obrigatoria, 
            'min' => $request->min ?? null, 
            'max' => $request->max ?? null, 
            'step' => $request->step ?? null, 
            'accept' => $request->has('accept') ? implode(',', $request->accept) : null, 
            'regex' => $request->regex ?? null  
        ]);

        if(isset($request->opcoes)){
            foreach ($request->opcoes as $value) {
                Opcao_Pergunta::create([
                    'id_pergunta' => $pergunta->id, 
                    'rotulo' => $value, 
                    'valor' => $value
                ]);
            }
        }

        return $pergunta;
    }

    public function update($request, $uuid)
    {
        $form = Formulario::findOrFail($uuid);

        $form->titulo = $request->titulo;
        $form->descricao = $request->descricao;
        $form->status = $request->status;
        if ($form->published == 0) {
            $form->published = $request->status;
        }
        
        $form->save();
        
        return $form;
    }

    public function updateSession($request, $uuid)
    {
        $session = Secao::findOrFail($uuid);

        $session->titulo = $request->titulo;
        $session->descricao = $request->descricao;
        $session->ordem = $request->ordem;
        $session->save();
        
        return $session;
    }

    public function getSessionById($uuid){
        return Secao::findOrFail($uuid);
    }

    public function getFormById($uuid){
        return Formulario::findOrFail($uuid);
    }

    public function deleteSessions($uuid){
        Secao::findOrFail($uuid)->delete();   
    }

    public function deleteQuestion($uuid){
        Pergunta::findOrFail($uuid)->delete();   
    }
  
    public function storeSessions($request, $uuid){
        
        $form = Formulario::findOrFail($uuid);
        $count = 1;

        if($form && $form->secoes->count() > 0){
            $count = $count + $form->secoes->last()->ordem;
        }

        return Secao::create([
            "id_formulario" => $uuid, 
            'titulo' => $request->titulo, 
            'descricao' => $request->titulo, 
            'ordem' => $count
        ]);
    }
}
