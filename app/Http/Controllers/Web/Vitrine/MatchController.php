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
use App\Models\Instituicao_Externa;
use App\Mail\Match\MatchProposalMail;
use App\Mail\Match\MatchProposalAcceptedMail;

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
    $match = Match_Acao::with(['instituicao', 'acao.equipe'])->findOrFail($id_match);
    $user = Auth::user();

    $cleanUserInstituicaoId = trim((string) ($user->id_instituicao ?? ''));
    $isInstituicao = $cleanUserInstituicaoId !== '';

    if ($isInstituicao) {
        $ownsMatch = trim((string) $match->id_instituicao) === $cleanUserInstituicaoId;
        $canAccept = $match->solicitante === 'coordenador';

        if (!$ownsMatch || !$canAccept) {
            abort(403, 'Você não tem permissão para aceitar esta parceria.');
        }
    } else {
        $userUuid = trim((string) $user->uuid);
        $isCoordinator = $match->acao->equipe->contains(function ($membro) use ($userUuid) {
            $isSameUser = trim((string) $membro->id_usuario) === $userUuid;
            $categoria = trim(strtoupper($membro->categoria_membro));
            $validCategories = [
                'COORDENADOR',
                'COORDENADOR(A)',
                'COORDENADOR(A) ADJUNTO(A)',
                'COORDENADORA',
                'COORDENADOR ADJUNTO',
            ];

            return $isSameUser && in_array($categoria, $validCategories);
        });

        $canConfirm = $match->solicitante === 'instituicao';

        if (!$isCoordinator || !$canConfirm) {
            abort(403, 'Apenas a coordenação desta ação pode confirmar esta parceria.');
        }
    }

    try {
        $this->matchesRepository->confirmMatch($id_match);

        if ($isInstituicao) {
            $membroCoordenador = $match->acao->equipe->first(function ($membro) {
                $categoria = trim(strtoupper($membro->categoria_membro));
                return in_array($categoria, [
                    'COORDENADOR',
                    'COORDENADOR(A)',
                    'COORDENADOR(A) ADJUNTO(A)',
                    'COORDENADORA',
                    'COORDENADOR ADJUNTO',
                ]);
            });

            if ($membroCoordenador) {
                $userCoordenador = User::where('uuid', trim((string) $membroCoordenador->id_usuario))->first();

                if ($userCoordenador) {
                    Mail::to($userCoordenador->email)->send(
                        new MatchProposalAcceptedMail(
                            $match->instituicao->nome,
                            $userCoordenador->name,
                            $match->instituicao->email,
                            $match->acao->titulo
                        )
                    );
                }
            }

            return redirect()->back()->with('success', 'Parceria aceita com sucesso! A coordenação foi notificada.');
        }

        Mail::to($match->instituicao->email)->send(
            new MatchConfirmedMail(
                $match->instituicao->nome,
                $user->name,
                $user->email,
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

    public function proposeMatch(Request $request, $id_acao, $id_instituicao)
    {
        try {
            $cleanInstituicaoId = trim((string) $id_instituicao);
            $cleanAcaoId = trim((string) $id_acao);

            $this->matchesRepository->proposeMatch($cleanInstituicaoId, $cleanAcaoId);

            $acao = Acao::findOrFail($cleanAcaoId);
            $instituicao = Instituicao_Externa::findOrFail($cleanInstituicaoId);
            $coordenador = Auth::user();

            Mail::to($instituicao->email)->send(
                new MatchProposalMail(
                    $instituicao->nome,
                    $coordenador->name,
                    $coordenador->email,
                    $acao->titulo
                )
            );

            return redirect()->back()->with('success', 'Convite de parceria enviado! A instituição foi notificada por e-mail.');
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Erro ao propor parceria. Tente novamente mais tarde.');
        }
    }
}