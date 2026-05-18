<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

Route::get('/', [OrderController::class, 'index'])->name('orders.index');
Route::get('/send-receipt/{id}', [OrderController::class, 'sendReceipt'])->name('orders.sendReceipt');