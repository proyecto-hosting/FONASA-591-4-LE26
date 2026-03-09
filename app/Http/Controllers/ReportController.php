<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // IMPORTANTE: Importamos la fachada DB
use App\Models\Recipient; 

class ReportController extends Controller
{
    // Carga el Dashboard Consolidado
    public function traceability()
    {
        $reports = \App\Models\CampaignReport::orderBy('extraction_date', 'desc')->get();
        return view('reports.traceability', compact('reports'));
    }

    // El buscador específico que exige FONASA
    public function search(Request $request)
    {
        $query = $request->input('query');
        
        $results = Recipient::with('campaign')
            ->where('rut', $query)
            ->orWhere('email', $query)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reports.search_results', compact('results', 'query'));
    }
    
    public function exportConsolidated()
    {
        $reports = DB::table('campaign_reports_view')->orderBy('extraction_date', 'desc')->get();

        // Configuramos las cabeceras para forzar la descarga del archivo
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=consolidado_fonasa_" . date('Y-m-d_H-i') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // Creamos el archivo al vuelo en la memoria
        $callback = function() use($reports) {
            $file = fopen('php://output', 'w');
            
            // Truco vital: Insertamos el BOM de UTF-8 para que Excel lea bien los acentos
            fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Fila 1: Los títulos de las columnas (usamos ';' como separador para Excel en español)
            fputcsv($file, ['Campaña', 'Fecha Extracción', 'Total Enviados', 'Recibidos', 'Leídos', 'Rebotados'], ';');

            // Llenamos los datos
            foreach ($reports as $report) {
                fputcsv($file, [
                    $report->campaign_name,
                    \Carbon\Carbon::parse($report->extraction_date)->format('d/m/Y H:i'),
                    $report->total_sent,
                    $report->total_received,
                    $report->total_opened,
                    $report->total_errors
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}