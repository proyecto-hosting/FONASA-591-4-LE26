<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Creamos la vista usando los nombres exactos de tu tabla recipients
        DB::statement("
            CREATE OR REPLACE VIEW fonasa_campaign_reports AS
            SELECT 
                c.id AS campaign_id,
                c.name AS campaign_name,
                c.type AS campaign_type,
                c.created_at AS extraction_date,
                COUNT(r.id) AS total_sent,
                SUM(CASE WHEN r.status IN ('entregado', 'leído') THEN 1 ELSE 0 END) AS total_received,
                SUM(CASE WHEN r.status = 'leído' THEN 1 ELSE 0 END) AS total_opened,
                SUM(CASE WHEN r.status = 'rebotado' THEN 1 ELSE 0 END) AS total_errors
            FROM campaigns c
            LEFT JOIN recipients r ON c.id = r.campaign_id
            GROUP BY c.id, c.name, c.type, c.created_at;
        ");
    }

    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS fonasa_campaign_reports;");
    }
};