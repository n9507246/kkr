<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ObektiNedvizhimocti;
use App\Http\Controllers\PorucheniyaUrr;

Route::get('/test', App\Http\Controllers\TestController::class)->name('test.index');

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
    //  ---------------------------------------------------------------------------------------------------

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
            Route::put('/porucheniya-urr/{poruchenie_urr}', function() {
                return 'ok';
            })->name('porucheniya-urr.obnovit-poruchenie');

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
            Route::put('/porucheniya-urr/{poruchenie_urr}/obekty-nedvizhimosti',
                PorucheniyaUrr\ObektiNedvizhimocti\SohranitObektNedvizhimosti::class)
                ->name('porucheniya-urr.obekti-nedvizhimosti.sozdat-obekt');

        // Удаление объекта
            Route::delete('/porucheniya-urr/{poruchenie_urr}/obekty-nedvizhimosti/{obekt}',
                PorucheniyaUrr\ObektiNedvizhimocti\UdalitObektNedvizhimosti::class)
                ->name('porucheniya-urr.obekti-nedvizhimosti.udalit-obekt');
    // ----------------------------------------------------------------------------------------------------

    // ==================== ПРОФИЛЬ ====================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

require __DIR__.'/auth.php';
