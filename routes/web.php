<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportController;

Route::get('/orders/import', [ImportController::class, 'showForm'])->name('orders.import.form');
Route::post('/orders/import', [ImportController::class, 'import'])->name('orders.import');
// Route::get('/import/template', [ImportController::class, 'downloadTemplate'])->name('import.template');
