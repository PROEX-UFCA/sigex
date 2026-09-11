<?php

namespace App\Http\Controllers\Web\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Report\StoreRequest;
use App\Http\Requests\Web\Report\UpdateRequest;
use App\Jobs\SendReportNotificationsJob;
use App\Mail\ReportCreatedMail;
use App\Models\Acao;
use App\Models\Equipe_Acao;
use App\Models\Instituicao_Externa;
use App\Models\Pergunta;
use App\Models\Relatorio;
use App\Models\Resposta;
use App\Models\Submissao;
use App\Models\User;
use App\Models\ValidacaoResposta;
use App\Repositories\Actions\ActionsRepository;
use App\Repositories\Forms\FormsRepository;
use App\Repositories\Parametros\ParametrosRepository;
use App\Repositories\Reports\ReportsRepository;
use App\Repositories\Settings\User\UsersRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Console\ViewClearCommand;

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

    public function edit(Request $request, $uuid)
    {
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('dir', 'desc') === 'asc' ? 'asc' : 'desc';
        $allowedFields = ['titulo', 'finalizada_em', 'created_at'];

        if (!in_array($sort, $allowedFields)) {
            $sort = 'created_at';
        }

        $this->data['relatorio'] = $this->reportRepository->getById($uuid);
        $this->data['submissao'] = $this->data['relatorio']; 
        $this->data['submissoes'] = $this->reportRepository->getSubmissionsByIdReport($uuid, $request->query(), $sort, $direction);

        // Parâmetros para novos filtros
        $this->data['parametros'] = $this->parametrosRepository->getAllActiveByFunctions(['TIPO', 'MODALIDADE_EDITAL', 'SITUACAO'])->groupBy('function');
        $this->data['parametros_membros'] = $this->parametrosRepository->getAllActiveByFunctions(['TIPO_MEMBRO', 'CATEGORIA_MEMBRO', 'STATUS_MEMBROS'])->groupBy('function');

        // Mapeia IDs já cadastrados para aplicar a interseção/exclusão
        $existingAcaoIds = $this->data['relatorio']->submissoes->pluck('id_acao')->filter()->toArray();
        $existingUserIds = $this->data['relatorio']->submissoes->pluck('id_usuario')->filter()->toArray();

        $this->data['who'] = $request->input('who', old('who', ''));
        $this->data['items'] = collect();

        if ($request->isMethod('post') && !empty($this->data['who'])) {
            switch ($this->data['who']) {
                case 'acoes':
                    $rawItems = $this->actionsRepository->getActionsForReports($request->only(['parametros', 'is_ej']));
                    // Filtra removendo ações que já possuem submissão neste relatório
                    $this->data['items'] = $rawItems->filter(function ($item) use ($existingAcaoIds) {
                        return !in_array($item->id, $existingAcaoIds);
                    });
                    break;

                case 'membros':
                    $rawItems = $this->actionsRepository->getMembersForReports($request->only(['parametros']));
                    // Filtra removendo membros que já possuem submissão vinculada
                    $this->data['items'] = $rawItems->filter(function ($item) use ($existingUserIds, $existingAcaoIds) {
                        $userId = $item->user->id ?? $item->id_usuario ?? null;
                        $actionId = $item->action->id ?? $item->id_acao ?? null;
                        return !in_array($userId, $existingUserIds) || !in_array($actionId, $existingAcaoIds);
                    });
                    break;

                case 'usuarios':
                    $rawItems = $this->usersRepository->getForCoordinator();
                    // Filtra removendo usuários que já possuem submissão neste relatório
                    $this->data['items'] = $rawItems->filter(function ($item) use ($existingUserIds) {
                        return !in_array($item->id, $existingUserIds);
                    });
                    break;
            }
        }

        return view('pages.report.edit', $this->data);
    }

    public function addSubmissions(Request $request, $uuid)
    {
        try {
            $report = $this->reportRepository->getById($uuid);

            if (!$request->has('target_ids') || empty($request->target_ids)) {
                return redirect()->back()->with('error', 'Nenhum destinatário foi selecionado.');
            }

            $submissoes = [];
            $notificacoes = [];

            foreach ($request->target_ids as $targetJson) {
                $target = json_decode($targetJson, true);
                $idAcao = $target['id_acao'] ?? null;
                $idUsuario = $target['id_usuario'] ?? null;
                $submissionId = (string) Str::uuid();

                $submissoes[] = [
                    'id' => $submissionId,
                    'id_relatorio' => $report->id,
                    'id_acao' => $idAcao,
                    'id_usuario' => $idUsuario,
                    'finalizada_em' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $notificacoes[] = [
                    'submission_id' => $submissionId,
                    'id_acao' => $idAcao,
                    'id_usuario' => $idUsuario,
                ];
            }

            if (!empty($submissoes)) {
                $this->reportRepository->bulkInsertSubmission($submissoes);

                // Disparo de notificações por e-mail para os novos cadastrados
                foreach ($notificacoes as $item) {
                    $email = null;
                    $nomeAcao = null;
                    $nome = null;

                    if (!empty($item['id_acao'])) {
                        $acao = Acao::find($item['id_acao']);
                        if ($acao) {
                            $nomeAcao = $acao->titulo;
                            $coordenador = $acao->coordenador();
                            $email = $coordenador->user->email ?? $coordenador->email ?? null;
                            $nome = $coordenador->user->name ?? $coordenador->name ?? null;
                        }
                    }

                    if (empty($email) && !empty($item['id_usuario'])) {
                        $usuario = User::find($item['id_usuario']);
                        $email = $usuario->email ?? null;
                        $nome = $usuario->name ?? null;
                    }

                    if ($email) {
                        $url = route('actions.report', $item['submission_id']);
                        Mail::to($email)->queue(new ReportCreatedMail([
                            'nome' => $nome,
                            'titulo_relatorio' => $report->titulo,
                            'nome_acao' => $nomeAcao,
                            'data_inicio' => date('d/m/Y H:i:s', strtotime($report->data_inicio)),
                            'prazo' => date('d/m/Y H:i:s', strtotime($report->prazo)),
                            'url' => $url,
                        ]));
                    }
                }
            }

            return redirect()->route('report.edit', $uuid)->with('success', "Novos destinatários vinculados com sucesso.");
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', "Erro ao vincular novos destinatários.");
        }
    }

    public function store(StoreRequest $request)
    {
        try {
            $report = $this->reportRepository->create($request);

            $submissoes = [];
            $targets = [];

            // 1. Extrai os alvos em um único loop O(N)
            foreach ($request->target_ids as $targetJson) {
                $target = json_decode($targetJson, true);

                $idAcao = $target['id_acao'] ?? null;
                $idUsuario = $target['id_usuario'] ?? null;
                $submissionId = (string) Str::uuid();
                
                $submissoes[] = [
                    'id' => $submissionId,
                    'id_relatorio' => $report->id,
                    'id_acao' => $idAcao,
                    'id_usuario' => $idUsuario,
                    'finalizada_em' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $targets[] = [
                    'id_acao' => $idAcao,
                    'id_usuario' => $idUsuario,
                ];
            }

            if (!empty($submissoes)) {
                // Bulk insert rápido
                $this->reportRepository->bulkInsertSubmission($submissoes);

                // 2. Pré-carrega ações e usuários em APENAS 2 CONSULTAS SQL (Evita N+1)
                $actionIds = array_filter(array_column($targets, 'id_acao'));
                $userIds = array_filter(array_column($targets, 'id_usuario'));

                $acoes = !empty($actionIds) ? Acao::whereIn('id', $actionIds)->get()->keyBy('id') : collect();
                $usuarios = !empty($userIds) ? User::whereIn('id', $userIds)->get()->keyBy('id') : collect();

                // 3. Dispara cada e-mail para a fila individualmente (executa em < 100ms)
                foreach ($targets as $item) {
                    $email = null;
                    $nomeAcao = null;
                    $nome = null;

                    if (!empty($item['id_acao']) && isset($acoes[$item['id_acao']])) {
                        $acao = $acoes[$item['id_acao']];
                        $nomeAcao = $acao->titulo;
                        $coordenador = $acao->coordenador();
                        $email = $coordenador->user->email ?? $coordenador->email ?? null;
                        $nome = $coordenador->user->name ?? $coordenador->name ?? null;
                    }

                    if (empty($email) && !empty($item['id_usuario']) && isset($usuarios[$item['id_usuario']])) {
                        $usuario = $usuarios[$item['id_usuario']];
                        $email = $usuario->email ?? null;
                        $nome = $usuario->name ?? null;
                    }

                    if ($email) {
                        // Cada Mail::to()->queue gera um Job isolado no banco/redis
                        Mail::to($email)->queue(new ReportCreatedMail([
                            'nome' => $nome,
                            'titulo_relatorio' => $report->titulo,
                            'nome_acao' => $nomeAcao,
                            'data_inicio' => date('d/m/Y H:i:s', strtotime($report->data_inicio)),
                            'prazo' => date('d/m/Y H:i:s', strtotime($report->prazo)),
                            'url' => route('login'),
                        ]));
                    }
                }
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

    public function finish(Request $request, $uuid){
        
        $request->validate(['aceite' => 'required|accepted',], [
            'aceite.required' => 'Você precisa marcar a caixa de consentimento para prosseguir.',
            'aceite.accepted' => 'O termo de ciência deve ser aceito.',
        ]);

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

            $title = 'Relatorio_' . date('Y-m-d_H-i');
            $pdf = Pdf::loadView('pages.report.exports.pdf2', compact('title', 'submissao'));
            // return view('pages.report.exports.pdf2', compact('title', 'submissao'));

            $pdfContent = base64_encode($pdf->setPaper('a4', 'landscape')->output());

            return to_route('actions.my')
                ->with('success', "Relatório finalizado com sucesso. Clique <a href='https://sig.ufca.edu.br/sigaa/public/home.jsf' class='fw-bol' target='_blank'><strong>Aqui</strong></a> para anexar no sigaa.")
                ->with('pdf_content', $pdfContent)
                ->with('pdf_name', "{$title}.pdf");

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

    public function delete_submission($uuid){
        try{
            Submissao::findOrFail($uuid)->destroy($uuid);
            return redirect()->back()->with('success', 'Submissão deletada com sucesso.');
        } catch (\Throwable $err) {
            return redirect()->back()->with('error', 'Erro ao deletar submissão. Por favor, tente novamente mais tarde.');
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

    public function download(Request $request)
    {
        $this->data['reports'] = Relatorio::get();
        $this->data['parametros_acao'] = $this->parametrosRepository->getAllActiveByFunctions(['TIPO', 'MODALIDADE_EDITAL', 'ÁREA_TEMÁTICA', 'SITUACAO'])->groupBy('function');
        $this->data['anos'] = Acao::get()->groupBy('ano');
        $this->data['parametros_membros'] = $this->parametrosRepository->getAllActiveByFunctions(['TIPO_MEMBRO', 'CATEGORIA_MEMBRO', 'STATUS_MEMBROS'])->groupBy('function');

        return view('pages.report.download', $this->data);
    }

    public function baixar(Request $request)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        // 1. Validação dos parâmetros obrigatórios
        $request->validate([
            'what'   => 'required|in:relatorios,acoes,membros,instituicoes',
            'format' => 'required|in:pdf,excel,csv',
        ]);

        $what   = $request->input('what');
        $format = $request->input('format');

        $data    = collect();
        $title   = '';
        $headers = [];

        // 2. Montagem dos dados conforme o tipo selecionado
        switch ($what) {
            case 'relatorios':
                $title = 'Relatorio_Submissoes_' . date('Y-m-d_H-i');
                $whatReport = $request->input('what_report');
                $quais      = $request->input('quais', 'todos');

                // Consulta no repositório de relatórios/submissões
                $submissoes = $this->reportRepository->getSubmissionsForDownload($whatReport, $quais);

                $headers = ['Título do Relatório', 'Destinatário/Ação', 'Data Início', 'Prazo', 'Finalizado'];

                $perguntas = collect();

                // Identifica o modelo do Relatório e seu formulário vinculado
                if ($whatReport) {
                    $reportModel = Relatorio::find($whatReport);
                    $formulario  = $reportModel->formulario ?? $reportModel->relatorio->formulario ?? null;
                } else {
                    // Se baixou "Todos os relatórios", pega a estrutura de formulário da primeira submissão existente
                    $primeiraSubmissao = $submissoes->first();
                    $formulario = $primeiraSubmissao->relatorio->formulario ?? $primeiraSubmissao->formulario ?? null;
                }

                // Mapeia todas as perguntas principais (cabeçalhos dinâmicos)
                if ($formulario && isset($formulario->secoes)) {
                    foreach ($formulario->secoes as $secao) {
                        // Filtra apenas perguntas principais (não filhas de tabelas diretamente no topo)
                        $perguntasPrincipais = $secao->perguntas ? $secao->perguntas->whereNull('id_pergunta_pai') : collect();

                        foreach ($perguntasPrincipais as $pergunta) {
                            $headers[] = $pergunta->enunciado;
                            $perguntas->push($pergunta);
                        }
                    }
                }

                // Monta os dados de cada submissão combinando metadados + respostas dinâmicas
                $data = $submissoes->map(function ($item) use ($perguntas) {
                    $linha = [
                        'titulo'      => $item->relatorio->titulo ?? $item->titulo ?? 'N/A',
                        'destinatario'=> $item->action->titulo ?? $item->user->name ?? 'N/A',
                        'data_inicio' => isset($item->relatorio->data_inicio) ? date('d/m/Y H:i', strtotime($item->relatorio->data_inicio)) : '',
                        'prazo'       => isset($item->relatorio->prazo) ? date('d/m/Y H:i', strtotime($item->relatorio->prazo)) : '',
                        'finalizado'  => $item->finalizada_em ? date('d/m/Y H:i', strtotime($item->finalizada_em)) : 'Não finalizado',
                    ];

                    // Mapeia a resposta correspondente para cada pergunta do formulário
                    foreach ($perguntas as $pergunta) {
                        $linha['pergunta_' . $pergunta->id] = $this->formatarRespostaParaExportacao($pergunta, $item->id);
                    }

                    return $linha;
                });
                break;
            case 'acoes':
                $title = 'Relatorio_Acoes_' . date('Y-m-d_H-i');
                $filters = [
                    'parametros' => $request->input('parametros', []),
                    'anos'       => $request->input('anos', []),
                    'is_ej'      => $request->input('is_ej', 'todas'),
                ];

                // Consulta ações filtradas
                $acoes = $this->actionsRepository->getActionsForReports($filters);

                $headers = ['id_projeto', 'coordenador(a)', 'ano', 'titulo', 'modalidade edital', 'bolsas solicitadas', 'bolsas concedidas', 'financiamento interno', 'financiamento externo', 'situacao', 'data cadastro', 'data inicio', 'data fim', 'data atualizacao', 'centro departamento sigla', 'tipo acao', 'area tematica', 'palavras chave', 'ods', 'empresa junior'];
                
                $data = $acoes->map(function ($acao) {
                    $coordenador = method_exists($acao, 'coordenador') ? $acao->coordenador() : null;
                    return [
                        'id_projeto'  => $acao->id_projeto,
                        'coordenador(a)' => $coordenador->user->name ?? $coordenador->nome ?? 'N/A',
                        'ano'      => $acao->ano ?? 'N/A',
                        'titulo'      => $acao->titulo ?? 'N/A',
                        'modalidade edital'  => $acao->modalidade_edital ?? 'N/A',
                        'bolsas solicitadas'        => $acao->bolsas_solicitadas ?? 'N/A',
                        'bolsas concedidas'        => $acao->bolsas_concedidas ?? 'N/A',
                        'financiamento interno'        => $acao->financiamento_interno ?? 'N/A',
                        'financiamento externo'        => $acao->financiamento_externo ?? 'N/A',
                        'situacao'        => $acao->situacao ?? 'N/A',
                        'data cadastro'        => $acao->data_cadastro ?? 'N/A',
                        'data inicio'        => $acao->data_inicio ?? 'N/A',
                        'data fim'        => $acao->data_fim ?? 'N/A',
                        'data atualizacao'        => $acao->data_atualizacao ?? 'N/A',
                        'centro departamento sigla'        => $acao->centro_departamento_sigla ?? 'N/A',
                        'tipo acao'        => $acao->tipo_acao ?? 'N/A',
                        'area tematica'        => $acao->area_tematica ?? 'N/A',
                        'palavras chave'  => $acao->palavras_chave ?? 'N/A',
                        'ods'    => $acao->ods ?? 'N/A',
                        'ej?'       => $acao->is_ej ? 'Sim' : 'Não',
                    ];
                });
                break;

            case 'membros':
                $title = 'Relatorio_Membros_' . date('Y-m-d_H-i');
                $filters = [
                    'parametros' => $request->input('parametros', []),
                ];

                // Consulta membros filtrados
                $membros = $this->actionsRepository->getMembersForReports($filters);
                
                $headers = ['ID', 'Nome do Membro', 'Ação Relacionada', 'Tipo de Membro', 'Categoria', 'Status'];
                $data = $membros->map(function ($membro) {
                    return [
                        'id'        => $membro->id,
                        'nome'      => $membro->user->name ?? $membro->nome ?? 'N/A',
                        'acao'      => $membro->action->titulo ?? 'N/A',
                        'tipo'      => $membro->tipo_membro ?? 'N/A',
                        'categoria' => $membro->categoria_membro ?? 'N/A',
                        'status'    => $membro->status ?? 'N/A',
                    ];
                });
                break;

            case 'instituicoes':
                $title = 'Relatorio_Instituicoes_' . date('Y-m-d_H-i');
                $instituicoes = Instituicao_Externa::get();

                $headers = ['ID', 'Nome da Instituição', 'CNPJ/Identificador', 'Cep', 'Logradouro', 'Numero', 'Complemento', 'Telefone_contato', 'Status'];
                $data = $instituicoes->map(function ($inst) {
                    return [
                        'id'     => $inst->id,
                        'nome'   => $inst->nome ?? 'N/A',
                        'cnpj'   => $inst->cnpj ?? 'N/A',
                        'cep' => $inst->cidade ?? 'N/A',
                        'logradouro' => $inst->cidade ?? 'N/A',
                        'numero' => $inst->cidade ?? 'N/A',
                        'complemento' => $inst->estado ?? 'N/A',
                        'telefone_contato' => $inst->estado ?? 'N/A',
                        'status' => $inst->estado ?? 'N/A',
                    ];
                });
                break;
        }

        // 3. Redireciona para o tipo de exportação solicitado
        if ($format === 'pdf') {
            return $this->exportToPdf($title, $headers, $data);
        } elseif ($format === 'excel') {
            return $this->exportToExcel($title, $headers, $data);
        } else {
            return $this->exportToCsv($title, $headers, $data);
        }
    }

    /**
     * Gera o arquivo PDF usando DomPDF
     */
    private function exportToPdf(string $title, array $headers, $data)
    {
        // return view('pages.report.exports.pdf', compact('title', 'headers', 'data'));
        $pdf = Pdf::loadView('pages.report.exports.pdf', compact('title', 'headers', 'data'));
        return $pdf->setPaper('tabloid', 'landscape')->download("{$title}.pdf");
    }

    /**
     * Gera o arquivo CSV nativo sem necessidade de pacotes externos
     */
    private function exportToCsv(string $title, array $headers, $data)
    {
        $fileName = "{$title}.csv";

        return response()->streamDownload(function () use ($headers, $data) {
            $file = fopen('php://output', 'w');
            
            // Adiciona UTF-8 BOM para garantir acentuação correta no Excel em português
            fputs($file, "\xEF\xBB\xBF");

            // Linha de Cabeçalho (delimitador ponto e vírgula ';')
            fputcsv($file, $headers, ';');

            // Linhas de dados
            foreach ($data as $row) {
                fputcsv($file, (array) $row, ';');
            }

            fclose($file);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Gera o arquivo Excel (.xlsx) ou CSV com codificação Excel
     */
    private function exportToExcel(string $title, array $headers, $data)
    {
        // Se você tiver o pacote Maatwebsite\Excel instalado:
        // return Excel::download(new GenericExport($headers, $data), "{$title}.xlsx");

        // Caso contrário, gera um CSV otimizado para abertura no Microsoft Excel:
        return $this->exportToCsv($title, $headers, $data);
    }

    /**
    * Formata as respostas de uma pergunta para exibição na célula do relatório baixado (PDF, Excel, CSV).
    */
    /**
    * Formata as respostas de uma pergunta para exibição em exportações (Excel, CSV, PDF, etc.).
    */
    private function formatarRespostaParaExportacao($pergunta, string $idSubmissao): string
    {
        $tipo = strtolower($pergunta->tipo ?? '');

        // 1. TRATAMENTO PARA PERGUNTAS DO TIPO TABELA DINÂMICA
        if (in_array($tipo, ['tabela', 'tabela_dinamica', 'table'])) {
            $filhas = $pergunta->filhas;

            if ($filhas && $filhas->count() > 0) {
                // Busca todas as respostas das colunas/perguntas filhas desta submissão
                $respostasFilhas = \App\Models\Resposta::whereIn('id_pergunta', $filhas->pluck('id'))
                    ->where('id_submissao', $idSubmissao)
                    ->get();

                if ($respostasFilhas->isEmpty()) {
                    return '-';
                }

                // Agrupa as respostas por linha/registro da tabela
                $gruposLinha = $respostasFilhas->groupBy(function ($resp) {
                    return $resp->indice_grupo;
                });

                $linhasTexto = [];
                $numLinha = 1;

                foreach ($gruposLinha as $respostasDaLinha) {
                    $colunasTexto = [];

                    foreach ($filhas as $filha) {
                        $resp = $respostasDaLinha->firstWhere('id_pergunta', $filha->id);

                        $nomeColuna  = $filha->enunciado ?? 'Coluna';
                        $valorColuna = $resp
                            ? $this->tratarValorUnitario($resp->valor, $filha->tipo ?? 'text')
                            : '-';

                        if ($valorColuna !== '' && $valorColuna !== '-') {
                            $colunasTexto[] = [
                                'nome'  => $nomeColuna,
                                'valor' => $valorColuna
                            ];
                        }
                    }

                    if (!empty($colunasTexto)) {
                        $linhasTexto[] = [
                            'numero' => $numLinha,
                            'colunas' => $colunasTexto
                        ];

                        $numLinha++;
                    }
                }

                if (empty($linhasTexto)) {
                    return '-';
                }

                $html = '<table style="width: 100%; border-collapse: collapse;">';

                // Cabeçalho
                $html .= '<thead>';
                $html .= '<tr>';

                $html .= '<th style="border: 1px solid #ddd; padding: 5px;">#</th>';

                foreach ($filhas as $filha) {
                    $nomeColuna = $filha->enunciado ?? 'Coluna';

                    $html .= '<th style="border: 1px solid #ddd; padding: 5px;">'
                        . e($nomeColuna)
                        . '</th>';
                }

                $html .= '</tr>';
                $html .= '</thead>';

                // Dados
                $html .= '<tbody>';

                $numLinha = 1;

                foreach ($gruposLinha as $respostasDaLinha) {

                    $html .= '<tr>';

                    $html .= '<td>[' . $numLinha . ']</td>';

                    foreach ($filhas as $filha) {

                        $resp = $respostasDaLinha->firstWhere(
                            'id_pergunta',
                            $filha->id
                        );

                        $valorColuna = $resp
                            ? $this->tratarValorUnitario(
                                $resp->valor,
                                $filha->tipo ?? 'text'
                            )
                            : '-';

                        $html .= '<td>' . $valorColuna . '</td>';
                    }

                    $html .= '</tr>';

                    $numLinha++;
                }

                $html .= '</tbody>';
                $html .= '</table>';

                return $html;
            }
        }

        // 2. TRATAMENTO PARA DEMAIS TIPOS DE PERGUNTAS (Texto, Select, Radio, Checkbox, Arquivo, Localização, etc.)
        $respostas = $pergunta->respostas()->where('id_submissao', $idSubmissao)->get();

        if ($respostas->isEmpty()) {
            return '-';
        }

        $valoresFormatados = [];
        foreach ($respostas as $resposta) {
            $valorTratado = $this->tratarValorUnitario($resposta->valor, $tipo);
            if ($valorTratado !== '' && $valorTratado !== '-') {
                $valoresFormatados[] = $valorTratado;
            }
        }

        if (empty($valoresFormatados)) {
            return '-';
        }

        return implode("\n", $valoresFormatados);
    }

    /**
     * Trata o valor unitário da resposta de acordo com o tipo da pergunta.
     */
    private function tratarValorUnitario($valor, string $tipoPergunta = ''): string
    {
        if (is_null($valor) || $valor === '' || $valor === 'Não respondido') {
            return '-';
        }

        $tipo = strtolower($tipoPergunta);

        // 1. ARQUIVOS / IMAGENS / DOCUMENTOS -> Retorna o caminho (path) exatamente como está no banco
        if (in_array($tipo, ['file', 'arquivo', 'imagem', 'image', 'documento'])) {
            // Caso múltiplos arquivos tenham sido salvos em formato JSON
            if (is_string($valor) && (str_starts_with($valor, '[') || str_starts_with($valor, '{'))) {
                $decoded = json_decode($valor, true);
                if (is_array($decoded)) {
                    return "<a href=".route('arquivo.visualizar', ['path' => implode("\n", array_filter($decoded))])." target='_blank'>Abrir</a>";
                }
            }
            return "<a href=".route('arquivo.visualizar', ['path' => $valor])." target='_blank'>Abrir</a>"; // Ex: "uploads/submissoes/comprovante.pdf"
        }

        // 2. TRATAMENTO DE VALORES GRAVADOS EM JSON (Localização, Checkbox, Múltipla Escolha)
        if (is_string($valor) && (str_starts_with($valor, '{') || str_starts_with($valor, '['))) {
            $decoded = json_decode($valor, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {

                // Caso A: Localização / Mapa
                if (in_array($tipo, ['local', 'localizacao', 'location', 'mapa']) || isset($decoded['address']) || isset($decoded['endereco']) || isset($decoded['lat'])) {
                    $partes = [];
                    if (!empty($decoded['endereco'])) {
                        $partes[] = $decoded['endereco'];
                    } elseif (!empty($decoded['address'])) {
                        $partes[] = $decoded['address'];
                    } elseif (!empty($decoded['nome'])) {
                        $partes[] = $decoded['nome'];
                    }

                    if (isset($decoded['latitude']) && isset($decoded['longitude'])) {
                        $partes[] = "(Lat: {$decoded['latitude']}, Lng: {$decoded['longitude']})";
                    } elseif (isset($decoded['lat']) && isset($decoded['lng'])) {
                        $partes[] = "(Lat: {$decoded['lat']}, Lng: {$decoded['lng']})";
                    }

                    return !empty($partes) ? implode(' ', $partes) : implode(', ', array_filter($decoded));
                }

                // Caso B: Múltipla Escolha / Lista de Itens
                $itens = array_map(function ($item) {
                    return is_array($item) ? implode(': ', $item) : $item;
                }, array_filter($decoded));

                return implode(', ', $itens);
            }
        }

        // 3. TEXTO SIMPLES / VALOR PADRÃO
        return (string) $valor;
    }
}