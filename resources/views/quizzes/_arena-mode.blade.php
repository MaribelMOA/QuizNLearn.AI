<div class="p-6 h-full flex flex-col">
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Arena Mode</h2>
        <p class="text-gray-600">Play and learn with your friends simultaniously!</p>
    </div>

    <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-lg p-6 mb-8">
        <div class="mb-6">
            <label for="display_name" class="block text-sm font-medium text-gray-700 mb-1">Player Name:</label>
            <input type="text" id="display_name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" placeholder="Enter name">
        </div>

        <div class="mb-6">
            <label for="game_pin" class="block text-sm font-medium text-gray-700 mb-1">Game PIN</label>
            <input type="text" id="game_pin" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" placeholder="Enter PIN">
        </div>

        <button type="button" class="w-full py-3 px-4 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-md transition-colors shadow-md">
            Join Game
        </button>
    </div>

    <div class="text-center mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Start New Arena</h3>
        <p class="text-gray-600 text-sm mb-4">Create new game and share with friends</p>
    </div>

    <div class="mt-auto">
        <a href="{{ route('arena.create') }}" class="block w-full py-3 px-4 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-medium rounded-md transition-colors text-center shadow-md">
            Create game
        </a>
    </div>

    <div class="mt-6 text-center">
        <p class="text-sm text-gray-500">You have <span class="font-semibold text-purple-600">{{ $arenaModeUses }}</span> Arena mode uses left</p>
    </div>
</div>

