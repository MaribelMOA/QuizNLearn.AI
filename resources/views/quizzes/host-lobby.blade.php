<x-app-layout >

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Arena Mode</h1>
        </div>
    </x-slot>
    <div class="container text-center">
        <h1 class="text-3xl font-bold mb-4">Host Waiting Room</h1>
        <p class="mb-2">Game PIN: <strong id="quiz-code">{{ $pin }}</strong></p>

        <div class="my-4">
            <h2 class="text-xl font-semibold">Conected Players:</h2>
            <ul id="players-list" class="list-disc list-inside mt-2">
                {{-- Jugadores se agregarán dinámicamente --}}
            </ul>
        </div>

        @if (isset($error))
            <div class="alert alert-danger">
                {{ $error }}
            </div>
        @endif


        <button id="start-game" class="bg-green-500 text-white px-4 py-2 rounded">Iniciar Juego</button>
    </div>


    <script>

        window.addEventListener('DOMContentLoaded', () => {
            var arenaGameId = @json($arenaGameId);

            window.Echo.join(`arena.{{$arenaGameId}}`)
                .here((users) => {
                    updatePlayers(users);
                })
                .joining((user) => {
                    addPlayer(user);
                })
                .leaving((user) => {
                    removePlayer(user);
                })
                .listen('.player.joined', (e) => {
                    addPlayer(e.player.user);
                })


            function addPlayer(user) {
                if (user.is_host) return;
                if (!document.getElementById('player-' + user.id)) {
                    const li = document.createElement('li');
                    li.textContent = user.name;
                    li.id = 'player-' + user.id;
                    document.getElementById('players-list').appendChild(li);
                }
            }

            function removePlayer(user) {
                const li = document.getElementById('player-' + user.id);
                if (li) li.remove();
            }

            function updatePlayers(users) {
                document.getElementById('players-list').innerHTML = '';
                users.forEach(user => addPlayer(user));
            }
        });






        //
        // document.getElementById('start-game').addEventListener('click', () => {
        //     axios.post(`/quizzes/arena/${quizId}/start`)
        //         .then(() => {
        //             window.location.href = `/quizzes/arena/${quizId}/host-question`;
        //         });
        // });

        document.getElementById('start-game').addEventListener('click', () => {
            axios.post('/arena/start-game', { arenaGameId: arenaGameId });
        });
    </script>

</x-app-layout>
