<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Recipient;
use App\Jobs\SendFonasaEmail;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::orderBy('created_at', 'desc')->get();
        return view('campaigns.index', compact('campaigns'));
    }

    public function dispatchCampaign(Campaign $campaign)
    {
        // Buscamos los destinatarios pendientes que pertenezcan a esta campaña específica
        $recipients = Recipient::where('campaign_id', $campaign->id)
                               ->where('status', 'pendiente')
                               ->get();

        if ($recipients->isEmpty()) {
            return back()->with('status', 'Esta campaña ya fue procesada o no tiene destinatarios pendientes.');
        }

        // Enviamos cada correo a la tabla de 'jobs' (el motor asíncrono)
        foreach ($recipients as $recipient) {
            SendFonasaEmail::dispatch($recipient);
        }

        // Actualizamos el estado de la campaña general a 'procesada'
        $campaign->update(['status' => 'procesada']); 

        // Devolvemos al usuario a la vista con un mensaje de éxito
        return back()->with('status', "Campaña aprobada. {$recipients->count()} correos han sido encolados para su envío en segundo plano.");
    }

    public function exportCsv(Campaign $campaign)
    {
        $fileName = 'Reporte_FONASA_Campana_' . $campaign->id . '_' . date('d-m-Y') . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['RUT', 'Email', 'Estado', 'Fecha de Encolado', 'Fecha de Apertura', 'Motivo de Rebote'];

        $callback = function() use($campaign, $columns) {
            $file = fopen('php://output', 'w');
            
            // Inyectamos el BOM para que Excel reconozca los acentos y el UTF-8 mágicamente
            fputs($file, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
            
            // MAGIA PARA EXCEL: Le decimos explícitamente qué separador estamos usando
            fputs($file, "sep=;\n");
            
            // Escribimos los encabezados
            fputcsv($file, $columns, ';');

            // Procesamos de 1000 en 1000 para no saturar la memoria RAM de Google Cloud
            Recipient::where('campaign_id', $campaign->id)->chunk(1000, function($recipients) use($file) {
                foreach ($recipients as $recipient) {
                    $row['RUT'] = $recipient->rut;
                    $row['Email'] = $recipient->email;
                    $row['Estado'] = strtoupper($recipient->status);
                    $row['Fecha de Encolado'] = $recipient->created_at->format('d/m/Y H:i:s');
                    $row['Fecha de Apertura'] = $recipient->opened_at ? \Carbon\Carbon::parse($recipient->opened_at)->format('d/m/Y H:i:s') : 'N/A';
                    $row['Motivo de Rebote'] = $recipient->bounce_reason ?? 'N/A';

                    fputcsv($file, array($row['RUT'], $row['Email'], $row['Estado'], $row['Fecha de Encolado'], $row['Fecha de Apertura'], $row['Motivo de Rebote']), ';');
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}