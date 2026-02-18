<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KkrReportController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\OrderController;

// Главная страница
Route::get('/', [KkrReportController::class, 'index'])->name('home');

// МАРШРУТЫ ДЛЯ РАСПОРЯЖЕНИЙ (ORDERS)
Route::prefix('orders')->name('orders.')->group(function () {
    
    // Список всех распоряжений
    Route::get('/', [OrderController::class, 'index'])->name('index');
    
    // Форма создания нового
    Route::get('/create', [OrderController::class, 'create'])->name('create');
    
    // Сохранение нового
    Route::post('/store', [OrderController::class, 'store'])->name('store');
    
    // Просмотр одного распоряжения
    Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    
    // Форма редактирования
    Route::get('/{order}/edit', [OrderController::class, 'edit'])->name('edit');
    
    // Обновление
    Route::put('/{order}', [OrderController::class, 'update'])->name('update');
    
    // Удаление
    Route::delete('/{order}', [OrderController::class, 'destroy'])->name('destroy');
    
    // ИМПОРТ (отдельно, не относится к конкретному распоряжению)
    Route::get('/import', [ImportController::class, 'showForm'])->name('import.form');
    Route::post('/import', [ImportController::class, 'import'])->name('import');
    
    // ЭКСПОРТ (на будущее)
    Route::get('/export', [OrderController::class, 'export'])->name('export');
    
    // ОТПРАВКА ОТВЕТА (для конкретного распоряжения)
    Route::post('/{order}/send-response', [OrderController::class, 'sendResponse'])->name('send-response');
});

// Шаблон для импорта (если нужен)
Route::get('/import/template', [ImportController::class, 'downloadTemplate'])->name('import.template');

// Можно также добавить API маршруты (если нужны)
Route::prefix('api/orders')->name('api.orders.')->group(function () {
    Route::get('/', [OrderController::class, 'apiIndex']);
    Route::get('/{order}', [OrderController::class, 'apiShow']);
});