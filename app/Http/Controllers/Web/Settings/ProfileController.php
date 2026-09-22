<?php

namespace App\Http\Controllers\Web\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Settings\Profile\UpdateRequest;
use App\Repositories\Parametros\ParametrosRepository;
use App\Repositories\Settings\User\UsersRepository;
use App\Models\Match_Acao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Instituicao_Externa;

class ProfileController extends Controller
{
    private $data = [];
    private $usersRepository;
    private $parametrosRepository;

    public function __construct(
        UsersRepository $usersRepository,
        ParametrosRepository $parametrosRepository
    ) {
        $this->usersRepository = $usersRepository;
        $this->parametrosRepository = $parametrosRepository;
    }

    public function index()
    {
        $user = Auth::user();

        if (!empty($user->id_instituicao)) {
            return redirect()->route('vitrine.profile');
        }

        $this->data['parametros'] = $this->parametrosRepository->getAllActiveByFunctions(['CENTRO', 'CURSO'])->groupBy('function');
        $this->data['user'] = $user;

        return view('pages.profile.index', $this->data);
    }

    public function update(UpdateRequest $request)
    {
        $user = Auth::user();

        $credentials = [
            'email' => $user->email,
            'password' => $request->actual_password,
        ];

        try {
            if ($request->actual_password != null) {
                if (Auth::attempt($credentials)) {
                    $this->usersRepository->update($user->uuid, $request);
                    $this->usersRepository->updatePassword($request, $user->uuid);
                } else {
                    return redirect()->back()->with('error', 'Senha atual não correspondente, tente novamente.');
                }
            } else {
                $this->usersRepository->update($user->uuid, $request);
                return redirect()->back()->with('success', 'Informações atualizadas com sucesso.');
            }

            return redirect()->back()->with('success', 'Informações atualizadas com sucesso.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Erro ao tentar atualizar informações, tente novamente mais tarde.');
        }
    }

    public function vitrineIndex()
    {
        $user = Auth::user();

        if (empty($user->id_instituicao)) {
            return redirect()->route('profile.index');
        }

        $this->data['user'] = $user;

        $cleanId = trim($user->id_instituicao);
        $this->data['instituicao'] = Instituicao_Externa::find($cleanId);
        $this->data['matches'] = Match_Acao::with('acao')
        ->where('id_instituicao', $cleanId)
        ->orderBy('mutual', 'desc')
        ->latest()->get();

        return view('pages.vitrine.profile', $this->data);
    }

    public function vitrineUpdate(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'telefone_contato' => 'required|string|max:20',
            'logradouro' => 'required|string|max:255',
            'numero' => 'required|string|max:20',
            'complemento' => 'nullable|string|max:255',
            'cep' => 'required|string|max:20',
            'actual_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);

        try {
            if ($request->filled('actual_password') || $request->filled('new_password')) {
                if (!Auth::attempt(['email' => $user->email, 'password' => $request->actual_password])) {
                    return redirect()->back()->with('error', 'A senha atual está incorreta.');
                }
                $user->password = bcrypt($request->new_password);
            }

            $user->name = $request->nome;
            $user->email = $request->email;
            $user->phone = $request->telefone_contato;
            $user->save();

            $cleanId = trim((string) $user->id_instituicao);
            $instituicao = Instituicao_Externa::findOrFail($cleanId);
            
            $instituicao->update([
                'nome' => $request->nome,
                'email' => $request->email,
                'telefone_contato' => $request->telefone_contato,
                'logradouro' => $request->logradouro,
                'numero' => $request->numero,
                'complemento' => $request->complemento,
                'cep' => $request->cep,
            ]);

            return redirect()->back()->with('success', 'Perfil atualizado com sucesso!');
            
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Erro ao atualizar o perfil. Tente novamente.');
        }
    }
}
