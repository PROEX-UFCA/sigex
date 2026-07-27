<?php

namespace App\Http\Controllers\Web\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Report\StoreRequest;
use App\Http\Requests\Web\Report\UpdateRequest;
use App\Models\Pergunta;
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
        $this->data['parametros'] = $this->parametrosRepository->getAllActiveByFunctions(['TIPO', 'MODALIDADE_EDITAL', 'SITUACAO'])->groupBy('function');
        $this->data['formularios'] = $this->formsRepository->getAllActive();

        return view('pages.report.create', $this->data);
    }

    public function store(StoreRequest $request){
        try {
            $actions = $this->actionsRepository->getActionsForReports($request->only(['parametros']));

            $report = $this->reportRepository->create($request);
    
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
        $this->data['progresso'] = $this->getProgress($this->data['submissao']->id);
    
        if($this->data['submissao']->finalizada_em != null){
            return redirect()->back()->with('warning', "Esse relatório já foi enviado, não é possível mais acessá-lo!");
        }
        
        if($this->data['submissao']->relatorio->prazo < now()){        
            return redirect()->back()->with('warning', "O prazo para enviar esse relatório já passou!");
        }

        if($this->data['submissao']->relatorio->data_inicio > now()){
            return redirect()->back()->with('warning', "Esse relatório ainda não está liberado!");
        }

        return view('pages.actions.report', $this->data);
    }

    public function finish($uuid){
        try {
            $submissao = $this->reportRepository->getSubmissionById($uuid);
            $submissao->finalizada_em = now();
            $submissao->save();

            if($this->getProgress($submissao->id) < 100){
                return redirect()->back()->with('warning', "Preecha todo o reatório para poder finalizar!");
            }

            return to_route('actions.my')->with('success', "Relatório finalizado com sucesso.");
        }
        catch (\Throwable $th) {
            return redirect()->back()->with('error', "Erro ao finalizar formulário, tente novamente mais tarde!");
        }
    }

    public function autoSave(Request $request)
    {
        $request->validate([
            'id_submissao' => 'required',
            'id_pergunta'  => 'required',
            'indice_grupo' => 'nullable|integer' // Validando o novo campo
        ]);

        // Se vier nulo (perguntas comuns fora de tabelas), assume 0
        $indiceGrupo = $request->input('indice_grupo', 0); 
        $valor = $request->input('valor');

        // Trata arrays (como checkboxes)
        if (is_array($valor)) {
            $valor = json_encode($valor);
        }

        // Busca a resposta existente considerando também o índice da linha
        $respostaExistente = Resposta::where('id_submissao', $request->id_submissao)
            ->where('id_pergunta', $request->id_pergunta)
            ->where('indice_grupo', $indiceGrupo)
            ->first();

        // Tratamento de Upload de Arquivos
        if ($request->hasFile('file')) {
            // Remove o arquivo antigo se estiver substituindo
            if ($respostaExistente && !empty($respostaExistente->valor)) {
                if (Storage::exists($respostaExistente->valor)) {
                    Storage::delete($respostaExistente->valor);
                }
            }

            $path = $request->file('file')->store('respostas/arquivos');
            $valor = $path;
        }

        // Mantém o arquivo antigo se um novo não foi enviado nesta requisição
        if (!$request->hasFile('file') && $request->file === null && $respostaExistente && str_starts_with($respostaExistente->valor, 'respostas/arquivos')) {
            $valor = $respostaExistente->valor;
        }

        // Atualiza ou Cria o registro no banco
        Resposta::updateOrCreate(
            [
                'id_submissao' => $request->id_submissao,
                'id_pergunta'  => $request->id_pergunta,
                'indice_grupo' => $indiceGrupo // Garante que cada linha da tabela seja salva separadamente
            ],
            [
                'valor' => $valor ?? ''
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Progresso salvo com sucesso!',
            'progresso' => $this->getProgress($request->id_submissao)
        ]);
    }

    public function removerGrupo(Request $request)
    {
        $request->validate([
            'id_submissao'    => 'required',
            'id_pergunta_pai' => 'required',
            'indice_grupo'    => 'required|integer'
        ]);

        // 1. Encontra todos os IDs das colunas (sub-perguntas) que pertencem a essa tabela
        $subPerguntasIds = Pergunta::where('id_pergunta_pai', $request->id_pergunta_pai)->pluck('id');

        if ($subPerguntasIds->isNotEmpty()) {
            
            // 2. Busca se há arquivos salvos nessa linha específica para apagá-los do servidor (Storage)
            $respostasComArquivos = Resposta::where('id_submissao', $request->id_submissao)
                ->whereIn('id_pergunta', $subPerguntasIds)
                ->where('indice_grupo', $request->indice_grupo)
                ->where('valor', 'like', 'respostas/arquivos/%')
                ->get();

            foreach ($respostasComArquivos as $resposta) {
                if (Storage::exists($resposta->valor)) {
                    Storage::delete($resposta->valor);
                }
            }

            // 3. Deleta todas as respostas daquela linha do banco de dados
            Resposta::where('id_submissao', $request->id_submissao)
                ->whereIn('id_pergunta', $subPerguntasIds)
                ->where('indice_grupo', $request->indice_grupo)
                ->delete();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Item removido do relatório com sucesso!',
            'progresso' => $this->getProgress($request->id_submissao) // Atualiza a barra de progresso
        ]);
    }

    private function getProgress($uuid)
    {
        $submissao = $this->reportRepository->getSubmissionById($uuid);

        $qtdPerguntas = 0;
        $qtdPerguntasRespondidas = 0;

        foreach($submissao->relatorio->formulario->secoes as $index => $secao){
            
            foreach ($secao->perguntas->whereNull('id_pergunta_pai') as $pergunta) {

                $qtdPerguntas++;

                if ($pergunta->tipo === 'tabela') {
                    
                    $temRespostaNaTabela = \App\Models\Resposta::whereIn('id_pergunta', $pergunta->filhas->pluck('id'))
                        ->where('id_submissao', $submissao->id)
                        ->whereNotNull('valor')
                        ->where('valor', '!=', '')
                        ->exists();

                    if ($temRespostaNaTabela) {
                        $qtdPerguntasRespondidas++;
                    }

                } else {
                    $resposta = $pergunta->getRespostaPorSubmissao($submissao->id);

                    if ($resposta && $resposta->valor != null && $resposta->valor != '') {
                        $qtdPerguntasRespondidas++; 
                    }
                }
            }
        }

        return $qtdPerguntas > 0 ? intval(round(($qtdPerguntasRespondidas / $qtdPerguntas) * 100, 2)) : 0;
    }
}