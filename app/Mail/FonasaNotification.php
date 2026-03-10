<?php

namespace App\Mail;

use App\Models\Recipient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Storage;

class FonasaNotification extends Mailable
{
    use Queueable, SerializesModels;

    // Guardamos la información del destinatario
    public $recipient;

    /**
     * El constructor recibe al destinatario desde el Job
     */
    public function __construct(Recipient $recipient)
    {
        $this->recipient = $recipient;
    }

    /**
     * Configuramos el "Sobre" (El asunto del correo)
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notificación Importante - FONASA',
        );
    }

    /**
     * Definimos qué vista HTML se usará para el cuerpo del correo
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.fonasa', // Tendremos que crear este archivo HTML en el siguiente paso
        );
    }

    /**
     * Aquí ocurre la magia de los adjuntos con nuestro disco dinámico
     */
    public function attachments(): array
    {
        $attachments = [];

        // Si el destinatario tiene un PDF asignado
        if ($this->recipient->pdf_filename) {
            
            $diskName = env('FONASA_DISK', 'fonasa_local');
            
            // Verificamos que el archivo realmente exista en tu carpeta o Bucket de Google Cloud
            if (Storage::disk($diskName)->exists($this->recipient->pdf_filename)) {
                
                // Le decimos a Laravel que lo saque del disco y lo adjunte al correo
                $attachments[] = Attachment::fromStorageDisk($diskName, $this->recipient->pdf_filename)
                        ->as('Notificacion_FONASA.pdf') // Así se llamará el archivo cuando lo descargue el usuario
                        ->withMime('application/pdf');
            }
        }

        return $attachments;
    }
}