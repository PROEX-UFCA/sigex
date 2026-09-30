<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    public function __construct(array $mailData)
    {
        $this->mailData = $mailData;
    }

    public function build()
    {
        return $this->subject('Novo Relatório Disponível: ' . $this->mailData['titulo_relatorio'])
                    ->view('mails.report.report_created')
                    ->with('data', $this->mailData);
    }

    /**
     * Get the message envelope.
     */
    // public function envelope(): Envelope
    // {
    //     return new Envelope(
    //         subject: 'Report Created Mail',
    //     );
    // }

    // /**
    //  * Get the message content definition.
    //  */
    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'view.name',
    //     );
    // }

    // /**
    //  * Get the attachments for the message.
    //  *
    //  * @return array<int, Attachment>
    //  */
    // public function attachments(): array
    // {
    //     return [];
    // }
}

// InvalidArgumentException: View [mails.reports.report_created] not found. in /var/www/vendor/laravel/framework/src/Illuminate/View/FileViewFinder.php:138
