<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AskController;
use App\Http\Controllers\ChatController;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

Route::get('/ask', [AskController::class, 'index'])->name('ask.index');
Route::post('/ask', [AskController::class, 'ask'])->name('ask.post');
Route::post('/user/model', [AskController::class, 'changeModel'])->name('ask.changeModel');

Route::get("/chat/{chatId}", [ChatController::class, 'show'])->name('chat.show');
Route::post('/chat/{chatId}/ask', [ChatController::class, 'ask'])->name('chat.ask');

Route::delete('/chat/{chatId}', [ChatController::class, 'destroy'])->name('chat.destroy');

require __DIR__.'/settings.php';
