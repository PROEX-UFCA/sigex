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

class VitrineController extends Controller
{
    private $data = [];
    private $userTokensRepository;

    public function __construct(
        UsersTokensRepository $userTokensRepository,
    ) {
        $this->userTokensRepository = $userTokensRepository;
    }

    public function vitrine(){
        return view('pages.vitrine.vitrine', $this->data);
    }

    public function index(){
        $this->data['instituicoes'] = Instituicao_Externa::paginate(30);

        return view('pages.vitrine.index', $this->data);
    }

    public function aprovar($uuid){
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
}
