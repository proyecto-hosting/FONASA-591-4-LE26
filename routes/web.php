<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SftpController; // <-- NUEVO: Importamos el SftpController

Route::get('/', function () {
    return view('welcome');
});

// Disparador manual para la cola en Cloud Run (Demo)
Route::post('/sys/procesar-cola', function () {
    \Illuminate\Support\Facades\Log::info('Iniciando procesamiento de colas por demanda...');
    
    // Ejecuta el worker, pero se detiene automáticamente cuando la cola queda vacía
    \Illuminate\Support\Facades\Artisan::call('queue:work', [
        '--stop-when-empty' => true,
        '--max-time' => 60 // Evita que Cloud Run mate el proceso por tiempo
    ]);
    
    return back()->with('status', 'Motor de envíos ejecutado: Se despacharon los correos encolados.');
})->name('sys.queue.process');

// Ruta pública para el Webhook
Route::post('/webhook/sendgrid', [WebhookController::class, 'handleSendgrid']);

// Rutas protegidas (Requieren Login)
Route::middleware(['auth', 'verified'])->group(function () {
    
    // 1. Un único Dashboard oficial apuntando al controlador correcto
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. NUEVO: La ruta que recibe el clic del botón SFTP
    Route::post('/sftp/sync', [SftpController::class, 'syncNow'])->name('sftp.sync');

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