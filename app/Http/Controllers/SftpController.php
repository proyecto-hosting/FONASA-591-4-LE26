<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str; // <-- NUEVO: Vital para generar los UUIDs
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
        $lines = explode(PHP_EOL, trim($fileContent));
        $headers = array_shift($lines); 
        
        $totalRegistros = count(array_filter($lines, function($line) {
            return !empty(trim($line));
        }));

        // 1. CREAMOS LA CAMPAÑA
        $campaign = Campaign::create([
            'name'         => 'Remesa FONASA ' . now()->format('d/m/Y H:i'),
            'type'         => 'Notificación Masiva',
            'status'       => 'pendiente',
            'total_emails' => $totalRegistros,
            'scheduled_at' => now(),
        ]);

        $recipientsData = [];
        $previewData = [];

        // 2. PREPARAMOS LOS DATOS DE LOS DESTINATARIOS


        // 3. INSERCIÓN MASIVA
        if (!empty($recipientsData)) {
            Recipient::insert($recipientsData);
        }

        return back()->with([
            'status' => "¡Ingesta exitosa! Campaña #{$campaign->id} creada con {$totalRegistros} destinatarios.",
            'preview' => array_slice($previewData, 0, 5)
        ]);
    }
}