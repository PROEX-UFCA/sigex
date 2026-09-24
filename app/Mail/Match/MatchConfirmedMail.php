<?php

namespace App\Mail\Match;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MatchConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $instituicaoName,
        public readonly string $coordenadorName,
        public readonly string $coordenadorEmail,
        public readonly string $acaoTitulo
    ) {}

    public function build()
    {
        return $this->subject('Match Confirmado: Parceria com a UFCA!')
                    ->view('mails.match.match_confirmed');
    }
}