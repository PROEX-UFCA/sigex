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
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
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

    public function index(Request $request){

        $this->data['actions'] = $this->actionsRepository->getByFilter($request->query());
        $this->data['parametros'] = $this->parametrosRepository->getAllActiveByFunctions(['TIPO', 'MODALIDADE', 'CENTRO_DEPARTAMENTO', 'ÁREA_TEMÁTICA'])->groupBy('function');

        return view('pages.actions.index', $this->data);
    }

    public function create(){

        $this->data['parametros'] = $this->parametrosRepository->getAllActiveByFunctions(['TIPO', 'MODALIDADE', 'CENTRO_DEPARTAMENTO', 'ÁREA_TEMÁTICA'])->groupBy('function');
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

            $row = array_pad($row, 13, null);

            $startDate = $this->formatDateSafe($row[7]);
            $endDate = $this->formatDateSafe($row[8]);

            $email = strtolower($row[5] ?? '');
            
            $user = !empty($email) ? User::where('email', $email)->first() : null;

            $checkData = [
                'id_atividade'  => $row[0] ?? null,
                'id_projeto'    => $row[1] ?? null,
                'titulo'         => $row[2] ?? null,
                'id_coordenador'   => $user->uuid ?? null,
                'centro_departamento'        => $row[6] ?? null,
                'data_inicio'    => $startDate,
                'data_fim'      => $endDate,
                'ano'          => $row[9] ?? null,
                'tipo_acao'          => $row[10] ?? null,
                'area_tematica' => $row[11] ?? null,
                'modalidade'      => $row[12] ?? null
            ];

            $exists = Acao::where($checkData)->exists();

            if ($exists) {
                $duplicadosIgnorados++;
                $rowIndex++;
                continue; 
            }
            
            $errors = [];
            if (empty($row[2])) $errors['titulo'] = 'Título é obrigatório';
            if (empty($row[3])) $errors['coordenador'] = 'Coordenador é obrigatório';
            if (empty($row[5])) $errors['email'] = 'Email é obrigatório';

            $projectsData[] = [
                'id_atividade'        => $row[0],
                'id_projeto'          => $row[1],
                'titulo'              => $row[2],
                'coordenador' => $row[3],
                'siape' => $row[4],
                'email' => strtolower($row[5] ?? ''),
                'centro_departamento' => $row[6],
                'data_inicio'         => $startDate ?? $row[7],
                'data_fim'            => $endDate ?? $row[8],
                'ano'                 => $row[9],
                'tipo_acao'           => $row[10],
                'area_tematica'       => $row[11],
                'modalidade'          => $row[12],
                'errors'              => $errors,
                'row_index'           => $rowIndex
            ];
            
            $rowIndex++;
        }

        fclose($handle);

        $totalErrors = collect($projectsData)->filter(fn($item) => count($item['errors']) > 0)->count();

        return view('pages.actions.preview', compact('projectsData', 'totalErrors', 'duplicadosIgnorados'));
    }

    private function formatDateSafe($dateString) {
        if (empty($dateString)) return null;
        try {
            return \Carbon\Carbon::createFromFormat('d/m/Y', $dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                return \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $dateString)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }
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

                $emailCoordenador = strtolower(trim($linha['email'] ?? ''));
                $idCoordenador = null;

                if (!empty($emailCoordenador)) {
                    $usuario = User::where('email', $emailCoordenador)->first();
                    
                    if (!$usuario) {
                        $usuario = User::create([
                            'name' => mb_strtoupper(trim($linha['coordenador']), 'UTF-8'),
                            'email' => $emailCoordenador,
                            'matricula_siape' => trim($linha['siape'] ?? null),
                            'status' => 2,
                            // 'password' => bcrypt('Mudar123') // Descomente e ajuste se sua model User exigir senha
                        ]);
                        $usuario->assignRole("Coordenador");
                    }
                    $idCoordenador = $usuario->uuid;
                }

                // 3. Montar o array da Ação
                $acoesParaInserir[] = [
                    'id'                  => (string) Str::uuid(), // Se sua tabela acoes NÃO usar UUID, apague esta linha
                    'titulo'              => $linha['titulo'] ?? null,
                    'id_atividade'        => $linha['id_atividade'] ?? null,
                    'id_projeto'          => $linha['id_projeto'] ?? null,
                    'id_coordenador'      => $idCoordenador,
                    'centro_departamento' => $centroDepartamento ? $centroDepartamento->value : null,
                    'data_inicio'         => !empty($linha['data_inicio']) ? $linha['data_inicio'] : null,
                    'data_fim'            => !empty($linha['data_fim']) ? $linha['data_fim'] : null,
                    'ano'                 => $linha['ano'] ?? null,
                    'tipo_acao'           => $tipoAcao ? $tipoAcao->value : null,
                    'area_tematica'       => $areaTematica ? $areaTematica->value : null,
                    'modalidade'          => $modalidade ? $modalidade->value : null,
                    'status'              => 1,
                    'created_at'          => $agora,
                    'updated_at'          => $agora,
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
}
