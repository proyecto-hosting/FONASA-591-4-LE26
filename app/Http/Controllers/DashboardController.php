<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Descomenta estas líneas cuando tengas tus modelos listos
use App\Models\Campaign;
use App\Models\Recipient;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Métricas Globales (Contando los estados exactos de tu BD)
        $totalCampaigns = Campaign::count();
        $totalSent = Recipient::count(); // Todos los encolados/enviados
        
        // Entregados (Incluye los que ya fueron leídos)
        $totalDelivered = Recipient::whereIn('status', ['entregado', 'leído'])->count();
        
        // Abiertos
        $totalOpened = Recipient::where('status', 'leído')->count();
        
        // REBOTADOS (Aquí estaba el problema, forzamos a buscar 'rebotado')
        $totalBounced = Recipient::where('status', 'rebotado')->count();

        // 2. Últimas Campañas para la tabla rápida del dashboard
        $recentCampaigns = Campaign::orderBy('created_at', 'desc')->take(5)->get();

        return view('dashboard', compact(
            'totalCampaigns', 
            'totalSent', 
            'totalDelivered', 
            'totalOpened', 
            'totalBounced',
            'recentCampaigns'
        ));

        return view('dashboard', compact('stats'));
    }
}