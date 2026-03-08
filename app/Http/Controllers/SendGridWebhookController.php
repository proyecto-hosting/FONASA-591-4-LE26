<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class SendGridWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info($request->all());

        // 1. SendGrid envía un array de eventos. Lo decodificamos.
        $events = $request->json()->all();

        // (Opcional) Log para debuggear en Project IDX y ver qué llega realmente
        // Log::info('SendGrid Webhook Payload:', $events);

        if (!is_array($events)) {
            return response()->json(['status' => 'ignored'], 200);
        }

        foreach ($events as $event) {
            // Verificamos que sea el evento de apertura y que traiga nuestro ID personalizado
            if (isset($event['event']) && $event['event'] === 'open' && isset($event['recipient_id'])) {
                
                // 2. Actualizamos la tabla recipients
                DB::table('recipients')
                    ->where('id', $event['recipient_id'])
                    ->update([
                        'status' => 'leído',
                        'opened_at' => now(), // o usar el timestamp de SendGrid: date('Y-m-d H:i:s', $event['timestamp'])
                        'updated_at' => now(),
                    ]);
            }
        }

        // 3. Siempre devolver 200 OK rápido, o SendGrid reintentará la petición durante 3 días
        return response()->json(['status' => 'success'], 200);
    }
}