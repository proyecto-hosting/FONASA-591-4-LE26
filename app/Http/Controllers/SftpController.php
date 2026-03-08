<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Campaign; // Importamos tu modelo
use App\Models\Recipient; // Importamos tu modelo

class SftpController extends Controller
{
    public function syncNow()
    {
        $fileName = 'entregas_fonasa_demo.csv';
        $disk = Storage::disk('fonasa');
        $fileContent = $disk->get($fileName);
        $lines = explode(PHP_EOL, trim($fileContent));
        $headers = array_shift($lines); // Quitamos cabeceras
        
        // Contamos rápidamente cuántos registros válidos vienen en el archivo
        $totalRegistros = count(array_filter($lines, function($line) {
            return !empty(trim($line));
        }));

        // 1. CREAMOS LA CAMPAÑA (Adaptada a tus campos exactos)
        $campaign = Campaign::create([
            'name'         => 'Remesa FONASA ' . now()->format('d/m/Y H:i'),
            'type'         => 'Notificación Masiva',
            'status'       => 'pendiente',
            'total_emails' => $totalRegistros,
            'scheduled_at' => now(), // Lo dejamos programado para envío inmediato
        ]);

        $recipientsData = [];
        $previewData = [];

        // 2. PREPARAMOS LOS DATOS DE LOS DESTINATARIOS
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            
            $data = str_getcsv($line);
            
            if (count($data) >= 3) {
                $rut = str_replace(['.', '-'], '', $data[0]); 
                $nombre = $data[1];
                $email = $data[2];
                $tipo = $data[3] ?? 'General';
                
                $recipientsData[] = [
                    'campaign_id' => $campaign->id,
                    'rut' => $rut,
                    'name' => $nombre,
                    'email' => $email,
                    'notification_type' => $tipo,
                    'status' => 'pendiente',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $previewData[] = "RUT: $rut | $nombre | Destino: $email";
            }
        }

        // 3. INSERCIÓN MASIVA EN LA BASE DE DATOS
        if (!empty($recipientsData)) {
            Recipient::insert($recipientsData);
        }

        return back()->with([
            'status' => "¡Ingesta exitosa! Campaña #{$campaign->id} creada con {$totalRegistros} destinatarios encolados.",
            'preview' => array_slice($previewData, 0, 5)
        ]);
    }
}