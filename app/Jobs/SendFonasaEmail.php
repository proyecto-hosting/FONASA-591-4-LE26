<?php

namespace App\Jobs;

use App\Models\Recipient;
use App\Mail\FonasaCampaignEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendFonasaEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $recipient;

    public function __construct(Recipient $recipient)
    {
        $this->recipient = $recipient;
    }

    public function handle(): void
    {
        try {
            // Enviamos el correo
            Mail::to($this->recipient->email)->send(new \App\Mail\FonasaCampaignEmail($this->recipient));

            // Actualizamos el estado a "enviado"
            $this->recipient->update([
                'status' => 'enviado',
                'sent_at' => Carbon::now(),
            ]);

        } catch (\Throwable $e) { // <-- EL CAMBIO MAGICO: Throwable atrapa TODO
            // Si cualquier cosa falla (SendGrid, Vistas, Errores fatales), lo guardamos
            \Illuminate\Support\Facades\Log::error('Fallo en Job de Correo: ' . $e->getMessage());
            
            $this->recipient->update([
                'status' => 'rebotado',
                'bounce_reason' => substr($e->getMessage(), 0, 250), // Guardamos los primeros 250 caracteres del error
            ]);
        }
    }
}