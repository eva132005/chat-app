<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat Room</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100 h-screen flex">

    <!-- Sidebar -->
    <div class="w-72 bg-gray-800 text-white flex flex-col">
        <div class="p-4 border-b border-gray-700">
            <a href="{{ route('chat.index') }}" class="text-gray-400 hover:text-white text-sm">← Kembali</a>
            <h1 class="text-xl font-bold mt-1">💬 Chat App</h1>
        </div>

        <!-- Members -->
        <div class="p-4 flex-1 overflow-y-auto">
            <p class="text-xs text-gray-400 uppercase mb-3">Members</p>
            @foreach($members as $member)
            <div class="flex items-center gap-3 p-2 rounded mb-1">
                <span class="w-2 h-2 rounded-full {{ $member->is_online ? 'bg-green-400' : 'bg-gray-500' }}"
                      id="status-{{ $member->id }}"></span>
                <div>
                    <p class="text-sm">{{ $member->name }}</p>
                    <p class="text-xs text-gray-400" id="lastseen-{{ $member->id }}">
                        {{ $member->is_online ? 'Online' : ($member->last_seen ? 'Last seen ' . $member->last_seen->diffForHumans() : 'Offline') }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Chat Area -->
    <div class="flex-1 flex flex-col">

        <!-- Header -->
        <div class="bg-white p-4 shadow flex items-center gap-3">
            <div>
                <h2 class="font-semibold text-lg">
                    {{ $room->type === 'group' ? $room->name : 'Private Chat' }}
                </h2>
                <p class="text-xs text-gray-500 uppercase">{{ $room->type }}</p>
            </div>
        </div>

        <!-- Messages -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3" id="messages">
            @foreach($messages as $msg)
            <div class="flex {{ $msg->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}" id="msg-{{ $msg->id }}">
                <div class="{{ $msg->user_id === auth()->id() ? 'bg-blue-500 text-white' : 'bg-white' }} 
                            rounded-lg px-4 py-2 max-w-sm shadow relative">
                    @if($msg->user_id !== auth()->id())
                    <p class="text-xs font-semibold mb-1 text-gray-500">
                        {{ $msg->user->name }}
                    </p>
                    @endif
                    <p>{{ $msg->body }}</p>
                    <p class="text-xs opacity-70 text-right mt-1">
                        {{ $msg->created_at->format('H:i') }}
                    </p>
                    @if($msg->user_id === auth()->id())
                    <button onclick="deleteMessage({{ $msg->id }})"
                            class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                        ✕
                    </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <!-- Input -->
        <div class="bg-white p-4 border-t">
            <div class="flex gap-2">
                <input type="text" id="messageInput"
                       class="flex-1 border rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                       placeholder="Ketik pesan...">
                <button onclick="sendMessage()"
                        class="bg-blue-500 text-white px-6 py-2 rounded-full hover:bg-blue-600">
                    Kirim
                </button>
            </div>
        </div>

    </div>

@vite(['resources/js/app.js'])
<script>
const ROOM_ID = {{ $room->id }};
const USER_ID = {{ auth()->id() }};

// Scroll ke bawah
const messagesDiv = document.getElementById('messages');
messagesDiv.scrollTop = messagesDiv.scrollHeight;

// Enter key
document.getElementById('messageInput').addEventListener('keypress', (e) => {
    if (e.key === 'Enter') sendMessage();
});

async function sendMessage() {
    const input = document.getElementById('messageInput');
    const body = input.value.trim();
    if (!body) return;

    const res = await fetch(`/chat/room/${ROOM_ID}/message`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ body })
    });

    const msg = await res.json();
    appendMessage({
        id: msg.id,
        body: msg.body,
        user: msg.user,
        created_at: new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'})
    }, true);
    input.value = '';
}

function appendMessage(msg, isMine) {
    const div = document.createElement('div');
    div.className = `flex ${isMine ? 'justify-end' : 'justify-start'}`;
    div.id = `msg-${msg.id}`;
    div.innerHTML = `
        <div class="${isMine ? 'bg-blue-500 text-white' : 'bg-white'} rounded-lg px-4 py-2 max-w-sm shadow relative">
            ${!isMine ? `<p class="text-xs font-semibold mb-1 text-gray-500">${msg.user.name}</p>` : ''}
            <p>${msg.body}</p>
            <p class="text-xs opacity-70 text-right mt-1">${msg.created_at}</p>
            ${isMine ? `<button onclick="deleteMessage(${msg.id})" 
                class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">✕</button>` : ''}
        </div>
    `;
    messagesDiv.appendChild(div);
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

// Hapus pesan
async function deleteMessage(id) {
    if (!confirm('Hapus pesan ini?')) return;

    const res = await fetch(`/chat/message/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    });

    if (res.ok) {
        const el = document.getElementById(`msg-${id}`);
        if (el) el.remove();
    }
}

// WebSocket - terima pesan
window.addEventListener('load', function() {
    window.Echo.private(`room.${ROOM_ID}`)
        .listen('MessageSent', (e) => {
            appendMessage(e, false);
        });

    // WebSocket - presence tracking
    window.Echo.channel('presence')
        .listen('UserPresence', (e) => {
            const dot = document.getElementById(`status-${e.user_id}`);
            const lastSeen = document.getElementById(`lastseen-${e.user_id}`);
            if (dot) {
                dot.className = `w-2 h-2 rounded-full ${e.status === 'online' ? 'bg-green-400' : 'bg-gray-500'}`;
            }
            if (lastSeen) {
                lastSeen.textContent = e.status === 'online' ? 'Online' : 'Offline';
            }
        });

    // Set online saat buka halaman
    fetch('/presence/online', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    });
});

// Set offline saat tutup tab
window.addEventListener('beforeunload', () => {
    navigator.sendBeacon('/presence/offline');
});
</script>

</body>
</html>