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

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=consolidado_fonasa_" . date('Y-m-d_H-i') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($reports) {
            $file = fopen('php://output', 'w');
            
            // 1. EL TRUCO DE LOS ACENTOS: Insertamos el BOM (Byte Order Mark)
            // Esto le dice a Excel: "Ojo, soy un archivo UTF-8, respeta las Ñ y los tildes"
            fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // 2. EL TRUCO DEL SEPARADOR: Forzamos a Excel a usar el punto y coma
            // Esta línea es invisible para el usuario pero mágica para Excel
            fputs($file, "sep=;\n");

            // Fila de encabezados usando ';'
            fputcsv($file, ['Campaña', 'Fecha Extracción', 'Total Enviados', 'Recibidos', 'Leídos', 'Rebotados'], ';');

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