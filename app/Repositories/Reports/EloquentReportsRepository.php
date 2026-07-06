<?php

namespace App\Repositories\Reports;

use App\Models\Parametro;
use App\Models\Relatorio;
use App\Models\Submissao;

class EloquentReportsRepository implements ReportsRepository
{
    public function getByFilter(array $filtros = [], string $sort = 'desc', string $direction = 'desc')
    {
        $query = Relatorio::query();

        $query->when($filtros['search'] ?? null, function ($q, $search) {
            $q->where(function ($subQuery) use ($search) {
                $subQuery->where('titulo', 'like', "%{$search}%");
            });
        });

        return $query->orderBy($sort, $direction)->paginate(30);
    }

    public function create($request)
    {
        $report = Relatorio::create([
            'id_formulario' => $request->formulario, 
            'titulo' => $request->titulo, 
            'data_inicio' => $request->data_inicio, 
            'prazo' => $request->prazo, 
            'status' => 1
        ]);

        return $report;
    }

    public function bulkInsertSubmission($submissoes){
        Submissao::insert($submissoes);
    }

    public function getSubmissionById($uuid){
        return Submissao::find($uuid);
    }

    public function update($request, $uuid){
        $report = Relatorio::findOrFail($uuid);

        $report->titulo = $request->titulo;
        $report->data_inicio = $request->data_inicio;
        $report->prazo = $request->prazo;
        $report->status = $request->status;

        $report->save();

        return $report;
    }
}
