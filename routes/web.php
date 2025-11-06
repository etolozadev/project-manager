<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\QuotationController;


Route::get('/', function () {
    return Inertia::render('auth/login');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('clients', [\App\Http\Controllers\ClientController::class, 'index'])->name('clients');
    Route::post('clients', [\App\Http\Controllers\ClientController::class, 'store'])->name('clients.store');
    Route::put('clients/{client}', [\App\Http\Controllers\ClientController::class, 'update'])->name('clients.update');
    Route::delete('clients/{client}', [\App\Http\Controllers\ClientController::class, 'destroy'])->name('clients.destroy');
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    Route::get('quotes', [\App\Http\Controllers\QuotationController::class, 'index'])->name('quotes');
    Route::get('quotes/create', [\App\Http\Controllers\QuotationController::class, 'create'])->name('quotes.create');
    Route::post('quotes', [\App\Http\Controllers\QuotationController::class, 'store'])->name('quotes.store');
    Route::get('quotes/{quotation}', [\App\Http\Controllers\QuotationController::class, 'show'])->name('quotes.show');
    Route::put('quotes/{quotation}', [\App\Http\Controllers\QuotationController::class, 'update'])->name('quotes.update');
    Route::delete('quotes/{quotation}', [\App\Http\Controllers\QuotationController::class, 'destroy'])->name('quotes.destroy');

    Route::get('users', [\App\Http\Controllers\UserController::class, 'index'])->name('users');
    Route::post('users', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
    Route::put('users/{user}', [\App\Http\Controllers\UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
});

require __DIR__.'/settings.php';
