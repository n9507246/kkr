<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ObektiNedvizhimocti;
use App\Http\Controllers\PorucheniyaUrr;

// Маршруты для объектов недвижимости (доступны только авторизованным)
Route::middleware(['auth'])->group(function () {

    Route::get('/', function() {
        return redirect()->route('obekti-nedvizhimosti.spisok-obektov');
    })->name('home');

    Route::get('/obekti-nedvizhimosti', ObektiNedvizhimocti\SpisokObektov::class)
        ->name('obekti-nedvizhimosti.spisok-obektov');

    Route::get('/obekti-nedvizhimosti/{id_obekta}/redaktirovat-obekt', ObektiNedvizhimocti\RedactirovatObekt::class)
        ->name('obekti-nedvizhimosti.redactirovat-obekt');

    Route::post('/obekti-nedvizhimosti/{id_obekta}/obnovit-obekt', function(){}, 'index')
        ->name('obekti-nedvizhimosti.obnovit-obekt');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


// Маршруты для поручений (тоже под auth)
    Route::prefix('porucheniya-urr')
    ->name('porucheniya-urr.')
    ->group(function () {

        Route::get('/', PorucheniyaUrr\SpisokPorucheniy::class)->name('spisok-porucheniy');
        Route::post('/', PorucheniyaUrr\SohranitPoruchenie::class)->name('sohranit-poruchenie');
        Route::get('/sordat-poruchenie', PorucheniyaUrr\SozdatPoruchenie::class)->name('sozdat-poruchenie');

        // Работа с конкретным поручением
        Route::get('/{poruchenie_urr}/redaktirovat-poruchenie', PorucheniyaUrr\RedaktirovatPoruchenie::class)
            ->name('redaktirovat-poruchenie');
        Route::put('/{poruchenie_urr}', function(){return'ok';})->name('obnovit-poruchenie');
        Route::delete('/{poruchenie_urr}', PorucheniyaUrr\UdalitPoruchenie::class)->name('udalit-posuchenie');

        // Вложенные ресурсы
        Route::prefix('{poruchenie_urr}/obekty-nedvizhimosti')
            ->name('obekti-nedvizhimosti.')
            ->group(function () {
                Route::get('/', PorucheniyaUrr\ObektiNedvizhimocti\SpisokObektovNedvizhimosti::class)
                    ->name('spisok-obektov');
                Route::post('/', PorucheniyaUrr\ObektiNedvizhimocti\SohranitObektNedvizhimosti::class)
                    ->name('sozdat-obekt');
                Route::delete('/{obekt}', PorucheniyaUrr\ObektiNedvizhimocti\UdalitObektNedvizhimosti::class)
                    ->name('udalit-obekt');
            });
    });
});
require __DIR__.'/auth.php';
