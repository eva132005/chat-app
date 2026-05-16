<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('room.{roomId}', function ($user, $roomId) {
    return $user->rooms()->where('rooms.id', $roomId)->exists();
});