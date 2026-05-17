<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat App</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 h-screen flex">

    <!-- Sidebar -->
    <div class="w-72 bg-gray-800 text-white flex flex-col">
        <div class="p-4 border-b border-gray-700">
            <h1 class="text-xl font-bold">💬 Chat App</h1>
            <p class="text-sm text-gray-400">{{ auth()->user()->name }}</p>
            <form action="/logout" method="POST" class="mt-1">
                @csrf
                <button type="submit" class="text-xs text-red-400 hover:text-red-300">Logout</button>
            </form>
        </div>

        <!-- Users List -->
        <div class="p-4 flex-1 overflow-y-auto">
            <p class="text-xs text-gray-400 uppercase mb-3">Users</p>
            @foreach($users as $user)
            <form action="{{ route('chat.private') }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <button type="submit" class="w-full flex items-center gap-3 p-2 rounded hover:bg-gray-700 mb-1">
                    <span class="w-2 h-2 rounded-full {{ $user->is_online ? 'bg-green-400' : 'bg-gray-500' }}"></span>
                    <span class="text-sm">{{ $user->name }}</span>
                </button>
            </form>
            @endforeach

            <!-- Group Rooms -->
            <p class="text-xs text-gray-400 uppercase mt-4 mb-3">Group Chats 👥</p>
            @foreach($rooms->where('type', 'group') as $room)
            <a href="{{ route('chat.room', $room) }}" 
               class="flex items-center gap-3 p-2 rounded hover:bg-gray-700 mb-1">
                <span class="text-lg">👥</span>
                <span class="text-sm">{{ $room->name }}</span>
            </a>
            @endforeach
        </div>

        <!-- Create Group -->
        <div class="p-4 border-t border-gray-700">
            <button onclick="document.getElementById('groupModal').classList.remove('hidden')"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded text-sm">
                + Buat Group
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex items-center justify-center">
        <div class="text-center text-gray-500">
            <p class="text-4xl mb-3">💬</p>
            <p class="text-lg font-semibold">Selamat datang, {{ auth()->user()->name }}!</p>
            <p class="text-sm">Pilih user untuk mulai chat</p>
        </div>
    </div>

    <!-- Modal Buat Group -->
    <div id="groupModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-96">
            <h2 class="text-lg font-bold mb-4">Buat Group Chat</h2>
            <form action="{{ route('chat.group') }}" method="POST">
                @csrf
                <input type="text" name="name" placeholder="Nama Group"
                       class="w-full border rounded px-3 py-2 mb-3 focus:outline-none focus:ring-2 focus:ring-blue-400">
                <p class="text-sm text-gray-600 mb-2">Pilih Members:</p>
                @foreach($users as $user)
                <label class="flex items-center gap-2 mb-2">
                    <input type="checkbox" name="members[]" value="{{ $user->id }}">
                    <span>{{ $user->name }}</span>
                </label>
                @endforeach
                <div class="flex gap-2 mt-4">
                    <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                        Buat
                    </button>
                    <button type="button" onclick="document.getElementById('groupModal').classList.add('hidden')"
                            class="flex-1 bg-gray-200 text-gray-700 py-2 rounded hover:bg-gray-300">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>