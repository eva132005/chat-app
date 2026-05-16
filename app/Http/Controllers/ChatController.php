<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        $users = User::where('id', '!=', auth()->id())->get();
        $rooms = auth()->user()->rooms()->with('members')->get();
        return view('chat.index', compact('users', 'rooms'));
    }

    public function room(Room $room)
    {
        $messages = $room->messages()->with('user')->latest()->take(50)->get()->reverse();
        $members = $room->members;
        return view('chat.room', compact('room', 'messages', 'members'));
    }

    public function sendMessage(Request $request, Room $room)
    {
        $request->validate(['body' => 'required|string|max:1000']);

        $message = Message::create([
            'room_id' => $room->id,
            'user_id' => auth()->id(),
            'body' => $request->body,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message->load('user'));
    }

    public function createPrivateRoom(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);

        $existingRoom = Room::where('type', 'private')
            ->whereHas('members', fn($q) => $q->where('user_id', auth()->id()))
            ->whereHas('members', fn($q) => $q->where('user_id', $request->user_id))
            ->first();

        if ($existingRoom) {
            return redirect()->route('chat.room', $existingRoom);
        }

        $room = Room::create(['type' => 'private']);
        $room->members()->attach([auth()->id(), $request->user_id]);

        return redirect()->route('chat.room', $room);
    }

    public function createGroupRoom(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'members' => 'required|array|min:1',
        ]);

        $room = Room::create(['name' => $request->name, 'type' => 'group']);
        $members = array_merge($request->members, [auth()->id()]);
        $room->members()->attach($members);

        return redirect()->route('chat.room', $room);
    }

    public function deleteMessage(Message $message)
    {
        if ($message->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->delete();

        return response()->json(['success' => true]);
    }
}