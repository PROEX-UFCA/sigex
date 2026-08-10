<?php

namespace App\Http\Controllers\Web\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Action\ScheduleRequest;
use App\Http\Requests\Web\Action\StoreRequest;
use App\Http\Requests\Web\Action\TeamRequest;
use App\Models\Acao;
use App\Models\Equipe_Acao;
use App\Models\Parametro;
use App\Models\User;
use App\Repositories\Actions\ActionsRepository;
use App\Repositories\Parametros\ParametrosRepository;
use App\Repositories\Settings\User\UsersRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ActionController extends Controller
{
    private $data = [];
    private $actionsRepository;
    private $parametrosRepository;
    private $usersRepository;
    private $userId;

    public function __construct(ActionsRepository $actionsRepository, ParametrosRepository $parametrosRepository, UsersRepository $usersRepository)
    {
        $this->actionsRepository = $actionsRepository;
        $this->parametrosRepository = $parametrosRepository;
        $this->usersRepository = $usersRepository;
        $this->userId = Auth::user()->id;
    }

    public function index(Request $request)
    {
        $sort = $request->get('sort', 'ano');
        $direction = $request->get('dir', 'desc') === 'asc' ? 'asc' : 'desc';

        $allowedFields = ['ano', 'titulo', 'status', 'data_inicio', 'data_fim', 'tipo_acao', 'modalidade_edital', 'area_tematica', 'centro_departamento_sigla'];

        if (!in_array($sort, $allowedFields)) $sort = 'ano';


        $this->data['actions'] = $this->actionsRepository->getByFilter($request->query(), $sort, $direction);
        $this->data['parametros'] = $this->parametrosRepository->getAllActiveByFunctions(['TIPO', 'MODALIDADE_EDITAL', 'CENTRO_DEPARTAMENTO_SIGLA', 'ÁREA_TEMÁTICA', 'SITUACAO'])->groupBy('function');
        
        return view('pages.actions.index', $this->data);
    }

    public function create(){

        $this->data['parametros'] = $this->parametrosRepository->getAllActiveByFunctions(['TIPO', 'MODALIDADE_EDITAL', 'CENTRO_DEPARTAMENTO_SIGLA', 'ÁREA_TEMÁTICA', 'SITUACAO', 'CONTEXTO'])->groupBy('function');
        $this->data['coordinators'] = $this->usersRepository->getForCoordinator();


        return view('pages.actions.create', $this->data);
    }

    public function store(StoreRequest $request){
        try {
            $this->actionsRepository->create($request);
            return redirect()->back()->with("success", "Ação cadastrada com sucesso.");
        } catch (\Throwable $th) {
            return redirect()->back()->with("error", "Erro ao cadastrar ação. Por favor, tente novamente mais tarde.")->withInput();
        }
    }

    public function storeTeam(TeamRequest $request, $id_acao){
        try {
            $action = $this->actionsRepository->getByUuid($id_acao);
            $this->actionsRepository->createTeam($request, $id_acao, $action);

            return redirect()->back()->with("success", "Usuário adicionado a ação com sucesso.");
        } catch (\Throwable $th) {
            return redirect()->back()->with("error", "Erro. Por favor, tente novamente mais tarde.")->withInput();
        }
    }

    public function storeSchedule(ScheduleRequest $request, $id_acao){
        try {
            $this->actionsRepository->createSchedule($request, $id_acao);
            return redirect()->to(url()->previous() . '#agenda-pane')->with("success", "Evento adicionado a ação com sucesso.");
        } catch (\Throwable $th) {
            return redirect()->to(url()->previous() . '#agenda-pane')->with("error", "Erro. Por favor, tente novamente mais tarde.")->withInput();
        }
    }

    public function updateSchedule(ScheduleRequest $request, $id_agenda){
        try {
            $this->actionsRepository->updateSchedule($request, $id_agenda);
            
            return redirect()->to(url()->previous() . '#agenda-pane')->with("success", "Evento atualizado com sucesso.");
        } catch (\Throwable $th) {
            return redirect()->to(url()->previous() . '#agenda-pane')->with("error", "Erro ao atualizar evento. Por favor, tente novamente mais tarde.")->withInput();
        }
    }

    public function deleteSchedule($id_agenda)
    {
        try {
            $this->actionsRepository->deleteSchedule($id_agenda);
            
            return redirect()->to(url()->previous() . '#agenda-pane')->with("success", "Evento removido da ação com sucesso.");
        } catch (\Throwable $th) {
            return redirect()->to(url()->previous() . '#agenda-pane')->with("error", "Erro ao remover o evento. Por favor, tente novamente mais tarde.");
        }
    }

    public function my(Request $request){

        $this->data['actions'] = $this->actionsRepository->getAllByUuid(Auth::user()->uuid, $request->query());

        // foreach ($this->data['actions']->action->submissoes as $submissao) {
        //     dd($$submissao->id);
        // }
        return view('pages.actions.my', $this->data);
    }

    public function details($uuid){

        $this->data['action'] = $this->actionsRepository->getByUserUuid(Auth::user()->uuid, $uuid)->action;
        $this->data['coordinators'] = $this->usersRepository->getForCoordinator();
        $this->data['parametros'] = $this->parametrosRepository->getAllActiveByFunctions(['CATEGORIA_MEMBRO', 'TIPO_MEMBRO', 'STATUS_MEMBROS', 'TIPO_VINCULO'])->groupBy('function');

        return view('pages.actions.details', $this->data);
    }

    public function previewImport(Request $request)
    {
        $cacheKey = 'import_preview_' . $this->userId;

        if ($request->hasFile('csv')) {
            $request->validate([
                'csv' => 'required|file|mimes:csv,txt|max:10240'
            ], [
                'csv.mimes' => 'O arquivo precisa ser um CSV válido.'
            ]);

            $file = $request->file('csv');
            $handle = fopen($file->getRealPath(), "r");

            $headerLine = fgets($handle);
            if ($headerLine === false) {
                fclose($handle);
                return redirect()->back()->with("error", "Arquivo CSV vazio ou inválido.");
            }

            $delimiter = substr_count($headerLine, ';') > substr_count($headerLine, ',') ? ';' : ',';

            $projectsData = [];
            $rowIndex = 1;
            $duplicadosIgnorados = 0;

            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $row = array_map(fn($field) => trim(mb_convert_encoding($field, 'UTF-8', 'auto')), $row);

                $checkData = [
                    'id_projeto' => $row[0] ?? null,
                    'ano' => $row[1] ?? null,
                    'titulo' => $row[2] ?? null,
                    'data_inicio' => $row[10] ?? null,
                    'data_fim' => $row[11] ?? null,
                ];

                if (Acao::where($checkData)->exists()) {
                    $duplicadosIgnorados++;
                    $rowIndex++;
                    continue; 
                }

                $errors = [];
                if (empty($row[0])) $errors['id_projeto'] = 'Campo obrigatório';
                if (empty($row[1])) $errors['ano'] = 'Campo obrigatório';
                if (empty($row[2])) $errors['titulo'] = 'Campo obrigatório';
                if (empty($row[3])) $errors['modalidade_edital'] = 'Campo obrigatório';
                if (!isset($row[4]) || trim((string) $row[4]) === '') $errors['bolsas_solicitadas'] = 'Campo obrigatório';
                if (!isset($row[5]) || trim((string) $row[5]) === '') $errors['bolsas_concedidas'] = 'Campo obrigatório';
                if (empty($row[6])) $errors['financiamento_interno'] = 'Campo obrigatório';
                if (empty($row[7])) $errors['financiamento_externo'] = 'Campo obrigatório';
                if (empty($row[8])) $errors['situacao'] = 'Campo obrigatório';
                if (empty($row[9])) $errors['data_cadastro'] = 'Campo obrigatório';
                if (empty($row[10])) $errors['data_inicio'] = 'Campo obrigatório';
                if (empty($row[11])) $errors['data_fim'] = 'Campo obrigatório';
                if (empty($row[12])) $errors['data_atualizacao'] = 'Campo obrigatório';
                if (empty($row[13])) $errors['centro_departamento_sigla'] = 'Campo obrigatório';
                if (empty($row[14])) $errors['tipo_acao'] = 'Campo obrigatório';
                if (empty($row[15])) $errors['area_tematica'] = 'Campo obrigatório';
                // if (empty($row[16])) $errors['resumo'] = 'Campo obrigatório';
                // if (empty($row[17])) $errors['palavras_chave'] = 'Campo obrigatório';
                // if (empty($row[18])) $errors['ods'] = 'Campo obrigatório';
                if (empty($row[19])) $errors['contexto'] = 'Campo obrigatório';

                $projectsData[$rowIndex] = [
                    'id_projeto' => $row[0] ?? null,
                    'ano' => $row[1] ?? null,
                    'titulo' => $row[2] ?? null,
                    'modalidade_edital' => $row[3] ?? null,
                    'bolsas_solicitadas' => $row[4] ?? null,
                    'bolsas_concedidas' => $row[5] ?? null,
                    'financiamento_interno' => $row[6] ?? null,
                    'financiamento_externo' => $row[7] ?? null,
                    'situacao' => $row[8] ?? null,
                    'data_cadastro' => $row[9] ?? null,
                    'data_inicio' => $row[10] ?? null,
                    'data_fim' => $row[11] ?? null,
                    'data_atualizacao' => $row[12] ?? null,
                    'centro_departamento_sigla' => $row[13] ?? null,
                    'tipo_acao' => $row[14] ?? null,
                    'area_tematica' => $row[15] ?? null,
                    'resumo' => $row[16] ?? null,
                    'palavras_chave' => $row[17] ?? null,
                    'ods' => $row[18] ?? null,
                    'contexto' => $row[19] ?? null,
                    'errors' => $errors,
                    'row_index' => $rowIndex
                ];
                $rowIndex++;
            }
            fclose($handle);

            Cache::put($cacheKey, [
                'projects' => $projectsData,
                'duplicados' => $duplicadosIgnorados
            ], now()->addHours(2));

        } else {
            $cacheData = Cache::get($cacheKey);
            if (!$cacheData || empty($cacheData['projects'])) {
                return redirect()->route('actions.index')->with('error', 'A sessão de importação expirou ou não há dados.');
            }
            
            $allProjects = $cacheData['projects'];
            $duplicadosIgnorados = $cacheData['duplicados'];
            $cacheFoiAtualizado = false;

            if ($request->filled('deleted_indexes')) {
                $deletedIndexes = explode(',', $request->deleted_indexes);
                foreach ($deletedIndexes as $idx) {
                    if (isset($allProjects[$idx])) {
                        unset($allProjects[$idx]);
                        $cacheFoiAtualizado = true;
                    }
                }
            }

            if ($request->has('projects')) {
                foreach ($request->projects as $index => $submittedData) {
                    if (isset($allProjects[$index])) {
                        $allProjects[$index] = array_merge($allProjects[$index], $submittedData);
                        
                        $errors = [];
                        if (empty($allProjects[$index]['id_projeto'])) $errors['id_projeto'] = 'Campo obrigatório';
                        if (empty($allProjects[$index]['ano'])) $errors['ano'] = 'Campo obrigatório';
                        if (empty($allProjects[$index]['titulo'])) $errors['titulo'] = 'Campo obrigatório';
                        if (empty($allProjects[$index]['modalidade_edital'])) $errors['modalidade_edital'] = 'Campo obrigatório';
                        if (!isset($allProjects[$index]['bolsas_solicitadas']) || trim((string) $allProjects[$index]['bolsas_solicitadas']) === '') $errors['bolsas_solicitadas'] = 'Campo obrigatório';
                        if (!isset($allProjects[$index]['bolsas_concedidas']) || trim((string) $allProjects[$index]['bolsas_concedidas']) === '') $errors['bolsas_concedidas'] = 'Campo obrigatório';
                        if (empty($allProjects[$index]['financiamento_interno'])) $errors['financiamento_interno'] = 'Campo obrigatório';
                        if (empty($allProjects[$index]['financiamento_externo'])) $errors['financiamento_externo'] = 'Campo obrigatório';
                        if (empty($allProjects[$index]['situacao'])) $errors['situacao'] = 'Campo obrigatório';
                        if (empty($allProjects[$index]['data_cadastro'])) $errors['data_cadastro'] = 'Campo obrigatório';
                        if (empty($allProjects[$index]['data_inicio'])) $errors['data_inicio'] = 'Campo obrigatório';
                        if (empty($allProjects[$index]['data_fim'])) $errors['data_fim'] = 'Campo obrigatório';
                        if (empty($allProjects[$index]['data_atualizacao'])) $errors['data_atualizacao'] = 'Campo obrigatório';
                        if (empty($allProjects[$index]['centro_departamento_sigla'])) $errors['centro_departamento_sigla'] = 'Campo obrigatório';
                        if (empty($allProjects[$index]['tipo_acao'])) $errors['tipo_acao'] = 'Campo obrigatório';
                        if (empty($allProjects[$index]['area_tematica'])) $errors['area_tematica'] = 'Campo obrigatório';
                        // if (empty($allProjects[$index]['resumo'])) $errors['resumo'] = 'Campo obrigatório';
                        // if (empty($allProjects[$index]['palavras_chave'])) $errors['palavras_chave'] = 'Campo obrigatório';
                        // if (empty($allProjects[$index]['ods'])) $errors['ods'] = 'Campo obrigatório';
                        if (empty($allProjects[$index]['contexto'])) $errors['contexto'] = 'Campo obrigatório';

                        $allProjects[$index]['errors'] = $errors;
                        $cacheFoiAtualizado = true;
                    }
                }
            }

            if ($cacheFoiAtualizado) {
                Cache::put($cacheKey, [
                    'projects' => $allProjects,
                    'duplicados' => $duplicadosIgnorados
                ], now()->addHours(2));
            }

            $projectsData = $allProjects;
        }

        $collection = collect($projectsData)->sortByDesc(fn($project) => !empty($project['errors']))->values();
        
        $totalErrors = $collection->filter(fn($item) => count($item['errors']) > 0)->count();

        $perPage = 50;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $collection->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $paginatedProjects = new LengthAwarePaginator(
            $currentItems, 
            $collection->count(), 
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return view('pages.actions.preview', compact('paginatedProjects', 'totalErrors', 'duplicadosIgnorados'));
    }

    public function storeImport(Request $request)
    {
        $cacheKey = 'import_preview_' . $this->userId;
        $cacheData = Cache::get($cacheKey);

        if (!$cacheData || empty($cacheData['projects'])) {
            return redirect()->route('actions.index')->with('error', 'Sessão de importação expirada ou sem dados válidos.');
        }

        $allProjects = $cacheData['projects'];
        $duplicadosIgnorados = $cacheData['duplicados'];

        if ($request->filled('deleted_indexes')) {
            $deletedIndexes = explode(',', $request->deleted_indexes);
            foreach ($deletedIndexes as $idx) {
                if (isset($allProjects[$idx])) {
                    unset($allProjects[$idx]);
                }
            }
        }

        if ($request->has('projects')) {
            foreach ($request->projects as $index => $submittedData) {
                if (isset($allProjects[$index])) {
                    $allProjects[$index] = array_merge($allProjects[$index], $submittedData);
                    
                    $errors = [];
                    if (empty($allProjects[$index]['id_projeto'])) $errors['id_projeto'] = 'Campo obrigatório';
                    if (empty($allProjects[$index]['ano'])) $errors['ano'] = 'Campo obrigatório';
                    if (empty($allProjects[$index]['titulo'])) $errors['titulo'] = 'Campo obrigatório';
                    if (empty($allProjects[$index]['modalidade_edital'])) $errors['modalidade_edital'] = 'Campo obrigatório';
                    if (!isset($allProjects[$index]['bolsas_solicitadas']) || trim((string) $allProjects[$index]['bolsas_solicitadas']) === '') $errors['bolsas_solicitadas'] = 'Campo obrigatório';
                    if (!isset($allProjects[$index]['bolsas_concedidas']) || trim((string) $allProjects[$index]['bolsas_concedidas']) === '') $errors['bolsas_concedidas'] = 'Campo obrigatório';
                    if (empty($allProjects[$index]['financiamento_interno'])) $errors['financiamento_interno'] = 'Campo obrigatório';
                    if (empty($allProjects[$index]['financiamento_externo'])) $errors['financiamento_externo'] = 'Campo obrigatório';
                    if (empty($allProjects[$index]['situacao'])) $errors['situacao'] = 'Campo obrigatório';
                    if (empty($allProjects[$index]['data_cadastro'])) $errors['data_cadastro'] = 'Campo obrigatório';
                    if (empty($allProjects[$index]['data_inicio'])) $errors['data_inicio'] = 'Campo obrigatório';
                    if (empty($allProjects[$index]['data_fim'])) $errors['data_fim'] = 'Campo obrigatório';
                    if (empty($allProjects[$index]['data_atualizacao'])) $errors['data_atualizacao'] = 'Campo obrigatório';
                    if (empty($allProjects[$index]['centro_departamento_sigla'])) $errors['centro_departamento_sigla'] = 'Campo obrigatório';
                    if (empty($allProjects[$index]['tipo_acao'])) $errors['tipo_acao'] = 'Campo obrigatório';
                    if (empty($allProjects[$index]['area_tematica'])) $errors['area_tematica'] = 'Campo obrigatório';
                    // if (empty($allProjects[$index]['resumo'])) $errors['resumo'] = 'Campo obrigatório';
                    // if (empty($allProjects[$index]['palavras_chave'])) $errors['palavras_chave'] = 'Campo obrigatório';
                    // if (empty($allProjects[$index]['ods'])) $errors['ods'] = 'Campo obrigatório';
                    if (empty($allProjects[$index]['contexto'])) $errors['contexto'] = 'Campo obrigatório';
                    
                    $allProjects[$index]['errors'] = $errors;
                }
            }
        }

        Cache::put($cacheKey, ['projects' => $allProjects, 'duplicados' => $duplicadosIgnorados], now()->addHours(2));

        $totalErrors = collect($allProjects)->filter(fn($item) => count($item['errors']) > 0)->count();

        if ($totalErrors > 0) {
            return redirect()->back()->with('error', "Não foi possível salvar. Ainda existem {$totalErrors} linha(s) com erro no lote. Corrija ou exclua as linhas.");
        }

        if (empty($allProjects)) {
            return redirect()->route('actions.index')->with('info', 'Nenhuma linha restou para ser importada.');
        }

        DB::beginTransaction();
        try {
            $acoesParaInserir = [];
            $agora = now();

            foreach ($allProjects as $linha) {
                
                $centroDepartamento = !empty($linha['centro_departamento_sigla']) 
                    ? Parametro::firstOrCreate([
                        'function' => 'CENTRO_DEPARTAMENTO_SIGLA', 
                        'value' => mb_strtoupper(trim($linha['centro_departamento_sigla']), 'UTF-8')
                    ]) : null;

                $tipoAcao = !empty($linha['tipo_acao']) 
                    ? Parametro::firstOrCreate([
                        'function' => 'TIPO', 
                        'value' => mb_strtoupper(trim($linha['tipo_acao']), 'UTF-8')
                    ]) : null;

                $areaTematica = !empty($linha['area_tematica']) 
                    ? Parametro::firstOrCreate([
                        'function' => 'AREA_TEMATICA', 
                        'value' => mb_strtoupper(trim($linha['area_tematica']), 'UTF-8')
                    ]) : null;

                $modalidade = !empty($linha['modalidade_edital']) 
                    ? Parametro::firstOrCreate([
                        'function' => 'MODALIDADE_EDITAL', 
                        'value' => mb_strtoupper(trim($linha['modalidade_edital']), 'UTF-8')
                    ]) : null;

                $situacao = !empty($linha['situacao']) 
                    ? Parametro::firstOrCreate([
                        'function' => 'SITUACAO', 
                        'value' => mb_strtoupper(trim($linha['situacao']), 'UTF-8')
                    ]) : null;

                $contexto = !empty($linha['contexto']) 
                    ? Parametro::firstOrCreate([
                        'function' => 'CONTEXTO', 
                        'value' => mb_strtoupper(trim($linha['contexto']), 'UTF-8')
                    ]) : null;

                $id = (string) Str::uuid();

                $acoesParaInserir[] = [
                    'id'=> $id,
                    'id_projeto' => $linha['id_projeto'] ?? null,
                    'ano' => $linha['ano'] ?? null,
                    'titulo' => $linha['titulo'] ?? null,
                    'modalidade_edital' => $modalidade ? $modalidade->value : null,
                    'bolsas_solicitadas' => $linha['bolsas_solicitadas'] ?? null,
                    'bolsas_concedidas' => $linha['bolsas_concedidas'] ?? null,
                    'financiamento_interno' => $linha['financiamento_interno'] ?? null,
                    'financiamento_externo' => $linha['financiamento_externo'] ?? null,
                    'situacao' => $situacao ? $situacao->value : null,
                    'data_cadastro' => $linha['data_cadastro'] ?? null,
                    'data_inicio' => $linha['data_inicio'] ?? null,
                    'data_fim' => $linha['data_fim'] ?? null,
                    'data_atualizacao' => $linha['data_atualizacao'] ?? null,
                    'centro_departamento_sigla' => $centroDepartamento ? $centroDepartamento->value : null,
                    'tipo_acao' => $tipoAcao ? $tipoAcao->value : null,
                    'area_tematica' => $areaTematica ? $areaTematica->value : null,
                    'resumo' => $linha['resumo'] ?? null,
                    'palavras_chave' => $linha['palavras_chave'] ?? null,
                    'ods' => $linha['ods'] ?? null,
                    'contexto' => $contexto ? $contexto->value : null,
                    'status' => 1,
                    'created_at' => $agora,
                    'updated_at' => $agora,
                ];
            }

            if (!empty($acoesParaInserir)) {
                Acao::insert($acoesParaInserir);

                $usuarioLogado = $this->userId;
                $acaoReferencia = Acao::first();
                
                if ($acaoReferencia && $usuarioLogado) {
                    activity()
                        ->causedBy($usuarioLogado)
                        ->performedOn($acaoReferencia)
                        ->event('importado')
                        ->withProperties([
                            'attributes' => [
                                'message' => 'Uma importação em lote de ' . count($acoesParaInserir) . ' ações foi realizada.',
                            ]
                        ])
                        ->log('importado');
                }
            }

            DB::commit();
            Cache::forget($cacheKey);

            return redirect()->route('actions.index')->with("success", "Ações importadas e salvas com sucesso!");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('actions.index')->with("error", "Erro ao salvar os dados: " . $e->getTraceAsString());
        }
    }

    public function edit($uuid){
        $this->data['action'] = $this->actionsRepository->getByUuid($uuid);
        $this->data['parametros'] = $this->parametrosRepository->getAllActiveByFunctions(['TIPO', 'MODALIDADE_EDITAL', 'CENTRO_DEPARTAMENTO_SIGLA', 'ÁREA_TEMÁTICA', 'SITUACAO', 'CONTEXTO'])->groupBy('function');
        $this->data['coordinators'] = $this->usersRepository->getForCoordinator();
        $this->data['actual_coordinator'] = Equipe_Acao::where(['id_acao' => $uuid, 'categoria_membro' => 'COORDENADOR(A)'])->first();

        return view('pages.actions.edit', $this->data);
    }

    public function update(Request $request, $id)
    {
        try {
            $this->actionsRepository->update($request, $id);

            return redirect()->back()->with("success", "Ação atualizada com sucesso");
        } catch (\Throwable $e) {
            return redirect()->back()->with("error", "Erro ao atualizar ação");
        }
    }

    public function addBanner(Request $request, $uuid)
    {
        $request->validate([
            'banner' => [
                'required',
                'file',
                'mimes:jpeg,png,jpg,webp',
                // 'max:5120',
            ],
        ], [
            'banner.required' => 'O banner é obrigatório.',
            'banner.file' => 'O banner deve ser um arquivo válido.',
            'banner.mimes' => 'O banner deve ser uma imagem (jpg, png, jpeg ou webp).',
            // 'banner.max' => 'O banner não pode ser maior que 5MB.',
        ]);

        $action = $this->actionsRepository->getByUuid($uuid);

        try {
            if ($request->hasFile("banner") && $request->file("banner")->isValid()) {
                
                if ($action->img && Storage::disk('public')->exists($action->img)) {
                    Storage::disk('public')->delete($action->img);
                }

                $path = $request->file("banner")->store('banner', 'public');
                
                $action->img = $path;
                $action->save();

                return redirect()->to(url()->previous() . '#banner-pane')->with('success', 'Banner adicionado com sucesso!');
            }

            return redirect()->to(url()->previous() . '#banner-pane')->with('error', 'O arquivo enviado não é válido.');

        } catch (\Throwable $th) {
            return redirect()->to(url()->previous() . '#banner-pane')->with('error', 'Erro ao tentar adicionar banner, tente novamente mais tarde.');
        }
    }
}
