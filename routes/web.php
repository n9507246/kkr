<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ObektiNedvizhimocti;
use App\Http\Controllers\PorucheniyaUrrController;
use App\Http\Controllers\PorucheniyaUrr;

Route::get('/', function() {return redirect()->route('home');});
Route::get('/оbekti-nedvizhimocti', ObektiNedvizhimocti\SpisokObektov::class, 'index')->name('home');

Route::prefix('porucheniya-urr')
    ->name('porucheniya-urr.')
    ->group(function () {

        Route::get('/', PorucheniyaUrr\SpisokPorucheniy::class)->name('spisok-porucheniy');
        Route::post('/', PorucheniyaUrr\SohranitPoruchenie::class)->name('sohranit-poruchenie');
        Route::get('/sordat-poruchenie', PorucheniyaUrr\SozdatPoruchenie::class)->name('sozdat-poruchenie');

        // Работа с конкретным поручением
        Route::get('/{poruchenie_urr}/redaktirovat-poruchenie', PorucheniyaUrr\RedaktirovatPoruchenie::class)->name('redaktirovat-poruchenie');
        Route::put('/{poruchenie_urr}', function(){return'ok';})->name('obnovit-poruchenie');
        Route::delete('/{poruchenie_urr}', PorucheniyaUrr\UdalitPoruchenie::class)->name('udalit-posuchenie');

        // Вложенные ресурсы (объекты недвижимости для конкретного поручения)
        Route::prefix('{poruchenie_urr}/obekty-nedvizhimosti')
            ->name('obekti-nedvizhimosti.')  // ВАЖНО: добавили точку в конце
            ->group(function () {

                // СПИСОК объектов недвижимости (porucheniya-urr.obekti-nedvizhimosti.spisok-obektov)
                Route::get('/', PorucheniyaUrr\ObektiNedvizhimocti\SpisokObektovNedvizhimosti::class)
                    ->name('spisok-obektov');

                Route::post('/', PorucheniyaUrr\ObektiNedvizhimocti\SohranitObektNedvizhimosti::class)
                    ->name('sozdat-obekt');

                Route::delete('/{obekt}', PorucheniyaUrr\ObektiNedvizhimocti\UdalitObektNedvizhimosti::class)
                    ->name('udalit-obekt');
/*
                // Форма создания объекта недвижимости (porucheniya-urr.obekti-nedvizhimosti.sozdat)
                Route::get('/sozdat', PorucheniyaUrr\ObektiNedvizhimocti\SozdatObektNedvizhimosti::class)
                    ->name('sozdat');

                // Сохранение объекта недвижимости (porucheniya-urr.obekti-nedvizhimosti.sohranit)
                Route::post('/', PorucheniyaUrr\ObektiNedvizhimocti\SohranitObektNedvizhimosti::class)
                    ->name('sohranit');

                // Просмотр конкретного объекта (porucheniya-urr.obekti-nedvizhimosti.pokazat)
                Route::get('/{obekt}', PorucheniyaUrr\ObektiNedvizhimocti\PokazatObektNedvizhimosti::class)
                    ->name('pokazat');

                // Редактирование объекта (porucheniya-urr.obekti-nedvizhimosti.redaktirovat)
                Route::get('/{obekt}/redaktirovat', PorucheniyaUrr\ObektiNedvizhimocti\RedaktirovatObektNedvizhimosti::class)
                    ->name('redaktirovat');

                // Обновление объекта (porucheniya-urr.obekti-nedvizhimosti.obnovit)
                Route::put('/{obekt}', PorucheniyaUrr\ObektiNedvizhimocti\ObnovitObektNedvizhimosti::class)
                    ->name('obnovit');

                // Удаление объекта (porucheniya-urr.obekti-nedvizhimosti.udalit)
                Route::delete('/{obekt}', PorucheniyaUrr\ObektiNedvizhimocti\UdalitObektNedvizhimosti::class)
                    ->name('udalit');*/
            });
    });
