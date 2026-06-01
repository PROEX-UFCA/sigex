<?php

namespace App\Http\Controllers\Web\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Action\ScheduleRequest;
use App\Http\Requests\Web\Action\StoreRequest;
use App\Http\Requests\Web\Action\TeamRequest;
use App\Models\Acao;
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

class ActionController extends Controller
{
    private $data;
    private $actionsRepository;
    private $parametrosRepository;
    private $usersRepository;

    public function __construct(ActionsRepository $actionsRepository, ParametrosRepository $parametrosRepository, UsersRepository $usersRepository)
    {
        $this->actionsRepository = $actionsRepository;
        $this->parametrosRepository = $parametrosRepository;
        $this->usersRepository = $usersRepository;
    }

    public function index(Request $request)
    {
        $sort = $request->get('sort', 'ano');
        $direction = $request->get('dir', 'desc') === 'asc' ? 'asc' : 'desc';

        $allowedFields = ['ano', 'titulo', 'status', 'data_inicio', 'data_fim', 'tipo_acao', 'modalidade', 'area_tematica', 'centro_departamento', 'id_atividade', 'id_projeto'];

        if (!in_array($sort, $allowedFields)) $sort = 'ano';


        $this->data['actions'] = $this->actionsRepository->getByFilter($request->query(), $sort, $direction);
        $this->data['parametros'] = $this->parametrosRepository->getAllActiveByFunctions(['TIPO', 'MODALIDADE', 'CENTRO_DEPARTAMENTO', 'ÁREA_TEMÁTICA', 'SITUACAO'])->groupBy('function');
        
        return view('pages.actions.index', $this->data);
    }

    public function create(){

        $this->data['parametros'] = $this->parametrosRepository->getAllActiveByFunctions(['TIPO', 'MODALIDADE', 'CENTRO_DEPARTAMENTO', 'ÁREA_TEMÁTICA', 'SITUACAO'])->groupBy('function');
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
            $this->actionsRepository->createTeam($request, $id_acao);
            return redirect()->back()->with("success", "Usuário adicionado a ação com sucesso.");
        } catch (\Throwable $th) {
            return redirect()->back()->with("error", "Erro. Por favor, tente novamente mais tarde.")->withInput();
        }
    }

    public function storeSchedule(ScheduleRequest $request, $id_acao){
        try {
            $this->actionsRepository->createSchedule($request, $id_acao);
            return redirect()->back()->with("success", "Evento adicionado a ação com sucesso.");
        } catch (\Throwable $th) {
            return redirect()->back()->with("error", "Erro. Por favor, tente novamente mais tarde.")->withInput();
        }
    }

    public function my(Request $request){

        $this->data['actions'] = $this->actionsRepository->getAllByUuid(Auth::user()->uuid, $request->query());

        return view('pages.actions.my', $this->data);
    }

    public function details($uuid){

        $this->data['action'] = $this->actionsRepository->getByUserUuid(Auth::user()->uuid, $uuid)->action;
        $this->data['coordinators'] = $this->usersRepository->getForCoordinator();
        $this->data['categorias'] = $this->parametrosRepository->getAllActiveByFunctions(['CETAGORIA_COORDENADOR']);

        return view('pages.actions.details', $this->data);
    }

    public function previewImport(Request $request)
    {
        $cacheKey = 'import_preview_' . auth()->id();

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
                return redirect()->back()->with("toast_error", "Arquivo CSV vazio ou inválido.");
            }

            $delimiter = substr_count($headerLine, ';') > substr_count($headerLine, ',') ? ';' : ',';

            $projectsData = [];
            $rowIndex = 1;
            $duplicadosIgnorados = 0;

            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $row = array_map(fn($field) => trim(mb_convert_encoding($field, 'UTF-8', 'auto')), $row);
                $row = array_pad($row, 17, null);

                $email = strtolower($row[17] ?? '');
                
                $user = !empty($email) ? User::where('email', $email)->first() : null;

                $checkData = [
                    'ano' => $row[1] ?? null,
                    'id_projeto' => $row[2] ?? null,
                    'data_inicio' => $row[6] ?? null,
                    'data_fim' => $row[7] ?? null,
                    'id_proponente' => $user->uuid ?? null,
                ];

                if (Acao::where($checkData)->exists()) {
                    $duplicadosIgnorados++;
                    $rowIndex++;
                    continue; 
                }

                $errors = [];
                if (empty($row[3])) $errors['titulo'] = 'Título é obrigatório';
                if (empty($row[16])) $errors['proponente'] = 'Nome do proponente é obrigatório';
                if (empty($row[17])) $errors['email'] = 'Email é obrigatório';

                $projectsData[$rowIndex] = [
                    'ano' => $row[1],
                    'id_projeto' => $row[2],
                    'titulo' => $row[3],
                    'centro_departamento' => $row[4],
                    'situacao' => $row[5],
                    'data_inicio' => $row[6],
                    'data_fim' => $row[7],
                    'data_atualizacao' => $row[8],
                    'resumo' => $row[9],
                    'palavras_chave' => $row[10],
                    'tipo_acao' => $row[11],
                    'area_tematica' => $row[12],
                    'modalidade' => $row[13],
                    'com_bolsa' => $row[14],
                    'ods' => $row[15],
                    'proponente' => $row[16],
                    'email_proponente' => $email,
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
                return redirect()->route('actions.index')->with('toast_error', 'A sessão de importação expirou ou não há dados.');
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
                        if (empty($allProjects[$index]['titulo'])) $errors['titulo'] = 'Título é obrigatório';
                        if (empty($allProjects[$index]['proponente'])) $errors['proponente'] = 'Nome do proponente é obrigatório';
                        if (empty($allProjects[$index]['email_proponente'])) $errors['email'] = 'Email é obrigatório';
                        
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
        $cacheKey = 'import_preview_' . auth()->id();
        $cacheData = Cache::get($cacheKey);

        if (!$cacheData || empty($cacheData['projects'])) {
            return redirect()->route('actions.index')->with('toast_error', 'Sessão de importação expirada ou sem dados válidos.');
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
                    if (empty($allProjects[$index]['titulo'])) $errors['titulo'] = 'Título é obrigatório';
                    if (empty($allProjects[$index]['proponente'])) $errors['proponente'] = 'Nome do proponente é obrigatório';
                    if (empty($allProjects[$index]['email_proponente'])) $errors['email'] = 'Email é obrigatório';
                    
                    $allProjects[$index]['errors'] = $errors;
                }
            }
        }

        Cache::put($cacheKey, ['projects' => $allProjects, 'duplicados' => $duplicadosIgnorados], now()->addHours(2));

        $totalErrors = collect($allProjects)->filter(fn($item) => count($item['errors']) > 0)->count();

        if ($totalErrors > 0) {
            return redirect()->back()->with('toast_error', "Não foi possível salvar. Ainda existem {$totalErrors} linha(s) com erro no lote. Corrija ou exclua as linhas.");
        }

        if (empty($allProjects)) {
            return redirect()->route('actions.index')->with('toast_info', 'Nenhuma linha restou para ser importada.');
        }

        DB::beginTransaction();
        try {
            $acoesParaInserir = [];
            $agora = now();

            foreach ($allProjects as $linha) {
                
                $centroDepartamento = !empty($linha['centro_departamento']) 
                    ? Parametro::firstOrCreate([
                        'function' => 'CENTRO_DEPARTAMENTO', 
                        'value' => mb_strtoupper(trim($linha['centro_departamento']), 'UTF-8')
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

                $modalidade = !empty($linha['modalidade']) 
                    ? Parametro::firstOrCreate([
                        'function' => 'MODALIDADE', 
                        'value' => mb_strtoupper(trim($linha['modalidade']), 'UTF-8')
                    ]) : null;

                $situacao = !empty($linha['situacao']) 
                    ? Parametro::firstOrCreate([
                        'function' => 'SITUACAO', 
                        'value' => mb_strtoupper(trim($linha['situacao']), 'UTF-8')
                    ]) : null;

                $emailCoordenador = strtolower(trim($linha['email_proponente'] ?? ''));
                $idCoordenador = null;

                if (!empty($emailCoordenador)) {
                    $usuario = User::where('email', $emailCoordenador)->first();
                    
                    if (!$usuario) {
                        $usuario = User::create([
                            'name' => mb_strtoupper(trim($linha['proponente']), 'UTF-8'),
                            'email' => $emailCoordenador,
                            'status' => 2,
                        ]);
                        $usuario->assignRole("Coordenador");
                    }
                    $idCoordenador = $usuario->uuid;
                }

                $acoesParaInserir[] = [
                    'id'                  => (string) Str::uuid(),
                    'id_proponente'       => $idCoordenador,
                    'ano'                 => $linha['ano'] ?? null,
                    'id_projeto'          => $linha['id_projeto'] ?? null,
                    'titulo'              => $linha['titulo'] ?? null,
                    'centro_departamento' => $centroDepartamento ? $centroDepartamento->value : null,
                    'situacao'            => $situacao ? $situacao->value : null,
                    'data_inicio'         => $linha['data_inicio'] ?? null, 
                    'data_fim'            => $linha['data_fim'] ?? null,
                    'data_atualizacao'    => $linha['data_atualizacao'] ?? null,
                    'resumo'              => $linha['resumo'] ?? null,
                    'palavras_chave'      => $linha['palavras_chave'] ?? null,
                    'tipo_acao'           => $tipoAcao ? $tipoAcao->value : null,
                    'area_tematica'       => $areaTematica ? $areaTematica->value : null,
                    'modalidade'          => $modalidade ? $modalidade->value : null,
                    'com_bolsa'           => $linha['com_bolsa'] ?? null,
                    'ods'                 => $linha['ods'] ?? null,
                    'status'              => 1,
                    'created_at'          => $agora,
                    'updated_at'          => $agora,
                ];
            }

            if (!empty($acoesParaInserir)) {
                Acao::insert($acoesParaInserir);

                $usuarioLogado = auth()->user();
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

            return redirect()->route('actions.index')->with("toast_success", "Ações importadas e salvas com sucesso!");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('actions.index')->with("toast_error", "Erro ao salvar os dados: " . $e->getMessage());
        }
    }

    public function edit($uuid){
        $this->data['action'] = $this->actionsRepository->getByUuid($uuid);
        $this->data['parametros'] = $this->parametrosRepository->getAllActiveByFunctions(['TIPO', 'MODALIDADE', 'CENTRO_DEPARTAMENTO', 'ÁREA_TEMÁTICA', 'SITUACAO'])->groupBy('function');
        $this->data['coordinators'] = $this->usersRepository->getForCoordinator();

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
}
