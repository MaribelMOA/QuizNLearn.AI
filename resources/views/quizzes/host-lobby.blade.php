<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 to-purple-50 py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Arena Header -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <div class="flex items-center mb-4 md:mb-0">
                            <div class="bg-white p-2 rounded-full mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-white">Arena Mode</h1>
                                <p class="text-purple-200">Host Lobby</p>
                            </div>
                        </div>
                        <div class="flex items-center">
{{--                            <a href="{{ route('arena.finish_game') }}" class="bg-white text-purple-600 px-4 py-2 rounded-lg font-medium hover:bg-purple-50 transition-colors" onclick="return confirm('Are you sure you want to finish the game? Your progress will be lost.')">--}}

{{--                            Cancel Game--}}
{{--                            </a>--}}
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Game Info -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Game PIN Card -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-500 px-6 py-4">
                            <h2 class="text-xl font-bold text-white">Game PIN</h2>
                        </div>
                        <div class="p-6 text-center">
                            <div class="bg-indigo-100 rounded-lg py-4 px-6 mb-4">
                                <span id="quiz-code" class="text-4xl font-bold tracking-wider text-indigo-800">{{ $pin }}</span>
                            </div>
                            <p class="text-gray-600 mb-4">Share this PIN with your players</p>

                            <button onclick="copyGamePin()" class="inline-flex items-center px-4 py-2 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                Copy PIN
                            </button>
                        </div>
                    </div>


                </div>

                <!-- Right Column: Players List and Start Button -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-500 to-indigo-500 px-6 py-4">
                            <h2 class="text-xl font-bold text-white">Connected Players</h2>
                        </div>

                        <div class="p-6">
                            <!-- Empty State -->
                            <div id="waiting-message" class="text-center py-8">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="text-xl font-medium text-gray-500 mb-2">Waiting for players to join...</h3>
                                <p class="text-gray-400">Share the Game PIN with your players</p>

                                <!-- Loading Animation -->
                                <div class="flex justify-center items-center space-x-2 mt-6">
                                    <div class="w-3 h-3 bg-purple-600 rounded-full animate-bounce"></div>
                                    <div class="w-3 h-3 bg-purple-600 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                    <div class="w-3 h-3 bg-purple-600 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                                </div>
                            </div>

                            <!-- Players List -->
                            <div id="players-container" class="hidden">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6">
                                    <div id="players-list" class="space-y-2">
                                        <!-- Players will be added here dynamically -->
                                    </div>
                                </div>

                                <div class="text-center mt-8">
                                    <p class="text-gray-600 mb-4">Ready to start the game?</p>
                                    <button id="start-game" class="px-8 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors shadow-md flex items-center justify-center mx-auto opacity-50 cursor-not-allowed" disabled>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Start Game
                                    </button>
                                </div>
                            </div>

                            <!-- Error Message (if any) -->
                            @if (isset($error))
                                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mt-4">
                                    {{ $error }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const arenaGameId = @json($arenaGameId);
        // const useWebSockets = false; // Change to true when using Echo and Pusher in production

        window.addEventListener('DOMContentLoaded', () => {
            if (window.useWebSockets) {
                listenWithWebSockets();
            } else {
                startPollingForPlayers();
            }

            // Event for starting the game
            document.getElementById('start-game').addEventListener('click', () => {
                const startButton = document.getElementById('start-game');
                startButton.disabled = true;
                startButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Starting...
                `;

                axios.post(`/arena/${arenaGameId}/start-game`)
                    .then(() => {
                        console.log("Game started");
                    })
                    .catch(error => {
                        console.error("Error starting game:", error);
                        startButton.disabled = false;
                        startButton.innerHTML = `
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Start Game
                        `;
                    });
            });
        });

        // WebSocket Mode
        function listenWithWebSockets() {
            window.Echo.join(`arena.${arenaGameId}`)
                .here((users) => {
                    console.log("HOST Users in channel:", users);
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
                .listen('.game.started', (e) => {
                    console.log("Event received: game.started", e);
                    window.location.href = `/arena/host/${arenaGameId}`;
                });
        }

        // Polling Mode
        function startPollingForPlayers() {
            console.log("Starting polling for players...");
            setInterval(() => {
                axios.get(`/arena/${arenaGameId}/players`)
                    .then(response => {
                        updatePlayers(response.data.players);
                    })
                    .catch(error => {
                        console.error("Error getting players:", error);
                    });

                axios.get(`/arena/${arenaGameId}/status`)
                    .then(response => {
                        if (response.data.status === 'started') {
                            window.location.href = `/arena/host/${arenaGameId}`;
                        }
                    })
                    .catch(error => {
                        console.error("Error getting status:", error);
                    });
            }, 3000);
        }

        // Common functions
        function addPlayer(user) {
            if (user.is_host) return;
            if (!document.getElementById('player-' + user.id)) {
                const playerItem = document.createElement('div');
                playerItem.id = 'player-' + user.id;
                playerItem.className = 'flex items-center justify-between bg-gray-50 rounded-lg p-3 border border-gray-200';

                // Get first letter of name for avatar
                const firstLetter = user.name.charAt(0).toUpperCase();

                playerItem.innerHTML = `
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                            <span class="text-lg font-semibold text-indigo-700">${firstLetter}</span>
                        </div>
                        <span class="font-medium text-gray-800">${user.name}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Ready
                        </span>
                    </div>
                `;

                document.getElementById('players-list').appendChild(playerItem);
                updateStartButtonState();
            }
        }

        function removePlayer(user) {
            const playerItem = document.getElementById('player-' + user.id);
            if (playerItem) playerItem.remove();

            fetch(`/arena/${arenaGameId}/remove-player`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ user_id: user.id })
            })
                .then(response => {
                    if (!response.ok) throw new Error("Error in response");
                    return response.json();
                })
                .then(data => {
                    console.log("Player removed", data);
                })
                .catch(err => {
                    console.error("Error removing player in backend", err);
                });

            updateStartButtonState();
        }

        function updatePlayers(newPlayers) {
            const currentPlayerElements = document.querySelectorAll('#players-list [id^="player-"]');
            const currentPlayerIds = Array.from(currentPlayerElements).map(el => parseInt(el.id.replace('player-', '')));

            const newPlayerIds = newPlayers.map(p => p.id);

            // Add new players
            newPlayers.forEach(player => {
                addPlayer(player);
            });

            // Remove players who are no longer present
            currentPlayerIds.forEach(id => {
                if (!newPlayerIds.includes(id)) {
                    removePlayer({ id });
                }
            });
        }

        function updateStartButtonState() {
            const players = document.querySelectorAll('#players-list [id^="player-"]');
            const btn = document.getElementById('start-game');
            const waitingMessage = document.getElementById('waiting-message');
            const playersContainer = document.getElementById('players-container');

            if (players.length === 0) {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
                waitingMessage.classList.remove('hidden');
                playersContainer.classList.add('hidden');
            } else {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                waitingMessage.classList.add('hidden');
                playersContainer.classList.remove('hidden');
            }
        }

        function copyGamePin() {
            const pin = document.getElementById('quiz-code').textContent;
            navigator.clipboard.writeText(pin).then(() => {
                // Show a temporary success message
                const button = document.querySelector('button[onclick="copyGamePin()"]');
                const originalText = button.innerHTML;

                button.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Copied!
                `;
                button.classList.remove('bg-indigo-100', 'text-indigo-700');
                button.classList.add('bg-green-100', 'text-green-700');

                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('bg-green-100', 'text-green-700');
                    button.classList.add('bg-indigo-100', 'text-indigo-700');
                }, 2000);
            });
        }
    </script>
</x-app-layout>
