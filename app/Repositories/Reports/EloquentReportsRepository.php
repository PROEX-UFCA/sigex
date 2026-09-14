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

    public function getById($uuid){
        return Relatorio::find($uuid);
    }

    public function getSubmissionsForDownload($whatReport, $quais){

        if($quais == "todos"){
            return Submissao::where('id_relatorio', $whatReport)->get();
        }
        elseif($quais == "finalizados"){
            return Submissao::where('id_relatorio', $whatReport)->whereNotNull('finalizada_em')->get();
        }
        elseif($quais == "nao_finalizados"){
            return Submissao::where(['id_relatorio' => $whatReport, 'finalizada_em' => null])->get();
        }
        else{
            return [];
        }
    } 

    public function getSubmissionsByIdReport($uuid, array $filtros = [], string $sort = 'id', string $direction = 'desc')
    {
        $query = Submissao::select('submissao.*')->where('submissao.id_relatorio', $uuid);

        $query->when($filtros['search'] ?? null, function ($q, $search) {
            $q->where(function ($subQuery) use ($search) {
                $subQuery->whereHas('acao', function ($acaoQuery) use ($search) {
                    $acaoQuery->where('titulo', 'like', "%{$search}%");
                })
                ->orWhere('submissao.finalizada_em', 'like', "%{$search}%");
            });
        });

        if ($sort === 'titulo') {
            $query->join('acao', 'acao.id', '=', 'submissao.id_acao')
                ->orderBy('acao.titulo', $direction);
        } else {
            $query->orderBy("submissao.{$sort}", $direction);
        }

        return $query->paginate(30);
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
