<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendGridWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $events = $request->json()->all();

        // Si SendGrid solo manda un ping de validación o un formato raro, salimos rápido.
        if (!is_array($events)) {
            return response()->json(['status' => 'ignored'], 200);
        }

        foreach ($events as $event) {
            // Verificamos que el evento traiga el ID de nuestro registro en base de datos.
            // OJO: SendGrid manda esto en la raíz del evento si lo pasamos como 'custom_args'.
            if (!isset($event['email_log_id'])) {
                continue; // Si es un evento sin ID nuestro (como el de prueba que acabas de hacer), lo ignoramos.
            }

            $logId = $event['email_log_id'];
            $timestamp = Carbon::createFromTimestamp($event['timestamp'])->toDateTimeString();

            // Usamos un Switch para manejar cada estado exigido por las bases[cite: 7].
            switch ($event['event']) {
                
                case 'delivered':
                    DB::table('email_logs')->where('id', $logId)->update([
                        'status' => 'delivered',
                        'delivered_at' => $timestamp,
                        'updated_at' => now(),
                    ]);
                    break;

                case 'open':
                    // SendGrid puede mandar múltiples 'open' si el usuario abre el correo varias veces.
                    // Solo actualizamos el estado si no estaba ya marcado como 'opened'.
                    DB::table('email_logs')->where('id', $logId)
                                           ->where('status', '!=', 'opened')
                                           ->update([
                        'status' => 'opened',
                        'opened_at' => $timestamp,
                        'updated_at' => now(),
                    ]);
                    break;

                case 'bounce':
                case 'dropped':
                case 'deferred':
                    // Extraemos el motivo exacto del rebote[cite: 7].
                    $reason = $event['reason'] ?? 'Motivo no especificado por SendGrid';
                    $statusCode = $event['status'] ?? 'N/A';
                    
                    DB::table('email_logs')->where('id', $logId)->update([
                        'status' => 'failed',
                        'reject_reason' => $statusCode . ' - ' . $reason,
                        'updated_at' => now(),
                    ]);
                    break;
            }
        }

        return response()->json(['status' => 'success'], 200);
    }
}