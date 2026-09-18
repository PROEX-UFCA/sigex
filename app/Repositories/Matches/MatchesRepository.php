<?php

namespace App\Repositories\Matches;

interface MatchesRepository
{
    public function expressInterest($id_instituicao, $id_acao);
    public function confirmMatch($id_match);
    public function endMatch($id_match);
}