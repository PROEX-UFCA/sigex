<?php

namespace App\Http\Controllers\Web\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Report\StoreRequest;
use App\Http\Requests\Web\Report\UpdateRequest;
use App\Models\Pergunta;
use App\Models\Resposta;
use App\Models\ValidacaoResposta;
use App\Repositories\Actions\ActionsRepository;
use App\Repositories\Forms\FormsRepository;
use App\Repositories\Parametros\ParametrosRepository;
use App\Repositories\Reports\ReportsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    public function monitorar(Request $request, $uuid){

        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('dir', 'desc') === 'asc' ? 'asc' : 'desc';

        $allowedFields = ['titulo', 'finalizada_em', 'created_at'];

        if (!in_array($sort, $allowedFields)) $sort = 'created_at';

        $this->data['submissoes'] = $this->reportRepository->getSubmissionsByIdReport($uuid, $request->query(), $sort, $direction);
        
        $this->data['submissao'] = $this->reportRepository->getById($uuid);
        
        return view('pages.report.monitor', $this->data);
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

    public function validator($uuid)
    {
        $submissao = $this->reportRepository->getSubmissionById($uuid);
        $secoesData = [];

        // Trazemos as seções ordenadas pela coluna 'ordem'
        $secoes = $submissao->relatorio->formulario->secoes()->orderBy('ordem')->get();
        foreach ($secoes as $secao) {
            $perguntasData = [];
            
            // Traz apenas as perguntas pai (ignora as sub-perguntas soltas)
            foreach ($secao->perguntas()->whereNull('id_pergunta_pai')->get() as $pergunta) {
                // --- TRATAMENTO PARA TABELA/REPEATER ---
                if ($pergunta->tipo === 'tabela') {
                    $respostasTabela = Resposta::whereIn('id_pergunta', $pergunta->filhas->pluck('id'))
                        ->where('id_submissao', $submissao->id)
                        ->get()
                        ->groupBy('indice_grupo');
                    $valoresTabela = [];
                    foreach($respostasTabela as $indice => $respostasLinha) {
                        $linhaFormatada = [];
                        foreach($pergunta->filhas as $filha) {
                            $resp = $respostasLinha->where('id_pergunta', $filha->id)->first();
                            
                            $regras = [];
    
                            if($filha->min){
                                $regras['min'] = $filha->min;
                            }
                            if($filha->max ){
                                $regras['max'] = $filha->max;
                            }
                            if($filha->step ){
                                $regras['intervalo'] = $filha->step;
                            }
                            if($filha->accept){
                                $regras['formatos'] = $filha->accept;
                                // $regras['formatos'] = str_replace(',', ', ', $filha->accept) 
                            }
                            if($filha->regex){
                                $regras['formato'] = $filha->regex;
                            }

                            $linhaFormatada[] = [
                                'id' => $filha->id_pergunta_pai,
                                'id_resposta' => $resp->id ?? null,
                                'enunciado' => $filha->enunciado,
                                'tipo'      => $filha->tipo,
                                'valor'     => $this->formatarResposta($resp, $filha->tipo),
                                'obrigatorio' => $filha->obrigatoria, 
                                'validacao_salva' => $resp->validacao,
                                'regras' => $regras ? str_replace([',', '"', ':', '{', '}'], [', ', '', ': ', '', ''], json_encode($regras)) : '',
                            ];
                        }
                        
                        $valoresTabela[] = $linhaFormatada;
                    }

                     $regras = [];
    
                    if($pergunta->min){
                        $regras['min'] = $pergunta->min;
                    }
                    if($pergunta->max ){
                        $regras['max'] = $pergunta->max;
                    }
                    if($pergunta->step ){
                        $regras['intervalo'] = $pergunta->step;
                    }
                    if($pergunta->accept){
                        $regras['formatos'] = $pergunta->accept;
                        // $regras['formatos'] = str_replace(',', ', ', $pergunta->accept) 
                    }
                    if($pergunta->regex){
                        $regras['formato'] = $pergunta->regex;
                    }
                    
                    $perguntasData[] = [
                        'id_pergunta'             => $pergunta->id,
                        'enunciado'      => $pergunta->enunciado,
                        'tipo'           => 'tabela',
                        'valores_tabela' => $valoresTabela, 
                        'obrigatorio' => $pergunta->obrigatoria, 
                        'validacao_salva' => $pergunta->validacao,
                        'regras' => $regras ? str_replace([',', '"', ':', '{', '}'], [', ', '', ': ', '', ''], json_encode($regras)) : '',
                    ];

                } 
                // --- TRATAMENTO PARA PERGUNTAS COMUNS ---
                else {
                    $resposta = $pergunta->getRespostaPorSubmissao($submissao->id);

                    $regras = [];

                    if($pergunta->min){
                        $regras['min'] = $pergunta->min;
                    }
                    if($pergunta->max ){
                        $regras['max'] = $pergunta->max;
                    }
                    if($pergunta->step ){
                        $regras['intervalo'] = $pergunta->step;
                    }
                    if($pergunta->accept){
                        $regras['formatos'] = $pergunta->accept;
                        // $regras['formatos'] = str_replace(',', ', ', $pergunta->accept) 
                    }
                    if($pergunta->regex){
                        $regras['formato'] = $pergunta->regex;
                    }

                    $perguntasData[] = [
                        'id_pergunta'        => $pergunta->id,
                        'id_resposta' => $resposta->id,
                        'enunciado' => $pergunta->enunciado,
                        'tipo'      => $pergunta->tipo,
                        'valor'     => $this->formatarResposta($resposta, $pergunta->tipo),
                        'obrigatorio' => $pergunta->obrigatoria, 
                        'validacao_salva' => $resposta->validacao,
                        'regras' => $regras ? str_replace([',', '"', ':', '{', '}'], [', ', '', ': ', '', ''], json_encode($regras)) : '', 
                    ];
                }
            }

            $secoesData[] = [
                'id_secao'        => $secao->id,
                'titulo'    => $secao->titulo,
                'descricao' => $secao->descricao,
                'perguntas' => $perguntasData
            ];
        }

        // dd($secoesData);

        $this->data['submissao'] = $submissao;
        $this->data['secoes']    = $secoesData;

        
        return view('pages.report.validator', $this->data);
    }

    /**
     * Método auxiliar para transformar os dados brutos do banco em algo visual.
     */
    private function formatarResposta($resposta, $tipo)
    {
        if (!$resposta || empty($resposta->valor)) {
            return '<span class="text-muted fst-italic">Não respondido</span>';
        }

        $valor = $resposta->valor;
        
        switch ($tipo) {
            case 'file':
                // Cria a URL do storage para o avaliador baixar/abrir o arquivo
                $url = route('arquivo.visualizar', ['path' => $valor]);
                return '<a href="'.$url.'" target="_blank" class="btn btn-sm btn-outline-primary"><i class="ti ti-external-link"></i> Abrir Arquivo</a>';
                
            case 'location':
                $json = json_decode($valor, true);
                return $json['nome'] ?? $valor;
                
            case 'checkbox':
                $json = json_decode($valor, true);
                return is_array($json) ? implode(', ', $json) : $valor;
                
            case 'date':
                return date('d/m/Y', strtotime($valor));
                
            case 'datetime-local':
                return date('d/m/Y H:i', strtotime($valor));
                
            default:
                return $valor;
        }
    }

    public function visualizarArquivo(Request $request)
    {
        $path = $request->query('path');

        if (!Storage::exists($path)) {
            abort(404, 'Arquivo não encontrado.');
        }

        return Storage::response($path);
    }

    public function validar(Request $request, $idSubmissao)
    {
        // dd($request->all());
        // 1. Validação de segurança dos dados que vêm do formulário
        $request->validate([
            'validacao' => 'required|array',
            'validacao.*.id_resposta' => 'required',
            'validacao.*.status' => 'required|in:0,1',
            // O feedback só é obrigatório SE o status for 'correcao'
            'validacao.*.feedback' => 'required_if:validacao.*.status,correcao',
        ], [
            'validacao.*.feedback.required_if' => 'O preenchimento do feedback é obrigatório para os itens reprovados.'
        ]);

        // 2. Inicia a transação no banco de dados
        DB::beginTransaction();

        try {
            $avaliadorId = Auth::user()->uuid;

            // 3. Itera sobre o array recebido (agora a chave é o id_resposta)
            foreach ($request->input('validacao') as $idResposta => $dados) {

                // Busca a resposta para pegar o id_pergunta verdadeiro vinculado a ela
                $resposta = Resposta::find($idResposta);

                // Se a resposta não existir, pula essa iteração (evita falhas de integridade)
                if (!$resposta) {
                    continue; 
                }

                // 4. Salva ou atualiza a validação daquela resposta específica
                ValidacaoResposta::updateOrCreate(
                    [
                        'id_resposta' => $idResposta
                    ],
                    [
                        'id_avaliador' => $avaliadorId,
                        'id_pergunta'  => $resposta->id_pergunta, // Pega direto do banco
                        'status'       => $dados['status'],
                        // Se foi aprovado, força null no banco. Se foi correção, salva o texto.
                        'correcao'     => $dados['status'] == 1 ? null : $dados['feedback'], 
                    ]
                );
            }
// dd($dados['status']);
            // 5. Atualizar o status geral da Submissão
            // $submissao = Submissao::findOrFail($idSubmissao);
            $submissao = $this->reportRepository->getSubmissionById($idSubmissao);
            
            // if ($precisaDeCorrecao) {
            //     $submissao->status = 'correcao'; // Ajuste para o status real que seu sistema usa (ex: 'retornado', 'correcao_solicitada')
            // } else {
            //     $submissao->status = 'aprovado'; // Ajuste para o status real (ex: 'validado', 'finalizado')
            // }
            
            // $submissao->save();

            // Confirma a gravação no banco
            DB::commit();
return redirect()->back();
            // Redireciona com mensagem de sucesso (Atenção: enviei para id_relatorio, conforme o botão voltar da sua view)
            return redirect()->route('report.monitor', $submissao->id_relatorio)
                            ->with('success', 'Avaliação da submissão salva e processada com sucesso!');

        } catch (\Exception $e) {
            // Se der qualquer erro, cancela tudo que foi feito no banco
            DB::rollBack();
            
            // Retorna para a tela de validação com o erro e os dados preenchidos (withInput ajuda o old() da view)
            return back()->withInput()->with('error', 'Ocorreu um erro ao salvar a avaliação: ' . $e->getMessage());
        }
    }
}