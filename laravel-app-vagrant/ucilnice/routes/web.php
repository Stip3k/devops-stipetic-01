<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TabelaUcilniceController;
use App\Http\Controllers\RezervacijaController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('home');
    }
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Skupina poti za prijavljene uporabnike
Route::middleware(['auth'])->group(function () {
    
    // Učilnice - eksplicitno definirane poti
    Route::get('/ucilnice', [TabelaUcilniceController::class, 'index'])->name('ucilnice.index');
    Route::get('/ucilnice/create', [TabelaUcilniceController::class, 'create'])->name('ucilnice.create');
    Route::post('/ucilnice', [TabelaUcilniceController::class, 'store'])->name('ucilnice.store');
    Route::get('/ucilnice/{ucilnica}', [TabelaUcilniceController::class, 'show'])->name('ucilnice.show');
    Route::get('/ucilnice/{ucilnica}/edit', [TabelaUcilniceController::class, 'edit'])->name('ucilnice.edit');
    Route::put('/ucilnice/{ucilnica}', [TabelaUcilniceController::class, 'update'])->name('ucilnice.update');
    Route::delete('/ucilnice/{ucilnica}', [TabelaUcilniceController::class, 'destroy'])->name('ucilnice.destroy');
    Route::get('/ucilnice/{ucilnica}/calendar', [TabelaUcilniceController::class, 'calendar'])->name('ucilnice.calendar');
    Route::get('/ucilnice/{ucilnica}/rezervacije', [TabelaUcilniceController::class, 'getReservations'])->name('ucilnice.rezervacije');
    
    // Rezervacije - eksplicitno definirane poti
    Route::get('/rezervacije', [RezervacijaController::class, 'index'])->name('rezervacije.index');
    Route::get('/rezervacije/create', [RezervacijaController::class, 'create'])->name('rezervacije.create');
    Route::post('/rezervacije', [RezervacijaController::class, 'store'])->name('rezervacije.store');
    Route::get('/rezervacije/{id}', [RezervacijaController::class, 'show'])->name('rezervacije.show');
    Route::get('/rezervacije/{id}/edit', [RezervacijaController::class, 'edit'])->name('rezervacije.edit');
    Route::put('/rezervacije/{id}', [RezervacijaController::class, 'update'])->name('rezervacije.update');
    Route::delete('/rezervacije/{id}', [RezervacijaController::class, 'destroy'])->name('rezervacije.destroy');
    Route::post('/rezervacije/check-availability', [RezervacijaController::class, 'checkAvailability'])->name('rezervacije.check-availability');
});