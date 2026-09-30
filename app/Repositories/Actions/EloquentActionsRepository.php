<?php

namespace App\Repositories\Actions;

use App\Models\Acao;
use App\Models\Agenda_Acao;
use App\Models\Equipe_Acao;
use Illuminate\Support\Facades\DB;
use App\Models\Agenda_Interna_Acao;
use App\Models\Galeria_Acao;

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
            'categoria_membro' => 'COORDENADOR(A)'
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

    public function updateSchedule($request, $id_agenda)
    {
        $agenda = \App\Models\Agenda_Acao::findOrFail($id_agenda);
        
        $agenda->update([
            'titulo_evento' => $request->titulo, 
            'data_hora_inicio' => $request->data_hora_inicio, 
            'data_hora_fim' => $request->data_hora_fim, 
            'local_formato' => $request->local_formato, 
            'descricao' => $request->descricao
        ]);

        return $agenda;
    }

    public function deleteSchedule($id_agenda)
    {
        $agenda = \App\Models\Agenda_Acao::findOrFail($id_agenda);
        
        return $agenda->delete();
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
            'categoria_membro' => 'COORDENADOR(A)'
        ])->first();

        if($membro){
            $membro->id_usuario = $request->id_coordenador;
            $membro->save();
        }
        else{
            Equipe_Acao::create([
                'id_acao' => $acao->id, 
                'id_usuario' => $request->id_coordenador, 
                'categoria_membro' => 'COORDENADOR(A)',
                'id_projeto' => $request->id_projeto, 
                'status' => 'ATIVO', 
                'data_inicio' => now()
            ]);
        }

        $acao->titulo = $request->titulo;
        $acao->palavras_chave = $request->palavras_chave;
        $acao->tipo_acao = $request->tipo;
        $acao->modalidade_edital = $request->modalidade_edital;
        $acao->centro_departamento_sigla = $request->centro_departamento_sigla;
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
        $acao->is_ej = $request->is_ej;

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
        $modalidades = array_filter($parametros['modalidade_edital'] ?? []);
        $situacoes = array_filter($parametros['situacao'] ?? []);
        $is_ej = $filtros['is_ej'] == 'todos' ? [0, 1] : (array)$filtros['is_ej'];

        $query = Acao::query()
            ->when(!empty($tipos), function ($q) use ($tipos) {
            $q->whereIn('tipo_acao', $tipos);
            })
            ->when(!empty($modalidades), function ($q) use ($modalidades) {
                $q->whereIn('modalidade_edital', $modalidades);
            })
            ->when(!empty($situacoes), function ($q) use ($situacoes) {
                $q->whereIn('situacao', $situacoes);
            })->whereIn('is_ej', $is_ej);

        return $query->get();
    }

    public function getMembersForReports(array $filtros)
    {
        $parametros = $filtros['parametros'] ?? [];
        
        $tipo_membro = array_filter($parametros['tipo_membro'] ?? []);
        $categoria_membro = array_filter($parametros['categoria_membro'] ?? []);
        $status_membros = array_filter($parametros['status_membros'] ?? []);

        $query = Equipe_Acao::query()
            ->when(!empty($tipo_membro), function ($q) use ($tipo_membro) {
            $q->whereIn('tipo_membro', $tipo_membro);
            })
            ->when(!empty($categoria_membro), function ($q) use ($categoria_membro) {
                $q->whereIn('categoria_membro', $categoria_membro);
            })
            ->when(!empty($status_membros), function ($q) use ($status_membros) {
                $q->whereIn('status', $status_membros);
            });

        return $query->get();
    }

    public function createInternalSchedule($request, $id_acao)
    {
        return Agenda_Interna_Acao::create([
            'id_acao' => $id_acao,
            'titulo_evento' => $request->titulo,
            'data_hora_inicio' => $request->data_hora_inicio,
            'data_hora_fim' => $request->data_hora_fim,
            'local_formato' => $request->local_formato,
            'descricao' => $request->descricao,
            'pauta_interna' => $request->pauta_interna,
        ]);
    }

    public function updateInternalSchedule($request, $id_agenda)
    {
        $agenda = Agenda_Interna_Acao::findOrFail($id_agenda);
        
        $agenda->update([
            'titulo_evento' => $request->titulo,
            'data_hora_inicio' => $request->data_hora_inicio,
            'data_hora_fim' => $request->data_hora_fim,
            'local_formato' => $request->local_formato,
            'descricao' => $request->descricao,
            'pauta_interna' => $request->pauta_interna,
        ]);

        return $agenda;
    }

    public function deleteInternalSchedule($id_agenda)
    {
        $agenda = Agenda_Interna_Acao::findOrFail($id_agenda);
        return $agenda->delete();
    }

    public function createGalleryImages(array $paths, array $altTexts, $id_acao)
    {
        foreach ($paths as $index => $path) {
            Galeria_Acao::create([
                'id_acao' => $id_acao,
                'caminho_imagem' => $path,
                'texto_alternativo' => $altTexts[$index] ?? null,
            ]);
        }
    }

    public function updateGalleryAltText($id_imagem, $altText)
    {
        $imagem = Galeria_Acao::findOrFail($id_imagem);
        $imagem->texto_alternativo = $altText;
        $imagem->save();
        
        return $imagem;
    }

    public function deleteGalleryImage($id_imagem)
    {
        $imagem = Galeria_Acao::findOrFail($id_imagem);
        return $imagem->delete();
    }

    public function updateBannerAltText($uuid, $altText)
    {
        $acao = Acao::findOrFail($uuid);
        $acao->alt_capa = $altText;
        $acao->save();
        
        return $acao;
    }

}
