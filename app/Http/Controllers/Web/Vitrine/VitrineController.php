<?php

namespace App\Http\Controllers\Web\Vitrine;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Settings\UsersController;
use Carbon\Carbon;
use App\Jobs\Auth\SendEmailToDoFirstAccess;
use App\Models\Instituicao_Externa;
use App\Models\User;
use App\Models\Vitrine_Access;
use App\Repositories\Tokens\UserTokens\UsersTokensRepository;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Acao;

class VitrineController extends Controller
{
    private $data = [];
    private $userTokensRepository;

    public function __construct(
        UsersTokensRepository $userTokensRepository,
    ) {
        $this->userTokensRepository = $userTokensRepository;
    }

    public function vitrine(Request $request)
    {
        Vitrine_Access::create([
        'ip_address' => $request->ip(),
        'user_agent' => $request->userAgent(),
        'user_id'    => auth()->id()
    ]);

        
        $this->data['mainCarousel'] = Acao::where('situacao', 'EM EXECUÇÃO')
        ->whereNull('acao.deleted_at')
        ->orderByRaw('img IS NULL') 
        ->latest('data_cadastro')
        ->take(10)
        ->get();
        
        $this->data['thematicAreas'] = Acao::where('situacao', 'EM EXECUÇÃO')
        ->whereNull('acao.deleted_at')
        ->orderByRaw('img IS NULL')
        ->latest('data_cadastro')
        ->get()
        ->groupBy('area_tematica');
        
        return view('pages.vitrine.vitrine', $this->data);
        }
        
    public function index(Request $request)
    {
        $this->data['instituicoes'] = Instituicao_Externa::paginate(30);

        $currentMonth = Vitrine_Access::whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year);

        $this->data['instituicoes'] = Instituicao_Externa::when($request->search, function ($q, $search) {
                return $q->where(function ($subQuery) use ($search) {
                    $subQuery->where('nome', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('cnpj', 'like', "%{$search}%");
                });
            })
            ->orderBy($request->input('sort', 'created_at'), $request->input('dir', 'desc'))
            ->paginate(30)
            ->appends($request->all());

        $currentMonth = Vitrine_Access::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year);

        $this->data['acessosGerais'] = (clone $currentMonth)->count();

        $this->data['visitantesUnicos'] = (clone $currentMonth)
        ->distinct('ip_address')
        ->count('ip_address');

        $this->data['instituicoesConectando'] = (clone $currentMonth)
        ->whereHas('user', function ($query) {
            $query->whereNotNull('id_instituicao')
                  ->whereRaw("TRIM(id_instituicao) != ''");
        })
        ->distinct('user_id')
        ->count('user_id');

        $this->data['internosConectando'] = (clone $currentMonth)
        ->whereHas('user', function ($query) {
            $query->where(function ($q) {
                $q->whereNull('id_instituicao')
                  ->orWhereRaw("TRIM(id_instituicao) = ''");
            });
        })
        ->distinct('user_id')
        ->count('user_id');

        $this->data['totalInstituicoes'] = Instituicao_Externa::where('status', 1)->count();
        $this->data['pendentesInstituicoes'] = Instituicao_Externa::where('status', 0)->count();

        return view('pages.vitrine.index', $this->data);
    }

    public function aprovar($uuid)
    {
         try {
            $instituicao = Instituicao_Externa::findOrFail($uuid);
            $instituicao->status = 1;
            $instituicao->save();

            $password = Str::random(10);

            $user = User::create([
                'name' => $instituicao->nome,
                'email' => $instituicao->email,
                'id_instituicao' => $instituicao->id,
                'status' => true,
                'password' => $password,
            ]);
            $user->assignRole('Instituição');

            $token = $this->userTokensRepository->store($user, "first_access");

            SendEmailToDoFirstAccess::dispatch(
                $user,
                $token->created_at,
                $token->id,
                $password
            );
            
            return redirect()->back()->with("success", "Instituição aprovada com sucesso.")->withInput();
            
        } catch (\Throwable $err) {
            return redirect()->back()->with('error', 'Erro ao aprovar instituição. Por favor, tente novamente mais tarde.');
        }
    }

    public function show($uuid)
    {
        $this->data['acao'] = Acao::with('galeria')->findOrFail($uuid);
        
        return view('pages.vitrine.show', $this->data);
    }

    public function catalogo(Request $request)
    {
        $subQuery = DB::table('acao')
            ->select('id')
            ->selectRaw('ROW_NUMBER() OVER (PARTITION BY titulo ORDER BY data_fim DESC) as rn')
            ->whereIn('situacao', ['EM EXECUÇÃO', 'CONCLUÍDA']);

        $query = Acao::whereIn('situacao', ['EM EXECUÇÃO', 'CONCLUÍDA']);

        $aplicarFiltros = function ($q) use ($request) {
            $q->when($request->area_tematica, function ($q, $area) {
                return $q->where('area_tematica', $area);
            })
            ->when($request->situacao, function ($q, $situacao) {
                return $q->where('situacao', $situacao);
            })
            ->when($request->tipo_acao, function ($q, $tipo) {
                return $q->where('tipo_acao', $tipo);
            })
            ->when($request->ods, function ($q, $ods) {
                return $q->where('ods', 'like', "%{$ods}%");
            })
            ->when($request->search, function ($q, $search) {
                return $q->where(function ($subQuery) use ($search) {
                    $subQuery->where('titulo', 'like', "%{$search}%")
                            ->orWhere('palavras_chave', 'like', "%{$search}%");
                });
            });
        };

        $aplicarFiltros($subQuery);
        $aplicarFiltros($query);

        $query->joinSub($subQuery, 'top_acoes', function ($join) {
            $join->on('acao.id', '=', 'top_acoes.id')
                ->where('top_acoes.rn', '=', 1);
        });

        $query->orderByRaw('img IS NULL ASC')
            ->latest('data_cadastro');

        $this->data['acoes'] = $query->paginate(12)->appends($request->all());

        return view('pages.vitrine.catalogo', $this->data);
    }

}
