<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Recipient;
use App\Jobs\SendFonasaEmail;

class DispatchFonasaCampaigns extends Command
{
    // El comando que ejecutaremos
    protected $signature = 'fonasa:dispatch-campaigns';
    protected $description = 'Envía a la cola de procesamiento los correos pendientes';

    public function handle()
    {
        $this->info('Buscando destinatarios pendientes...');

        // Buscamos todos los que estén pendientes
        $recipients = Recipient::where('status', 'pendiente')->get();

        if ($recipients->isEmpty()) {
            $this->info('No hay correos pendientes por despachar.');
            return;
        }

        // Creamos una barra de progreso visual en la terminal
        $bar = $this->output->createProgressBar(count($recipients));

        $count = 0;
        foreach ($recipients as $recipient) {
            // ¡AQUÍ ESTÁ LA MAGIA! En lugar de enviar el correo directo,
            // lo mandamos a la tabla 'jobs' para que se procese en segundo plano
            SendFonasaEmail::dispatch($recipient);
            
            $count++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("¡Éxito! {$count} correos han sido enviados a la cola de procesamiento.");
    }
}