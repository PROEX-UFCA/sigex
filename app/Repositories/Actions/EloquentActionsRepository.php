<?php

namespace App\Repositories\Actions;

use App\Models\Acao;
use App\Models\Equipe_Acao;

class EloquentActionsRepository implements ActionsRepository
{
    public function getByFilter(array $filtros = [])
    {
        $query = Acao::query();

        $query->when($filtros['search'] ?? null, function ($q, $search) {
            $q->where(function ($subQuery) use ($search) {
                $subQuery->where('id_atividade', 'like', "%{$search}%")
                    ->orWhere('id_projeto', 'like', "%{$search}%")
                    ->orWhere('titulo', 'like', "%{$search}%")
                    ->orWhere('centro_departamento', 'like', "%{$search}%")
                    ->orWhere('data_inicio', 'like', "%{$search}%")
                    ->orWhere('data_fim', 'like', "%{$search}%")
                    ->orWhere('ano', 'like', "%{$search}%")
                    ->orWhere('tipo_acao', 'like', "%{$search}%")
                    ->orWhere('area_tematica', 'like', "%{$search}%")
                    ->orWhere('modalidade', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    
                    ->orWhereHas('coordenador', function ($qCoordenador) use ($search) {
                        $qCoordenador->where('name', 'like', "%{$search}%"); 
                    });
            });
        });

        $camposFiltro = [
            'centro_departamento', 'data_inicio', 'data_fim', 
            'ano', 'tipo_acao', 'area_tematica', 'modalidade', 'status'
        ];

        foreach ($camposFiltro as $campo) {
            $query->when($filtros[$campo] ?? null, function ($q, $valor) use ($campo) {
                $q->where($campo, $valor); 
            });
        }

        return $query->paginate(30);
    }

    public function create($request)
    {
        $acao = Acao::create([
            'titulo' => $request->titulo,
            'tipo_acao' => $request->tipo,
            'modalidade' => $request->modalidade,
            'centro_departamento' => $request->centro_departamento,
            'id_coordenador' => $request->id_coordenador,
            'data_inicio' => $request->data_inicio,
            'data_fim' => $request->data_fim,
            'status' => $request->status,
            'ano' => $request->ano,
            'id_atividade' => $request->id_atividade,
            'id_projeto' => $request->id_projeto,
            'area_tematica' => $request->area_tematica,
            'centro_departamento' => $request->centro_departamento,
        ]);

        Equipe_Acao::create([
            'id_acao' => $acao->id, 
            'id_usuario' => $request->id_coordenador, 
            'categoria' => 'Coordenador'
        ]);

        return $acao;
    }

    public function getAllByUuid($uuid){
        return Equipe_Acao::where('id_usuario', $uuid)->orderBy('created_at', 'asc')->get();
    }

    public function getByUserUuid($user_uuid, $uuid){
        return Equipe_Acao::where(['id_usuario' => $user_uuid, 'id_acao' => $uuid])->first();
    }
}
