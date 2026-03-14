<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\InvoiceController;

Route::get('/', [InvoiceController::class, 'create'])->name('invoice.form');
Route::post('/generate', [InvoiceController::class, 'generate'])->name('invoice.generate');
