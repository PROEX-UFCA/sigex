<?php

namespace App\Repositories\Actions;

use App\Models\Acao;
use App\Models\Agenda_Acao;
use App\Models\Equipe_Acao;
use Illuminate\Support\Facades\DB;

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

    public function createTeam($request, $id_acao, $action)
    {
        $registroExistente = Equipe_Acao::where('id_usuario', $request->id_usuario)->first();

        $idPessoa = $registroExistente ? $registroExistente->id_pessoa : (Equipe_Acao::max('id_pessoa') ?? 0) + 1;

        Equipe_Acao::create([
            'id_acao' =>  $id_acao, 
            'id_usuario' => $request->id_usuario, 
            'categoria_membro' => $request->categoria_membro,
            'id_projeto' => $action->id_projeto, 
            'id_pessoa' => $idPessoa, 
            'tipo_membro' => $request->tipo_membro, 
            'tipo_vinculo' => $request->tipo_vinculo,
            'status' => $request->status_membros, 
            'data_inicio' => $request->data_inicio, 
            'data_fim' => $request->data_fim, 
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
        $acoes = Equipe_Acao::with('action')
        ->select(
            'id_acao', 
            'id_usuario',
            DB::raw('GROUP_CONCAT(DISTINCT categoria_membro SEPARATOR ", ") as categorias_membros'),
            DB::raw('MIN(created_at) as created_at') 
        )
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
        })
        ->groupBy('id_acao', 'id_usuario')
        ->orderBy('created_at', 'asc')
        ->paginate(30)
        ->withQueryString();

        return $acoes;
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

        if($membro){
            $membro->id_usuario = $request->id_coordenador;
            $membro->save();
        }
        else{
            Equipe_Acao::create([
                'id_acao' => $acao->id, 
                'id_usuario' => $request->id_coordenador, 
                'categoria_membro' => 'COORDENADOR',
                'id_projeto' => $request->id_projeto, 
                'status' => 'ATIVO', 
                'data_inicio' => now()
            ]);
        }

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
        $parametros = $filtros['parametros'] ?? [];
        
        
        $tipos = array_filter($parametros['tipo'] ?? []);
        $modalidades = array_filter($parametros['modalidade'] ?? []);
        $situacoes = array_filter($parametros['situacao'] ?? []);

        $query = Acao::query()
            ->when(!empty($tipos), function ($q) use ($tipos) {
            $q->whereIn('tipo_acao', $tipos);
            })
            ->when(!empty($modalidades), function ($q) use ($modalidades) {
                $q->whereIn('modalidade_edital', $modalidades);
            })
            ->when(!empty($situacoes), function ($q) use ($situacoes) {
                $q->whereIn('situacao', $situacoes);
            });

        return $query->get();
    }
}
