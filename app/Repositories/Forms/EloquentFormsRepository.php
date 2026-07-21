<?php

namespace App\Repositories\Forms;

use App\Models\Formulario;
use App\Models\Opcao_Pergunta;
use App\Models\Pergunta;
use App\Models\Secao;
use Illuminate\Support\Facades\DB;

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

    public function getAllActive(){
        return Formulario::where('status', 1)->get();
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
        return DB::transaction(function () use ($request, $uuid) { 
            $pergunta = Pergunta::create([
                'id_secao' => $uuid, 
                'id_pergunta_pai' => null,
                'tipo' => $request->tipo, 
                'enunciado' => $request->enunciado, 
                'obrigatoria' => $request->obrigatoria, 
                'min' => $request->min ?? null, 
                'max' => $request->max ?? null, 
                'step' => $request->step ?? null, 
                'accept' => $request->has('accept') ? implode(',', $request->accept) : null, 
                'regex' => $request->regex ?? null  
            ]);

            if ($request->tipo !== 'tabela' && isset($request->opcoes)) {
                foreach ($request->opcoes as $value) {
                    Opcao_Pergunta::create([
                        'id_pergunta' => $pergunta->id, 
                        'rotulo' => $value, 
                        'valor' => $value
                    ]);
                }
            }

            if ($request->tipo === 'tabela' && $request->has('colunas')) {
                foreach ($request->colunas as $coluna) {
                    
                    $subPergunta = Pergunta::create([
                        'id_secao' => $uuid, 
                        'id_pergunta_pai' => $pergunta->id,
                        'tipo' => $coluna['tipo'], 
                        'enunciado' => $coluna['enunciado'], 
                        'obrigatoria' => $coluna['obrigatoria'], 
                        'min' => $coluna['min'] ?? null, 
                        'max' => $coluna['max'] ?? null, 
                        'step' => $coluna['step'] ?? null, 
                        'accept' => isset($coluna['accept']) ? implode(',', $coluna['accept']) : null, 
                        'regex' => $coluna['regex'] ?? null  
                    ]);

                    if (isset($coluna['opcoes'])) {
                        foreach ($coluna['opcoes'] as $opcaoValue) {
                            Opcao_Pergunta::create([
                                'id_pergunta' => $subPergunta->id, 
                                'rotulo' => $opcaoValue, 
                                'valor' => $opcaoValue
                            ]);
                        }
                    }
                }
            }

            return $pergunta;
        });
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

    public function getQuestionById($uuid){
        return Pergunta::findOrFail($uuid);
    }

    public function getFormById($uuid){
        return Formulario::findOrFail($uuid);
    }

    public function deleteSessions($uuid){
        Secao::findOrFail($uuid)->delete();   
    }

    public function deleteQuestion($uuid){
        DB::transaction(function () use ($uuid) {
            $pergunta = Pergunta::findOrFail($uuid);

            $subPerguntasIds = Pergunta::where('id_pergunta_pai', $pergunta->id)->pluck('id');

            if ($subPerguntasIds->isNotEmpty()) {
                Opcao_Pergunta::whereIn('id_pergunta', $subPerguntasIds)->delete();
                
                Pergunta::whereIn('id', $subPerguntasIds)->delete();
            }

            Opcao_Pergunta::where('id_pergunta', $pergunta->id)->delete();

            $pergunta->delete();
        });
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

    public function destroy($uuid) {
        $form = Formulario::findOrFail($uuid);

        foreach ($form->secoes() as $section) {
            $section->perguntas()->delete();
        }
        
        $form->secoes()->delete();
        $form->delete();
    }
}
