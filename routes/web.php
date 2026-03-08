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
    $correoDestino = 'tu_correo_personal@gmail.com'; 

    // 2. Cambia EmailLog por Recipient (Asegúrate de que los campos coincidan con tu tabla)
    $log = Recipient::create([
        'email_address' => $correoDestino, 
        'status' => 'pending'
    ]);

    Mail::to($correoDestino)->send(new CobranzaTestMail($log->id));

    return "Correo enviado. ID del log: " . $log->id;
});

Route::get('/', function () {
    return view('welcome');
});

// Ruta pública para el Webhook de SendGrid
Route::post('/webhook/sendgrid', [WebhookController::class, 'handleSendgrid']);

// Rutas protegidas (Requieren Login)
Route::middleware(['auth', 'verified'])->group(function () {
    
    // 1. Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Ruta de Sincronización SFTP
    Route::post('/sftp/sync', [SftpController::class, 'syncNow'])->name('sftp.sync');

    // 3. Disparador manual para la cola en Cloud Run (Demo con Rayos X)
    Route::post('/sys/procesar-cola', function () {
        // Forzamos la limpieza de la caché de configuración
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        
        // Contamos cuántos trabajos hay realmente en la tabla jobs
        $jobsCount = \Illuminate\Support\Facades\DB::table('jobs')->count();
        
        if ($jobsCount === 0) {
            return back()->with('status', '⚠️ EL MOTOR NO ENCONTRÓ TAREAS: La tabla "jobs" de PostgreSQL está vacía. Verifica tu archivo .env en Cloud Run.');
        }

        // Ejecuta el worker forzando explícitamente la conexión a base de datos
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
});

require __DIR__.'/auth.php';