<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ObektiNedvizhimocti;
use App\Http\Controllers\PorucheniyaUrr;
use App\Http\Controllers\Users;

Route::get('/test', App\Http\Controllers\TestController::class)->name('test');

Route::middleware(['auth'])->group(function () {

    Route::get('/', function() {
            return redirect()->route('obekti-nedvizhimosti.spisok-obektov');
        })->name('home');

    // obekti-nedvizhimosti -------------------------------------------------------------------------------
        // Список объектов недвижимости
            Route::get('/obekti-nedvizhimosti', ObektiNedvizhimocti\SpisokObektov::class)
                ->name('obekti-nedvizhimosti.spisok-obektov');

        // Создание объекта недвижимости
            Route::get('/obekti-nedvizhimosti/{id_obekta}/redaktirovat-obekt', ObektiNedvizhimocti\RedactirovatObekt::class)
                ->name('obekti-nedvizhimosti.redactirovat-obekt');

        // Сохранение нового объекта недвижимости
            Route::post('/obekti-nedvizhimosti/{id_obekta}/obnovit-obekt', ObektiNedvizhimocti\ObnovitObekt::class)
                ->name('obekti-nedvizhimosti.obnovit-obekt');

        // Удаление объекта недвижимости
            Route::delete('/obekti-nedvizhimosti/{id_obekta}', ObektiNedvizhimocti\UdalitObekt::class)
                ->name('obekti-nedvizhimosti.udalit-obekt');
    
    // dopolnitelno-vyyavlennye ---------------------------------------------------------------
        Route::get('/obekti-nedvizhimosti/{id_obekta}/dopolnitelno-vyyavlennye/{id_dopolnitelnogo_obekta}/redaktirovat-obekt',
            ObektiNedvizhimocti\DopolnitelnoVyyavlennye\RedactirovatObekt::class)
            ->name('obekty-nedvizhimosti.dopolnitelno-vyyavlennye.redactirovat-obekt');

        Route::delete('/obekti-nedvizhimosti/{id_obekta}/dopolnitelno-vyyavlennye/{id_dopolnitelnogo_obekta}',
            ObektiNedvizhimocti\DopolnitelnoVyyavlennye\UdalitObekt::class)
            ->name('obekty-nedvizhimosti.dopolnitelno-vyyavlennye.udalit-obekt');

        Route::get('/obekty-nedvizhimosti/{id_obekta}/dopolnitelno-vyyavlennye/sozdat-obekt',
            ObektiNedvizhimocti\DopolnitelnoVyyavlennye\SozdatObekt::class)
            ->name('obekty-nedvizhimosti.dopolnitelno-vyyavlennye.sozdat-obekt');

        Route::post('/obekty-nedvizhimosti/{id_obekta}/dopolnitelno-vyyavlennye/sozdat-obekt',
            ObektiNedvizhimocti\DopolnitelnoVyyavlennye\SohranitObekt::class)
            ->name('obekty-nedvizhimosti.dopolnitelno-vyyavlennye.sozdat-obekt.post');

        Route::post('/obekty-nedvizhimosti/{id_obekta}/dopolnitelno-vyyavlennye',
            ObektiNedvizhimocti\DopolnitelnoVyyavlennye\SohranitObekt::class)
            ->name('obekty-nedvizhimosti.dopolnitelno-vyyavlennye.sohranit-obekt');
    // ---------------------------------------------------------------------------------------------------

    // 

    // porucheniya-urr ------------------------------------------------------------------------------------
        // Список поручений
            Route::get('/porucheniya-urr', PorucheniyaUrr\SpisokPorucheniy::class)
                ->name('porucheniya-urr.spisok-porucheniy');

        // Создание поручения (форма)
            Route::get('/porucheniya-urr/sozdat-poruchenie', PorucheniyaUrr\SozdatPoruchenie::class)
                ->name('porucheniya-urr.sozdat-poruchenie');

        // Сохранение поручения
            Route::post('/porucheniya-urr', PorucheniyaUrr\SohranitPoruchenie::class)
                ->name('porucheniya-urr.sohranit-poruchenie');

        // Редактирование поручения (форма)
            Route::get('/porucheniya-urr/{poruchenie_urr}/redaktirovat-poruchenie', PorucheniyaUrr\RedaktirovatPoruchenie::class)
                ->name('porucheniya-urr.redaktirovat-poruchenie');

        // Обновление поручения
            Route::put('/porucheniya-urr/{poruchenie_urr}', PorucheniyaUrr\ObnovitPoruchenie::class)
                ->name('porucheniya-urr.obnovit-poruchenie');

        // Удаление поручения
            Route::delete('/porucheniya-urr/{poruchenie_urr}', PorucheniyaUrr\UdalitPoruchenie::class)
                ->name('porucheniya-urr.udalit-poruchenie');
    // ----------------------------------------------------------------------------------------------------

    // porucheniya-urr/{poruchenie_urr}/obekty-nedvizhimosti ----------------------------------------------
        // Список объектов поручения
            Route::get('/porucheniya-urr/{poruchenie_urr}/obekty-nedvizhimosti',
                PorucheniyaUrr\ObektiNedvizhimocti\SpisokObektovNedvizhimosti::class)
                ->name('porucheniya-urr.obekti-nedvizhimosti.spisok-obektov');

        // Создание объекта (сохранение)
            Route::match(['post', 'put'], '/porucheniya-urr/{poruchenie_urr}/obekty-nedvizhimosti',
                PorucheniyaUrr\ObektiNedvizhimocti\SohranitObektNedvizhimosti::class)
                ->name('porucheniya-urr.obekti-nedvizhimosti.sozdat-obekt');

        // Удаление объекта
            Route::delete('/porucheniya-urr/{poruchenie_urr}/obekty-nedvizhimosti/{obekt}',
                PorucheniyaUrr\ObektiNedvizhimocti\UdalitObektNedvizhimosti::class)
                ->name('porucheniya-urr.obekti-nedvizhimosti.udalit-obekt');
    // ----------------------------------------------------------------------------------------------------

    // users ----------------------------------------------------------------------------------------------
        // Создание пользователя (форма)
            Route::get('/users/sozdat-polzovatelya', Users\SozdatPolzovatelya::class)
                ->name('users.create');

        // Сохранение пользователя
            Route::post('/users', Users\SohranitPolzovatelya::class)
                ->name('users.store');

        // Редактирование пользователя (форма)
            Route::get('/users/{user}/redaktirovat-polzovatelya', Users\RedaktirovatPolzovatelya::class)
                ->name('users.edit');

        // Обновление пользователя
            Route::put('/users/{user}', Users\ObnovitPolzovatelya::class)
                ->name('users.update');

        // Удаление пользователя
            Route::delete('/users/{user}', Users\UdalitPolzovatelya::class)
                ->name('users.destroy');

        // Список пользователей
            Route::get('/users', Users\SpisokPolzovateley::class)
                ->name('users.index');
    // ----------------------------------------------------------------------------------------------------

    // ==================== ПРОФИЛЬ ====================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

require __DIR__.'/auth.php';
