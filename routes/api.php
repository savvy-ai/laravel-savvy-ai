<?php

use Illuminate\Support\Facades\Route;
use SavvyAI\Http\Controllers\ChatController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::group(['prefix' => 'savvy'], function () {
    Route::post('chat', [ChatController::class, 'chat'])->name('chat');

    Route::post('sms/webhook', [SmsController::class, 'webhook'])->name('sms.webhook');
    Route::post('sms/chat', [SmsController::class, 'chat'])->name('sms.chat');

    Route::post('whatsapp/webhook', [WhatsAppController::class, 'webhook'])->name('whatsapp.webhook');
    Route::post('whatsapp/chat', [WhatsAppController::class, 'chat'])->name('whatsapp.chat');
});
