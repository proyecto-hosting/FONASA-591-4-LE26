<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Campaign extends Model
{
    use HasFactory;
    // Permitimos que el script llene estos campos masivamente
    protected $fillable = [
        'name', 
        'type', 
        'status', 
        'total_emails', 
        'scheduled_at'
    ];

    // Relación: Una campaña tiene muchos destinatarios
    public function recipients()
    {
        return $this->hasMany(Recipient::class);
    }
}