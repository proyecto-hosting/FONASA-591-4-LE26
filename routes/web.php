<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SftpController;

use Illuminate\Support\Facades\Mail;
use App\Models\Recipient; 
use App\Mail\CobranzaTestMail;

Route::get('/disparar-prueba', function () {
    $correoDestino = 'tu_correo_personal@gmail.com'; // Pon tu correo real aquí

    $campana = \App\Models\Campaign::firstOrCreate(
        ['name' => 'Campaña Prueba Webhook'],
        ['type' => 'pae'] 
    );

    $log = Recipient::create([
        'campaign_id' => $campana->id,
        'email' => $correoDestino, 
        'status' => 'pending',
        'rut' => '167525784',
        'pdf_filename' => 'prueba_cobranza.pdf' // Agregado para evitar el error de "Not null violation"
    ]);

    Mail::to($correoDestino)->send(new CobranzaTestMail($log->id));

    return "Correo enviado con éxito. ID del log: " . $log->id;
});

Route::get('/', function () {
    return view('welcome');
});

// Ruta pública para el Webhook de SendGrid
Route::post('/webhook/sendgrid', [WebhookController::class, 'handleSendgrid']);

// Rutas protegidas (Requieren Login)
Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/sftp/sync', [SftpController::class, 'syncNow'])->name('sftp.sync');

    Route::post('/sys/procesar-cola', function () {
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        $jobsCount = \Illuminate\Support\Facades\DB::table('jobs')->count();
        
        if ($jobsCount === 0) {
            return back()->with('status', '⚠️ EL MOTOR NO ENCONTRÓ TAREAS: La tabla "jobs" de PostgreSQL está vacía.');
        }

        \Illuminate\Support\Facades\Artisan::call('queue:work', [
            'connection' => 'database',
            '--stop-when-empty' => true,
            '--max-time' => 60
        ]);
        
        return back()->with('status', "🚀 Motor ejecutado con éxito. Se procesaron $jobsCount correos de la tabla jobs.");
    })->name('sys.queue.process');

    // Módulo de Campañas
    Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
    Route::post('/campaigns/{campaign}/dispatch', [CampaignController::class, 'dispatchCampaign'])->name('campaigns.dispatch');
    Route::get('/campaigns/{campaign}/export', [CampaignController::class, 'exportCsv'])->name('campaigns.export');

    // Módulo de Trazabilidad y Reportes
    Route::get('/trazabilidad', [ReportController::class, 'traceability'])->name('reports.traceability');
    Route::get('/reports/search', [ReportController::class, 'search'])->name('reports.search');

    // Rutas de Perfil (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/sys/forzar-vista', function () {
        try {
            \Illuminate\Support\Facades\DB::statement("
                CREATE OR REPLACE VIEW campaign_reports_view AS
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
            
            return "¡Vista 'campaign_reports_view' creada a la fuerza directamente en PostgreSQL!";
        } catch (\Exception $e) {
            return "Error creando la vista: " . $e->getMessage();
        }
    });

    Route::get('/sys/debug-vista', function () {
        $datos = \Illuminate\Support\Facades\DB::select('SELECT * FROM campaign_reports_view');
        return response()->json($datos);
    });
});

require __DIR__.'/auth.php';