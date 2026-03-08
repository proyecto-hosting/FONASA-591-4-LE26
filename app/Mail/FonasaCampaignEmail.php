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
        // Desactivamos temporalmente los adjuntos para la demo ya que los PDF físicos no existen
        return [];
    }
}