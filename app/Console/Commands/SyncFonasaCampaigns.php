<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Campaign;
use App\Models\Recipient;
use Carbon\Carbon;

class SyncFonasaCampaigns extends Command
{
    // El nombre del comando que ejecutarás en la terminal
    protected $signature = 'fonasa:sync-campaigns';
    protected $description = 'Lee el SFTP de FONASA, procesa el CSV y prepara la base de datos para el envío';

    public function handle()
    {
        $this->info('Iniciando sincronización con SFTP FONASA...');

        // NOTA: 'sftp_fonasa' será configurado en filesystems.php después.
        // Por ahora, usamos el disco 'local' para probar en IDX.
        $disk = Storage::disk('local'); 
        $campaignFolder = 'fonasa_testing'; // Carpeta donde dejaremos los archivos de prueba

        // 1. Buscamos el archivo CSV de la campaña
        $files = $disk->files($campaignFolder);
        $csvFile = collect($files)->first(fn($file) => str_ends_with($file, '.csv'));

        if (!$csvFile) {
            $this->error('No se encontró ningún archivo CSV de campaña para procesar.');
            return;
        }

        $this->info("Archivo encontrado: {$csvFile}. Procesando...");

        // 2. Creamos la Campaña en la Base de Datos
        $campaign = Campaign::create([
            'name' => 'Campaña Morosidad - ' . Carbon::now()->format('d/m/Y'),
            'type' => 'Morosidad',
            'status' => 'pendiente', // Queda pendiente de aprobación humana
            'total_emails' => 0,
        ]);

        // 3. Leemos el CSV y creamos los Destinatarios
        $csvContent = $disk->get($csvFile);
        $rows = explode("\n", $csvContent);
        $count = 0;

        foreach ($rows as $index => $row) {
            if ($index === 0 || empty(trim($row))) continue; // Saltamos la cabecera y líneas vacías

            // Asumimos un CSV con formato: RUT;EMAIL;PDF_FILENAME
            $data = str_getcsv($row, ';');
            
            if (count($data) >= 3) {
                $pdfFilename = trim($data[2]);

                // Verificamos si el PDF referenciado realmente existe en el SFTP
                if (!$disk->exists($campaignFolder . '/' . $pdfFilename)) {
                    $this->warn("El PDF {$pdfFilename} no existe para el RUT {$data[0]}. Se omitirá.");
                    continue;
                }

                Recipient::create([
                    'id' => Str::uuid(),
                    'campaign_id' => $campaign->id,
                    'rut' => trim($data[0]),
                    'email' => trim($data[1]),
                    'pdf_filename' => $pdfFilename,
                    'status' => 'pendiente',
                ]);
                $count++;
            }
        }

        // Actualizamos el total real de la campaña
        $campaign->update(['total_emails' => $count]);

        $this->info("¡Sincronización exitosa! Se cargaron {$count} destinatarios a la campaña ID: {$campaign->id}.");
    }
}