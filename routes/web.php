<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ObektyNedvizhimostiController;
use App\Http\Controllers\PorucheniyaUrrController;

use App\Http\Controllers\PorucheniyaUrr;

// use App\Http\Controllers\PorucheniyaUrr\Nedvizhimosti\CreateController as NedvizhimostiCreateController;
// use App\Http\Controllers\PorucheniyaUrr\Nedvizhimosti\StoreController as NedvizhimostiStoreController;

Route::get('/', [ObektyNedvizhimostiController::class, 'index'])->name('home');



Route::prefix('porucheniya-urr')
    ->name('porucheniya-urr.')
    ->group(function () {

        Route::get('/', PorucheniyaUrr\SpisokPorucheniy::class)->name('spisok-porucheniy');
        Route::post('/', PorucheniyaUrr\SohranitPoruchenie::class)->name('sohranit-poruchenie');
        Route::get('/sordat-poruchenie', PorucheniyaUrr\SozdatPoruchenie::class)->name('sozdat-poruchenie');

        // Работа с конкретным поручением
        // Route::get('/{poruchenie_urr}', function(){return'ok';})->name('pokazat-poruchenie');
        Route::get('/{poruchenie_urr}/redaktirovat-poruchenie', PorucheniyaUrr\RedaktirovatPoruchenie::class)->name('redaktirovat-poruchenie');
        // Route::put('/{poruchenie_urr}', function(){return'ok';})->name('obnovit-poruchenie');
        Route::delete('/{poruchenie_urr}', PorucheniyaUrr\UdalitPoruchenie::class)->name('udalit-posuchenie');
/* 
        // Вложенные ресурсы (объекты недвижимости для конкретного поручения)
        Route::prefix('{poruchenie_urr}/obekty-nedvizhimosti')
            ->name('nedvizhimosti.')
            ->group(function () {

                // Форма создания объекта недвижимости
                Route::get('/create', NedvizhimostiCreateController::class)->name('create');

                // Сохранение объекта недвижимости
                Route::post('/', NedvizhimostiStoreController::class)->name('store');

                // Можно добавить другие методы для объектов недвижимости:
                // Route::get('/', [SomeController::class, '__invoke'])->name('index');
                // Route::get('/{obekt}', [SomeController::class, '__invoke'])->name('show');
                // Route::get('/{obekt}/edit', [SomeController::class, '__invoke'])->name('edit');
                // Route::put('/{obekt}', [SomeController::class, '__invoke'])->name('update');
                // Route::delete('/{obekt}', [SomeController::class, '__invoke'])->name('destroy');
    });

*/
});