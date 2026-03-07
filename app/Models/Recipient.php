<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipient extends Model
{
    // Le decimos a Laravel que el ID no es numérico autoincremental, sino un UUID (String)
    protected $keyType = 'string';
    public $incrementing = false;

    // Permitimos la asignación masiva de estos campos
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

    // Relación: Un destinatario pertenece a una campaña
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}