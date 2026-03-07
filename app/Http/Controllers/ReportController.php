<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Recipient;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function dashboard()
    {
        // Calculamos las métricas en tiempo real para las tarjetas
        $stats = [
            'total_campaigns' => Campaign::count(),
            'total_emails' => Recipient::count(),
            'sent_emails' => Recipient::where('status', 'enviado')->count(),
            'bounced_emails' => Recipient::where('status', 'rebotado')->count(),
        ];

        return view('dashboard', compact('stats'));
    }

    public function traceability(Request $request)
    {
        $query = $request->input('query'); // Puede ser RUT o Email
        $recipient = null;

        if ($query) {
            // Buscamos el último envío que coincida con el RUT o el Email
            $recipient = Recipient::where('rut', $query)
                                  ->orWhere('email', $query)
                                  ->with('campaign') // Traemos los datos de la campaña asociada
                                  ->orderBy('created_at', 'desc')
                                  ->first();
        }

        return view('reports.traceability', compact('recipient', 'query'));
    }
}