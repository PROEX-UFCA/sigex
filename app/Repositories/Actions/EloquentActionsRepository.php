<?php

namespace App\Repositories\Actions;

use App\Models\Acao;

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
}
