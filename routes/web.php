<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Clientes
    Route::resource('clients', ClientController::class);

    // Proyectos
    Route::resource('projects', ProjectController::class);

    // Tareas
    Route::resource('tasks', TaskController::class)->except(['index', 'create']);
    Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus'])
        ->name('tasks.update-status');

    // Cotizaciones
    Route::resource('quotes', QuoteController::class);
    Route::post('quotes/{quote}/send',      [QuoteController::class, 'markAsSent'])->name('quotes.send');
    Route::post('quotes/{quote}/approve',   [QuoteController::class, 'approve'])->name('quotes.approve');
    Route::post('quotes/{quote}/reject',    [QuoteController::class, 'reject'])->name('quotes.reject');
    Route::post('quotes/{quote}/duplicate', [QuoteController::class, 'duplicate'])->name('quotes.duplicate');
    Route::get('quotes/{quote}/pdf',        [QuoteController::class, 'downloadPdf'])->name('quotes.pdf');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
