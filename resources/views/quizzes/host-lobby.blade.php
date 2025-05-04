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
            <h1 id="waiting-message" class="text-gray-500 mt-2">Waiting for players...</h1>

            <ul id="players-list" class="list-disc list-inside mt-2">
                {{-- Jugadores se agregarán dinámicamente --}}
            </ul>
        </div>

        @if (isset($error))
            <div class="alert alert-danger">
                {{ $error }}
            </div>
        @endif


        <button id="start-game" class="bg-green-500 text-white px-4 py-2 rounded opacity-50 cursor-not-allowed" disabled>
            Iniciar Juego
        </button>
    </div>


    <script>

        var arenaGameId = @json($arenaGameId);
        window.addEventListener('DOMContentLoaded', () => {


            window.Echo.join(`arena.{{$arenaGameId}}`)
                .here((users) => {
                    console.log("HOST Usuarios en el canal:", users);
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
                }).listen('.game.started', (e) => {
                console.log("Evento recibido: game.started", e);
                // Redirigir a los jugadores a la página de espera o la pantalla de juego
                window.location.href = `/arena/host/${arenaGameId}`;
            });
                // .listen('App\\Events\\GameStarted', (e) => {
                //     console.log("Evento recibido: game.started", e);
                //     document.getElementById('start-message').classList.remove('hidden');
                //     setTimeout(() => {console.log("Redirigiendo al host...");
                //         window.location.href = `/arena/host/${arenaGameId}`;
                //     }, 2000); // espera breve para mostrar el mensaje
                // });



            function addPlayer(user) {
                if (user.is_host) return;
                if (!document.getElementById('player-' + user.id)) {
                    const li = document.createElement('li');
                    li.textContent = user.name;
                    li.id = 'player-' + user.id;
                    document.getElementById('players-list').appendChild(li);
                    updateStartButtonState();
                }
            }

            function removePlayer(user) {
                const li = document.getElementById('player-' + user.id);
                if (li) li.remove();

                updateStartButtonState();

                fetch(`/arena/${arenaGameId}/remove-player`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ user_id: user.id })  // 👈 asegúrate que user.id tenga valor
                })
                    .then(response => {
                        if (!response.ok) throw new Error("Error en respuesta");
                        return response.json();
                    })
                    .then(data => {
                        console.log("Jugador eliminado", data);
                    })
                    .catch(err => {
                        console.error("Error al eliminar jugador en el backend", err);
                    });

            }

            function updatePlayers(users) {
                document.getElementById('players-list').innerHTML = '';
                users.forEach(user => addPlayer(user));
                updateStartButtonState();
            }
            function updateStartButtonState() {
                const playersList = document.getElementById('players-list');
                const startButton = document.getElementById('start-game');
                const waitingMessage = document.getElementById('waiting-message');

                const players = playersList.querySelectorAll('li');
                if (players.length === 0) {
                    startButton.disabled = true;
                    startButton.classList.add('opacity-50', 'cursor-not-allowed');
                    waitingMessage.style.display = 'block';
                } else {
                    startButton.disabled = false;
                    startButton.classList.remove('opacity-50', 'cursor-not-allowed');
                    waitingMessage.style.display = 'none';
                }
            }

            // document.getElementById('start-game').addEventListener('click', () => {
            //     axios.post(`/arena/${arenaGameId}/start-game`)
            //         .then(() => {
            //             // No redirigir, sino esperar el evento 'game.started' para redirigir
            //         });
            // });

        });

        //
        document.getElementById('start-game').addEventListener('click', () => {
            axios.post(`/arena/${arenaGameId}/start-game`)
                .then(() => {
                    console.log("hey")
                  //  window.location.href = `/quizzes/arena/${arenaGameId}/host-question`;
                });
        });


    </script>

</x-app-layout>
