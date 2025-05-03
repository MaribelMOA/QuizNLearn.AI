<x-app-layout >

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Arena Mode</h1>
        </div>
    </x-slot>

    <div class="py-6 ">
        <div class="text-4xl font-mono bg-gray-100 p-4 rounded">{{ $arenaGame->pin }}</div>

        <div id="player-count" class="text-lg text-gray-700">Esperando jugadores...</div>

        <button id="start-game-btn" class="hidden bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            Iniciar Juego
        </button>


    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Echo.join(`arena.{{ $arenaGame->pin }}`)
            .here(users => {
                document.getElementById('player-count').innerText = `${users.length}conected player(s) `;
                if (users.length > 0) {
                    document.getElementById('start-game-btn').classList.remove('hidden');
                }
            })
            .joining(user => {
                let count = parseInt(document.getElementById('player-count').dataset.count || 0) + 1;
                document.getElementById('player-count').innerText = `${count} conected player(s) `;
                if (count > 0) {
                    document.getElementById('start-game-btn').classList.remove('hidden');
                }
            })
            .leaving(user => {
                let count = parseInt(document.getElementById('player-count').dataset.count || 0) - 1;
                document.getElementById('player-count').innerText = `${count} conected player(s) `;
                if (count <= 0) {
                    document.getElementById('start-game-btn').classList.add('hidden');
                }
            });
    </script>

</x-app-layout>
