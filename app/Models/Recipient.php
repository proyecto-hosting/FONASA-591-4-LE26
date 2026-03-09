<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Recipient extends Model
{
    // 1. Configuración de Identidad (UUID)
    use HasUuids;
    protected $keyType = 'string';
    public $incrementing = false;

    // 2. Configuración de Seguridad y Asignación
    protected $guarded = [];
    protected $fillable = [
        'id', 
        'campaign_id', 
        'rut', 
        'email', 
        'pdf_filename', 
        'status', 
        'bounce_reason', 
        'sent_at', 
        'opened_at'
    ];

    // 3. Relaciones
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * TRADUCTOR DE ERRORES (Accessor)
     * Transforma el error técnico de SendGrid en un mensaje para el usuario final.
     */
    public function getFriendlyBounceReasonAttribute()
    {
        $reason = strtolower($this->bounce_reason);

        if (!$reason) {
            return 'No se registró un motivo específico.';
        }

        // Mapeo de errores técnicos a lenguaje humano
        if (str_contains($reason, 'mx info') || str_contains($reason, 'unrecognized') || str_contains($reason, 'ptr record')) {
            return 'El dominio del correo es inválido o el servidor de destino no existe.';
        }

        if (str_contains($reason, 'user unknown') || str_contains($reason, 'mailbox unavailable') || str_contains($reason, 'not found')) {
            return 'La cuenta de correo no existe o ha sido desactivada.';
        }

        if (str_contains($reason, 'quota exceeded') || str_contains($reason, 'mailbox full')) {
            return 'La casilla del destinatario está llena.';
        }

        if (str_contains($reason, 'spam') || str_contains($reason, 'blocked') || str_contains($reason, 'banned')) {
            return 'El correo fue rechazado por los filtros de seguridad (Spam) del cliente.';
        }

        return 'Error de conexión con el servidor de correo del destinatario.';
    }
}