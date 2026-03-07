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
}