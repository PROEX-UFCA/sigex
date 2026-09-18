<?php
namespace App\Http\Controllers\Web\Vitrine;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Matches\MatchesRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            
            // Clean the UUID to prevent the invisible char(36) spacing bug
            $id_instituicao = trim((string) $user->id_instituicao);

            $this->matchesRepository->expressInterest($id_instituicao, $id_acao);

            return redirect()->back()->with('success', 'Interesse registrado com sucesso! A coordenação da ação será notificada.');
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Erro ao registrar interesse. Tente novamente mais tarde.');
        }
    }

    public function confirmMatch(Request $request, $id_match)
    {
        try {
            $this->matchesRepository->confirmMatch($id_match);

            return redirect()->back()->with('success', 'Match confirmado! A instituição parceira foi notificada.');
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Erro ao confirmar o match. Tente novamente mais tarde.');
        }
    }

    public function endMatch($id_match)
    {
        $match = \App\Models\Match_Acao::findOrFail($id_match);
        
        $match->update([
            'concluida' => true
        ]);

        return back()->with('success', 'Parceria concluída com sucesso!'); 
    }
}