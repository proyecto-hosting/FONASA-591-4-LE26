<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Campaign;
use App\Models\Recipient;

class SftpController extends Controller
{
    public function syncNow()
    {
        $fileName = 'entregas_fonasa_demo.csv';
        $disk = Storage::disk('fonasa');

        if (!$disk->exists($fileName)) {
            return back()->with('error', 'No se encontró el archivo de remesas en el servidor.');
        }

        Log::info('Iniciando lectura e inserción del archivo CSV...');

        $fileContent = $disk->get($fileName);
        
        // 1. Magia Anti-Windows/Mac: Estandariza los saltos de línea
        $lines = preg_split('/\r\n|\r|\n/', trim($fileContent));
        $headers = array_shift($lines); 
        
        $totalRegistros = count(array_filter($lines, function($line) {
            return !empty(trim($line));
        }));

        // 2. CREAMOS LA CAMPAÑA
        $campaign = Campaign::create([
            'name'         => 'Remesa FONASA ' . now()->format('d/m/Y H:i'),
            'type'         => 'Notificación Masiva',
            'status'       => 'pendiente',
            'total_emails' => $totalRegistros,
            'scheduled_at' => now(),
        ]);

        $recipientsData = [];
        $previewData = [];

        // 3. PREPARAMOS LOS DATOS DE LOS DESTINATARIOS
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            
            // Magia Anti-Excel: Detecta si usa punto y coma o coma
            $separador = strpos($line, ';') !== false ? ';' : ',';
            $data = str_getcsv($line, $separador);
            
            if (count($data) >= 3) {
                $rut = str_replace(['.', '-'], '', $data[0]); 
                $email = isset($data[2]) ? trim($data[2]) : null; 
                
                if($email) {
                    $recipientsData[] = [
                        'id' => Str::uuid()->toString(), 
                        'campaign_id' => $campaign->id,
                        'rut' => $rut,
                        'email' => $email,
                        'pdf_filename' => $rut . '_notificacion_fonasa.pdf', 
                        'status' => 'pendiente',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $previewData[] = "RUT: $rut | Destino: $email";
                }
            }
        }

        // 4. ¡EL PASO FALTANTE! INSERCIÓN MASIVA EN LA BASE DE DATOS
        if (!empty($recipientsData)) {
            Recipient::insert($recipientsData);
        }

        return back()->with([
            'status' => "¡Ingesta exitosa! Campaña #{$campaign->id} creada con {$totalRegistros} destinatarios.",
            'preview' => array_slice($previewData, 0, 5)
        ]);
    }
}