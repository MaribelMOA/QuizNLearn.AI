<div class="p-6 h-full flex flex-col">
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Modo Arena</h2>
        <p class="text-gray-600">¡Juega con tus amigos y aprende al mismo tiempo!</p>
    </div>

    <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-lg p-6 mb-8">
        <div class="mb-6">
            <label for="display_name" class="block text-sm font-medium text-gray-700 mb-1">Nombre de Jugador:</label>
            <input type="text" id="display_name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" placeholder="Ingresa tu nombre">
        </div>

        <div class="mb-6">
            <label for="game_pin" class="block text-sm font-medium text-gray-700 mb-1">PIN del Juego:</label>
            <input type="text" id="game_pin" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" placeholder="Ingresa el PIN">
        </div>

        <button type="button" class="w-full py-3 px-4 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-md transition-colors shadow-md">
            Unirse al Juego
        </button>
    </div>

    <div class="text-center mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Iniciar una nueva Arena</h3>
        <p class="text-gray-600 text-sm mb-4">Crea un nuevo juego y comparte el PIN con tus amigos</p>
    </div>

    <div class="mt-auto">
        <a href="{{ route('arena.create') }}" class="block w-full py-3 px-4 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-medium rounded-md transition-colors text-center shadow-md">
            Crear Juego
        </a>
    </div>

    <div class="mt-6 text-center">
        <p class="text-sm text-gray-500">Tienes <span class="font-semibold text-purple-600">{{ $arenaModeUses }}</span> usos de Arena disponibles</p>
    </div>
</div>

