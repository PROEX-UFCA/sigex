<?php

namespace App\Http\Controllers\Web\System;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Settings\UsersController;
use App\Jobs\Auth\SendEmailToDoFirstAccess;
use App\Models\Instituicao_Externa;
use App\Models\User;
use App\Repositories\Tokens\UserTokens\UsersTokensRepository;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
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

    public function vitrine()
    {
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

    public function index()
    {
        $this->data['instituicoes'] = Instituicao_Externa::paginate(30);

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
        $query = Acao::whereIn('situacao', ['EM EXECUÇÃO', 'CONCLUÍDA'])
            ->orderByRaw('img IS NULL')
            ->latest('data_cadastro');

        $query->when($request->area_tematica, function ($q, $area) {
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

        $this->data['acoes'] = $query->paginate(12)->appends($request->all());

        return view('pages.vitrine.catalogo', $this->data);
    }
}
