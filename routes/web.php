<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportController;

Route::get('/orders/{order}/import', [ImportController::class, 'showForm'])->name('orders.import.form');
Route::post('/orders/{order}/import', [ImportController::class, 'import'])->name('orders.import');
// Route::get('/import/template', [ImportController::class, 'downloadTemplate'])->name('import.template');
