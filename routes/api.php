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

// Route::post('sms/webhook', [SmsController::class, 'webhook'])->name('sms.webhook');
// Route::post('sms/{property:handle}', [SmsController::class, 'ask'])->name('sms.ask');

// Route::post('whatsapp/webhook', [WhatsAppController::class, 'webhook'])->name('whatsapp.webhook');
// Route::post('whatsapp/{property:handle}', [WhatsAppController::class, 'ask'])->name('whatsapp.ask');

Route::group(['prefix' => 'savvy'], function () {
    Route::get('config', [ChatController::class, 'config'])->name('config');
    Route::post('chat', [ChatController::class, 'chat'])->name('chat');
});
