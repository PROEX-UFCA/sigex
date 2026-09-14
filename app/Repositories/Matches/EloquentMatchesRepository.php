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

        return $match;
    }
}