<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index()
    {
        // Traemos todas las campañas ordenadas por la más reciente
        $campaigns = Campaign::orderBy('created_at', 'desc')->get();
        
        return view('campaigns.index', compact('campaigns'));
    }

    // Esta función reemplazará al comando de consola que hicimos antes
    public function dispatchCampaign(Campaign $campaign)
    {
        // Aquí luego pondremos la lógica para encolar los correos al hacer clic en "Aprobar"
        return back()->with('status', 'Campaña enviada a la cola de despacho.');
    }
}