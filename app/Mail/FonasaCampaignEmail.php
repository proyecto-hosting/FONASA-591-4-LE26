<?php

namespace App\Mail;

use App\Models\Recipient;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class FonasaCampaignEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $recipient;

    public function __construct(Recipient $recipient)
    {
        $this->recipient = $recipient;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notificación Importante - FONASA',
        );
    }

    public function content(): Content
    {
        // Usaremos una vista simple de texto o HTML que crearemos en un momento
        return new Content(
            view: 'emails.campaign', 
        );
    }

    public function attachments(): array
    {
        // Adjuntamos el PDF desde la carpeta segura (private) donde lo guardó el SFTP
        return [
            Attachment::fromStorageDisk('local', 'fonasa_testing/' . $this->recipient->pdf_filename)
                ->as($this->recipient->pdf_filename)
                ->withMime('application/pdf'),
        ];
    }
}