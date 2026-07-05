<?php

namespace App\Http\Controllers\Web\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Report\StoreRequest;
use App\Http\Requests\Web\Report\UpdateRequest;
use App\Repositories\Actions\ActionsRepository;
use App\Repositories\Forms\FormsRepository;
use App\Repositories\Parametros\ParametrosRepository;
use App\Repositories\Reports\ReportsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    private $data;
    private $actionsRepository;
    private $parametrosRepository;
    private $formsRepository;
    private $reportRepository;

    public function __construct(ActionsRepository $actionsRepository, ParametrosRepository $parametrosRepository, FormsRepository $formsRepository, ReportsRepository $reportRepository)
    {
        $this->actionsRepository = $actionsRepository;
        $this->parametrosRepository = $parametrosRepository;
        $this->formsRepository = $formsRepository;
        $this->reportRepository = $reportRepository;
    }

    public function index(Request $request){

        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('dir', 'desc') === 'asc' ? 'asc' : 'desc';

        $allowedFields = ['titulo', 'status', 'created_at'];

        if (!in_array($sort, $allowedFields)) $sort = 'created_at';

        $this->data['reports'] = $this->reportRepository->getByFilter($request->query(), $sort, $direction);

        return view('pages.report.index', $this->data);
    }

    public function create(){
        $this->data['parametros'] = $this->parametrosRepository->getAllActiveByFunctions(['TIPO', 'MODALIDADE', 'SITUACAO'])->groupBy('function');
        $this->data['formularios'] = $this->formsRepository->getAllActive();

        return view('pages.report.create', $this->data);
    }

    public function store(StoreRequest $request){
        try {
            $report = $this->reportRepository->create($request);
    
            $actions = $this->actionsRepository->getActionsForReports($request->only(['parametros', 'ano_acao', 'ano_inicio', 'ano_fim']));
    
            $submissoes = [];
    
            foreach ($actions as $action) {
                $submissoes[] = [
                    'id' => Str::uuid(),
                    'id_relatorio' => $report->id,
                    'id_acao' => $action->id,
                    'finalizada_em' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
    
            if (!empty($submissoes)) {
                $this->reportRepository->bulkInsertSubmission($submissoes);
            }
    
            return redirect()->route('report.index')->with('success', "Relatório criado e ações vinculadas com sucesso! {$actions->count()} Ações foram contempladas.");
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', "Erro ao tentar criar relatório, tente novamente mais tarde.");
        }
    }

    public function update(UpdateRequest $request, $uuid){
        try {
            $report = $this->reportRepository->update($request, $uuid);
    
            return redirect()->route('report.index')->with('success', "Relatório atualizado com sucesso!");
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', "Erro ao tentar atualizar relatório, tente novamente mais tarde.");
        }
    }
}