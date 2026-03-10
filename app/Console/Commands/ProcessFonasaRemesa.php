<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Campaign;
use App\Models\Recipient;
use Illuminate\Support\Str;
use App\Jobs\DispatchFonasaEmail;

class ProcessFonasaRemesa extends Command
{
    protected $signature = 'fonasa:procesar-remesa';
    protected $description = 'Busca y procesa nuevos archivos de remesas de FONASA en el disco configurado (Local o GCS)';

    public function handle()
    {
        $diskName = env('FONASA_DISK', 'fonasa_local');
        $this->info("Iniciando exploración en el disco: [{$diskName}]");

        try {
            $disco = Storage::disk($diskName);
            $archivos = $disco->files('');

            if (empty($archivos)) {
                $this->warn("No se encontraron archivos nuevos en la bandeja de entrada.");
                return 0;
            }

            foreach ($archivos as $archivo) {
                if (str_ends_with(strtolower($archivo), '.csv') || str_ends_with(strtolower($archivo), '.txt')) {
                    $this->line("<fg=green>✓ Base de datos detectada:</> {$archivo}");
                    $this->procesarCSV($disco, $archivo);
                } 
                elseif (str_ends_with(strtolower($archivo), '.pdf')) {
                    $this->line("<fg=blue>✓ Documento adjunto detectado:</> {$archivo}");
                }
            }

            $this->info("Exploración finalizada.");
            return 0;

        } catch (\Exception $e) {
            $this->error("Error al conectar con el disco: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Módulo de Ingesta Eficiente (Línea por Línea)
     */
    private function procesarCSV($disco, $archivo)
    {
        $this->info("Procesando filas de: {$archivo}");

        // 1. Creamos una Campaña nueva en la BD usando el nombre del archivo
        $campana = Campaign::create([
            'name' => 'Remesa FONASA - ' . str_replace(['.csv', '.txt'], '', $archivo) . ' - ' . date('d/m/Y H:i'),
            'type' => 'Notificación Masiva',
            'status' => 'activa'
        ]);

        // 2. Abrimos el archivo como un "flujo" (Stream) para no colapsar la RAM
        $stream = $disco->readStream($archivo);
        
        $fila = 0;
        $insertados = 0;

        // Leemos línea por línea
        while (($datos = fgetcsv($stream, 1000, ";")) !== false) {
            $fila++;
            
            // Saltamos la fila 1 si son los encabezados (rut;email;pdf_filename)
            if ($fila === 1 && strtolower($datos[0]) === 'rut') {
                continue;
            }

            // Validamos que la fila tenga al menos 3 columnas
            if (count($datos) >= 3) {
                // 1. Insertamos en la base de datos y lo guardamos en la variable $destinatario
                $destinatario = Recipient::create([
                    'campaign_id' => $campana->id,
                    'rut' => trim($datos[0]),
                    'email' => trim($datos[1]),
                    'pdf_filename' => trim($datos[2]),
                    'status' => 'encolado'
                ]);

                // 2. ¡LA LÍNEA MÁGICA! Mandamos a este destinatario a la fila de trabajo
                DispatchFonasaEmail::dispatch($destinatario);

                $insertados++;
            }
        }

        fclose($stream);
        $this->info("Se inyectaron {$insertados} registros a la base de datos (Estado: Encolado).");

        // 3. Movemos el archivo a la carpeta 'procesados' para no volver a leerlo mañana
        $nuevoNombre = 'procesados/' . date('Y-m-d_H-i-s') . '_' . $archivo;
        $disco->move($archivo, $nuevoNombre);
        $this->warn("Archivo movido a: {$nuevoNombre}");
    }
}