<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 to-purple-50 py-6">
        <div class="max-w-lg mx-auto px-4">
            <!-- Game Header -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
                    <div class="flex items-center justify-center">
                        <div class="bg-white p-2 rounded-full mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-white">Arena Mode</h1>
                            <p class="text-purple-200">Waiting Room</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Player Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
                <div class="p-6 text-center">
                    <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl font-bold text-indigo-600">{{ substr($player->name, 0, 1) }}</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Hello, {{ $player->name }}!</h2>


                        <div class="inline-flex items-center bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                            <span class="font-medium">Score: {{ $score }}</span>
                        </div>

                </div>
            </div>

            <!-- Game PIN Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
                <div class="p-6 text-center">
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Game PIN</h3>
                    <div class="bg-indigo-100 rounded-lg py-3 px-6 mb-4">
                        <span class="text-3xl font-bold tracking-wider text-indigo-800">{{ $pin }}</span>
                    </div>
                    <p class="text-gray-600">Share this PIN with other players to join!</p>
                </div>
            </div>

            <!-- Waiting Status -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-6 text-center">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">Waiting for the host to start the game...</h3>

                    <!-- Loading Animation -->
                    <div class="flex justify-center items-center space-x-2 mb-4">
                        <div class="w-3 h-3 bg-purple-600 rounded-full animate-bounce"></div>
                        <div class="w-3 h-3 bg-purple-600 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        <div class="w-3 h-3 bg-purple-600 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                    </div>

                    <!-- Error Message (if any) -->
                    @if (isset($error))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mt-4">
                            {{ $error }}
                        </div>
                    @endif

                    <!-- Start Message (hidden by default) -->
                    <div id="start-message" class="hidden">
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mt-4 animate-pulse">
                            <p class="text-lg font-bold">The game is starting!</p>
                            <p>Get ready...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const arenaGameId = @json($arenaGameId);
       // const useWebSockets = false; // Cámbialo a true cuando subas a producción
        const userId = @json(Auth::id());
      //  console.log(userId);

       //temp for polling
       //  window.addEventListener('unload', function () {
       //      fetch(`/arena/${arenaGameId}/remove-player`, {
       //          method: 'POST',
       //          keepalive: true, // importante
       //          headers: {
       //              'Content-Type': 'application/json',
       //              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
       //          },
       //          body: JSON.stringify({ user_id: userId })
       //      });
       //  });
        window.addEventListener('DOMContentLoaded', () => {
            if (window.useWebSockets) {
                window.Echo.join(`arena.${arenaGameId}`)
                    .here((users) => {
                        console.log("PLAYER Usuarios en el canal:", users); // ✅
                        // updatePlayers(users);
                    })
                    .listen('.game.started', (e) => {
                        console.log("Recibido game.started");
                        showStartingMessage();
                        window.location.href = `/arena/play/${arenaGameId}`;
                    });
            } else {
                startPollingForStart();
            }
        }); function showStartingMessage() {
            const startMessage = document.getElementById('start-message');
            startMessage.classList.remove('hidden');
        }

        function startPollingForStart() {
            console.log("Iniciando polling para esperar inicio...");
            setInterval(() => {
                axios.get(`/arena/${arenaGameId}/status`)
                    .then(response => {
                        if (response.data.status === 'started') {
                            showStartingMessage();
                            setTimeout(() => {
                                window.location.href = `/arena/play/${arenaGameId}`;
                            }, 1500);  }
                    });
            }, 3000);
        }
    </script>


</x-app-layout>
