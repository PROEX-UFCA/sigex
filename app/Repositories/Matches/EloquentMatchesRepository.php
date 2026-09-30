<?php

namespace App\Repositories\Matches;

use App\Models\Match_Acao;

class EloquentMatchesRepository implements MatchesRepository
{
    public function expressInterest($id_instituicao, $id_acao)
    {
        return Match_Acao::firstOrCreate([
            'id_instituicao' => $id_instituicao,
            'id_acao' => $id_acao,
        ], [
            'mutual' => false
        ]);
    }

    public function confirmMatch($id_match)
    {
        $match = Match_Acao::findOrFail($id_match);
        
        $match->update([
            'mutual' => true
        ]);

        return back()->with('success', 'Parceria iniciada! Cheque a ação em "Minhas ações" para mais detalhes sobre as parcerias.'); 
    }

    public function endMatch($id_match)
    {
        $match = Match_Acao::findOrFail($id_match);
        
        $match->update([
            'concluída' => true
        ]);

        return back()->with('success', 'Parceria concluída com sucesso!'); 
    }
}