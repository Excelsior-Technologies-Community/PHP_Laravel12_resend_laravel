<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

Route::get('/', [OrderController::class, 'index'])->name('orders.index');
Route::get('/send-receipt/{id}', [OrderController::class, 'sendReceipt'])->name('orders.sendReceipt');
Route::post('/send-batch', [OrderController::class, 'sendBatchReceipts'])->name('orders.sendBatch');
Route::get('/email-history', [OrderController::class, 'emailHistory'])->name('orders.emailHistory');
Route::get('/email-report', [OrderController::class, 'emailReport'])->name('orders.emailReport');
Route::get('/resend-failed/{id}', [OrderController::class, 'resendFailedEmail'])->name('orders.resendFailed');
Route::get('/export', [OrderController::class, 'export'])->name('orders.export');