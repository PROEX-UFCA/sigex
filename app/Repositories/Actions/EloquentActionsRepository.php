<?php

namespace App\Repositories\Actions;

use App\Models\Acao;
use App\Models\Agenda_Acao;
use App\Models\Equipe_Acao;

class EloquentActionsRepository implements ActionsRepository
{
    public function getByFilter(array $filtros = [], string $sort = 'ano', string $direction = 'desc')
    {
        $query = Acao::query();

        $query->when($filtros['search'] ?? null, function ($q, $search) {
            $q->where(function ($subQuery) use ($search) {
                $subQuery->where('id_projeto', 'like', "%{$search}%")
                    ->orWhere('titulo', 'like', "%{$search}%")
                    ->orWhere('centro_departamento_sigla', 'like', "%{$search}%")
                    ->orWhere('data_inicio', 'like', "%{$search}%")
                    ->orWhere('data_fim', 'like', "%{$search}%")
                    ->orWhere('ano', 'like', "%{$search}%")
                    ->orWhere('tipo_acao', 'like', "%{$search}%")
                    ->orWhere('area_tematica', 'like', "%{$search}%")
                    ->orWhere('modalidade_edital', 'like', "%{$search}%")
                    ->orWhere('situacao', 'like', "%{$search}%");
            });
        });

        $camposFiltro = [
            'centro_departamento_sigla', 'data_inicio', 'data_fim', 
            'ano', 'tipo_acao', 'area_tematica', 'modalidade_edital', 'situacao'
        ];

        foreach ($camposFiltro as $campo) {
            $query->when($filtros[$campo] ?? null, function ($q, $valor) use ($campo) {
                $q->where($campo, $valor); 
            });
        }

        return $query->orderBy($sort, $direction)->paginate(30)->withQueryString();
    }

    public function create($request)
    {
        $acao = Acao::create([
            'titulo' => $request->titulo,
            'tipo_acao' => $request->tipo,
            'modalidade_edital' => $request->modalidade,
            'centro_departamento_sigla' => $request->centro_departamento,
            'data_inicio' => $request->data_inicio,
            'data_fim' => $request->data_fim,
            'status' => 1,
            'situacao' => $request->situacao,
            'palavras_chave' => $request->palavras_chave,
            'ano' => $request->ano,
            'resumo' => $request->resumo,
            'id_projeto' => $request->id_projeto,
            'area_tematica' => $request->area_tematica,
            'ods' => implode('; ', $request->ods),
            'bolsas_solicitadas' => $request->bolsas_solicitadas,
            'bolsas_concedidas' => $request->bolsas_concedidas,
            'financiamento_interno' => $request->financiamento_interno,
            'financiamento_externo' => $request->financiamento_externo,
            'data_cadastro' => now(),
            'data_atualizacao' => now(),
            'contexto' => $request->contexto
        ]);

        Equipe_Acao::create([
            'id_acao' => $acao->id, 
            'id_usuario' => $request->id_coordenador, 
            'categoria_membro' => 'COORDENADOR'
        ]);

        return $acao;
    }

    public function createTeam($request, $id_acao)
    {
        Equipe_Acao::create([
            'id_acao' =>  $id_acao, 
            'id_usuario' => $request->id_usuario, 
            'categoria_membro' => $request->categoria
        ]);
    }

    public function createSchedule($request, $id_acao)
    {
        Agenda_Acao::create([
            'id_acao' =>  $id_acao,  
            'titulo_evento' => $request->titulo, 
            'data_hora_inicio' => $request->data_hora_inicio, 
            'data_hora_fim' => $request->data_hora_fim, 
            'local_formato' => $request->local_formato, 
            'descricao' => $request->descricao
        ]);
    }

    public function getAllByUuid($uuid, array $filtros = [])
    {
        return Equipe_Acao::with('action')
            ->where('id_usuario', $uuid)
            ->when($filtros['search'] ?? null, function ($query, $search) {
                $query->whereHas('action', function ($subQuery) use ($search) {
                    $subQuery->where(function ($q) use ($search) {
                        $q->where('titulo', 'like', "%{$search}%")
                            ->orWhere('data_inicio', 'like', "%{$search}%")
                            ->orWhere('data_fim', 'like', "%{$search}%")
                            ->orWhere('ano', 'like', "%{$search}%")
                            ->orWhere('status', 'like', "%{$search}%");
                    });
                });
            })->orderBy('created_at', 'asc')->get();
    }

    public function getByUserUuid($user_uuid, $uuid){
        return Equipe_Acao::where(['id_usuario' => $user_uuid, 'id_acao' => $uuid])->first();
    }

    public function getByUuid($uuid){
        return Acao::findOrFail($uuid);
    }

    public function update($request, $uuid){
        $acao = Acao::findOrFail($uuid);

        $membro = Equipe_Acao::where([
            'id_acao' => $acao->id, 
            'categoria_membro' => 'COORDENADOR'
        ])->first();

        $membro->id_usuario = $request->id_coordenador;
        $membro->save();

        $acao->titulo = $request->titulo;
        $acao->palavras_chave = $request->palavras_chave;
        $acao->tipo_acao = $request->tipo;
        $acao->modalidade_edital = $request->modalidade;
        $acao->centro_departamento_sigla = $request->centro_departamento;
        $acao->data_inicio = $request->data_inicio;
        $acao->data_fim = $request->data_fim;
        $acao->data_atualizacao = now();
        $acao->situacao = $request->situacao;
        $acao->ano = $request->ano;
        $acao->resumo = $request->resumo;
        $acao->id_projeto = $request->id_projeto;
        $acao->area_tematica = $request->area_tematica;
        $acao->ods = implode('; ', $request->ods);
        $acao->bolsas_solicitadas = $request->bolsas_solicitadas;
        $acao->bolsas_concedidas = $request->bolsas_concedidas;
        $acao->financiamento_interno = $request->financiamento_interno;
        $acao->financiamento_externo = $request->financiamento_externo;
        $acao->data_atualizacao = now();
        $acao->contexto = $request->contexto;

        $acao->save();

        return $acao;
    }

    public function getParameters($parameter){
        return Acao::select($parameter)->groupBy($parameter)->pluck($parameter);
    }

    public function getActionsForReports(array $filtros)
    {
        $query = Acao::query();

        if (isset($filtros['parametros']) && is_array($filtros['parametros'])) {
            
            if (!empty($filtros['parametros']['tipo'])) {
                $query->whereIn('tipo_acao', $filtros['parametros']['tipo']);
            }
            
            if (!empty($filtros['parametros']['modalidade'])) {
                $query->whereIn('modalidade', $filtros['parametros']['modalidade']);
            }

            if (!empty($filtros['parametros']['situacao'])) {
                $query->whereIn('situacao', $filtros['parametros']['situacao']);
            }
        }

        // if (!empty($filtros['ano_acao'])) {
        //     $query->where('ano', $filtros['ano_acao']);
        // }

        // if (!empty($filtros['ano_inicio'])) {
        //     $query->whereYear('data_inicio', $filtros['ano_inicio']);
        // }

        // if (!empty($filtros['ano_fim'])) {
        //     $query->whereYear('data_fim', $filtros['ano_fim']);
        // }

        return $query->get();
    }
}
