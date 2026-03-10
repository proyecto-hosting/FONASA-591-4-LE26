<?php

namespace App\Jobs;

use App\Models\Recipient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class DispatchFonasaEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Propiedad pública que guardará al destinatario específico
    public $recipient;

    /**
     * El constructor recibe al destinatario desde el bucle del CSV
     */
    public function __construct(Recipient $recipient)
    {
        $this->recipient = $recipient;
    }

    /**
     * Ejecutar el trabajo (Enviar el correo)
     */
    public function handle(): void
    {
        Log::info("Iniciando envío para RUT: {$this->recipient->rut}");

        try {
            // Aquí llamaremos a la plantilla del correo (Mailable) que crearemos en el siguiente paso
            Mail::to($this->recipient->email)->send(new \App\Mail\FonasaNotification($this->recipient));

            // Si el envío es exitoso, actualizamos la base de datos
            $this->recipient->update([
                'status'  => 'enviado',
                'sent_at' => now(),
            ]);

            Log::info("Correo enviado exitosamente a: {$this->recipient->email}");

        } catch (\Exception $e) {
            // Si hay un error crítico (ej: SendGrid rechazó la conexión)
            Log::error("Fallo al enviar a {$this->recipient->email}: " . $e->getMessage());

            $this->recipient->update([
                'status'        => 'error',
                'bounce_reason' => substr($e->getMessage(), 0, 250), // Guardamos el error técnico
            ]);
        }
    }
}