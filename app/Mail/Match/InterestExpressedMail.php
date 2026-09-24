<?php

namespace App\Mail\Match;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InterestExpressedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $instituicaoName,
        public readonly string $coordenadorName,
        public readonly string $acaoTitulo,
        public readonly string $instituicaoEmail
    ) {}

    public function build()
    {
        return $this->subject('Novo Interesse de Parceria!')
                    ->view('mails.match.interest_expressed');
    }
}