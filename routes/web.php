<?php


use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rutas protegidas por contraseña (Laravel Breeze)
Route::middleware(['auth', 'verified'])->group(function () {
    // Reemplazamos el dashboard por defecto por el nuestro
    Route::get('/dashboard', [ReportController::class, 'dashboard'])->name('dashboard');

    // Módulo de Campañas (Aprobación)
    Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
    Route::post('/campaigns/{campaign}/dispatch', [CampaignController::class, 'dispatchCampaign'])->name('campaigns.dispatch');

    // Módulo de Reportería Específica
    Route::get('/reports/search', [ReportController::class, 'search'])->name('reports.search');

    // Rutas de Perfil (las trae Breeze por defecto)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
