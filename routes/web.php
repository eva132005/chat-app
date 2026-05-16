<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\PresenceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('chat.index');
    }
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/room/{room}', [ChatController::class, 'room'])->name('chat.room');
    Route::post('/chat/room/{room}/message', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/chat/private', [ChatController::class, 'createPrivateRoom'])->name('chat.private');
    Route::post('/chat/group', [ChatController::class, 'createGroupRoom'])->name('chat.group');
    Route::delete('/chat/message/{message}', [ChatController::class, 'deleteMessage'])->name('chat.delete');

    Route::post('/presence/online', [PresenceController::class, 'online'])->name('presence.online');
    Route::post('/presence/offline', [PresenceController::class, 'offline'])->name('presence.offline');
});

require __DIR__.'/auth.php';