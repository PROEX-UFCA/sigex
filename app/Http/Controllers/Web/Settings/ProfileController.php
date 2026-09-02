<?php

namespace App\Http\Controllers\Web\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Settings\Profile\UpdateRequest;
use App\Repositories\Parametros\ParametrosRepository;
use App\Repositories\Settings\User\UsersRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $this->data['parametros'] = $this->parametrosRepository->getAllActiveByFunctions(['CENTRO', 'CURSO'])->groupBy('function');
        $this->data['user'] = Auth::user();

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
}
