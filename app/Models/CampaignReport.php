<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignReport extends Model
{
    // Le decimos a Eloquent que apunte a la Vista de PostgreSQL, no a una tabla normal
    protected $table = 'campaign_reports_view';

    // Apagamos los timestamps porque las vistas no se "actualizan" ni "crean" desde Laravel
    public $timestamps = false;
    
    // Evitamos problemas de mutabilidad bloqueando la escritura
    protected $guarded = ['*']; 
}