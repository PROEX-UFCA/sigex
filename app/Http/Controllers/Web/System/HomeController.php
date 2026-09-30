<?php

namespace App\Http\Controllers\Web\System;

use App\Http\Controllers\Controller;
use App\Models\Submissao;
use App\Models\Match_Acao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    private $data = [];

    public function index()
    {
        $user = Auth::user();
        $this->data['user'] = $user;
        $user = Auth::user();
        $this->data['user'] = $user;

        $diff = $this->data['user']->updated_at->diffInMonths();

        if ($diff < 1) {
            $progress = '100%';
        } elseif ($diff < 2) {
            $progress = '90%';
        } elseif ($diff < 3) {
            $progress = '60%';
        } elseif ($diff < 4) {
            $progress = '50%';
        } elseif ($diff < 5) {
            $progress = '40%';
        } elseif ($diff < 6) {
            $progress = '30%';
        } elseif ($diff < 7) {
            $progress = '20%';
        } elseif ($diff < 8) {
            $progress = '10%';
        } else {
            $progress = '1%';
        }

        $this->data['progress'] = $progress;
        $this->data['diff'] = $diff;

        $profile = 100;

        if($this->data['user']->centro == null){
            $profile -= 10;
        }
        if($this->data['user']->curso == null){
            $profile -= 10;
        }
        if($this->data['user']->cpf == null){
            $profile -= 10;
        }
        if($this->data['user']->matricula_siape == null){
            $profile -= 10;
        }
        if($this->data['user']->phone == null){
            $profile -= 10;
        }

        $this->data['profile'] = $profile;

        $this->data['pendingMatches'] = Match_Acao::with(['instituicao', 'acao'])
        ->where('mutual', false)
        ->whereHas('acao.equipe', function ($query) use ($user) {
            $query->where('id_usuario', $user->uuid)
                    ->whereIn('categoria_membro', [
                        'COORDENADOR', 
                        'COORDENADOR(A)', 
                        'COORDENADOR(A) ADJUNTO(A)', 
                        'COORDENADORA', 
                        'COORDENADOR ADJUNTO'
                    ]);
        })->latest()->get();

        $this->data['submissoes'] = Submissao::where(['id_usuario' => $user->uuid, 'id_acao' => null])->get();
        return view('pages.home.index', $this->data);
    }
}