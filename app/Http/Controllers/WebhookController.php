<?php

namespace App\Http\Controllers;

use App\Models\Recipient;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WebhookController extends Controller
{
    public function handleSendgrid(Request $request)
    {
        // SendGrid envía un arreglo de eventos
        $events = $request->all();

        // En caso de que SendGrid mande un solo evento fuera de un arreglo, lo forzamos
        if (!is_array(reset($events))) {
            $events = [$events];
        }

        foreach ($events as $event) {
            $email = $event['email'] ?? null;
            $eventType = $event['event'] ?? null;

            if ($email && $eventType) {
                // Buscamos el último registro de este correo en nuestra tabla
                $recipient = Recipient::where('email', $email)
                                      ->orderBy('created_at', 'desc')
                                      ->first();

                if ($recipient) {
                    // Actualizamos según el evento reportado por SendGrid
                    if ($eventType === 'open') {
                        $recipient->update([
                            'status' => 'leído',
                            'opened_at' => Carbon::now(),
                        ]);
                    } elseif ($eventType === 'bounce' || $eventType === 'dropped') {
                        $recipient->update([
                            'status' => 'rebotado',
                            'bounce_reason' => $event['reason'] ?? 'Rechazado por el servidor de destino',
                        ]);
                    } elseif ($eventType === 'delivered') {
                        // Opcional: Marcarlo como entregado en la bandeja
                        if ($recipient->status === 'enviado') {
                            $recipient->update(['status' => 'entregado']);
                        }
                    }
                }
            }
        }

        // SendGrid exige que le respondamos con un código 200 OK
        return response()->json(['message' => 'Eventos procesados correctamente'], 200);
    }
}