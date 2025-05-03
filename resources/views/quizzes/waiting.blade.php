<x-app-layout>

    <x-slot name="header">
        <h1 class="text-2xl font-bold">Waiting Room</h1>
    </x-slot>

    <div class="text-center mt-10">
        <h2 class="text-xl mb-4">Hello,  {{ $player->name }}</h2>
        <p class="mb-2">Waiting for the host to start the game...</p>
        <p class="text-gray-600 mb-6">Game PIN: <strong>{{ $pin }}</strong></p>

        @if (isset($error))
            <div class="alert alert-danger">
                {{ $error }}
            </div>
        @endif

        
        <div id="start-message" class="text-lg font-bold text-green-600 hidden">
            The game is starting!
        </div>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const arenaGameId = @json($arenaGameId);

            Echo.join(`arena.${arenaGameId}`)
                .listen('.game.started', (e) => {
                    window.location.href = `/arena/play/${arenaGameId}`;
                });
        });



    </script>

</x-app-layout>
