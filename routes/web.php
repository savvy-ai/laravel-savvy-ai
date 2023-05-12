<?php

use SavvyAI\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

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

Route::get('@{trainable:handle}', [ChatController::class, 'show'])->name('chat.show');
Route::post('@{trainable:handle}', [ChatController::class, 'ask'])->name('chat.ask');
Route::post('@{trainable:handle}/history', [ChatController::class, 'history'])->name('chat.history');
Route::post('@{trainable:handle}/clear', [ChatController::class, 'clear'])->name('chat.clear');