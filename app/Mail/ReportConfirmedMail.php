<?php

namespace App\Mail;

use App\Models\Submissao;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $data;
    /**
     * Create a new message instance.
     */
    public function __construct(
        public $submissao,
        protected $pdfBinary,
        protected string $pdfName,
        $nome,
        $titulo,
        $data
    ) {
        $this->data = [
            'nome' => $nome,
            'titulo' => $titulo,
            'data' => $data,
        ];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Relatório recebido',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mails.report.report_confirmed',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */    
    public function attachments(): array
    {
        return [];
        // incorreto por enquanto
        // return [
        //     Attachment::fromData(
        //         fn () => $this->pdfBinary,
        //         $this->pdfName
        //     )->withMime('application/pdf'),
        // ];
    }
}