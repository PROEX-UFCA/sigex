<?php

namespace App\Jobs;

use App\Mail\ReportConfirmedMail;
use App\Models\Submissao;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendReportConfirmationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $submissaoId,
        protected string $pdfContent,
        protected string $pdfName
    ) {}

    public function handle(): void
    {
        $submissao = Submissao::findOrFail($this->submissaoId);

        $pdfBinary = base64_decode($this->pdfContent, true);

        if ($pdfBinary === false) {
            throw new \RuntimeException('PDF Base64 inválido.');
        }

        $email = new ReportConfirmedMail(
            $submissao,
            $this->pdfContent,
            $this->pdfName,
            $submissao->user->name,
            $submissao->relatorio->titulo,
            $submissao->finalizada_em,
        );

        Mail::to($submissao->user->email)->send($email);
    }
}