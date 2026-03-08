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
        /* * VERSIÓN MVP: 
         * Mientras creamos las tablas reales, enviaremos los contadores en cero.
         * Cuando tengas tus modelos listos, simplemente borra este bloque
         * y descomenta el bloque "VERSIÓN PRODUCCIÓN" de abajo.
         */
        /*
        $stats = [
            'total_campaigns' => 0,
            'total_emails'    => 0,
            'sent_emails'     => 0,
            'bounced_emails'  => 0,
        ]; */

        
        // VERSIÓN PRODUCCIÓN (Lógica real con PostgreSQL):
         
          $stats = [
         'total_campaigns' => Campaign::count(),
          'total_emails'    => Recipient::count(),
          'sent_emails'     => Recipient::where('status', 'enviado')->count(),
          'bounced_emails'  => Recipient::whereIn('status', ['rebote', 'error'])->count(),
          ];
         

        return view('dashboard', compact('stats'));
    }
}