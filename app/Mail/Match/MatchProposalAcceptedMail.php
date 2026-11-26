<?php

namespace App\Mail\Match;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MatchProposalAcceptedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $instituicaoName,
        public readonly string $coordenadorName,
        public readonly string $instituicaoEmail,
        public readonly string $acaoTitulo
    ) {}

    public function build()
    {
        return $this->subject('Proposta de Parceria Aceita!')
                    ->view('mails.match.match_proposal_accepted');
    }
}