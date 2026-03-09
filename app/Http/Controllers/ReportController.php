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
}