<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\Recipient;

class DashboardController extends Controller
{
    public function index()
    {
        // Consolidamos todas las métricas en el array $stats que tu vista está esperando
        $stats = [
            'total_campaigns' => Campaign::count(),
            
            'total_emails'    => Recipient::count(),
            
            // Correos enviados o en proceso que no han fallado
            'sent_emails'     => Recipient::whereIn('status', ['enviado', 'entregado', 'leído'])->count(),
            
            // AQUÍ ESTABA EL ERROR: Ahora busca exactamente 'rebotado'
            'bounced_emails'  => Recipient::where('status', 'rebotado')->count(), 
        ];

        // Opcional: Si tu vista también necesita las últimas campañas
        $recentCampaigns = Campaign::orderBy('created_at', 'desc')->take(5)->get();

        return view('dashboard', compact('stats', 'recentCampaigns'));
    }
}