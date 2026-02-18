<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ObektyNedvizhimostiController;
use App\Http\Controllers\PorucheniyaUrrController;

Route::get('/', [ObektyNedvizhimostiController::class, 'index'])->name('home');

// Группа маршрутов для поручений (юр. лица)
Route::prefix('porucheniya-urr')
    ->name('porucheniya-urr.')
    ->controller(PorucheniyaUrrController::class)
    ->group(function () {

        // Список поручений
        Route::get('/', 'index')->name('index');

        // Создание нового поручения
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');

        // Работа с конкретным поручением
        Route::get('/{poruchenie_urr}', 'show')->name('show');
        Route::get('/{poruchenie_urr}/edit', 'edit')->name('edit');
        Route::put('/{poruchenie_urr}', 'update')->name('update');
        Route::delete('/{poruchenie_urr}', 'destroy')->name('destroy');

        // Вложенные ресурсы (объекты недвижимости для конкретного поручения)
        Route::prefix('{poruchenie_urr}/obekty-nedvizhimosti')
            ->name('nedvizhimosti.')
            ->controller(ObektyNedvizhimostiController::class)
            ->group(function () {

                // Форма создания объекта недвижимости
                Route::get('/create', 'forma_obekta_privyazkoy_k_porucheniyu')->name('create');

                // Сохранение объекта недвижимости
                Route::post('/', 'sozdat_s_privyazkoy_k_porucheniyu')->name('store');

                // Можно добавить другие методы для объектов недвижимости:
                // Route::get('/', 'index')->name('index');
                // Route::get('/{obekt}', 'show')->name('show');
                // Route::get('/{obekt}/edit', 'edit')->name('edit');
                // Route::put('/{obekt}', 'update')->name('update');
                // Route::delete('/{obekt}', 'destroy')->name('destroy');
    });

});
