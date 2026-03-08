<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class CobranzaTestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $logId;

    public function __construct($logId)
    {
        $this->logId = $logId;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notificación Importante - Prueba de Sistema',
        );
    }

    public function content(): Content
    {
        // Puedes crear una vista simple en resources/views/emails/test.blade.php
        return new Content(
            view: 'emails.test', 
        );
    }

    // ¡ESTA ES LA CLAVE! Inyectamos el ID oculto para SendGrid
    public function headers(): Headers
    {
        return new Headers(
            text: [
                'X-SMTPAPI' => json_encode([
                    'unique_args' => [
                        'email_log_id' => $this->logId
                    ]
                ]),
            ],
        );
    }
}