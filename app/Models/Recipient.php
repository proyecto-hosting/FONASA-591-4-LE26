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

    /**
     * MUTADOR DE RUT
     * Intercepta el RUT antes de guardarlo en la base de datos, 
     * limpia puntos, guiones y espacios, y lo formatea a XXXXXXXX-X
     */
    public function setRutAttribute($value)
    {
        // 1. Limpieza extrema: Quitamos espacios, puntos, guiones y dejamos solo números y la K
        $rutLimpio = preg_replace('/[^0-9kK]/', '', $value);
        
        // 2. Pasamos la 'k' a mayúscula por si la escribieron en minúscula
        $rutLimpio = strtoupper($rutLimpio);

        // Si por algún motivo enviaron un campo vacío, lo dejamos pasar para que la BD tire el error o lo maneje
        if (empty($rutLimpio)) {
            $this->attributes['rut'] = $value;
            return;
        }

        // 3. Separamos el cuerpo del Dígito Verificador (el último carácter)
        $cuerpo = substr($rutLimpio, 0, -1);
        $dv = substr($rutLimpio, -1);

        // 4. Armamos el formato estándar (Ej: 16752578-4) y lo pasamos al modelo
        $this->attributes['rut'] = $cuerpo . '-' . $dv;
    }

    /**
     * MUTADOR DE EMAIL
     * Elimina espacios en blanco accidentales y fuerza minúsculas 
     * para estandarizar la base de datos y evitar errores SMTP.
     */
    public function setEmailAttribute($value)
    {
        // trim() quita espacios al inicio y final. strtolower() pasa a minúscula.
        $this->attributes['email'] = strtolower(trim($value));
    }

    /**
     * MUTADOR DE NOMBRE DE ARCHIVO
     * Previene que espacios invisibles rompan la búsqueda en Google Cloud Storage.
     */
    public function setPdfFilenameAttribute($value)
    {
        $this->attributes['pdf_filename'] = trim($value);
    }
}