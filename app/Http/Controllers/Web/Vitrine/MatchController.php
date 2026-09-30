<?php
namespace App\Http\Controllers\Web\Vitrine;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Matches\MatchesRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Acao;
use App\Models\User;
use App\Models\Match_Acao;
use Illuminate\Support\Facades\Mail;
use App\Mail\Match\InterestExpressedMail;
use App\Mail\Match\MatchConfirmedMail;
use App\Mail\Match\MatchEndedMail;

class MatchController extends Controller
{
    protected $matchesRepository;

    public function __construct(MatchesRepository $matchesRepository)
    {
        $this->matchesRepository = $matchesRepository;
    }

    public function expressInterest(Request $request, $id_acao)
    {
        try {
            $user = Auth::user();

            if ($user->id_instituicao === null) {
                abort(403, 'Apenas instituições externas podem demonstrar interesse.');
            }
            
            $id_instituicao = trim((string) $user->id_instituicao);

            $this->matchesRepository->expressInterest($id_instituicao, $id_acao);

            $acao = Acao::with('equipe')->findOrFail($id_acao);
            $instituicao = \App\Models\Instituicao_Externa::find($id_instituicao);

            $membroCoordenador = $acao->equipe->first(function ($membro) {
                $categoria = trim(strtoupper($membro->categoria_membro));
                $validCategories = [
                    'COORDENADOR(A)',
                    'COORDENADOR(A) ADJUNTO(A)',
                ];
                return in_array($categoria, $validCategories);
            });

            if ($membroCoordenador) {
                $cleanUserId = trim((string) $membroCoordenador->id_usuario);
                $userCoordenador = User::where('uuid', $cleanUserId)->first();

                if ($userCoordenador) {
                    Mail::to($userCoordenador->email)->send(
                        new InterestExpressedMail(
                            $instituicao->nome,
                            $userCoordenador->name,
                            $acao->titulo,
                            $instituicao->email
                        )
                    );
                }
            }
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Interesse registrado com sucesso!'
                ]);
            }

            return redirect()->back()->with('success', 'Interesse registrado com sucesso! A coordenação da ação será notificada.');
        } catch (\Throwable $th) {
            \Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Erro ao registrar interesse. Tente novamente mais tarde.');
        }
    }

    public function confirmMatch(Request $request, $id_match)
    {
        try {
            $this->matchesRepository->confirmMatch($id_match);

            $match = Match_Acao::with(['instituicao', 'acao'])->findOrFail($id_match);
            $coordenador = Auth::user();

            Mail::to($match->instituicao->email)->send(
            new MatchConfirmedMail(
                $match->instituicao->nome,
                $coordenador->name,
                $coordenador->email,
                $match->acao->titulo
            )
            );

            return redirect()->back()->with('success', 'Match confirmado! A instituição parceira foi notificada.');
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Erro ao confirmar o match. Tente novamente mais tarde.');
        }
    }

    public function endMatch($id_match)
    {
        try{
            $match = \App\Models\Match_Acao::findOrFail($id_match);
            
            $match->update([
                'concluida' => true
            ]);
    
            $coordenador = Auth::user();
    
            Mail::to($match->instituicao->email)->send(
                new MatchEndedMail(
                    $match->instituicao->nome,
                    $coordenador->name,
                    $coordenador->email,
                    $match->acao->titulo
                )
            );
            return back()->with('success', 'Parceria concluída com sucesso!'); 
        } catch (\Throwable $th){
            Log::error($th->getMessage());
            return back()->with('error', 'Erro ao concluir parceria. Tente novamente mais tarde.');
        }
    }
}