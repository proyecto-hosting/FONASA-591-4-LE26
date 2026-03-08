<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CampaignReport;
use App\Models\Recipient; 

class ReportController extends Controller
{
    // Carga el Dashboard Consolidado
    public function traceability()
    {
        // Traemos los reportes ordenados por la fecha de extracción (los más nuevos primero)
        $reports = CampaignReport::orderBy('extraction_date', 'desc')->get();
        
        return view('reports.traceability', compact('reports'));
    }

    // El buscador específico que exige FONASA
    public function search(Request $request)
    {
        $query = $request->input('query');
        
        // Buscamos por RUT o Email, trayendo también los datos de la campaña asociada
        $results = Recipient::with('campaign')
            ->where('rut', $query)
            ->orWhere('email', $query)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reports.search_results', compact('results', 'query'));
    }
}