<?php

namespace App\Http\Controllers;

use App\Events\UserPresence;

class PresenceController extends Controller
{
    public function online()
    {
        $user = auth()->user();
        $user->update(['is_online' => true, 'last_seen' => now()]);
        broadcast(new UserPresence($user, 'online'));
        return response()->json(['status' => 'online']);
    }

    public function offline()
    {
        $user = auth()->user();
        $user->update(['is_online' => false, 'last_seen' => now()]);
        broadcast(new UserPresence($user, 'offline'));
        return response()->json(['status' => 'offline']);
    }
}