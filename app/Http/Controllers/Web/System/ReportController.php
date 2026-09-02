<?php

namespace App\Http\Controllers\Web\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Report\StoreRequest;
use App\Http\Requests\Web\Report\UpdateRequest;
use App\Models\Equipe_Acao;
use App\Models\Pergunta;
use App\Models\Resposta;
use App\Models\ValidacaoResposta;
use App\Repositories\Actions\ActionsRepository;
use App\Repositories\Forms\FormsRepository;
use App\Repositories\Parametros\ParametrosRepository;
use App\Repositories\Reports\ReportsRepository;
use App\Repositories\Settings\User\UsersRepository;
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
    private $usersRepository;

    public function __construct(ActionsRepository $actionsRepository, ParametrosRepository $parametrosRepository, FormsRepository $formsRepository, ReportsRepository $reportRepository, UsersRepository $usersRepository)
    {
        $this->actionsRepository = $actionsRepository;
        $this->parametrosRepository = $parametrosRepository;
        $this->formsRepository = $formsRepository;
        $this->reportRepository = $reportRepository;
        $this->usersRepository = $usersRepository;
    }

    public function index(Request $request){

        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('dir', 'desc') === 'asc' ? 'asc' : 'desc';

        $allowedFields = ['titulo', 'status', 'created_at'];

        if (!in_array($sort, $allowedFields)) $sort = 'created_at';

        $this->data['reports'] = $this->reportRepository->getByFilter($request->query(), $sort, $direction);

        return view('pages.report.index', $this->data);
    }

    public function create(Request $request)
    {
        $this->data['parametros'] = $this->parametrosRepository->getAllActiveByFunctions(['TIPO', 'MODALIDADE_EDITAL', 'SITUACAO'])->groupBy('function');
        $this->data['formularios'] = $this->formsRepository->getAllActive();
        $this->data['parametros_membros'] = $this->parametrosRepository->getAllActiveByFunctions(['TIPO_MEMBRO', 'CATEGORIA_MEMBRO', 'STATUS_MEMBROS'])->groupBy('function');

        $this->data['who'] = $request->input('who', old('who', ''));
        $this->data['items'] = collect();

        if ($request->isMethod('post') && !empty($this->data['who'])) {
            switch ($this->data['who']) {
                case 'acoes':
                    $this->data['items'] = $this->actionsRepository->getActionsForReports($request->only(['parametros', 'is_ej']));
                    break;
                case 'membros':
                    $this->data['items'] = $this->actionsRepository->getMembersForReports($request->only(['parametros']));
                    break;
                case 'usuarios':
                    // Substitua pelo método correto do repositório de usuários
                    $this->data['items'] = $this->usersRepository->getForCoordinator();
                    break;
            }
        }

        return view('pages.report.create', $this->data);
    }

    public function store(StoreRequest $request){

        // dd($request->all());
        try {
            
            // $actions = $this->actionsRepository->getActionsForReports($request->only(['parametros']));

            $report = $this->reportRepository->create($request);
    
            $submissoes = [];
    
            foreach ($request->target_ids as $targetJson) {
                $target = json_decode($targetJson, true);

                $idAcao = $target['id_acao'] ?? null;
                $idUsuario = $target['id_usuario'] ?? null;
                
                $submissoes[] = [
                    'id' => Str::uuid(),
                    'id_relatorio' => $report->id,
                    'id_acao' => $idAcao,
                    'id_usuario' => $idUsuario,
                    'finalizada_em' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            
            }
    
            if (!empty($submissoes)) {
                $this->reportRepository->bulkInsertSubmission($submissoes);
            }
    
            return redirect()->route('report.index')->with('success', "Relatório criado e ações vinculadas com sucesso.");
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

        if($this->data['submissao']->relatorio->data_inicio > now()){
            return redirect()->back()->with('warning', "Esse relatório ainda não está liberado!");
        }

        $precisaCorrecao = Resposta::where('id_submissao', $this->data['submissao']->id)
            ->whereHas('validacao', function($query) {
                $query->where('status', 0);
            })->exists();
        
        $this->data['emCorrecao'] = $precisaCorrecao;
        
        if($this->data['submissao']->finalizada_em != null && !$precisaCorrecao){
            return redirect()->back()->with('warning', "Esse relatório já foi enviado e está em análise, não é possível mais acessá-lo!");
        }
        
        if($this->data['submissao']->relatorio->prazo < now() && !$precisaCorrecao){        
            return redirect()->back()->with('warning', "O prazo para enviar esse relatório já passou!");
        }

        if($this->data['submissao']->relatorio->status == 2 || $this->data['submissao']->relatorio->status == 0){
            return redirect()->back()->with('warning', "Esse relatório não está liberado!");
        }

        return view('pages.actions.report', $this->data);
    }

    public function finish($uuid){
        try {
            $submissao = $this->reportRepository->getSubmissionById($uuid);

            // 1. Validação robusta de campos obrigatórios no Backend
            if(!$this->validarObrigatorias($submissao)) {
                return redirect()->back()->with('warning', "Existem campos obrigatórios não preenchidos. Verifique todas as seções e tabelas.");
            }

            // 2. Garante que perguntas opcionais ou não tocadas sejam salvas como null para gerar o id_resposta
            $this->salvarRespostasNulasEIgnoradas($submissao);

            // 3. Ao re-enviar, apagamos os status 0 para que a tela volte a bloquear (o avaliador vai gerar novos status 0 ou 1)
            $respostasIds = Resposta::where('id_submissao', $submissao->id)->pluck('id');
            ValidacaoResposta::whereIn('id_resposta', $respostasIds)->where('status', 0)->delete();

            $submissao->finalizada_em = now();
            $submissao->save();

            return to_route('actions.my')->with('success', "Relatório finalizado com sucesso.");
        }
        catch (\Throwable $th) {
            return redirect()->back()->with('error', "Erro ao finalizar formulário, tente novamente mais tarde! " . $th->getMessage());
        }
    }

    public function autoSave(Request $request)
    {
        $request->validate([
            'id_submissao' => 'required',
            'id_pergunta'  => 'required',
            'indice_grupo' => 'nullable|integer'
        ]);

        $indiceGrupo = $request->input('indice_grupo', 0); 
        $valor = $request->input('valor');

        if (is_array($valor)) {
            $valor = json_encode($valor);
        }

        // Busca a resposta existente e carrega a validação
        $respostaExistente = Resposta::with('validacao')
            ->where('id_submissao', $request->id_submissao)
            ->where('id_pergunta', $request->id_pergunta)
            ->where('indice_grupo', $indiceGrupo)
            ->first();

        // VALIDAÇÃO DE SEGURANÇA: Se já foi avaliado e aprovado (1), não permite alterar
        if ($respostaExistente && $respostaExistente->validacao && $respostaExistente->validacao->status == 1) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Este campo já foi aprovado pelo avaliador e não pode ser alterado.'
            ], 403);
        }

        // Tratamento de Upload de Arquivos
        if ($request->hasFile('file')) {
            if ($respostaExistente && !empty($respostaExistente->valor) && Storage::exists($respostaExistente->valor)) {
                Storage::delete($respostaExistente->valor);
            }
            $path = $request->file('file')->store('respostas/arquivos');
            $valor = $path;
        }

        if (!$request->hasFile('file') && $request->file === null && $respostaExistente && str_starts_with($respostaExistente->valor, 'respostas/arquivos')) {
            $valor = $respostaExistente->valor;
        }

        Resposta::updateOrCreate(
            [
                'id_submissao' => $request->id_submissao,
                'id_pergunta'  => $request->id_pergunta,
                'indice_grupo' => $indiceGrupo
            ],
            [
                'valor' => $valor ?? null // Salva nulo se vazio
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

        $subPerguntasIds = Pergunta::where('id_pergunta_pai', $request->id_pergunta_pai)->pluck('id');

        if ($subPerguntasIds->isNotEmpty()) {
            
            // SEGURANÇA: Verificar se alguma célula dessa linha já está aprovada
            $respostasAprovadas = Resposta::where('id_submissao', $request->id_submissao)
                ->whereIn('id_pergunta', $subPerguntasIds)
                ->where('indice_grupo', $request->indice_grupo)
                ->whereHas('validacao', function($query) { $query->where('status', 1); })
                ->exists();

            if($respostasAprovadas) {
                return response()->json(['status' => 'error', 'message' => 'Esta linha contém campos aprovados e não pode ser removida.'], 403);
            }

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

            Resposta::where('id_submissao', $request->id_submissao)
                ->whereIn('id_pergunta', $subPerguntasIds)
                ->where('indice_grupo', $request->indice_grupo)
                ->delete();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Item removido do relatório com sucesso!',
            'progresso' => $this->getProgress($request->id_submissao)
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
                                'opcoes' => $filha->opcoes ? str_replace([',', '"', ':', '{', '}', '[', ']'], [', ', '', ': ', '', '', '', ''], json_encode($filha->opcoes->pluck('rotulo'))) : '', 
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
                        'opcoes' => $pergunta->opcoes ? str_replace([',', '"', ':', '{', '}', '[', ']'], [', ', '', ': ', '', '', '', ''], json_encode($pergunta->opcoes->pluck('rotulo'))) : '', 
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
                        'opcoes' => $pergunta->opcoes ? str_replace([',', '"', ':', '{', '}', '[', ']'], [', ', '', ': ', '', '', '', ''], json_encode($pergunta->opcoes->pluck('rotulo'))) : '', 
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
            // dd($request->previous);
            $urlDestino = $request->previous;

            // Segurança: Se por algum motivo a URL for vazia ou for a rota atual do POST, define uma rota padrão
            if (empty($urlDestino) || $urlDestino == url()->current()) {
                return redirect()->route('report.monitor', $submissao->id_relatorio)->with('success', 'Avaliação da submissão salva e processada com sucesso!');
            }

            return redirect()->to($urlDestino)->with('success', 'Avaliação da submissão salva e processada com sucesso!');

        } catch (\Exception $e) {
            // Se der qualquer erro, cancela tudo que foi feito no banco
            DB::rollBack();
            
            // Retorna para a tela de validação com o erro e os dados preenchidos (withInput ajuda o old() da view)
            return back()->withInput()->with('error', 'Ocorreu um erro ao salvar a avaliação: ' . $e->getMessage());
        }
    }

    /**
     * Função auxiliar para validar campos obrigatórios no Backend antes de finalizar
     */
    private function validarObrigatorias($submissao)
    {
        foreach ($submissao->relatorio->formulario->secoes as $secao) {
            foreach ($secao->perguntas->whereNull('id_pergunta_pai') as $pergunta) {
                if ($pergunta->tipo === 'tabela') {
                    $gruposDeRespostas = Resposta::whereIn('id_pergunta', $pergunta->filhas->pluck('id'))
                        ->where('id_submissao', $submissao->id)
                        ->get()
                        ->groupBy('indice_grupo');
                    
                    // Se a tabela for obrigatória e não tiver nenhuma linha, retorna falso
                    if ($pergunta->obrigatoria && $gruposDeRespostas->isEmpty()) return false;

                    foreach ($gruposDeRespostas as $respostasLinha) {
                        foreach ($pergunta->filhas as $filha) {
                            if ($filha->obrigatoria) {
                                $resp = $respostasLinha->where('id_pergunta', $filha->id)->first();
                                if (!$resp || empty($resp->valor)) return false;
                            }
                        }
                    }
                } else {
                    if ($pergunta->obrigatoria) {
                        $resposta = $pergunta->getRespostaPorSubmissao($submissao->id);
                        if (!$resposta || empty($resposta->valor)) return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * Função auxiliar para criar as respostas com valor NULL para o que não foi respondido
     */
    private function salvarRespostasNulasEIgnoradas($submissao)
    {
        foreach ($submissao->relatorio->formulario->secoes as $secao) {
            foreach ($secao->perguntas->whereNull('id_pergunta_pai') as $pergunta) {
                if ($pergunta->tipo !== 'tabela') {
                    Resposta::firstOrCreate(
                        ['id_submissao' => $submissao->id, 'id_pergunta' => $pergunta->id, 'indice_grupo' => 0],
                        ['valor' => null]
                    );
                } else {
                    // 1. Pega todos os índices (linhas da tabela) que o usuário chegou a responder
                    $indicesRespondidos = Resposta::whereIn('id_pergunta', $pergunta->filhas->pluck('id'))
                        ->where('id_submissao', $submissao->id)
                        ->distinct()
                        ->pluck('indice_grupo');
                    
                    // 2. Se não respondeu absolutamente nada, cria a linha 0 toda nula
                    if ($indicesRespondidos->isEmpty()) {
                        foreach ($pergunta->filhas as $filha) {
                            Resposta::firstOrCreate(
                                ['id_submissao' => $submissao->id, 'id_pergunta' => $filha->id, 'indice_grupo' => 0],
                                ['valor' => null]
                            );
                        }
                    } else {
                        // 3. Se respondeu alguma linha, percorre esses índices
                        foreach ($indicesRespondidos as $indice) {
                            foreach ($pergunta->filhas as $filha) {
                                // O firstOrCreate é perfeito aqui: se a resposta obrigatória já existir, 
                                // ele não faz nada. Se a opcional não existir, ele cria com null.
                                Resposta::firstOrCreate(
                                    ['id_submissao' => $submissao->id, 'id_pergunta' => $filha->id, 'indice_grupo' => $indice],
                                    ['valor' => null] 
                                );
                            }
                        }
                    }
                }
            }
        }
    }
}