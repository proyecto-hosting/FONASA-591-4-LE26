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

        if (!$disk->exists($fileName)) {
            return back()->with('error', 'No se encontró el archivo de remesas en el servidor.');
        }

        Log::info('Iniciando lectura e inserción del archivo CSV de FONASA...');

        $fileContent = $disk->get($fileName);
        $lines = explode(PHP_EOL, trim($fileContent));
        $headers = array_shift($lines); // Quitamos cabeceras
        
        // 1. CREAMOS LA CAMPAÑA EN LA BASE DE DATOS
        $campaign = Campaign::create([
            'name' => 'Remesa FONASA ' . now()->format('d/m/Y H:i'),
            'source_file' => $fileName,
            'status' => 'pendiente'
        ]);

        $recipientsData = [];
        $previewData = [];

        // 2. PREPARAMOS LOS DATOS
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            
            $data = str_getcsv($line);
            
            if (count($data) >= 3) {
                $rut = str_replace(['.', '-'], '', $data[0]); // Limpiamos el RUT por si viene con puntos
                $nombre = $data[1];
                $email = $data[2];
                $tipo = $data[3] ?? 'General';
                
                // Agregamos al arreglo para inserción masiva
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

        // 3. INSERCIÓN MASIVA (Para no matar el rendimiento de Cloud SQL)
        if (!empty($recipientsData)) {
            Recipient::insert($recipientsData);
        }

        // 4. (Opcional por ahora) Mover el archivo a una carpeta de "procesados" para que no se vuelva a leer mañana
        // $disk->move($fileName, 'procesados/' . $fileName . '_' . time());

        return back()->with([
            'status' => "¡Sincronización y guardado exitoso! Se vinculó la campaña #{$campaign->id} con " . count($recipientsData) . " destinatarios.",
            'preview' => array_slice($previewData, 0, 5) // Mostramos solo los primeros 5 en pantalla para no saturar
        ]);
    }
}