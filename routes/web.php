<?php

use Illuminate\Support\Facades\Route;
use SavvyAI\Http\Controllers\ChatController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Demo Chat
Route::prefix(config('savvy-ai.path'))->group(function () {
    Route::get('/{trainable:handle}', [ChatController::class, 'show'])->name('demo.chat');
    Route::post('/{trainable:handle}', [ChatController::class, 'ask'])->name('demo.chat.ask');
    Route::post('/{trainable:handle}/history', [ChatController::class, 'history'])->name('demo.chat.history');
});
