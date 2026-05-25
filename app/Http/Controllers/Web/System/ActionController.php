<?php

namespace App\Http\Controllers\Web\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Action\ImportRequest;
use App\Http\Requests\Web\Action\ScheduleRequest;
use App\Http\Requests\Web\Action\StoreRequest;
use App\Http\Requests\Web\Action\TeamRequest;
use App\Models\Acao;
use App\Models\Parametro;
use App\Models\User;
use App\Repositories\Actions\ActionsRepository;
use App\Repositories\Parametros\ParametrosRepository;
use App\Repositories\Settings\User\UsersRepository;
use App\Support\DateFormatter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
        $file = $request->file('csv');
        $handle = fopen($file->getRealPath(), "r");

        $headerLine = fgets($handle);
        if ($headerLine === false) {
            fclose($handle);
            return redirect()->back()->with("toast_error", "Arquivo CSV vazio ou inválido.");
        }

        $commaCount = substr_count($headerLine, ',');
        $semicolonCount = substr_count($headerLine, ';');
        $delimiter = $semicolonCount > $commaCount ? ';' : ',';

        $projectsData = [];
        $rowIndex = 1;
        $duplicadosIgnorados = 0;

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $row = array_map(function ($field) {
                return trim(mb_convert_encoding($field, 'UTF-8', 'auto'));
            }, $row);

            $row = array_pad($row, 17, null);

            $email = strtolower($row[17] ?? '');
            $startDate = DateFormatter::formatDateSafe($row[7]);
            $endDate = DateFormatter::formatDateSafe($row[8]);

            $user = !empty($email) ? User::where('email', $email)->first() : null;

            $checkData = [
                'ano' => $row[1] ?? null,
                'id_projeto' => $row[2] ?? null,
                'data_inicio' => $row[6] ?? null,
                'data_fim' => $row[7] ?? null,
                'id_proponente' => $user->uuid ?? null,
                
                // 'titulo' => $row[3] ?? null,
                // 'centro_departamento' => $row[4] ?? null,
                // 'area_tematica' => $row[12] ?? null,
                // 'palavras_chave' => $row[10] ?? null,
                // 'tipo_acao' => $row[11] ?? null,
                // 'modalidade' => $row[13] ?? null,
                //'situacao' => $row[5] ?? null,
                //'data_atualizacao' => $row[8] ?? null,
                // 'resumo' => $row[9] ?? null,
                //'com_bolsa' => $row[14] ?? null,
                //'ods' => $row[15] ?? null,
            ];

            $exists = Acao::where($checkData)->exists();

            if ($exists) {
                $duplicadosIgnorados++;
                $rowIndex++;
                continue; 
            }
            $errors = [];
            if (empty($row[3])) $errors['titulo'] = 'Título é obrigatório';
            if (empty($row[16])) $errors['proponente'] = 'Nome do proponente é obrigatório';
            if (empty($row[17])) $errors['email'] = 'Email é obrigatório';

            $projectsData[] = [
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
                'email_proponente' => strtolower($row[17] ?? ''),
                'errors' => $errors,
                'row_index' => $rowIndex
            ];
            
            $rowIndex++;
        }

        fclose($handle);

        $totalErrors = collect($projectsData)->filter(fn($item) => count($item['errors']) > 0)->count();

        $projectsData = collect($projectsData)->sortByDesc(function ($project) {
            return !empty($project['errors']); 
        })->values()->all();

        return view('pages.actions.preview', compact('projectsData', 'totalErrors', 'duplicadosIgnorados'));
    }

    public function storeImport(Request $request)
    {
        $projetos = $request->input('projects', []);

        if (empty($projetos)) {
            return redirect()->route('actions.index')->with('error', 'Nenhum dado válido para importar.');
        }

        DB::beginTransaction();
        try {
            $acoesParaInserir = [];
            $agora = now();

            foreach ($projetos as $linha) {
                
                if(count($linha) != 17){
                    continue;
                }

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
                            // 'password' => bcrypt('Mudar123')
                        ]);
                        $usuario->assignRole("Coordenador");
                    }
                    $idCoordenador = $usuario->uuid;
                }


                // 3. Montar o array da Ação
                $acoesParaInserir[] = [
                    'id' => (string) Str::uuid(),
                    'id_proponente' => $idCoordenador,
                    'ano' => $linha['ano'] ?? null,
                    'id_projeto' => $linha['id_projeto'] ?? null,
                    'titulo' => $linha['titulo'] ?? null,
                    'centro_departamento' => $centroDepartamento ? $centroDepartamento->value : null,
                    'situacao' => $linha['situacao'] ?? null,
                    'data_inicio' => $linha['data_inicio'] ?? null, 
                    'data_fim' => $linha['data_fim'] ?? null,
                    'data_atualizacao' => $linha['data_atualizacao'] ?? null,
                    'resumo' => $linha['resumo'] ?? null,
                    'palavras_chave' => $linha['palavras_chave'] ?? null,
                    'tipo_acao' => $tipoAcao ? $tipoAcao->value : null,
                    'area_tematica' => $areaTematica ? $areaTematica->value : null,
                    'modalidade' => $modalidade ? $modalidade->value : null,
                    'com_bolsa' => $linha['com_bolsa'] ?? null,
                    'ods' => $linha['ods'] ?? null,
                    'status' => 1,
                    'created_at' => $agora,
                    'updated_at' => $agora,
                ];
            }

            // 4. Inserção em Lote (Batch Insert)
            if (!empty($acoesParaInserir)) {
                // Usando o DB::table ou Eloquent insert para máxima velocidade
                Acao::insert($acoesParaInserir);

                // Log de Atividade (Opcional, usando spatie/laravel-activitylog)
                $usuarioLogado = auth()->user();
                $acaoReferencia = Acao::first(); // Pega apenas como model de referência pro log
                
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
            // Substitua 'acoes.index' pela rota correta de redirecionamento do seu sistema
            return redirect()->route('actions.index')->with("success", "Ações importadas e salvas com sucesso!");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('actions.index')->with("error", "Erro ao salvar os dados: " . $e->getMessage());
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
