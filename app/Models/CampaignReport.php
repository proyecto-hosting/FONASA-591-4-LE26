<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignReport extends Model
{
    // Le decimos a Eloquent que apunte a la Vista de PostgreSQL
    protected $table = 'campaign_reports_view';

    // ¡LA LLAVE MÁGICA! Le decimos que la columna principal no se llama 'id'
    protected $primaryKey = 'campaign_id';

    // Apagamos los timestamps
    public $timestamps = false;

    // Bloqueamos la escritura
    protected $guarded = ['*'];
}