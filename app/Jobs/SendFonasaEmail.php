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
            Mail::to($this->recipient->email)->send(new FonasaCampaignEmail($this->recipient));

            // Actualizamos el estado a "enviado"
            $this->recipient->update([
                'status' => 'enviado',
                'sent_at' => Carbon::now(),
            ]);

        } catch (\Exception $e) {
            // Si el proveedor (SendGrid/Mailgun) falla, guardamos el error para la reportería
            $this->recipient->update([
                'status' => 'rebotado',
                'bounce_reason' => $e->getMessage(),
            ]);
        }
    }
}