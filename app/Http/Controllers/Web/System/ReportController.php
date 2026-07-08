<?php

namespace App\Http\Controllers\Web\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Report\StoreRequest;
use App\Http\Requests\Web\Report\UpdateRequest;
use App\Models\Resposta;
use App\Repositories\Actions\ActionsRepository;
use App\Repositories\Forms\FormsRepository;
use App\Repositories\Parametros\ParametrosRepository;
use App\Repositories\Reports\ReportsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

     public function report($uuid){
        $this->data['submissao'] = $this->reportRepository->getSubmissionById($uuid);
        return view('pages.actions.report', $this->data);
    }

    public function autoSave(Request $request)
    {
        $request->validate([
            'id_submissao' => 'required',
            'id_pergunta' => 'required',
        ]);

        $valor = $request->input('valor');

        if (is_array($valor)) {
            $valor = json_encode($valor);
        }

        // Buscamos se já existe uma resposta salva para essa pergunta/submissão
        $respostaExistente = Resposta::where('id_submissao', $request->id_submissao)
                                    ->where('id_pergunta', $request->id_pergunta)
                                    ->first();

        if ($request->hasFile('file')) {
            // Se já havia uma resposta com um caminho de arquivo salvo, apagamos do disco
            if ($respostaExistente && !empty($respostaExistente->valor)) {
                // Verifica se o arquivo realmente existe no storage antes de apagar
                if (Storage::exists($respostaExistente->valor)) {
                    Storage::delete($respostaExistente->valor);
                }
            }

            // Salva o novo arquivo enviado
            $path = $request->file('file')->store('respostas/arquivos');
            $valor = $path;
        }

        // Caso seja um input normal que foi apagado (valor vazio), e não um arquivo
        // Precisamos evitar que uma resposta que era arquivo seja sobreposta por um valor vazio acidental
        if (!$request->hasFile('file') && $request->file === null && $respostaExistente && str_starts_with($respostaExistente->valor, 'respostas/arquivos')) {
            // Não alteramos o valor se a requisição não tem arquivo, mas o banco já tem um arquivo salvo.
            $valor = $respostaExistente->valor;
        }

        // Atualiza ou cria a resposta no banco
        Resposta::updateOrCreate(
            [
                'id_submissao' => $request->id_submissao,
                'id_pergunta'  => $request->id_pergunta,
            ],
            [
                'valor' => $valor ?? ''
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Progresso salvo com sucesso!'
        ]);
    }
}