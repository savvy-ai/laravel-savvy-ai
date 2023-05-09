<?php

use SavvyAI\Http\Controllers\ChatController;
use SavvyAI\Http\Controllers\TrainableController;
use SavvyAI\Http\Controllers\TrainingController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin'       => Route::has('login'),
        'canRegister'    => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion'     => PHP_VERSION,
    ]);
});

Route::get('@{trainable:handle}', [ChatController::class, 'show'])->name('chat.show');
Route::post('@{trainable:handle}', [ChatController::class, 'ask'])->name('chat.ask');
Route::post('@{trainable:handle}/history', [ChatController::class, 'history'])->name('chat.history');
Route::post('@{trainable:handle}/clear', [ChatController::class, 'clear'])->name('chat.clear');

Route::middleware(['auth', 'verified',])->group(function () {
    Route::get('/dashboard', fn() => redirect()->route('trainable.index'))->name('dashboard');

    Route::get('/trainable', [TrainableController::class, 'index'])->name('trainable.index');
    Route::get('/trainable/create', [TrainableController::class, 'create'])->name('trainable.create');
    Route::post('/trainable', [TrainableController::class, 'store'])->name('trainable.store');
    Route::get('/trainable/{trainable:id}/show', [TrainableController::class, 'show'])->name('trainable.show');

    Route::post('/training/{trainable:id}/intake', [TrainingController::class, 'intake'])->name('training.intake');
    Route::post('/training/{trainable:id}/ask', [TrainingController::class, 'ask'])->name('training.ask');
});
